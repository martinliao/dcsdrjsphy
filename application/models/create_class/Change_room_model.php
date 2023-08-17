<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Change_room_model extends MY_Model
{
    public $table = 'require';
    public $pk = 'seq_no';

    public function __construct()
    {
        parent::__construct();

        $this->init($this->table, $this->pk);

    }

    public function getFormDefault($info=array())
    {
        $data = array_merge(array(
                        'room_id' => ''
                    ),$info);

        return $data;
    }

    public function getVerifyConfig()
    {
        $config = array(
            'room_id' => array(
                'field' => 'room_id',
                'label' => '使用教室',
                'rules' => 'trim|required',
            ),
            'use_date' => array(
                'field' => 'use_date',
                'label' => '日期',
                'rules' => 'trim|required',
            ),
            'room_id' => array(
                'field' => 'room_id',
                'label' => '使用教室',
                'rules' => 'trim|required',
            ),
        );

        return $config;
    }

    public function getListCount($attrs=array())
    {
        $params = array(
            'conditions' => $attrs['conditions'],
        );

        if (isset($attrs['query_class_name'])) {
            $params['query_class_name'] = $attrs['query_class_name'];
        }
        $data = $this->getList($params);
        return count($data);
    }

    public function getList($attrs=array())
    {
        $params = array(
            'select' => 'seq_no,year,class_no,term,class_name',
            'order_by' => 'year desc,class_no,term',
        );

        $params['where_special'] = '(year, class_no, term) in (select distinct year, class_id, term from room_use)';

        if (isset($attrs['query_class_name'])) {
            $params['or_like'] = array(
                'many' => TRUE,
                'data' => array(
                    array('field' => 'class_name', 'value'=>$attrs['query_class_name'], 'position'=>'both'),
                ),
            );
        }

        if (isset($attrs['conditions'])) {
            $params['conditions'] = $attrs['conditions'];
        }
        if (isset($attrs['rows'])) {
            $params['rows'] = $attrs['rows'];
        }
        if (isset($attrs['offset'])) {
            $params['offset'] = $attrs['offset'];
        }
        if (isset($attrs['sort'])) {
            $params['order_by'] = $attrs['sort'];
        }

        $data = $this->getData($params);
        
        return $data;
    }

    public function getRoom($year,$class_no,$term){
        $today = date('Y-m-d');
        $this->db->select('room_use.year,room_use.class_id,room_use.term,room_use.room_id,venue_information.room_name as room_name');
        $this->db->join('venue_information','venue_information.room_id = room_use.room_id','left');
        $this->db->where('room_use.year',$year);
        $this->db->where('room_use.class_id',$class_no);
        $this->db->where('room_use.term',$term);
        $this->db->where('room_use.use_date >=',$today);
        $this->db->group_by('room_use.year,room_use.class_id,room_use.term,room_use.room_id,venue_information.room_name');
        $query = $this->db->get('room_use');
        $result = $query->result_array();

        $data = array();
        for($i=0;$i<count($result);$i++){
            $data[$result[$i]['room_id']] = $result[$i]['room_name'];
        }
        return $data;
    }

    public function getRoomUseDate($year,$class_no,$term,$room_id){
        $today = date('Y-m-d');

        $this->db->select('use_date');
        $this->db->where('year',$year);
        $this->db->where('class_id',$class_no);
        $this->db->where('term',$term);
        $this->db->where('room_id',$room_id);
        $this->db->where('use_date >=',$today);
        $this->db->group_by('use_date');

        $query = $this->db->get('room_use');
        $result = $query->result_array();

        for($i=0;$i<count($result);$i++) { 
            $result[$i]['use_date'] = date('Y-m-d',strtotime($result[$i]['use_date']));
        }

        return $result;
    }

    public function getRoomUseTime($data=array()){
        $sql = sprintf("SELECT
                            from_time,
                            to_time 
                        FROM
                            periodtime 
                        WHERE
                            `year` = %s 
                            AND class_no = %s 
                            AND term = %s 
                            AND course_date = %s
                            AND room_id = %s",$this->db->escape(addslashes($data['year'])),$this->db->escape(addslashes($data['class_no'])),$this->db->escape(addslashes($data['term'])),$this->db->escape(addslashes($data['use_date'])),$this->db->escape(addslashes($data['room_id'])));

        $query = $this->db->query($sql);
        $result = $query->result_array();

        return $result;
    }

    public function checkExist($data=array(),$testuser=''){
        if($testuser == 'T222291880') {
            $use_time = $this->getRoomUseTime($data);

            $where = '';
            for($i=0;$i<count($use_time);$i++){
                $where .= sprintf("((%s between periodtime.from_time and periodtime.to_time) or (%s between periodtime.from_time and periodtime.to_time))",intval($use_time[$i]['from_time']),intval($use_time[$i]['to_time']));
                if(($i+1) != count($use_time)){
                    $where .= ' or ';
                }
            }

            if($where == ''){
                $where = '1=1';
            }

            $sql = sprintf("SELECT DISTINCT
                                *
                            FROM
                                (
                                    SELECT DISTINCT
                                        b.booking_date,
                                        b.year,
                                        r.class_name,
                                        r.term,
                                        r.class_no
                                    FROM
                                        booking_place b
                                    LEFT JOIN `require` r ON b. YEAR = r. YEAR
                                    AND b.class_no = r.class_no
                                    AND b.term = r.term
                                    WHERE
                                        b.room_id = %s
                                    UNION ALL
                                        SELECT DISTINCT
                                            b.use_date,
                                            b.year,
                                            r.class_name,
                                            b.term,
                                            b.class_id as class_no
                                        FROM
                                            room_use b
                                        JOIN `require` r ON b. YEAR = r. YEAR
                                        AND b.class_id = r.class_no
                                        AND b.term = r.term
                                        JOIN periodtime ON b.`year` = periodtime.`year` 
                                        AND b.class_id = periodtime.class_no 
                                        AND b.term = periodtime.term 
                                        AND b.use_date = periodtime.course_date 
                                        AND b.use_id = periodtime.course_code 
                                        WHERE
                                            b.room_id = %s
                                        AND (
                                            r.is_cancel NOT IN ('1')
                                            OR r.is_cancel IS NULL
                                        )
                                        AND (%s)
                                    UNION ALL
                                        SELECT DISTINCT
                                            b.use_date,
                                            b.YEAR,
                                            NULL,
                                            b.term,
                                            b.class_id as class_no 
                                        FROM
                                            room_use b 
                                        WHERE
                                            b.room_id = %s 
                                            AND b.appi_id IS NOT NULL 
                                ) a
                            WHERE
                                booking_date = %s
                            AND (year, class_no, term) NOT IN (
                                SELECT
                                    year,
                                    class_no,
                                    term
                                FROM
                                    `require`
                                WHERE
                                    year = %s
                                AND class_no = %s
                                AND term = %s
                            )
                            ORDER BY
                                booking_date", $this->db->escape(addslashes($data['new_room_id'])), $this->db->escape(addslashes($data['new_room_id'])), $where, $this->db->escape(addslashes($data['new_room_id'])), $this->db->escape(addslashes($data['use_date'])), $this->db->escape(addslashes($data['year'])), $this->db->escape(addslashes($data['class_no'])), $this->db->escape(addslashes($data['term'])));
        } else {
            $sql = sprintf("SELECT DISTINCT
                            *
                        FROM
                            (
                                SELECT DISTINCT
                                    b.booking_date,
                                    b.year,
                                    r.class_name,
                                    r.term,
                                    r.class_no
                                FROM
                                    booking_place b
                                LEFT JOIN `require` r ON b. YEAR = r. YEAR
                                AND b.class_no = r.class_no
                                AND b.term = r.term
                                WHERE
                                    b.room_id = %s
                                UNION ALL
                                    SELECT DISTINCT
                                        b.use_date,
                                        b.year,
                                        r.class_name,
                                        r.term,
                                        r.class_no
                                    FROM
                                        room_use b
                                    LEFT JOIN `require` r ON b. YEAR = r. YEAR
                                    AND b.class_id = r.class_no
                                    AND b.term = r.term
                                    WHERE
                                        b.room_id = %s
                                    AND (
                                        r.is_cancel NOT IN ('1')
                                        OR r.is_cancel IS NULL
                                    )
                            ) a
                        WHERE
                            booking_date = %s
                        AND (year, class_no, term) NOT IN (
                            SELECT
                                year,
                                class_no,
                                term
                            FROM
                                `require`
                            WHERE
                                year = %s
                            AND class_no = %s
                            AND term = %s
                        )
                        ORDER BY
                            booking_date",$this->db->escape(addslashes($data['new_room_id'])),$this->db->escape(addslashes($data['new_room_id'])),$this->db->escape(addslashes($data['use_date'])),$this->db->escape(addslashes($data['year'])),$this->db->escape(addslashes($data['class_no'])),$this->db->escape(addslashes($data['term'])));

        }
        
        $query = $this->db->query($sql);
        $result = $query->result_array();

        return $result;
    }   

    public function changeRoom($data=array()){
        $this->db->trans_start();

        $this->db->set('room_id',$data['new_room_id']);
        $this->db->where('room_id',$data['room_id']);
        $this->db->where('year',$data['year']);
        $this->db->where('class_id',$data['class_no']);
        $this->db->where('term',$data['term']);
        $this->db->where('use_date',$data['use_date']);
        $this->db->update('room_use');

        $this->db->set('room_id',$data['new_room_id']);
        $this->db->where('room_id',$data['room_id']);
        $this->db->where('year',$data['year']);
        $this->db->where('class_no',$data['class_no']);
        $this->db->where('term',$data['term']);
        $this->db->where('course_date',$data['use_date']);
        $this->db->update('periodtime');

        $this->db->select('room_id,course_date');
        $this->db->where('year',$data['year']);
        $this->db->where('class_no',$data['class_no']);
        $this->db->where('term',$data['term']);
        $this->db->order_by('course_date,from_time');
        $this->db->limit(1);
        $query = $this->db->get('periodtime');
        $result = $query->result_array();

        if(!empty($result)){
            for($i=0;$i<count($result);$i++){ 
                if(!empty($result[$i]['course_date'])){
                    $result[$i]['course_date'] = date('Y-m-d',strtotime($result[$i]['course_date']));
                }
                if($data['new_room_id'] == $result[$i]['room_id'] && $data['use_date'] == $result[$i]['course_date']){
                    $this->db->set('room_code',$data['new_room_id']);
                    $this->db->where('year',$data['year']);
                    $this->db->where('class_no',$data['class_no']);
                    $this->db->where('term',$data['term']);
                    $this->db->update('require');
                }
            }
        }

        $this->db->trans_complete();

        if($this->db->trans_status() === TRUE){
            return true;
        } 

        return false;
    }
}
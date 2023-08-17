<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Handouts_status_model extends MY_Model
{
    public $table = 'handouts_status';
    public $pk = '';

    public function __construct()
    {
        parent::__construct();

        $this->init($this->table, $this->pk);

    }

    public function getListCount($attrs=array())
    {
        $params = array(
            'conditions' => $attrs['conditions'],
        );

        if (isset($attrs['q'])) {
            $params['q'] = $attrs['q'];
        }
        $data = $this->getList($params);
        return count($data);
    }

    public function getList($attrs=array())
    {
        $params = array(
            'select' => '',
            'order_by' => 'cre_date desc',
        );
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
        if (isset($attrs['q'])) {
            $params['or_like'] = array(
                'many' => TRUE,
                'data' => array(
                    array('field' => 'description', 'value'=>$attrs['q'], 'position'=>'both'),
                ),
            );
            // unset
        }

        $data = $this->getData($params);

        foreach($data as & $row){
        	$user_data = $this->user_model->get($row['cre_user']);
        	$row['cre_user'] = $user_data['name'];
        }

        return $data;
    }

    public function insertHandoutsStatus($query_year,$query_class_no,$course_code,$teacher_id)
    {
        $insert=['year'=>$query_year,
                 'class_no'=>$query_class_no,
                 'course_code'=>$course_code,
                 'teacher_id'=>$teacher_id,
                 'status'=>'1'];
        $this->db->insert('handouts_status',$insert);

    }
    public function deleteHandoutsStatus($query_year,$query_class_no,$course_code)
    {
        $this->db->where('year',$query_year);
        $this->db->where('class_no',$query_class_no);
        $this->db->where('course_code',$course_code);
        $this->db->delete('handouts_status');
        var_dump('hello');
    }
    public function getCourseCode($query_year,$query_class_no,$query_term,$del_course_date,$del_period,$del_room_id)
    {
        $this->db->select('use_id');
        $this->db->where('year',$query_year);
        $this->db->where('class_id',$query_class_no);
        $this->db->where('term',$query_term);
        $this->db->where('use_date',$del_course_date);
        $this->db->where('use_period',$del_period);
        $this->db->where('room_id',$del_room_id);
        $course_code=$this->db->get('room_use');
        $course_code=$course_code->result_array();
        return $course_code[0]['use_id'];
    }

    public function getBookRoom($condition)
    {
        /*
        $this->db->select("require.class_name,reservation_time.start_time,reservation_time.end_time,require.year,require.class_no,require.term,reservation_time.item_id");
        $this->db->join("require","require.year = booking_place.year AND require.class_no = booking_place.class_no
                            AND require.term = booking_place.term
                            AND require.class_status IN (2, 3)");
        $this->db->join("reservation_time","reservation_time.item_id = booking_place.booking_period");
        $this->db->where("booking_place.booking_date",$condition['booking_date']);
        $this->db->where("booking_place.room_id",$condition['room_id']);
        $where2="NOT (booking_place.year={$condition['year']} and booking_place.class_no='{$condition['class_no']}' and booking_place.term={$condition['term']})";
        $this->db->where($where2);
        $where="require.is_cancel != '1' OR require.is_cancel IS NULL";
        $this->db->where($where);
        
        $this->db->order_by("reservation_time.start_time");
        $query=$this->db->get("booking_place");
        $query=$query->result_array();
        $c_stime=strtotime($condition['pre_start_time']);
        $c_etime=strtotime($condition['pre_end_time']);
        */ //2021-05-20 修正無法正常正常值篩選預約教室問題,註解上方,新增下方$booking_sql

        $c_stime=strtotime($condition['pre_start_time']);
        $c_etime=strtotime($condition['pre_end_time']);
        $booking_sql = sprintf("SELECT req.class_name,reservation_time.start_time,reservation_time.end_time,req.year,req.class_no,req.term,reservation_time.item_id 
        FROM booking_place AS bp 
        JOIN `require` AS req 
        ON req.year = bp.year AND req.class_no = bp.class_no AND req.term = bp.term AND req.class_status IN (2, 3) 
        JOIN reservation_time  
        ON reservation_time.item_id = bp.booking_period 
        WHERE bp.booking_date = %s	AND bp.room_id = %s 
         AND NOT (bp.year = %s and bp.class_no = %s and bp.term = %s)
         AND (req.is_cancel != '1' OR req.is_cancel IS NULL )",$this->db->escape(addslashes($condition['booking_date'])),$this->db->escape(addslashes($condition['room_id'])),$this->db->escape(addslashes($condition['year'])),$this->db->escape(addslashes($condition['class_no'])),$this->db->escape(addslashes($condition['term'])));

        $query=$this->db->query($booking_sql);
        $query=$query->result_array();

        foreach ($query as $temp) {
            $start_time=strtotime($temp['start_time']);
            $end_time=strtotime($temp['end_time']);
            if($temp['item_id']=='10' || $temp['item_id']=='16'){
               return $query;
            }
            if(($c_stime<=$end_time&&$c_stime>=$start_time)||($c_etime>=$start_time&&$c_etime<=$end_time)||($c_stime<=$start_time&&$c_etime>=$end_time)){
                return $query;
            }
        }
        return null;
    }

    public function getUseRoom($condition)
    {
        
        $sql=sprintf("SELECT NVL ( `require`.class_name, appinfo.app_reason ) AS class_name,NVL ( NVL ( periodtime1.from_time, periodtime2.from_time ), reservation_time.start_time ) AS start_time,NVL ( NVL ( periodtime1.to_time, periodtime2.to_time ), reservation_time.end_time ) AS end_time FROM room_use
            LEFT JOIN periodtime periodtime1 ON periodtime1.YEAR = room_use.YEAR AND periodtime1.class_no = room_use.class_id AND periodtime1.term = room_use.term AND periodtime1.id = room_use.use_period AND periodtime1.room_id = room_use.room_id AND periodtime1.course_code = room_use.use_id AND periodtime1.course_date = room_use.use_date
            LEFT JOIN periodtime periodtime2 ON periodtime2.id = room_use.use_period AND periodtime2.YEAR IS NULL AND periodtime2.class_no IS NULL AND periodtime2.term IS NULL AND periodtime2.room_id IS NULL AND periodtime2.course_code IS NULL AND room_use.appi_id
            IS NULL LEFT JOIN reservation_time ON reservation_time.item_id = room_use.use_period
            LEFT JOIN `require` ON `require`.YEAR = room_use.YEAR AND `require`.class_no = room_use.class_id AND `require`.term = room_use.term AND `require`.class_status IN ( 2, 3 )
            LEFT JOIN appinfo ON appinfo.appi_id = room_use.appi_id 
            WHERE room_use.room_id = %s AND room_use.use_date = %s AND ( `require`.is_cancel != '1' OR `require`.is_cancel IS NULL ) 
            GROUP BY ifnull ( ifnull ( periodtime1.from_time, periodtime2.from_time ), reservation_time.start_time ),ifnull ( ifnull ( periodtime1.to_time, periodtime2.to_time ), reservation_time.end_time ),require.class_name 
            ORDER BY start_time",$this->db->escape(addslashes($condition['room_id'])),$this->db->escape(addslashes($condition['booking_date'])));

        $data=$this->db->query($sql);
        $data=$data->result_array();
 
        $c_stime=strtotime($condition['pre_start_time']);
        $c_etime=strtotime($condition['pre_end_time']);
        foreach ($data as $temp) {
            $start_time=strtotime($temp['start_time']);
            $end_time=strtotime($temp['end_time']);
            if(($c_stime<$end_time&&$c_stime>=$start_time)||($c_etime>=$start_time&&$c_etime<=$end_time)||($c_stime<=$start_time&&$c_etime>=$end_time)){
                return $data;
            }

        }
        return null;
    }

    public function getUseRoomForEdit($condition)
    {
        
        $sql=sprintf("SELECT NVL ( `require`.class_name, appinfo.app_reason ) AS class_name,NVL ( NVL ( periodtime1.from_time, periodtime2.from_time ), reservation_time.start_time ) AS start_time,NVL ( NVL ( periodtime1.to_time, periodtime2.to_time ), reservation_time.end_time ) AS end_time FROM room_use
            LEFT JOIN periodtime periodtime1 ON periodtime1.YEAR = room_use.YEAR AND periodtime1.class_no = room_use.class_id AND periodtime1.term = room_use.term AND periodtime1.id = room_use.use_period AND periodtime1.room_id = room_use.room_id AND periodtime1.course_code = room_use.use_id AND periodtime1.course_date = room_use.use_date
            LEFT JOIN periodtime periodtime2 ON periodtime2.id = room_use.use_period AND periodtime2.YEAR IS NULL AND periodtime2.class_no IS NULL AND periodtime2.term IS NULL AND periodtime2.room_id IS NULL AND periodtime2.course_code IS NULL AND room_use.appi_id
            IS NULL LEFT JOIN reservation_time ON reservation_time.item_id = room_use.use_period
            LEFT JOIN `require` ON `require`.YEAR = room_use.YEAR AND `require`.class_no = room_use.class_id AND `require`.term = room_use.term AND `require`.class_status IN ( 2, 3 )
            LEFT JOIN appinfo ON appinfo.appi_id = room_use.appi_id 
            WHERE room_use.room_id = %s AND room_use.use_date = %s AND ( `require`.is_cancel != '1' OR `require`.is_cancel IS NULL ) AND NOT(room_use.year=%s AND room_use.class_id=%s AND room_use.term=%s)
            GROUP BY ifnull ( ifnull ( periodtime1.from_time, periodtime2.from_time ), reservation_time.start_time ),ifnull ( ifnull ( periodtime1.to_time, periodtime2.to_time ), reservation_time.end_time ),class_name 
            ORDER BY start_time",$this->db->escape(addslashes($condition['room_id'])),$this->db->escape(addslashes($condition['booking_date'])),$this->db->escape(addslashes($condition['year'])),$this->db->escape(addslashes($condition['class_no'])),$this->db->escape(addslashes($condition['term'])));

        $data=$this->db->query($sql);
        $data=$data->result_array();
 
        $c_stime=strtotime($condition['pre_start_time']);
        $c_etime=strtotime($condition['pre_end_time']);
        foreach ($data as $temp) {
            $start_time=strtotime($temp['start_time']);
            $end_time=strtotime($temp['end_time']);
            if(($c_stime<=$end_time&&$c_stime>=$start_time)||($c_etime>=$start_time&&$c_etime<=$end_time)||($c_stime<=$start_time&&$c_etime>=$end_time)){
                return $data;
            }

        }
        return null;
    }
}
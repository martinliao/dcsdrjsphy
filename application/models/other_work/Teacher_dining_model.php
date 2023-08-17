<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Teacher_dining_model extends MY_Model
{
    public $table = 'manual_teacher_dining';
    public $pk = 'id';

    public function __construct()
    {
        parent::__construct();

        $this->init($this->table, $this->pk);

    }

    public function getTotalList($start_date, $end_date)
    {
        $data = $this->getList($start_date, $end_date);

        return $data;
    }

    public function getList($start_date, $end_date, $rows=-1, $offset=-1){
        $limit = '';
        if($rows >= 0 && $offset >= 0){
            $limit = sprintf(" limit %s, %s", intVal($offset), intVal($rows));
        }
        
        $sql = sprintf("SELECT * FROM 
                        (
                            SELECT
                                '' as id,
                                `require`.class_name,
                                periodtime.term,
                                BS_user.`name` AS worker_name,
                                venue_information.room_sname,
                                teacher.`name` AS teacher_name,
                                '講師' as type,
                                periodtime.course_date,
                                teacher_dining.place,
                                teacher_dining.way,
                                teacher_dining.food_type,
                                teacher_dining.num,
                                teacher_dining.remark,
                                periodtime.`year`,
                                periodtime.class_no,
                                room_use.teacher_id,
                                `require`.worker 
                            FROM
                                periodtime
                                JOIN room_use ON periodtime.`year` = room_use.`year` 
                                AND periodtime.class_no = room_use.class_id 
                                AND periodtime.term = room_use.term 
                                AND periodtime.course_date = room_use.use_date 
                                AND periodtime.course_code = room_use.use_id 
                                AND periodtime.room_id = room_use.room_id
                                JOIN `require` ON `require`.`year` = periodtime.`year` 
                                AND `require`.class_no = periodtime.class_no 
                                AND `require`.term = periodtime.term
                                JOIN teacher ON teacher.idno = room_use.teacher_id
                                JOIN BS_user ON BS_user.idno = `require`.worker
                                JOIN venue_information ON venue_information.room_id = periodtime.room_id 
                                JOIN mail_log on mail_log.`year` = periodtime.`year` and mail_log.class_no = periodtime.class_no and mail_log.term = periodtime.term and mail_log.mail_type = 3
                                LEFT JOIN teacher_dining on teacher_dining.`year` = periodtime.`year` and teacher_dining.class_no = periodtime.class_no and teacher_dining.term = periodtime.term and teacher_dining.course_date = periodtime.course_date and teacher_dining.idno = room_use.teacher_id
                            WHERE
                                periodtime.course_date BETWEEN %s 
                                AND %s 
                                AND ( periodtime.from_time BETWEEN 1100 AND 1230 OR periodtime.to_time BETWEEN 1100 AND 1230 )
                                AND venue_information.room_bel != 68001  
                                AND mail_log.`year` > 0
                                AND `require`.is_cancel <> 1
                            GROUP BY
                                room_use.`year`,
                                room_use.class_id,
                                room_use.term,
                                room_use.use_date,
                                room_use.teacher_id 
                            UNION
                            SELECT
                                manual_teacher_dining.id,
                                manual_teacher_dining.class_name,
                                '',
                                BS_user.name as worker_name,
                                venue_information.room_sname,
                                manual_teacher_dining.dining_name,
                                manual_teacher_dining.type,
                                manual_teacher_dining.use_date,
                                manual_teacher_dining.place,
                                manual_teacher_dining.way,
                                manual_teacher_dining.food_type,
                                manual_teacher_dining.num,
                                manual_teacher_dining.remark,
                                '',
                                '',
                                '',
                                manual_teacher_dining.creator
                            FROM
                                manual_teacher_dining
                            JOIN venue_information ON venue_information.room_id = manual_teacher_dining.room_id
                            JOIN BS_user ON BS_user.idno = `manual_teacher_dining`.creator
                            WHERE  
                                manual_teacher_dining.use_date BETWEEN %s and %s
                        ) x 
                        ORDER BY x.year desc,x.course_date, x.class_no, x.term 
                        %s", 
                        $this->db->escape(addslashes($start_date)), 
                        $this->db->escape(addslashes($end_date)), 
                        $this->db->escape(addslashes($start_date)), 
                        $this->db->escape(addslashes($end_date)), 
                        $limit
                    );
        
        $query = $this->db->query($sql);
        $result = $query->result_array();

        return $result;
    }

    public function add($data = array()){
        if($this->db->insert($this->table, $data)){
            return true;
        }
        
        return false;
    }

    public function chkAutoExist($year, $class_no, $term, $course_date, $idno){
        $this->db->select('count(1) cnt');
        $this->db->where('year', $year);
        $this->db->where('class_no', $class_no);
        $this->db->where('term', $term);
        $this->db->where('course_date', $course_date);
        $this->db->where('idno', $idno);

        $query = $this->db->get('teacher_dining');
        $result = $query->result_array();

        if($result[0]['cnt'] > 0){
            return true;
        }

        return false;
    }

    public function insertAuto($data=array()){
        if($this->db->insert('teacher_dining', $data)){
            return true;
        }
        
        return false;
    }

    public function updateAuto($data=array()){
        $this->db->set('place', $data['place']);
        $this->db->set('way', $data['way']);
        $this->db->set('food_type', $data['food_type']);
        $this->db->set('num', $data['num']);
        $this->db->set('remark', $data['remark']);

        $this->db->where('year', $data['year']);
        $this->db->where('class_no', $data['class_no']);
        $this->db->where('term', $data['term']);
        $this->db->where('course_date', $data['course_date']);
        $this->db->where('idno', $data['idno']);

        if($this->db->update('teacher_dining')){
            return true;
        }
        
        return false;
    }

    public function updateManual($data=array()){
        $this->db->set('place', $data['place']);
        $this->db->set('way', $data['way']);
        $this->db->set('food_type', $data['food_type']);
        $this->db->set('num', $data['num']);
        $this->db->set('remark', $data['remark']);
        $this->db->set('modify_time', date('Y-m-d H:i:s'));

        $this->db->where('id', $data['id']);

        if($this->db->update($this->table)){
            return true;
        }

        return false;
    }

    public function clearAutoDining($auto)
    {
        if (empty($auto['year']) || empty($auto['class_no']) || empty($auto['term']) || empty($auto['course_date']) || empty($auto['idno'])){
            return false;
        }
        $this->db->set('place', null);
        $this->db->set('way', null);
        $this->db->set('food_type', null);
        $this->db->set('num', null);

        $this->db->where('year', $auto['year']);
        $this->db->where('class_no', $auto['class_no']);
        $this->db->where('term', $auto['term']);
        $this->db->where('course_date', $auto['course_date']);
        $this->db->where('idno', $auto['idno']);

        if($this->db->update('teacher_dining')){
            return true;
        }

        return false;
    }

    public function clearManualDining($manual_id)
    {
        if (empty($manual_id)) return false;
        $this->db->set('place', null);
        $this->db->set('way', null);
        $this->db->set('food_type', null);
        $this->db->set('num', null);
        $this->db->set('modify_time', date('Y-m-d H:i:s'));

        $this->db->where('id', $manual_id);

        if($this->db->update($this->table)){
            return true;
        }

        return false;
    }
}
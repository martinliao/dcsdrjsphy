<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Room_use_model extends MY_Model
{
    public $table = 'room_use';
    public $pk = '';

    public function __construct()
    {
        parent::__construct();
    }

    public function getClassRoomUse($class_info){
        $class_info['class_id'] = $class_info['class_no'];
        unset($class_info['class_no']);
        $this->db->select("DATE_FORMAT(use_date, '%Y-%m-%d') use_date")
                 ->distinct()
                 ->from("room_use")
                 ->where($class_info);
        $query = $this->db->get();                 
        return $query->result();
    }

    /*
        取得實體課程課表
    */
    public function getPhySchedule($class_info){
        $select_name = "
            CASE WHEN IFNULL (t.another_name, t.name) = '教務組' OR ru.title = '無' THEN IFNULL(t.another_name, t.name)
                      WHEN ru.title <> '' THEN ru.title|| IFNULL(t.another_name, t.name)
                      ELSE
                           CASE WHEN t.teacher_type = 1 THEN CONCAT(IFNULL(t.another_name, t.name), ' 老師')
                                WHEN t.teacher_type = 2 THEN CONCAT(IFNULL(t.another_name, t.name), ' (助)')
                                WHEN ct.add_val2 = 'Y' THEN '教務組'
                                ELSE ''
                           END
            END AS name";

        $this->db->select([
            "ru.teacher_id", 
            "ru.use_period", 
            "DATE_FORMAT(ru.use_date, '%Y-%m-%d') use_date", 
            "ru.use_id", 
            "ru.year", 
            "ru.class_id", 
            "ru.term", 
            "pt.from_time", 
            "pt.to_time", 
            "ct.description",
            "ifnull(cr.room_sname, cr.room_name) room_name", 
            "r.contactor", 
            "r.tel", 
            $select_name,
            "r.class_name",
            "t.name teacher_name"
            ])
             ->from("room_use ru")
             ->join("periodtime pt", "
                pt.year = ru.year AND 
                pt.class_no = ru.class_id AND 
                pt.term = ru.term AND 
                pt.id = ru.use_period AND
                pt.course_date = ru.use_date AND
                pt.course_code = ru.use_id AND
                pt.room_id = ru.room_id
             ", 'left')
             ->join("code_table_his ct", "ct.item_id = ru.use_id AND ct.type_id = '17'", 'left')
             ->join("venue_information cr", "cr.room_id = ru.room_id", 'left')
             ->join("require r", "r.year = ru.year AND r.term = ru.term AND r.class_no = ru.class_id")
             ->join("teacher t", "t.idno = ru.teacher_id", 'left')
             ->where("ru.year", $class_info['year'])
             ->where("ru.term", $class_info['term'])
             ->where("ru.class_id", $class_info['class_no'])
             ->where("ru.use_date is not null")
             ->order_by("ru.use_date, use_period");
        $query = $this->db->get();
        return $query->result();
    }

     /*
        取得實體課程課表
    */
    public function getPhySchedule_new($class_info){
        $select_name = "";

        $this->db->select([
            "ru.teacher_id", 
            "ru.use_period", 
            "DATE_FORMAT(ru.use_date, '%Y-%m-%d') use_date", 
            "ru.use_id", 
            "ru.year", 
            "ru.class_id", 
            "ru.term", 
            "pt.from_time", 
            "pt.to_time", 
            "ct.description",
            "ifnull(cr.room_sname, cr.room_name) room_name", 
            "r.contactor", 
            "r.tel", 
            $select_name,
            "r.class_name",
            "t.name teacher_name"
            ])
             ->from("room_use ru")
             ->join("periodtime pt", "
                pt.year = ru.year AND 
                pt.class_no = ru.class_id AND 
                pt.term = ru.term AND 
                pt.id = ru.use_period AND
                pt.course_date = ru.use_date AND
                pt.course_code = ru.use_id AND
                pt.room_id = ru.room_id
             ", 'left')
             ->join("code_table ct", "ct.item_id = ru.use_id", 'left')
             ->join("venue_information cr", "cr.room_id = ru.room_id", 'left')
             ->join("require r", "r.year = ru.year AND r.term = ru.term AND r.class_no = ru.class_id")
             ->join("teacher t", "t.idno = ru.teacher_id", 'left')
             ->where("ru.year", $class_info['year'])
             ->where("ru.term", $class_info['term'])
             ->where("ru.class_id", $class_info['class_no'])
             ->where("ru.use_date is not null")
             ->group_by("ru.use_date,ru.use_id,pt.from_time,pt.to_time,ru.teacher_id")
             ->order_by("pt.from_time,ru.use_date, use_period"); //2021-06-30 修正29B某些課程時間排序錯誤
        $query = $this->db->get();
        return $query->result();
    }


    function getMaterial($condition){
        $this->db->start_cache();
        $this->db->select("distinct date_format(use_date, '%Y-%m-%d') use_date, ru.year, ru.class_id, ru.term, t.id t_id ,r.class_name, t.name teacher_name, teacher_auth.id auth_id")
                 ->from("room_use ru")
                 ->join("require r", "r.year = ru.year AND r.class_no = ru.class_id AND r.term = ru.term AND r.is_cancel = 0 ")
                 ->join("teacher t", "t.idno = ru.teacher_id AND teacher_type = 1 ")
                 ->join("teacher_auth", "teacher_auth.teacher_id = t.id AND teacher_auth.year = ru.year AND teacher_auth.class_no = ru.class_id AND teacher_auth.term = ru.term", "left")
                 ->join("periodtime pt", "
                    pt.year = ru.year AND 
                    pt.class_no = ru.class_id AND 
                    pt.term = ru.term AND 
                    pt.id = ru.use_period AND
                    pt.course_date = ru.use_date AND
                    pt.course_code = ru.use_id AND
                    pt.room_id = ru.room_id                 
                 ")
                 ->where("use_date is not null");

        if(isset($condition['start_date'])) $this->db->where("ru.use_date >=", $condition['start_date']);
        if(isset($condition['end_date'])) $this->db->where("ru.use_date <=", $condition['end_date']);

        $this->db->stop_cache();
        // $this->paginate();
        $query = $this->db->get();
        // dd($this->db->last_query());
        $this->db->flush_cache();                  
        return $query->result();
    }

}
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MixClassReport_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getSignStudentCount($year,$class_no,$term){
    	$this->db->select('count(1) cnt');
    	$this->db->from('online_app');
    	$this->db->where('year',$year);
    	$this->db->where('class_no',$class_no);
    	$this->db->where('term',$term);
    	$this->db->where_not_in('yn_sel', ['6']);

    	$query = $this->db->get();
    	$data = $query->result_array();

    	return $data[0]['cnt'];
    }	

    public function getOnlineCourse($year,$class_no,$term){
    	$this->db->select('class_name,teacher_name');
    	$this->db->from('require_online');
    	$this->db->where('year',$year);
    	$this->db->where('class_no',$class_no);
    	$this->db->where('term',$term);
    	

    	$query = $this->db->get();
    	$data = $query->result_array();

    	return $data;
    }

    public function getphyCourse($year,$class_no,$term){
    	// $sql = sprintf("SELECT
					// 		course_code.`name` AS course_name,
					// 		teacher.`name` as teacher_name,
					// 		room_use.isteacher 
					// 	FROM
					// 		room_use
					// 		JOIN teacher ON room_use.teacher_id = teacher.idno 
					// 		AND room_use.isteacher = teacher.teacher
					// 		JOIN course_code ON room_use.use_id = course_code.item_id 
					// 	WHERE
					// 		room_use.`year` = '%s' 
					// 		AND room_use.class_id = '%s' 
					// 		AND room_use.term = '%s'",$year,$class_no,$term);


            $sql = sprintf("SELECT
                                course_code.`name` AS course_name,
                                teacher.`name` as teacher_name,
                                ru.isteacher 
                            FROM
                                room_use ru
                                JOIN periodtime pt ON pt.`year` = ru.`year` AND pt.class_no = ru.class_id AND pt.term = ru.term AND pt.course_date = ru.use_date AND pt.id = ru.use_period 
                                JOIN teacher ON ru.teacher_id = teacher.idno AND ru.isteacher = teacher.teacher
                                JOIN course_code ON ru.use_id = course_code.item_id 
                            WHERE
                                ru.`year` = %s 
                                AND ru.class_id = %s 
                                AND ru.term = %s
                            ORDER BY use_date asc, from_time asc",$this->db->escape(addslashes($year)),$this->db->escape(addslashes($class_no)),$this->db->escape(addslashes($term)));        


    	$query = $this->db->query($sql);
    	$data = $query->result_array();

    	return $data;
    }
}
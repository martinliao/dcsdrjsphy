<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Hour_app_model extends MY_Model
{   
    public $table = 'hour_app';
    public $pk = '';

    public function __construct()
    {
        parent::__construct();
    }   

    public function getList($queryData)
    {
        $this->db->start_cache(); 

    	$this->db->distinct()
    			 ->select("DISTINCT year, class_no, term, class_name, app_seq", false)
    			 ->from('hour_traffic_tax')
    			 ->join('hour_app', 'hour_app.seq = hour_traffic_tax.seq');

    	if (isset($queryData['sdate'])){
    		$this->db->where('hour_traffic_tax.use_date >=', $queryData['sdate']);
    	}

    	if (isset($queryData['edate'])){
    		$this->db->where('hour_traffic_tax.use_date <=', $queryData['edate']);
    	}
    			 
    	$this->db->stop_cache();

    	//$this->paginate(); 
    	$query = $this->db->get();
    	$this->db->flush_cache(); 
    	return $query->result();
    }

    public function getByGroup($queryData)
    {
        $firstCourse = "SELECT DISTINCT run.`year`, run.class_id, run.term, run.teacher_id, run.use_date, run.from_time , rux.to_time, coursen.name course_name, crn.room_name
                        FROM (
                            SELECT ru.`year`, ru.class_id , ru.term, ru.teacher_id, ru.use_date, min(pt.from_time) from_time, max(pt.to_time) to_time 
                            FROM room_use ru 
                            JOIN periodtime pt ON pt.`year` = ru.`year` AND 
                                                  pt.class_no = ru.class_id AND 
                                                  pt.term = ru.term AND 
                                                  pt.id = ru.use_period AND
                                                  pt.course_date = ru.use_date AND
                                                  pt.course_code = ru.use_id AND
                                                  pt.room_id = ru.room_id
                            GROUP BY ru.`year`, ru.class_id , ru.term, ru.teacher_id, ru.use_date  
                        ) nx
                        JOIN (
                            SELECT ru.`year`, ru.class_id , ru.term, ru.teacher_id, ru.use_date, pt.id, pt.from_time, pt.course_code, pt.room_id 
                            FROM room_use ru 
                            JOIN periodtime pt ON pt.`year` = ru.`year` AND 
                                                  pt.class_no = ru.class_id AND 
                                                  pt.term = ru.term AND 
                                                  pt.id = ru.use_period AND
                                                  pt.course_date = ru.use_date AND
                                                  pt.course_code = ru.use_id AND
                                                  pt.room_id = ru.room_id
                        ) run ON run.`year` = nx.`year` AND 
                                 run.class_id = nx.class_id AND 
                                 run.term = nx.term AND 
                                 run.teacher_id = nx.teacher_id AND 
                                 run.use_date = nx.use_date AND 
                                 run.from_time = nx.from_time
                        JOIN (
                            SELECT ru.`year`, ru.class_id , ru.term, ru.teacher_id, ru.use_date, pt.id, pt.to_time
                            FROM room_use ru 
                            JOIN periodtime pt ON pt.`year` = ru.`year` AND 
                                                  pt.class_no = ru.class_id AND 
                                                  pt.term = ru.term AND 
                                                  pt.id = ru.use_period AND
                                                  pt.course_date = ru.use_date AND
                                                  pt.course_code = ru.use_id AND
                                                  pt.room_id = ru.room_id
                        ) rux ON rux.`year` = nx.`year` AND 
                                 rux.class_id = nx.class_id AND 
                                 rux.term = nx.term AND 
                                 rux.teacher_id = nx.teacher_id AND 
                                 rux.use_date = nx.use_date AND 
                                 rux.to_time = nx.to_time   
                        JOIN course_code coursen ON coursen.item_id = run.course_code
                        LEFT JOIN venue_information crn ON crn.room_id = run.room_id    
                        ";

        $this->db->select("hour_traffic_tax.*, hour_app.app_seq, user.name worker_name, firstCourse.course_name, firstCourse.from_time , firstCourse.to_time, firstCourse.course_name course_name, firstCourse.room_name ", false)
                 ->from('hour_traffic_tax')
                 ->join('hour_app', 'hour_app.seq = hour_traffic_tax.seq')
                 ->join('`require` r', 'r.`year` = hour_traffic_tax.`year` AND r.`class_no` = hour_traffic_tax.`class_no` AND r.`term` = hour_traffic_tax.`term`')
                 ->join('BS_user user', 'user.idno = r.worker', 'left')
                 ->join("( $firstCourse ) as firstCourse", 'firstCourse.`year` = hour_traffic_tax.`year` AND firstCourse.class_id = hour_traffic_tax.class_no AND firstCourse.term = hour_traffic_tax.term AND firstCourse.teacher_id = hour_traffic_tax.teacher_id AND firstCourse.use_date = hour_traffic_tax.use_date');

        if (isset($queryData['sdate'])){
            $this->db->where('hour_traffic_tax.use_date >=', $queryData['sdate']);
        }

        if (isset($queryData['edate'])){
            $this->db->where('hour_traffic_tax.use_date <=', $queryData['edate']);
        }

        $this->db->order_by("hour_app.app_seq", "asc");
        $this->db->order_by("hour_traffic_tax.class_name", "asc");
        $this->db->order_by("firstCourse.from_time", "asc");

        $query = $this->db->get();
        return $query->result();

    }
}
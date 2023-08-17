<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class OutsideSign_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getList($startdate,$enddate,$class_name){
    	$this->db->select('*');
    	$this->db->where('course_date >=',$startdate);
    	$this->db->where('course_date <=',$enddate);

        if(!empty($class_name)){
            $this->db->like('class_name',$class_name);
        }

    	$query = $this->db->get('outside');
    	$data = $query->result_array();

    	return $data;
    }

    public function getInfo($id){
		$this->db->select('*');
    	$this->db->where('id',$id);
    	$query = $this->db->get('outside');
    	$data = $query->result_array();

    	return $data;
    }

    public function addClassInfo($data){
    	if($this->db->insert('outside', $data)){
    		return $this->db->insert_id();
    	}
    	
    	return 0;
    }

    public function updateClassInfo($data,$id){
    	$this->db->where('id',$id);
		$data = array_map('addslashes', $data);
    	if($this->db->update('outside',$data)){
    		return true;
    	}

    	return false;
    }

    public function getStudentMaxNo($id){
    	$this->db->select('max(no) as maxNO');
    	$this->db->where('id',$id);
    	$query = $this->db->get('outside_student');
    	$result = $query->result_array();

    	if(!empty($result[0]['maxNO'])){
    		return $result[0]['maxNO']+1;
    	} else {
    		return 1;
    	}
    }

    public function addStudent($data){
		$data = array_map('addslashes', $data);
    	if($this->db->insert('outside_student', $data)){
    		return true;
    	}
    	
    	return false;
    }

    public function updateStudentCount($id){
    	$sql = sprintf("update outside set student_count = student_count+1 where id = '%s'",$id);
    	
    	if($this->db->query($sql)){
    		return true;
    	}

    	return false;
    }

    public function insideDelete($id){
    	$this->db->trans_start();

    	$this->db->where('id',$id);
    	$this->db->delete('outside');

    	$this->db->where('oid',$id);
    	$this->db->delete('outside_sign_log');

    	$this->db->trans_complete();

        if($this->db->trans_status() === TRUE){
            return true;
        } 

        return false;
    }

    public function outsideDelete($id){
    	$this->db->trans_start();

    	$this->db->where('id',$id);
    	$this->db->delete('outside');

    	$this->db->where('oid',$id);
    	$this->db->delete('outside_sign_log');

    	$this->db->where('id',$id);
    	$this->db->delete('outside_student');

    	$this->db->trans_complete();

        if($this->db->trans_status() === TRUE){
            return true;
        } 

        return false;
    }

    public function checkClassExist($id){
    	$this->db->select('count(1) cnt');
    	$this->db->where('id',$id);
    	$query = $this->db->get('outside');
    	$result = $query->result_array();

    	if($result[0]['cnt'] > 0){
    		return true;
    	}

    	return false;
    }

    public function getInsideSignList($id){
    	$sql = sprintf("SELECT
							outside.id,
							outside.class_name,
							online_app.id AS idno,
							online_app.st_no AS no,
							BS_user.name,
							outside.hours,
							outside.course_date AS sign_date 
						FROM
							outside
							JOIN online_app ON outside.YEAR = online_app.`year` 
							AND outside.class_no = online_app.class_no 
							AND outside.term = online_app.term 
							AND online_app.yn_sel IN ( 1, 2, 3, 5, 8 )
							JOIN BS_user ON online_app.id = BS_user.idno
							LEFT JOIN outside_sign_log ON outside.id = outside_sign_log.oid 
							AND online_app.id = outside_sign_log.idno 
						WHERE
							outside.id = '%s' 
						GROUP BY
							online_app.id 
						ORDER BY
							online_app.st_no",$id);
    	$query = $this->db->query($sql);
    	$data = $query->result_array();

    	return $data;
    }

    public function getOutsideSignList($id){
    	$sql = sprintf("SELECT
							`outside`.`id`,
							`outside`.`class_name`,
							`outside_student`.`idno`,
							`outside_student`.`no`,
							`outside_student`.`name`,
							`outside`.`hours`,
							`outside`.`course_date` as sign_date
						FROM
							outside
							JOIN `outside_student` ON outside_student.id = outside.id 
							LEFT JOIN `outside_sign_log` ON `outside_sign_log`.`oid` = `outside_student`.`id` 
							AND `outside_sign_log`.`idno` = `outside_student`.`idno`
						WHERE
							`outside_student`.`id` = '%s' 
						GROUP BY
							`outside_student`.`idno` 
						ORDER BY
							outside_student.no",$id);
    	$query = $this->db->query($sql);
    	$data = $query->result_array();

    	return $data;
    }

    public function getSignTime($id,$idno,$type=''){
    	if($type == 'min'){
    		$this->db->select('min(sign_time) as sign_time,type');
    	} else if($type == 'max'){
    		$this->db->select('max(sign_time) as sign_time,type');
    	} else {
    		$this->db->select('sign_time,type');
    	}

    	$this->db->where('oid',$id);
    	$this->db->where('idno',$idno);

    	$query = $this->db->get('outside_sign_log');
    	$data = $query->result_array();

    	return $data;
    }
}
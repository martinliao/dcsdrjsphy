<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Volunteer_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();

        $this->tdb = $this->load->database('training',TRUE);

    }

    public function getVolunteerClassId($year,$class_no,$term){
    	$this->tdb->select('id');
    	$this->tdb->where('year',$year);
    	$this->tdb->where('class_no',$class_no);
    	$this->tdb->where('term',$term);
    	$query = $this->tdb->get('course');
    	$result = $query->result_array();

    	if(isset($result[0]['id']) && $result[0]['id'] > 0){
    		return $result[0]['id'];
    	}

    	return '-1';
    }

    public function getClassRoomId($room_id){
    	$this->tdb->select('id');
    	$this->tdb->where('room_id',$room_id);
    	$query = $this->tdb->get('classroom');
    	$result = $query->result_array();

    	if(isset($result[0]['id']) && $result[0]['id'] > 0){
    		return $result[0]['id'];
    	}

    	return '-1';
    }

    public function getCourseList($id){
    	$this->tdb->select('volunteer_calendar.*,classroom.name');
    	$this->tdb->join('volunteer_classroom','volunteer_classroom.id = volunteer_calendar.vcid');
    	$this->tdb->join('classroom','volunteer_classroom.classroomID = classroom.id');
    	$this->tdb->where('courseID',$id);
    	$this->tdb->order_by('volunteer_calendar.date,volunteer_calendar.start_time,volunteer_calendar.end_time');
    	$query = $this->tdb->get('volunteer_calendar');
    	$result = $query->result_array();
    	
    	return $result;
    }

    public function getVcid($room_id){
    	$this->tdb->select('volunteer_classroom.id');
    	$this->tdb->join('volunteer_classroom','volunteer_classroom.classroomID = classroom.id');
    	$this->tdb->where('classroom.room_id',$room_id);
    	$query = $this->tdb->get('classroom');
    	$result = $query->result_array();

    	if(isset($result[0]['id']) && $result[0]['id'] > 0){
    		return $result[0]['id'];
    	}

    	return '-1';
    }

    public function updateVolunteerCalendar($vid,$vcid,$change_date,$change_from_time,$change_to_time){
    	if(intval(preg_replace('/:/','',$change_from_time)) >= 1300 && intval(preg_replace('/:/','',$change_to_time)) < 1800){
            $type = 2;
        } else if(intval(preg_replace('/:/','',$change_to_time)) < 1300){
            $type = 1;
        } else if(intval(preg_replace('/:/','',$change_from_time)) >= 1800){
            $type = 3;
        }

        $start_time = substr($change_from_time,0,2).':'.substr($change_from_time,2,2).':00';
		$end_time = substr($change_to_time,0,2).':'.substr($change_to_time,2,2).':00';

    	$this->tdb->set('date',$change_date);
    	$this->tdb->set('vcid',$vcid);
    	$this->tdb->set('type',$type);
    	$this->tdb->set('start_time',$start_time);
    	$this->tdb->set('end_time',$end_time);
    	$this->tdb->where('id',$vid);

    	if($this->tdb->update('volunteer_calendar')){
    		return true;
    	}

    	return false;
    }

    public function insertClassRoom($room_id,$room_info){
    	$this->tdb->trans_start();

    	$this->tdb->set('room_id',addslashes($room_id));

        if(isset($room_info[0]['room_name']) && !empty($room_info[0]['room_name'])){
            $this->tdb->set('name',addslashes($room_info[0]['room_name']));
        }

        if(isset($room_info[0]['room_sname']) && !empty($room_info[0]['room_sname'])){
            $this->tdb->set('sname',addslashes($room_info[0]['room_sname']));
        }

        if(isset($room_info[0]['room_bel']) && !empty($room_info[0]['room_bel'])){
            $this->tdb->set('belongto',addslashes($room_info[0]['room_bel']));
        }
    	
    	$this->tdb->insert('classroom');
    	$rid = $this->tdb->insert_id();

    	$this->tdb->set('volunteerID','1');
    	$this->tdb->set('classroomID',$rid);
    	$this->tdb->insert('volunteer_classroom');
    	$vcid = $this->tdb->insert_id();

    	$this->tdb->trans_complete();

        if($this->tdb->trans_status() === TRUE){
            return $vcid;
        } 

        return '-1';
    }

    public function updateVolunteerCalendarRoom($volunteer_class_id,$vcid,$old_vcid,$old_upd_course_date,$volunteer_type=-1){
    	$this->tdb->set('vcid', intval($vcid));
    	$this->tdb->where('courseID', intval($volunteer_class_id));
    	$this->tdb->where('vcid', intval($old_vcid));
    	$this->tdb->where('date', addslashes($old_upd_course_date));

		if($volunteer_type > 0){
			$this->tdb->where('type', intval($volunteer_type));
		}
    	
    	if($this->tdb->update('volunteer_calendar')){
    		return true;
    	}

    	return false;
    }

	public function getCourseInfo($id){
    	$this->tdb->select('volunteer_calendar.*,course.name');
    	$this->tdb->join('course','course.id = volunteer_calendar.courseID');
    	$this->tdb->where('volunteer_calendar.courseID',$id);
    	$this->tdb->order_by('volunteer_calendar.date,volunteer_calendar.start_time,volunteer_calendar.end_time');
    	$query = $this->tdb->get('volunteer_calendar');
    	$result = $query->result_array();
    	
    	return $result;
    }

	public function getVolunteerApplyUser($id){
		$this->tdb->select('users.name,users.email');
		$this->tdb->join('users', 'volunteer_calendar_apply.userID = users.id');
		$this->tdb->where('volunteer_calendar_apply.calendarID', $id);
		$this->tdb->where('volunteer_calendar_apply.got_it', 1);

		$query = $this->tdb->get('volunteer_calendar_apply');
    	$result = $query->result_array();

		return $result;
	}

	public function delVolunteerCalendarApply($id){
		$this->tdb->where('calendarID', $id);

		if($this->tdb->delete('volunteer_calendar_apply')){
			return true;
		}

		return false;
	}

	public function delVolunteerCalendar($id){
		$this->tdb->where('courseID', $id);

		if($this->tdb->delete('volunteer_calendar')){
			return true;
		}

		return false;
	}

	public function delVolunteerCourse($id){
		$this->tdb->where('id', $id);

		if($this->tdb->delete('course')){
			return true;
		}

		return false;
	}
}

?>
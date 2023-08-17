<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Teacher_lounge_model extends MY_Model
{
    public $table = 'teacher_lounge';
    public $pk = 'id';

    public function __construct()
    {
        parent::__construct();

        $this->init($this->table, $this->pk);

    }

    public function getReserveList($start_date, $end_date, $idno=''){
        $this->db->select('teacher_lounge.*, BS_user.name');
        $this->db->join('BS_user', 'BS_user.idno = teacher_lounge.creator', 'left');
        $this->db->where("teacher_lounge.reserve_date between '$start_date' and '$end_date'", null, false);

        if(!empty($idno)){
            $this->db->where('creator', $idno);
        }

        $query = $this->db->get($this->table);

        return $query->result_array();
    }

    public function getReserveInfo($id){
        $this->db->select('reserve_date, time_interval, lounge');
        $this->db->where('id', $id);
        $query = $this->db->get($this->table);
        $result = $query->result_array();

        return $result;
    }

    public function reserve($reserve_date, $time_interval, $lounge, $teacher_name, $class_name, $idno)
    {
        $this->db->set('reserve_date', $reserve_date);
        $this->db->set('time_interval', $time_interval);
        $this->db->set('lounge', $lounge);
        $this->db->set('teacher_name', $teacher_name);
        $this->db->set('class_name', $class_name);
        $this->db->set('creator', $idno);
        $this->db->set('modify_time', date('Y-m-d H:i:s'));

        if($this->db->insert($this->table)){
            return true;
        }
        
        return false;
    }

    public function reserveById($id, $teacher_name, $class_name){
        $this->db->set('teacher_name', $teacher_name);
        $this->db->set('class_name', $class_name);
        $this->db->set('modify_time', date('Y-m-d H:i:s'));
        $this->db->where('id', $id);

        if($this->db->update($this->table)){
            return true;
        }

        return false;
    }

    public function insertKeep($start_date, $end_date, $lounge, $idno){
        $this->db->set('start_date', $start_date);
        $this->db->set('end_date', $end_date);
        $this->db->set('lounge', $lounge);
        $this->db->set('creator', $idno);
        $this->db->set('create_time', date('Y-m-d H:i:s'));

        if($this->db->insert('keep_teacher_lounge')){
            return true;
        }
        
        return false;
    }

    public function checkReserve($start_date, $end_date, $lounge){
        $this->db->select('count(1) cnt');
        $this->db->where("reserve_date between '$start_date' and '$end_date'", null, false);
        $this->db->where('lounge', $lounge);
        $query = $this->db->get($this->table);
        $result = $query->result_array();

        if($result[0]['cnt'] > 0){
            return true;
        }

        return false;
    }

    public function checkKeep($start_date, $end_date, $lounge){
        $this->db->select('count(1) cnt');
        $this->db->where("((start_date between '$start_date' and '$end_date') or (end_date between '$start_date' and '$end_date'))", null, false);
        $this->db->where('lounge', $lounge);
        $query = $this->db->get('keep_teacher_lounge');
        $result = $query->result_array();

        if($result[0]['cnt'] > 0){
            return true;
        }

        return false;
    }

    public function checkKeepForReserve($reserve_date,$lounge){
        $this->db->select('count(1) cnt');
        $this->db->where("'$reserve_date' between start_date and end_date", null, false);
        $this->db->where('lounge', $lounge);
        $query = $this->db->get('keep_teacher_lounge');
        $result = $query->result_array();

        if($result[0]['cnt'] > 0){
            return true;
        }

        return false;
    }

    public function getKeepList($idno){
        $this->db->select('*');
        $this->db->where('creator', $idno);
        $query = $this->db->get('keep_teacher_lounge');
        $result = $query->result_array();

        return $result;
    }

    public function getKeepListInfo($start_date){
        $this->db->select('keep_teacher_lounge.*, BS_user.name');
        $this->db->join('BS_user', 'BS_user.idno = keep_teacher_lounge.creator');
        $this->db->where("'$start_date' between start_date and end_date", null, false);
        $query = $this->db->get('keep_teacher_lounge');
        $result = $query->result_array();

        return $result;
    }

    public function cancelKeep($id){
        $this->db->where('id', $id);
        
        if($this->db->delete('keep_teacher_lounge')){
            return true;
        }

        return false;
    }

    public function delReserve($id){
        $this->db->where('id', $id);

        if($this->db->delete($this->table)){
            return true;
        }

        return false;
    }
}
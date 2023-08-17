<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class teacher_log_model extends MY_Model
{	
    public $table = 'teacher_log';
    public $pk = '';

    public function __construct()
    {
        parent::__construct();
    }	

    public function getLogByTeacher($idno, $teacher_type){
        $this->db->select("tl.*, u.name upd_name, cc.city_name, sc.subcity_name")
                 ->from("teacher_log tl")
                 ->join('BS_user u', 'u.username = tl.upd_user', 'left')
                 ->join('co_city cc', 'cc.city = tl.city', 'left')
                 ->join('co_subcity sc', 'sc.city = tl.subcity', 'left')                
                 ->where('tl.id', $idno)
                 ->where('tl.teacher_type', $teacher_type)
                 ->order_by('action_dt desc');
        $query = $this->db->get();
        return $query->result();
    }
}
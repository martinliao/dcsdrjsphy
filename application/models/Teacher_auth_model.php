<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class teacher_auth_model extends MY_Model
{	
    public $table = 'teacher_auth';
    public $pk = '';

    public function __construct()
    {
        parent::__construct();
    }	

    public function find($condition){
        $this->db->select("*")
                 ->from("teacher_auth")
                 ->where('year', $condition['year'])
                 ->where('class_no', $condition['class_no'])
                 ->where('term', $condition['term'])
                 ->where('teacher_id', $condition['teacher_id'])
                 ->order_by('id desc');
        $query = $this->db->get();
        return $query->row();
    }
}
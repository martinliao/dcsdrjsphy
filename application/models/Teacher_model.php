<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Teacher_model extends MY_Model
{   
    public $table = 'teacher';
    public $pk = 'id';

    public function __construct()
    {
        parent::__construct();
    }   

    public function getForArriveSelect($queryData)
    {
        $this->db->start_cache();

        $this->db->select("teacher.id, teacher.idno, teacher.name")
                 ->from('teacher');

        if (isset($queryData['idno'])){
            $this->db->where('idno LIKE', "%".$queryData['idno']."%");
        }

        if (isset($queryData['member_name'])){
            $this->db->where('name LIKE', "%".$queryData['member_name']."%");
        }

        $this->db->stop_cache(); 

        $this->paginate();

        $query = $this->db->get();

        $this->db->flush_cache(); 
        
        return $query->result();
    }
}
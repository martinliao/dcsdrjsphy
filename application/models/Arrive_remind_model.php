<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Arrive_remind_model extends MY_Model
{   
    public $table = 'arrive_reminds';
    public $pk = 'id';

    public function __construct()
    {
        parent::__construct();
    }   

    public function deleteByIds($ids)
    {
    	return $this->db->where_in('id', $ids)
    			 		->delete('arrive_reminds');
    }

    public function getForList($queryData)
    {
    	$this->db->start_cache();

        $this->db->from('arrive_reminds');

        if ($queryData['member_type'] == 'teacher'){
    		$this->db->select("arrive_reminds.*, teacher.name")
    				 ->join('teacher', 'teacher.idno = arrive_reminds.idno');
            if (isset($queryData['member_name'])){
                $this->db->where('teacher.name LIKE', '%'.$queryData['member_name'].'%');
            }                     
    	}elseif ($queryData['member_type'] == 'student'){
    		$this->db->select("arrive_reminds.*, BS_user.name")
    				 ->join('BS_user', 'BS_user.idno = arrive_reminds.idno');
            if (isset($queryData['member_name'])){
                $this->db->where('BS_user.name LIKE', '%'.$queryData['member_name'].'%');
            }                      
    	}

    	if (isset($queryData['idno'])){
    		$this->db->where('arrive_reminds.idno LIKE', '%'.$queryData['idno'].'%');
    	}
        // dd($queryData);
    	// if (isset($queryData['member_name'])){
    	// 	$this->db->where('BS_user.name LIKE', '%'.$queryData['member_name'].'%');
    	// }

    	$this->db->where('member_type', $queryData['member_type']);

        $this->db->stop_cache(); 

        //$this->paginate();

        $query = $this->db->get();
        $this->db->flush_cache(); 

        return $query->result();
    }
}
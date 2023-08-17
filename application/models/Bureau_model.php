<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Bureau_model extends MY_Model
{	
    public $table = 'bureau';
    public $pk = 'id';

    public function __construct()
    {
        parent::__construct();
    }	

    public function getList($condition, $page, $rows){

    	$this->db->select ("*")
    			 ->from($this->table)
    			 ->where("name LIKE", '%'.$condition['keyword'].'%');

        // 包含裁撤機關
        if ($condition["agency"]) {
        	$this->db->group_start()
        			 ->where("del_flag <>", 'C')
        			 ->or_where("del_flag", null)
        			 ->group_end();
        }

        $this->db->limit($rows, $rows * ($page - 1));
        $query = $this->db->get();
        $list['data'] = $query->result();
        $list['count'] = $this->getListCount($condition);
        return $list;    	
    }
    public function getListCount($condition){
    	$this->db->select ("*")
    			 ->from($this->table)
    			 ->where("name LIKE", '%'.$condition['keyword'].'%');

        // 包含裁撤機關
        if ($condition["agency"]) {
        	$this->db->group_start()
        			 ->where("del_flag <>", 'C')
        			 ->or_where("del_flag", null)
        			 ->group_end();
        }

        return $this->db->count_all_results(); 	
    }
    
}
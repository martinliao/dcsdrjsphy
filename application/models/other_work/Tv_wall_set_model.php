<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Tv_wall_set_model extends MY_Model
{
    public $table = 'rotation_play';
    public $pk = 'id';

    public function __construct()
    {
        parent::__construct();

        $this->init($this->table, $this->pk);
    }	

    public function getPhoto(){
		$this->db->select('*');
		$this->db->order_by('order_id asc');
		$query=$this->db->get('rotation_play');
		$result=$query->result_array();
		
    	return $result;
    }

    public function getDataCount($condition){
    	$this->db->select("count(*) count")
    			 ->from("class_info");
    	// 搜尋欄位
		if (!empty($condition["title"])){
    		$this->db->where("title LIKE", "%".$condition["title"]."%");			
		}
		if (!empty($condition["start_date"])){
    		$this->db->where("start_date >=", $condition["start_date"]);			
		}
		if (!empty($condition["end_date"])){
    		$this->db->where("end_date <=", $condition["end_date"]);			
		}

    	$query = $this->db->get();
    	return $query->row()->count;
    }

    public function getRotationPlaySetup(){
        $this->db->select("*");
        $query = $this->db->get('rotation_play_setup');
        $result = $query->result_array();

        return $result;
    }

}
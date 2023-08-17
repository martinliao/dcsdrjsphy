<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Class_info_model extends MY_Model
{
    public $table = 'class_info';
    public $pk = 'id';

    public function __construct()
    {
        parent::__construct();

        $this->init($this->table, $this->pk);
    }	

    public function getList($condition, $rows = 10){
    	$this->db->select("*");
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

    	// 排序
    	if (!empty($condition["sort"])) {
    		$this->db->order_by($condition["sort"]);
    	}
    	$page = (!empty($condition['page'])) ? $condition['page'] : 1;
		$query = $this->db->get('class_info', $rows * $page, $rows * ($page - 1));
		$list["data"] = $query->result();
		$list["count"] = $this->getDataCount($condition);
    	return $list;
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

}
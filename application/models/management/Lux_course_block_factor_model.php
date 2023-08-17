<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Lux_course_block_factor_model extends MY_Model
{
    public $table = 'lux_course_block_factor';
    public $pk = 'id';

    public function __construct()
    {
        parent::__construct();

        $this->init($this->table, $this->pk);

    }

    public function getBlockSetting($conditions=array()) {
    	$this->db->select('LCBF.type, LCBF.value, JL3.limit_name');
		$this->db->from('lux_course_block_factor LCBF');
		$this->db->join('enroll_condition_3 JL3', 'LCBF.value=JL3.id AND LCBF.type=3', 'LEFT OUTER JOIN');
		$this->db->where("LCBF.year", $conditions['year']);
        $this->db->where("LCBF.class_no", $conditions['class_no']);
        $this->db->where("LCBF.term", $conditions['term']);

		$query = $this->db->get();
        $data = $query->result_array();

	    $list = array('0', '0', array());
	    foreach($data as $row){
	    	if($row["type"]=='1') {
		      $list['0'] = $row["value"];
		    }
		    if($row["type"]=='2') {
		      $list['1'] = $row["value"];
		    }
		    if($row["type"]=='3') {
		      $list['2'][] = array("value"=>$row["value"], "text"=>$row["limit_name"]);
		    }
	    }

	    return $list;
	}


}
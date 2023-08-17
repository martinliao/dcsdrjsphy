<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Stud_modify_model extends MY_Model
{
    public $table = 'stud_modify';
    public $pk = '';

    public function __construct()
    {
        parent::__construct();
        $this->init($this->table, $this->pk);
    }

    public function find($year, $class_no, $term){
        $this->db->select("sm.*, r.class_name, date_format(r.start_date1, '%Y-%m-%d') start_date1, date_format(r.end_date1, '%Y-%m-%d') end_date1, date_format(r.apply_e_date, '%Y-%m-%d') apply_e_date, date_format(r.co_sheet_open_sdate, '%Y-%m-%d') co_sheet_open_sdate, date_format(r.co_sheet_open_edate, '%Y-%m-%d') co_sheet_open_edate,r.no_persons ")
                 ->from("require r")
                 ->join("stud_modify sm", "r.year = sm.year AND r.class_no = sm.class_no AND r.term = sm.term", "left")
                 ->where("r.class_no", $class_no)
                 ->where("r.term", $term)
                 ->where("r.year", $year);
        $query = $this->db->get();
        return $query->row_array();
    }

}
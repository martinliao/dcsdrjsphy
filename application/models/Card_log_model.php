<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Card_log_model extends MY_Model
{	
    public $table = 'card_log';
    public $pk = '';

    public function __construct()
    {
        parent::__construct();
    }	

    public function getStudentCardLog($class_info, $idno){
        $this->db->select("*")
                 ->from("card_log")
                 ->where($class_info)
                 ->where("gid", $idno);
        $query = $this->db->get();
        return $query->result();
    }

}
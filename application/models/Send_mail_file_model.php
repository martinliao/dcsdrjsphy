<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Send_mail_file_model extends MY_Model
{	
    public $table = 'send_mail_file';
    public $pk = '';

    public function __construct()
    {
        parent::__construct();
    }	
    public function getList($condition){
        $this->db->select("smf.*, user.name")
                 ->from("send_mail_file smf")
                 ->join("BS_user user", "user.username = smf.cre_user")
                 ->where("smf.year", $condition['year'])
                 ->where("smf.class_no", $condition['class_no'])
                 ->where("smf.term", $condition['term']);
        $query = $this->db->get();
        return $query->result();
    }
}
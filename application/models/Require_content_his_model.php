<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Require_content_his_model extends MY_Model
{
    public $table = 'require_content_his';
    public $pk = '';

    public function __construct()
    {
        parent::__construct();
    }
    /*
        搬移 
    */
    public function moveFromRequireContent($class_info){
        $sql = "INSERT INTO require_content_his SELECT * FROM `require_content` WHERE year=? AND class_no=? AND term=?";
        $result = $this->db->query($sql, array($class_info['year'], $class_info['class_no'], $class_info['term']));
        return $result;
    }

}
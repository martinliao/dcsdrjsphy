<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Require_content_model extends MY_Model
{
    public $table = 'require_content';
    public $pk = '';

    public function __construct()
    {
        parent::__construct();
    }
    /*
        搬移 舊功能早就壞了先放著不做
    */
    public function copyFromRequire($class_info){
        // $sql = "INSERT INTO require_his SELECT * FROM `require` WHERE year='108' AND class_no='A00014' AND term='1'";

        // $this->db->query($sql, [$class_info['year'], $class_info['class_no'], $class_info['term']]);
        // dd($this->db->last_query());
    }

}
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Textbook_download extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("canteach_model");
        if(!isset($this->data['filter']['query_year'])){
            $this->data['filter']['query_year']=date('Y')-1911;
        }
    }

    public function index()
    {
        $condition = $this->getFilterData(['query_year', 'term', 'class_no', 'class_name', 'course_name', 'teacher', 'queryFile', 'b_name']);
        $condition['idno']=$this->flags->user['idno'];
        //var_dump($condition);
        $this->data['files'] = $this->canteach_model->getDownloadList($condition);
        // dd($this->data['files']);
        foreach ($this->data['files'] as $row) {
            if($row->id!=""){
                $this->db->select('name');
                $this->db->where('idno',$row->id);
                $this->db->where('teacher','Y');
                $test=$this->db->get('teacher');
                $test=$test->result_array();
                //var_dump($test);
                $row->tname=$test[0]['name'];
            }else{
                $row->tname=null;
            }
            
            
        }

         
        
        $this->data['link_refresh'] = base_url("student/textbook_download/");
        $this->layout->view('student/textbook_download/list',$this->data);
    }

}

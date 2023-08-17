<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Signup_change_report extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model([
            "require_model",
            "stud_modifylog_model",
            "Bs_user_model"
        ]);      
        
        
        if(!isset($this->data['filter']['query_year'])) {
            $this->data['filter']['query_year'] = date('Y')-1911;
        }
        if(!isset($this->data['filter']['query_class_no'])) {
            $this->data['filter']['query_class_no'] = "";
        }
        if(!isset($this->data['filter']['class_name'])) {
            $this->data['filter']['class_name'] = "";
        }
        if(!isset($this->data['filter']['search_mode'])) {
            $this->data['filter']['search_mode'] = "請選擇";
        }
    }

    public function index()
    {
        //$condition = $this->getFilterData(['term', 'class_no', 'class_name']);
        //$condition['year'] = $this->getFilterData('year', date("Y")-1911);

        if($this->data['filter']['query_year']!=""){
            $condition['year']=$this->data['filter']['query_year'];
        }
        if($this->data['filter']['query_class_no']!=""){
            $condition['class_no']=$this->data['filter']['query_class_no'];
        }
        if($this->data['filter']['class_name']!=""){
            $condition['class_name']=$this->data['filter']['class_name'];
        }
        
        $condition['select'] = 'r.year,r.class_no,r.term,r.class_name,r.range,r.apply_s_date,r.apply_e_date,r.apply_s_date2,r.apply_e_date2,r.start_date1,r.end_date1, as.ext1 phone, user.name worker';
        $this->data['requires'] = $this->require_model->getList($condition);
        $this->data['link_refresh'] = base_url("management/signup_change_report/");
        $this->layout->view('management/signup_change_report/list',$this->data);
    }

    public function detail()
    {   

        //var_dump($search_mode);
        $class_info = $this->getFilterData(['term', 'class_no', 'year']);
        $condition = $class_info;
        // $condition['search_mode']=$search_mode;
        $this->data['require'] = $this->require_model->getModifyInfo($class_info);
        $this->data['stud_modifylogs'] = $this->stud_modifylog_model->getModifyLogByRequire($condition);
        $this->data['stud_modify']=$this->stud_modifylog_model->getModify($condition);
        $this->data['link_cancel'] = 'history_go_back';
        

        //var_dump($this->data['stud_modifylogs']);
        // dd($this->data['stud_modifylogs']);
        $this->data['link_refresh'] = base_url("management/signup_change_report/");
        $this->layout->view('management/signup_change_report/detail',$this->data);
    }

}

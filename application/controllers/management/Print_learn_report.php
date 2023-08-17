<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Print_learn_report extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model(['require_model']);

        if(!isset($this->data['filter']['query_year'])) {
            $this->data['filter']['query_year'] = date('Y')-1911;
        }
        if(!isset($this->data['filter']['class_no'])) {
            $this->data['filter']['class_no'] = "";
        }
        if(!isset($this->data['filter']['class_name'])) {
            $this->data['filter']['class_name'] = "";
        }
    }

    public function index()
    {
        $idno=$this->flags->user['idno'];
        $group_id=$this->flags->user['group_id'];
        $key1 = in_array('1', $group_id);
        $key2 = in_array('9', $group_id);

        if(!$key1 && !$key2){
            $condition['idno']=$idno;
        }
        if($this->data['filter']['query_year']!=""){
            $condition['year']=$this->data['filter']['query_year'];
        }
        if($this->data['filter']['class_no']!=""){
            $condition['class_no']=$this->data['filter']['class_no'];
        }
        if($this->data['filter']['class_name']!=""){
            $condition['class_name']=$this->data['filter']['class_name'];
        }

        //$condition = $this->getFilterData(['term', 'class_no']);
        //$condition['year'] = $this->getFilterData('year', date("Y")-1911);
        $this->data['requires'] = $this->require_model->getList($condition);
        $this->data['link_refresh'] = base_url("management/print_learn_report/");
        $this->layout->view('management/print_learn_report/list',$this->data);
    }
    public function detail()
    {
        $condition = $this->getFilterData('seq_no');
        $this->data['bureaus'] =$this->require_model->getBureau($condition);
        $this->data['link_refresh'] = base_url("management/print_learn_report/detail");
        $this->data['link_cancel'] = 'history_go_back';

        $this->layout->view('management/print_learn_report/detail',$this->data);
    }
}

<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Class_info_ha extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model(['require_model', 'online_app_model']);
    }

    public function index()
    {
        // $this->output->enable_profiler(TRUE);
        $this->data['filter']['query_month'] = $this->getFilterData('query_month');
        $this->data['filter']['query_year'] = $this->getFilterData('query_year', (int)date("Y")-1911);
        $this->data['filter']['query_type'] = $this->getFilterData('query_type');
        $this->data['filter']['class_no'] = $this->getFilterData('class_no');
        $this->data['filter']['class_name'] = $this->getFilterData('class_name');
        $this->data['bureau_id']=$this->flags->user['bureau_id'];
        //var_dump($bureau_id);

        $this->data['uid'] = $this->flags->user['id'];
        $bureau_id = $this->flags->user['bureau_id'];
        $condition = $this->getFilterData(['query_type', 'class_no', 'class_name', 'query_month', 'query_year']); 
        
        if ($this->data['filter']['query_type'] != null){
            $this->data['requires'] = $this->require_model->getRequireInfoByStatus($condition, $bureau_id);
            //var_dump($this->data['requires'][0]->seq_no);          
        }else{
            $this->data['requires'] = [];
        }
        $this->data['link_refresh'] = $_SERVER['REQUEST_URI'];
        $this->data['now'] = date("Y-m-d H:i:s");
        $this->layout->view('customer_service/class_info_ha/index', $this->data);
    }

    public function cancel_list(){
        $class_info = $this->getFilterData(['year', 'class_no', 'term']);
        $bureau_id = $this->flags->user['bureau_id'];
        $this->data['require'] = $this->require_model->find($class_info);
        $this->data['cancel_list'] = $this->online_app_model->getCancelList($class_info, $bureau_id);
        $this->load->view('customer_service/class_info_ha/cancel_list', $this->data);
    }
    /*
        未選員名單
    */
    public function student_no_record(){
        $class_info = $this->getFilterData(['year', 'class_no', 'term']);
        $bureau_id = $this->flags->user['bureau_id'];
        $this->data['require'] = $this->require_model->find($class_info);
        $this->data['no_records'] = $this->online_app_model->getNoRecord($class_info, $bureau_id);
        $this->load->view('customer_service/class_info_ha/no_record', $this->data);
    }
}

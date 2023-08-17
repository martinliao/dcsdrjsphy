<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Co_admin_student_sel extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model(['require_model', 'online_app_model']);
        
    }

    public function index()
    {
        $condition = $this->getFilterData(['year', 'class_no', 'term', 'class_name', 'student_name', 'student_idno', 'start_date', 'end_date']);
        $this->data['condition'] = $condition;
        //var_dump($this->input->post());
        //if($this->data['value']==1){
        if(!empty($this->input->get())){
            $this->data['requires'] = $this->require_model->getList($condition);
        }
        //}
       // var_dump($this->data['value']);
        $this->data['link_refresh'] = base_url("customer_service/co_admin_student_sel");
        $this->layout->view('customer_service/col_admin_student_sel/list',$this->data);
    }

    public function detail(){
        $class_info = $this->getFilterData(['year', 'class_no', 'term']);
        $this->data['require'] = $this->require_model->find($class_info);
        $this->data['enrolls'] = $this->online_app_model->getEnroll($class_info);
        $this->load->view('customer_service/col_admin_student_sel/detail', $this->data);
               
    }
}

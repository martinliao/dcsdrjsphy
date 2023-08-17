<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mail_log_search extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model(["mail_log_model"]);
    }

    public function index()
    {
        $condition = $this->getFilterData(['year', 'class_no', 'class_name']);

        $this->data['mail_logs'] = $this->mail_log_model->getList($condition);
        $this->data['link_refresh'] = base_url("customer_service/mail_log_search/");
        $this->layout->view('customer_service/mail_log_search/list',$this->data);
    }

}

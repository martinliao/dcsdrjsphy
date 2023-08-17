<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Change_practice_download extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model(['require_model', 'send_mail_file_model']);
        if (!isset($this->data['filter']['year'])) {
            $this->data['filter']['year'] = date('Y')-1911;
        }
        if (!isset($this->data['filter']['class_no'])) {
            $this->data['filter']['class_no'] = '';
        }
        if (!isset($this->data['filter']['class_name'])) {
            $this->data['filter']['class_name'] = '';
        }
    }

    public function index()
    {
        $condition = $this->getFilterData(['year', 'class_no', 'class_name']);
        $this->data['requires'] = $this->require_model->getList($condition);
        $this->data['link_refresh'] = base_url("management/change_practice_download/");
        $this->layout->view('management/change_practice_download/list',$this->data);
    }
    public function detail()
    {
        $condition = $this->getFilterData(['year', 'class_no', 'term']);
        $this->data['require'] = $this->require_model->find($condition);
        $this->data['mail_files'] =$this->send_mail_file_model->getList($condition);
        $this->data['link_refresh'] = base_url("management/change_practice_download/");
        $this->layout->view('management/change_practice_download/detail',$this->data);
    }

}

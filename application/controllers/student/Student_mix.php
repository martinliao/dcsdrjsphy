<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Student_mix extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('online_app_model');
    }

    public function index()
    {
    	$condition = $this->getFilterData(['year', 'month']);
    	$condition['student_id'] = $this->flags->user['idno'];
        $this->data['stu_mixs'] = $this->online_app_model->getStuMixsForGroup($condition);

        $this->data['link_refresh'] = base_url("other_work/card_rotation/");
        $this->layout->view('student/student_mix/list',$this->data);
    }

}

<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Course_admit extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model(['online_app_model', 'room_use_model', 'require_model']);
    }

    public function index()
    {
        $condition = $this->getFilterData(['class_no', 'class_name', 'bureau_name', 'year', 'start']);
        $condition['year'] = $this->getFilterData('year', (int)date('Y')-1911);
        $this->data['pass_list'] = $this->online_app_model->getPassList($condition, $this->flags->user);

        $this->data['link_refresh'] = base_url("student/course_admit/");
        $this->layout->view('student/course_admit/list',$this->data);
    }
    public function detail()
    {
        $class_info = $this->getFilterData(['year', 'class_no', 'term']);
        $this->data['phy_schedule'] = $this->room_use_model->getPhySchedule_new($class_info);

        $this->data['students'] = $this->online_app_model->getCourseUserList($class_info);
        $this->data['require'] = $this->require_model->find($class_info);
        // dd($this->data['phy_schedule']);
        $muti_room = false;
        if (!empty($this->data['phy_schedule'])){
            $room_use = $this->data['phy_schedule'][0]->room_name;
            for($i=0;$i<count($this->data['phy_schedule']);$i++){
                if($i > 0 && $this->data['phy_schedule'][$i]->use_date == $this->data['phy_schedule'][$i-1]->use_date && $this->data['phy_schedule'][$i]->use_id == $this->data['phy_schedule'][$i-1]->use_id && $this->data['phy_schedule'][$i]->from_time == $this->data['phy_schedule'][$i-1]->from_time && $this->data['phy_schedule'][$i]->to_time == $this->data['phy_schedule'][$i-1]->to_time && $this->data['phy_schedule'][$i]->room_name == $this->data['phy_schedule'][$i-1]->room_name){
                    $this->data['phy_schedule'][$i-1]->teacher_name .= '<br>'.$this->data['phy_schedule'][$i]->teacher_name;
                    $this->data['phy_schedule'][$i]->display = -1; 
                }

                if ($room_use != $this->data['phy_schedule'][$i]->room_name){
                    $muti_room = true;
                }
            }
        }

        $this->data['muti_room'] = $muti_room;
        $this->data['room_name'] = (isset($room_use)) ? $room_use : '';
        $this->data['link_refresh'] = base_url("student/course_admit/detail");
        $this->load->view('student/course_admit/detail',$this->data);
    }

}

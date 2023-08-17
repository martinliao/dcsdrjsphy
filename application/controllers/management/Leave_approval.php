<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Leave_approval extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('management/leave_approval_model');
        $this->load->model('management/leave_model');

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
        if($this->data['filter']['query_year']!=""){
            $condition['year']=$this->data['filter']['query_year'];
        }
        if($this->data['filter']['class_no']!=""){
            $condition['class_no']=$this->data['filter']['class_no'];
        }
        if($this->data['filter']['class_name']!=""){
            $condition['class_name']=$this->data['filter']['class_name'];
        }

        if(!$this->input->get()){
            $default = date('Y-m-d');
        } else {
            $default = '';
        }

        $this->data['list'] = $this->leave_approval_model->getList($condition,$default);

        $this->data['link_refresh'] = base_url("management/leave_approval/");
        $this->layout->view('management/leave_approval/list',$this->data);
    }

    public function approval(){
        $class_info = $this->getFilterData(['class_no', 'year', 'term']);
        $this->data['class_info'] = $class_info;
        $this->data['list'] = $this->leave_approval_model->getLeaveDetail($class_info);
        $class_info['class_id'] = $class_info['class_no'];

        unset($class_info['class_no']);
        $this->data['room_uses'] = $this->leave_model->getRoomUse($class_info);
        $this->data['link_approval'] = base_url("management/leave_approval/approval_confirm");
        $this->data['link_edit'] = base_url("management/leave_approval/edit?{$_SERVER['QUERY_STRING']}");
        $this->data['link_delete2'] = base_url("management/leave_approval/delete");
        $this->data['link_cancel'] = base_url("management/leave_approval");
        $this->data['link_refresh'] = base_url("management/leave_approval/approval?{$_SERVER['QUERY_STRING']}");
        $this->data['link_getstud'] = base_url("management/leave/getStudent");

        $this->layout->view('management/leave_approval/approval',$this->data);    
    }

    public function approval_confirm(){
        $post = $this->input->post();
        $list = $this->input->post('chk');
        $mode = $this->input->post('mode');
        if(count($list) > 0){   
            if($mode == 'confirm'){
                for($i=0;$i<count($list);$i++){
                    $status = $this->leave_approval_model->approval_confirm($list[$i],$this->flags->user['name']);
                }
                
                if ($status){
                    $this->setAlert(1, '批核成功');
                } else {
                    $this->setAlert(2, '批核失敗');
                }
            } else if($mode == 'delete'){
                if(count($list) > 1){
                    $this->setAlert(2, '只能選擇一筆');
                } else {
                    for($i=0;$i<count($list);$i++){
                        $status = $this->leave_approval_model->approval_delete($list[$i]);
                    }
                    
                    if ($status){
                        $this->setAlert(1, '刪除成功');
                    } else {
                        $this->setAlert(2, '刪除失敗');
                    }
                }
            }
            
            redirect(base_url("management/leave_approval/approval?year={$post['year']}&term={$post['term']}&class_no={$post['class_no']}"));
        } else {
            $this->setAlert(2, '請至少選擇一筆');
            redirect(base_url("management/leave_approval/approval?year={$post['year']}&term={$post['term']}&class_no={$post['class_no']}"));
        }
    }

    public function add()
    {
        $post = $this->input->post();
        //if ($this->form_validation->run("add_leave") !== FALSE)
        {
            $insert = $this->leave_approval_model->add_leave($post);
            if ($insert){
                $this->setAlert(2, '新增成功');
            }            
            redirect(base_url("management/leave_approval/approval?year={$post['year']}&term={$post['term']}&class_no={$post['class_no']}"));
        }   
    }

    public function edit()
    {

        $id = $this->input->get('id');
        if($this->input->post()){
            $from_time = $this->input->post('start_hour').$this->input->post('start_minute');
            $to_time = $this->input->post('end_hour').$this->input->post('end_minute');
            $vacation_date = $this->input->post('vacation_date');
            $hours = ceil((((intval($this->input->post('end_hour'))-intval($this->input->post('start_hour')))*3600)+((intval($this->input->post('end_minute'))-intval($this->input->post('start_minute')))*60))/3600);

            $status = $this->leave_approval_model->updateLeave($id,$from_time,$to_time,$vacation_date,$hours);
            
            if($status){
                $this->setAlert(1, '修改成功');
            } 
        }

        $class_info = $this->getFilterData(['class_no', 'year', 'term']);
        $class_info['class_id'] = $class_info['class_no'];

        unset($class_info['class_no']);
        $this->data['room_uses'] = $this->leave_model->getRoomUse($class_info);

        

        $this->data['info'] = $this->leave_approval_model->getStudentLeaveInfo($id);
        
        $this->layout->view('management/leave_approval/edit',$this->data); 
    }

}

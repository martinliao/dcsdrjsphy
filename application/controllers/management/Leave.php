<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Leave extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('management/leave_model');
        $this->load->model('management/leave_approval_model');
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
        //$condition = $this->getFilterData(['class_no', 'class_name']);
        //$condition['year'] = $this->getFilterData('year', date("Y")-1911);
        //$worker=$this->flags->user['idno'];
        $group_id=$this->flags->user['group_id'];
        $worker="";
        if($this->data['filter']['query_year']!=""){
            $condition['year']=$this->data['filter']['query_year'];
        }
        if($this->data['filter']['class_no']!=""){
            $condition['class_no']=$this->data['filter']['class_no'];
        }
        if($this->data['filter']['class_name']!=""){
            $condition['class_name']=$this->data['filter']['class_name'];
        }
        /*$worker = $this->getFilterData('worker', 0);
        if ($worker){
            $worker = $this->flags->user['idno'];
        }*/
        $key1 = in_array('1', $group_id);
        $key2 = in_array('9', $group_id);

        if(!$key1 && !$key2){
            $worker=$this->flags->user['idno'];
        }
        //var_dump($worker);
        $this->data['requires'] = $this->leave_model->getRequires($condition, true, $worker);
        $this->data['link_refresh'] = base_url("management/leave/");
        $this->layout->view('management/leave/list',$this->data);
    }
    public function detail()
    {
        $this->data['link_save'] = "";
        $this->data['link_delete2'] = base_url("management/leave/delete");
        $class_info = $this->getFilterData(['class_no', 'year', 'term']);
        $this->data['class_info'] = $this->leave_model->getRequire($class_info);
        $this->data['link_refresh'] = base_url("/management/leave/detail?year={$class_info['year']}&term={$class_info['term']}&class_no={$class_info['class_no']}");
        $this->data['link_get_stud'] = base_url("/management/leave/getStudent");

        $post = $this->input->post();
        if (!empty($post['action'])){
            if ($post['action'] == "recount"){
                if ($this->form_validation->run("leave_recount") !== FALSE){
                    $this->data['leaves'] = $this->leave_model->getListInfoNew($class_info, false, ['sv.vacation_date' => $post['real_end_date']]);
                    $success = $this->recount($post);
                    if ($success){
                        $this->setAlert(2, '重新計算完成');
                    }else{
                        $this->setAlert(2, '重新計算失敗，參數有誤');
                    }
                }else{
                    $this->setAlert(1, '請輸入完整資訊');
                }                
            }else if($post['action'] == "update"){
                if ($this->form_validation->run("leave_update") !== FALSE){
                    $update = $this->update($post);
                    if ($update){
                        $this->setAlert(2, '已完成更新');
                    }
                }
            }else if ($post['action'] == "delete"){
                $seq_no = $this->input->post("chk");
                $delete = $this->leave_model->delete_vacation($class_info, $seq_no);
                if ($delete){
                    $this->setAlert(2, '刪除成功');
                }
            }
            redirect($this->data['link_refresh']);
        }

        $this->data['leaves'] = $this->leave_model->getListInfoNew($class_info);

        
        $this->data['leaves_online'] = $this->leave_approval_model->getLeaveDetail($class_info);

        $tmp_count = count($this->data['leaves']);
        for($i=0;$i<count($this->data['leaves_online']);$i++){
            // $hasOne = false;
            for($j=0;$j<$tmp_count;$j++){
                if($this->data['leaves_online'][$i]['st_no'] == $this->data['leaves'][$j]->st_no && $this->data['leaves_online'][$i]['vacation_date'] == $this->data['leaves'][$j]->vacation_date){
                    if(isset( $this->data['leaves'][$j]->online_from_time) && !empty($this->data['leaves'][$j]->online_from_time)){
                        $this->data['leaves'][$j]->online_from_time .= '<br>'.$this->data['leaves_online'][$i]['from_time'];
                    } else {
                        $this->data['leaves'][$j]->online_from_time = $this->data['leaves_online'][$i]['from_time'];
                    }
                    
                    if(isset( $this->data['leaves'][$j]->online_to_time) && !empty($this->data['leaves'][$j]->online_to_time)){
                        $this->data['leaves'][$j]->online_to_time .= '<br>'.$this->data['leaves_online'][$i]['to_time'];
                    } else {
                        $this->data['leaves'][$j]->online_to_time = $this->data['leaves_online'][$i]['to_time'];
                    }
                    // $hasOne = true;
                    continue;
                }
            }
            // if(!$hasOne){
            //     array_push($this->data['leaves'], $this->data['leaves_online'][$i]);
            // }
        }
    
        $class_info['class_id'] = $class_info['class_no'];
        unset($class_info['class_no']);
        $this->data['room_uses'] = $this->leave_model->getRoomUse($class_info);
        $this->data['link_cancel'] = base_url("/management/leave?query_year={$this->data['filter']['query_year']}&class_no={$this->data['filter']['class_no']}&class_name={$this->data['filter']['class_name']}&rows=10");

        $this->layout->view('management/leave/detail',$this->data);
    }

    private function recount($post){
        // dd($this->data['leaves']);
        if (DateTime::createFromFormat('Y-m-d', $post['real_end_date']) !== false){
            $post['real_end_time'] = (int)$post['real_end_time'];
            foreach($this->data['leaves'] as $leave){
                $leave->from_time = (int)$leave->from_time;
                // 請假開始時間小於實際下課時間 => 刪除
                
                if (isset($post['real_end_time'])){
                    if ($post['real_end_time'] <= $leave->from_time){
                        $this->leave_model->delete($leave->seq_no);
                        continue;
                    } 
                }

                if (isset($post['real_start_time'])){
                    if ($post['real_start_time'] >= $leave->to_time){
                        $this->leave_model->delete($leave->seq_no);
                        continue;
                    } 
                }
    
            }
            return true;            
        }else{
            return false;
        }

    }

    private function update($post){

        $fields = ['va_code', 'vacation_date', 'from_time', 'to_time', 'hours'];
        $fields_text = ['假別', '請假日期', '請假開始時間', '請假結束時間', '請假時數'];
        $leaves = [];
        $all_update = true;
        $error = "";
        foreach($post['leaves'] as $seq_no => $leave){
            $ok = -1;
            foreach ($leave as $key => $value){
                if (empty($value)) {
                    $ok=$key;
                    break;
                }
            }  
            if ($ok == -1){
                $this->leave_model->update($seq_no, $leave);
            }else{
                $all_update = true;
                $error .= "流水號{$seq_no} {$fields_text[$ok]}資料欄位不得為空<br>";
            }
            
        }            
        if (!empty($error)){
            $this->setAlert(3, $error);  
            return false;
        }
        
        return true;
    }
    /*
    **  查詢某個班有無該學員
    **  @params st_no 學號
    */
    public function getStudent(){
        $student_info = $this->getFilterData(['year', 'class_no', 'term', 'st_no']);
        $student = $this->leave_model->findStudent($student_info);
        $student = [
            "student" => $student,
            "status" => "0"
        ];

        if (empty($student['student'])){
            $student['status'] = 1;
        } 
    
        echo json_encode($student);
    }

    public function add(){
        $post = $this->input->post();
        //if ($this->form_validation->run("add_leave") !== FALSE)
        {
            $insert = $this->leave_model->add_s_vacation($post);
            if ($insert){
                $this->setAlert(2, '新增成功');
            }            
            redirect(base_url("management/leave/detail?year={$post['year']}&term={$post['term']}&class_no={$post['class_no']}"));
        }      
        
    }

}

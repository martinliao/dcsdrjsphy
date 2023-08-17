<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Not_reported extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('management/not_reported_model');
        if(!isset($this->data['filter']['query_year'])) {
            $this->data['filter']['query_year'] = date('Y')-1911;
        }
        if(!isset($this->data['filter']['checkAll'])) {
            $this->data['filter']['checkAll'] = "";
        }
        if(!isset($this->data['filter']['class_name'])) {
            $this->data['filter']['class_name'] = "";
        }
        if(!isset($this->data['filter']['class_no'])) {
            $this->data['filter']['class_no'] = "";
        }
    }

    public function index()
    {
        $condition = $this->getFilterData(['class_no', 'class_name']);
        $condition['year'] = $this->getFilterData('query_year', date("Y")-1911);
        $worker = $this->getFilterData('worker', 0);

        if($this->data['filter']['checkAll']!='on'){
           $worker = $this->flags->user['idno']; 
        }
        
        
        $this->data['requires'] = $this->not_reported_model->getRequires($condition, true, $worker);
        $this->data['link_refresh'] = base_url("management/not_reported/");
        $this->layout->view('management/not_reported/list',$this->data);
    }
    public function detail()
    {
        $class_info = $this->getFilterData(['class_no', 'year', 'term']);
        $this->data['link_save'] = "";
        $this->data['link_refresh'] = base_url("/management/not_reported/detail?year={$class_info['year']}&term={$class_info['term']}&class_no={$class_info['class_no']}");
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST'){
            $post = $this->input->post();
            $origin_not_reporteds = $this->not_reported_model->getListInfo($class_info, false, 5);
            $origin_idno = [];
            foreach ($origin_not_reporteds as $origin_not_reported) {
                $origin_idno[] = $origin_not_reported->id;
            }
            if (empty($post['not_reported'])) $post['not_reported'] = [];

            $not_reporteds = array_diff($post['not_reported'], $origin_idno); 
            $reporteds = array_diff($origin_idno, $post['not_reported']); 
            $update_status = true;

            foreach ($not_reporteds as $idno){
                $condition = array_merge($class_info, ['id' => $idno]);
                $update = $this->not_reported_model->update($condition, ['yn_sel' => 5, 'stop_reason' => '取消報名', 'upd_user' => $this->flags->user['username']]);
                $update_status = $update_status & $update;
            }
            
            foreach ($reporteds as $idno){
                $condition = array_merge($class_info, ['id' => $idno]);
                $update = $this->not_reported_model->update($condition, ['yn_sel' => 3, 'stop_reason' => '', 'upd_user' => $this->flags->user['username']]);
                $update_status = $update_status & $update;
            }
            if ($update_status){
                $this->setAlert(2, '已完成更新');
            }else{
                $this->setAlert(1, '部份更新失敗');
            }
            redirect($this->data['link_refresh']);
        }
        $this->data['class_info'] = $this->not_reported_model->getRequire($class_info);
        $this->data['link_refresh'] = base_url("/management/not_reported/detail?year={$class_info['year']}&term={$class_info['term']}&class_no={$class_info['class_no']}");
        $this->data['not_reporteds'] = $this->not_reported_model->getListInfo($class_info, false);
        $this->data['link_cancel'] = 'history_go_back';
        $this->layout->view('management/not_reported/detail',$this->data);
    }
}

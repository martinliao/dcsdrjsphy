<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Retreat extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model(['require_model', 'online_app_model']);

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
        //$condition = $this->getFilterData(['class_no', 'class_name']);
        //$condition['year'] = $this->getFilterData('year', date("Y")-1911);
        if($this->data['filter']['query_year']!=""){
            $condition['year']=$this->data['filter']['query_year'];
        }
        if($this->data['filter']['class_no']!=""){
            $condition['class_no']=$this->data['filter']['class_no'];
        }
        if($this->data['filter']['class_name']!=""){
            $condition['class_name']=$this->data['filter']['class_name'];
        }
        $key1 = in_array('1', $group_id);
        $key2 = in_array('9', $group_id);

        if(!$key1 && !$key2){
            $condition['idno']=$idno;
        }

        $this->data['requires'] = $this->require_model->getList($condition);
        
        $this->data['link_refresh'] = base_url("management/retreat/");
        $this->layout->view('management/retreat/list',$this->data);
    }
    public function detail()
    {
        $class_info = $this->getFilterData(['class_no', 'year', 'term']);
        $this->data['link_save'] = "";
        $this->data['link_refresh'] = base_url("/management/retreat/detail?year={$class_info['year']}&term={$class_info['term']}&class_no={$class_info['class_no']}");
        $this->data['require'] = $this->require_model->find($class_info); //retreat_standard
        $retreat_standard=empty($this->data['require']->retreat_standard) ? 0 : $this->data['require']->retreat_standard;
        $class_info['retreat_standard']=$retreat_standard;
        //var_dump($class_info);
        $this->data['retreats'] = $this->online_app_model->getRetreat($class_info);
        // dd($this->data['retreat']);
        if ($_SERVER['REQUEST_METHOD'] == 'POST'){
            $post = $this->input->post();
            if (!empty($post['retreats'])){
                unset($class_info['retreat_standard']);
                $update_status = true;
                $now = date("Y-m-d H:i:s");
                foreach ($this->data['retreats'] as $data) {
                    $update_data = $post['retreats'][$data->id];
                    if (isset($update_data)){
                        if (empty($update_data['yn_sel']) && $data->yn_sel == 4){
                            $update_data['yn_sel'] = 3;
                        }else if (!empty($update_data['yn_sel'])){
                            $update_data['yn_sel'] = 4;
                            $update_data['stop_date'] = $now;
                            $update_data['stop_reason'] = "退訓";                            
                        }
                        $condition = array_merge($class_info, ['id' => $data->id]);
                        $update_data['upd_date'] = $now;
                        $update_data['upd_user'] = $this->flags->user['name'];
                        $update_status = $update_status && $this->online_app_model->update($condition, $update_data);
                    }
                }
                if ($update_status){
                    $this->setAlert(2, '已完成更新');
                }else{
                    $this->setAlert(1, '部份更新失敗');
                }
                redirect($this->data['link_refresh']);                
            }
        }
        
        $this->data['link_cancel'] = 'history_go_back';
        $this->layout->view('management/retreat/detail',$this->data);
    }

}

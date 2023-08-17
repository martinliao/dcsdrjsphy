<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mixurge extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        if ($this->flags->is_login === FALSE) {
            redirect(base_url('welcome'));
        }

        $this->load->model('system/mixurge_model');
    }

    public function index()
    {
        $this->data['list'] = $this->mixurge_model->getList();

        $this->data['link_setup'] = base_url("system/mixurge/setup");
        $this->data['link_refresh'] = base_url("system/mixurge/");
        $this->layout->view('system/mixurge/list',$this->data);
    }

    public function setup(){
        $post = $this->input->post();
        
        if(!empty($post)){
            $check_upd = $this->mixurge_model->updateMixurge($post);

            if($check_upd){
                $this->setAlert(1, '設定成功');
            } else {
                $this->setAlert(2, '設定失敗');
            }

            redirect(base_url("system/mixurge"));
        }
    }
}

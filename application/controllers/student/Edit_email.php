<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Edit_email extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        if ($this->flags->is_login === FALSE) {
			redirect(base_url('welcome'));
		}

		$this->load->model('student/edit_email_model');
    }

    public function index()
    {
    	$id = $this->flags->user['id'];
        $this->data['email'] = $this->edit_email_model->getEmail($id);
        $this->data['link_edit'] = base_url("student/edit_email/edit/{$id}");

        $this->layout->view('student/edit_email/list', $this->data);
    }

    public function edit($id)
    {
    	if ($post = $this->input->post()) {
    		if(empty($post['email'])){
    			$post['email'] = null;
    		}

    		$rs = $this->edit_email_model->_update($id, $post);
			if ($rs) {
				$this->setAlert(2, '資料編輯成功');
			}
			redirect(base_url("student/edit_email"));
    	}

    	$this->data['email'] = $this->edit_email_model->getEmail($id);

        $this->data['link_save'] = base_url("student/edit_email/edit/{$id}");
		$this->data['link_cancel'] = base_url("student/edit_email/");
		$this->layout->view('student/edit_email/edit', $this->data);
    }

}

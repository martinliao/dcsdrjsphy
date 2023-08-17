<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Human_authority extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();

		if ($this->flags->is_login === FALSE) {
			redirect(base_url('welcome'));
		}
		$this->load->model('data/human_authority_model');

        if (!isset($this->data['filter']['page'])) {
            $this->data['filter']['page'] = '1';
        }
        if (!isset($this->data['filter']['sort'])) {
            $this->data['filter']['sort'] = '';
        }
        if (!isset($this->data['filter']['bureau_name'])) {
            $this->data['filter']['bureau_name'] = '';
        }
        if (!isset($this->data['filter']['name'])) {
            $this->data['filter']['name'] = '';
        }
	}

	public function index()
	{
		$this->data['page_name'] = 'list';

        $page = $this->data['filter']['page'];
        $rows = $this->data['filter']['rows'];

       	$conditions = array('account_role.group_id' => '6');

		$attrs = array(
            'conditions' => $conditions,
        );
        if ($this->data['filter']['bureau_name'] !== '' ) {
            $attrs['bureau_name'] = $this->data['filter']['bureau_name'];
        }
        if ($this->data['filter']['name'] !== '' ) {
            $attrs['name'] = $this->data['filter']['name'];
        }

        if(isset($this->data['filter']['post']) && $this->data['filter']['post'] == 'post'){
        	$this->data['filter']['total'] = $total = $this->human_authority_model->getListCount($attrs);
        }else{
        	$this->data['filter']['total'] = $total = '0';
        }
        

        $this->data['filter']['offset'] = $offset = ($page -1) * $rows;

        $attrs = array(
            'conditions' => $conditions,
            'rows' => $rows,
            'offset' => $offset,
        );

        if ($this->data['filter']['bureau_name'] !== '' ) {
            $attrs['bureau_name'] = $this->data['filter']['bureau_name'];
        }
        if ($this->data['filter']['name'] !== '' ) {
            $attrs['name'] = $this->data['filter']['name'];
        }
        if ($this->data['filter']['sort'] !== '' ) {
            $attrs['sort'] = $this->data['filter']['sort'];
        }
		if(isset($this->data['filter']['post']) && $this->data['filter']['post'] == 'post'){
        	$this->data['list'] = $this->human_authority_model->getList($attrs);
        }else{
        	$this->data['list'] = array();
        }
		foreach ($this->data['list'] as & $row) {
			$row['link_switch'] = base_url("data/human_authority/switch_ac/{$row['id']}/?{$_SERVER['QUERY_STRING']}");
		}
		$this->load->library('pagination');
        $config['base_url'] = base_url("data/human_authority?". $this->getQueryString(array(), array('page')));
        $config['total_rows'] = $total;
        $config['per_page'] = $rows;
        $this->pagination->initialize($config);

		$this->data['link_refresh'] = base_url("data/human_authority/");

		$this->layout->view('data/human_authority/list', $this->data);
	}

	public function switch_ac($id=NULL)
	{
		if($id!=NULL){
			$switch_ac = $this->user_model->get($id);
			$conditions = array(
				'group_id' => '6',
				'username' => $switch_ac['username'],
			);
			$data_count = $this->human_authority_model->getCount($conditions);
			$data_old = $this->session->userdata($this->site.$this->session_id);
		}

		if ( ($id==NULL || empty($switch_ac)) && empty($data_old['switch_ac']) ) {
			$this->setAlert(4, '操作錯誤');
		}
		if($switch_ac && ($data_count > 0)){
			$data = array(
	            'member_userid' => $data_old['member_userid'],
	            'switch_ac' => $switch_ac['id'],
	        );
	        if($data_old['member_userid'] == $id){
	        	$data = array(
		            'member_userid' => $data_old['member_userid'],
		        );
	        }

        	$this->session->set_userdata($this->site.$this->session_id, $data);
        	$this->setAlert(2, '切換成功');
		}else{
			$this->setAlert(4, '操作錯誤');
		}

		redirect(base_url("dcsdindex"));
	}

	// public function add()
	// {
	// 	$this->data['page_name'] = 'add';
	// 	$this->data['form'] = $this->human_authority_model->getFormDefault();
	// 	$this->data['choices']['gender'] = array('M'=>'男','F'=>'女');

	// 	// default form data
	// 	if (isset($this->data['filter']['user_group_id'])) {
	// 		$this->data['form']['user_group_id'] = $this->data['filter']['user_group_id'];
	// 	}

	// 	if ($post = $this->input->post()) {
	// 		if ($this->_isVerify('add') == TRUE) {
	// 			unset($post['bureau_name']);
	// 			unset($post['job_title_name']);
	// 			$post['user_group_id'] = '6';
	// 			$saved_id = $this->human_authority_model->_insert($post);
	// 			if ($saved_id) {
	// 				$this->setAlert(1, '資料新增成功');
	// 			}

	// 			redirect(base_url('data/human_authority'));
	// 		}
	// 	}

	// 	$this->data['link_save'] = base_url("data/human_authority/add/?{$_SERVER['QUERY_STRING']}");
	// 	$this->data['link_cancel'] = base_url("data/human_authority/?{$_SERVER['QUERY_STRING']}");
	// 	$this->layout->view('data/human_authority/add', $this->data);
	// }

	// public function edit($id=NULL)
	// {
	// 	$this->data['page_name'] = 'edit';
	// 	$this->data['form'] = $this->human_authority_model->getFormDefault($this->human_authority_model->get($id));

	// 	if(isset($this->data['form']['bureau_id']) && !empty($this->data['form']['bureau_id'])){
 //            $this->data['form']['bureau_name'] = $this->human_authority_model->getBureau($this->data['form']['bureau_id']);
 //        }

 //        if(isset($this->data['form']['job_title']) && !empty($this->data['form']['job_title'])){
 //            $this->data['form']['job_title_name'] = $this->human_authority_model->getJobTitle($this->data['form']['job_title']);
 //        }

	// 	$this->data['choices']['gender'] = array('M'=>'男','F'=>'女');
	// 	$this->data['choices']['departure'] = array('0'=>'否','1'=>'是');
	// 	$this->data['choices']['retirement'] = array('0'=>'否','F'=>'是');

	// 	if ($post = $this->input->post()) {
	// 		$old_data = $this->human_authority_model->get($id);
	// 		if ($this->_isVerify('edit', $old_data) == TRUE) {
	// 			unset($post['bureau_name']);
	// 			unset($post['job_title_name']);
	// 			$rs = $this->human_authority_model->_update($id, $post);
	// 			if ($rs) {
	// 				$this->setAlert(2, '資料編輯成功');
	// 			}
	// 			redirect(base_url("data/human_authority/?{$_SERVER['QUERY_STRING']}"));
	// 		}
	// 	}

	// 	$this->data['link_save'] = base_url("data/human_authority/edit/{$id}/?{$_SERVER['QUERY_STRING']}");
	// 	$this->data['link_cancel'] = base_url("data/human_authority/?{$_SERVER['QUERY_STRING']}");
	// 	$this->layout->view('data/human_authority/edit', $this->data);
	// }

	// public function delete()
	// {
	// 	if ($post = $this->input->post()) {
	// 		foreach ($post['rowid'] as $id) {
	// 			$rs = $this->human_authority_model->delete($id);
	// 		}
	// 		$this->setAlert(2, '資料刪除成功');
	// 	}

	// 	redirect(base_url("data/human_authority/?{$_SERVER['QUERY_STRING']}"));
	// }

	// private function _isVerify($action='add', $old_data=array())
	// {
	// 	$config = $this->human_authority_model->getVerifyConfig();
	// 	if ($action == 'edit') {
	// 		$config['idno']['rules'] = '';
	// 	}

	// 	$this->form_validation->set_rules($config);
	// 	$this->form_validation->set_error_delimiters('<p class="text-danger">', '</p>');
	// 	// $this->form_validation->set_message('required', '請勿空白');

	// 	return ($this->form_validation->run() == FALSE)? FALSE : TRUE;
	// }


	// public function ajax_toggle($field)
	// {
	// 	$result = array(
	// 		'status' => FALSE,
	// 		'message' => '',
	// 	);

	// 	if ($post = $this->input->post()) {
	// 		$rs = $this->human_authority_model->update($post['pk'], array($field=>$post['value']));
	// 		if ($rs) {
	// 			$result['status'] = TRUE;
	// 		}
	// 	}

	// 	echo json_encode($result);
	// }

}

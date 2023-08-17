<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Training_contractor_manage extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();

		if ($this->flags->is_login === FALSE) {
			redirect(base_url('welcome'));
		}
		$this->load->model('data/Training_contractor_model');
		$this->load->model('system/account_role_model');

		if (empty($this->data['filter']['user_group_id'])) {
			$this->data['filter']['user_group_id'] = 'all';
		}

        if (!isset($this->data['filter']['page'])) {
            $this->data['filter']['page'] = '1';
        }
        if (!isset($this->data['filter']['sort'])) {
            $this->data['filter']['sort'] = '';
        }
        if (!isset($this->data['filter']['q'])) {
            $this->data['filter']['q'] = '';
        }
	}

	public function index()
	{
		$this->data['page_name'] = 'list';

        $page = $this->data['filter']['page'];
        $rows = $this->data['filter']['rows'];

        $conditions = array();
        if($this->flags->user['bureau_id'] != '379680000A'){
        	// $conditions = array('BS_user.user_group_id' => '7', 'BS_user.bureau_id' => $this->flags->user['bureau_id']);
        	$conditions = array('BS_user.bureau_id' => $this->flags->user['bureau_id']);
        }

		$attrs = array(
            'conditions' => $conditions,
        );
        if ($this->data['filter']['q'] !== '' ) {
            $attrs['q'] = $this->data['filter']['q'];
        }
        $attrs['where_special'] = " BS_user.username in (select username from account_role where group_id = '7')";

        $this->data['filter']['total'] = $total = $this->Training_contractor_model->getListCount($attrs);
        $this->data['filter']['offset'] = $offset = ($page -1) * $rows;

        $attrs = array(
            'conditions' => $conditions,
            'rows' => $rows,
            'offset' => $offset,
        );
        $attrs['bureau_id'] = $this->flags->user['bureau_id'];

        if ($this->data['filter']['q'] !== '' ) {
            $attrs['q'] = $this->data['filter']['q'];
        }
        if ($this->data['filter']['sort'] !== '' ) {
            $attrs['sort'] = $this->data['filter']['sort'];
        }

        $attrs['where_special'] = " BS_user.username in (select username from account_role where group_id = '7')";

		$this->data['list'] = $this->Training_contractor_model->getList($attrs);
		// foreach ($this->data['list'] as & $row) {
		// 	$row['link_edit'] = base_url("data/training_contractor_manage/edit/{$row['id']}/?{$_SERVER['QUERY_STRING']}");
		// }
		$this->load->library('pagination');
        $config['base_url'] = base_url("data/training_contractor_manage?". $this->getQueryString(array(), array('page')));
        $config['total_rows'] = $total;
        $config['per_page'] = $rows;
        $this->pagination->initialize($config);

		// $this->data['link_add'] = base_url("data/training_contractor_manage/add/?{$_SERVER['QUERY_STRING']}");
		// $this->data['link_delete'] = base_url("data/training_contractor_manage/delete/?{$_SERVER['QUERY_STRING']}");
		$this->data['link_refresh'] = base_url("data/training_contractor_manage/");

		$this->layout->view('data/training_contractor/list', $this->data);
	}

	public function add()
	{
		$this->data['page_name'] = 'add';
		$this->data['form'] = $this->Training_contractor_model->getFormDefault();
		$this->data['choices']['gender'] = array('M'=>'男','F'=>'女');

		// default form data
		if (isset($this->data['filter']['user_group_id'])) {
			$this->data['form']['user_group_id'] = $this->data['filter']['user_group_id'];
		}

		if ($post = $this->input->post()) {
			if ($this->_isVerify('add') == TRUE) {
				unset($post['bureau_name']);
				unset($post['job_title_name']);
				// $post['user_group_id'] = '7';

				$saved_id = $this->Training_contractor_model->_insert($post);
				if ($saved_id) {
					$insert_date = new DateTime();
                    $insert_date = $insert_date->format('Y-m-d H:i:s');
	                $fields = array(
	                	'username' => $post['idno'],
        				'group_id' => '7',
        				'cre_user' => $this->flags->user['username'],
        				'upd_user' => $this->flags->user['username'],
        				'cre_date' => $insert_date,
        				'upd_date' => $insert_date,
	                );

	                $saved_id = $this->account_role_model->insert($fields);
					$this->setAlert(1, '資料新增成功');
				}

				redirect(base_url('data/training_contractor_manage'));
			}
		}

		$this->data['link_save'] = base_url("data/training_contractor_manage/add/?{$_SERVER['QUERY_STRING']}");
		$this->data['link_cancel'] = base_url("data/training_contractor_manage/?{$_SERVER['QUERY_STRING']}");
		$this->layout->view('data/training_contractor/add', $this->data);
	}

	public function edit($id=NULL)
	{
		$this->data['page_name'] = 'edit';
		$this->data['form'] = $this->Training_contractor_model->getFormDefault($this->Training_contractor_model->get($id));

		if(isset($this->data['form']['bureau_id']) && !empty($this->data['form']['bureau_id'])){
            $this->data['form']['bureau_name'] = $this->Training_contractor_model->getBureau($this->data['form']['bureau_id']);
        }

        if(isset($this->data['form']['job_title']) && !empty($this->data['form']['job_title'])){
            $this->data['form']['job_title_name'] = $this->Training_contractor_model->getJobTitle($this->data['form']['job_title']);
        }

		$this->data['choices']['gender'] = array('M'=>'男','F'=>'女');
		$this->data['choices']['departure'] = array('0'=>'否','1'=>'是');
		$this->data['choices']['retirement'] = array('0'=>'否','F'=>'是');

		if ($post = $this->input->post()) {
			$old_data = $this->Training_contractor_model->get($id);
			if ($this->_isVerify('edit', $old_data) == TRUE) {
				unset($post['bureau_name']);
				unset($post['job_title_name']);
				$rs = $this->Training_contractor_model->_update($id, $post);
				if ($rs) {
					$this->setAlert(2, '資料編輯成功');
				}
				redirect(base_url("data/training_contractor_manage/?{$_SERVER['QUERY_STRING']}"));
			}
		}

		$this->data['link_save'] = base_url("data/training_contractor_manage/edit/{$id}/?{$_SERVER['QUERY_STRING']}");
		$this->data['link_cancel'] = base_url("data/training_contractor_manage/?{$_SERVER['QUERY_STRING']}");
		$this->layout->view('data/training_contractor/edit', $this->data);
	}

	public function delete()
	{
		if ($post = $this->input->post()) {
			foreach ($post['rowid'] as $id) {
				$delete_user = $this->Training_contractor_model->get($id);
				$rs = $this->Training_contractor_model->delete($id);
				if($rs){
					$this->account_role_model->delete($delete_user['username']);
				}
			}
			$this->setAlert(2, '資料刪除成功');
		}

		redirect(base_url("data/training_contractor_manage/?{$_SERVER['QUERY_STRING']}"));
	}

	private function _isVerify($action='add', $old_data=array())
	{
		$config = $this->Training_contractor_model->getVerifyConfig();
		if ($action == 'edit') {
			$config['idno']['rules'] = '';
		}

		$this->form_validation->set_rules($config);
		$this->form_validation->set_error_delimiters('<p class="text-danger">', '</p>');
		// $this->form_validation->set_message('required', '請勿空白');

		return ($this->form_validation->run() == FALSE)? FALSE : TRUE;
	}


	public function ajax_toggle($field)
	{
		$result = array(
			'status' => FALSE,
			'message' => '',
		);

		if ($post = $this->input->post()) {
			$rs = $this->Training_contractor_model->update($post['pk'], array($field=>$post['value']));
			if ($rs) {
				$result['status'] = TRUE;
			}
		}

		echo json_encode($result);
	}

}

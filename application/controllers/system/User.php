<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class user extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();

		if ($this->flags->is_login === FALSE) {
			redirect(base_url('welcome'));
		}
		$this->load->model('system/user_model');
		$this->load->model('system/login_history_model');
		$this->load->model('system/account_role_model');
		$this->data['choices']['group'] = $this->user_group_model->getChoices();
		$this->data['choices']['member'] = $this->user_model->getChoices();

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

        $conditions = array();
        $page = $this->data['filter']['page'];
        $rows = $this->data['filter']['rows'];

		$attrs = array(
            'conditions' => $conditions,
        );
        if ($this->data['filter']['q'] !== '' ) {
            $attrs['q'] = $this->data['filter']['q'];
        }

        $this->data['filter']['total'] = $total = $this->user_model->getListCount($attrs);
        $this->data['filter']['offset'] = $offset = ($page -1) * $rows;

        $attrs = array(
            'conditions' => $conditions,
            'rows' => $rows,
            'offset' => $offset,
        );
        if ($this->data['filter']['q'] !== '' ) {
            $attrs['q'] = $this->data['filter']['q'];
        }
        if ($this->data['filter']['sort'] !== '' ) {
            $attrs['sort'] = $this->data['filter']['sort'];
        }

		$this->data['list'] = $this->user_model->getList($attrs);
		
		// 擁有 管理者 或是 教務組管理者權限不可切換
		
		$usernames = array_map(function($user){
			return $user['username'];
		}, $this->data['list']);

		$this->data['easNadmins'] = [];

		if (count($usernames) > 0){
			$this->data['easNadmins'] = $this->user_model->getAccountRoleEASNadmin($usernames);
			$tmp = [];
			foreach ($this->data['easNadmins'] as $key => $user) {
				$tmp[$user->username] = true;
			}
			$this->data['easNadmins'] = $tmp;				
		}
		
		foreach ($this->data['list'] as & $row) {
			if($this->flags->user['username'] != $row['username']){
				$row['link_switch'] = base_url("system/user/switch_ac/{$row['id']}/?{$_SERVER['QUERY_STRING']}");
			}
			$row['link_edit'] = base_url("system/user/edit/{$row['id']}/?{$_SERVER['QUERY_STRING']}");
		}
		$this->load->library('pagination');
        $config['base_url'] = base_url("system/user?". $this->getQueryString(array(), array('page')));
        $config['total_rows'] = $total;
        $config['per_page'] = $rows;
        $this->pagination->initialize($config);

		$this->data['link_add'] = base_url("system/user/add/?{$_SERVER['QUERY_STRING']}");
		$this->data['link_delete'] = base_url("system/user/delete/?{$_SERVER['QUERY_STRING']}");
		$this->data['link_refresh'] = base_url("system/user/");
		//  $this->data['link_toggle_enable'] = base_url('system/user/ajax_toggle/enable/');

		$this->layout->view('system/user/list', $this->data);
	}

	public function add()
	{
		$this->data['page_name'] = 'add';
		$this->data['form'] = $this->user_model->getFormDefault();

		// default form data
		if (isset($this->data['filter']['user_group_id'])) {
			$this->data['form']['user_group_id'] = $this->data['filter']['user_group_id'];
		}

		if ($post = $this->input->post()) {
			if ($this->_isVerify('add') == TRUE) {
				$saved_id = $this->user_model->_insert($post);
				if ($saved_id) {
					$this->setAlert(1, '資料新增成功');
				}

				redirect(base_url('system/user/'));
			}
		}

		$this->data['link_save'] = base_url("system/user/add/?{$_SERVER['QUERY_STRING']}");
		$this->data['link_cancel'] = base_url("system/user/?{$_SERVER['QUERY_STRING']}");
		$this->layout->view('system/user/add', $this->data);
	}

	public function edit($id=NULL)
	{
		$this->data['page_name'] = 'edit';
		$this->data['form'] = $this->user_model->getFormDefault($this->user_model->get($id));

		if ($post = $this->input->post()) {
			$old_data = $this->user_model->get($id);
			if ($this->_isVerify('edit', $old_data) == TRUE) {
				$rs = $this->user_model->_update($id, $post);
				if ($rs) {
					$this->setAlert(2, '資料編輯成功');
				}
				redirect(base_url("system/user/?{$_SERVER['QUERY_STRING']}"));
			}
		}

		$this->data['link_save'] = base_url("system/user/edit/{$id}/?{$_SERVER['QUERY_STRING']}");
		$this->data['link_cancel'] = base_url("system/user/?{$_SERVER['QUERY_STRING']}");
		$this->layout->view('system/user/edit', $this->data);
	}

	public function delete()
	{
		if ($post = $this->input->post()) {
			foreach ($post['rowid'] as $id) {
				$delete_user = $this->user_model->get($id);
				$conditions = array(
					'username' => $delete_user['username'],
					'group_id !=' => '5',
				);
				$role_data = $this->account_role_model->get($id);

				if(empty($role_data)){
					$rs = $this->user_model->delete($id);
					if($rs){
						$this->account_role_model->delete($delete_user['username']);
					}
				}
			}
			$this->setAlert(2, '資料刪除成功(如果帳號有學員以外的權限則無法刪除)');
		}

		redirect(base_url("system/user/?{$_SERVER['QUERY_STRING']}"));
	}


	private function _isVerify($action='add', $old_data=array())
	{
		$config = $this->user_model->getVerifyConfig();
		if ($action == 'edit') {
			$config['username']['rules'] = '';
			$config['password']['rules'] = 'min_length[4]|max_length[20]';
			$config['passconf']['rules'] = 'matches[password]';
			if ($old_data['email'] == $this->input->post('email')) {
				$config['email']['rules'] = 'trim|required|valid_email';
			}
			unset($config['email']);
			unset($config['idno']);
		}

		$this->form_validation->set_rules($config);
		$this->form_validation->set_error_delimiters('<p class="text-danger">', '</p>');
		// $this->form_validation->set_message('required', '請勿空白');

		return ($this->form_validation->run() == FALSE)? FALSE : TRUE;
	}

	public function switch_ac($id=NULL)
	{
		if($id!=NULL){
			$switch_ac = $this->user_model->get($id);
			$data_old = $this->session->userdata($this->site.$this->session_id);
		}

		if ( ($id==NULL || empty($switch_ac)) && empty($data_old['switch_ac']) ) {
			$this->setAlert(4, '操作錯誤');
		}

		$result = $this->user_model->getAccountRoleEASNadmin(array($switch_ac['username']));
		if (!empty($result)){
			$this->setAlert(4, '管理者及教務組管理者權限不可切換');
			redirect(base_url("dcsdindex"));
		}

		if($switch_ac){
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
		}

		redirect(base_url("dcsdindex"));
	}

	public function ajax_toggle($field)
	{
		$result = array(
			'status' => FALSE,
			'message' => '',
		);

		if ($post = $this->input->post()) {
			$rs = $this->user_model->update($post['pk'], array($field=>$post['value']));
			if ($rs) {
				$result['status'] = TRUE;
			}
		}

		echo json_encode($result);
	}

}

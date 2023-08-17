<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Bureau_manage extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();

		if ($this->flags->is_login === FALSE) {
			redirect(base_url('welcome'));
		}
	
		$this->load->model('data/bureau_manage_model');

        if (!isset($this->data['filter']['page'])) {
            $this->data['filter']['page'] = '1';
        }
        if (!isset($this->data['filter']['sort'])) {
            $this->data['filter']['sort'] = '';
        }
        if (!isset($this->data['filter']['name'])) {
            $this->data['filter']['name'] = '';
        }
        if (!isset($this->data['filter']['bureau_id'])) {
            $this->data['filter']['bureau_id'] = '';
        }
        if (!isset($this->data['filter']['del_flag'])) {
            $this->data['filter']['del_flag'] = '';
        }
        if (!isset($this->data['filter']['position'])) {
            $this->data['filter']['position'] = '';
        }
	}

	public function index()
	{
		$this->data['page_name'] = 'list';

        $page = $this->data['filter']['page'];
        $rows = $this->data['filter']['rows'];
        $this->data['choices']['position'] = array(''=>'請選擇','1'=>'私人','0'=>'公務');

        $conditions = array();

        if ($this->data['filter']['position'] !== '' ) {
            $conditions['position'] = $this->data['filter']['position'];
        }


		$attrs = array(
            'conditions' => $conditions,
        );
        if ($this->data['filter']['name'] !== '' ) {
            $attrs['name'] = $this->data['filter']['name'];
        }

        if ($this->data['filter']['bureau_id'] !== '' ) {
            $attrs['bureau_id'] = $this->data['filter']['bureau_id'];
        }

        if ($this->data['filter']['del_flag'] !== '' ) {
            $attrs['del_flag'] = $this->data['filter']['del_flag'];
        }

        $this->data['filter']['total'] = $total = $this->bureau_manage_model->getListCount($attrs);
        $this->data['filter']['offset'] = $offset = ($page -1) * $rows;

       	$attrs['rows'] = $rows;
        $attrs['offset'] = $offset;

        if ($this->data['filter']['sort'] !== '' ) {
            $attrs['sort'] = $this->data['filter']['sort'];
        }

		$this->data['list'] = $this->bureau_manage_model->getList($attrs);
		foreach ($this->data['list'] as & $row) {
			$row['link_edit'] = base_url("data/bureau_manage/edit/{$row['id']}/?{$_SERVER['QUERY_STRING']}");
			$row['link_transfer'] = base_url("data/bureau_manage/transfer/{$row['id']}/?{$_SERVER['QUERY_STRING']}");
		}
		$this->load->library('pagination');
        $config['base_url'] = base_url("data/bureau_manage?". $this->getQueryString(array(), array('page')));
        $config['total_rows'] = $total;
        $config['per_page'] = $rows;
        $this->pagination->initialize($config);

		$this->data['link_add'] = base_url("data/bureau_manage/add/?{$_SERVER['QUERY_STRING']}");
		$this->data['link_refresh'] = base_url("data/bureau_manage/");

		$this->layout->view('data/bureau_manage/list', $this->data);
	}

	public function add()
	{
		$this->data['page_name'] = 'add';
		$this->data['form'] = $this->bureau_manage_model->getFormDefault();
		$this->data['choices']['bureau_level'] = array(''=>'請選擇層級','1'=>'1','2'=>'2','3'=>'3','4'=>'4','5'=>'5');
		$this->data['choices']['position'] = array('1'=>'私人機關','0'=>'公務機關');

		if ($post = $this->input->post()) {
			if ($this->_isVerify('add') == TRUE) {
				if(empty($post['effective_date'])){
					unset($post['effective_date']);
				}

				if(empty($post['abolish_date'])){
					unset($post['abolish_date']);
				}

				$post['del_flag'] = 'N';
				$post['create_time'] = date('Y-m-d H:i:s');
				$post['create_user'] = $this->flags->user['id'];
				$post['modify_time'] = date('Y-m-d H:i:s');
				$post['modify_user'] = $this->flags->user['id'];
				$saved_id = $this->bureau_manage_model->_insert($post);
				if ($saved_id) {
					$this->setAlert(1, '資料新增成功');
				}

				redirect(base_url('data/bureau_manage'));
			}
		}

		$this->data['link_save'] = base_url("data/bureau_manage/add/?{$_SERVER['QUERY_STRING']}");
		$this->data['link_cancel'] = base_url("data/bureau_manage/?{$_SERVER['QUERY_STRING']}");
		$this->layout->view('data/bureau_manage/add', $this->data);
	}

	public function edit($id=NULL)
	{
		$this->data['page_name'] = 'edit';
		$this->data['form'] = $this->bureau_manage_model->getFormDefault($this->bureau_manage_model->get($id));
		if(!empty($this->data['form']['parent_id'])){
			$this->data['form']['parent_name'] = $this->bureau_manage_model->getBureauName($this->data['form']['parent_id']);
		}
		
		$this->data['choices']['bureau_level'] = array(''=>'請選擇層級','1'=>'1','2'=>'2','3'=>'3','4'=>'4','5'=>'5');
		$this->data['choices']['position'] = array('1'=>'私人機關','0'=>'公務機關');
		$this->data['choices']['del_flag'] = array('N'=>'NO','C'=>'YES');

		if ($post = $this->input->post()) {
			$old_data = $this->bureau_manage_model->get($id);
			if ($this->_isVerify('edit', $old_data) == TRUE) {
				if(empty($post['effective_date'])){
					unset($post['effective_date']);
				}

				if(empty($post['abolish_date'])){
					unset($post['abolish_date']);
				}
				
				
				$post['modify_time'] = date('Y-m-d H:i:s');
				$post['modify_user'] = $this->flags->user['id'];
				$rs = $this->bureau_manage_model->_update($id, $post);
				if ($rs) {
					$this->setAlert(2, '資料編輯成功');
				}
				redirect(base_url("data/bureau_manage/?{$_SERVER['QUERY_STRING']}"));
			}
		}

		$this->data['link_save'] = base_url("data/bureau_manage/edit/{$id}/?{$_SERVER['QUERY_STRING']}");
		$this->data['link_cancel'] = base_url("data/bureau_manage/?{$_SERVER['QUERY_STRING']}");
		$this->layout->view('data/bureau_manage/edit', $this->data);
	}

	public function transfer($id=NULL)
	{
		$this->data['page_name'] = 'transfer';
		$this->data['form'] = $this->bureau_manage_model->getNewFormDefault($this->bureau_manage_model->getFormDefault($this->bureau_manage_model->get($id)));
		if(!empty($this->data['form']['parent_id'])){
			$this->data['form']['parent_name'] = $this->bureau_manage_model->getBureauName($this->data['form']['parent_id']);
		}
		if(!empty($this->data['form']['new_parent_id'])){
			$this->data['form']['new_parent_name'] = $this->bureau_manage_model->getBureauName($this->data['form']['new_parent_id']);
		}
		
		$this->data['choices']['bureau_level'] = array(''=>'請選擇層級','1'=>'1','2'=>'2','3'=>'3','4'=>'4','5'=>'5');
		$this->data['choices']['new_bureau_level'] = array(''=>'請選擇層級','1'=>'1','2'=>'2','3'=>'3','4'=>'4','5'=>'5');
		$this->data['choices']['position'] = array('1'=>'私人機關','0'=>'公務機關');
		$this->data['choices']['del_flag'] = array('N'=>'NO','C'=>'YES');

		if ($post = $this->input->post()) {
			if ($this->_isVerify('transfer') == TRUE) {
				$old_bureau_id = $post['bureau_id'];
				unset($post['bureau_id']);
				$post['modify_time'] = date('Y-m-d H:i:s');
				$post['modify_user'] = $this->flags->user['id'];
				$rs = $this->bureau_manage_model->_update($id, $post);
				if ($rs) {
					$add_data = array(
									'bureau_id' => $post['new_bureau_id'],
									'name' => $post['new_name'],
									'bureau_level' => $post['new_bureau_level'],
									'parent_id' => $post['new_parent_id'],
									'position' => $post['position'],
									'effective_date' => $post['new_effective_date'],
									'create_time' => date('Y-m-d H:i:s'),
									'create_user' => $this->flags->user['id'],
									'modify_time' => date('Y-m-d H:i:s'),
									'modify_user' => $this->flags->user['id']
								);
					if($this->bureau_manage_model->_insert($add_data)){
						$today = date('Y-m-d');
						if(strtotime($today) >= strtotime($add_data['effective_date'])){
							$this->bureau_manage_model->updateUserBureau($old_bureau_id,$add_data['bureau_id'],$add_data['name']);
						}
						$this->setAlert(2, '局處轉換成功');
					}
				}
				redirect(base_url("data/bureau_manage/?{$_SERVER['QUERY_STRING']}"));
			}
		}

		$this->data['link_save'] = base_url("data/bureau_manage/transfer/{$id}/?{$_SERVER['QUERY_STRING']}");
		$this->data['link_cancel'] = base_url("data/bureau_manage/?{$_SERVER['QUERY_STRING']}");
		$this->layout->view('data/bureau_manage/transfer', $this->data);
	}

	private function _isVerify($action='add', $old_data=array())
	{
		if($action == 'transfer'){
			$config = $this->bureau_manage_model->getNewVerifyConfig();
		} else {
			$config = $this->bureau_manage_model->getVerifyConfig();
		}
		
		if ($action == 'edit') {
			$config['bureau_id']['rules'] = '';
		}

		$this->form_validation->set_rules($config);
		$this->form_validation->set_error_delimiters('<p class="text-danger">', '</p>');
		// $this->form_validation->set_message('required', '請勿空白');

		return ($this->form_validation->run() == FALSE)? FALSE : TRUE;
	}
}

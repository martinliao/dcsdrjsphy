<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Human_manger extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();

		if ($this->flags->is_login === FALSE) {
			redirect(base_url('welcome'));
		}
		$this->load->model('data/human_manger_model');

		if (empty($this->data['filter']['user_group_id'])) {
			$this->data['filter']['user_group_id'] = 'all';
		}

        if (!isset($this->data['filter']['page'])) {
            $this->data['filter']['page'] = '1';
        }
        if (!isset($this->data['filter']['sort'])) {
            $this->data['filter']['sort'] = '';
        }
        if (!isset($this->data['filter']['username'])) {
            $this->data['filter']['username'] = '';
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
        if ($this->data['filter']['username'] !== '' ) {
            $attrs['username'] = $this->data['filter']['username'];
        }
        if ($this->data['filter']['name'] !== '' ) {
            $attrs['name'] = $this->data['filter']['name'];
        }

        $this->data['filter']['total'] = $total = $this->human_manger_model->getListCount($attrs);
        $this->data['filter']['offset'] = $offset = ($page -1) * $rows;

        $attrs = array(
            'conditions' => $conditions,
            'rows' => $rows,
            'offset' => $offset,
        );
        // $attrs['bureau_id'] = $this->flags->user['bureau_id'];

        if ($this->data['filter']['username'] !== '' ) {
            $attrs['username'] = $this->data['filter']['username'];
        }
        if ($this->data['filter']['name'] !== '' ) {
            $attrs['name'] = $this->data['filter']['name'];
        }
        if ($this->data['filter']['sort'] !== '' ) {
            $attrs['sort'] = $this->data['filter']['sort'];
        }

		$this->data['list'] = $this->human_manger_model->getList($attrs);
		foreach ($this->data['list'] as & $row) {
			$row['link_edit'] = base_url("data/human_manger/edit/{$row['id']}/?{$_SERVER['QUERY_STRING']}");
		}
		$this->load->library('pagination');
        $config['base_url'] = base_url("data/human_manger?". $this->getQueryString(array(), array('page')));
        $config['total_rows'] = $total;
        $config['per_page'] = $rows;
        $this->pagination->initialize($config);

		$this->data['link_refresh'] = base_url("data/human_manger/");

		$this->layout->view('data/human_manger/list', $this->data);
	}

	public function edit($id=NULL)
	{
		$this->data['page_name'] = 'edit';
		$this->data['form'] = $this->user_model->getFormDefault($this->user_model->get($id));

		if(isset($this->data['form']['bureau_id']) && !empty($this->data['form']['bureau_id'])){
            $this->data['form']['bureau_name'] = $this->human_manger_model->getBureau($this->data['form']['bureau_id']);
        }

		if ($post = $this->input->post()) {
			$old_data = $this->user_model->get($id);
			if ($this->_isVerify('edit', $old_data) == TRUE) {
				unset($post['bureau_name']);
				unset($post['job_title_name']);
				$rs = $this->user_model->_update($id, $post);
				if ($rs) {
					$this->setAlert(2, '資料編輯成功');
				}
				redirect(base_url("data/human_manger/?{$_SERVER['QUERY_STRING']}"));
			}
		}

		$this->data['link_save'] = base_url("data/human_manger/edit/{$id}/?{$_SERVER['QUERY_STRING']}");
		$this->data['link_cancel'] = base_url("data/human_manger/?{$_SERVER['QUERY_STRING']}");
		$this->layout->view('data/human_manger/edit', $this->data);
	}

	private function _isVerify($action='add', $old_data=array())
	{
		$config = $this->user_model->getVerifyConfig();
		if ($action == 'edit') {
			$config['idno']['rules'] = '';
			$config['name']['rules'] = '';
			$config['username']['rules'] = '';
			$config['email']['rules'] = '';
			$config['password']['rules'] = 'min_length[4]|max_length[20]';
			$config['passconf']['rules'] = 'matches[password]';
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
			$rs = $this->human_manger_model->update($post['pk'], array($field=>$post['value']));
			if ($rs) {
				$result['status'] = TRUE;
			}
		}

		echo json_encode($result);
	}

}

<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Reservation_time extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();

		if ($this->flags->is_login === FALSE) {
			redirect(base_url('welcome'));
		}

		$this->load->model('data/reservation_time_model');
		$this->load->model('data/code_history_model');

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

		$attrs = array(
            'conditions' => $conditions,
        );
        if ($this->data['filter']['q'] !== '' ) {
            $attrs['q'] = $this->data['filter']['q'];
        }

        $this->data['filter']['total'] = $total = $this->reservation_time_model->getListCount($attrs);
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

		$this->data['list'] = $this->reservation_time_model->getList($attrs);
		foreach ($this->data['list'] as & $row) {
			$row['link_edit'] = base_url("data/reservation_time/edit/{$row['id']}/?{$_SERVER['QUERY_STRING']}");
		}
		$this->load->library('pagination');
        $config['base_url'] = base_url("data/reservation_time?". $this->getQueryString(array(), array('page')));
        $config['total_rows'] = $total;
        $config['per_page'] = $rows;
        $this->pagination->initialize($config);

		$this->data['link_add'] = base_url("data/reservation_time/add/?{$_SERVER['QUERY_STRING']}");
		$this->data['link_delete'] = base_url("data/reservation_time/delete/?{$_SERVER['QUERY_STRING']}");
		$this->data['link_refresh'] = base_url("data/reservation_time/");

		$this->layout->view('data/reservation_time/list', $this->data);
	}

	public function add()
	{
		$this->data['page_name'] = 'add';
		$this->data['form'] = $this->reservation_time_model->getFormDefault();

		if ($post = $this->input->post()) {
			if ($this->_isVerify('add') == TRUE) {
				$post['create_time'] = date('Y-m-d H:i:s');
				$post['create_user'] = $this->flags->user['id'];
				$post['modify_time'] = date('Y-m-d H:i:s');
				$post['modify_user'] = $this->flags->user['id'];
				$saved_id = $this->reservation_time_model->_insert($post);
				if ($saved_id) {
					$fields = array(
						'type_id' => '31',
						'item_id' => $post['item_id'],
						'description' => $post['name'],
						'memo' => $post['remark'],
						'add_val1' => $post['start_time'],
						'add_val2' => $post['end_time'],
						'cre_user' => $post['create_user'],
						'cre_date' => $post['create_time'],
						'task' => '新增',
						'upd_user' => $post['modify_user'],
						'log_date' => date('Y-m-d H:i:s'),
					);
					$this->code_history_model->insert($fields);
					$this->setAlert(1, '資料新增成功');
				}

				redirect(base_url('data/reservation_time'));
			}
		}

		$this->data['link_save'] = base_url("data/reservation_time/add/?{$_SERVER['QUERY_STRING']}");
		$this->data['link_cancel'] = base_url("data/reservation_time/?{$_SERVER['QUERY_STRING']}");
		$this->layout->view('data/reservation_time/add', $this->data);
	}

	public function edit($id=NULL)
	{
		$this->data['page_name'] = 'edit';
		$this->data['form'] = $this->reservation_time_model->getFormDefault($this->reservation_time_model->get($id));
		$this->data['form']['start_time'] = substr($this->data['form']['start_time'], 0,5);
		$this->data['form']['end_time'] = substr($this->data['form']['end_time'], 0,5);

		if ($post = $this->input->post()) {
			$old_data = $this->reservation_time_model->get($id);
			if ($this->_isVerify('edit', $old_data) == TRUE) {
				$post['modify_time'] = date('Y-m-d H:i:s');
				$post['modify_user'] = $this->flags->user['id'];
				$rs = $this->reservation_time_model->_update($id, $post);
				if ($rs) {
					$fields = array(
						'type_id' => '31',
						'item_id' => $post['item_id'],
						'description' => $post['name'],
						'memo' => $post['remark'],
						'add_val1' => $post['start_time'],
						'add_val2' => $post['end_time'],
						'cre_user' => $post['modify_user'],
						'cre_date' => $post['modify_time'],
						'task' => '修改',
						'upd_user' => $post['modify_user'],
						'log_date' => date('Y-m-d H:i:s'),
					);
					$this->code_history_model->insert($fields);
					$this->setAlert(2, '資料編輯成功');
				}
				redirect(base_url("data/reservation_time/?{$_SERVER['QUERY_STRING']}"));
			}
		}

		$this->data['link_save'] = base_url("data/reservation_time/edit/{$id}/?{$_SERVER['QUERY_STRING']}");
		$this->data['link_cancel'] = base_url("data/reservation_time/?{$_SERVER['QUERY_STRING']}");
		$this->layout->view('data/reservation_time/edit', $this->data);
	}

	public function delete()
	{
		if ($post = $this->input->post()) {
			foreach ($post['rowid'] as $id) {
				$old_data = $this->reservation_time_model->get($id);
				// jd($old_data,1);
				$fields = array(
					'type_id' => '31',
					'item_id' => $old_data['item_id'],
					'description' => $old_data['name'],
					'memo' => $old_data['remark'],
					'add_val1' => $old_data['start_time'],
					'add_val2' => $old_data['end_time'],
					'cre_user' => $this->flags->user['id'],
					'cre_date' => date('Y-m-d H:i:s'),
					'task' => '刪除',
					'upd_user' => $this->flags->user['id'],
					'log_date' => date('Y-m-d H:i:s'),
				);
				$this->code_history_model->insert($fields);
				$rs = $this->reservation_time_model->delete($id);
			}
			$this->setAlert(2, '資料刪除成功');
		}

		redirect(base_url("data/reservation_time/?{$_SERVER['QUERY_STRING']}"));
	}

	private function _isVerify($action='add', $old_data=array())
	{
		$config = $this->reservation_time_model->getVerifyConfig();
		$config['start_time']['rules'] = 'trim|required|callback_validate_time_start';
		$config['end_time']['rules'] = 'trim|required|callback_validate_time_end';
		if ($action == 'edit') {
			if($old_data['item_id'] == $this->input->post('item_id')){
				$config['item_id']['rules'] = '';
			}
		}

		$this->form_validation->set_rules($config);
		$this->form_validation->set_error_delimiters('<p class="text-danger">', '</p>');
		// $this->form_validation->set_message('required', '請勿空白');

		return ($this->form_validation->run() == FALSE)? FALSE : TRUE;
	}

	public function validate_time_start($str)
	{
		//Assume $str SHOULD be entered as HH:MM
		if($str){

			if(preg_match("/:/", $str)){

				list($hh, $mm) = explode(':', $str);

				if (!is_numeric($hh) || !is_numeric($mm))
				{
				    $this->form_validation->set_message('validate_time_start', '起始時間 時間格式錯誤');
				    return FALSE;
				}
				else if ((int) $hh > 24 || (int) $mm > 59)
				{
				    $this->form_validation->set_message('validate_time_start', '起始時間 時間格式錯誤');
				    return FALSE;
				}
				else if (mktime((int) $hh, (int) $mm) === FALSE)
				{
				    $this->form_validation->set_message('validate_time_start', '起始時間 時間格式錯誤');
				    return FALSE;
				}

				return TRUE;
			}else{
				$this->form_validation->set_message('validate_time_start', '起始時間 時間格式錯誤');
				return FALSE;
			}

		}

	}

	public function validate_time_end($str)
	{
		//Assume $str SHOULD be entered as HH:MM
		if($str){

			if($str){
				list($hh, $mm) = explode(':', $str);

				if (!is_numeric($hh) || !is_numeric($mm))
				{
				    $this->form_validation->set_message('validate_time_end', '結束時間 時間格式錯誤');
				    return FALSE;
				}
				else if ((int) $hh > 24 || (int) $mm > 59)
				{
				    $this->form_validation->set_message('validate_time_end', '結束時間 時間格式錯誤');
				    return FALSE;
				}
				else if (mktime((int) $hh, (int) $mm) === FALSE)
				{
				    $this->form_validation->set_message('validate_time_end', '結束時間 時間格式錯誤');
				    return FALSE;
				}

				return TRUE;
			}else{
				$this->form_validation->set_message('validate_time_end', '結束時間 時間格式錯誤');
				return FALSE;
			}

		}

	}

}

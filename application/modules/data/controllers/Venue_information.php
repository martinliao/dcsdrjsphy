<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Venue_information extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();

		if ($this->flags->is_login === FALSE) {
			redirect(base_url('welcome'));
		}

		$this->load->model('data/venue_information_model');
		$this->load->model('data/place_category_model');
		$this->load->model('data/place_unit_model');
		$this->load->model('data/reservation_time_model');
		$this->load->model('data/venue_time_model');

		$this->data['choices']['room_type'] = $this->place_category_model->getChoices();
		$this->data['choices']['room_bel'] = $this->place_unit_model->getChoices();
		$this->data['choices']['time_list'] = $this->reservation_time_model->getList();
		$this->data['choices']['room_countby'] = array(
				'1' => '人',
				'2' => '桌',
				'3' => '場地',
		);

        if (!isset($this->data['filter']['page'])) {
            $this->data['filter']['page'] = '1';
        }
        if (!isset($this->data['filter']['sort'])) {
            $this->data['filter']['sort'] = '';
        }
        if (!isset($this->data['filter']['q'])) {
            $this->data['filter']['q'] = '';
        }
        if (!isset($this->data['filter']['room_type'])) {
            $this->data['filter']['room_type'] = 'all';
        }
	}

	public function index()
	{
		$this->data['page_name'] = 'list';

        $page = $this->data['filter']['page'];
        $rows = $this->data['filter']['rows'];

        $conditions = array();

        $conditions["IFNULL(del_flag,'') !="] = 'Y';

		$attrs = array(
            'conditions' => $conditions,
        );
        if ($this->data['filter']['room_type'] !== 'all' ) {
            $attrs['conditions']['room_type'] = $this->data['filter']['room_type'];
        }

        if ($this->data['filter']['q'] !== '' ) {
            $attrs['q'] = $this->data['filter']['q'];
        }

        $this->data['filter']['total'] = $total = $this->venue_information_model->getListCount($attrs);
        $this->data['filter']['offset'] = $offset = ($page -1) * $rows;
        
        $attrs = array(
            'conditions' => $conditions,
            'rows' => $rows,
            'offset' => $offset,
        );
        if ($this->data['filter']['q'] !== '' ) {
            $attrs['q'] = $this->data['filter']['q'];
        }
        if ($this->data['filter']['room_type'] !== 'all' ) {
            $attrs['conditions']['room_type'] = $this->data['filter']['room_type'];
        }
        if ($this->data['filter']['sort'] !== '' ) {
            $attrs['sort'] = $this->data['filter']['sort'];
        }

		$this->data['list'] = $this->venue_information_model->getList($attrs);
		foreach ($this->data['list'] as & $row) {
			$row['link_edit'] = base_url("data/venue_information/edit/{$row['id']}/?{$_SERVER['QUERY_STRING']}");
		}
		$this->load->library('pagination');
        $config['base_url'] = base_url("data/venue_information?". $this->getQueryString(array(), array('page')));
        $config['total_rows'] = $total;
        $config['per_page'] = $rows;
        $this->pagination->initialize($config);

		$this->data['link_add'] = base_url("data/venue_information/add/?{$_SERVER['QUERY_STRING']}");
		$this->data['link_delete'] = base_url("data/venue_information/delete/?{$_SERVER['QUERY_STRING']}");
		$this->data['link_refresh'] = base_url("data/venue_information/");

		$this->layout->view('data/venue_information/list', $this->data);
	}

	public function add()
	{
		$this->data['page_name'] = 'add';
		$this->data['form'] = $this->venue_information_model->getFormDefault();

		if ($post = $this->input->post()) {
			$item = $this->input->post('item');
			if ($this->_isVerify('add') == TRUE) {
				unset($post['item']);
				unset($post['times']);
				$post['del_flag'] = NULL;
				$saved_id = $this->venue_information_model->_insert($post);
				if ($saved_id) {
					if ($item) {
						foreach($item as & $row){
							$row['room_id'] = $post['room_id'];
							$this->venue_time_model->insert($row);
						}
					}
					$this->setAlert(1, '資料新增成功');
				}

				redirect(base_url('data/venue_information'));
			}
		}
		if($post && isset($post['item'][0]) ){
        	$this->data['form']['item'] = $post['item'];
        }

		$this->data['link_save'] = base_url("data/venue_information/add/?{$_SERVER['QUERY_STRING']}");
		$this->data['link_cancel'] = base_url("data/venue_information/?{$_SERVER['QUERY_STRING']}");
		$this->layout->view('data/venue_information/add', $this->data);
	}

	public function edit($id=NULL)
	{
		$this->data['page_name'] = 'edit';
		$this->data['form'] = $this->venue_information_model->getFormDefault($this->venue_information_model->get($id));
		$this->data['form']['item'] = $this->venue_time_model->getByRoomID($this->data['form']['room_id']);

		if ($post = $this->input->post()) {
			$item = $this->input->post('item');
			$old_data = $this->venue_information_model->get($id);
			if ($this->_isVerify('edit', $old_data) == TRUE) {
				unset($post['item']);
				unset($post['times']);
				$rs = $this->venue_information_model->_update($id, $post);
				if ($rs) {
					$this->venue_time_model->delete(array('room_id'=>$this->data['form']['room_id']));
					if ($item) {
						foreach($item as & $row){
							$row['room_id'] = $post['room_id'];
							$this->venue_time_model->insert($row);
						}
					}
					$this->setAlert(2, '資料編輯成功');
				}
				redirect(base_url("data/venue_information/?{$_SERVER['QUERY_STRING']}"));
			}
		}

		if($post && isset($post['item'][0]) ){
        	$this->data['form']['item'] = $post['item'];
        }


		$this->data['link_save'] = base_url("data/venue_information/edit/{$id}/?{$_SERVER['QUERY_STRING']}");
		$this->data['link_cancel'] = base_url("data/venue_information/?{$_SERVER['QUERY_STRING']}");
		$this->layout->view('data/venue_information/edit', $this->data);
	}

	public function delete()
	{
		if ($post = $this->input->post()) {
			foreach ($post['rowid'] as $id) {
				$fields = array(
					'del_flag' => 'Y',
				);
				$rs = $this->venue_information_model->_update($id, $fields);
			}
			$this->setAlert(2, '資料刪除成功');
		}

		redirect(base_url("data/venue_information/?{$_SERVER['QUERY_STRING']}"));
	}

	private function _isVerify($action='add', $old_data=array())
	{
		$config = $this->venue_information_model->getVerifyConfig();
		

		if ($action == 'edit') {
			if($old_data['room_id'] == $this->input->post('room_id')){
				$config['room_id']['rules'] = '';
			}
		}

		$this->form_validation->set_rules($config);
		$this->form_validation->set_error_delimiters('<p class="text-danger">', '</p>');
		// $this->form_validation->set_message('required', '請勿空白');

		return ($this->form_validation->run() == FALSE)? FALSE : TRUE;
	}

	public function valid_exist($str)
	{	

		$result=$this->venue_information_model->getDelFlagIsY($str);
		if(!$result){
			$this->form_validation->set_message('valid_exist','該場地代碼 已被使用並已刪除，請重新輸入');
            return FALSE;
		}
		return true;
		
	}
}

<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Fee extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        if ($this->flags->is_login === FALSE) {
            redirect(base_url('welcome'));
        }
        $this->load->model('data/hour_fee_model');
        $this->load->model('data/hour_fee_his_model');
        $this->load->model('data/teacher_model');
        $this->load->model('data/hire_category_model');
        $this->load->model('data/hourlyfee_category_model');

        $this->data['choices']['hourlyfee_category'] = $this->hourlyfee_category_model->getChoices();
        $this->data['choices']['hire_category'] = $this->hire_category_model->getChoices();
        $this->data['choices']['teacher_type'] = $this->teacher_model->teacher_type;

        $this->data['choices']['teacher_type'] = array(''=>'請選擇身分別') + $this->data['choices']['teacher_type'];
        $this->data['choices']['hire_category'] = array(''=>'請選擇助教聘請類別') + $this->data['choices']['hire_category'];
        $this->data['choices']['hourlyfee_category'] = array(''=>'請選擇鐘點費類別') + $this->data['choices']['hourlyfee_category'];


        if (!isset($this->data['filter']['page'])) {
            $this->data['filter']['page'] = '1';
        }
        if (!isset($this->data['filter']['sort'])) {
            $this->data['filter']['sort'] = '';
        }
        if (!isset($this->data['filter']['q'])) {
            $this->data['filter']['q'] = '';
        }
        if (!isset($this->data['filter']['insert_teachingassistant'])) {
            $this->data['filter']['insert_teachingassistant'] = '';
        }
        if (!isset($this->data['filter']['insert_Lecturer'])) {
            $this->data['filter']['insert_Lecturer'] = '';
        }
        if (!isset($this->data['filter']['hourlyfee_category'])) {
            $this->data['filter']['hourlyfee_category'] = '';
        }
        if (!isset($this->data['filter']['teacher_type'])) {
            $this->data['filter']['teacher_type'] = '';
        }

    }
    public function index()
    {
        $this->data['page_name'] = 'list';

        $page = $this->data['filter']['page'];
        $rows = $this->data['filter']['rows'];

        $conditions = array();

        if ($this->data['filter']['insert_teachingassistant'] != '') {
            $conditions['assistant_type_id'] = $this->data['filter']['insert_teachingassistant'];
        }
        if ($this->data['filter']['insert_Lecturer'] != '') {
           $conditions['teacher_type_id'] = $this->data['filter']['insert_Lecturer'];
        }
        if ($this->data['filter']['hourlyfee_category'] != '') {
           $conditions['class_type_id'] = $this->data['filter']['hourlyfee_category'];
        }
        if ($this->data['filter']['teacher_type'] != '') {
           $conditions['type'] = $this->data['filter']['teacher_type'];
        }

        $attrs = array(
            'conditions' => $conditions,
        );
        if ($this->data['filter']['q'] !== '' ) {
            $attrs['q'] = $this->data['filter']['q'];
        }

        $this->data['filter']['total'] = $total = $this->hour_fee_model->getListCount($attrs);
        $this->data['filter']['offset'] = $offset = ($page -1) * $rows;

        $attrs = array(
            'conditions' => $conditions,
            'rows' => $rows,
            'offset' => $offset,
        );

        $this->data['list'] = $this->hour_fee_model->getList($attrs);
        foreach ($this->data['list'] as & $row) {
            $row['link_edit'] = base_url("data/fee/edit/{$row['id']}/?{$_SERVER['QUERY_STRING']}");
        }

        $this->load->library('pagination');
        $config['base_url'] = base_url("data/fee?". $this->getQueryString(array(), array('page')));
        $config['total_rows'] = $total;
        $config['per_page'] = $rows;
        $this->pagination->initialize($config);

        $this->data['link_add'] = base_url("data/fee/add/?{$_SERVER['QUERY_STRING']}");
        $this->data['link_delete'] = base_url("data/fee/delete?{$_SERVER['QUERY_STRING']}");
        $this->data['link_refresh'] = base_url("data/fee/");

        $this->layout->view('data/fee/list',$this->data);
    }
    public function add()
    {
        $this->data['page_name'] = 'add';
		$this->data['form'] = $this->hour_fee_model->getFormDefault();

		if ($post = $this->input->post()) {
			if ($this->_isVerify('add') == TRUE) {
				$conditions = array(
					'class_type_id' => $post['class_type_id'],
					'type' => $post['type'],
					'assistant_type_id' => $post['assistant_type_id'],
					'teacher_type_id' => $post['teacher_type_id'],
				);
				$get_count = $this->hour_fee_model->getCount($conditions);
				if($get_count == '0'){
					$post['upd_user'] = $this->flags->user['username'];
					$post['upd_date'] = date('Y-m-d H:i:s');

					$saved_id = $this->hour_fee_model->insert($post);
					if ($saved_id) {
						$fields = array(
							'class_type_id' => $post['class_type_id'],
							'type' => $post['type'],
							'assistant_type_id' => $post['assistant_type_id'],
							'teacher_type_id' => $post['teacher_type_id'],
							'hour_fee' => $post['hour_fee'],
							'traffic_fee' => $post['traffic_fee'],
							'cre_user' => $post['upd_user'],
							'cre_date' => $post['upd_date'],
							'task' => '新增',
							'log_date' => date('Y-m-d H:i:s'),
						);
						$this->hour_fee_his_model->insert($fields);
						$this->setAlert(1, '資料新增成功');
					}

					redirect(base_url('data/fee'));
				}else{
					$this->setAlert(3, '條件已存在!');
					redirect(base_url('data/fee'));
				}
			}
		}

		$this->data['link_save'] = base_url("data/fee/add/?{$_SERVER['QUERY_STRING']}");
		$this->data['link_cancel'] = base_url("data/fee/?{$_SERVER['QUERY_STRING']}");
		$this->layout->view('data/fee/add', $this->data);
    }

    public function edit($id)
    {
        $this->data['page_name'] = 'edit';
		$this->data['form'] = $this->hour_fee_model->getFormDefault($this->hour_fee_model->get($id));

		if ($post = $this->input->post()) {
			if ($this->_isVerify('edit') == TRUE) {
				$post['upd_user'] = $this->flags->user['username'];
				$post['upd_date'] = date('Y-m-d H:i:s');
				$saved_id = $this->hour_fee_model->update($id, $post);
				if ($saved_id) {
					$fields = array(
						'class_type_id' => $this->data['form']['class_type_id'],
						'type' => $this->data['form']['type'],
						'assistant_type_id' => $this->data['form']['assistant_type_id'],
						'teacher_type_id' => $this->data['form']['teacher_type_id'],
						'hour_fee' => $post['hour_fee'],
						'traffic_fee' => $post['traffic_fee'],
						'cre_user' => $post['upd_user'],
						'cre_date' => $post['upd_date'],
						'task' => '修改',
						'log_date' => date('Y-m-d H:i:s'),
					);
					$this->hour_fee_his_model->insert($fields);
					$this->setAlert(1, '資料修改成功');
				}

				redirect(base_url('data/fee'));
			}
		}

		$this->data['link_save'] = base_url("data/fee/edit/{$id}?{$_SERVER['QUERY_STRING']}");
		$this->data['link_cancel'] = base_url("data/fee/?{$_SERVER['QUERY_STRING']}");
		$this->layout->view('data/fee/edit', $this->data);
    }

    public function log()
    {
        $page = $this->data['filter']['page'];
        $rows = $this->data['filter']['rows'];

        $conditions = array();

        if ($this->data['filter']['insert_teachingassistant'] != '') {
            $conditions['assistant_type_id'] = $this->data['filter']['insert_teachingassistant'];
        }
        if ($this->data['filter']['insert_Lecturer'] != '') {
           $conditions['teacher_type_id'] = $this->data['filter']['insert_Lecturer'];
        }
        if ($this->data['filter']['hourlyfee_category'] != '') {
           $conditions['class_type_id'] = $this->data['filter']['hourlyfee_category'];
        }
        if ($this->data['filter']['teacher_type'] != '') {
           $conditions['type'] = $this->data['filter']['teacher_type'];
        }

        $attrs = array(
            'conditions' => $conditions,
        );
        if ($this->data['filter']['q'] !== '' ) {
            $attrs['q'] = $this->data['filter']['q'];
        }

        $this->data['filter']['total'] = $total = $this->hour_fee_his_model->getListCount($attrs);
        $this->data['filter']['offset'] = $offset = ($page -1) * $rows;

        $attrs = array(
            'conditions' => $conditions,
            'rows' => $rows,
            'offset' => $offset,
        );

        $this->data['list'] = $this->hour_fee_his_model->getList($attrs);
        // jd($this->data['list']);
        $this->load->library('pagination');
        $config['base_url'] = base_url("data/fee/log?". $this->getQueryString(array(), array('page')));
        $config['total_rows'] = $total;
        $config['per_page'] = $rows;
        $this->pagination->initialize($config);

        $this->layout->view('data/fee/log',$this->data);
    }

    private function _isVerify($action='add', $old_data=array())
	{
		$config = $this->hour_fee_model->getVerifyConfig();
		if ($action == 'edit') {
			$config['class_type_id']['rules'] = 'trim';
			$config['type']['rules'] = 'trim';
			$config['teacher_type_id']['rules'] = 'trim';
		}

		$this->form_validation->set_rules($config);
		$this->form_validation->set_error_delimiters('<p class="text-danger">', '</p>');
		// $this->form_validation->set_message('required', '請勿空白');

		return ($this->form_validation->run() == FALSE)? FALSE : TRUE;
	}

	public function delete()
    {

        if ($post = $this->input->post()) {
            $del_num = 0;
            foreach ($post['rowid'] as $id) {
            	$old_data = $this->hour_fee_model->get($id);
            	$fields = array(
					'class_type_id' => $old_data['class_type_id'],
					'type' => $old_data['type'],
					'assistant_type_id' => $old_data['assistant_type_id'],
					'teacher_type_id' => $old_data['teacher_type_id'],
					'hour_fee' => $old_data['hour_fee'],
					'traffic_fee' => $old_data['traffic_fee'],
					'cre_user' => $this->flags->user['username'],
					'cre_date' => date('Y-m-d H:i:s'),
					'task' => '刪除',
					'log_date' => date('Y-m-d H:i:s'),
				);

				$this->hour_fee_his_model->insert($fields);
                $rs = $this->hour_fee_model->delete($id);
                if ($rs['status']) {
                    $del_num ++;
                }
            }

            $error_num = count($post['rowid']) - $del_num;
            if ($error_num == 0) {
                $this->setAlert(2, "共刪除 {$del_num} 筆資料");
            } else {
                $this->setAlert(2, "共刪除 {$del_num} 筆資料, {$error_num} 筆未刪除");
            }

        }

        redirect(base_url("data/fee/?".$this->getQueryString()));
    }
}

<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Modify_classname extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();

		if ($this->flags->is_login === FALSE) {
			redirect(base_url('welcome'));
		}
		$this->load->model('create_class/modify_classname_model');
	
        if (!isset($this->data['filter']['page'])) {
            $this->data['filter']['page'] = '1';
        }
        if (!isset($this->data['filter']['sort'])) {
            $this->data['filter']['sort'] = '';
        }
        if (!isset($this->data['filter']['query_year'])) {
            $this->data['filter']['query_year'] = date('Y')-1911;
        }
        if (!isset($this->data['filter']['query_class_no'])) {
            $this->data['filter']['query_class_no'] = '';
        }
        if (!isset($this->data['filter']['query_class_name'])) {
            $this->data['filter']['query_class_name'] = '';
        }
	}

	public function index()
	{
		$this->data['page_name'] = 'list';

        $conditions = array();

        if ($this->data['filter']['query_year'] !== '' ) {
            $conditions['year'] = $this->data['filter']['query_year'];
        }

        if ($this->data['filter']['query_class_no'] !== '' ) {
            $conditions['class_no'] = $this->data['filter']['query_class_no'];
        }

        $page = $this->data['filter']['page'];
        $rows = $this->data['filter']['rows'];

		$attrs = array(
            'conditions' => $conditions,
        );

        if ($this->data['filter']['query_class_name'] !== '') {
            $attrs['query_class_name'] = $this->data['filter']['query_class_name'];
        }

        $this->data['filter']['total'] = $total = $this->modify_classname_model->getListCount($attrs);
        $this->data['filter']['offset'] = $offset = ($page -1) * $rows;

        $attrs = array(
            'conditions' => $conditions,
            'rows' => $rows,
            'offset' => $offset,
        );
    
        if ($this->data['filter']['sort'] !== '' ) {
            $attrs['sort'] = $this->data['filter']['sort'];
        }

        if ($this->data['filter']['query_class_name'] !== '') {
            $attrs['query_class_name'] = $this->data['filter']['query_class_name'];
        }

		$this->data['list'] = $this->modify_classname_model->getList($attrs);
        foreach ($this->data['list'] as & $row) {
            $row['link_edit'] = base_url("create_class/modify_classname/edit/{$row['seq_no']}/?{$_SERVER['QUERY_STRING']}");
        }
		
		$this->load->library('pagination');
        $config['base_url'] = base_url("create_class/modify_classname?". $this->getQueryString(array(), array('page')));
        $config['total_rows'] = $total;
        $config['per_page'] = $rows;
        $this->pagination->initialize($config);

		$this->data['link_refresh'] = base_url("create_class/modify_classname/");

		$this->layout->view('create_class/modify_classname/list', $this->data);
	}

    public function edit($id=NULL)
    {
        if ($post = $this->input->post()) {
			$old_data = $this->modify_classname_model->get($id);
			if ($this->_isVerify('edit', $old_data) == TRUE) {
				$rs = $this->modify_classname_model->updateClassName($post);
				if ($rs) {
					$this->setAlert(2, '資料編輯成功');
				}
				redirect(base_url("create_class/modify_classname/?{$_SERVER['QUERY_STRING']}"));
			}
		}

        $this->data['page_name'] = 'edit';
        $this->data['form'] = $this->modify_classname_model->getFormDefault($this->modify_classname_model->get($id));

        $this->data['link_save'] = base_url("create_class/modify_classname/edit/{$id}/?{$_SERVER['QUERY_STRING']}");
        $this->data['link_cancel'] = base_url("create_class/modify_classname/?{$_SERVER['QUERY_STRING']}");
        $this->layout->view('create_class/modify_classname/edit', $this->data);
    }

    private function _isVerify($action='add', $old_data=array())
	{
		$config = $this->modify_classname_model->getVerifyConfig();

		$this->form_validation->set_rules($config);
		$this->form_validation->set_error_delimiters('<p class="text-danger">', '</p>');
		// $this->form_validation->set_message('required', '請勿空白');

		return ($this->form_validation->run() == FALSE)? FALSE : TRUE;
	}
}
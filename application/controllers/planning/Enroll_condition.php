<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Enroll_condition extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        
        if ($this->flags->is_login === FALSE) {
            redirect(base_url('welcome'));
        }
    
        $this->load->model('planning/enroll_condition_model');

        if (!isset($this->data['filter']['page'])) {
            $this->data['filter']['page'] = '1';
        }
        if (!isset($this->data['filter']['sort'])) {
            $this->data['filter']['sort'] = '';
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

        $this->data['filter']['total'] = $total = $this->enroll_condition_model->getListCount($attrs);
        $this->data['filter']['offset'] = $offset = ($page -1) * $rows;

        $attrs = array(
            'conditions' => $conditions,
            'rows' => $rows,
            'offset' => $offset,
        );

        if ($this->data['filter']['sort'] !== '' ) {
            $attrs['sort'] = $this->data['filter']['sort'];
        }

        $this->data['list'] = $this->enroll_condition_model->getList($attrs);
       
        $this->load->library('pagination');
        $config['base_url'] = base_url("planning/enroll_condition?". $this->getQueryString(array(), array('page')));
        $config['total_rows'] = $total;
        $config['per_page'] = $rows;
        $this->pagination->initialize($config);

        $this->data['link_add'] = base_url("planning/enroll_condition/add/?{$_SERVER['QUERY_STRING']}");
        $this->data['link_delete'] = base_url("planning/enroll_condition/delete/?{$_SERVER['QUERY_STRING']}");
        $this->data['link_refresh'] = base_url("planning/enroll_condition/");

        $this->layout->view('planning/enroll_condition/list', $this->data);
    }

    public function add()
    {
        $this->data['page_name'] = 'add';
        $this->data['form'] = $this->enroll_condition_model->getFormDefault();

        if ($post = $this->input->post()) {
            if ($this->_isVerify('add') == TRUE) {
                $post['create_time'] = date('Y-m-d H:i:s');
                $post['create_user'] = $this->flags->user['id'];
                $post['modify_time'] = date('Y-m-d H:i:s');
                $post['modify_user'] = $this->flags->user['id'];
                $saved_id = $this->enroll_condition_model->_insert($post);
                if ($saved_id) {
                    $this->setAlert(1, '資料新增成功');
                }

                redirect(base_url('planning/enroll_condition'));
            }
        }

        $this->data['link_save'] = base_url("planning/enroll_condition/add/?{$_SERVER['QUERY_STRING']}");
        $this->data['link_cancel'] = base_url("planning/enroll_condition/?{$_SERVER['QUERY_STRING']}");
        $this->layout->view('planning/enroll_condition/add', $this->data);
    }

    public function delete()
    {
        if ($post = $this->input->post()) {
            foreach ($post['rowid'] as $id) {
                $rs = $this->enroll_condition_model->delete($id);
            }
            $this->setAlert(2, '資料刪除成功');
        }

        redirect(base_url("planning/enroll_condition/?{$_SERVER['QUERY_STRING']}"));
    }

    private function _isVerify($action='add', $old_data=array())
    {
        $config = $this->enroll_condition_model->getVerifyConfig();

        $this->form_validation->set_rules($config);
        $this->form_validation->set_error_delimiters('<p class="text-danger">', '</p>');
        // $this->form_validation->set_message('required', '請勿空白');

        return ($this->form_validation->run() == FALSE)? FALSE : TRUE;
    }
}

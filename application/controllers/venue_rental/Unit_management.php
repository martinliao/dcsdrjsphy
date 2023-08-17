<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Unit_management extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        if ($this->flags->is_login === FALSE) {
            redirect(base_url('welcome'));
        }
        $this->load->model('venue_rental/unit_management_model');

        if (!isset($this->data['filter']['page'])) {
            $this->data['filter']['page'] = '1';
        }
        if (!isset($this->data['filter']['sort'])) {
            $this->data['filter']['sort'] = '';
        }
        if (!isset($this->data['filter']['app_name'])) {
            $this->data['filter']['app_name'] = '';
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

        if ($this->data['filter']['app_name'] !== '' ) {
            $attrs['app_name'] = $this->data['filter']['app_name'];
        }
        $attrs['where_special'] = " (del_flag is NULL OR del_flag='') ";
        $this->data['filter']['total'] = $total = $this->unit_management_model->getListCount($attrs);
        $this->data['filter']['offset'] = $offset = ($page -1) * $rows;

        $attrs['rows'] = $rows;
        $attrs['offset'] = $offset;
        $this->data['list'] = $this->unit_management_model->getList($attrs);
        
        foreach ($this->data['list'] as & $row) {
            $row['link_edit'] = base_url("venue_rental/unit_management/edit/{$row['app_id']}/?{$_SERVER['QUERY_STRING']}");
        }
        $this->load->library('pagination');
        $config['base_url'] = base_url("venue_rental/unit_management?". $this->getQueryString(array(), array('page')));
        $config['total_rows'] = $total;
        $config['per_page'] = $rows;
        $this->pagination->initialize($config);

        $this->data['link_add'] = base_url("venue_rental/unit_management/add/?{$_SERVER['QUERY_STRING']}");
        $this->data['link_delete'] = base_url("venue_rental/unit_management/delete/?{$_SERVER['QUERY_STRING']}");
        $this->data['link_refresh'] = base_url("venue_rental/unit_management/");

        $this->layout->view('venue_rental/unit_management/list', $this->data);
    }

    public function add()
    {
        $this->data['page_name'] = 'add';
        $this->data['u_id'] = $this->flags->user['id'];
        $this->data['form'] = $this->unit_management_model->getFormDefault();

        if ($post = $this->input->post()) {
            if ($this->_isVerify('add') == TRUE) {
                if($post['is_public'] != 'Y'){
                    $post['is_public'] = 'N';
                }
                $post['cre_date'] = date('Y-m-d H:i:s');
                $post['cre_user'] = $this->flags->user['username'];
                $post['upd_date'] = date('Y-m-d H:i:s');
                $post['upd_user'] = $this->flags->user['username'];

                $saved_id = $this->unit_management_model->_insert($post);
                if ($saved_id) {
                    $this->setAlert(1, '資料新增成功');
                }

                redirect(base_url('venue_rental/unit_management'));
            }
        }

        $this->data['link_save'] = base_url("venue_rental/unit_management/add/?{$_SERVER['QUERY_STRING']}");
        $this->data['link_cancel'] = base_url("venue_rental/unit_management/?{$_SERVER['QUERY_STRING']}");
        $this->layout->view('venue_rental/unit_management/add', $this->data);
    }

    public function edit($id=NULL)
    {
        $this->data['page_name'] = 'edit';
        $this->data['u_id'] = $this->flags->user['id'];
        $this->data['form'] = $this->unit_management_model->getFormDefault($this->unit_management_model->get($id));

        if ($post = $this->input->post()) {
            $old_data = $this->unit_management_model->get($id);
            if ($this->_isVerify('edit', $old_data) == TRUE) {
                if($post['is_public'] != 'Y'){
                    $post['is_public'] = 'N';
                }

                $post['upd_date'] = date('Y-m-d H:i:s');
                $post['upd_user'] = $this->flags->user['username'];
                $rs = $this->unit_management_model->_update($id, $post);
                if ($rs) {

                    $this->setAlert(2, '資料編輯成功');
                }
                redirect(base_url("venue_rental/unit_management/?{$_SERVER['QUERY_STRING']}"));
            }
        }

        $this->data['link_save'] = base_url("venue_rental/unit_management/edit/{$id}/?{$_SERVER['QUERY_STRING']}");
        $this->data['link_cancel'] = base_url("venue_rental/unit_management/?{$_SERVER['QUERY_STRING']}");
        $this->layout->view('venue_rental/unit_management/edit', $this->data);
    }

    public function delete()
    {
        if ($post = $this->input->post()) {
            foreach ($post['rowid'] as $id) {
                $fields = array(
                    'del_flag' => 'Y',
                );
                $rs = $this->unit_management_model->_update($id, $fields);
            }
            $this->setAlert(2, '資料刪除成功');
        }

        redirect(base_url("venue_rental/unit_management/?{$_SERVER['QUERY_STRING']}"));
    }

    private function _isVerify($action='add', $old_data=array())
    {
        $config = $this->unit_management_model->getVerifyConfig();

        $this->form_validation->set_rules($config);
        $this->form_validation->set_error_delimiters('<p class="text-danger">', '</p>');
        // $this->form_validation->set_message('required', '請勿空白');

        return ($this->form_validation->run() == FALSE)? FALSE : TRUE;
    }

}

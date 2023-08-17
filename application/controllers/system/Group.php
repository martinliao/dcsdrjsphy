<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Group extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        if ($this->flags->is_login === FALSE) {
            redirect(base_url('welcome'));
        }

        $this->load->model('system/user_group_auth_model');

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

        $this->data['filter']['total'] = $total = $this->user_group_model->getListCount($attrs);
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
        $this->data['list'] = $this->user_group_model->getList($attrs);
        //var_dump($this->data['list']);
        foreach ($this->data['list'] as & $row) {
            //$row['link_view'] = base_url("system/group/view/{$row['id']}/?{$_SERVER['QUERY_STRING']}");
            $row['link_edit'] = base_url("system/group/edit/{$row['id']}/?{$_SERVER['QUERY_STRING']}");
        }
        $this->load->library('pagination');
        $config['base_url'] = base_url("system/group?". $this->getQueryString(array(), array('page')));
        $config['total_rows'] = $total;
        $config['per_page'] = $rows;
        $this->pagination->initialize($config);

        $this->data['link_add'] = base_url("system/group/add/?{$_SERVER['QUERY_STRING']}");
        $this->data['link_delete'] = base_url("system/group/delete/?{$_SERVER['QUERY_STRING']}");
        $this->data['link_refresh'] = base_url("system/group/");

        $this->layout->view('system/user_group/list', $this->data);
    }

    public function add()
    {
        $this->data['page_name'] = 'add';

        if ($post = $this->input->post()) {
            if ($this->_isVerify('add') == TRUE) {
                $auth = $post['auth'];
                unset($post['auth']);

                $saved_id = $this->user_group_model->insert($post);
                if ($saved_id) {
                    // write group auth
                    foreach ($auth as $menu_id) {
                        $fields = array(
                            'user_group_id' => $saved_id,
                            'menu_id' => $menu_id,
                        );
                        $this->user_group_auth_model->insert($fields);
                    }

                    $this->setAlert(1, '資料新增成功');
                }

                redirect(base_url('system/group/'));
            }
        }

        $this->data['form'] = $this->user_group_model->getFormDefault();

        $this->data['choices']['menu'] = $this->menu_model->getChoices('admin');

        $this->data['link_save'] = base_url("system/group/add/");
        $this->data['link_cancel'] = base_url('system/group/');
        $this->data['link_refresh'] = base_url("system/group/add");

        $this->layout->view('system/user_group/add', $this->data);
    }

    public function edit($id=NULL)
    {
        $this->data['page_name'] = 'edit';
        if ($post = $this->input->post()) {
            $old_data = $this->user_group_model->get($id);
            if ($this->_isVerify('edit', $old_data) == TRUE) {
                $auth = $post['auth'];
                unset($post['auth']);

                $rs = $this->user_group_model->update($id, $post);
                if ($rs) {
                    // write group auth
                    $this->user_group_auth_model->delete(array('user_group_id'=>$id));
                    foreach ($auth as $menu_id) {
                        $fields = array(
                            'user_group_id' => $id,
                            'menu_id' => $menu_id,
                        );
                        $this->user_group_auth_model->insert($fields);
                    }

                    $this->setAlert(2, '資料編輯成功');
                }
                redirect(base_url("system/group/?{$_SERVER['QUERY_STRING']}"));
            }
        }

        $this->data['form'] = $this->user_group_model->getFormDefault($this->user_group_model->get($id));
        $this->data['form']['auth'] = $this->user_group_auth_model->getByGroupID($id);

        $this->data['choices']['menu'] = $this->menu_model->getChoices('admin');

        $this->data['link_save'] = base_url("system/group/edit/{$id}/?{$_SERVER['QUERY_STRING']}");
        $this->data['link_cancel'] = base_url("system/group/?{$_SERVER['QUERY_STRING']}");
        $this->data['link_refresh'] = base_url("system/menu/edit/{$id}");

        $this->layout->view('system/user_group/edit', $this->data);
    }

    public function delete()
    {
        if ($post = $this->input->post()) {
            foreach ($post['rowid'] as $id) {
                $rs = $this->user_group_model->delete($id);
            }
            $this->setAlert(2, '資料刪除成功');
        }

        redirect(base_url("system/group/?{$_SERVER['QUERY_STRING']}"));
    }

    private function _isVerify($action='add', $old_data=array())
    {
        $config = $this->user_group_model->getVerifyConfig();
        if ($action == 'edit') {
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
            $rs = $this->user_group_model->update($post['pk'], array($field=>$post['value']));
            if ($rs) {
                $result['status'] = TRUE;
            }
        }

        echo json_encode($result);
    }


}

/* End of file group.php */
/* Location: ./application/controllers/group.php */

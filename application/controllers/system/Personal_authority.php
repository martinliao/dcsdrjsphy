<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Personal_authority extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        if ($this->flags->is_login === FALSE) {
            redirect(base_url('welcome'));
        }

        $this->load->model('system/personal_authority_model');

        $this->data['choices']['user_id'] = $this->user_model->getUserChoices();

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

        $this->data['filter']['total'] = $total = $this->personal_authority_model->getListCount($attrs);
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
        $this->data['list'] = $this->personal_authority_model->getList($attrs);
        foreach ($this->data['list'] as & $row) {
            //$row['link_view'] = base_url("system/personal_authority/view/{$row['id']}/?{$_SERVER['QUERY_STRING']}");
            $row['link_edit'] = base_url("system/personal_authority/edit/{$row['id']}/?{$_SERVER['QUERY_STRING']}");
        }
        $this->load->library('pagination');
        $config['base_url'] = base_url("system/personal_authority?". $this->getQueryString(array(), array('page')));
        $config['total_rows'] = $total;
        $config['per_page'] = $rows;
        $this->pagination->initialize($config);

        $this->data['link_add'] = base_url("system/personal_authority/add/?{$_SERVER['QUERY_STRING']}");
        $this->data['link_delete'] = base_url("system/personal_authority/delete/?{$_SERVER['QUERY_STRING']}");
        $this->data['link_refresh'] = base_url("system/personal_authority/");

        $this->layout->view('system/personal_authority/list', $this->data);
    }

    public function add()
    {
        $this->data['page_name'] = 'add';

        $user_ids = $this->personal_authority_model->getUserId();
        foreach($user_ids as $row){
            unset($this->data['choices']['user_id'][$row]);
        }

        if ($post = $this->input->post()) {
            if ($this->_isVerify('add') == TRUE) {
                $auth = $post['auth'];
                unset($post['auth']);
                    // write group auth
                    foreach ($auth as $menu_id) {
                        $fields = array(
                            'user_id' => $post['user_id'],
                            'menu_id' => $menu_id,
                        );
                        //jd($fields,1);
                        $this->personal_authority_model->insert($fields);
                    }

                    $this->setAlert(1, '資料新增成功');

                redirect(base_url('system/personal_authority/'));
            }
        }

        $this->data['form'] = $this->personal_authority_model->getFormDefault();

        $this->data['choices']['menu'] = $this->menu_model->getChoices('admin');

        $this->data['link_save'] = base_url("system/personal_authority/add/");
        $this->data['link_cancel'] = base_url('system/personal_authority/');
        $this->data['link_refresh'] = base_url("system/personal_authority/add");

        $this->layout->view('system/personal_authority/add', $this->data);
    }

    public function edit($id=NULL)
    {
        $this->data['page_name'] = 'edit';
        if ($post = $this->input->post()) {
            $old_data = $this->personal_authority_model->_get($id);

            if ($this->_isVerify('edit', $old_data) == TRUE && $old_data['user_id'] == $id) {

                $auth = $post['auth'];
                unset($post['auth']);
                    // write group auth
                    $this->personal_authority_model->delete(array('user_id'=>$id));
                    foreach ($auth as $menu_id) {
                        $fields = array(
                            'user_id' => $id,
                            'menu_id' => $menu_id,
                        );
                        $this->personal_authority_model->insert($fields);
                    }


                    $this->setAlert(2, '資料編輯成功');

                redirect(base_url("system/personal_authority/?{$_SERVER['QUERY_STRING']}"));
            }
        }

        $this->data['form'] = $this->personal_authority_model->getFormDefault($this->personal_authority_model->_get($id));
        $this->data['form']['auth'] = $this->personal_authority_model->getByUserID($id);

        $this->data['choices']['menu'] = $this->menu_model->getChoices('admin');

        $this->data['link_save'] = base_url("system/personal_authority/edit/{$id}/?{$_SERVER['QUERY_STRING']}");
        $this->data['link_cancel'] = base_url("system/personal_authority/?{$_SERVER['QUERY_STRING']}");
        $this->data['link_refresh'] = base_url("system/menu/edit/{$id}");

        $this->layout->view('system/personal_authority/edit', $this->data);
    }

    public function delete()
    {
        if ($post = $this->input->post()) {
            foreach ($post['rowid'] as $id) {
                $rs = $this->personal_authority_model->delete(array('user_id' => $id));
            }
            $this->setAlert(2, '資料刪除成功');
        }

        redirect(base_url("system/personal_authority/?{$_SERVER['QUERY_STRING']}"));
    }

    private function _isVerify($action='add', $old_data=array())
    {
        $config = $this->personal_authority_model->getVerifyConfig();
        if ($action == 'edit') {
            $config['user_id']['rules'] = 'trim';
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
            $rs = $this->personal_authority_model->update($post['pk'], array($field=>$post['value']));
            if ($rs) {
                $result['status'] = TRUE;
            }
        }

        echo json_encode($result);
    }

}

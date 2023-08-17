<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class menu extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        if ($this->flags->is_login === FALSE) {
            redirect(base_url('welcome'));
        }

        $this->load->library('form_validation');

        $this->data['choices']['port'] = $this->menu_model->choices_port;


        if (empty($this->data['filter']['port'])) {
            $keys = array_keys($this->data['choices']['port']);
            $this->data['filter']['port'] = array_shift($keys);
        }
    }

    public function index()
    {
        $this->data['page_name'] = 'list';

        $conditions = array();
        $this->data['port'] = $this->data['filter']['port'];
        if ($this->data['port'] != '') {
            $conditions['port'] = $this->data['port'];
        }

        $this->data['list'] = $this->menu_model->getList($conditions);
        foreach ($this->data['list'] as & $row) {
            $row['link_edit'] = base_url("system/menu/edit/{$row['id']}?{$_SERVER['QUERY_STRING']}");
        }

        $this->data['link_add'] = base_url("system/menu/add/?{$_SERVER['QUERY_STRING']}");
        $this->data['link_delete'] = base_url("system/menu/delete/?{$_SERVER['QUERY_STRING']}");
        $this->data['link_refresh'] = base_url("system/menu/");

        $this->layout->view('system/menu/list', $this->data);
    }

    public function add()
    {
        $this->data['page_name'] = 'add';
        $this->data['form'] = $this->menu_model->getFormDefault();
        $this->data['form']['port'] = $this->data['filter']['port'];
        $this->data['form']['actions_to_string'] = '';

        if ($post = $this->input->post()) {
            if ($this->_isVerify('add') == TRUE) {
                $saved_id = $this->menu_model->_insert($post);
                if ($saved_id) {
                    $this->setAlert(1, '資料新增成功');
                }

                redirect(base_url("system/menu/?{$_SERVER['QUERY_STRING']}"));
            }
        }

        $this->data['choices']['parent'] = $this->menu_model->getParentChoices(array('port'=>$this->data['form']['port']));
        $this->setJson('choices_parent', $this->data['choices']['parent']);

        $this->data['link_save'] = base_url("system/menu/add/?{$_SERVER['QUERY_STRING']}");
        $this->data['link_cancel'] = base_url("system/menu/?{$_SERVER['QUERY_STRING']}");
        $this->data['link_refresh'] = base_url("system/menu/add??{$_SERVER['QUERY_STRING']}");
        $this->layout->view('system/menu/add', $this->data);
    }

    public function edit($id=NULL)
    {
        $this->data['page_name'] = 'edit';

        if (isset($this->data['filter']['port'])) {
            $this->data['form']['port'] = $this->data['filter']['port'];
        }

        if ($post = $this->input->post()) {
            $old_data = $this->menu_model->get($id);
            if ($this->_isVerify('edit', $old_data) == TRUE) {
                $rs = $this->menu_model->_update($id, $post);
                if ($rs) {
                    $this->setAlert(2, '資料編輯成功');
                }
                redirect(base_url("system/menu/?{$_SERVER['QUERY_STRING']}"));
            }
        }

        $this->data['form'] = $this->menu_model->getFormDefault($this->menu_model->_get($id));


        $this->data['choices']['parent'] = $this->menu_model->getParentChoices(array('port'=>$this->data['form']['port']));
        $this->setJson('choices_parent', $this->data['choices']['parent']);

        $this->data['link_save'] = base_url("system/menu/edit/{$id}/?{$_SERVER['QUERY_STRING']}");
        $this->data['link_cancel'] = base_url("system/menu/?{$_SERVER['QUERY_STRING']}");
        $this->data['link_refresh'] = base_url("system/menu/edit/{$id}?{$_SERVER['QUERY_STRING']}");
        $this->layout->view('system/menu/edit', $this->data);
    }

    public function delete()
    {
        if ($post = $this->input->post()) {
            $del_num = 0;
            foreach ($post['rowid'] as $id) {
                $rs = $this->menu_model->_delete($id);
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

        redirect(base_url("system/menu/?{$_SERVER['QUERY_STRING']}"));
    }

    private function _isVerify($action='add', $old_data=array())
    {
        $config = $this->menu_model->getVerifyConfig();
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
            $rs = $this->menu_model->update($post['pk'], array($field=>$post['value']));
            if ($rs) {
                $result['status'] = TRUE;
            }
        }

        echo json_encode($result);
    }

}


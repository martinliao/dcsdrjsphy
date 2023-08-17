<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Setlevel extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        if ($this->flags->is_login === FALSE) {
            redirect(base_url('welcome'));
        }
        $this->load->model('system/setlevel_model');

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

        $this->data['filter']['total'] = $total = $this->setlevel_model->getListCount($attrs);
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
        $this->data['list'] = $this->setlevel_model->getList($attrs);
        foreach ($this->data['list'] as & $row) {
            $row['link_edit'] = base_url("system/setlevel/edit/{$row['id']}/?". $this->getQueryString());
        }
        $this->load->library('pagination');
        $config['base_url'] = base_url("system/setlevel_model?". $this->getQueryString(array(), array('page')));
        $config['total_rows'] = $total;
        $config['per_page'] = $rows;
        $this->pagination->initialize($config);

        $this->data['link_add'] = base_url("system/setlevel/add/?". $this->getQueryString());
        $this->data['link_delete'] = base_url("system/setlevel/delete/?". $this->getQueryString());
        $this->data['link_refresh'] = base_url("system/setlevel/");

        $this->layout->view('system/setlevel/list', $this->data);
    }

    public function add()
    {
        $this->data['page_name'] = 'add';
        $this->data['form'] =  $this->setlevel_model->getFormDefault();

        if ($post = $this->input->post()) {
            if ($this->_isVerify('add') == TRUE) {
                $saved_id = $this->setlevel_model->insert($post);
                if ($saved_id) {
                    $this->setAlert(1, '資料新增成功');
                }

                redirect(base_url('system/setlevel/?'. $this->getQueryString()));
            }
        }



        $this->data['link_save'] = base_url("system/setlevel/add/?". $this->getQueryString());
        $this->data['link_cancel'] = base_url('system/setlevel/?'. $this->getQueryString());
        $this->layout->view('system/setlevel/add', $this->data);
    }

    public function edit($id=NULL)
    {
        $this->data['page_name'] = 'edit';
        $this->data['form'] = $this->setlevel_model->getFormDefault($this->setlevel_model->get($id));

        if ($post = $this->input->post()) {
            $old_data = $this->setlevel_model->get($id);
            if ($this->_isVerify('edit', $old_data) == TRUE) {
                $rs = $this->setlevel_model->update($id, $post);
                if ($rs) {
                    $this->setAlert(2, '資料編輯成功');
                }
                redirect(base_url("system/setlevel/?". $this->getQueryString()));
            }
        }

        $this->data['link_save'] = base_url("system/setlevel/edit/{$id}/?". $this->getQueryString());
        $this->data['link_cancel'] = base_url("system/setlevel/?". $this->getQueryString());
        $this->layout->view('system/setlevel/edit', $this->data);
    }

    public function delete()
    {
        if ($post = $this->input->post()) {
            $del_num = 0;
            foreach ($post['rowid'] as $id) {
                if ($this->order_model->getCount(array('order_status'=>$id)) == 0) {
                    $rs = $this->setlevel_model->delete($id);
                    if ($rs['status']) {
                        $del_num ++;
                    }
                }
            }

            $error_num = count($post['rowid']) - $del_num;
            if ($error_num == 0) {
                $this->setAlert(2, "共刪除 {$del_num} 筆資料");
            } else {
                $this->setAlert(2, "共刪除 {$del_num} 筆資料, {$error_num} 筆未刪除<br>未刪除原因可能是因為該訂單狀態巳被訂單使用。");
            }
        }

        redirect(base_url("system/setlevel/?". $this->getQueryString()));
    }

    private function _isVerify($action='add', $old_data=array())
    {
        $config = $this->setlevel_model->getVerifyConfig();
        if ($action == 'edit') {
        }

        $this->form_validation->set_rules($config);
        $this->form_validation->set_error_delimiters('<p class="text-danger">', '</p>');

        return ($this->form_validation->run() == FALSE)? FALSE : TRUE;
    }

}

<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Account_role extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        if ($this->flags->is_login === FALSE) {
            redirect(base_url('welcome'));
        }
        $this->load->model('system/account_role_model');
        $this->data['choices']['group'] = $this->user_group_model->getChoices();
        if (!isset($this->data['filter']['page'])) {
            $this->data['filter']['page'] = '1';
        }
        if (!isset($this->data['filter']['user_group_id'])) {
            $this->data['filter']['user_group_id'] = '';
        }
        if (!isset($this->data['filter']['name'])) {
            $this->data['filter']['name'] = '';
        }
        if (!isset($this->data['filter']['b_name'])) {
            $this->data['filter']['b_name'] = '';
        }
        if (!isset($this->data['filter']['idno'])) {
            $this->data['filter']['idno'] = '';
        }
        if (!isset($this->data['filter']['username'])) {
            $this->data['filter']['username'] = '';
        }
    }

    public function index()
    {
        $this->data['page_name'] = 'list';

        $conditions = array();
        $page = $this->data['filter']['page'];
        $rows = $this->data['filter']['rows'];

        if ($this->data['filter']['user_group_id'] != '') {
            $conditions['group_id'] = $this->data['filter']['user_group_id'];
        }
        if ($this->data['filter']['idno'] != '') {
            $conditions['idno'] = $this->data['filter']['idno'];
        }
        if ($this->data['filter']['username'] != '') {
            $conditions['username'] = $this->data['filter']['username'];
        }
        $attrs = array(
            'conditions' => $conditions,
        );

        if ($this->data['filter']['name'] != '') {
            $attrs['name'] = $this->data['filter']['name'];
        }
        if ($this->data['filter']['b_name'] != '') {
            $attrs['b_name'] = $this->data['filter']['b_name'];
        }

        $this->data['filter']['total'] = $total = $this->account_role_model->getListCount($attrs);
        $this->data['filter']['offset'] = $offset = ($page -1) * $rows;

        $attrs['rows'] = $rows;
        $attrs['offset'] = $offset;

        $this->data['list'] = $this->account_role_model->getList($attrs);
        // jd($this->data['list']);
        foreach ($this->data['list'] as & $row) {
            $row['link_edit'] = base_url("system/account_role/edit/{$row['id']}/?{$_SERVER['QUERY_STRING']}");
        }
        $this->load->library('pagination');
        $config['base_url'] = base_url("system/account_role?". $this->getQueryString(array(), array('page')));
        $config['total_rows'] = $total;
        $config['per_page'] = $rows;
        $this->pagination->initialize($config);

        $this->data['link_add'] = base_url("system/account_role/add/?{$_SERVER['QUERY_STRING']}");
        $this->data['link_delete'] = base_url("system/account_role/delete/?{$_SERVER['QUERY_STRING']}");
        $this->data['role_import'] = base_url("system/account_role/import");
        $this->data['link_refresh'] = base_url("system/account_role/");

        $this->layout->view('system/account_role/list', $this->data);
    }

    public function co_worker()
	{
		if (!isset($this->data['filter']['worker_page'])) {
            $this->data['filter']['worker_page'] = '1';
        }
        if (!isset($this->data['filter']['key'])) {
            $this->data['filter']['key'] = '';
        }
        if (!isset($this->data['filter']['key1'])) {
            $this->data['filter']['key1'] = '';
        }
        if (!isset($this->data['filter']['key2'])) {
            $this->data['filter']['key2'] = '';
        }
        if (!isset($this->data['filter']['key3'])) {
            $this->data['filter']['key3'] = '';
        }

        $page = $this->data['filter']['worker_page'];
        $rows = $this->data['filter']['rows'];

        $conditions = array();

        $attrs = array();
        $attrs['conditions'] = $conditions;

        if ($this->data['filter']['key'] != '') {
            $attrs['b_name'] = $this->data['filter']['key'];
        }
        if ($this->data['filter']['key1'] != '') {
            $attrs['username'] = $this->data['filter']['key1'];
        }
        if ($this->data['filter']['key2'] != '') {
            $attrs['idno'] = strtoupper($this->data['filter']['key2']);
        }
        if ($this->data['filter']['key3'] != '') {
            $attrs['name'] = $this->data['filter']['key3'];
        }

        // jd($attrs);
        $total_query_records = $this->user_model->getWorkerListCount($attrs);
        $this->data['filter']['offset'] = $offset = ($page -1) * $rows;
        $this->data['total_page'] = ceil($total_query_records / $rows);

        $attrs['rows'] = $rows;
        $attrs['offset'] = $offset;

        $this->data['worker_list'] = $this->user_model->getWorkerList($attrs);
        // jd($this->data['worker_list']);
		$this->load->view('system/account_role/co_worker_1', $this->data);
	}

    public function add()
    {
        $this->data['page_name'] = 'add';
        $this->data['form'] = $this->account_role_model->getFormDefault();
        if ($post = $this->input->post()) {
        	$this->data['form'] = $post;
        	$conditions = array(
        		'username' => $post['username'],
        		'group_id' => $post['group_id'],
        	);
        	$num = $this->account_role_model->getCount($conditions);
        	if($num == '0'){
        		if ($this->_isVerify('add') == TRUE) {
	                $insert_date = new DateTime();
                    $insert_date = $insert_date->format('Y-m-d H:i:s');
	                $fields = array(
	                	'username' => $post['username'],
        				'group_id' => $post['group_id'],
        				'cre_user' => $this->flags->user['username'],
        				'upd_user' => $this->flags->user['username'],
        				'cre_date' => $insert_date,
        				'upd_date' => $insert_date,
	                );

	                $saved_id = $this->account_role_model->insert($fields);
	                if ($saved_id) {
	                    $this->setAlert(1, '資料新增成功');
	                }

	                redirect(base_url('system/account_role/'));
	            }
        	}else{
        		$this->data['error_msg'] = "新增失敗!&emsp;帳號重複。";
        	}
        }

        $this->data['link_save'] = base_url("system/account_role/add/");
        $this->data['link_cancel'] = base_url('system/account_role/');
        $this->data['link_refresh'] = base_url("system/account_role/add");
        $this->data['co_worker'] = base_url("system/account_role/co_worker");

        $this->layout->view('system/account_role/add', $this->data);
    }

    public function edit($id)
    {
        $this->data['page_name'] = 'edit';
        $this->data['form'] = $this->account_role_model->_get($id);
        if ($post = $this->input->post()) {
        	$this->data['form'] = $post;
        	$conditions = array(
        		'username' => $post['username'],
        		'group_id' => $post['group_id'],
        	);
        	$num = $this->account_role_model->getCount($conditions);
        	if($num == '0'){
        		if ($this->_isVerify('edit') == TRUE) {
	                $insert_date = new DateTime();
                    $insert_date = $insert_date->format('Y-m-d H:i:s');
	                $fields = array(
	                	'username' => $post['username'],
        				'group_id' => $post['group_id'],
        				'upd_user' => $this->flags->user['username'],
        				'upd_date' => $insert_date,
	                );
                    
	                $rs = $this->account_role_model->update($id, $fields);
	                if ($rs) {
	                    $this->setAlert(1, '資料更新成功');
	                }

	                redirect(base_url('system/account_role/'));
	            }
        	}else{
        		$this->data['error_msg'] = "更新失敗!&emsp;帳號重複。";
        	}
        }

        $this->data['link_save'] = base_url("system/account_role/edit/{$id}");
        $this->data['link_cancel'] = base_url('system/account_role/');
        $this->data['link_refresh'] = base_url("system/account_role/edit/{$id}");
        $this->data['co_worker'] = base_url("system/account_role/co_worker");

        $this->layout->view('system/account_role/edit', $this->data);
    }

    private function _isVerify($action='add', $old_data=array())
    {
        $config = $this->account_role_model->getVerifyConfig();
        if ($action == 'edit') {
        }

        $this->form_validation->set_rules($config);
        $this->form_validation->set_error_delimiters('<p class="text-danger">', '</p>');
        // $this->form_validation->set_message('required', '請勿空白');

        return ($this->form_validation->run() == FALSE)? FALSE : TRUE;
    }

    public function delete()
    {
        if ($post = $this->input->post()) {
            foreach ($post['rowid'] as $id) {
                $rs = $this->account_role_model->delete($id);
            }
            $this->setAlert(2, '資料刪除成功');
        }

        redirect(base_url("system/account_role/?{$_SERVER['QUERY_STRING']}"));
    }

    public function import()
    {
        $massage = '';
        if($post = $this->input->post()){
           if (isset($_FILES['courseSetupfile']) && $_FILES['courseSetupfile']['tmp_name'] != '') {
                if (!fileExtensionCheck($_FILES['courseSetupfile']['name'], ['csv'])){
                    $massage = "不允許的檔案格式";
                }else{
                    $file = fopen(sys_get_temp_dir().DIRECTORY_SEPARATOR.basename($_FILES['courseSetupfile']['tmp_name']),"r");
                    $i = 1;
                    $import_susess = '0';
                    $import_falut = '0';
                    while(! feof($file))
                    {
                        $data = fgetcsv($file);
                        if($i == 1){
                            $i++;
                            continue;
                        }
    
                        if($data){
                            foreach($data as & $row){
                                $row = iconv('big5', 'UTF-8//IGNORE', $row);
                                $row = strtoupper(trim($row));
                            }
                            $conditions = array(
                                'idno' => $data[0],
                            );
                            $person = $this->user_model->get($conditions);
                            if($person){
                                $conditions = array(
                                    'username' => $person['username'],
                                    'group_id' => $post['group_id'],
                                );
                                $num = $this->account_role_model->getCount($conditions);
                                if($num == 0){
                                    $insert_date = new DateTime();
                                    $insert_date = $insert_date->format('Y-m-d H:i:s');
                                    $fields = array(
                                        'username' => $person['username'],
                                        'group_id' => $post['group_id'],
                                        'cre_user' => $this->flags->user['username'],
                                        'upd_user' => $this->flags->user['username'],
                                        'cre_date' => $insert_date,
                                        'upd_date' => $insert_date,
                                    );
    
                                    $saved_id = $this->account_role_model->insert($fields);
                                    if ($saved_id) {
                                        $import_susess ++;
                                    }else{
                                        $massage .= $data[0]." 匯入失敗"."<br>";
                                        $import_falut++;
                                    }
    
                                }else{
                                    $massage .= $data[0]." 匯入失敗"."<br>";
                                    $import_falut++;
                                }
    
                            }else{
                                $massage .= $data[0]." 無此帳號"."<br>";
                                $import_falut++;
                            }
    
                        }
                    }
                    fclose($file);
                    $massage .= "匯入成功".$import_susess."筆<br>";
                    $massage .= "匯入失敗".$import_falut."筆<br>";
                }
            }
        }

        $this->data['form']['massage'] =  $massage;

        $this->load->view('system/account_role/account_role_import', $this->data);
    }

}

<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Bank_code_merge extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        if ($this->flags->is_login === FALSE) {
            redirect(base_url('welcome'));
        }

        $this->load->model('data/bank_code_model');
        $this->load->model('data/code_history_model');
        $this->load->model('data/teacher_model');

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

        $page = $this->data['filter']['page'];
        $rows = $this->data['filter']['rows'];

        $conditions = array();
        $conditions['(cancel_flag <> "Y" or cancel_flag is null)'] = null;
        $attrs = array(
            'conditions' => $conditions,
        );
        if ($this->data['filter']['q'] !== '' ) {
            $attrs['q'] = $this->data['filter']['q'];
        }

        $this->data['filter']['total'] = $total = $this->bank_code_model->getListCount($attrs);
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

        $this->data['list'] = $this->bank_code_model->getList($attrs);
        foreach ($this->data['list'] as & $row) {
            $row['link_edit'] = base_url("data/bank_code_merge/edit/{$row['id']}/?{$_SERVER['QUERY_STRING']}");
        }
        $this->load->library('pagination');
        $config['base_url'] = base_url("data/bank_code_merge?". $this->getQueryString(array(), array('page')));
        $config['total_rows'] = $total;
        $config['per_page'] = $rows;
        $this->pagination->initialize($config);

        $this->data['btn_import'] = base_url("data/bank_code_merge/import");
        $this->data['link_delete'] = base_url("data/bank_code_merge/delete/?{$_SERVER['QUERY_STRING']}");
        $this->data['link_refresh'] = base_url("data/bank_code_merge/");

        $this->layout->view('data/bank_code_merge/list', $this->data);
    }
    public function edit($id)
    {
        $this->data['page_name'] = 'edit';
        $old_data = $this->bank_code_model->get($id);

        $this->data['form'] = array(
            'old_item_id' => $old_data['item_id'],
            'item_id' => '',
        );

        if ($post = $this->input->post()) {
            $old_data = $this->bank_code_model->get($id);
            if ($this->_isVerify('edit', $old_data) == TRUE) {
                $conditions = array(
                    'item_id' => $post['item_id'],
                );
                $data_exists = $this->bank_code_model->exists($conditions);

                if($data_exists){

                    $conditions = array(
                        'bank_code' => $old_data['item_id'],
                    );
                    $fields = array(
                        'bank_code' => $post['item_id'],
                        'bank_account' => '',
                    );
                    $this->teacher_model->update($conditions, $fields);
                    $upd_date = date('Y-m-d H:i:s');
                    $fields = array(
                        'modify_time' => $upd_date,
                        'modify_user' => $this->flags->user['id'],
                        'cancel_flag' => 'Y',
                    );
                    $this->bank_code_model->update($old_data['id'], $fields);

                }else{

                    $conditions = array(
                        'bank_code' => $old_data['item_id'],
                    );
                    $fields = array(
                        'bank_code' => $post['item_id'],
                        'bank_account' => '',
                    );
                    $this->teacher_model->update($conditions, $fields);

                    $fields = array(
                        'item_id' => $post['item_id'],
                        'name' => $old_data['name'],
                        'remark' => $old_data['remark'],
                        'enable' => $old_data['enable'],
                    );
                    $fields['cancel_flag'] = '';
                    $fields['create_time'] = date('Y-m-d H:i:s');
                    $fields['create_user'] = $this->flags->user['id'];
                    $fields['modify_time'] = date('Y-m-d H:i:s');
                    $fields['modify_user'] = $this->flags->user['id'];
                    $saved_id = $this->bank_code_model->_insert($fields);
                    $fields = array(
                        'type_id' => '14',
                        'item_id' => $fields['item_id'],
                        'description' => $old_data['name'],
                        'memo' => $old_data['remark'],
                        'cre_user' => $fields['create_user'],
                        'cre_date' => $fields['create_time'],
                        'task' => '異動(新增)',
                        'upd_user' => $fields['modify_user'],
                        'log_date' => date('Y-m-d H:i:s'),
                        'add_val1' => '',
                    );
                    $this->code_history_model->insert($fields);

                    $upd_date = date('Y-m-d H:i:s');
                    $fields = array(
                        'modify_time' => $upd_date,
                        'modify_user' => $this->flags->user['id'],
                        'cancel_flag' => 'Y',
                    );
                    $this->bank_code_model->update($old_data['id'], $fields);
                }

                $post['modify_time'] = date('Y-m-d H:i:s');
                $post['modify_user'] = $this->flags->user['id'];
                $fields = array(
                    'type_id' => '14',
                    'item_id' => $old_data['item_id'],
                    'description' => $old_data['name'],
                    'memo' => $old_data['remark']."(new:".$post['item_id'].")",
                    'cre_user' => $post['modify_user'],
                    'cre_date' => $post['modify_time'],
                    'task' => '異動(修改)',
                    'upd_user' => $post['modify_user'],
                    'log_date' => date('Y-m-d H:i:s'),
                    'add_val1' => '',
                );
                $this->code_history_model->insert($fields);
                $this->setAlert(2, '銀行代碼整併更新完成!');
                redirect(base_url("data/bank_code_merge/?{$_SERVER['QUERY_STRING']}"));
            }
        }

        $this->data['link_save'] = base_url("data/bank_code_merge/edit/{$id}/?{$_SERVER['QUERY_STRING']}");
        $this->data['link_cancel'] = base_url("data/bank_code_merge/?{$_SERVER['QUERY_STRING']}");
        $this->layout->view('data/bank_code_merge/edit', $this->data);

    }

    private function _isVerify($action='add', $old_data=array())
    {
        $config = $this->bank_code_model->getVerifyConfig();
        if ($action == 'edit') {
            $config['item_id']['rules'] = 'trim|required';
            $config['name']['rules'] = '';
            $config['enable']['rules'] = '';
        }

        $this->form_validation->set_rules($config);
        $this->form_validation->set_error_delimiters('<p class="text-danger">', '</p>');
        // $this->form_validation->set_message('required', '請勿空白');

        return ($this->form_validation->run() == FALSE)? FALSE : TRUE;
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
    
                        if(count($data) > '0'){
                            foreach($data as & $row){
                                $row = iconv('big5', 'UTF-8//IGNORE', $row);
                                $row = strtoupper(trim($row));
                            }
                            // jd($data,1);
                            $insert_fields = array(
                                    'item_id' => $data[1],
                                    'name' => $data[2],
                                    'remark' => $data[3],
                                    'cancel_flag' => '',
                                    'enable' => '1',
                                    'create_user' => $this->flags->user['id'],
                                    'create_time' => date('Y-m-d H:i:s'),
                                    'modify_user' => $this->flags->user['id'],
                                    'modify_time' => date('Y-m-d H:i:s'),
                                );
    
                            if(($data[0]=="")&&($data[1]!='')){
    
                                $rs = $this->bank_code_model->_insert($insert_fields);
                                if($rs){
                                    $import_susess ++;
                                }else{
                                    $massage .= "新增銀行代碼:".$data[1]."銀行名稱:".$data[2]."失敗<br>";
                                }
    
                            }elseif(($data[0]!='')&&($data[1]!='')&&($data[0]!=$data[1])){
    
                                $fields = array(
                                    'modify_time' => date('Y-m-d H:i:s'),
                                    'modify_user' => $this->flags->user['id'],
                                    'cancel_flag' => 'Y',
                                );
                                $conditions = array(
                                    'item_id' => $data[0],
                                );
                                $this->bank_code_model->update($conditions, $fields);
    
                                $rs = $this->bank_code_model->_insert($insert_fields);
                                if($rs){
                                    $fields = array(
                                        'bank_code' => $data[1],
                                        'bank_account' => '',
                                    );
                                    $conditions = array(
                                        'bank_code' => $data[0],
                                    );
                                    $this->teacher_model->update($conditions, $fields);
                                    $import_susess ++;
                                }else{
                                    $massage .= "更新銀行代碼:".$data[1]."銀行名稱:".$data[2]."失敗<br>";
                                }
    
                            }elseif(($data[0]!='')&&($data[1]!='')&&($data[0]==$data[1])){
    
                                $fields = array(
                                    'item_id' => $data[1],
                                    'name' => $data[2],
                                    'remark' => $data[3],
                                    'modify_time' => date('Y-m-d H:i:s'),
                                    'modify_user' => $this->flags->user['id'],
                                );
                                $conditions = array(
                                    'item_id' => $data[0],
                                );
                                $rs = $this->bank_code_model->update($conditions, $fields);
    
                                if($rs){
                                    $fields = array(
                                        'bank_code' => $data[1],
                                        'bank_account' => '',
                                    );
                                    $conditions = array(
                                        'bank_code' => $data[0],
                                    );
                                    $this->teacher_model->update($conditions, $fields);
                                    $import_susess ++;
                                }else{
                                    $massage .= "更新銀行代碼:".$data[1]."銀行名稱:".$data[2]."失敗<br>";
                                }
    
                            }elseif(($data[0]!='')&&($data[1]=='')){
    
                                $fields = array(
                                    'name' => $data[2],
                                    'remark' => $data[3],
                                    'modify_time' => date('Y-m-d H:i:s'),
                                    'modify_user' => $this->flags->user['id'],
                                );
                                $conditions = array(
                                    'item_id' => $data[0],
                                );
                                $rs = $this->bank_code_model->update($conditions, $fields);
                                if($rs){
                                    $fields = array(
                                        'bank_code' => $data[1],
                                        'bank_account' => '',
                                    );
                                    $conditions = array(
                                        'bank_code' => $data[0],
                                    );
                                    $this->teacher_model->update($conditions, $fields);
                                    $import_susess ++;
                                }else{
                                    $massage .= "更新銀行代碼:".$data[1]."銀行名稱:".$data[2]."失敗<br>";
                                }
                            }
    
                        }
                    }
                    fclose($file);
                    $massage .= "匯入成功".$import_susess."筆<br>";                    
                }                 

            }
        }

        $this->data['form']['massage'] =  $massage;

        $this->load->view('data/bank_code_merge/bank_code_merge_import', $this->data);
    }
}

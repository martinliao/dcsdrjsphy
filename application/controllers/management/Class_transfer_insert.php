<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Class_transfer_insert extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        if ($this->flags->is_login === FALSE) {
            redirect(base_url('welcome'));
        }

        $this->load->model('management/online_app_model');
        $this->load->model('management/class_transfer_model');
        $this->load->model('management/stud_modifylog_model');
    }

    public function index()
    {
        $this->load->library('pagination');
        $this->data['choices']['year'] = $this->_get_year_list();
        $this->data['choices']['yn_sel'] = array(
            '' => '請選擇修課狀態',
            '1' => '結訓',
            '2' => '報名',
            '3' => '選員',
            '4' => '退訓',
            '5' => '未報到',
            '6' => '取消報名',
            '7' => '取消參訓',
            '8' => '調訓',
        );

        $this->data['form'] = array(
             'class_no' => '',
             'class_name' => '',
             'yn_sel' => '',
             'term_before' => '',
             'studentid' => '',
             'year_move' => '108',
        );

        $date_now = new DateTime('now');
        $year_now = $date_now->format('Y');
        $this_yesr = $year_now - 1911;

        if (!isset($this->data['form']['year'])) {
            $this->data['form']['year'] = $this_yesr;
        }

        $this->data['query_type'] = '';
        $this->data['stud_list'] = array();
        if($post = $this->input->post()){
            $this->data['form']['year'] = $post['year'];
            $this->data['form']['class_no'] = $post['class_no_before'];
            $this->data['form']['class_name'] = $post['class_name_before'];
            $this->data['form']['yn_sel'] = $post['yn_sel'];
            $this->data['form']['term_before'] = $post['term_before'];
            $this->data['form']['studentid'] = $post['studentid'];

            $conditions = array(
                'year' => $post['year'],
                'class_no' => $post['class_no_before'],
                'term' => $post['term_before'],
            );

            $this->data['query_type'] = 'Y';
            $query_cond_string="";

            if($post['studentid']!=""){
                $conditions['id'] = $post['studentid'];
                $query_cond_string=$query_cond_string. " and o.id=".$this->db->escape(addslashes($post['studentid']))." ";
            }

            if($post['yn_sel']!=""){
                $conditions['yn_sel'] = $post['yn_sel'];
                $query_cond_string=$query_cond_string." and o.yn_sel=".$this->db->escape(addslashes($post['yn_sel']))." ";
            }
            /*
            $sql =  " select o.*,t.name as title,C.NAME Beaurau_name,v.first_name,v.last_name,v.birthday,o.yn_sel from online_app o left join vm_all_account v on o.id=v.personal_id  LEFT JOIN BUREAU_CODE C ON V.BEAURAU_ID = C.BUREAU_ID left join title t on t.id = v.title
                        where o.year=".$this->db->escape(addslashes($post['year']))." and o.term=".$this->db->escape(addslashes($post['term_before']))." and o.class_no=".$this->db->escape(addslashes($post['class_no_before']))." ".$query_cond_string;
            $f = fopen("hao.txt", "w");
            fwrite($f, str_replace(array("\r", "\n", "\r\n", "\n\r" , "%0a", "%0d"), "", $sql));
            fclose($f);
            */

            $this->data['stud_list'] = $this->online_app_model->getList($conditions);
        }

        $this->data['link_refresh'] = base_url("management/class_transfer_insert/");
        $this->data['co_class_url'] = base_url("management/class_transfer_insert/co_class/1");
        $this->data['co_class_url2'] = base_url("management/class_transfer_insert/co_class/2");
        $this->layout->view('management/class_transfer_insert/list',$this->data);
    }

    public function _get_year_list()
    {
        $year_list = array();

        $date_now = new DateTime('now');
        $year_now = $date_now->format('Y');
        $this_yesr = $year_now - 1910;

        for($i=$this_yesr; $i>=90; $i--){
            $year_list[$i] = $i;
        }
        // jd($year_list,1);
        return $year_list;
    }

    public function co_class($field_type=NULL)
    {
        if (!isset($this->data['filter']['class_page'])) {
            $this->data['filter']['class_page'] = '1';
        }
        if (!isset($this->data['filter']['class_q'])) {
            $this->data['filter']['class_q'] = '';
        }

        if($field_type == '1'){
            $savefeild = 'addClass_before';
        }else{
            $savefeild = 'addClass_move';
        }

        $this->data['savefeild'] = $savefeild;

        $page = $this->data['filter']['class_page'];
        $rows = $this->data['filter']['rows'];

        $conditions = array();
        $conditions['is_cancel'] = '0';
        $attrs = array();
        $attrs['conditions'] = $conditions;

        if ($this->data['filter']['class_q'] !== '' ) {
            $attrs['q'] = $this->data['filter']['class_q'];
        }
        // jd($attrs);
        $total_query_records = $this->class_transfer_model->getClassListCount($attrs);
        $this->data['filter']['offset'] = $offset = ($page -1) * $rows;
        $this->data['total_page'] = ceil($total_query_records / $rows);

        $attrs['rows'] = $rows;
        $attrs['offset'] = $offset;

        $this->data['class_list'] = $this->class_transfer_model->getClassList($attrs);
        // jd($this->data['bureau_list'],1);
        $this->load->view('management/class_transfer_insert/co_class_name', $this->data);
    }

    public function ajax($action)
    {
        //$action = $this->input->get('action');
        $post = $this->input->post();

        $result = array(
            'status' => FALSE,
            'data' => array(),
        );
        $rs = NULL;
        if ($action && $post) {
            $fields = array();
            switch ($action) {

                case 'do_transfer':

                    $error = FALSE;

                    $conditions = array(
                        'year' => $post['year_move'],
                        'class_no' => $post['class_no_move'],
                        'term' => $post['term_move'],
                    );
                    $class = $this->class_transfer_model->get($conditions);

                    if(empty($class)){
                        $error = TRUE;
                    }

                    if($error === TRUE){
                        $result['msg'] = '轉入錯誤: 無此班期';
                    }else{
                        for ($i=0;$i<count($post['chk']);$i++) {
                            $arr = explode(",",$post['chk'][$i]);
                            $id = $arr[0];
                            $conditions = array(
                                'id' => $id,
                                'year' => $post['year_move'],
                                'class_no' => $post['class_no_move'],
                                'term' => $post['term_move'],
                            );
                            $checkPerson = $this->online_app_model->getRegist($conditions);
                            if($checkPerson == 0){
                            	$regist_del = $this->online_app_model->getDel($conditions);
                                $insert_date = new DateTime();
                                $insert_date = $insert_date->format('Y-m-d H:i:s');
                                $conditions = array(
                                    'idno' => $id,
                                );
                                $person = $this->user_model->get($conditions);
                                $conditions = array(
                                    'bureau_id' => $person['bureau_id'],
                                    'year' => $post['year_move'],
                                    'class_no' => $post['class_no_move'],
                                    'term' => $post['term_move'],
                                );
                                $insertOrder = $this->online_app_model->getInsertOrder($conditions);

                                if($regist_del != 0){
                                    $conditions = array(
                                        'id' => $id,
                                        'year' => $post['year_move'],
                                        'class_no' => $post['class_no_move'],
                                        'term' => $post['term_move'],
                                    );
                                    $fields = array(
                                        'yn_sel' => '2',
                                        'insert_order' => $insertOrder,
                                        'upd_user' => $this->flags->user['username'],
                                        'upd_date' => $insert_date,
                                    );
                                    $this->online_app_model->update($conditions, $fields);

                                }else{
                                    $conditions = array(
                                        'year' => $post['year_move'],
                                        'class_no' => $post['class_no_move'],
                                        'term' => $post['term_move'],
                                    );
                                    $priority = $this->class_transfer_model->getPriority($conditions);

                                    $insert_fields = array(
                                        'year' => $post['year_move'],
                                        'class_no' => $post['class_no_move'],
                                        'term' => $post['term_move'],
                                        'id' => $id,
                                        'beaurau_id' => $person['bureau_id'],
                                        'yn_sel' => '2',
                                        'insert_order' => $insertOrder,
                                        'insert_date' => $insert_date,
                                        'cre_user' => $this->flags->user['username'],
                                        'cre_date' => $insert_date,
                                        'upd_user' => $this->flags->user['username'],
                                        'upd_date' => $insert_date,
                                        'priority' => $priority,
                                    );
                                    $this->online_app_model->insert($insert_fields);

                                }

                                $fields = array(
                                    'year' => $post['year_move'],
                                    'class_no' => $post['class_no_move'],
                                    'term' => $post['term_move'],
                                    'beaurau_id' => $this->flags->user['bureau_id'],
                                    'id' => $id,
                                    'modify_item' => '報名',
                                    'modify_date' => $insert_date,
                                    'o_id' => $id,
                                    'n_term' => $post['term_move'],
                                    'upd_user' => $this->flags->user['username'],
                                    's_beaurau_id' => $person['bureau_id'],
                                );
                                $this->stud_modifylog_model->insert($fields);

                                $conditions = array(
                                    'id' => $id,
                                    'year' => $post['oldyear'],
                                    'class_no' => $post['oldclassno'],
                                    'term' => $post['oldterm'],
                                );
		                        $fields = array(
		                            'yn_sel' => '6',
		                            'upd_user' => $this->flags->user['username'],
		                            'upd_date' => $insert_date,
		                        );
		                        $this->online_app_model->update($conditions, $fields);

                                $fields = array(
                                    'year' => $post['oldyear'],
                                    'class_no' => $post['oldclassno'],
                                    'term' => $post['oldterm'],
                                    'beaurau_id' => $this->flags->user['bureau_id'],
                                    'id' => $id,
                                    'modify_item' => '取消',
                                    'modify_date' => $insert_date,
                                    'modify_log' => "轉班:{$post['class_no_move']}:{$post['term_move']}",
                                    'o_id' => $id,
                                    'n_term' => $post['term_move'],
                                    'upd_user' => $this->flags->user['username'],
                                    's_beaurau_id' => $person['bureau_id'],
                                );
                                $this->stud_modifylog_model->insert($fields);
                                $result['status'] = TRUE;
                            }

                        }

                    }

                    break;

            }
        }

        echo json_encode($result);
    }

}

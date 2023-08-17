<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Election_worker_training_business extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        if ($this->flags->is_login === FALSE) {
            redirect(base_url('welcome'));
        }

        $this->load->model('management/election_model');
        $this->load->model('management/beaurau_persons_model');
        $this->load->model('management/org_detail_model');
        $this->load->model('management/stud_modifylog_model');
        $this->load->model('management/online_app_model');
        $this->data['choices']['year'] = $this->_get_year_list();

        if (!isset($this->data['filter']['page'])) {
            $this->data['filter']['page'] = '1';
        }
        if (!isset($this->data['filter']['sort'])) {
            $this->data['filter']['sort'] = '';
        }
        if (!isset($this->data['filter']['class_no'])) {
            $this->data['filter']['class_no'] = '';
        }
        if (!isset($this->data['filter']['class_name'])) {
            $this->data['filter']['class_name'] = '';
        }

        $date_now = new DateTime('now');
        $year_now = $date_now->format('Y');
        $this_yesr = $year_now - 1911;

        if (!isset($this->data['filter']['year'])) {
            $this->data['filter']['year'] = $this_yesr;
        }

    }

    public function index()
    {
        $this->data['page_name'] = 'list';
        $page = $this->data['filter']['page'];
        $rows = $this->data['filter']['rows'];

        $conditions = array();
        $conditions['year'] = $this->data['filter']['year'];
        $conditions['req_beaurau'] = $this->flags->user['bureau_id'];
        $sql_date = new DateTime('now');
        $sql_date = $sql_date->format('Y-m-d');
        $conditions['sel_s_date <='] = $sql_date;
        $conditions['sel_e_date >='] = $sql_date;
        $attrs = array(
            'conditions' => $conditions,
        );

        $attrs['class_status'] = array('2','3');

        if ($this->data['filter']['class_name'] !== '' ) {
            $attrs['class_name'] = $this->data['filter']['class_name'];
        }
        if ($this->data['filter']['class_no'] != '' ) {
            $attrs['class_no'] = $this->data['filter']['class_no'];
        }

        $this->data['filter']['total'] = $total = $this->election_model->getListCount($attrs);
        $this->data['filter']['offset'] = $offset = ($page -1) * $rows;

        $attrs = array(
            'conditions' => $conditions,
            'rows' => $rows,
            'offset' => $offset,
        );
        $attrs['class_status'] = array('2','3');
        if ($this->data['filter']['class_name'] != '' ) {
            $attrs['class_name'] = $this->data['filter']['class_name'];
        }
        if ($this->data['filter']['class_no'] != '' ) {
            $attrs['class_no'] = $this->data['filter']['class_no'];
        }
        if ($this->data['filter']['sort'] != '' ) {
            $attrs['sort'] = $this->data['filter']['sort'];
        }

        $this->data['list'] = $this->election_model->getList($attrs);
        // jd($this->data['list'],1);
        foreach ($this->data['list'] as & $row) {
            $row['link_add'] = base_url("management/election_worker_training_business/election_add/{$row['seq_no']}");
        }
        $this->load->library('pagination');
        $config['base_url'] = base_url("management/election_worker_training_business?". $this->getQueryString(array(), array('page')));
        $config['total_rows'] = $total;
        $config['per_page'] = $rows;
        $this->pagination->initialize($config);

        $this->data['link_refresh'] = base_url("management/election_worker_training_business/");

        $this->layout->view('management/election_worker_training_business/list', $this->data);
    }

    public function election_add($seq_no=NULL)
    {

        $this->data['class'] = $this->election_model->get($seq_no);
        $this->data['class']['limil_max'] = $this->data['class']['no_persons'];
        $this->data['class']['num'] = '';
        $this->data['class']['id1'] = '';
        $this->data['class']['pageType'] = '1';

        if (!isset($this->data['filter']['id'])) {
            $this->data['filter']['id'] = '';
        }

        if(!empty($this->data['class']['worker'])){
            $conditions = array(
                'idno' => $this->data['class']['worker'],
            );
            $person = $this->user_model->get($conditions);
            $this->data['class']['worker'] = $person['name'];
        }
        $conditions = array(
            'year' => $this->data['class']['year'],
            'class_no' => $this->data['class']['class_no'],
            'term' => $this->data['class']['term'],
        );
        $this->data['class']['max_group'] = $this->election_model->get_max_group($conditions);
        $this->data['class']['disableCount'] = $this->election_model->get_disableCount($conditions);
        $this->data['class']['counter'] = $this->election_model->get_counter($conditions);
        if($this->data['filter']['id'] != ''){
            $this->data['class']['id1'] = $this->data['filter']['id'];
            $id1Split = explode(",", $this->data['class']['id1']);
            $id1Str = "";
            for($i = 0;$i<count($id1Split) - 1;$i++) {

                if($i == count($id1Split) - 2) {
                    $id1Str .= "'".$id1Split[$i]."'";
                }else {
                    $id1Str .= "'".$id1Split[$i]."',";
                }

            }
            $conditions['id1'] = $id1Str;
        }
        $this->data['regist_list'] = $this->election_model->get_regist_list($conditions);
        $this->data['class']['data_count'] = count($this->data['regist_list']);

        foreach($this->data['regist_list'] as & $row){
            $insert_date = new DateTime($row['insert_date']);
            $row['insert_date'] = $insert_date->format('Y-m-d');

            if($row['bureau_id']){
                $conditions = array(
                    'year' => $this->data['class']['year'],
                    'class_no' => $this->data['class']['class_no'],
                    'term' => $this->data['class']['term'],
                    'beaurau' => $row['bureau_id'],
                );
                $beaurau_persons = $this->beaurau_persons_model->get($conditions);
                if(empty($beaurau_persons['persons2'])){
                    $row['persons'] = '0';
                }else{
                    $row['persons'] = $beaurau_persons['persons2'];
                }
            }else{
                $row['persons'] = '0';
            }

        }
        $this->data['link_ids'] = base_url("management/election_worker_training_business/election_add/{$seq_no}");

        $this->data['link_cancel'] = base_url("management/election_worker_training_business/?{$_SERVER['QUERY_STRING']}");
        // jd($this->data['class'],1);
        $this->layout->view('management/election_worker_training_business/election_add', $this->data);
    }

    public function _get_year_list()
    {
        $year_list = array();

        $date_now = new DateTime('now');
        $year_now = $date_now->format('Y');
        $this_yesr = $year_now - 1911;

        for($i=$this_yesr+1; $i>=90; $i--){
            $year_list[$i] = $i;
        }
        // jd($year_list,1);
        return $year_list;
    }

    public function ajax($action)
    {
        $post = $this->input->post();

        $result = array(
            'status' => FALSE,
            'data' => array(),
        );
        $rs = NULL;
        if ($action && $post) {
            $fields = array();
            switch ($action) {
                case 'do_election':

                    $error = FALSE;

                    if(empty($post['year'])){
                        $error = TRUE;
                    }
                    if(empty($post['class_no'])){
                        $error = TRUE;
                    }
                    if(empty($post['term'])){
                        $error = TRUE;
                    }
                    if(empty($post['id'])){
                        $error = TRUE;
                    }

                    if($error === TRUE){
                        $result['msg'] = '操作錯誤';
                    }else{

                        $idarr=explode(',', $post['id']);
                        $yn_selarr=explode(',', $post['yn_sel']);
                        $st_noarr=explode(',', $post['st_no']);
                        $group_noarr=explode(',', $post['group_no']);
                        $old_yn_selarr=explode(',', $post['old_yn_sel']);

                        $select_no_person = '0';

                        foreach($idarr as $key => $perid ){
                            $id    = $idarr[$key];
                            $st_no = $st_noarr[$key];
                            $group_no = $group_noarr[$key];
                            $yn_sel = $yn_selarr[$key];
                            $old_yn_sel = $old_yn_selarr[$key];

                            if($yn_sel==3){
                                $conditions = array(
                                    'year' => $post['year'],
                                    'class_no' => $post['class_no'],
                                    'term' => $post['term'],
                                    'id' => $id,
                                );
                                $bureau_name = $this->election_model->get_person($conditions);
                                $fields = array(
                                    'user_id' => $id,
                                    'year' => $post['year'],
                                    'term' => $post['term'],
                                    'classname' => $bureau_name['class_name'],
                                    'status' => '選課',
                                    'unit' => $bureau_name['bureau_name'],
                                );
                                $this->org_detail_model->insert($fields, 'time');
                            }

                            if ($old_yn_sel == '1' || $old_yn_sel == '4' || $old_yn_sel == '5' ){
                            continue;
                            }else if ($yn_sel == '2'){

                                $conditions = array(
                                    'year' => $post['year'],
                                    'class_no' => $post['class_no'],
                                    'term' => $post['term'],
                                    'id' => $id,
                                );

                                $fields = array(
                                    'upd_date' => date('Y-m-d H:i:s'),
                                    'upd_user' => $this->flags->user['username'],
                                    'st_no' => NULL,
                                    'yn_sel' => $yn_sel,
                                    'group_no' => '',
                                );
                                $this->online_app_model->update($conditions, $fields);

                            }else if ($old_yn_sel == '8'){

                                $conditions = array(
                                    'year' => $post['year'],
                                    'class_no' => $post['class_no'],
                                    'term' => $post['term'],
                                    'id' => $id,
                                );

                                $fields = array(
                                    'upd_date' => date('Y-m-d H:i:s'),
                                    'upd_user' => $this->flags->user['username'],
                                    'st_no' => $st_no,
                                    'yn_sel' => '8',
                                    'group_no' => $group_no,
                                );
                                $this->online_app_model->update($conditions, $fields);

                            }else{
                                $conditions = array(
                                    'year' => $post['year'],
                                    'class_no' => $post['class_no'],
                                    'term' => $post['term'],
                                    'id' => $id,
                                );

                                $fields = array(
                                    'upd_date' => date('Y-m-d H:i:s'),
                                    'upd_user' => $this->flags->user['username'],
                                    'st_no' => $st_no,
                                    'yn_sel' => $yn_sel,
                                    'group_no' => $group_no,
                                );
                                $this->online_app_model->update($conditions, $fields);
                            }

                            if($st_no != ''){
                                $select_no_person = $select_no_person + 1;
                            }

                            if ($old_yn_sel != '1' && $yn_sel != '2' && $old_yn_sel != '4' && $old_yn_sel != '5'){
                                $conditions = array(
                                    'year' => $post['year'],
                                    'class_no' => $post['class_no'],
                                    'term' => $post['term'],
                                    'id' => $id,
                                    'modify_item' => '選員',
                                );
                                $exist = $this->stud_modifylog_model->getCount($conditions);

                                $bureauId = $this->flags->user['bureau_id'];
                                if ($exist == '0'){
                                    $conditions = array(
                                        'idno' => $id,
                                    );
                                    $s_person = $this->user_model->_get($conditions);
                                    $fields = array(
                                        'year' => $post['year'],
                                        'class_no' => $post['class_no'],
                                        'term' => $post['term'],
                                        'beaurau_id' => $this->flags->user['bureau_id'],
                                        'id' => $id,
                                        'st_no' => $st_no,
                                        'modify_item' => '選員',
                                        'modify_log' => '',
                                        'o_id' => $id,
                                        'n_term' => $post['term'],
                                        'upd_user' => $this->flags->user['username'],
                                        's_beaurau_id' => $s_person['bureau_id'],
                                    );
                                    $this->stud_modifylog_model->insert($fields, 'modify_date');

                                } else {
                                    $fields = array(
                                        'beaurau_id' => $this->flags->user['bureau_id'],
                                        'st_no' => $st_no,
                                        'modify_date' => date('Y-m-d H:i:s'),
                                        'modify_log' => '',
                                        'upd_user' => $this->flags->user['username'],
                                    );
                                    $conditions = array(
                                        'year' => $post['year'],
                                        'class_no' => $post['class_no'],
                                        'term' => $post['term'],
                                        'id' => $id,
                                        'modify_item' => '選員',
                                    );
                                    $this->stud_modifylog_model->update($conditions, $fields);

                                }
                            }

                            if($select_no_person > '0'){
                                $conditions = array(
                                    'year' => $post['year'],
                                    'class_no' => $post['class_no'],
                                    'term' => $post['term'],
                                );
                                $fields = array(
                                    'seled_no_persons' => $select_no_person,
                                );
                                $this->election_model->update($conditions, $fields);
                            }

                        }
                        $result['status'] = TRUE;
                    }

                    break;
            }
        }
        echo json_encode($result);
    }

}

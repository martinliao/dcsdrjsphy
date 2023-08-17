<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Bb_1 extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        if ($this->flags->is_login === FALSE) {
            redirect(base_url('welcome'));
        }
        $this->load->model('management/bb_1_model');
        $this->load->model('management/stud_modifylog_model');
        $this->load->model('management/online_app_model');
        $this->load->model('data/venue_information_model');

        $this->data['choices']['year'] = $this->_get_year_list();

        if (!isset($this->data['filter']['page'])) {
            $this->data['filter']['page'] = '1';
        }
        if (!isset($this->data['filter']['class_name'])) {
            $this->data['filter']['class_name'] = '';
        }
        if (!isset($this->data['filter']['class_no'])) {
            $this->data['filter']['class_no'] = '';
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

        if(!in_array("1", $this->flags->user['group_id'])){
            $conditions['worker'] = $this->flags->user['idno'];
        }

        $conditions['year'] = $this->data['filter']['year'];

        $attrs = array(
            'conditions' => $conditions,
        );
        $attrs['where_special'] = "class_status in ('2','3')";
        if ($this->data['filter']['class_name'] !== '' ) {
            $attrs['class_name'] = $this->data['filter']['class_name'];
        }
        if ($this->data['filter']['class_no'] !== '' ) {
            $attrs['class_no'] = $this->data['filter']['class_no'];
        }

        $this->data['filter']['total'] = $total = $this->bb_1_model->getListCount($attrs);
        $this->data['filter']['offset'] = $offset = ($page -1) * $rows;

        $attrs = array(
            'conditions' => $conditions,
            'rows' => $rows,
            'offset' => $offset,
        );
        $attrs['where_special'] = "class_status in ('2','3')";
        if ($this->data['filter']['class_name'] !== '' ) {
            $attrs['class_name'] = $this->data['filter']['class_name'];
        }
        if ($this->data['filter']['class_no'] !== '' ) {
            $attrs['class_no'] = $this->data['filter']['class_no'];
        }

        $this->data['list'] = $this->bb_1_model->getList($attrs);
        foreach ($this->data['list'] as & $row) {
            $row['link_detail'] = base_url("management/bb_1/detail/{$row['seq_no']}/?{$_SERVER['QUERY_STRING']}");
        }
        $this->load->library('pagination');
        $config['base_url'] = base_url("management/bb_1?". $this->getQueryString(array(), array('page')));
        $config['total_rows'] = $total;
        $config['per_page'] = $rows;
        $this->pagination->initialize($config);

        $this->data['link_refresh'] = base_url("management/bb_1/");

        $this->layout->view('management/bb_1/list', $this->data);
    }
    public function detail($seq_no=NULL)
    {

        $this->data['require'] = $this->bb_1_model->get($seq_no);
        if($this->data['require']){
        	$this->data['link_refresh'] = base_url("management/bb_1/detail/{$seq_no}/?{$_SERVER['QUERY_STRING']}");
            $conditions = array(
                'year' => $this->data['require']['year'],
                'class_no' => $this->data['require']['class_no'],
                'term' => $this->data['require']['term'],
            );
            $this->data['require']['all_term'] = $this->bb_1_model->get_term($conditions);
            $conditions['order_by'] = 'st_no';
            $conditions['where_special'] = "yn_sel in ('3','8')";
            $this->data['require_list'] = $this->online_app_model->getList($conditions);
            // jd($this->data['require_list']);
            $this->data['link_cancel'] = base_url("management/bb_1?{$_SERVER['QUERY_STRING']}");
            $this->layout->view('management/bb_1/detail',$this->data);
        }else{
            $this->setAlert(3, '操作錯誤');
            redirect(base_url('management/bb_1'));
        }
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

                    if($post['rdo_type'] == 'single'){
                    	$conditions = array(
	                        'year' => $post['year'],
	                        'class_no' => $post['class_no'],
	                        'term' => $post['new_term'],
	                    );
	                    $class = $this->bb_1_model->get($conditions);
	                    $mx = $this->online_app_model->get_st_no($conditions);
	                    $max = $mx;
	                    $ck = count($post['ck']);
	                    $conditions['yn_sel'] = '3';
	                    $cnt_peo = $this->online_app_model->getCount($conditions);
	                    $total_ck = $cnt_peo + $ck;
	                    $conditions = array(
	                        'room_id' => $class['room_code'],
	                    );
	                    $class_room = $this->venue_information_model->get($conditions);
	                    $total_peo = $class_room['room_cap'];
	                    if($total_ck > $total_peo){
	                    	$result['msg'] = "超過該期設定人數!!!";
	                    }

	                    foreach($post['ck'] as $pid){
	                    	$conditions = array(
		                        'year' => $post['year'],
		                        'class_no' => $post['class_no'],
		                        'term' => $post['new_term'],
		                        'id' => $pid,
		                    );
                            $isset_count = $this->online_app_model->getCount($conditions);

                            if($isset_count == '0'){
                                $conditions = array(
                                    'year' => $post['year'],
                                    'class_no' => $post['class_no'],
                                    'term' => $post['term'],
                                    'id' => $pid,
                                );
                                $fields = array(
                                    'term' => $post['new_term'],
                                    'st_no' => $max,
                                );
                                $this->online_app_model->update($conditions, $fields);
                                $bureau_id = $this->flags->user['bureau_id'];
                                $insert_fields = array(
                                    'year' => $post['year'],
                                    'class_no' => $post['class_no'],
                                    'term' => $post['new_term'],
                                    'id' => $pid,
                                    'beaurau_id' => $bureau_id,
                                    'st_no' => $max,
                                    'modify_item' => '選員',
                                    'modify_log' => '批次換期',
                                    'o_id' => $pid,
                                    'n_term' => $post['term'],
                                    'upd_user' => $this->flags->user['username'],
                                );
                                $this->stud_modifylog_model->insert($insert_fields, 'modify_date');
                                $max++;
                            }
	                    }

                    }

                    if($post['rdo_type'] == 'multi'){
                    	$array_length = count($post['new_multi_term_array']);
					    $ck_length = count($post['ck']);
					    if($ck_length%$array_length==0){
					    	$range = $ck_length/$array_length;
					    }
					    else{
					    	if($post['change_type']=="1"){
					    		$range = ceil($ck_length/$array_length);
                    		}
                    		if($post['change_type']=="2"){
                    			$range = floor($ck_length/$array_length);
                    		}
					    }

					    $where_in = array(
					    	'field' => 'idno',
            				'value' => $post['ck'],
					    );
					    $person_data = $this->user_model->getPersonal($where_in);
					    $tmp_array = array();
					    foreach($person_data as $p_data){
					    	$tmp_array[$p_data['bureau_id']][] = $p_data['idno'];
					    }

					    $dispersion_array = array();
					    $tmp = 0;
					    $tmp_time = 0;

					    foreach($tmp_array as $v1) {
					    	foreach($v1 as $v2) {
					    		$dispersion_array[$post['new_multi_term_array'][$tmp]][] = $v2;
					    		if($post['change_type']=="1"){
					    			$tmp = $tmp + 1;
			        				if($tmp==$array_length){
			        					$tmp = 0;
			        				}
	                    		}
	                    		if($post['change_type']=="2"){
	                    			$tmp_time =$tmp_time +1;
				        			if($tmp_time==$range){
				        				$tmp = $tmp +1 ;
				        			}
	                    		}
					    	}
					    }

                    	foreach($dispersion_array as $key => $dispersion_row){
                    		$conditions = array(
		                        'year' => $post['year'],
		                        'class_no' => $post['class_no'],
		                        'term' => $key,
		                    );
		                    $class = $this->bb_1_model->get($conditions);
		                    $mx = $this->online_app_model->get_st_no($conditions);
		                    $max = $mx;
		                    $ck = count($dispersion_row);
		                    $conditions['yn_sel'] = '3';
		                    $cnt_peo = $this->online_app_model->getCount($conditions);
		                    $total_ck = $cnt_peo + $ck;
		                    $conditions = array(
		                        'room_id' => $class['room_code'],
		                    );
		                    $class_room = $this->venue_information_model->get($conditions);
		                    $total_peo = $class_room['room_cap'];
		                    if($total_ck > $total_peo){
		                    	$result['msg'] = "超過教室容訓量!!!";
		                    }

		                    foreach($dispersion_row as $pid){
                                $conditions = array(
                                    'year' => $post['year'],
                                    'class_no' => $post['class_no'],
                                    'term' => $key,
                                    'id' => $pid,
                                );
                                $isset_count = $this->online_app_model->getCount($conditions);

                                if($isset_count == '0'){
                                    $conditions = array(
                                        'year' => $post['year'],
                                        'class_no' => $post['class_no'],
                                        'term' => $post['term'],
                                        'id' => $pid,
                                    );
                                    $fields = array(
                                        'term' => $key,
                                        'st_no' => $max,
                                        'yn_sel' => '3',
                                    );
                                    // jd($post,1);
                                    $this->online_app_model->update($conditions, $fields);
                                    if($post['change_type']=="1"){
                                        $conditions = array(
                                            'idno' => $pid,
                                        );
                                        $person = $this->user_model->get($conditions);
                                        $bureau_id = $person['bureau_id'];
                                    }
                                    if($post['change_type']=="2"){
                                        $bureau_id = $this->flags->user['bureau_id'];
                                    }
                                    $insert_fields = array(
                                        'year' => $post['year'],
                                        'class_no' => $post['class_no'],
                                        'term' => $key,
                                        'id' => $pid,
                                        'beaurau_id' => $bureau_id,
                                        'st_no' => $max,
                                        'modify_item' => '選員',
                                        'modify_log' => '批次換期',
                                        'o_id' => $pid,
                                        'n_term' => $post['term'],
                                        'upd_user' => $this->flags->user['username'],
                                    );
                                    $this->stud_modifylog_model->insert($insert_fields, 'modify_date');
                                    $max++;
                                }
		                    	// jd($fields);
		                    	// jd($insert_fields);
		                    }
                    	}
                    }

                    $result['status'] = TRUE;

                    break;

            }
        }

        echo json_encode($result);
    }

}

<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Createclass extends AdminController
{
    public function __construct()
	{
		parent::__construct();

		//if ($this->flags->is_login === FALSE) {
		//	redirect(base_url('welcome'));
		//}

		$this->load->model('planning/createclass_model');
        $this->load->model('data/second_category_model');
        $this->load->model('planning/setclass_model');
        $this->load->model('planning/booking_place_model'); //mark 2021-06-03 更新沒有選擇教室的課程
        $this->load->model('defaultclass/defaultclass_model');

        if (!isset($this->data['filter']['page'])) {
            $this->data['filter']['page'] = '1';
        }
        if (!isset($this->data['filter']['sort'])) {
            $this->data['filter']['sort'] = '';
        }
        if (!isset($this->data['filter']['query_year'])) {
            $this->data['filter']['query_year'] = date('Y')-1911;
        }
        if (!isset($this->data['filter']['query_class_no'])) {
            $this->data['filter']['query_class_no'] = '';
        }
        if (!isset($this->data['filter']['query_class_name'])) {
            $this->data['filter']['query_class_name'] = '';
        }
        if($this->flags->user['bureau_id'] == '379680000A'){
            if (!isset($this->data['filter']['query_type'])) {
                $this->data['filter']['query_type'] = '';
            }
            if (!isset($this->data['filter']['query_second'])) {
                $this->data['filter']['query_second'] = '';
            }
            if (!isset($this->data['filter']['query_class_status'])) {
                $this->data['filter']['query_class_status'] = '';
            }
            if (!isset($this->data['filter']['query_is_cancel'])) {
                $this->data['filter']['query_is_cancel'] = '';
            }
        }
	}

	public function index()
	{
		$this->data['page_name'] = 'list';
        //$this->data['user_bureau'] = $this->flags->user['bureau_id'];
        $this->data['user_bureau'] = '379680000A';

        $this->data['choices']['query_type'] = $this->getSeriesCategory();
        $this->data['choices']['query_type'][''] = '請選擇系列別';
        $this->data['choices']['query_class_status'] = array(''=>'請選擇計劃','1'=>'草案','2'=>'確定計劃','3'=>'新增計劃');

        $conditions = array();
        //$conditions['5a_is_cancel!=']='Y';
        //$conditions['escape_query']['statusSql']="(5a_is_cancel!='Y' or 5a_is_cancel is null)";
        //$conditions['5a_is_cancel']=null;
        if($this->flags->user['bureau_id'] == '379680000A'){
            if ($this->data['filter']['query_class_status'] !== '' ) {
                $conditions['class_status'] = $this->data['filter']['query_class_status'];
            }

            if ($this->data['filter']['query_type'] !== '' ) {
                $conditions['type'] = $this->data['filter']['query_type'];
                $this->data['choices']['query_second'] = $this->createclass_model->getSecondCategory($this->data['filter']['query_type']);
            }

            if ($this->data['filter']['query_second'] !== '' ) {
                $conditions['beaurau_id'] = $this->data['filter']['query_second'];
            }

            if ($this->data['filter']['query_is_cancel'] !== '' ) {
                //$conditions['is_cancel'] = $this->data['filter']['query_is_cancel'];
                //$conditions['5a_is_cancel']='Y';
                $conditions['where_special']="(is_cancel='1'or 5a_is_cancel='Y')";
            }
        }
        if ($this->data['filter']['query_year'] !== '' ) {
            $conditions['year'] = $this->data['filter']['query_year'];
        }

        if ($this->data['filter']['query_class_no'] !== '' ) {
            $conditions['class_no'] = $this->data['filter']['query_class_no'];
        }

        $page = $this->data['filter']['page'];
        $rows = $this->data['filter']['rows'];

		$attrs = array(
            'conditions' => $conditions,
        );

        if ($this->data['filter']['query_class_name'] !== '' ) {
            $attrs['query_class_name'] = $this->data['filter']['query_class_name'];
        }

        $this->data['filter']['total'] = $total = $this->createclass_model->getListCount($attrs,$this->data['user_bureau']);
        $this->data['filter']['offset'] = $offset = ($page -1) * $rows;

        $attrs = array(
            'conditions' => $conditions,
            //'rows' => $rows,
            //'offset' => $offset,
        );
        if ($this->data['filter']['query_class_name'] !== '' ) {
            $attrs['query_class_name'] = $this->data['filter']['query_class_name'];
        }
        if ($this->data['filter']['sort'] !== '' ) {
            $attrs['sort'] = $this->data['filter']['sort'];
        }
//debugBreak();
        if($this->input->get()){
            $this->data['list'] = $this->createclass_model->getList($attrs,$this->data['user_bureau']);
            foreach ($this->data['list'] as & $row) {
                $tmp_user = substr($this->flags->user['idno'], 0, 4);
                if($tmp_user != 'EDAT' || ($tmp_user == 'EDAT' && $row['class_status'] != '2' && $row['class_status'] != '3')){
                    //$row['link_edit'] = base_url("planning/createclass/edit/{$row['seq_no']}/?{$_SERVER['QUERY_STRING']}");
                    $row['link_edit'] = base_url("Createclass/edit/{$row['seq_no']}/?{$_SERVER['QUERY_STRING']}");
                }
                
                if(empty($row['room_code']) && $row['room_remark'] == '非公訓處上課'){
                    $row['room_name'] = '非公訓處上課';
                }
            }
            //$config['total_rows'] = $total;
            //$config['per_page'] = $rows;
        } else {
            $this->data['list'] = array();
            $this->data['filter']['total'] = 0;
            
            //$config['total_rows'] = 0;
            //$config['per_page'] = $rows;
        }

		//$this->load->library('pagination');
        $config['base_url'] = base_url("planning/createclass?". $this->getQueryString(array(), array('page')));
        
        $this->pagination->initialize($config);
        $this->data['link_get_second_category'] = base_url("planning/createclass/getSecondCategory");
		$this->data['link_add'] = base_url("planning/createclass/add/?{$_SERVER['QUERY_STRING']}");
        $this->data['link_refresh'] = base_url("planning/createclass/");

		//$this->layout->view('planning/createclass/list', $this->data);
        $this->render_page('list', $this->data);
	}

    public function add()
    {
        $this->data['page_name'] = 'add';
        $this->data['user_bureau'] = $this->flags->user['bureau_id'];

        $this->data['is_edat'] = false;

        $get = $this->input->get();
        if(isset($get['year']) && !empty($get['year']) && isset($get['id']) && !empty($get['id'])){
            //$this->data['form'] = $this->createclass_model->getFormDefault($this->createclass_model->get($get['id']));
            $default_values = (array)$this->defaultclass_model->getDefault();
            $this->data['form'] = $default_values;
            $this->data['form']['year'] = $get['year'];
            $this->data['form']['room_code'] = '';
            if(isset($this->data['form']['dev_type']) && !empty($this->data['form']['dev_type'])){
                $this->data['form']['dev_type_name'] = $this->createclass_model->getDevTypeName($this->data['form']['dev_type']);
            }

            if(isset($this->data['form']['req_beaurau']) && !empty($this->data['form']['req_beaurau'])){
                $this->data['form']['req_beaurau_name'] = $this->createclass_model->getDevTypeName($this->data['form']['req_beaurau']);
            }

            if(isset($this->data['form']['ecpa_class_id']) && !empty($this->data['form']['ecpa_class_id'])){
                $this->data['form']['ecpa_class_name'] = $this->createclass_model->getEcpaClassName($this->data['form']['ecpa_class_id']);
            }

            if(isset($this->data['form']['type']) && !empty($this->data['form']['type'])){
                $this->data['beaurau_id'] = $this->createclass_model->getSecondCategory($this->data['form']['type']);
            }

            $this->data['form']['class_status'] = 1;

            $this->data['transfer'] = true;
            $this->data['link_cancel'] = base_url("planning/createclass/");
        } else {
            //$this->data['form'] = $this->createclass_model->getFormDefault();
            $default_values = (array)$this->defaultclass_model->getDefault();
            $this->data['form'] = $default_values;

            //if(preg_match("/^211.79.136.20[2,3,4,5,6]$/", $_SERVER["REMOTE_ADDR"]) || preg_match("/^163.29.35.[0-9]*[0-9]*[0-9]*$/", $_SERVER["REMOTE_ADDR"])) {
                if($this->data['is_edat']){
                    $this->data['beaurau_id'] = $this->createclass_model->getSecondCategory('A');
                }
            //}

            $this->data['form']['dev_type'] = $this->flags->user['bureau_id'];
            $this->data['form']['dev_type_name'] = $this->flags->user['bureau_name'];
            $this->data['form']['beaurau_id']  = $this->flags->user['bureau_id'];
            $this->data['link_cancel'] = base_url("planning/createclass/?{$_SERVER['QUERY_STRING']}");
        }
        //3A import to 3B
        if(isset($get['id']) && !empty($get['id'])){
            $this->data['form']['way1'] = 'Y';
        }
        
        $current_year = date('Y')-1911;
        $next_year = $current_year+1;
        $next_year2 = $current_year+2;
        
        $this->data['choices']['year'] = array($current_year => $current_year,$next_year => $next_year,$next_year2 => $next_year2);
        if($this->data['is_edat']){
            $this->data['choices']['year'] = array($next_year => $next_year);
        }

        $this->data['choices']['type'] = $this->second_category_model->getSeriesCategory();
        $this->data['choices']['type'][''] = '請選擇';
        $this->data['choices']['ht_class_type'] = $this->createclass_model->getHourlyFee();
        $this->data['choices']['classify'] = $this->createclass_model->getClassProperty();
        $this->data['choices']['class_cate'] = $this->createclass_model->getStudyWayOne();
        $this->data['choices']['class_cate1'] = $this->createclass_model->getStudyWayTwo();
        $this->data['choices']['class_cate2'] = $this->createclass_model->getStudyWayThree();
        $this->data['choices']['isappsameclass'] = array('1' => 'YES','2' => 'NO');
        $this->data['choices']['app_type'] = $this->createclass_model->getElectionWay();
        $this->data['choices']['class_status'] = array('1' => '草案','2' => '確定計畫','3' => '新增計畫');
        $this->data['choices']['reason'] = array('' => '自動偵測');
        $this->data['choices']['is_start'] = array('Y' => 'YES','N' => 'NO');
        $this->data['choices']['is_assess'] = array('1' => '是','0' => '否');
        $this->data['choices']['is_mixed'] = array('1' => '是','0' => '否');
        $this->data['choices']['map'] = array('' => '請選擇','1' => 'A營造永續環境','2' => 'B健全都市發展','3' => 'C發展多元文化','4' => 'D優化產業勞動','5' => 'E強化社會支持','6' => 'F打造優質教育','7' => 'G精進健康安全','8' => 'H精實良善治理');
        $this->data['choices']['env_class'] = array('Y' => '是','N' => '否');
        $this->data['choices']['policy_class'] = array('Y' => '是','N' => '否');
        $this->data['choices']['open_retirement'] = array('Y' => '是','N' => '否');
        // $this->data['choices']['special_status'] = array('' => '請選擇','1' => '無須支應講座鐘點費','2' => '上課地點非公訓處');

        if ($post = $this->input->post()) {
            $segmemo = $this->input->post('segmemo');
            $course_name = $this->input->post('course_name');
            $material = $this->input->post('material');

            $fmap_check = false;
            if($post['fmap'] == 'Y'){
                for($i=1;$i<=8;$i++){
                    $key = 'map'.$i;
                    if(isset($post[$key]) && $post[$key] > 0){
                        $fmap_check = true;
                        break;
                    }
                }
            }

            if(isset($post['online_course_name']) && !empty($post['online_course_name'])){
                $online_course_name = $post['online_course_name'];
            }

            if(isset($post['hours']) && !empty($post['hours'])){
                $hours = $post['hours'];
            }

            if(isset($post['elrid']) && !empty($post['elrid'])){
                $elrid = $post['elrid'];
            }

            if ($this->_isVerify('add',$this->data['user_bureau'],$fmap_check) == TRUE) {
                if($post['room_name'] == '非公訓處上課'){
                    $post['room_remark'] = '非公訓處上課';
                    $m = date('m',strtotime($post['start_date1']));
                    $m = intval($m);
                    if($m >=1 && $m <= 3){
                        $post['reason'] = '1';
                    } else if($m >=4 && $m <= 6){
                        $post['reason'] = '2';
                    } else if($m >=7 && $m <= 9){
                        $post['reason'] = '3';
                    } else if($m >=10 && $m <= 12){
                        $post['reason'] = '4';
                    }
                }

                if(!isset($post['start_date1'])&&!isset($post['end_date1'])){
                    $y=$post['year']+1911;
                    $m1='01';
                    $d1='01';
                    $m2='12';
                    $d2='31';
                    $start=$y.'-'.$m1.'-'.$d1;
                    $end=$y.'-'.$m2.'-'.$d2;
                    $post['start_date1'] = $start;
                    $post['end_date1']=$end;
                }
                
                unset($post['room_name']);
                unset($post['segmemo']);
                unset($post['course_name']);
                unset($post['material']);
                unset($post['dev_type_name']);
                unset($post['req_beaurau_name']);
                unset($post['ecpa_class_name']);
                unset($post['fmap']);
                unset($post['online_course_name']);
                unset($post['hours']);
                unset($post['elrid']);
                $term_total = $post['term'];
                for($i=1;$i<=$term_total;$i++){
                    $post['term'] = $i;
                    $saved_id = $this->createclass_model->_insert($post);
                    if($i == '1'){
                        $first_save_id = $saved_id;
                    }
                }
                
                if ($saved_id) {
                    if(!empty($segmemo)){
                        $segmemo_info['year'] = $post['year'];
                        $segmemo_info['class_no'] = $post['class_no'];
                        $segmemo_info['segmemo'] = $segmemo;

                        $check_segmemo = $this->createclass_model->getSegmemo($segmemo_info['year'],$segmemo_info['class_no']);

                        if(!empty($check_segmemo)){
                            $this->createclass_model->updateSegmemo($segmemo_info);
                        } else {
                            $this->createclass_model->insertSegmemo($segmemo_info);
                        }
                    }

                    if(isset($online_course_name) && !empty($online_course_name)){
                        $this->createclass_model->insertRequireOnline($post['year'],$post['class_no'],$post['term'],$post['is_assess'],$post['is_mixed'],$online_course_name,$hours,$elrid);
                    }

                    if(!empty($course_name) && !empty($material)){
                        for($j=1;$j<=$term_total;$j++){
                            $post['term'] = $j;
                            for($i=0;$i<count($course_name);$i++){
                                if(!isset($material[$i])){$material[$i] = 4;}
                                $this->createclass_model->insertCourse($post['year'],$post['class_no'],$post['term'],$course_name[$i],$material[$i]);
                            }
                        }
                    }

                    $this->setAlert(1, '資料新增成功');
                    if(isset($post['room_remark']) && $post['room_remark'] == '非公訓處上課'){
                        redirect(base_url("planning/createclass"));//?query_year={$segmemo_info['year']}&query_class_no={$segmemo_info['class_no']}
                    } else {
                        redirect(base_url("planning/classroom/add/{$first_save_id}"));
                    }
                    
                } else {
                    $this->setAlert(1, '資料新增失敗');
                    redirect(base_url('planning/createclass'));
                }
            }
        }

        if(isset($post['course_name']) && !empty($post['course_name'])){
            for($i=0;$i<count($post['course_name']);$i++){
                $this->data['course_name'][$i]['course_name'] = $post['course_name'][$i];
                $this->data['course_name'][$i]['material'] = $post['material'][$i];
            }
        }

        if(isset($post['type']) && !empty($post['type'])){
            $this->data['beaurau_id'] = $this->createclass_model->getSecondCategory($post['type']);
        }

        if(isset($post['beaurau_id']) && !empty($post['beaurau_id'])){
            $this->data['form']['beaurau_id'] = $post['beaurau_id'];
        }

        $this->data['link_save2'] = base_url("planning/createclass/add/?{$_SERVER['QUERY_STRING']}");
        $this->data['link_get_ecpa_name'] = base_url("planning/createclass/getEcpaClassName");
        $this->data['link_get_second_category'] = base_url("planning/createclass/getSecondCategory");
        $this->data['link_get_classno'] = base_url("planning/createclass/getClassNo");
        $this->layout->view('planning/createclass/add', $this->data);
    }

    public function edit($id=NULL)
    {
        $this->data['page_name'] = 'edit';
        $this->data['user_bureau'] = $this->flags->user['bureau_id'];
        $this->data['is_edat'] = false;

        //$this->data['form'] = $this->createclass_model->getFormDefault($this->createclass_model->get($id));
        $createValues = $this->createclass_model->getFormDefault($this->createclass_model->get($id));
        $defaultValues = (array)$this->defaultclass_model->getDefault();
        $this->data['form'] = array_merge($createValues, $defaultValues);
        //$this->data['form']['segmemo'] = $this->createclass_model->getSegmemo($this->data['form']['year'],$this->data['form']['class_no']);
        $segmemoCreate = $this->createclass_model->getSegmemo($this->data['form']['year'],$this->data['form']['class_no']);
        $this->data['form']['segmemo'] = !empty($segmemoCreate) ? $segmemoCreate : $defaultValues['segmemo'];

        if(isset($this->data['form']['dev_type']) && !empty($this->data['form']['dev_type'])){
            $this->data['form']['dev_type_name'] = $this->createclass_model->getDevTypeName($this->data['form']['dev_type']);
        }

        if(isset($this->data['form']['req_beaurau']) && !empty($this->data['form']['req_beaurau'])){
            $this->data['form']['req_beaurau_name'] = $this->createclass_model->getDevTypeName($this->data['form']['req_beaurau']);
        }

        if(isset($this->data['form']['ecpa_class_id']) && !empty($this->data['form']['ecpa_class_id'])){
            $this->data['form']['ecpa_class_name'] = $this->createclass_model->getEcpaClassName($this->data['form']['ecpa_class_id']);
        }

        if(isset($this->data['form']['room_code']) && !empty($this->data['form']['room_code'])){
            $this->data['form']['room_name'] = $this->createclass_model->getRoomName($this->data['form']['room_code']);
            //var_dump($this->data['form']['room_name']);
        }

        if(isset($this->data['form']['room_remark']) && !empty($this->data['form']['room_remark'])){
            $this->data['form']['room_name'] = '非公訓處上課';
        }
    
        $this->data['choices']['year'] = array($this->data['form']['year'] => $this->data['form']['year']);
        $this->data['choices']['type'] = $this->second_category_model->getSeriesCategory();
        $this->data['choices']['type'][''] = '請選擇';

        if(isset($this->data['form']['type']) && !empty($this->data['form']['type'])){
            $this->data['beaurau_id'] = $this->createclass_model->getSecondCategory($this->data['form']['type']);
        }

        //if($this->data['form']['is_assess'] == '1' && $this->data['form']['is_mixed'] == '1'){ //2021-06-09 取消3B.edit *考核班期*影響*混成班級*的設定
        if($this->data['form']['is_mixed'] == '1'){   
            $this->data['form']['online_course'] = $this->createclass_model->getRequireOnline($this->data['form']['year'],$this->data['form']['class_no'],$this->data['form']['term']);
        }

        $this->data['course_name'] = $this->createclass_model->getCourse($this->data['form']['year'],$this->data['form']['class_no'],$this->data['form']['term']);
        $this->data['choices']['ht_class_type'] = $this->createclass_model->getHourlyFee();
        $this->data['choices']['classify'] = $this->createclass_model->getClassProperty();
        $this->data['choices']['class_cate'] = $this->createclass_model->getStudyWayOne();
        $this->data['choices']['class_cate1'] = $this->createclass_model->getStudyWayTwo();
        $this->data['choices']['class_cate2'] = $this->createclass_model->getStudyWayThree();
        $this->data['choices']['isappsameclass'] = array('1' => 'YES','2' => 'NO');
        $this->data['choices']['app_type'] = $this->createclass_model->getElectionWay();
        $this->data['choices']['class_status'] = array('1' => '草案','2' => '確定計畫','3' => '新增計畫');
        $this->data['choices']['reason'] = array('' => '自動偵測','1' => '1','2' => '2','3' => '3','4' => '4');
        $this->data['choices']['is_start'] = array('Y' => 'YES','N' => 'NO');
        $this->data['choices']['is_assess'] = array('1' => '是','0' => '否');
        $this->data['choices']['is_mixed'] = array('1' => '是','0' => '否');
        $this->data['choices']['map'] = array('' => '請選擇','1' => 'A營造永續環境','2' => 'B健全都市發展','3' => 'C發展多元文化','4' => 'D優化產業勞動','5' => 'E強化社會支持','6' => 'F打造優質教育','7' => 'G精進健康安全','8' => 'H精實良善治理');
        $this->data['choices']['env_class'] = array('Y' => '是','N' => '否');
        $this->data['choices']['policy_class'] = array('Y' => '是','N' => '否');
        $this->data['choices']['open_retirement'] = array('Y' => '是','N' => '否');
        // $this->data['choices']['special_status'] = array('' => '請選擇','1' => '無須支應講座鐘點費','2' => '上課地點非公訓處');

        if ($post = $this->input->post()) {
            $segmemo = $this->input->post('segmemo');
            $course_name = $this->input->post('course_name');
            $material = $this->input->post('material');

            $fmap_check = false;
            if($post['fmap'] == 'Y'){
                for($i=1;$i<=8;$i++){
                    $key = 'map'.$i;
                    if(isset($post[$key]) && $post[$key] > 0){
                        $fmap_check = true;
                        break;
                    }
                }
            }

            if(isset($post['online_course_name']) && !empty($post['online_course_name'])){
                $online_course_name = $post['online_course_name'];
            }

            if(isset($post['hours']) && !empty($post['hours'])){
                $hours = $post['hours'];
            }

            if(isset($post['elrid']) && !empty($post['elrid'])){
                $elrid = $post['elrid'];
            }

            if ($this->_isVerify('edit',$this->data['user_bureau'],$fmap_check) == TRUE) {
                if($post['room_name'] == '非公訓處上課'){
                    $post['room_remark'] = '非公訓處上課';
                    $m = date('m',strtotime($post['start_date1']));
                    $m = intval($m);
                    // print_r($pos['start_date1']);
                    // die($m);
                    if($m >=1 && $m <= 3){
                        $post['reason'] = '1';
                    } else if($m >=4 && $m <= 6){
                        $post['reason'] = '2';
                    } else if($m >=7 && $m <= 9){
                        $post['reason'] = '3';
                    } else if($m >=10 && $m <= 12){
                        $post['reason'] = '4';
                    }
                }

                unset($post['room_name']);
                unset($post['segmemo']);
                unset($post['course_name']);
                unset($post['material']);
                unset($post['dev_type_name']);
                unset($post['req_beaurau_name']);
                unset($post['ecpa_class_name']);
                unset($post['fmap']);
                unset($post['online_course_name']);
                unset($post['hours']);
                unset($post['elrid']);
                
                $rs = $this->createclass_model->updateRequire($id, $post);
                if ($rs) {
                    if(!empty($segmemo)){
                        $segmemo_info['year'] = $post['year'];
                        $segmemo_info['class_no'] = $post['class_no'];
                        $segmemo_info['segmemo'] = $segmemo;

                        $check_segmemo = $this->createclass_model->getSegmemo($segmemo_info['year'],$segmemo_info['class_no']);

                        if(!empty($check_segmemo)){
                            $this->createclass_model->updateSegmemo($segmemo_info);
                        } else {
                            $this->createclass_model->insertSegmemo($segmemo_info);
                        }
                    }

                    //if(isset($online_course_name) && !empty($online_course_name)){ //2021-06-09 取消3B.edit *考核班期*影響*混成班級*的設定
                        $this->createclass_model->insertRequireOnline($post['year'],$post['class_no'],$post['term'],$post['is_assess'],$post['is_mixed'],$online_course_name,$hours,$elrid);
                    //}

                    if(!empty($course_name) && !empty($material)){
                        $this->createclass_model->deleteCourse($post['year'],$post['class_no'],$post['term']);
                        for($i=0;$i<count($course_name);$i++){
                            if(!isset($material[$i])){$material[$i] = 4;}
                            $this->createclass_model->insertCourse($post['year'],$post['class_no'],$post['term'],$course_name[$i],$material[$i]);
                        }
                    }

                    $this->setAlert(2, '資料編輯成功');
                }
                redirect(base_url("planning/createclass/?{$_SERVER['QUERY_STRING']}"));
            }
        }

        if(isset($post['type']) && !empty($post['type'])){
            $this->data['beaurau_id'] = $this->createclass_model->getSecondCategory($post['type']);
        }

        if(isset($post['beaurau_id']) && !empty($post['beaurau_id'])){
            $this->data['form']['beaurau_id'] = $post['beaurau_id'];
        }

        //$this->data['link_save2'] = base_url("planning/createclass/edit/{$id}/?{$_SERVER['QUERY_STRING']}");
        $this->data['link_save2'] = base_url("Createclass/edit/{$id}/?{$_SERVER['QUERY_STRING']}");
        $this->data['link_cancel'] = base_url("planning/createclass/?{$_SERVER['QUERY_STRING']}");
        $this->data['link_get_ecpa_name'] = base_url("planning/createclass/getEcpaClassName");
        $this->data['link_get_second_category'] = base_url("planning/createclass/getSecondCategory");
        $this->data['link_get_room'] = base_url("planning/createclass/getRoom");
        $this->data['link_update_require'] = base_url("planning/createclass/update_require_RoomCode_and_time"); //mark 2021-06-04

        if($_SESSION['username'] == 'A226193585' OR $_SESSION['username'] == '3009006' OR $_SESSION['username'] == 'admin' OR $_SESSION['username'] == 'F227164127'){   //mark 2021-06-04
            $this->data['unlock_start_date1'] = 'true';
            $this->data['unlock_end_day1'] = 'true';          
        }//mark 2021-06-04 加入unlock條件username

        if($this->data['user_bureau'] != '379680000A'){
            $this->data['link_printApplication'] = base_url("planning/createclass/printApplication/{$id}");
        }
        //$this->layout->view('planning/createclass/edit', $this->data);

        //$this->load_css(array(
        //    'assets/plugins/datatables-responsive/css/responsive.bootstrap4.min.css',
        //    'assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css'
		//));
        //$this->load_js(array(
        //    //'assets/plugins/datatables/jquery.dataTables.min.js',
        //    //'assets/plugins/datatables-responsive/js/dataTables.responsive.min.js',
        //    //'assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js',
        //    //'assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js'
        //));
        $this->render_page('edit', $this->data);
    }

    public function getRoom()
    {
        $seq_no = $this->input->post('seq_no');
        $data = $this->createclass_model->getRoom($seq_no);

        if(isset($data[0]['start_date1']) && !empty($data[0]['start_date1'])){
            $data[0]['start_date1'] = date('Y-m-d',strtotime($data[0]['start_date1']));
        }

        if(isset($data[0]['end_date1']) && !empty($data[0]['end_date1'])){
            $data[0]['end_date1'] = date('Y-m-d',strtotime($data[0]['end_date1']));
        }

        print_r(json_encode($data));
    }

    private function _isVerify($action='add', $user_bureau, $fmap_check)
    {
        $config = $this->createclass_model->getVerifyConfig();
        
        if($user_bureau != '379680000A'){
            $config['type']['rules'] = '';
            $config['class_name']['rules'] = '';
            $config['ht_class_type']['rules'] = '';
            $config['is_assess']['rules'] = '';
            $config['open_retirement']['rules'] = '';
        }
        $this->form_validation->set_rules($config);
        $this->form_validation->set_error_delimiters('<p class="text-danger">', '</p>');

        return ($this->form_validation->run() == FALSE)? FALSE : TRUE;
    }

}
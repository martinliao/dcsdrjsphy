<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Lecture_notes_assignments extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        
        if ($this->flags->is_login === FALSE) {
            redirect(base_url('welcome'));
        }

        $this->load->model('management/lecture_notes_assignments_model');
        $this->load->model('management/upload_file_model');
        $this->load->model('management/handouts_status_model');

        $this->data['choices']['year'] = $this->_get_year_list();
        $this->data['choices']['year']['109'] = 109;
        krsort($this->data['choices']['year']);
        $this->data['choices']['argSeason'] = array(
            '' => '請選擇季別',
            '1' => '第1季',
            '2' => '第2季',
            '3' => '第3季',
            '4' => '第4季',
        );
        $this->data['choices']['period'] = array(
            '-1' => '請選擇',
            '1' => '第1期',
            '2' => '第2期',
            '3' => '第3期',
            '4' => '第4期',
            '5' => '第5期',
            '6' => '第6期',
            '7' => '第7期',
            '8' => '第8期',
            '9' => '第9期',
            '10' => '第10期',
            '11' => '第11期',
            '12' => '第12期',
        );

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
        if (!isset($this->data['filter']['argSeason'])) {
            $this->data['filter']['argSeason'] = '';
        }
        if (!isset($this->data['filter']['query_teacher'])) {
            $this->data['filter']['query_teacher'] = '';
        }
        if (!isset($this->data['filter']['query_course_name'])) {
            $this->data['filter']['query_course_name'] = '';
        }
        if (!isset($this->data['filter']['query_file_title'])) {
            $this->data['filter']['query_file_title'] = '';
        }
        if (!isset($this->data['filter']['allQuery'])) {
            $this->data['filter']['allQuery'] = '';
        }
        if (!isset($this->data['filter']['allClassesQuery'])) {
            $this->data['filter']['allClassesQuery'] = '';
        }

    }

    public function index()
    {
        $this->data['page_name'] = 'list';
        $page = $this->data['filter']['page'];
        $rows = $this->data['filter']['rows'];

        $conditions = array();
        $conditions['require.year'] = $this->data['filter']['year'];
        if ($this->data['filter']['argSeason'] != '') {
            $conditions['require.reason'] = $this->data['filter']['argSeason'];
        }

        $attrs = array(
            'conditions' => $conditions,
        );
        $this->data['filter']['query_search_from'] = (empty($this->data['filter']['query_search_from'])) ? "" : $this->data['filter']['query_search_from'];
        $attrs['where_special'] = '';
        if ($this->data['filter']['class_name'] !== '' ) {
            $attrs['class_name'] = $this->data['filter']['class_name'];
        }
        if ($this->data['filter']['class_no'] != '' ) {
            $attrs['class_no'] = $this->data['filter']['class_no'];
        }
        if ($this->data['filter']['query_teacher'] != '') {
            $attrs['query_teacher'] = $this->data['filter']['query_teacher'];
        }
        if ($this->data['filter']['query_course_name'] != '') {
            $attrs['query_course_name'] = $this->data['filter']['query_course_name'];
        }
        if(('allClassesQuery' != $this->data['filter']['allClassesQuery'])){
            $attrs['where_special'] = " t.name not in ('教務組', '綜企組', '總務組') ";
        }
        if(('allQuery' != $this->data['filter']['allQuery'])&&('allClassesQuery' != $this->data['filter']['allClassesQuery'])){
            if(isset($attrs['where_special'])){
                $attrs['where_special'] .= " and worker = '{$this->flags->user['idno']}' ";
            }else{
                $attrs['where_special'] = " worker = '{$this->flags->user['idno']}' ";
            }
        }

        if ($this->data['filter']['query_file_title'] != '') {
            $sub_conditions = array(
                'year' => $this->data['filter']['year'],
                'title' => $this->data['filter']['query_file_title'],
            );
            $query_sub = $this->lecture_notes_assignments_model->getSub($sub_conditions);
            // $attrs['where_special'] .= $query_sub;
            if(isset($attrs['where_special'])){
                $attrs['where_special'] .= $query_sub;
            }else{
                $attrs['where_special'] = $query_sub;
            }
        }
        if(isset($attrs['where_special']) && !empty($attrs['where_special']) ){
            $attrs['where_special'] .= " and c.course_code is not null and  IFNULL(require.is_cancel, '0') = '0'";
            // jd($attrs);
            // jd($attrs['where_special'],1);
        }else{
            $attrs['where_special'] = "c.course_code is not null and  IFNULL(require.is_cancel, '0') = '0'";
        }
        $this->data['filter']['total'] = $total = $this->lecture_notes_assignments_model->getListCount($attrs);
        $this->data['filter']['offset'] = $offset = ($page -1) * $rows;

        $attrs['rows'] = $rows;
        $attrs['offset'] = $offset;

        $this->data['list'] = $this->lecture_notes_assignments_model->getList($attrs);

        $this->load->library('pagination');
        $config['base_url'] = base_url("management/lecture_notes_assignments?". $this->getQueryString(array(), array('page')));
        $config['total_rows'] = $total;
        $config['per_page'] = $rows;
        
        $this->data['filter']['detail_link'] = base_url("management/lecture_notes_assignments/detail?". $this->getQueryString(array(), array('page')));

        if ($this->data['filter']['query_search_from'] == "2B"){
            /*
             舊系統 講座基本資料及可授課程查詢 的講義功能
             已壞掉查不到東西所以直接設為空
            */
            $this->data['list'] = [];
            $config = [];
        }

        $this->pagination->initialize($config);

        $this->data['exportcsv'] = base_url("management/lecture_notes_assignments/exportcsv");
        $this->data['dl_consent'] = base_url("management/lecture_notes_assignments/dl_consent");
        $this->data['detail'] = base_url("management/lecture_notes_assignments/detail?{$_SERVER['QUERY_STRING']}");
        $this->data['link_refresh'] = base_url("management/lecture_notes_assignments/");
        $this->layout->view('management/lecture_notes_assignments/list',$this->data);
    }

    public function detail()
    {
    	$this->data['btn_disabled']="disabled";

        if(in_array("1", $this->flags->user['group_id'])){
            $this->data['btn_disabled']='';
        }

        $post = $this->input->post();
        if(isset($post['year'])){
          $this->data['detail_data']['year'] = intval($post['year']);
        }
        if(isset($post['class_no'])){
          $this->data['detail_data']['class_no'] = addslashes($post['class_no']);
        }
        if(isset($post['class_name'])){
          $this->data['detail_data']['class_name'] = addslashes($post['class_name']);
        }
        if(isset($post['course_code'])){
          $this->data['detail_data']['course_code'] = addslashes($post['course_code']);
        }
        if(isset($post['id'])){
          $this->data['detail_data']['id'] = addslashes($post['id']);
        }
        $page = $this->data['filter']['page'];
        $rows = $this->data['filter']['rows'];

        if(in_array("8", $this->flags->user['group_id'])){
            $conditions = array(
                'worker' => $this->flags->user['idno'],
                'year' => addslashes($post['year']),
                'class_no' => addslashes($post['class_no']),
            );
            $get_worker = $this->lecture_notes_assignments_model->getCount();
            if($get_worker > 0){
                $this->data['btn_disabled']='';
            }
        }

        $conditions = array();
        $conditions['upload_file.year'] = $this->data['detail_data']['year'];
        $conditions['upload_file.class_no'] = $this->data['detail_data']['class_no'];


        $attrs = array(
            'conditions' => $conditions,
        );
        $attrs['where_special'] = '';
        $attrs['where_special'] = ($this->data['detail_data']['course_code'] == "" ? "  upload_file.course_code is null " : "  upload_file.course_code='{$this->data['detail_data']['course_code']}' ");
        $attrs['where_special'] .= ($this->data['detail_data']['id'] == "" ? " and upload_file.id is null " : " and upload_file.id='{$this->data['detail_data']['id']}' ");
        $this->data['filter']['total'] = $total = $this->upload_file_model->getListCount($attrs);
        $this->data['filter']['offset'] = $offset = ($page -1) * $rows;

        $attrs['rows'] = $rows;
        $attrs['offset'] = $offset;

        $this->data['list'] = $this->upload_file_model->getList($attrs);
        // jd($attrs);
        // jd($this->data['list']);
        $conditions =array(
            'item_id' => $this->data['detail_data']['course_code'],
        );
        $this->data['detail_data']['course_name'] = $this->lecture_notes_assignments_model->get_course_name($conditions);
        $conditions =array(
            'idno' => $this->data['detail_data']['id'],
        );
        $this->data['detail_data']['teacher_name'] = $this->lecture_notes_assignments_model->get_teacher_name($conditions);

        $this->load->library('pagination');
        $config['base_url'] = base_url("management/lecture_notes_assignments/detail?". $this->getQueryString(array(), array('page')));
        $config['total_rows'] = $total;
        $config['per_page'] = $rows;
        $this->pagination->initialize($config);

        $this->layout->view('management/lecture_notes_assignments/detail',$this->data);
    }

    public function add()
    {
        $this->data['page_name'] = 'add';
        $post = $this->input->post();
        if(isset($post['year'])){
            $this->data['detail_data']['year'] = intval($post['year']);
            $updateHandoutsStatus['year']= intval($post['year']);
        }
        if(isset($post['class_no'])){
            $this->data['detail_data']['class_no'] = addslashes($post['class_no']);
            $updateHandoutsStatus['class_no']= addslashes($post['class_no']);
        }
        if(isset($post['class_name'])){
          $this->data['detail_data']['class_name'] = addslashes($post['class_name']);
        }
        if(isset($post['course_code'])){
            $this->data['detail_data']['course_code'] = addslashes($post['course_code']);
            $updateHandoutsStatus['course_code']= addslashes($post['course_code']);
        }
        if(isset($post['id'])){
            $this->data['detail_data']['id'] = addslashes($post['id']);
            $updateHandoutsStatus['teacher_id']= addslashes($post['id']);
        }
        $this->data['set_to_terms'] = '';
        $conditions = array(
            'year' => intval($post['year']),
            'class_no' => addslashes($post['class_no']),
        );
        $this->data['choices']['term'] = $this->lecture_notes_assignments_model->get_term($conditions);

        //var_dump($updateHandoutsStatus);
        //die();

        $this->data['form'] = array(
            'title' => '',
            'term' => '',
            'start_date' => '',
            'end_date' => '',
            'is_open' => 'N',
            'open_to_all' => 'N',
            'file_path' => '',
            'approve' => '0',
        );

        if(isset($post['title'])){
            $file_part  = pathinfo($_FILES['userfile']['name']);
            $extendname = $file_part["extension"];
            if (( $_FILES["userfile"]["name"] <> "" ) && ($extendname != "php")){
            	// $path = trim("upload/".intval($post['year']).intval($post['term']).$post['class_no'].$post['course_code'].$post['id']);
                $path = trim(intval($post['year']).intval($post['term']).$post['class_no'].$post['course_code'].$post['id']);
                $target_path = DIR_MEDIA."upload/".basename($path);

                $total_size=$_FILES["userfile"]["size"];

                if($total_size <= 64000000){ //64MB
                    if (!fileExtensionCheck($_FILES['userfile']['name'], ['odt', 'ods', 'odp', 'docx', 'xlsx', 'pptx', 'doc', 'xls', 'ppt', 'zip', 'rar', 'jpg', 'png', 'gif', 'pdf'])){
                        $this->setAlert(3, "不允許的檔案格式");
                        redirect(base_url("management/lecture_notes_assignments"));
                    } 
                	$conditions = array(
		        		'file_path' => "upload/".$path."/". $_FILES['userfile']['name'],
		        	);
		        	$download_data = $this->upload_file_model->get($conditions);
		        	//同路徑同檔案不能新增
                	if(empty($download_data)){
                		if(!is_dir($target_path)){
		                    // $old=umask(0);
		                    // exec("mkdir -pm 777 '".$target_path."'");
		                    // umask($old);
                            $this->TMkdir($target_path,0777);

		                }

                        $filter_file_name = preg_replace('/^(.+[\\/])|(\\/)/', '', $_FILES['userfile']['name']);
		                $target_path = $target_path ."/". $filter_file_name;
                        // jd($target_path,1);
                        //var_dump($updateHandoutsStatus);
                        //die();
                        $this->lecture_notes_assignments_model->updateHandoutsStatus($updateHandoutsStatus);

		                if(!move_uploaded_file($_FILES["userfile"]["tmp_name"],  $target_path)){
                            $this->setAlert(3, '上傳失敗!');
                            redirect(base_url("management/lecture_notes_assignments"));
                        }
                	}else{
                		$this->setAlert(3, '講義名稱不可重複!');
                        redirect(base_url("management/lecture_notes_assignments"));
                	}

                } else {
                    $this->setAlert(3, '檔案大小超過限制(64MB)，請先分割!');
                    redirect(base_url("management/lecture_notes_assignments"));
                }
            }

            $insert_date = new DateTime('now');
            $insert_date = $insert_date->format('Y-m-d H:i:s');
            $fields = array(
            	'year' => intval($post['year']),
            	'class_no' => addslashes($post['class_no']),
            	'term' => addslashes($post['term']),
            	'start_date' => addslashes($post['start_date']),
            	'end_date' => addslashes($post['end_date']),
            	'title' => addslashes($post['title']),
            	'file_path' => "upload/".$path."/". $_FILES['userfile']['name'],
            	'is_open' => addslashes($post['is_open']),
            	'cre_user' => $this->flags->user['username'],
            	'cre_date' => $insert_date,
            	'course_code' => addslashes($post['course_code']),
            	'id' => addslashes($post['id']),
            	'open_to_all' => addslashes($post['open_to_all']),
            	'set_to_terms' => addslashes($post['all_set2Term']),
            	'is_authorize' => addslashes($post['approve']),
            );
            if(empty($post['course_code'])){
                unset($fields['course_code']);
            }
            if(empty($post['id'])){
                unset($fields['id']);
            }
            // jd($fields,1);
            $this->upload_file_model->insert($fields);
            $this->setAlert(1, '新增成功!');
            //echo"<script>history.go(-3);</script>";
            //header("Refresh:0");

            redirect("management/lecture_notes_assignments/?{$_SERVER['QUERY_STRING']}","refresh");
            //redirect(base_url("management/lecture_notes_assignments/detail")); //**
        }


        $this->data['link_save_file'] = base_url("management/lecture_notes_assignments/add?{$_SERVER['QUERY_STRING']}".$this->getQueryString());
        $this->data['back_to_detail'] = base_url("management/lecture_notes_assignments/detail");
        $this->layout->view('management/lecture_notes_assignments/add',$this->data);
    }

    public function edit()
    {
    	$this->data['page_name'] = 'edit';
        $post = $this->input->post();
        if(isset($post['year'])){
          $this->data['detail_data']['year'] = intval($post['year']);
        }
        if(isset($post['class_no'])){
          $this->data['detail_data']['class_no'] = addslashes($post['class_no']);
        }
        if(isset($post['class_name'])){
          $this->data['detail_data']['class_name'] = addslashes($post['class_name']);
        }
        if(isset($post['course_code'])){
          $this->data['detail_data']['course_code'] = addslashes($post['course_code']);
        }
        if(isset($post['id'])){
          $this->data['detail_data']['id'] = addslashes($post['id']);
        }
        $this->data['set_to_terms'] = '';
        $conditions = array(
            'year' => intval($post['year']),
            'class_no' => addslashes($post['class_no']),
        );
        $this->data['choices']['term'] = $this->lecture_notes_assignments_model->get_term($conditions);

        $conditions = array(
    		'file_path' => addslashes($post['path']),
    	);

        $this->data['form'] = $this->upload_file_model->get($conditions);

        $this->data['form']['approve'] = $this->data['form']['is_authorize'];
        $this->data['set_to_terms'] = $this->data['form']['set_to_terms'];

        if(isset($post['title'])){
        	// jd($post,1);
            $insert_date = new DateTime('now');
            $insert_date = $insert_date->format('Y-m-d H:i:s');
            $fields = array(
            	'start_date' => addslashes($post['start_date']),
            	'end_date' => addslashes($post['end_date']),
            	'is_open' => addslashes($post['is_open']),
            	'upd_user' => $this->flags->user['username'],
            	'upd_date' => $insert_date,
            	'open_to_all' => addslashes($post['open_to_all']),
            	'set_to_terms' => addslashes($post['all_set2Term']),
            	'is_authorize' => addslashes($post['approve']),
            );
            $conditions = array(
	    		'file_path' => addslashes($post['path']),
	    	);
            $this->upload_file_model->update($conditions, $fields);
            $this->setAlert(1, '儲存成功!');
            redirect(base_url("management/lecture_notes_assignments"));
        }


        $this->data['link_save_file'] = base_url("management/lecture_notes_assignments/edit?".$this->getQueryString());
        $this->data['back_to_detail'] = base_url("management/lecture_notes_assignments/detail");
        $this->layout->view('management/lecture_notes_assignments/edit',$this->data);
    }

    public function exportcsv()
    {
        $conditions = array();
        $conditions['require.year'] = $this->data['filter']['year'];
        if ($this->data['filter']['argSeason'] != '') {
            $conditions['require.reason'] = $this->data['filter']['argSeason'];
        }

        $attrs = array(
            'conditions' => $conditions,
        );
        if ($this->data['filter']['class_name'] !== '' ) {
            $attrs['class_name'] = $this->data['filter']['class_name'];
        }
        if ($this->data['filter']['class_no'] != '' ) {
            $attrs['class_no'] = $this->data['filter']['class_no'];
        }
        if ($this->data['filter']['query_teacher'] != '') {
            $attrs['query_teacher'] = $this->data['filter']['query_teacher'];
        }
        if ($this->data['filter']['query_course_name'] != '') {
            $attrs['query_course_name'] = $this->data['filter']['query_course_name'];
        }
        if(('allClassesQuery' != $this->data['filter']['allClassesQuery'])){
            $attrs['where_special'] = " t.name not in ('教務組', '綜企組', '總務組') ";
        }
        if(('allQuery' != $this->data['filter']['allQuery'])&&('allClassesQuery' != $this->data['filter']['allClassesQuery'])){
            if(isset($attrs['where_special'])){
                $attrs['where_special'] .= " and worker = '{$this->flags->user['idno']}' ";
            }else{
                $attrs['where_special'] = " worker = '{$this->flags->user['idno']}' ";
            }
        }

        if ($this->data['filter']['query_file_title'] != '') {
            $sub_conditions = array(
                'year' => $this->data['filter']['year'],
                'title' => $this->data['filter']['query_file_title'],
            );
            $query_sub = $this->lecture_notes_assignments_model->getSub($sub_conditions);
            $attrs['where_special'] .= $query_sub;
            if(isset($attrs['where_special'])){
                $attrs['where_special'] .= $query_sub;
            }else{
                $attrs['where_special'] = $query_sub;
            }
        }

        $this->data['list'] = $this->lecture_notes_assignments_model->getList($attrs);

        header("Content-type: application/csv");
        header("Content-Disposition: attachment; filename=Lecture.csv");
        header("Pragma: no-cache");
        header("Expires: 0");

        echo iconv("UTF-8","big5","年度,");
        echo iconv("UTF-8","big5","班期名稱,");
        echo iconv("UTF-8","big5","課程名稱,");
        echo iconv("UTF-8","big5","講師,");
        echo iconv("UTF-8","big5","講義名稱,");
        echo iconv("UTF-8","big5","實體檔案下載,");
        echo iconv("UTF-8","big5","上傳時間");
        echo "\r\n";
        foreach($this->data['list'] as $row){
            echo iconv("UTF-8","big5",$row["year"].",");
            echo iconv("UTF-8","big5",$row["class_name"].",");
            echo iconv("UTF-8","big5",$row["c_name"].",");
            echo iconv("UTF-8","big5",$row["name"].",");
            echo iconv("UTF-8","big5",$row["file_name"].",");
            $row["file"] = pathinfo($row["file"],PATHINFO_BASENAME);
            echo iconv("UTF-8","big5",$row["file"].",");
            echo iconv("UTF-8","big5",$row["cre_time_stamp"]."");
            echo "\r\n";
        }

    }

    public function dl_consent()
    {
    //===========================8E列印講座著作權授權使用同意書用================================//
    // Year, Term, ClassId, ClassName, Lesson
        $gt_Year = intval($_GET["v1"]);
        $gt_Term = intval($_GET["v2"]);
        // $gt_ClassId = addslashes($_GET["v3"]);
        $gt_ClassName = addslashes($_GET["v3"]);
        $gt_Lesson = addslashes($_GET["v4"]);

        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Content-type: application/vnd.ms-word");
        header("Content-Disposition: attachment; Filename=consent.doc");

        echo "<html style='font-family:標楷體;'>";
        echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=big-5\">";
        echo "<body>";

        if($this->flags->user['idno'] == 'T222291880') {
            echo "<div style='font-size:23px'><center>臺北市政府公務人員訓練處<br>講座教材著作授權使用同意書<br><br></center></div>";
        }else{
            echo "<div style='font-size:34px'><center>臺北市政府公務人員訓練處<br>講座教材著作授權使用同意書<br><br></center></div>";
        }
        if($this->flags->user['idno'] == 'T222291880') {
            echo "<div style='font-size:21.5px'>";            
        }else{
            echo "<div style='font-size:32px'>"; 
        }

        if($this->flags->user['idno'] == 'T222291880') {
            echo sprintf("本人擔任<font style='text-decoration:underline'>%s年度%s第%s期 %s</font>課程講座，所提供之教材著作同意供貴處無償使用，範圍如下：(以下得複式勾選、授權範圍隨項次逐項擴大)。<br><br>", $gt_Year, htmlspecialchars($gt_ClassName,ENT_HTML5|ENT_QUOTES), $gt_Term, htmlspecialchars($gt_Lesson,ENT_HTML5|ENT_QUOTES));
        }else{
            echo sprintf("本人擔任<font style='text-decoration:underline'>%s年度%s第%s期 %s</font>課程講座，所提供之教材著作同意供貴處無償使用，範圍如下：(以下請勾選)。<br><br>", $gt_Year, htmlspecialchars($gt_ClassName,ENT_HTML5|ENT_QUOTES), $gt_Term, htmlspecialchars($gt_Lesson,ENT_HTML5|ENT_QUOTES));
        }

        echo "□1、同意將數位教材掛置於臺北ｅ大－實體班期作業專區，供本人授課班期之學員下載及列印參考。(掛置時間至結訓日後一周)。<br><br>□2、同意未來相同課程之教材同第1項說明。<br><br>";


        if($this->flags->user['idno'] == 'T222291880') {
            echo "□3、同意供貴處班期講座參考之用，但除提供予講座學員參考及基於廣宣部份截錄公示之外，不得修改、重製及做商業使用。<br><br>";
            echo "□4、同意提供議員問政之用，但除問政基礎之公益目的外，不得修改、重製及做商業使用。<br><br>";
            echo "□5、採本人名義以「CC授權 姓名標示-非商業性-禁止改作 3.0 台灣版本」提供，後續貴處得以本人名義再行發布，然貴處及收受著作之人，皆必須於標註本人姓名、不得修改、不得商業使用的前提下方得利用該著作。<br><br>";
        }

        echo "&nbsp;&nbsp;此致<br>";
        echo "臺北市政府公務人員訓練處<br><br>";
        echo "<div style='text-align:right'>授權人：　　　　　　　　(簽章)</div><br>";
        echo sprintf("<center>中華民國 %s年 %s月 %s日</div></center>", date("Y")-1911, date("m"), date("d"));
        echo "</body>";
        echo "</html>";
    }

    public function download()
    {
        $this->data['page_name'] = 'download';

        if($post = $this->input->post()){
        	$conditions = array(
        		'file_path' => addslashes($post['path']),
        	);
        	$download_data = $this->upload_file_model->get($conditions);

			if(empty($download_data)){
                $this->setAlert(3, '操作錯誤');
                redirect(base_url("management/lecture_notes_assignments"));
            } else {
                //$path = HTTP_MEDIA.$download_data['file_path'] ;
                //$path = '/var/www/resource/'.$download_data['file_path']; 
                $path = DIR_MEDIA.$download_data['file_path'] ;
                
			    if(!is_file($path)||!is_readable($path)){
			        die("檔案無法讀取");
                }

                $file_name = preg_replace('/^.+[\\\\\\/]/', '', $download_data['file_path']) ;
                //$file_name = iconv("utf-8", "big5", $file_name);
                //$path = iconv("utf-8", "big5", $path);
                header("Content-Type: application/octet-stream");
                header("Content-Disposition: attachment; filename={$file_name}");
                header("Content-Transfer-Encoding:binary");
                header("Expires:0");
                header("Content-Length:"+filesize($path));
                ob_end_clean();
                readfile($path);
                //file_put_contents($file_name,fopen($path,'r'));

                exit;
	        }
        }

    }

    public function delete()
    {
        if($post = $this->input->post()){
        	$conditions = array(
        		'file_path' => addslashes($post['path']),
        	);
        	$download_data = $this->upload_file_model->get($conditions);

			if(empty($download_data)){
	            $this->setAlert(3, '操作錯誤');
	            redirect(base_url("management/lecture_notes_assignments"));
	        } else {
	        	$conditions = array(
	        		'file_path' => addslashes($post['path']),
	        	);
	        	$this->upload_file_model->delete($conditions);
	        	$path = DIR_MEDIA.$download_data['file_path'];
	        	// $path = iconv("utf-8", "big5", $path);
	        	if (file_exists($path)) {
		            unlink($path);
		        }
	        	$this->setAlert(1, '刪除成功!');
                echo"<script>history.go(-2);</script>";
	         //   redirect(base_url("management/lecture_notes_assignments")); //**
	        }
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

                case 'enableFun':

                    $error = FALSE;

                    if(empty($post['year'])){
                        $error = TRUE;
                    }
                    if(empty($post['class_no'])){
                        $error = TRUE;
                    }

                    if($error === TRUE){
                        $result['msg'] = '操作錯誤';
                    }else{
                        $conditions = array(
                            'year' => intval($post['year']),
                            'class_no' => addslashes($post['class_no']),
                            'course_code' => addslashes($post['id']),
                            'teacher_id' => addslashes($post['teacher_id']),
                        );
                        $cnt = $this->handouts_status_model->getCount($conditions);

                        $fields = array(
                            'course_code' => addslashes($post['id']),
                            'year' => intval($post['year']),
                            'class_no' => addslashes($post['class_no']),
                            'teacher_id' => addslashes($post['teacher_id']),
                            'status' => addslashes($post['status']),
                        );

                        if($cnt > 0){
                            $rs = $this->handouts_status_model->update($conditions, $fields);
                            $result['status'] = TRUE;
                        }else{
                            $this->handouts_status_model->insert($fields);
                            $result['status'] = TRUE;
                        }

                    }

                    break;

            }
        }

        echo json_encode($result);
    }

    private function TMkdir($pathname,$mode)
    {
        if (is_dir($pathname))
            return false;
    
        do
        {
            if (!($tFlag=@mkdir($pathname,$mode)))
            {
                $path_d=substr($pathname,0,strrpos($pathname,"/"));
                $old = umask(0);
                TMkdir($path_d,$mode);
                umask($old);
            }
        }
        while (!$tFlag);
    
        return true;
    }
}

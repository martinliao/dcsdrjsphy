<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Point_create extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        if ($this->flags->is_login === FALSE) {
            redirect(base_url('welcome'));
        }
        $this->load->model('management/point_create_model');
        $this->load->model('management/require_grade_model');
        $this->load->model('management/online_app_model');
        $this->load->model('management/online_app_score_model');
        $this->load->model('data/grade_category_model');

        $this->data['choices']['queryType'] = array(
            '' => '全部',
            'Y' => '已有設定類別',
            'N' => '尚未設定類別',
        );

        $date_now = new DateTime('now');
        $year_now = $date_now->format('Y');
        $this_yesr = $year_now - 1911;

        if (!isset($this->data['filter']['page'])) {
            $this->data['filter']['page'] = '1';
        }

        if (!isset($this->data['filter']['year'])) {
            $this->data['filter']['year'] = $this_yesr;
        }
        if (!isset($this->data['filter']['queryType'])) {
            $this->data['filter']['queryType'] = '';
        }
        if (!isset($this->data['filter']['class_no'])) {
            $this->data['filter']['class_no'] = '';
        }
        if (!isset($this->data['filter']['class_name'])) {
            $this->data['filter']['class_name'] = '';
        }
    }

    public function index()
    {
        $this->data['page_name'] = 'list';
        $page = $this->data['filter']['page'];
        $rows = $this->data['filter']['rows'];

        $conditions = array();
        //教務組承辦人
        if (in_array("8", $this->flags->user['group_id'])){
            $conditions['worker'] = $this->flags->user['idno'];
        }
        if (in_array("1", $this->flags->user['group_id'])){
            if(isset($conditions['worker'])){
                unset($conditions['worker']);
            }
        }

        $conditions['year'] = $this->data['filter']['year'];

        $attrs = array(
            'conditions' => $conditions,
        );
        if ($this->data['filter']['class_name'] !== '' ) {
            $attrs['class_name'] = $this->data['filter']['class_name'];
        }
        if ($this->data['filter']['class_no'] != '' ) {
            $attrs['class_no'] = $this->data['filter']['class_no'];
        }

        $attrs['where_special'] = " class_status in ('2','3') and IFNULL(is_cancel, '0') = '0' ";

        if($this->data['filter']['queryType'] == 'Y'){
            $attrs['where_special'] .= " and (year, class_no, term) in (select distinct year, class_no, term from require_grade) ";
        }
        if($this->data['filter']['queryType'] == 'N'){
            $attrs['where_special'] .= " and (year, class_no, term) not in (select distinct year, class_no, term from require_grade) ";
        }

        $this->data['filter']['total'] = $total = $this->point_create_model->getListCount($attrs);
        $this->data['filter']['offset'] = $offset = ($page -1) * $rows;

        $attrs['rows'] = $rows;
        $attrs['offset'] = $offset;

        $this->data['list'] = $this->point_create_model->getList($attrs);

        foreach($this->data['list'] as & $row){
            $row['detail'] = base_url("management/point_create/detail/{$row['seq_no']}");
        }

        $this->load->library('pagination');
        $config['base_url'] = base_url("management/point_create?". $this->getQueryString(array(), array('page')));
        $config['total_rows'] = $total;
        $config['per_page'] = $rows;
        $this->pagination->initialize($config);


        $this->data['link_refresh'] = base_url("management/point_create/");
        $this->layout->view('management/point_create/list',$this->data);
    }

    public function detail($seq_no)
    {

        $this->data['detail_data'] = $this->point_create_model->get($seq_no);

        if($post = $this->input->post()){

            if(isset($post['doActionImport']) && $post['doActionImport'] == 'imp'){
                if (isset($_FILES['impFile']) && $_FILES['impFile']['tmp_name'] != '') {
                    if (!fileExtensionCheck($_FILES['impFile']['name'], ['csv'])){
                        $this->setAlert(3, '檔案格式錯誤');
                        redirect(base_url("management/point_create/detail/{$seq_no}"));                        
                    }  
                    $file = fopen(sys_get_temp_dir().DIRECTORY_SEPARATOR.basename($_FILES['impFile']['tmp_name']),"r") or die("無法開啟");
                    $i = 1;
                    $successCount = 0;
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

                            if (!is_numeric($data[0])) {
                                //格式有誤
                                continue;
                            }
                            $conditions = array(
                                'st_no' => $data[0],
                                'year' => $this->data['detail_data']['year'],
                                'class_no' => $this->data['detail_data']['class_no'],
                                'term' => $this->data['detail_data']['term'],
                            );
                            $regist = $this->point_create_model->get_regist($conditions);

                            if($regist){
                                $id = $regist['id'];
                                $upd_conditions = array(
                                    'id' => $id,
                                    'year' => $this->data['detail_data']['year'],
                                    'class_no' => $this->data['detail_data']['class_no'],
                                    'term' => $this->data['detail_data']['term'],
                                );
                                $fields = array(
                                    'modi_num' => $data[2],
                                    'score' => $data[1],
                                );
                                $this->online_app_model->update($upd_conditions, $fields);
                            }else{
                                continue;
                            }

                        }

                    }

                    fclose($file);
                    $this->setAlert(1, '匯入成功');
                    redirect(base_url("management/point_create/detail/{$seq_no}"));
                }
            }
            if(isset($post['doAction']) && $post['doAction'] == 'save'){

                $onlineReady_Ary = $_POST["onlineReady"];
                $chkPerson_Ary = $_POST["chkPerson"];
                $npassdesc_Ary = $_POST["npassdesc"];
                foreach ($onlineReady_Ary as $k=>$v) {
                    $ndesc = $npassdesc_Ary[$k];
                    $onlineReady = 0;
                    if(!empty($onlineReady_Ary[$k])) {
                        $onlineReady = 1;
                    }
                    $chkPerson = 0;
                    if(isset($chkPerson_Ary[$k])) {
                        $chkPerson = 1;
                    }
                    $upd_conditions = array(
                        'id' => $k,
                        'year' => $this->data['detail_data']['year'],
                        'class_no' => $this->data['detail_data']['class_no'],
                        'term' => $this->data['detail_data']['term'],
                    );
                    $fields = array(
                        'online_ready' => $onlineReady,
                        'notpass_desc' => $ndesc,
                        'is_assess' => $chkPerson,
                    );
                    
                    if($this->online_app_model->update($upd_conditions, $fields)){
                        $this->setAlert(1, '儲存成功');
                    } else {
                        $this->setAlert(2, '儲存失敗');
                    }

                }

                if (isset($post['scoreInfo_SCORE'])) {
                    // 沒有分數類別，僅更新 online_app
                    foreach ($post['scoreInfo_SCORE'] as $id=>$score) {

                        $upd_conditions = array(
                            'id' => $id,
                            'year' => $this->data['detail_data']['year'],
                            'class_no' => $this->data['detail_data']['class_no'],
                            'term' => $this->data['detail_data']['term'],
                        );
                        if(empty($post['scoreInfo_SCORE'][$id])){
                            $post['scoreInfo_SCORE'][$id] = "0";
                        }
                        $fields = array(
                            'score' => number_format($post['scoreInfo_SCORE'][$id], 2),
                            'modi_num' => number_format($post['scoreInfo_MODI_NUM'][$id], 2),
                        );

                        $this->online_app_model->update($upd_conditions, $fields);
                    }
                }else{
                    if (isset($post['scoreInfo_type']) && is_array($post['scoreInfo_type'])) {
                        foreach ($post['scoreInfo_type'] as $gradeType=>$proportion) {
                            if (isset($post['scoreInfo_S_'.$gradeType]) && is_array($post['scoreInfo_S_'.$gradeType])) {
                                foreach ($post['scoreInfo_S_'.$gradeType] as $id=>$score) {
                                    // 判斷insert or update
                                    $cnt = $this->point_create_model->count_online_app_score($this->data['detail_data']['class_no'], $this->data['detail_data']['year'], $this->data['detail_data']['term'], $id, $gradeType);

                                    if ($cnt=='0') {
                                        // 新增
                                        //lux new
                                        $fields = array(
                                            'score' => $score,
                                            'year' => $this->data['detail_data']['year'],
                                            'class_no' => $this->data['detail_data']['class_no'],
                                            'term' => $this->data['detail_data']['term'],
                                            'id' => $id,
                                            'grade_type' => $gradeType,
                                        );
                                        $this->online_app_score_model->insert($fields);
                                        //lux new end
                                    } else {
                                        // 更新
                                        //lux new
                                        $upd_conditions = array(
                                            'year' => $this->data['detail_data']['year'],
                                            'class_no' => $this->data['detail_data']['class_no'],
                                            'term' => $this->data['detail_data']['term'],
                                            'id' => $id,
                                            'grade_type' => $gradeType,
                                        );
                                        $fields = array(
                                            'score' => $score,
                                        );
                                        $this->online_app_score_model->update($upd_conditions, $fields);
                                        //lux new end
                                    }
                                }
                            }
                        }
                    }

                    // 更新加減分
                    if (isset($post['scoreInfo_MODI_NUM']) && is_array($post['scoreInfo_type'])) {
                        foreach ($post['scoreInfo_MODI_NUM'] as $id=>$modiNum) {

                            //lux new
                            $upd_conditions = array(
                                'id' => $id,
                                'year' => $this->data['detail_data']['year'],
                                'class_no' => $this->data['detail_data']['class_no'],
                                'term' => $this->data['detail_data']['term'],
                            );
                            $fields = array(
                                'modi_num' => $modiNum,
                            );
                            $this->online_app_model->update($upd_conditions, $fields);
                            //lux new end
                        }
                    }
                }

                redirect(base_url("management/point_create/detail/{$seq_no}"));
            }
        }


        $this->data['model'] = $this->point_create_model->getScoreInfoByPkey($this->data['detail_data']['year'], $this->data['detail_data']['class_no'], $this->data['detail_data']['term']);
        $cid_list = $this->point_create_model->get_cid_list($this->data['detail_data']['year'], $this->data['detail_data']['class_no'], $this->data['detail_data']['term']);
        $new_cid_list = array();
        for($i=0;$i<count($cid_list);$i++){
            if($cid_list != '-1'){
                array_push($new_cid_list, $cid_list[$i]['elearn_id']);
            }
        }
        // jd($this->data['model']);
        foreach($this->data['model'] as & $row){
            $row['listData'] = $this->point_create_model->query_online_app($this->data['detail_data']['class_no'], $this->data['detail_data']['year'], $this->data['detail_data']['term'], $row['id']);
            $row['checkCourseFinish'] = 0;
            if(count($new_cid_list)>0) {
                //介接
                // jd($row);
                // jd($cid_list);
                $row['checkCourseFinish'] = $this->checkCourseFinish(md5(strtoupper($row['id'])), implode(", ", $new_cid_list),$this->data['detail_data']['year'], $this->data['detail_data']['start_date1'])?1:-1;
            }
        }

        $this->data['save_url'] = base_url("management/point_create/detail/{$seq_no}?".$this->getQueryString());
        $this->layout->view('management/point_create/detail',$this->data);
    }

    public function exportcsv()
    {
    	$eYear     = $this->data['filter']['year'];
		$eClass_no = $this->data['filter']['class_no'];
		$eTerm     = $this->data['filter']['term'];

		//查詢
		$model = $this->point_create_model->getScoreInfoByPkey($eYear, $eClass_no, $eTerm);

		//表頭
		$title = "";
		$title .= "組別資訊,";
		$title .= "學號,";
		$title .= "服務單位,";
		$title .= "職稱,";
		$title .= "姓名";
		if (isset($model[0]) && is_array($model[0]['gradesInfo'])) {
		   	foreach ($model[0]['gradesInfo'] as $grade) {
		  		$title .= "," . $grade['type_name'] .'(' . $grade['proportion'] . '％)';
		   	}
		}
		$title .= ",總分,";
		$title .= "加減分,";
		$title .= "總成績,";
		$title .= "等第,";

		//------------------------------------------------------------------------------------

		//資料
		//------------------------------------------------------------------------------------
		$mess = '';
		foreach ($model as $row) {
		    $mess .= "\n";
		    $mess .= $row['group_no'] . ",";
		    $mess .= $row['st_no'] . ",";
		    $mess .= $row['beaurau_name'] . ",";
		    $mess .= $row['title_name'] . ",";
		    $mess .= $row['name'];

			//輸出分 類成績
			$i = 1;
			$grades = array();
			if (isset($model[0]) && is_array($model[0]['gradesInfo'])) {
		       	foreach ($model[0]['gradesInfo'] as $grade) {
				    $mess .= "," . $row['s' . $i];
				    $grades['p' . $i] = 's' . $i;
			        $i = $i + 1;
		      	}
		    }
		    if (is_null($row['modi_num'])) {
		    	$row['modi_num'] = 0;
		    }
		    $mess .= "," .$row['main_score'];
		    $mess .= "," .$row['modi_num'];
		    $mess .= "," .$row['final_score'];
		    $mess .= "," .$row['p_score'];
		}

		//------------------------------------------------------------------------------------

		//輸出
		//------------------------------------------------------------------------------------
		header("Content-type: application/csv");
		header("Content-Disposition: attachment; filename=student_grade.csv;charset=UTF-8");
		header("Pragma: no-cache");
		header("Expires: 0");
		echo mb_convert_encoding($title,'Big5','UTF-8');
		echo mb_convert_encoding($mess,'Big5','UTF-8');
    }

    /**
     * 檢查某學員的特定課程是否都完成
     * @param resource md5(idno) $umid
     * @param resource int array $cid_list
     * @return bool tru on success, false otherwise
     */
    public function checkCourseFinish($umd5, $cid_list, $Yyear, $start_date)
    {
        $Y = (int)date('Y', strtotime($start_date));
        $m = (int)date('m', strtotime($start_date));

        if($m == 1){
            $Y = $Y-1;
        }

        if(strlen($cid_list)==0) {
            return false;
        }

        $corTotal = 1;
        for($i=0;$i<strlen($cid_list);$i++) {
            if($cid_list[$i]==",") {
                $corTotal++;
            }
        }

        $Yyear = intval($Yyear)+1911;

        $cid_array = array();

        for($i=$Yyear; $i>=$Y; $i--) {
            if($i==date("Y")) {
                $table = "mdl_fet_course_history";
            }
            else {
                $table = "mdl_fet_course_history_".$i;
            }
           
            $idno = OLD_DES::encrypt('ADLE3WE2R',$umd5);
            $idno = rtrim(strtr(base64_encode($idno), '+/', '-_'), '=');

            $data['idno'] = $idno;
            $data['table'] = $table;
            $data['cid_list'] = $cid_list;
            $data['mode'] = '3';

            $url = "https://elearning.taipei/get_data.php";

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $output = curl_exec($ch);

            curl_close($ch);

            $output = unserialize($output);
            // $sql = sprintf("SELECT a.courseid FROM $table a JOIN
            //     mdl_fet_pid b ON a.userid=b.uid
            //     WHERE a.courseid in (%s)
            //     AND md5(b.idno)='%s'
            //     AND a.timecomplete>0 ", $cid_list, $umd5);

            // $result = mysql_query($sql) or die('MySQL query error');
            // $row = mysql_fetch_array($result);

            // while($row = mysql_fetch_array($result,MYSQL_ASSOC)) {
            //     if(!in_array($row['courseid'],$cid_array)){
            //         $cid_array[] = $row['courseid'];
            //     }
            // }

            if($output) {
                if(count($output)==$corTotal) {
                    return true;
                }
            }
        }

        return false;
    }

}

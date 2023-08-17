<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Export_score extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        if ($this->flags->is_login === FALSE) {
            redirect(base_url('welcome'));
        }

        $this->load->model('management/export_score_model');

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

        $conditions['isend'] = 'Y';
        $conditions['year'] = $this->data['filter']['year'];

        $attrs = array(
            'conditions' => $conditions,
        );
        if ($this->data['filter']['class_name'] !== '' ) {
            $attrs['class_name'] = $this->data['filter']['class_name'];
        }
        if ($this->data['filter']['class_no'] !== '' ) {
            $attrs['class_no'] = $this->data['filter']['class_no'];
        }

        $this->data['filter']['total'] = $total = $this->export_score_model->getListCount($attrs);
        $this->data['filter']['offset'] = $offset = ($page -1) * $rows;

        $attrs = array(
            'conditions' => $conditions,
            'rows' => $rows,
            'offset' => $offset,
        );
        if ($this->data['filter']['class_name'] !== '' ) {
            $attrs['class_name'] = $this->data['filter']['class_name'];
        }
        if ($this->data['filter']['class_no'] !== '' ) {
            $attrs['class_no'] = $this->data['filter']['class_no'];
        }

        $this->data['list'] = $this->export_score_model->getList($attrs);
        foreach ($this->data['list'] as & $row) {
            $row['into_folder'] = base_url("management/export_score/into_folder/{$row['seq_no']}/?{$_SERVER['QUERY_STRING']}");
        }
        $this->load->library('pagination');
        $config['base_url'] = base_url("management/export_score?". $this->getQueryString(array(), array('page')));
        $config['total_rows'] = $total;
        $config['per_page'] = $rows;
        $this->pagination->initialize($config);

        $this->data['link_refresh'] = base_url("management/export_score/");
        $this->data['link_load_all'] = base_url("management/export_score/load_all");
        $this->data['link_zip'] = base_url("management/export_score/export_evn_zip");

        $this->layout->view('management/export_score/list', $this->data);
    }

    public function into_folder($seq_no)
    {
    	$data = $this->export_score_model->get($seq_no);

    	if(empty($data)){
    		$this->setAlert(3, '操作錯誤');
            redirect(base_url("management/export_score?{$_SERVER['QUERY_STRING']}"));
    	}

    	$dir = trim("upload_score/".$data['year'].'_'.$data['term']."_".$data['class_no']);
    	$file_dir = HTTP_MEDIA.$dir;
		$dir = DIR_MEDIA.$dir;
    	if (is_dir($dir)) {
			if ($dh = opendir($dir)) {
				while (($file = readdir($dh)) !== false) {
					if ($file!="." && $file!="..") {
						$arr['PATH'] = $file_dir.'/'.$file;
						$arr['NAME'] = $file;
						$fileAry[]=$arr;

					}
				}
				closedir($dh);
			}
		}

		$this->data['fileAry'] = $fileAry;
		$this->data['link_cancel'] = base_url("management/export_score/?{$_SERVER['QUERY_STRING']}");
		$this->layout->view('management/export_score/detail', $this->data);
    }

    public function load_all()
    {

		$dir = trim ( "upload_score_all" );
		$dir = DIR_MEDIA.$dir;
		$fileAry =array();
		if (is_dir($dir)) {
			if ($dh = opendir($dir)) {
				while (($file = readdir($dh)) !== false) {
					if ($file!="." && $file!="..") {
						$arr['PATH'] = HTTP_MEDIA.'upload_score_all/'.$file;
						$arr['DATE'] = substr($file, 0, 10) . ' ' . substr($file, 11, 2) . ':' . substr($file, 13, 2) . ':' . substr($file, 15, 2);
						$arr['NAME'] = substr($file, 18);
						$fileAry[$file] = $arr;
					}
				}
				closedir($dh);
			}
		}
		//array_flip($fileAry);
		krsort($fileAry);
		$this->data['fileAry'] = $fileAry;

		$this->load->view('management/export_score/load_all', $this->data);
    }

    public function ajax($action)
    {
        //$action = $this->input->get('action');
        $post = $this->input->post();

        $result = array(
            'status' => FALSE,
            'msg' => '無資料匯出!',
        );
        $rs = NULL;
        if ($action && $post) {
            $fields = array();
            switch ($action) {

                case 'export_scorecsv_output':

                    $error = FALSE;

                    if(empty($post['year'])){
                        $error = TRUE;
                    }
                    if(empty($post['term'])){
                        $error = TRUE;
                    }
                    if(empty($post['class_no'])){
                        $error = TRUE;
                    }
                    if(empty($post['kind'])){
                        $error = TRUE;
                    }

                    if($error === FALSE){
                    	$this->_export_csv_output($post['year'], $post['term'], $post['class_no'], $post['kind']);

	                    // jd($post);

	                    $result['status'] = TRUE;
	                    $result['msg'] = '產出成功!';
                    }

                    break;

                case 'export_csv_all':

                	$error = FALSE;

                    if(empty($post['year'])){
                        $error = TRUE;
                    }
                    if(empty($post['month'])){
                        $error = TRUE;
                    }

                    if($error === FALSE){

                    	$this->_export_csv_all($post['year'], $post['month']);

	                    // jd($post);

	                    $result['status'] = TRUE;
	                    $result['msg'] = '產出成功!';
                    }

                	break;

            }
        }

        echo json_encode($result);
    }

	/**
	 * 學習性質
	 * 1:數位, 2:實體, 3:混成
	 *
	 */
	public function learningattribute($online, $real){
	    if($online > 0 && $real == 0) return 1;
	    if($online == 0 && $real > 0) return 2;
	    if($online > 0 && $real > 0) return 3;
	}

	public function transdate($date) {
	    $tmp = explode("-", $date);
	    $year = $tmp[0]-1911;
	    $mon = $tmp[1];
	    $day = substr($tmp[2],0,2);
	    return "{$year}-{$mon}-{$day}";
	}

	public function transtime($time) {
	    return substr($time, 0, 2).":".substr($time, -2).":"."00";
	}

	public function _export_csv_output($year, $term, $class_no, $kind){
		//撈資料
		$data = array();

		$export_data = $this->export_score_model->getExport($year, $term, $class_no);

		$content = '';

		// 設定一個檔案最多幾筆
		$maxRows = 3000;
		$rowsCount = count($export_data);

		// 建立資料
		$contents = array();
		$no = 0;
		$count = 1;
		//echo $sql;
		$xx = '0';

		foreach($export_data as & $arr){
			if ($count > $maxRows*($no+1)) {
				$no++;
			}
			if (!isset($contents[$no])) {
				$contents[$no] = '';
			}

			// 學習類別
			/*
			$tmpcode = substr($arr["class_no"], 0, 3);
			$cpaclasstype = $sc2cpa[$tmpcode];
			*/

			//echo $sql_query."<br>";
			// 訓練總數 = 數位時數+實體時數
			$totalhours = $arr["range_real"];

			// 學習性質
			$learnattr = $this->learningattribute($arr["range_internet"], $arr["range_real"]);

			$rs1 = $this->export_score_model->getPeriodtime($year, $term, $class_no);
			$end_time=$this->export_score_model->getRealEndTime($year,$term,$class_no);

			if ($rs1) {
				foreach($rs1 as $rs1key => $arr1){
					if($rs1key == '0'){
						$arr["real_start_date"]=$this->transdate($arr1["course_date"]);
						$arr["real_start_time"]=$this->transtime($arr1["from_time"]);
						$arr["start_time"]=$arr["real_start_time"];
						//$arr["real_end_date"]=$this->transdate($arr1["course_date"]);
						//$arr["real_end_time"]=$this->transtime($arr1["to_time"]);
						$arr["real_end_date"]=$this->transdate($arr["end_date1"]);
						$arr["real_end_time"]=$this->transtime($end_time[0]['to_time']);
					}
					if($rs1key == count($rs1)) {
						$arr["real_end_date"]=$this->transdate($arr1["course_date"]);
						$arr["real_end_time"]=$this->transtime($arr1["to_time"]);
					}
				}
				$arr["end_time"]=$arr["real_end_time"];
			}else {
				$arr["real_start_date"]="";
				$arr["real_end_date"]="";
				$arr["real_start_time"]="";
				$arr["real_end_time"]="";

				$arr["start_time"]="";
				$arr["end_time"]="";
			}

			//$contents[$no] .= $arr["id"].",".$arr["class_name"].",".$arr["start_date1"].",".$arr["name"].",,,,";
			//$contents[$no] .= $arr["term"].",".$arr["end_date1"].",,,".$arr["score"].",,,".$arr["birthday"].",,".$arr["range_internet"].",".$arr["range_real"].",".$arr["class_no"]."\n";
			if ($kind == '2') {// 環教
			$contents[$no] .= '"'.$arr["year"].'","'.$arr["bureau_id"].'","'.$arr["id"].'","'.$arr["class_name"].'","'.$this->transdate($arr["start_date1"]).'","'.$arr["name"].'","1","'.$arr['ecpa_class_id'].'","","10","","'.$arr["term"].'","'.$this->transdate($arr["end_date1"]).'","'.((double)$totalhours).'","'.$learnattr.'","'.$arr["range_internet"].'","'.$arr["range_real"].'","'.$arr["class_no"].'"'."\n";
			} else {  // 終身學習
			  $ecpa_id = '';
			  if($xx == '0'){

			      $fields = $this->export_score_model->getCut_hour($arr["year"], $arr["term"], $arr["class_no"]);
			  }

			    $xx = 1;
			    $tmp_content = '';
			    if(!empty($fields['name1']) && !empty($fields['hour1'])){
			      if(!empty($fields['ecpa_id1'])){
			        $ecpa_id = $fields['ecpa_id1'];
			      } else {
			        $ecpa_id = $arr['ecpa_class_id'];
			      }

			      $tmp_content .= '"'.$arr["id"].'",'.$arr["class_name"].'-'.$fields['name1'].',"'.$this->transdate($arr["start_date1"]).'","'.$arr["start_time"].'","'.$this->transdate($arr["end_date1"]).'","'.$arr["end_time"].'","'.$arr["name"].'","6","'.$ecpa_id.'","10","'.$arr["term"].'","'.$fields['hour1'].'","1","'.$arr["score"].'","","","","2","","'.$fields['hour1'].'","'.$arr["class_no"].'","'.$arr["real_start_date"].'","'.$arr["real_start_time"].'","'.$arr["real_end_date"].'","'.$arr["real_end_time"].'"'."\n";

			      $totalhours = $totalhours - $fields['hour1'];
			      $count++;
			    }

			    if(!empty($fields['name2']) && !empty($fields['hour2'])){
			      if(!empty($fields['ecpa_id2'])){
			        $ecpa_id = $fields['ecpa_id2'];
			      } else {
			        $ecpa_id = $arr['ecpa_class_id'];
			      }

			      $tmp_content .= '"'.$arr["id"].'",'.$arr["class_name"].'-'.$fields['name2'].',"'.$this->transdate($arr["start_date1"]).'","'.$arr["start_time"].'","'.$this->transdate($arr["end_date1"]).'","'.$arr["end_time"].'","'.$arr["name"].'","6","'.$ecpa_id.'","10","'.$arr["term"].'","'.$fields['hour2'].'","1","'.$arr["score"].'","","","","2","","'.$fields['hour2'].'","'.$arr["class_no"].'","'.$arr["real_start_date"].'","'.$arr["real_start_time"].'","'.$arr["real_end_date"].'","'.$arr["real_end_time"].'"'."\n";

			      $totalhours = $totalhours - $fields['hour2'];
			      $count++;
			    }

			    if(!empty($fields['name3']) && !empty($fields['hour3'])){
			      if(!empty($fields['ecpa_id3'])){
			        $ecpa_id = $fields['ecpa_id3'];
			      } else {
			        $ecpa_id = $arr['ecpa_class_id'];
			      }

			      $tmp_content .= '"'.$arr["id"].'",'.$arr["class_name"].'-'.$fields['name3'].',"'.$this->transdate($arr["start_date1"]).'","'.$arr["start_time"].'","'.$this->transdate($arr["end_date1"]).'","'.$arr["end_time"].'","'.$arr["name"].'","6","'.$ecpa_id.'","10","'.$arr["term"].'","'.$fields['hour3'].'","1","'.$arr["score"].'","","","","2","","'.$fields['hour3'].'","'.$arr["class_no"].'","'.$arr["real_start_date"].'","'.$arr["real_start_time"].'","'.$arr["real_end_date"].'","'.$arr["real_end_time"].'"'."\n";

			      $totalhours = $totalhours - $fields['hour3'];
			      $count++;
			    }

			    if(!empty($fields['name4']) && !empty($fields['hour4'])){
			      if(!empty($fields['ecpa_id4'])){
			        $ecpa_id = $fields['ecpa_id4'];
			      } else {
			        $ecpa_id = $arr['ecpa_class_id'];
			      }

			      $tmp_content .= '"'.$arr["id"].'",'.$arr["class_name"].'-'.$fields['name4'].',"'.$this->transdate($arr["start_date1"]).'","'.$arr["start_time"].'","'.$this->transdate($arr["end_date1"]).'","'.$arr["end_time"].'","'.$arr["name"].'","6","'.$ecpa_id.'","10","'.$arr["term"].'","'.$fields['hour4'].'","1","'.$arr["score"].'","","","","2","","'.$fields['hour4'].'","'.$arr["class_no"].'","'.$arr["real_start_date"].'","'.$arr["real_start_time"].'","'.$arr["real_end_date"].'","'.$arr["real_end_time"].'"'."\n";

			      $totalhours = $totalhours - $fields['hour4'];
			      $count++;
			    }

			    if(!empty($fields['name5']) && !empty($fields['hour5'])){
			      if(!empty($fields['ecpa_id5'])){
			        $ecpa_id = $fields['ecpa_id5'];
			      } else {
			        $ecpa_id = $arr['ecpa_class_id'];
			      }

			      $tmp_content .= '"'.$arr["id"].'",'.$arr["class_name"].'-'.$fields['name5'].',"'.$this->transdate($arr["start_date1"]).'","'.$arr["start_time"].'","'.$this->transdate($arr["end_date1"]).'","'.$arr["end_time"].'","'.$arr["name"].'","6","'.$ecpa_id.'","10","'.$arr["term"].'","'.$fields['hour5'].'","1","'.$arr["score"].'","","","","2","","'.$fields['hour5'].'","'.$arr["class_no"].'","'.$arr["real_start_date"].'","'.$arr["real_start_time"].'","'.$arr["real_end_date"].'","'.$arr["real_end_time"].'"'."\n";

			      $totalhours = $totalhours - $fields['hour5'];
			      $count++;
			    }

			    if(!empty($fields['name6']) && !empty($fields['hour6'])){
			      if(!empty($fields['ecpa_id6'])){
			        $ecpa_id = $fields['ecpa_id6'];
			      } else {
			        $ecpa_id = $arr['ecpa_class_id'];
			      }

			      $tmp_content .= '"'.$arr["id"].'",'.$arr["class_name"].'-'.$fields['name6'].',"'.$this->transdate($arr["start_date1"]).'","'.$arr["start_time"].'","'.$this->transdate($arr["end_date1"]).'","'.$arr["end_time"].'","'.$arr["name"].'","6","'.$ecpa_id.'","10","'.$arr["term"].'","'.$fields['hour6'].'","1","'.$arr["score"].'","","","","2","","'.$fields['hour6'].'","'.$arr["class_no"].'","'.$arr["real_start_date"].'","'.$arr["real_start_time"].'","'.$arr["real_end_date"].'","'.$arr["real_end_time"].'"'."\n";

			      $totalhours = $totalhours - $fields['hour6'];
			      $count++;
			    }

			    if(!empty($fields['name7']) && !empty($fields['hour7'])){
			      if(!empty($fields['ecpa_id7'])){
			        $ecpa_id = $fields['ecpa_id7'];
			      } else {
			        $ecpa_id = $arr['ecpa_class_id'];
			      }

			      $tmp_content .= '"'.$arr["id"].'",'.$arr["class_name"].'-'.$fields['name7'].',"'.$this->transdate($arr["start_date1"]).'","'.$arr["start_time"].'","'.$this->transdate($arr["end_date1"]).'","'.$arr["end_time"].'","'.$arr["name"].'","6","'.$ecpa_id.'","10","'.$arr["term"].'","'.$fields['hour7'].'","1","'.$arr["score"].'","","","","2","","'.$fields['hour7'].'","'.$arr["class_no"].'","'.$arr["real_start_date"].'","'.$arr["real_start_time"].'","'.$arr["real_end_date"].'","'.$arr["real_end_time"].'"'."\n";

			      $totalhours = $totalhours - $fields['hour7'];
			      $count++;
			    }

			    if(!empty($fields['name8']) && !empty($fields['hour8'])){
			      if(!empty($fields['ecpa_id8'])){
			        $ecpa_id = $fields['ecpa_id8'];
			      } else {
			        $ecpa_id = $arr['ecpa_class_id'];
			      }

			      $tmp_content .= '"'.$arr["id"].'",'.$arr["class_name"].'-'.$fields['name8'].',"'.$this->transdate($arr["start_date1"]).'","'.$arr["start_time"].'","'.$this->transdate($arr["end_date1"]).'","'.$arr["end_time"].'","'.$arr["name"].'","6","'.$ecpa_id.'","10","'.$arr["term"].'","'.$fields['hour8'].'","1","'.$arr["score"].'","","","","2","","'.$fields['hour8'].'","'.$arr["class_no"].'","'.$arr["real_start_date"].'","'.$arr["real_start_time"].'","'.$arr["real_end_date"].'","'.$arr["real_end_time"].'"'."\n";

			      $totalhours = $totalhours - $fields['hour8'];
			      $count++;
			    }

			    if(!empty($fields['name9']) && !empty($fields['hour9'])){
			      if(!empty($fields['ecpa_id9'])){
			        $ecpa_id = $fields['ecpa_id9'];
			      } else {
			        $ecpa_id = $arr['ecpa_class_id'];
			      }

			      $tmp_content .= '"'.$arr["id"].'",'.$arr["class_name"].'-'.$fields['name9'].',"'.$this->transdate($arr["start_date1"]).'","'.$arr["start_time"].'","'.$this->transdate($arr["end_date1"]).'","'.$arr["end_time"].'","'.$arr["name"].'","6","'.$ecpa_id.'","10","'.$arr["term"].'","'.$fields['hour9'].'","1","'.$arr["score"].'","","","","2","","'.$fields['hour9'].'","'.$arr["class_no"].'","'.$arr["real_start_date"].'","'.$arr["real_start_time"].'","'.$arr["real_end_date"].'","'.$arr["real_end_time"].'"'."\n";

			      $totalhours = $totalhours - $fields['hour9'];
			      $count++;
			    }

			    if(!empty($fields['name10']) && !empty($fields['hour10'])){
			      if(!empty($fields['ecpa_id10'])){
			        $ecpa_id = $fields['ecpa_id10'];
			      } else {
			        $ecpa_id = $arr['ecpa_class_id'];
			      }

			      $tmp_content .= '"'.$arr["id"].'",'.$arr["class_name"].'-'.$fields['name10'].',"'.$this->transdate($arr["start_date1"]).'","'.$arr["start_time"].'","'.$this->transdate($arr["end_date1"]).'","'.$arr["end_time"].'","'.$arr["name"].'","6","'.$ecpa_id.'","10","'.$arr["term"].'","'.$fields['hour10'].'","1","'.$arr["score"].'","","","","2","","'.$fields['hour10'].'","'.$arr["class_no"].'","'.$arr["real_start_date"].'","'.$arr["real_start_time"].'","'.$arr["real_end_date"].'","'.$arr["real_end_time"].'"'."\n";

			      $totalhours = $totalhours - $fields['hour10'];
			      $count++;
			    }

			    if(!empty($tmp_content)){
			      $contents[$no] .= '"'.$arr["id"].'",'.$arr["class_name"].',"'.$this->transdate($arr["start_date1"]).'","'.$arr["start_time"].'","'.$this->transdate($arr["end_date1"]).'","'.$arr["end_time"].'","'.$arr["name"].'","6","'.$arr['ecpa_class_id'].'","10","'.$arr["term"].'","'.((double)$totalhours).'","1","'.$arr["score"].'","","","","2","","'.((double)$totalhours).'","'.$arr["class_no"].'","'.$arr["real_start_date"].'","'.$arr["real_start_time"].'","'.$arr["real_end_date"].'","'.$arr["real_end_time"].'"'."\n".$tmp_content;
			    } else {
			      $contents[$no] .= '"'.$arr["id"].'",'.$arr["class_name"].',"'.$this->transdate($arr["start_date1"]).'","'.$arr["start_time"].'","'.$this->transdate($arr["end_date1"]).'","'.$arr["end_time"].'","'.$arr["name"].'","6","'.$arr['ecpa_class_id'].'","10","'.$arr["term"].'","'.((double)$totalhours).'","1","'.$arr["score"].'","","","","'.$learnattr.'","'.$arr["range_internet"].'","'.$arr["range_real"].'","'.$arr["class_no"].'","'.$arr["real_start_date"].'","'.$arr["real_start_time"].'","'.$arr["real_end_date"].'","'.$arr["real_end_time"].'"'."\n".$tmp_content;
			    }


			}
			$count++;
		}
		if ($kind=='2') // 環教
		$title = "*年度,*學員所屬機關(構)代碼,*身分證字號,*課程名稱,*上課起始日期,*姓名,*課程方法代碼,*課程類別代碼,*內容概要,*實施縣市,實施地點,*期別,*上課結束日期,*課程時數,*學習性質,*數位時數,*實體時數,課程代碼\n";
		  //"*年度,*學員所屬機關(構)代碼,*身分證字號,*課程名稱,*起始日期,*姓名,*課程方法代碼,*課程類別代碼,*上課縣市,*期別,*終迄日期,*訓練總數,*學習性質,*數位時數,*實體時數,課程代碼\n";
		else  // 終身學習
		$title = "*身分證字號,*課程名稱,*開課起始日期,*開課起始時間,*開課結束日期,*開課結束時間,*姓名,*學位學分,*課程類別代碼,*上課縣市,*期別,*訓練總數,*訓練總數單位,訓練成績,證件字號,出勤上課狀況,生日,*學習性質,*數位時數,*實體時數,課程代碼,*實際上課起始日期,*實際上課起始時間,*實際上課結束日期,*實際上課結束時間\n";

		//echo $sql;
		$target_path = trim("upload_score/".basename($year).'_'.basename($term)."_".basename($class_no));
		$target_path = DIR_MEDIA.$target_path;
		//刪除當天的舊資料
		if (is_dir ( $target_path )) {
			if ($dh = opendir ( $target_path )) {
				$pattern = sprintf('/^%04d-%02d-%02d_(.+)\.csv$/', date('Y'), date('m'), date('d'));
				while ( ($file = readdir ( $dh )) !== false ) {
				  if ($file != "." && $file != "..") {
				    preg_match($pattern, $file, $matchs);
				    if (count($matchs) > 0) {
				    //echo $target_path . '/' . $file;
				      unlink ( $target_path . '/' . $file );
				    }
				  }
				}
				closedir ( $dh );
			}
		}

		// 寫檔
		if(!is_dir($target_path)){
			// jd($target_path);
			// jd($title);
			// jd($content);
			$old=umask(0);
			exec("mkdir -pm 777 ".escapeshellarg($target_path)."");
			umask($old);
		}

		foreach ($contents as $key => $content) {
			$start = $key*$maxRows+1;
			$end = ($key+1)*$maxRows;
			if ($end > $rowsCount) {
				$end = $rowsCount;
			}
			if ($kind == '2')
				$filename = sprintf('env%s_(%s~%s).csv', date('Y-m-d'), $start, $end);
			else
				$filename = sprintf('%s_(%s~%s).csv', date('Y-m-d'), $start, $end);

			$file=fopen($target_path.'/'.basename($filename), "w+");
			$out = mb_convert_encoding($title , 'Big5', 'UTF-8').mb_convert_encoding($content , 'Big5', 'UTF-8');
			fwrite($file, $out);
			fclose($file);
		}

		ob_end_flush();
	}

	public function _export_csv_all($year, $month){
		$query_year = $year + 1911;
		$query_Month = $month;

		if ($query_Month == 12) {
			$END_Year = $query_year + 1;
			$END_Month = 1;
		} else {
			$END_Year = $query_year;
			$END_Month = $query_Month + 1;
		}

		$start_date = sprintf('%s-%s-01', $query_year, $query_Month);
		$end_date = sprintf('%s-%s-01', $END_Year, $END_Month);
		//撈資料：帶班結束 require.isend = 'Y'才可以下載
		$export_list = $this->export_score_model->getExportList($start_date, $end_date);

		$complete = false;
		// 多加上是結訓或是有手動設定可下載的學員才會出現在CSV
		foreach($export_list as $arr1){
			$rs = $this->export_score_model->getExport($arr1['year'], $arr1['term'], $arr1['class_no']);
			// 設定一個檔案最多幾筆
			$maxRows = 3000;
			$rowsCount = count($rs);
			// 建立資料
			$contents = array();
			$no = 0;
			$count = 1;
			foreach($rs as $arr){
				if ($count > $maxRows*($no+1)) {
					$no++;
				}
				if (!isset($contents[$no])) {
					$contents[$no] = '';
				}
				// 訓練總數 = 數位時數+實體時數
        		$totalhours = $arr["range_internet"] + $arr["range_real"];
        		// 學習性質
        		$learnattr = $this->learningattribute($arr["range_internet"], $arr["range_real"]);

        		$contents[$no] .= '"'.$arr["id"].'","'.$arr["class_name"].'","'.$this->transdate($arr["start_date1"]).'","'.$arr["name"].'","6","'.$arr['ecpa_class_id'].'","10","'.$arr["term"].'","'.$this->transdate($arr["end_date1"]).'","'.$totalhours.'","1","'.$arr["score"].'","","","","'.$learnattr.'","'.$arr["range_internet"].'","'.$arr["range_real"].'","'.$arr["class_no"].'"'."\n";
        		$count++;
			}

			$title = "*身分證字號,*課程名稱,*開課起始日期,*開課起始時間,*開課結束日期,*開課結束時間,*姓名,*學位學分,*課程類別代碼,*上課縣市,*期別,*訓練總數,*訓練總數單位,訓練成績,證件字號,出勤上課狀況,生日,*學習性質,*數位時數,*實體時數,課程代碼,*實際上課起始日期,*實際上課起始時間,*實際上課結束日期,*實際上課結束時間\n ";
			// 寫檔
			$target_path = trim("upload_score/".basename($arr1['year']).'_'.basename($arr1['term'])."_".basename($arr1['class_no']));
			$target_path = DIR_MEDIA.$target_path;
			if(!is_dir($target_path)){
				$old=umask(0);
				exec("mkdir -pm 777 '".$target_path."'");
				umask($old);
			}
			foreach($contents as $key => $content){
				$start = $key*$maxRows+1;
				$end = ($key+1)*$maxRows;

				if ($end > $rowsCount) {
					$end = $rowsCount;
				}
				// $filename = sprintf('%s/%s_(%s~%s).csv', $target_path, date('Y-m-d'), $start, $end);
				$filename = date('Y-m-d').'_('.$start.'~'.$end.').csv';
				$file=fopen($target_path.'/'.basename($filename), "w+");
				$out = mb_convert_encoding($title , 'Big5', 'UTF-8').mb_convert_encoding($content , 'Big5', 'UTF-8');
				fwrite($file, $out);
				fclose($file);
			}
		}

		$rs = $this->export_score_model->getAllExport($start_date, $end_date);
		$content = array ();
		$tmp = 0;
		$count_record = 0;
		$limit_record = 3000;
		foreach($rs as $arr){
			// 訓練總數 = 數位時數+實體時數
		    $totalhours = $arr["range_internet"] + $arr["range_real"];
		    $totalhours_cut = $arr["range_real"];

		    // 學習性質
    		$learnattr = $this->learningattribute($arr["range_internet"], $arr["range_real"]);
    		$rs1 = $this->export_score_model->getPeriodtime($arr['year'], $arr['term'], $arr['class_no']);
    		$end_time=$this->export_score_model->getRealEndTime($arr['year'], $arr['term'], $arr['class_no']);

    		if ($rs1) {
				foreach($rs1 as $rs1key => $arr1){
					if($rs1key == '0'){
						$arr["real_start_date"]=$this->transdate($arr1["course_date"]);
						$arr["real_start_time"]=$this->transtime($arr1["from_time"]);
						$arr["start_time"]=$arr["real_start_time"];
						$arr["real_end_date"]=$this->transdate($arr["end_date1"]);
						$arr["real_end_time"]=$this->transtime($end_time[0]['to_time']);
						//$arr["real_end_date"]=$this->transdate($arr1["end_date1"]);
						//$arr["real_end_time"]=$this->transtime($arr1["end_date1"]);
					}
					if($rs1key == count($rs1)) {
						$arr["real_end_date"]=$this->transdate($arr1["course_date"]);
						$arr["real_end_time"]=$this->transtime($arr1["to_time"]);
					}
				}
				$arr["end_time"]=$arr["real_end_time"];
			}else {
				$arr["real_start_date"]="";
				$arr["real_end_date"]="";
				$arr["real_start_time"]="";
				$arr["real_end_time"]="";

				$arr["start_time"]="";
				$arr["end_time"]="";
			}

			$ecpa_id = '';
			$fields = $this->export_score_model->getCut_hour($arr["year"], $arr["term"], $arr["class_no"]);
			$tmp_content = '';

			if(!empty($fields['name1']) && !empty($fields['hour1'])){
		      if(!empty($fields['ecpa_id1'])){
		        $ecpa_id = $fields['ecpa_id1'];
		      } else {
		        $ecpa_id = $arr['ecpa_class_id'];
		      }

		      $tmp_content .= '"'.$arr["id"].'",'.$arr["class_name"].'-'.$fields['name1'].',"'.$this->transdate($arr["start_date1"]).'","'.$arr["start_time"].'","'.$this->transdate($arr["end_date1"]).'","'.$arr["end_time"].'","'.$arr["name"].'","6","'.$ecpa_id.'","10","'.$arr["term"].'","'.$fields['hour1'].'","1","'.$arr["score"].'","","","","2","","'.$fields['hour1'].'","'.$arr["class_no"].'","'.$arr["real_start_date"].'","'.$arr["real_start_time"].'","'.$arr["real_end_date"].'","'.$arr["real_end_time"].'"'."\n";

		      $totalhours_cut = $totalhours_cut - $fields['hour1'];
		      $count_record++;

		    }

		    if(!empty($fields['name2']) && !empty($fields['hour2'])){
		      if(!empty($fields['ecpa_id2'])){
		        $ecpa_id = $fields['ecpa_id2'];
		      } else {
		        $ecpa_id = $arr['ecpa_class_id'];
		      }

		      $tmp_content .= '"'.$arr["id"].'",'.$arr["class_name"].'-'.$fields['name2'].',"'.$this->transdate($arr["start_date1"]).'","'.$arr["start_time"].'","'.$this->transdate($arr["end_date1"]).'","'.$arr["end_time"].'","'.$arr["name"].'","6","'.$ecpa_id.'","10","'.$arr["term"].'","'.$fields['hour2'].'","1","'.$arr["score"].'","","","","2","","'.$fields['hour2'].'","'.$arr["class_no"].'","'.$arr["real_start_date"].'","'.$arr["real_start_time"].'","'.$arr["real_end_date"].'","'.$arr["real_end_time"].'"'."\n";

		      $totalhours_cut = $totalhours_cut - $fields['hour2'];
		      $count_record++;

		    }

		    if(!empty($fields['name3']) && !empty($fields['hour3'])){
		      if(!empty($fields['ecpa_id3'])){
		        $ecpa_id = $fields['ecpa_id3'];
		      } else {
		        $ecpa_id = $arr['ecpa_class_id'];
		      }

		      $tmp_content .= '"'.$arr["id"].'",'.$arr["class_name"].'-'.$fields['name3'].',"'.$this->transdate($arr["start_date1"]).'","'.$arr["start_time"].'","'.$this->transdate($arr["end_date1"]).'","'.$arr["end_time"].'","'.$arr["name"].'","6","'.$ecpa_id.'","10","'.$arr["term"].'","'.$fields['hour3'].'","1","'.$arr["score"].'","","","","2","","'.$fields['hour3'].'","'.$arr["class_no"].'","'.$arr["real_start_date"].'","'.$arr["real_start_time"].'","'.$arr["real_end_date"].'","'.$arr["real_end_time"].'"'."\n";

		      $totalhours_cut = $totalhours_cut - $fields['hour3'];
		      $count_record++;

		    }

		    if(!empty($fields['name4']) && !empty($fields['hour4'])){
		      if(!empty($fields['ecpa_id4'])){
		        $ecpa_id = $fields['ecpa_id4'];
		      } else {
		        $ecpa_id = $arr['ecpa_class_id'];
		      }

		      $tmp_content .= '"'.$arr["id"].'",'.$arr["class_name"].'-'.$fields['name4'].',"'.$this->transdate($arr["start_date1"]).'","'.$arr["start_time"].'","'.$this->transdate($arr["end_date1"]).'","'.$arr["end_time"].'","'.$arr["name"].'","6","'.$ecpa_id.'","10","'.$arr["term"].'","'.$fields['hour4'].'","1","'.$arr["score"].'","","","","2","","'.$fields['hour4'].'","'.$arr["class_no"].'","'.$arr["real_start_date"].'","'.$arr["real_start_time"].'","'.$arr["real_end_date"].'","'.$arr["real_end_time"].'"'."\n";

		      $totalhours_cut = $totalhours_cut - $fields['hour4'];
		      $count_record++;

		    }

		    if(!empty($fields['name5']) && !empty($fields['hour5'])){
		      if(!empty($fields['ecpa_id5'])){
		        $ecpa_id = $fields['ecpa_id5'];
		      } else {
		        $ecpa_id = $arr['ecpa_class_id'];
		      }

		      $tmp_content .= '"'.$arr["id"].'",'.$arr["class_name"].'-'.$fields['name5'].',"'.$this->transdate($arr["start_date1"]).'","'.$arr["start_time"].'","'.$this->transdate($arr["end_date1"]).'","'.$arr["end_time"].'","'.$arr["name"].'","6","'.$ecpa_id.'","10","'.$arr["term"].'","'.$fields['hour5'].'","1","'.$arr["score"].'","","","","2","","'.$fields['hour5'].'","'.$arr["class_no"].'","'.$arr["real_start_date"].'","'.$arr["real_start_time"].'","'.$arr["real_end_date"].'","'.$arr["real_end_time"].'"'."\n";

		      $totalhours_cut = $totalhours_cut - $fields['hour5'];
		      $count_record++;

		    }

		    if(!empty($fields['name6']) && !empty($fields['hour6'])){
		      if(!empty($fields['ecpa_id6'])){
		        $ecpa_id = $fields['ecpa_id6'];
		      } else {
		        $ecpa_id = $arr['ecpa_class_id'];
		      }

		      $tmp_content .= '"'.$arr["id"].'",'.$arr["class_name"].'-'.$fields['name6'].',"'.$this->transdate($arr["start_date1"]).'","'.$arr["start_time"].'","'.$this->transdate($arr["end_date1"]).'","'.$arr["end_time"].'","'.$arr["name"].'","6","'.$ecpa_id.'","10","'.$arr["term"].'","'.$fields['hour6'].'","1","'.$arr["score"].'","","","","2","","'.$fields['hour6'].'","'.$arr["class_no"].'","'.$arr["real_start_date"].'","'.$arr["real_start_time"].'","'.$arr["real_end_date"].'","'.$arr["real_end_time"].'"'."\n";

		      $totalhours_cut = $totalhours_cut - $fields['hour6'];
		      $count_record++;
		    }

		    if(!empty($fields['name7']) && !empty($fields['hour7'])){
		      if(!empty($fields['ecpa_id7'])){
		        $ecpa_id = $fields['ecpa_id7'];
		      } else {
		        $ecpa_id = $arr['ecpa_class_id'];
		      }

		      $tmp_content .= '"'.$arr["id"].'",'.$arr["class_name"].'-'.$fields['name7'].',"'.$this->transdate($arr["start_date1"]).'","'.$arr["start_time"].'","'.$this->transdate($arr["end_date1"]).'","'.$arr["end_time"].'","'.$arr["name"].'","6","'.$ecpa_id.'","10","'.$arr["term"].'","'.$fields['hour7'].'","1","'.$arr["score"].'","","","","2","","'.$fields['hour7'].'","'.$arr["class_no"].'","'.$arr["real_start_date"].'","'.$arr["real_start_time"].'","'.$arr["real_end_date"].'","'.$arr["real_end_time"].'"'."\n";

		      $totalhours_cut = $totalhours_cut - $fields['hour7'];
		      $count_record++;
		    }

		    if(!empty($fields['name8']) && !empty($fields['hour8'])){
		      if(!empty($fields['ecpa_id8'])){
		        $ecpa_id = $fields['ecpa_id8'];
		      } else {
		        $ecpa_id = $arr['ecpa_class_id'];
		      }

		      $tmp_content .= '"'.$arr["id"].'",'.$arr["class_name"].'-'.$fields['name8'].',"'.$this->transdate($arr["start_date1"]).'","'.$arr["start_time"].'","'.$this->transdate($arr["end_date1"]).'","'.$arr["end_time"].'","'.$arr["name"].'","6","'.$ecpa_id.'","10","'.$arr["term"].'","'.$fields['hour8'].'","1","'.$arr["score"].'","","","","2","","'.$fields['hour8'].'","'.$arr["class_no"].'","'.$arr["real_start_date"].'","'.$arr["real_start_time"].'","'.$arr["real_end_date"].'","'.$arr["real_end_time"].'"'."\n";

		      $totalhours_cut = $totalhours_cut - $fields['hour8'];
		      $count_record++;
		    }

		    if(!empty($fields['name9']) && !empty($fields['hour9'])){
		      if(!empty($fields['ecpa_id9'])){
		        $ecpa_id = $fields['ecpa_id9'];
		      } else {
		        $ecpa_id = $arr['ecpa_class_id'];
		      }

		      $tmp_content .= '"'.$arr["id"].'",'.$arr["class_name"].'-'.$fields['name9'].',"'.$this->transdate($arr["start_date1"]).'","'.$arr["start_time"].'","'.$this->transdate($arr["end_date1"]).'","'.$arr["end_time"].'","'.$arr["name"].'","6","'.$ecpa_id.'","10","'.$arr["term"].'","'.$fields['hour9'].'","1","'.$arr["score"].'","","","","2","","'.$fields['hour9'].'","'.$arr["class_no"].'","'.$arr["real_start_date"].'","'.$arr["real_start_time"].'","'.$arr["real_end_date"].'","'.$arr["real_end_time"].'"'."\n";

		      $totalhours_cut = $totalhours_cut - $fields['hour9'];
		      $count_record++;
		    }

		    if(!empty($fields['name10']) && !empty($fields['hour10'])){
		      if(!empty($fields['ecpa_id10'])){
		        $ecpa_id = $fields['ecpa_id10'];
		      } else {
		        $ecpa_id = $arr['ecpa_class_id'];
		      }

		      $tmp_content .= '"'.$arr["id"].'",'.$arr["class_name"].'-'.$fields['name10'].',"'.$this->transdate($arr["start_date1"]).'","'.$arr["start_time"].'","'.$this->transdate($arr["end_date1"]).'","'.$arr["end_time"].'","'.$arr["name"].'","6","'.$ecpa_id.'","10","'.$arr["term"].'","'.$fields['hour10'].'","1","'.$arr["score"].'","","","","2","","'.$fields['hour10'].'","'.$arr["class_no"].'","'.$arr["real_start_date"].'","'.$arr["real_start_time"].'","'.$arr["real_end_date"].'","'.$arr["real_end_time"].'"'."\n";

		      $totalhours_cut = $totalhours_cut - $fields['hour10'];
		      $count_record++;
		    }

		    if (!isset($content[$tmp])) {
				$content[$tmp] = '';
			}

		    if(!empty($tmp_content)){
				$content[$tmp] .= '"'.$arr["id"].'",'.$arr["class_name"].',"'.$this->transdate($arr["start_date1"]).'","'.$arr["start_time"].'","'.$this->transdate($arr["end_date1"]).'","'.$arr["end_time"].'","'.$arr["name"].'","6","'.$arr['ecpa_class_id'].'","10","'.$arr["term"].'","'.$totalhours_cut.'","1","'.$arr["score"].'","","","","2","","'.$totalhours_cut.'","'.$arr["class_no"].'","'.$arr["real_start_date"].'","'.$arr["real_start_time"].'","'.$arr["real_end_date"].'","'.$arr["real_end_time"].'"'."\n".$tmp_content;
			} else {
				$content[$tmp] .= '"'.$arr["id"].'",'.$arr["class_name"].',"'.$this->transdate($arr["start_date1"]).'","'.$arr["start_time"].'","'.$this->transdate($arr["end_date1"]).'","'.$arr["end_time"].'","'.$arr["name"].'","6","'.$arr['ecpa_class_id'].'","10","'.$arr["term"].'","'.$totalhours.'","1","'.$arr["score"].'","","","","'.$learnattr.'","'.$arr["range_internet"].'","'.$arr["range_real"].'","'.$arr["class_no"].'","'.$arr["real_start_date"].'","'.$arr["real_start_time"].'","'.$arr["real_end_date"].'","'.$arr["real_end_time"].'"'."\n".$tmp_content;
			}

		    $count_record = $count_record + 1;
			if ($count_record == $limit_record) {
				$count_record = 0;
				$tmp = $tmp + 1;
			}

		}

		$title = "*身分證字號,*課程名稱,*開課起始日期,*開課起始時間,*開課結束日期,*開課結束時間,*姓名,*學位學分,*課程類別代碼,*上課縣市,*期別,*訓練總數,*訓練總數單位,訓練成績,證件字號,出勤上課狀況,生日,*學習性質,*數位時數,*實體時數,課程代碼,*實際上課起始日期,*實際上課起始時間,*實際上課結束日期,*實際上課結束時間\n";
		$target_path_1 = trim ( "upload_score_all" );
		$target_path_1 = HTTP_MEDIA.$target_path_1;
		if (! is_dir ( $target_path_1 )) {
			$old = umask ( 0 );
			exec ( "mkdir -pm 777 '" . $target_path_1 . "'" );
			umask ( $old );

		}
		$dir = trim ( "upload_score_all" );
		$dir = DIR_MEDIA.$dir;
		$fileAry = array ();
		//刪除當年當月的舊資料
		if (is_dir ( $dir )) {
			if ($dh = opendir ( $dir )) {
				$pattern = sprintf('/_%03d-%02d_[0-9]+\.csv$/', $query_year, $query_Month);
				while ( ($file = readdir ( $dh )) !== false ) {
					if ($file != "." && $file != "..") {
						preg_match($pattern, $file, $matchs);
						if (count($matchs) > 0) {
							unlink ( $dir . '/' . $file );
						}
					}
				}
				closedir ( $dh );
			}
		}

		$data = date ( 'Y-m-d_His' );
		for($i = 0; $i < count ( $content ); $i ++) {
			$filename = basename(sprintf('%s_%03d-%02d_%02d.csv', $data, str_replace(" ", "", $query_year), str_replace(" ", "", $query_Month), ($i+1)));
			$filename1 = fopen ( $target_path_1 . "/" . basename($filename), "w+" );
			$out = mb_convert_encoding ( $title, 'Big5', 'UTF-8' ) . mb_convert_encoding ( $content[$i], 'Big5', 'UTF-8' );
			fwrite ( $filename1, $out);
			fclose ( $filename1 );
			$complete = true;
		}
	}

	public function export_evn_zip()
    {

		//$RESOURCE_PATH = DIR_MEDIA."extTemp/";//原本的
		$RESOURCE_PATH = DIR_MEDIA.'extTemp/';//2019-12-05
		$FILE_CSV1 = $RESOURCE_PATH."courseList.csv";
		$FILE_CSV2 = $RESOURCE_PATH."teacherList.csv";
		$FILE_ZIP = $RESOURCE_PATH."summary.zip";
		
		//清除之前的檔案
		if(file_exists($FILE_CSV1)) {
			unlink($FILE_CSV1);
		}
		if(file_exists($FILE_CSV2)) {
			unlink($FILE_CSV2);
		}
		if(file_exists($FILE_ZIP)) {
			unlink($FILE_ZIP);
		}
		//testdata
		$post["output_year"] = $this->input->post('output_year');
		$post["output_Month"] = $this->input->post('output_month');
		$ptYear = intval($post["output_year"])+1911;
		$ptMonth = intval($post["output_Month"]);
		if($ptMonth<10) {
			$ptMonth = "0$ptMonth";
		}
		$timeFlag = $ptYear."".$ptMonth;

		//=================================第一個檔案(courseList.csv)=================================

		$courseList = $this->export_score_model->getCourseList($ptYear, $timeFlag);

		

		// echo "<pre>";
		//產製檔案
		$listA = array ( array('Import_ID', 'CourseName', 'AllowNo', 'CourseProperty_1', 'CourseProperty_2',
								'CourseProperty_3', 'CourseProperty_4', 'SchoolId', 'CourseKind', 'CourseHour',
								'StartTime', 'EndTime', 'TimeId', 'TimeSet', 'FundId',
								'SubsidizeId', 'Contact_1_name', 'Contact_1_tel', 'Contact_1_mail', 'Contact_2_name',
								'Contact_2_tel', 'Contact_2_mail', 'TeacherList', 'FundMoney', 'Member',
								'MemberKind', 'Description', 'TeacherNum', 'ClassNum', 'ApplyStartTime',
								'ApplyEndTime', 'Charge', 'TeacherChargeList', 'CourseState', 'CourseError'));
		$T_AllowNo = "[自主辦理]105年3月23日臺北市公務人員訓練處北市訓教字10530274900號";
		$T_CourseProperty_1 = "75";
		$T_SchoolId = "383H02";
		$T_CourseKind = "0";
		$T_TimeId = "1";
		$T_TimeSet = "實體學習課程，上課地點：臺北市政府公務人員訓練處，上課時間：00:00-23:59";
		$T_FundId = "3";
		$T_SubsidizeId = "20";
		$T_FundMoney = "0";
		$T_Member = "10,20,30,40,50,60";
		$T_MemberKind = "教師";
		$T_Charge = "0";
		$T_TeacherChargeList = "A123456789";
		$T_CourseState = "1";
		$T_CourseError = "";
		foreach($courseList as $data){
			$evnR4 = sprintf("\"<!--<Note><category>%s</category><level>1</level><mode>1</mode></Note>-->\"", $data["ENV_R4"]);
			array_push($listA, array("\"".$data["IMPORTID"]."\"", "\"".$data["CLASS_NAME"]."\"", "\"".$T_AllowNo."\"", "\"".$T_CourseProperty_1."\"", "\"".$data["ENV_R1"]."\"",
									"\"".$data["ENV_R2"]."\"", "\"".$data["ENV_R3"]."\"", "\"".$T_SchoolId."\"", "\"".$T_CourseKind."\"", "\"".$data["RANGE_REAL"]."\"",
									"\"".$data["STARTDATE"]."\"", "\"".$data["ENDDATE"]."\"", "\"".$T_TimeId."\"", "\"".$T_TimeSet."\"", "\"".$T_FundId."\"",
									"\"".$T_SubsidizeId."\"", "\"".$data["NAME"]."\"", "\"".$data["CO_EMPDB_POFTEL"]."\"", "\"".$data["bs_user_email"]."\"", "",
									"", "", "", "\"".$T_FundMoney."\"", "\"".$T_Member."\"",
									"\"".$T_MemberKind."\"", "\"".$evnR4."\"", "\"".$data["NO_PERSONS"]."\"", "\"".$data["TERM"]."\"", "\"".$data["APLYSDATE"]."\"",
									"\"".$data["APLYEDATE"]."\"", "\"".$T_Charge."\"", "\"".$T_TeacherChargeList."\"", "\"".$T_CourseState."\"", "\"".$T_CourseError."\""));
		}
		// echo $sql."<br>";
		$fp = fopen($FILE_CSV1, 'w');
		foreach ($listA as $k => $v) {
			fwrite($fp, iconv("UTF-8","BIG5",implode(",", $v))."\n");
		}
		fclose($fp);
		unset($courseList);
		unset($listA);

		//=================================第二個檔案(teacherList.csv)=================================

		$teacherList = $this->export_score_model->getTeacherList($ptYear, $timeFlag);
		//產製檔案
		$listB = array ( array('Import_ID', 'TeacherId', 'Counts'));
		foreach($teacherList as $data){
			array_push($listB, array("\"".$data["IMPORTID"]."\"", "\"".$data["ID"]."\"", "\"".$data["RANGE_REAL"]."\""));
		}
		$fp = fopen($FILE_CSV2, 'w');
		foreach ($listB as $k => $v) {
			fwrite($fp, iconv("UTF-8","BIG5",implode(",", $v))."\n");
		}
		fclose($fp);
		unset($teacherList);
		unset($listB);

		//=================================壓縮檔案=================================
		$cmdScript = sprintf("zip -j %s %s %s", $FILE_ZIP, $FILE_CSV2, $FILE_CSV1);
		// jd($cmdScript,1);
		exec($cmdScript);
		sleep(1.5);
		//=================================輸出檔案=================================
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename=summary.zip');
		header("Content-Length: " . filesize($FILE_ZIP));
	 	readfile($FILE_ZIP);

    }

}

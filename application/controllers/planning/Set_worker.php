<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Set_worker extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();

		if ($this->flags->is_login === FALSE) {
			redirect(base_url('welcome'));
		}
		$this->load->model('planning/set_worker_model');
	
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
        if (!isset($this->data['filter']['query_season'])) {
            $this->data['filter']['query_season'] = '';
        }
        if (!isset($this->data['filter']['query_month_start'])) {
            $this->data['filter']['query_month_start'] = '';
        }
        if (!isset($this->data['filter']['query_month_end'])) {
            $this->data['filter']['query_month_end'] = '';
        }
        if (!isset($this->data['filter']['query_type'])) {
            $this->data['filter']['query_type'] = '';
        }
        if (!isset($this->data['filter']['query_second'])) {
            $this->data['filter']['query_second'] = '';
        }
        if (!isset($this->data['filter']['query_worker_idno'])) {
            $this->data['filter']['query_worker_idno'] = '';
        }
        if (!isset($this->data['filter']['query_worker_name'])) {
            $this->data['filter']['query_worker_name'] = '';
        }
	}

	public function index()
	{
		if ($post = $this->input->post()) {
			$message = $message2 = '';
			for($i=0;$i<count($post['rowid']);$i++){
                $class_info = $this->set_worker_model->getClassInfo($post['rowid'][$i]);
				if(!empty($post['batch_worker_idno'])){
                	$this->set_worker_model->updateWorker($post['rowid'][$i],$post['batch_worker_idno']);
                    $this->set_worker_model->updateWorkerForHourTrafficTax($class_info,$post['batch_worker_idno']);
                    $this->set_worker_model->updateWorkerForDiningStudent($class_info,$post['batch_worker_idno']);
                }

                if(!empty($post['batch_room_id'])){
                	$this->set_worker_model->updateRoom($post['rowid'][$i],$post['batch_room_id']);
                }

				if(!empty($post['batch_apply_s_date'])){
					$check_apply_date2 = $this->set_worker_model->checkApplyDate($post['rowid'][$i]);
					if(!empty($check_apply_date2)){
						$message .= $check_apply_date2;
						continue;
					} else {
						$this->set_worker_model->updateApplyStartDate($post['rowid'][$i],$post['batch_apply_s_date']);
					}
                } else {
                    $tmp_start_key = 'apply_s_date_'.$post['rowid'][$i];
                    $check_apply_s_date_is_change = false;
                    if(!empty($post[$tmp_start_key])){
                        $check_apply_s_date_is_change = $this->set_worker_model->checkApplyStartDateIsChange($post['rowid'][$i],$post[$tmp_start_key]);
                    }

                    if($check_apply_s_date_is_change){
                        $check_apply_date2 = $this->set_worker_model->checkApplyDate($post['rowid'][$i]);

                        if(!empty($check_apply_date2)){
                            $message .= $check_apply_date2;
                            continue;
                        } else {
                            if(!empty($post[$tmp_start_key])){
                                $this->set_worker_model->updateApplyStartDate($post['rowid'][$i],$post[$tmp_start_key]);
                            }
                        }
                    } 
                }

                if(!empty($post['batch_apply_e_date'])){
                	$check_apply_date2 = $this->set_worker_model->checkApplyDate($post['rowid'][$i]);
                	if(!empty($check_apply_date2)){
						$message .= $check_apply_date2;
						continue;
					} else {
                    	$this->set_worker_model->updateApplyEndDate($post['rowid'][$i],$post['batch_apply_e_date']);
                    }
                } else {
                    $tmp_end_key = 'apply_e_date_'.$post['rowid'][$i];
                    $check_apply_e_date_is_change = false;
                    if(!empty($post[$tmp_end_key])){
                        $check_apply_e_date_is_change = $this->set_worker_model->checkApplyEndDateIsChange($post['rowid'][$i],$post[$tmp_end_key]);
                    }

                    if($check_apply_e_date_is_change){
                        $check_apply_date2 = $this->set_worker_model->checkApplyDate($post['rowid'][$i]);
                        if(!empty($check_apply_date2)){
                            $message .= $check_apply_date2;
                            continue;
                        } else {
                            if(!empty($post[$tmp_end_key])){
                                $this->set_worker_model->updateApplyEndDate($post['rowid'][$i],$post[$tmp_end_key]);
                            }
                        }
                    }
                }

                if(!empty($post['batch_apply_s_date2'])){
                    $tmp_start_key = 'apply_s_date_'.$post['rowid'][$i];
                    if(empty($post[$tmp_end_key])){
                        $message2 .= $post['rowid'][$i].":「首次報名起日」尚未設定，因此本次資料將帶入「首次報名起日」<br>";
                        $this->set_worker_model->updateApplyStartDate($post['rowid'][$i],$post['batch_apply_s_date2']);
                    }
                    else{
                        $this->set_worker_model->updateApplyStartDate2($post['rowid'][$i],$post['batch_apply_s_date2']);
                    }
                } else {
                    $tmp_start_key = 'apply_s_date2_'.$post['rowid'][$i];
                    if(!empty($post[$tmp_start_key])){
                        $this->set_worker_model->updateApplyStartDate2($post['rowid'][$i],$post[$tmp_start_key]);
                    }
                }

                if(!empty($post['batch_apply_e_date2'])){
                    $tmp_end_key = 'apply_e_date_'.$post['rowid'][$i];
                    if(empty($post[$tmp_end_key])){
                        $message2 .= $post['rowid'][$i].":「首次報名迄日」尚未設定，因此本次資料將帶入「首次報名迄日」<br>";
                        $this->set_worker_model->updateApplyEndDate($post['rowid'][$i],$post['batch_apply_e_date2']);
                    }
                    else{
                        $this->set_worker_model->updateApplyEndDate2($post['rowid'][$i],$post['batch_apply_e_date2']);
                    }
                } else {
                    $tmp_end_key = 'apply_e_date2_'.$post['rowid'][$i];
                    if(!empty($post[$tmp_end_key])){
                        $this->set_worker_model->updateApplyEndDate2($post['rowid'][$i],$post[$tmp_end_key]);
                    }
                }

			}
            

            //var_dump($post);
			if(!empty($message)){
                $this->setAlert(2, $message);

            } else {
                $this->setAlert(2, '資料修改成功<br>'.$message2);
            }
		
			redirect(base_url("planning/set_worker?{$_SERVER['QUERY_STRING']}"));
		}

		$this->data['page_name'] = 'list';
        $this->data['choices']['query_type'] = $this->getSeriesCategory();
        $this->data['choices']['query_type'][''] = '請選擇系列別';

        $conditions = array();

        if ($this->data['filter']['query_year'] !== '' ) {
            $conditions['year'] = $this->data['filter']['query_year'];
        }

        if ($this->data['filter']['query_class_no'] !== '' ) {
            $conditions['class_no'] = $this->data['filter']['query_class_no'];
        }

        if ($this->data['filter']['query_season'] !== '' ) {
            $conditions['reason'] = $this->data['filter']['query_season'];
        }

        if ($this->data['filter']['query_month_start'] !== '' && $this->data['filter']['query_month_end'] !== '') {
            $conditions['MONTH(start_date1) >='] = $this->data['filter']['query_month_start'];
            $conditions['MONTH(end_date1) <='] = $this->data['filter']['query_month_end'];
        } else if($this->data['filter']['query_month_start'] !== ''){
            $conditions['MONTH(start_date1) >='] = $this->data['filter']['query_month_start'];
        } else if($this->data['filter']['query_month_end'] !== ''){
            $conditions['MONTH(end_date1) <='] = $this->data['filter']['query_month_end'];
        }

        if ($this->data['filter']['query_type'] !== '' ) {
            $conditions['type'] = $this->data['filter']['query_type'];
            $this->data['choices']['query_second'] = $this->set_worker_model->getSecondCategory($this->data['filter']['query_type']);
        }

        if ($this->data['filter']['query_second'] !== '' ) {
            $conditions['beaurau_id'] = $this->data['filter']['query_second'];
        }

        if ($this->data['filter']['query_worker_idno'] !== '' ) {
            $conditions['worker'] = $this->data['filter']['query_worker_idno'];
        }
        //$status=[2,3];
        //$conditions['status']=$status;

        $page = $this->data['filter']['page'];
        $rows = $this->data['filter']['rows'];

		$attrs = array(
            'conditions' => $conditions,
        );

        if ($this->data['filter']['query_class_name'] !== '' ) {
            $attrs['query_class_name'] = $this->data['filter']['query_class_name'];
        }
        
        $status=[2,3];
        $attrs['class_status']=$status;

        $this->data['filter']['total'] = $total = $this->set_worker_model->getListCount($attrs);
        $this->data['filter']['offset'] = $offset = ($page -1) * $rows;

        $attrs = array(
            'conditions' => $conditions,
            'rows' => $rows,
            'offset' => $offset,
        );
        
        if ($this->data['filter']['sort'] !== '' ) {
            $attrs['sort'] = $this->data['filter']['sort'];
        }

        if ($this->data['filter']['query_class_name'] !== '' ) {
            $attrs['query_class_name'] = $this->data['filter']['query_class_name'];
        }

        $status=[2,3];
        $attrs['class_status']=$status;

		$this->data['list'] = $this->set_worker_model->getList($attrs);
		
		$this->load->library('pagination');
        $config['base_url'] = base_url("planning/set_worker?". $this->getQueryString(array(), array('page')));
        $config['total_rows'] = $total;
        $config['per_page'] = $rows;
        $this->pagination->initialize($config);
        $this->data['link_get_second_category'] = base_url("planning/set_worker/getSecondCategory");
        $this->data['link_import_worker'] = base_url("planning/set_worker/import");
        $this->data['link_export_worker'] = base_url("planning/set_worker/export?{$_SERVER['QUERY_STRING']}");
        $this->data['link_delete_worker'] = base_url("planning/set_worker/delete?{$_SERVER['QUERY_STRING']}");
		// $this->data['link_confirm'] = '';
		$this->data['link_refresh'] = base_url("planning/set_worker");

		$this->layout->view('planning/set_worker/list', $this->data);
	}

    public function import()
    {
		if(isset($_FILES['myfile']['name'])){
			if(basename($_FILES['myfile']['name']) == 'set_worker.csv'){
				$uploaddir = DIR_UPLOAD_FILES;
				$uploadfile = $uploaddir.basename($_FILES['myfile']['name']);
				$uploadfile = iconv("utf-8", "big5", $uploadfile);

				if (move_uploaded_file($_FILES['myfile']['tmp_name'], $uploadfile)) {    
					$fp = fopen ($uploadfile,"r") or die("無法開啟");
					$data = array();
					$row = 0;
					$success = 0;
					$fail = 0;
					while(!feof($fp)){
						$content = fgets($fp);
						// $content = mb_convert_encoding($content, 'UTF-8', 'BIG5');
						$fields = explode(",",$content);
                        $fields[8]=trim($fields[8]);
                        //var_dump($fields);
                        //var_dump(count($fields));
                        //die();
						if($row == '1' && count($fields) == 9 && !empty($fields[0]) && !empty($fields[1]) && !empty($fields[2]) && !empty($fields[3])){
							$data['year'] =  intval($fields[0]);
							$data['class_no'] = trim($fields[1]);
							$data['term'] = intval($fields[2]);
							$data['worker'] = $this->set_worker_model->get_worker_idno(trim($fields[3]));

                            
                            if(!empty($fields[4])){
                                //$fields[4]=str_replace("/","-",$fields[4]);
                                $data['weights']=trim($fields[4]);
                            }
                            if(!empty($fields[5])){
                                $fields[5]=str_replace("/","-",$fields[5]);
                                $data['apply_s_date']=trim($fields[5]);
                            }
                            if(!empty($fields[6])){
                                $fields[6]=str_replace("/","-",$fields[6]);
                                $data['apply_e_date']=trim($fields[6]);
                            }
                            if(!empty($fields[7])){
                                $fields[7]=str_replace("/","-",$fields[7]);
                                $data['apply_s_date2']=trim($fields[7]);
                            }
                            if(!empty($fields[8])){
                                $fields[8]=str_replace("/","-",$fields[8]);
                                $data['apply_e_date2']=trim($fields[8]).'test';
                            }
                                //var_dump($data);
                               // die();
                                
                            //}
                            

							if(!empty($data['worker'])){
								$saved_status = $this->set_worker_model->updateWorkerByImport($data);
								if ($saved_status) {
									$success++;
								} else {
									$fail++;
								}
							}
						}
						$row = 1;
					}

					$this->setAlert(1, '資料匯入成功<br>'.'成功:'.$success.'筆<br>'.'失敗'.$fail.'筆');
					redirect(base_url('planning/set_worker'));
				}
			}
		}

		$this->data['link_cancel'] = base_url("planning/set_worker?{$_SERVER['QUERY_STRING']}");
		$this->layout->view('planning/set_worker/import',  $this->data);
	}

	public function export()
    {
		$conditions = array();

        if ($this->data['filter']['query_year'] !== '' ) {
            $conditions['year'] = $this->data['filter']['query_year'];
        }

        if ($this->data['filter']['query_class_no'] !== '' ) {
            $conditions['class_no'] = $this->data['filter']['query_class_no'];
        }

        if ($this->data['filter']['query_season'] !== '' ) {
            $conditions['reason'] = $this->data['filter']['query_season'];
        }
        if ($this->data['filter']['query_month_start'] !== '' && $this->data['filter']['query_month_end'] !== '') {
            $conditions['MONTH(start_date1) >='] = $this->data['filter']['query_month_start'];
            $conditions['MONTH(end_date1) <='] = $this->data['filter']['query_month_end'];
        } else if($this->data['filter']['query_month_start'] !== ''){
            $conditions['MONTH(start_date1) >='] = $this->data['filter']['query_month_start'];
        } else if($this->data['filter']['query_month_end'] !== ''){
            $conditions['MONTH(end_date1) <='] = $this->data['filter']['query_month_end'];
        }

        if ($this->data['filter']['query_type'] !== '' ) {
            $conditions['type'] = $this->data['filter']['query_type'];
            $this->data['choices']['query_second'] = $this->set_worker_model->getSecondCategory($this->data['filter']['query_type']);
        }

        if ($this->data['filter']['query_second'] !== '' ) {
            $conditions['beaurau_id'] = $this->data['filter']['query_second'];
        }

        $attrs = array(
            'conditions' => $conditions,
        );
        
        if ($this->data['filter']['sort'] !== '' ) {
            $attrs['sort'] = $this->data['filter']['sort'];
        }

        if ($this->data['filter']['query_class_name'] !== '' ) {
            $attrs['query_class_name'] = $this->data['filter']['query_class_name'];
        }

		$info = $this->set_worker_model->getList($attrs);

		// 新增Excel物件
        $this->load->library('excel');
        $objPHPExcel = new PHPExcel();

        // 設定屬性
        $objPHPExcel->getProperties()->setCreator("PHP")
                    ->setLastModifiedBy("PHP")
                    ->setTitle("Orders")
                    ->setSubject("Subject")
                    ->setDescription("Description")
                    ->setKeywords("Keywords")
                    ->setCategory("Category");

        // 設定操作中的工作表
        $objPHPExcel->setActiveSheetIndex(0);
        $sheet = $objPHPExcel->getActiveSheet();

        // 將工作表命名
        $sheet->setTitle('List');
        $sheet->setCellValue('A1','年度');
        $sheet->setCellValue('B1','系列別');
        $sheet->setCellValue('C1','次類別');
        $sheet->setCellValue('D1','班期代碼');
        $sheet->setCellValue('E1','班期名稱');
        $sheet->setCellValue('F1','期別');
        $sheet->setCellValue('G1','承辦人姓名');
        $sheet->setCellValue('H1','教室');
        $sheet->setCellValue('I1','計畫期程(小時)');
        $sheet->setCellValue('J1','權重');
        $sheet->setCellValue('K1','權重後時數');
        $sheet->setCellValue('L1','系統季別');
        $sheet->setCellValue('M1','開班起日');
        $sheet->setCellValue('N1','開班迄日');
        $sheet->setCellValue('O1','首次報名起日');
        $sheet->setCellValue('P1','首次報名迄日');
        $sheet->setCellValue('Q1','二次報名起日');
        $sheet->setCellValue('R1','二次報名迄日');

        $rows = 2;
        for($i=0;$i<count($info);$i++){
            $sheet->setCellValue('A'.$rows,$info[$i]['year']);
        	$sheet->setCellValue('B'.$rows,$info[$i]['series_name']);
	        $sheet->setCellValue('C'.$rows,$info[$i]['second_name']);
	        $sheet->setCellValue('D'.$rows,$info[$i]['class_no']);
            $sheet->setCellValue('E'.$rows,$info[$i]['class_name']);
	        $sheet->setCellValue('F'.$rows,$info[$i]['term']);
	        $sheet->setCellValue('G'.$rows,$info[$i]['worker_name']);
	        $sheet->setCellValue('H'.$rows,$info[$i]['room_name']);
	        $sheet->setCellValue('I'.$rows,$info[$i]['range']);
	        $sheet->setCellValue('J'.$rows,$info[$i]['weights']);
	        $sheet->setCellValue('K'.$rows,($info[$i]['range']*$info[$i]['weights']));
	        $sheet->setCellValue('L'.$rows,$info[$i]['reason']);
	        $sheet->setCellValue('M'.$rows,substr($info[$i]['start_date1'],0,10));
	        $sheet->setCellValue('N'.$rows,substr($info[$i]['end_date1'],0,10));
	        $sheet->setCellValue('O'.$rows,substr($info[$i]['apply_s_date'],0,10));
	        $sheet->setCellValue('P'.$rows,substr($info[$i]['apply_e_date'],0,10));
	        $sheet->setCellValue('Q'.$rows,substr($info[$i]['apply_s_date2'],0,10));
	        $sheet->setCellValue('R'.$rows,substr($info[$i]['apply_e_date2'],0,10));
	        $rows++;
        }

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');

        header('Content-Type:application/csv;charset=UTF-8');
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
        header("Content-Type:application/force-download");
        header("Content-Type:application/vnd.ms-excel;");
        header("Content-Type:application/octet-stream");
        header('Content-Disposition: attachment;filename="'.generatorRandom(10).'.xls"');
        header("Content-Transfer-Encoding:binary");
        $objWriter->save('php://output');

        exit;
	}

	public function delete()
    {
		if ($post = $this->input->post()) {
			foreach ($post['rowid'] as $id) {
				$rs = $this->set_worker_model->updateWorker($id,null);
			}
			$this->setAlert(2, '資料刪除成功');
		}

		redirect(base_url("planning/set_worker?{$_SERVER['QUERY_STRING']}"));
	}
}
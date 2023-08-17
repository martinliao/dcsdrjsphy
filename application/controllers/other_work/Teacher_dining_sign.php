<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Teacher_dining_sign extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('other_work/Teacher_dining_model');

        if (!isset($this->data['filter']['page'])) {
            $this->data['filter']['page'] = '1';
        }
    }

    public function index()
    {	
        
        if ($this->input->post('mode') == 'clearDining'){
            $autos = $this->input->post('auto');
            $manuals = $this->input->post('manual');
            if (!empty($autos)){
                $autos = array_map(function($auto){
                    $auto = explode('_', $auto);

                    if (count($auto) != 5) return null;

                    $keys = ['year', 'class_no', 'term', 'course_date', 'idno'];
                    $auto = array_combine($keys, $auto);
                    return $auto;
                }, $autos);
                $autos = array_filter($autos);

                if (empty($autos)){
                    die('error');
                }

                foreach($autos as $auto){
                    $this->Teacher_dining_model->clearAutoDining($auto);
                }
            }

            if (!empty($manuals)){
                foreach($manuals as $manual){
                    $this->Teacher_dining_model->clearManualDining($manual);
                }
            }
            $this->setAlert(1, '清除成功');
            redirect("other_work/teacher_dining_sign?{$_SERVER['QUERY_STRING']}");
        }
        

    	if($this->input->post('mode') == 'setup' && !empty($this->input->post('place')) && !empty($this->input->post('way')) && !empty($this->input->post('food_type')) && !empty($this->input->post('num')) && (!empty($this->input->post('auto')) || !empty($this->input->post('manual')))){

    		$modify_data = array();
    		$modify_data['place'] = $this->input->post('place');
    		$modify_data['way'] = $this->input->post('way');
    		$modify_data['food_type'] = $this->input->post('food_type');
    		$modify_data['num'] = $this->input->post('num');
    		$modify_data['remark'] = $this->input->post('remark');
    		$auto_list = $this->input->post('auto');
    		$manual_list = $this->input->post('manual');

    		$chk_auto = true;
    		$chk_manual = true;
    		for($i=0;$i<count($auto_list);$i++){
    			$auto_info = explode('_', $auto_list[$i]);
    			if(count($auto_info) == 5){
    				$modify_data['year'] = $auto_info[0];
    				$modify_data['class_no'] = $auto_info[1];
    				$modify_data['term'] = $auto_info[2];
    				$modify_data['course_date'] = $auto_info[3];
    				$modify_data['idno'] = $auto_info[4];

    				$chkExist = $this->Teacher_dining_model->chkAutoExist($modify_data['year'], $modify_data['class_no'], $modify_data['term'], $modify_data['course_date'], $modify_data['idno']);

    				if($chkExist){
    					$chk_auto = $this->Teacher_dining_model->updateAuto($modify_data);
    				} else {
    					$chk_auto = $this->Teacher_dining_model->insertAuto($modify_data);
    				}
    			}
    		}

    		for($i=0;$i<count($manual_list);$i++){
    			$modify_data['id'] = $manual_list[$i];
    			$chk_manual = $this->Teacher_dining_model->updateManual($modify_data);
    		}

    		if($chk_auto && $chk_manual){
    			$this->setAlert(1, '設定成功');
    		} else {
    			$this->setAlert(2, '設定失敗');
    		}

    		redirect("other_work/teacher_dining_sign?{$_SERVER['QUERY_STRING']}");
    	}

    	$start_date = $this->input->get('start_date');
    	$end_date = $this->input->get('end_date');

		if(isset($start_date) && isset($end_date)){
			$this->data['sess_start_date'] = $start_date;
			$this->data['sess_end_date'] = $end_date;
		} else {
			$this->data['sess_start_date'] = date('Y-m-d');
			$this->data['sess_end_date'] = date('Y-m-d');
		}

		$page = $this->data['filter']['page'];
        $rows = 20;
        $this->data['filter']['offset'] = $offset = ($page -1) * $rows;

        $total_list = $this->Teacher_dining_model->getTotalList($this->data['sess_start_date'], $this->data['sess_end_date']);
        $this->data['filter']['total'] = $total = count($total_list);
        $list = $this->Teacher_dining_model->getList($this->data['sess_start_date'], $this->data['sess_end_date'], $rows, $offset);

        $report = array();
        $report['total_bce'][1][1] = 0; //BCE區紙盒葷
		$report['total_bce'][1][2] = 0; //BCE區紙盒素
		$report['total_bce'][2][1] = 0; //BCE區鐵盒葷
		$report['total_bce'][2][2] = 0; //BCE區鐵盒素
		$report['total_bce'][3][1] = 0; //BCE區餐盤葷
		$report['total_bce'][3][2] = 0; //BCE區餐盤素
		$report['total_r'][1][1] = 0; //餐廳紙盒葷
		$report['total_r'][1][2] = 0; //餐廳紙盒素
		$report['total_r'][2][1] = 0; //餐廳鐵盒葷
		$report['total_r'][2][2] = 0; //餐廳鐵盒素
		$report['total_r'][3][1] = 0; //餐廳餐盤葷
		$report['total_r'][3][2] = 0; //餐廳餐盤素
        for($i=0;$i<count($total_list);$i++){
        	if(!empty($total_list[$i]['place']) && !empty($total_list[$i]['way']) && !empty($total_list[$i]['food_type']) && $total_list[$i]['num'] > 0){
        		if(isset($report[$total_list[$i]['place']][$total_list[$i]['way']][$total_list[$i]['food_type']])){
					$report[$total_list[$i]['place']][$total_list[$i]['way']][$total_list[$i]['food_type']] += $total_list[$i]['num']; 
				} else {
					$report[$total_list[$i]['place']][$total_list[$i]['way']][$total_list[$i]['food_type']] = $total_list[$i]['num'];
				}

				if($total_list[$i]['place'] == 'B' || $total_list[$i]['place'] == 'C' || $total_list[$i]['place'] == 'E'){
					if($total_list[$i]['way'] == 1){
						if($total_list[$i]['food_type'] == 1){
							$report['total_bce'][1][1] += $total_list[$i]['num'];
						} else if($total_list[$i]['food_type'] == 2){
							$report['total_bce'][1][2] += $total_list[$i]['num'];
						}
					}
					if($total_list[$i]['way'] == 2){
						if($total_list[$i]['food_type'] == 1){
							$report['total_bce'][2][1] += $total_list[$i]['num'];
						} else if($total_list[$i]['food_type'] == 2){
							$report['total_bce'][2][2] += $total_list[$i]['num'];
						}
					}
					if($total_list[$i]['way'] == 3){
						if($total_list[$i]['food_type'] == 1){
							$report['total_bce'][3][1] += $total_list[$i]['num'];
						} else if($total_list[$i]['food_type'] == 2){
							$report['total_bce'][3][2] += $total_list[$i]['num'];
						}
					}
				} else if($total_list[$i]['place'] == 'R'){
					if($total_list[$i]['way'] == 1){
						if($total_list[$i]['food_type'] == 1){
							$report['total_r'][1][1] += $total_list[$i]['num'];
						} else if($total_list[$i]['food_type'] == 2){
							$report['total_r'][1][2] += $total_list[$i]['num'];
						}
					}
					if($total_list[$i]['way'] == 2){
						if($total_list[$i]['food_type'] == 1){
							$report['total_r'][2][1] += $total_list[$i]['num'];
						} else if($total_list[$i]['food_type'] == 2){
							$report['total_r'][2][2] += $total_list[$i]['num'];
						}
					}
					if($total_list[$i]['way'] == 3){
						if($total_list[$i]['food_type'] == 1){
							$report['total_r'][3][1] += $total_list[$i]['num'];
						} else if($total_list[$i]['food_type'] == 2){
							$report['total_r'][3][2] += $total_list[$i]['num'];
						}
					}
				}
        	}


        }

        for($i=0;$i<count($list);$i++){
        	if($list[$i]['type'] != '講師'){
        		if($list[$i]['type'] == '1'){
        			$list[$i]['type'] = '講師';
        		} else if($list[$i]['type'] == '2'){
        			$list[$i]['type'] = '學員';
        		} else if($list[$i]['type'] == '3'){
        			$list[$i]['type'] = '工作人員';
        		}
        	}

        	if($list[$i]['place'] == 'B'){
    			$list[$i]['place'] = 'B區';
    		} else if($list[$i]['place'] == 'C'){
    			$list[$i]['place'] = 'C區';
    		} else if($list[$i]['place'] == 'E'){
    			$list[$i]['place'] = 'E區';
    		} else if($list[$i]['place'] == 'R'){
    			$list[$i]['place'] = '餐廳';
    		}

    		if($list[$i]['way'] == '1'){
    			$list[$i]['way'] = '紙盒';
    		} else if($list[$i]['way'] == '2'){
    			$list[$i]['way'] = '鐵盒';
    		} else if($list[$i]['way'] == '3'){
    			$list[$i]['way'] = '餐盤';
    		} 

    		if($list[$i]['food_type'] == '1'){
    			$list[$i]['food_type'] = '葷';
    		} else if($list[$i]['food_type'] == '2'){
    			$list[$i]['food_type'] = '素';
    		}

    		$list[$i]['course_date'] = date('Y-m-d',strtotime($list[$i]['course_date']));
        }

        if(isset($_GET['iscsv']) && $_GET['iscsv'] == 1){
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

	        $sheet->mergeCells('A1:B1');
	        $sheet->setCellValue('A1','('.$this->data['sess_start_date'].'~'.$this->data['sess_end_date'].')便當登記一覽表');

	        $sheet->setCellValue('A3','便當日期');
	        $sheet->setCellValue('B3','班期名稱');
	        $sheet->setCellValue('C3','教室');
	        $sheet->setCellValue('D3','用餐人員類別');
	        $sheet->setCellValue('E3','數量');
	        $sheet->setCellValue('F3','用餐人員姓名');
	        $sheet->setCellValue('G3','備註');
	        $sheet->setCellValue('H3','申請人');

	        $excel_rows = 4;
	        $excel_total = 0;
	        for($i=0;$i<count($total_list);$i++){
	        	if(empty($total_list[$i]['place'])){
	        		continue;
	        	}

	        	$total_list[$i]['course_date'] = date('Y/m/d', strtotime($total_list[$i]['course_date']));

	        	if($total_list[$i]['type'] == '1'){
        			$total_list[$i]['type'] = '講師';
        		} else if($total_list[$i]['type'] == '2'){
        			$total_list[$i]['type'] = '學員';
        		} else if($total_list[$i]['type'] == '3'){
        			$total_list[$i]['type'] = '工作人員';
        		}

	        	$sheet->setCellValue('A'.$excel_rows, $total_list[$i]['course_date']);
		        $sheet->setCellValue('B'.$excel_rows, $total_list[$i]['class_name']);
		        $sheet->setCellValue('C'.$excel_rows, $total_list[$i]['room_sname']);
		        $sheet->setCellValue('D'.$excel_rows, $total_list[$i]['type']);
		        $sheet->setCellValue('E'.$excel_rows, $total_list[$i]['num']);
		        $sheet->setCellValue('F'.$excel_rows, $total_list[$i]['teacher_name']);
		        $sheet->setCellValue('G'.$excel_rows, $total_list[$i]['remark']);
		        $sheet->setCellValue('H'.$excel_rows, $total_list[$i]['worker_name']);

		        $excel_rows++;
		        $excel_total += $total_list[$i]['num']; 
	        }


			$style_array = array(
                'borders' => array(
                    'allborders' => array(
                        'style' => \PHPExcel_Style_Border::BORDER_THIN
                    )
                )                                                                                                                                                       );
            $sheet->getStyle('A3:H'.($excel_rows-1))->applyFromArray($style_array);

	        $sheet->setCellValue('D'.$excel_rows, '小計');
	        $sheet->setCellValue('E'.$excel_rows, $excel_total);
	        $excel_rows++;
	        $sheet->setCellValue('D'.$excel_rows, '金額');
	        $sheet->setCellValue('E'.$excel_rows, ($excel_total*80));

	        $sheet->getStyle('D'.($excel_rows-1).':E'.$excel_rows)->applyFromArray($style_array);

	        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

	        header('Content-Type:application/csv;charset=UTF-8');
	        header("Pragma: public");
	        header("Expires: 0");
	        header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
	        header("Content-Type:application/force-download");
	        header("Content-Type:application/vnd.ms-excel;");
	        header("Content-Type:application/octet-stream");
	        header('Content-Disposition: attachment;filename="'.generatorRandom(10).'.xlsx"');
	        header("Content-Transfer-Encoding:binary");
	        $objWriter->save('php://output');

	        exit;
        }

        $this->load->library('pagination');
        $config['base_url'] = base_url("other_work/teacher_dining_sign?". $this->getQueryString(array(), array('page')));
        $config['total_rows'] = $total;
        $config['per_page'] = $rows;
        $this->pagination->initialize($config);

    	$this->data['link_data_add'] = base_url("other_work/teacher_dining_sign/add");
    	$this->data['list'] = $list;
    	$this->data['report'] = $report;
        $this->layout->view('other_work/teacher_dining_sign/list', $this->data);
    }

    public function add($reload = 0){
    	if($this->input->post('mode') == 'add' && !empty($this->input->post('use_date')) && !empty($this->input->post('class_name')) && !empty($this->input->post('room_id')) && !empty($this->input->post('dining_name')) && !empty($this->input->post('type')) && !empty($this->input->post('place')) && !empty($this->input->post('way')) && !empty($this->input->post('food_type')) && !empty($this->input->post('num'))){

    		$insert_data = array();
    		$insert_data['use_date'] = $this->input->post('use_date');
    		$insert_data['class_name'] = $this->input->post('class_name');
    		$insert_data['room_id'] = $this->input->post('room_id');
    		$insert_data['dining_name'] = $this->input->post('dining_name');
    		$insert_data['type'] = $this->input->post('type');
    		$insert_data['place'] = $this->input->post('place');
    		$insert_data['way'] = $this->input->post('way');
    		$insert_data['food_type'] = $this->input->post('food_type');
    		$insert_data['num'] = $this->input->post('num');
    		$insert_data['remark'] = $this->input->post('remark');
    		$insert_data['creator'] = $this->flags->user['idno'];
    		$insert_data['modify_time'] = date('Y-m-d H:i:s');

    		$chk_insert = $this->Teacher_dining_model->add($insert_data);

    		if($chk_insert){
    			$this->setAlert(1, '新增成功');
    			redirect("other_work/teacher_dining_sign/add/1");
    		} else {
    			$this->setAlert(2, '新增失敗');
    			redirect("other_work/teacher_dining_sign/add/0");
    		}
    	}

    	$this->data['reload'] = $reload;
    	$this->layout->view('other_work/teacher_dining_sign/add', $this->data);
    }

}

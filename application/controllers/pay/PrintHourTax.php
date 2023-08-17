<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class PrintHourTax extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        //ini_set("display_errors", "On"); 

		$this->load->model(['hour_app_model', 'pay/Print_pay_list_model', 'create_class/progress_model', 'require_model']);
        $this->load->model('create_class/print_schedule_model');
    }

    public function index()
    {

    	if ($export = $this->input->get('export')){
    		if ($export == 'hourPdf'){
    			$this->hourPdf();
    		}elseif ($export == 'checkCsv'){
    			$this->checkCsv();
    		}
    	}

        $now = new DateTime();
        $now->modify("-".($now->format('w')-1).'day');
        $monday = $now->format('Y-m-d');
        $now->modify("+6 day");
        $sunday = $now->format('Y-m-d');

        $queryData = [];
    	$queryData['sdate'] = $this->getFilterData('sdate', $monday);
        $queryData['edate'] = $this->getFilterData('edate', $sunday);

    	$this->data['list'] = $this->hour_app_model->getList($queryData);

    	$app_seqs = array_map(function($class){
    		return $class->app_seq;
    	}, $this->data['list']);

    	
        if (count($app_seqs) > 0){
            $this->data['hour_traffic_taxs'] = $this->Print_pay_list_model->getTaxByAppSeqs($app_seqs);
         
            $this->data['taxIsFinish'] = array_map(function($taxGroup){
                $isFinish = true;

                foreach ($taxGroup as $tax){
                    if ($tax->ischeck == 'N' || $tax->status != '請款確認'){
                        $isFinish = null;
                    }
                }

                return $isFinish;
            },$this->data['hour_traffic_taxs']);

        }else{
            $this->data['hour_traffic_taxs'] = [];
        }

    	$this->layout->view('pay/printHourTax/index', $this->data);
    }

    // 產生鐘點費核銷清冊(pdf)
    public function hourPdf()
    {
        $this->load->library('pdf/PHP_TCPDF');

        $this->load->helper("progress");
    	$app_seqs = $this->input->get('app_seq');
        if (empty($app_seqs)) return false;

    	$hour_traffic_taxs = $this->Print_pay_list_model->getTaxByAppSeqs($app_seqs);

        

    	$requires = array_map(function($taxGroup){
    		return [
    			'year' => $taxGroup[0]->year,
    			'class_no' => $taxGroup[0]->class_no,
    			'term' => $taxGroup[0]->term
    		];
    	}, $hour_traffic_taxs);

        $this->pdf = new PHP_TCPDF();
        $this->pdf->setPrintHeader(false); //不要頁首
        $this->pdf->setPrintFooter(false); //不要頁尾
        $countList = 0;
		foreach ($requires as $app_seq => $require){
            $taxs = $hour_traffic_taxs[$app_seq];
            $class_info = $this->progress_model->getRequire($require);

            $online_schedule = $this->progress_model->getOnlineSchedule($require);
            $phy_schedule = $this->progress_model->getPhySchedule($require); 

            $use_dates = array_column($phy_schedule, 'use_date');

            $use_sdate = array_shift($use_dates);
            $use_edate = end($use_dates);

            $this->pdf->AddPage();

            $html = "<h2 style=\"text-align:center\">臺北市政府公務人員訓練處  請款清冊(流水號{$app_seq})</h2>";
            $html .= "<h2 style=\"text-align:center\">{$taxs[0]->year}年度 {$taxs[0]->class_name} 第{$taxs[0]->term}期</h2>";

            $this->pdf->writeHTML($html, true, false, false, false, '');
            $this->pdf->MultiCell(63, 10, "查詢日期:".$this->input->get('sdate').'~'.$this->input->get('edate'), 0, 'L', 0, 0, '', '', true, 0, false, true, 10, 'M');
            $this->pdf->MultiCell(64, 10, "開課日期:{$use_sdate}~{$use_edate}", 0, 'C', 0, 0, '', '', true, 0, false, true, 10, 'M');
            $this->pdf->MultiCell(65, 10, "類別：{$class_info->description}", 0, 'R', 0, 1, '', '', true, 0, false, true, 10, 'M');

            $this->pdf->SetFont('', 'B');

            $header = array('上課日期', "姓名/公司\nID/編號", "銀行/郵局分行\n帳號(帳戶名稱)", "地址\nemail", "時數", "單價", "鐘點費", "交通費", "合計", "類別", "資料確認欄");
            $w = array(15, 25, 35, 30, 10, 10, 15, 15, 15, 10, 10);

            $this->pdf->SetFillColor(255, 255, 127);

            // 表格標題
            $num_headers = count($header);
            for($i = 0; $i < $num_headers; ++$i) {
                $this->pdf->MultiCell($w[$i], 12, $header[$i], 1, 'C', 0, 0, '', '', true, 0, false, true, 12, 'M');
            }

            $this->pdf->Ln();
            
            $this->pdf->SetFont('');
            // Data
            $scheduleDate = $hour_traffic_taxs[$app_seq][0]->u_date;
            $total = [
                'unit_hour_fee' => 0,
                'hour_fee' => 0,
                'traffic_fee' => 0,
                'subtotal' => 0
            ];

            $countList = 0;
            foreach($hour_traffic_taxs[$app_seq] as $tax) {
                $countList++;
                if($countList >= 8){
                    $this->pdf->AddPage();
                    $countList = 0;
                }

                $tax->unit_hour_fee = ($tax->unit_hour_fee < 0) ? 0 : $tax->unit_hour_fee;
                $tax->hour_fee = ($tax->hour_fee < 0) ? 0 : $tax->hour_fee;
                $tax->traffic_fee = ($tax->traffic_fee < 0) ? 0 : $tax->traffic_fee;
                $tax->subtotal = ($tax->subtotal < 0) ? 0 : $tax->subtotal;

                $tax->remark = ($tax->remark == "無") ? "" : $tax->remark;
                $this->pdf->MultiCell($w[0], 30, $tax->u_date, 1, 'C', 0, 0, '', '', true, 0, false, true, 30, 'M');
                $this->pdf->MultiCell($w[1], 30, $tax->teacher_name."\n".$tax->teacher_id, 1, 'L', 0, 0, '', '', true, 0, false, true, 30, 'M');
                $this->pdf->MultiCell($w[2], 30, "{$tax->bank_name}{$tax->teacher_account}({$tax->teacher_acct_name})"."<br><font style=\"font-size:16px;font-weight:bold;\">".$tax->remark."</font>", 1, 'L', 0, 0, '', '', true, 0, true, true, 30, 'M');
                $this->pdf->MultiCell($w[3], 30, $tax->teacher_addr."<br>".$tax->email, 1, 'L', 0, 0, '', '', true, 0, true, true, 30, 'M');
                $this->pdf->MultiCell($w[4], 30, $tax->hrs, 1, 'C', 0, 0, '', '', true, 0, false, true, 30, 'M');
                $this->pdf->MultiCell($w[5], 30, number_format($tax->unit_hour_fee), 1, 'C', 0, 0, '', '', true, 0, false, true, 30, 'M');
                $this->pdf->MultiCell($w[6], 30, number_format($tax->hour_fee), 1, 'C', 0, 0, '', '', true, 0, false, true, 30, 'M');
                $this->pdf->MultiCell($w[7], 30, number_format($tax->traffic_fee), 1, 'C', 0, 0, '', '', true, 0, false, true, 30, 'M');
                $this->pdf->MultiCell($w[8], 30, number_format($tax->subtotal), 1, 'C', 0, 0, '', '', true, 0, false, true, 30, 'M');
                $this->pdf->MultiCell($w[9], 30, $tax->description, 1, 'C', 0, 0, '', '', true, 0, false, true, 30, 'M');
                $ischeck = ($tax->ischeck == "Y") ? '已確認' : '未確認';
                $this->pdf->MultiCell($w[10], 30, $ischeck, 1, 'C', 0, 0, '', '', true, 0, false, true, 30, 'M');
                $this->pdf->Ln();

                $total['unit_hour_fee'] += $tax->unit_hour_fee;
                $total['hour_fee'] += $tax->hour_fee;
                $total['traffic_fee'] += $tax->traffic_fee;
                $total['subtotal'] += $tax->subtotal;
            }

            $this->pdf->MultiCell(115, 10, '總計', 1, 'R', 0, 0, '', '', true, 0, false, true, 10, 'M');
            $this->pdf->MultiCell($w[5], 10, number_format($total['unit_hour_fee']), 1, 'C', 0, 0, '', '', true, 0, false, true, 10, 'M');
            $this->pdf->MultiCell($w[6], 10, number_format($total['hour_fee']), 1, 'C', 0, 0, '', '', true, 0, false, true, 10, 'M');
            $this->pdf->MultiCell($w[7], 10, number_format($total['traffic_fee']), 1, 'C', 0, 0, '', '', true, 0, false, true, 10, 'M');
            $this->pdf->MultiCell($w[8], 10, number_format($total['subtotal']), 1, 'C', 0, 0, '', '', true, 0, false, true, 10, 'M');
            $this->pdf->MultiCell($w[9], 10, '', 1, 'C', 0, 0, '', '', true, 0, false, true, 10, 'M');
            $this->pdf->MultiCell($w[10], 10, '', 1, 'C', 0, 0, '', '', true, 0, false, true, 10, 'M');
            $this->pdf->Ln();

            $html = $this->createTaxList($app_seq, $hour_traffic_taxs[$app_seq]);
			$html = $this->createScheduleHtml($class_info, $online_schedule, $phy_schedule, $scheduleDate);

            $this->pdf->writeHTML($html, true, false, false, false, '');
    	}

		// -----------------------------------------------------------------------------

		$this->pdf->Output('鐘點費簽名清冊及課表.pdf', 'I');

    	exit;
    }

    // 產生鐘點費簽名清冊及課表 (csv)
    public function checkCsv()
    {
        $queryData = $this->getFilterData(['sdate', 'edate']);

        if (!(isset($queryData['sdate']) && isset($queryData['edate']))){
            return false;
        }

        $export_datas = $this->hour_app_model->getByGroup($queryData);

        $this->load->library('excel');   
        $objPHPExcel = new PHPExcel();   
        $objPHPExcel->setActiveSheetIndex(0);   
        // set Header   
        $objPHPExcel->getActiveSheet()->SetCellValue('A1', $queryData['sdate'].'至'.$queryData['edate'].'研習鐘點費核銷冊'); 

        $objPHPExcel->getActiveSheet()->SetCellValue('A2', '序號');   
        $objPHPExcel->getActiveSheet()->SetCellValue('B2', '班期名稱');   
        $objPHPExcel->getActiveSheet()->SetCellValue('C2', '期別');   
        $objPHPExcel->getActiveSheet()->SetCellValue('D2', '教室');   
        $objPHPExcel->getActiveSheet()->SetCellValue('E2', '課程名稱');   
        $objPHPExcel->getActiveSheet()->SetCellValue('F2', '時間');   
        $objPHPExcel->getActiveSheet()->SetCellValue('G2', '講師');   
        $objPHPExcel->getActiveSheet()->SetCellValue('H2', '上課日期');   
        $objPHPExcel->getActiveSheet()->SetCellValue('I2', '承辦人');   
        $objPHPExcel->getActiveSheet()->SetCellValue('J2', '授課時數'); 
        $objPHPExcel->getActiveSheet()->SetCellValue('K2', '單價'); 
        $objPHPExcel->getActiveSheet()->SetCellValue('L2', '鐘點費');  
        $objPHPExcel->getActiveSheet()->SetCellValue('M2', '交通費');  
        $objPHPExcel->getActiveSheet()->SetCellValue('N2', '合計');  
        $objPHPExcel->getActiveSheet()->SetCellValue('O2', '流水號'); 

        $index = 3;

        $r = array_map(function($data){
            return [
                'year' => $data->year,
                'class_no' => $data->class_no, 
                'term' => $data->term,
            ];
        },$export_datas);

        $requires = array();
        foreach($r as $array)
        {
            if(!in_array($array, $requires))
                $requires[] = $array;
        }

        $firstInfos = $this->require_model->getTeacherFirstSchedule($requires);

        $firstInfoTag = [];
        foreach($firstInfos as $firstInfo){
            $firstInfoTag[$firstInfo->year][$firstInfo->class_no][$firstInfo->term][$firstInfo->teacher_id] = $firstInfo;
        }

        foreach($export_datas as $key => $data){  
            $data->hrs = ($data->hrs <= 0) ? 0 : $data->hrs; 
            $data->unit_hour_fee = ($data->unit_hour_fee <= 0) ? 0 : $data->unit_hour_fee;
            $data->hour_fee = ($data->hour_fee <= 0) ? 0 : $data->hour_fee;
            $data->traffic_fee = ($data->traffic_fee <= 0) ? 0 : $data->traffic_fee;

            $firstInfo = (isset($firstInfoTag[$data->year][$data->class_no][$data->term][$data->teacher_id])) ? $firstInfoTag[$data->year][$data->class_no][$data->term][$data->teacher_id] : null;
            $objPHPExcel->getActiveSheet()->SetCellValue('A'.$index, $key + 1);   
            $objPHPExcel->getActiveSheet()->SetCellValue('B'.$index, $data->class_name);   
            $objPHPExcel->getActiveSheet()->SetCellValue('C'.$index, $data->term);   


            $objPHPExcel->getActiveSheet()->SetCellValue('D'.$index, $data->room_name);   
            $objPHPExcel->getActiveSheet()->SetCellValue('E'.$index, $data->course_name);   
            $objPHPExcel->getActiveSheet()->SetCellValue('F'.$index, $data->from_time.'~'.$data->to_time);  


            $objPHPExcel->getActiveSheet()->SetCellValue('G'.$index, $data->teacher_name);   
            $use_date = new DateTime($data->use_date);
            $use_date = $use_date->format('Y-m-d');
            $objPHPExcel->getActiveSheet()->SetCellValue('H'.$index, $use_date);   
            $objPHPExcel->getActiveSheet()->SetCellValue('I'.$index, $data->worker_name);   
            $objPHPExcel->getActiveSheet()->SetCellValue('J'.$index, $data->hrs); 
            $objPHPExcel->getActiveSheet()->SetCellValue('K'.$index, $data->unit_hour_fee); 
            $objPHPExcel->getActiveSheet()->SetCellValue('L'.$index, $data->hour_fee);  
            $objPHPExcel->getActiveSheet()->SetCellValue('M'.$index, $data->traffic_fee);  
            $objPHPExcel->getActiveSheet()->SetCellValue('N'.$index, $data->subtotal);  
            $objPHPExcel->getActiveSheet()->SetCellValue('O'.$index, $data->app_seq); 
            $index++; 
        }   

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'CSV');

        $filename = $queryData['sdate'].'至'.$queryData['edate'].'研習鐘點費核銷冊.csv';
        // download file   
        header("Content-Type: text/x-csv");  
        header('Content-Disposition: attachment; filename="'.$filename.'"');
        echo "\xEF\xBB\xBF";
        $objWriter->save('php://output'); 
        exit;

    }

    public function createScheduleHtml($class_info, $online_schedule, $phy_schedule, $scheduleDate)
    {
        $researcher = $this->print_schedule_model->getResearcher([(array)$class_info])[0];
        $html = "";
        // 線上課程課表
        // if (!empty($online_schedule)){
        //     $oneline_schedule_table = getOnlineScheduleHtml($online_schedule);
        //     // 課程表
        //     $html .= "
        //         <div align=\"center\" style=\"width:100%\">臺北市政府公務人員訓練處&nbsp;&nbsp;&nbsp;&nbsp;線上課程表</div>
        //         <div align=\"center\" style=\"width:100%\">".$class_info->year."年度　".$class_info->class_name."第".$class_info->term."期</div>
        //         <div align=\"center\" style=\"width:100%\">班期代碼：".$class_info->class_no."</div>";       
        //     $html .= $oneline_schedule_table;      
        //     $this->pdf->writeHTML($html, true, false, false, false, '');     
        // }

      

        // dd($class_info);
        $weekChinese = ['日','一','二' ,'三' ,'四' ,'五' ,'六'];

        $last_date = null;
        $classRoomIsSame = true;

        $lastRoom = null;
        $lastSchedule = null;
        $mergeSchedule = [];

        $today = new DateTime();
        $today = $today->format('Y-m-d');


        foreach ($phy_schedule as $key => $schedule){
            if ($scheduleDate != $schedule->use_date){
                continue;
            }

            if ($lastRoom == null){
                $lastRoom = $schedule->room_name;
            }

            if ($lastRoom != $schedule->room_name){
                $classRoomIsSame = false;
            }

            if ($lastSchedule == null){
                $lastSchedule = $schedule;
                $lastSchedule->teachers = [];
                $lastSchedule->teachers[] = $lastSchedule->name;
                $lastSchedule->room_names = [];
                $lastSchedule->room_names[] = $lastSchedule->room_name;

                continue;
            }

            if ($lastSchedule->use_date == $schedule->use_date && 
                $lastSchedule->description == $schedule->description && 
                $lastSchedule->from_time == $schedule->from_time &&
                $lastSchedule->to_time == $schedule->to_time
            ){
                $lastSchedule->teachers[] = $schedule->name;
                $lastSchedule->room_names[] = $schedule->room_name;
            }else{
                $mergeSchedule[] = $lastSchedule;
                $lastSchedule = $schedule;
                $lastSchedule->teachers = [];
                $lastSchedule->teachers[] = $lastSchedule->name;
                $lastSchedule->room_names = [];
                $lastSchedule->room_names[] = $lastSchedule->room_name;
            }
        }
        
        // 最後一筆沒有觸發加入 迴圈後補上
        $mergeSchedule[] = $lastSchedule;  

        if (empty($mergeSchedule)) $classRoomIsSame = false;

        // $classRoomIsSame = false;
        if ($classRoomIsSame){
            $this->pdf->MultiCell(190, 10, "{$class_info->year} 年度 {$class_info->class_name} 第 {$class_info->term} 期", 0, 'C', 0, 1, '', '', true, 0, false, true, 10, 'M');
            $this->pdf->MultiCell(190, 10, "{$class_info->class_no} 上課地點:{$lastRoom}", 0, 'L', 0, 1, '', '', true, 0, false, true, 10, 'M');

            $this->pdf->MultiCell(25, 10, "日期", 1, 'C', 0, 0, '', '', true, 0, false, true, 10, 'M');
            $this->pdf->MultiCell(20, 10, "星期", 1, 'C', 0, 0, '', '', true, 0, false, true, 10, 'M');
            $this->pdf->MultiCell(25, 10, "時間", 1, 'C', 0, 0, '', '', true, 0, false, true, 10, 'M');
            $this->pdf->MultiCell(80, 10, "課程", 1, 'C', 0, 0, '', '', true, 0, false, true, 10, 'M');
            $this->pdf->MultiCell(40, 10, "講座", 1, 'C', 0, 1, '', '', true, 0, false, true, 10, 'M');
        }else{
            $this->pdf->MultiCell(190, 10, "{$class_info->year} 年度 {$class_info->class_name} 第 {$class_info->term} 期", 0, 'C', 0, 1, '', '', true, 0, false, true, 10, 'M');
            $this->pdf->MultiCell(190, 10, "{$class_info->class_no}", 0, 'L', 0, 1, '', '', true, 0, false, true, 10, 'M');

            $this->pdf->MultiCell(25, 10, "日期", 1, 'C', 0, 0, '', '', true, 0, false, true, 10, 'M');
            $this->pdf->MultiCell(20, 10, "星期", 1, 'C', 0, 0, '', '', true, 0, false, true, 10, 'M');
            $this->pdf->MultiCell(32, 10, "時間", 1, 'C', 0, 0, '', '', true, 0, false, true, 10, 'M');
            $this->pdf->MultiCell(49, 10, "課程", 1, 'C', 0, 0, '', '', true, 0, false, true, 10, 'M');
            $this->pdf->MultiCell(32, 10, "講座", 1, 'C', 0, 0, '', '', true, 0, false, true, 10, 'M');
            $this->pdf->MultiCell(32, 10, "上課地點", 1, 'C', 0, 1, '', '', true, 0, false, true, 10, 'M');               
        }

        foreach ($mergeSchedule as $schedule){
            if (!empty($schedule->use_date)){
                $use_date = new DateTime($schedule->use_date);
            }else{
                $use_date = null;
            }
            
            $formatUseDate = $schedule->use_date;
            $weekText = $weekChinese[$use_date->format('w')];
            if ($last_date == $schedule->use_date){
                $formatUseDate = null;
                $weekText = null;
            }else{
                $last_date = $schedule->use_date;
            }

            $rowHeight = count($schedule->teachers) * 10;

            if ($classRoomIsSame){
                $this->pdf->MultiCell(25, $rowHeight, $formatUseDate, 1, 'C', 0, 0, '', '', true, 0, false, true, $rowHeight, 'M');
                $this->pdf->MultiCell(20, $rowHeight, $weekText, 1, 'C', 0, 0, '', '', true, 0, false, true, $rowHeight, 'M');
                $this->pdf->MultiCell(25, $rowHeight, $schedule->from_time.'~'.$schedule->to_time, 1, 'C', 0, 0, '', '', true, 0, false, true, $rowHeight, 'M');
                $this->pdf->MultiCell(80, $rowHeight, $schedule->description, 1, 'C', 0, 0, '', '', true, 0, false, true, $rowHeight, 'M');
                $this->pdf->MultiCell(40, $rowHeight, join("\n", $schedule->teachers), 1, 'C', 0, 1, '', '', true, 0, false, true, $rowHeight, 'M');
            }else{
                $this->pdf->MultiCell(25, $rowHeight, $formatUseDate, 1, 'C', 0, 0, '', '', true, 0, false, true, $rowHeight, 'M');
                $this->pdf->MultiCell(20, $rowHeight, $weekText, 1, 'C', 0, 0, '', '', true, 0, false, true, $rowHeight, 'M');
                $this->pdf->MultiCell(32, $rowHeight, $schedule->from_time.'~'.$schedule->to_time, 1, 'C', 0, 0, '', '', true, 0, false, true, $rowHeight, 'M');
                $this->pdf->MultiCell(49, $rowHeight, $schedule->description, 1, 'C', 0, 0, '', '', true, 0, false, true, $rowHeight, 'M');
                $this->pdf->MultiCell(32, $rowHeight, join("\n", $schedule->teachers), 1, 'C', 0, 0, '', '', true, 0, false, true, $rowHeight, 'M');
                $this->pdf->MultiCell(32, $rowHeight, join("\n", $schedule->room_names), 1, 'C', 0, 1, '', '', true, 0, false, true, $rowHeight, 'M');

            }
        }

        // dd($researcher);
        $remark = "一、承辦人：{$researcher['name']}(分機 {$researcher['add_val1']})、代理人：{$researcher['description']}(分機 {$researcher['add_val2']})。\n二、研習人數 {$researcher['sel_number']}人；研習總時數 ".($researcher['range_real'] + $researcher['range_internet'])."小時。";
        $this->pdf->MultiCell(190, 10, $remark, 1, 'L', 0, 1, '', '', true, 0, false, true, 10, 'M');             
        // 


		// // 實體課程課表
		// if (!empty($class_info->course_schedule_file_path)){
		// 	if (file_exists(DIR_UPLOAD_COURSE_SCHEDULE.$class_info->course_schedule_file_path)){
		// 		$html .= "<img src='".base_url("files/upload_course_schedule/".$class_info->course_schedule_file_path)."'>";
		// 	}
		// }else if (!empty($phy_schedule)){
		// 	$phy_schedule_table = getPhyScheduleHtml($phy_schedule);
		// 	// $html .= "<div align=\"center\" style='width:100%'>臺北市政府公務人員訓練處&nbsp;&nbsp;&nbsp;&nbsp;課程表</div>";
  //           /*
		// 	$html .= "
		// 		<div align=\"center\" style='width:100%'>".$class_info->year."年度　".$class_info->class_name."第".$class_info->term."期</div>
		// 		<div align='left' style='width:100%'>班期代碼：".$class_info->class_no."</div>";
  //           */
		// 	$html .= $phy_schedule_table;
		
		// }    
		// return $html;	
    }

    public function createTaxList($app_seq, $taxs)
    {

        $html = "<div style=\"text-align:center\">臺北市政府公務人員訓練處  請款清冊(流水號{$app_seq})</div>";
        $html .= "<div style=\"text-align:center\">{$taxs[0]->year}年度 {$taxs[0]->class_name} 第{$taxs[0]->term}期</div>";
        $html .= "<div>開課日期:{$taxs[0]->sdate}~{$taxs[0]->edate}</div>";

        $taxHtml = "";

        foreach ($taxs as $tax){

            $taxHtml .= "<tr><td>{$tax->u_date}</td>";
            $taxHtml .= "<td width=\"80\">{$tax->teacher_name}<br>$tax->teacher_id</td>";
            $taxHtml .= "<td width=\"100\">{$tax->bank_name}{$tax->teacher_account}({$tax->teacher_acct_name})</td>";
            $taxHtml .= "<td width=\"100\">{$tax->teacher_addr}<br>{$tax->email}</td>";
            $taxHtml .= "<td width=\"40\">{$tax->hrs}</td>";
            $taxHtml .= "<td width=\"42\">".number_format($tax->unit_hour_fee)."</td>";
            $taxHtml .= "<td>".number_format($tax->hour_fee)."</td>";
            $taxHtml .= "<td>".number_format($tax->traffic_fee)."</td>";
            $taxHtml .= "<td width=\"40\">".number_format($tax->subtotal)."</td>";
            $taxHtml .= "<td width=\"42\">{$tax->description}</td>";
            $ischeck = ($tax->ischeck == "Y") ? '已確認' : '未確認';
            $taxHtml .= "<td width=\"50\"><font color=\"red\">{$ischeck}</font></td></tr>";
        }

        $html .= "<table style=\"width:100%; background-color: #ECF0F5;\" border=\"1\">
                    <thead>
                        <tr>
                        <th>開課日期</th>
                        <th width=\"80\">姓名/公司<br>ID/編號</th>
                        <th width=\"100\">銀行/郵局分行<br>帳號(帳戶名稱)</th>
                        <th width=\"100\">地址<br>email</th>
                        <th width=\"40\">時數</th>
                        <th width=\"42\">單價</th>
                        <th>鐘點費</th>
                        <th>交通費</th>
                        <th width=\"40\">合計</th>
                        <th width=\"42\">備註</th>
                        <th width=\"50\">資料確認欄</th>
                        </tr>
                    </thead>
                    <tbody>
                    {$taxHtml}
                    </tbody>
                 </table>"; 

        return $html;
    }
}
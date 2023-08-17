<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Classroom_usage_list extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        if ($this->flags->is_login === FALSE) {
            redirect(base_url('welcome'));
        }

        $this->load->model('planning/booking_place_model');
        $this->load->model('data/place_category_model');
        $this->load->model('data/reservation_time_model');

        $this->data['choices']['room_type'] = $this->place_category_model->getChoices();
        $this->data['choices']['time_list'] = $this->reservation_time_model->getChoices();

        if (empty($this->data['filter']['start_date'])) {
            //$this->data['filter']['start_date'] = date('Y-m-d', time() - (86400 * 7));
            $this->data['filter']['start_date'] = date('Y-m-d', mktime(0, 0, 0,date('m'), date('d')-date('w')+1));
        }

        if (empty($this->data['filter']['end_date'])) {
            //$this->data['filter']['end_date'] = date('Y-m-d', time() );
            $this->data['filter']['end_date'] = date('Y-m-d', mktime(0, 0, 0,date('m'), date('d')-date('w')+7));
        }
        if (empty($this->data['filter']['room_type'])) {
            $this->data['filter']['room_type'] = '';
        }
        if (empty($this->data['filter']['room'])) {
            $this->data['filter']['room'] = '';
        }
        if (empty($this->data['filter']['class_room_type_B'])) {
            $this->data['filter']['class_room_type_B'] = '';
        }
        if (empty($this->data['filter']['class_room_type_C'])) {
            $this->data['filter']['class_room_type_C'] = '';
        }
        if (empty($this->data['filter']['class_room_type_E'])) {
            $this->data['filter']['class_room_type_E'] = '';
        }
        if (empty($this->data['filter']['red_class'])) {
            $this->data['filter']['red_class'] = '';
        }
        if (empty($this->data['filter']['only_time'])) {
            $this->data['filter']['only_time'] = '';
        }
    }

    public function index()
    {
        $this->data['page_name'] = 'index';
        $conditions = array();
		if ($this->data['filter']['start_date'] != '') {
            $conditions['start_date'] = $this->data['filter']['start_date'];
        }
        if ($this->data['filter']['end_date'] != '') {
            $conditions['end_date'] = $this->data['filter']['end_date'];
        }
        if ($this->data['filter']['class_room_type_B'] != '') {
            $conditions['class_room_type']['B'] = $this->data['filter']['class_room_type_B'];
        }
        if ($this->data['filter']['class_room_type_C'] != '') {
            $conditions['class_room_type']['C'] = $this->data['filter']['class_room_type_C'];
        }
        if ($this->data['filter']['class_room_type_E'] != '') {
            $conditions['class_room_type']['E'] = $this->data['filter']['class_room_type_E'];
        }
        if ($this->data['filter']['red_class'] != '') {
            $conditions['red_class'] = $this->data['filter']['red_class'];
        }
        if ($this->data['filter']['only_time'] != '') {
            $conditions['only_time'] = $this->data['filter']['only_time'];
        }

        if ($this->data['filter']['room_type'] != '') {
            $conditions['cat_id'] = $this->data['filter']['room_type'];
            $this->data['choices']['room'] = $this->booking_place_model->get_room($this->data['filter']['room_type'], TRUE);
        }else{
            $this->data['filter']['room'] = '';
            $this->data['choices']['room'] = array();
            if(isset($this->data['filter']['sort']) && $this->data['filter']['room_type']==''){
                $this->setAlert(3, '請選擇場地類別');
                redirect(base_url("planning/classroom_usage_list"));
            }
        }
        if ($this->data['filter']['room'] != '') {
            $conditions['room_id'] = $this->data['filter']['room'];
        }

        // jd($this->data['filter']);
        if($this->data['filter']['room_type'] != ''){
            if(isDate($conditions['start_date']) && isDate($conditions['end_date'])){
                $days = ((strtotime($conditions['end_date'])-strtotime($conditions['start_date'])) / 86400) + 1;
                if($days>300){
                    $this->setAlert(3, '為避免查詢時間過久,請設定日期區間在300日內');
                    redirect(base_url("planning/classroom_usage_list"));
                }
                // jd($conditions);
                $this->data['list'] = $this->booking_place_model->select_usage_list($conditions);
                    //var_dump($this->data['list']);
                if($this->data['filter']['only_time']=='Y'){
                    for($j=0;$j<count($this->data['list']);$j++){
                        foreach ($this->data['list'][$j] as $key =>& $value) {

                            if(strpos($key,'-')==true){
                                
                                $index=[];
                                for($i=count($value)-1;$i>=1;$i--){
                                    if($value[$i]['CNAME']==$value[$i-1]['CNAME']&&$value[$i]['BOOKING_DATE']==$value[$i-1]['BOOKING_DATE']&&
                                        $value[$i]['CLASS_NAME']==$value[$i-1]['CLASS_NAME']&&$value[$i]['Year']==$value[$i-1]['Year']&&
                                        $value[$i]['TERM']==$value[$i-1]['TERM']&&$value[$i-1]["BTYPE"]=='3'){
                                                $value[$i-1]["TO_TIME"]=$value[$i]["TO_TIME"];
                                                array_push($index,$i);
                                            
                                    }

                                }
                                
                                if(!empty($index)){
                                    for($z=0;$z<count($index);$z++){
                                        unset($value[$index[$z]]);
                                    }
                                    $value=array_values($value);
                                    
                                }
                            
                            }
                        }
                    }
                }
                    

                


            }else{
                $this->setAlert(3, '日期錯誤');
                redirect(base_url("planning/classroom_usage_list"));
            }
        }else{
            $this->data['list'] = array();
        }

		$this->data['link_refresh'] = base_url("planning/classroom_usage_list/");
		$this->data['room_export'] = base_url("planning/classroom_usage_list/export/?{$_SERVER['QUERY_STRING']}");
        $this->data['select_url'] = base_url("planning/classroom_usage_list/?{$_SERVER['QUERY_STRING']}");


        $this->layout->view('planning/classroom_usage_list/list', $this->data);
    }

    public function export()
    {

        $conditions = array();
		if ($this->data['filter']['start_date'] != '') {
            $conditions['start_date'] = $this->data['filter']['start_date'];
        }
        if ($this->data['filter']['end_date'] != '') {
            $conditions['end_date'] = $this->data['filter']['end_date'];
        }
        if ($this->data['filter']['class_room_type_B'] != '') {
            $conditions['class_room_type']['B'] = $this->data['filter']['class_room_type_B'];
        }
        if ($this->data['filter']['class_room_type_C'] != '') {
            $conditions['class_room_type']['C'] = $this->data['filter']['class_room_type_C'];
        }
        if ($this->data['filter']['class_room_type_E'] != '') {
            $conditions['class_room_type']['E'] = $this->data['filter']['class_room_type_E'];
        }
        if ($this->data['filter']['red_class'] != '') {
            $conditions['red_class'] = $this->data['filter']['red_class'];
        }
        if ($this->data['filter']['only_time'] != '') {
            $conditions['only_time'] = $this->data['filter']['only_time'];
        }

        if ($this->data['filter']['room_type'] != '') {
            $conditions['cat_id'] = $this->data['filter']['room_type'];
            $this->data['choices']['room'] = $this->booking_place_model->get_room($this->data['filter']['room_type'], TRUE);
        }else{
            $this->data['filter']['room'] = '';
            $this->data['choices']['room'] = array();
            if(isset($this->data['filter']['sort']) && $this->data['filter']['room_type']==''){
                $this->setAlert(3, '請選擇場地類別');
                redirect(base_url("planning/classroom_usage_list"));
            }
        }
        if ($this->data['filter']['room'] != '') {
            $conditions['room_id'] = $this->data['filter']['room'];
        }

        // jd($this->data['filter']);
        if($this->data['filter']['room_type'] != ''){
            if(isDate($conditions['start_date']) && isDate($conditions['end_date'])){
                $days = ((strtotime($conditions['end_date'])-strtotime($conditions['start_date'])) / 86400) + 1;
                if($days>300){
                    $this->setAlert(3, '為避免查詢時間過久,請設定日期區間在300日內');
                    redirect(base_url("planning/classroom_usage_list"));
                }
                // jd($conditions,1);
                $this->data['list'] = $this->booking_place_model->select_usage_list($conditions);
            }else{
                $this->setAlert(3, '日期錯誤');
                redirect(base_url("planning/classroom_usage_list"));
            }
        }

        // jd($orders);
        // jd($this->data['list'],1);
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
        $sheet->setTitle('clssroom List');

        // 合併儲存格
        // $sheet->mergeCells('A1:D2');

        $row = 1;
        //訂單編號、訂單總額、訂單狀態、訂單成立時間
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $row, '場地');
        $day_list = 1;
        for($i=0; $i<$days; $i++){
        $select_day = date("Y-m-d",strtotime("+{$i} day",strtotime($this->data['filter']['start_date'])));
            $select_day;
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($day_list, $row, $select_day);
            $day_list++;
        }
        $row = 2;
        $objPHPExcel->getActiveSheet() -> getColumnDimension("A") -> setAutoSize(true);
        // jd($this->data['list'],1);
        foreach ($this->data['list'] as $list_row) {
        	$objPHPExcel->getActiveSheet()->getCellByColumnAndRow(0, $row)->setValueExplicit($list_row['room_name'], PHPExcel_Cell_DataType::TYPE_STRING);
        	$word = 'A';
	        for($i=0; $i<$days; $i++){
	        	$select_day = date("Y-m-d",strtotime("+{$i} day",strtotime($this->data['filter']['start_date'])));
	        	$class_detail = '';
		        foreach ($list_row[$select_day] as $class) {
		            $class_detail .= substr($class['FROM_TIME'], 0, 5) .'~'. substr($class['TO_TIME'], 0, 5) . $class['Year'] . '年' . $class['CLASS_NAME'] . '(' . $class['TERM'] . ')' . $class['CNAME']."\n";
		        }
		        $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(($i+1), $row)->setValueExplicit("{$class_detail}", PHPExcel_Cell_DataType::TYPE_STRING);
		        $word++;
		        $objPHPExcel->getActiveSheet() -> getColumnDimension("{$word}") -> setAutoSize(true);
		    }
		    $row++;
		}

        $objPHPExcel->getActiveSheet()->getStyle("A1:{$word}{$row}")->getAlignment()->setWrapText(true);//設定換行
        // $sheet->setCellValue('A1','PHPEXCEL TEST'); //合併後的儲存格，設定時指定左上角那個。
        // $sheet->setCellValue('A3','test');
        // $sheet->setCellValue('B3','test');
        // $sheet->setCellValue('C3','test');
        // $sheet->setCellValue('D3','test');
        // $sheet->setCellValue('A4','test');
        // $sheet->setCellValue('B4','test');
        // $sheet->setCellValue('C4','test');
        // $sheet->setCellValue('D4','test');
        //
        // //設定背景顏色單色
        // $sheet->getStyle('A3:D3')->applyFromArray(
        //  array('fill'     => array(
        //                      'type' => PHPExcel_Style_Fill::FILL_SOLID,
        //                              'color' => array('argb' => 'D1EEEE')
        //      ),
        //      )
        //  );
        //
        //設定漸層背景顏色雙色(灰/白)   經測試，Excel2007才有漸層
        // $sheet->getStyle('A1:Z1')->applyFromArray(
        //     array(
        //     'font' => array(
        //         // 'bold' => true,
        //         // 'size' => 14,
        //     ),
        //     'alignment' => array(
        //         'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
        //     ),
        //     'borders'  => array(
        //         'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN)
        //     ),
        //     'fill'   => array(
        //         'type' => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
        //         'rotation'   => 90,
        //         'startcolor' => array('rgb' => '4AB190'),
        //         'endcolor'   => array('rgb' => 'FFFFFF')
        //     )
        // ));
        // 設定其它工作表
        // $objPHPExcel->createSheet();
        // $objPHPExcel->setActiveSheetIndex(1);
        // $sheet->setTitle('第二張表');
        // $sheet->setCellValue('A3',"test1");
        // $sheet->setCellValue('B3','test2');
        // $objPHPExcel->setActiveSheetIndex(0);

        //=============================================================================================

        //Excel 2007
        // $filename = generatorRandom(10) . '.xlsx';  // 亂數檔名
        // header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        // header('Content-Disposition: attachment;filename="'. $filename .'"');
        // header('Cache-Control: max-age=0');
        // $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

        // //Excel 2003
        // $filename = generatorRandom(10) . '.xls';  // 亂數檔名
        // header('Content-Type: application/vnd.ms-excel');
        // header('Content-Disposition: attachment;filename="'. $filename .'"');
        // header('Cache-Control: max-age=0');
        // $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5'); //Excel 2003 = Excel 5
        //=============================================================================================
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

}

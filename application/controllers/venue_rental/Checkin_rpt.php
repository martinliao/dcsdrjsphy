<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Checkin_rpt extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('venue_rental/room_use_model');

        if ($this->flags->is_login === FALSE) {
			redirect(base_url('welcome'));
		}

		if (!isset($this->data['filter']['page'])) {
            $this->data['filter']['page'] = '1';
        }

		if (empty($this->data['filter']['start_date'])) {
            $this->data['filter']['start_date'] = '';
        }

        if (empty($this->data['filter']['end_date'])) {
            $this->data['filter']['end_date'] = '';
        }

    }

    public function index()
    {
        $this->data['page_name'] = 'list';

        $page = $this->data['filter']['page'];
        $rows = $this->data['filter']['rows'];

        $conditions = array();

        $conditions['end_date'] = '';
        $conditions['start_date'] = '';

        if($this->data['filter']['end_date'] != '' && isDate($this->data['filter']['end_date'])){
        	$conditions['end_date'] = $this->data['filter']['end_date'];
        }

        if($this->data['filter']['start_date'] != '' && isDate($this->data['filter']['start_date'])){
        	$conditions['start_date'] = $this->data['filter']['start_date'];
        }

        $this->data['filter']['total'] = $total = $this->room_use_model->get_ckeckin_count($conditions);
        $this->data['filter']['offset'] = $offset = ($page -1) * $rows;

        $conditions['rows'] = $rows;
        $conditions['offset'] = $offset;

		$this->data['list'] = $this->room_use_model->get_ckeckin($conditions);
		// jd($this->data['list']);
		$this->load->library('pagination');
        $config['base_url'] = base_url("venue_rental/checkin_rpt?". $this->getQueryString(array(), array('page')));
        $config['total_rows'] = $total;
        $config['per_page'] = $rows;
        $this->pagination->initialize($config);

		$this->data['link_export'] = base_url("venue_rental/checkin_rpt/export/?{$_SERVER['QUERY_STRING']}");
		$this->data['link_refresh'] = base_url("venue_rental/checkin_rpt/");
		$this->data['select_url'] = base_url("venue_rental/checkin_rpt/?{$_SERVER['QUERY_STRING']}");

        $this->layout->view('venue_rental/checkin_rpt/list', $this->data);
    }

    public function export()
    {

    	if(!isDate($this->data['filter']['start_date']) || !isDate($this->data['filter']['end_date'])){
        	$this->setAlert(3, '請選擇日期');
            redirect(base_url("venue_rental/checkin_rpt/?{$_SERVER['QUERY_STRING']}"));
        }

        $conditions = array();
		if ($this->data['filter']['start_date'] != '') {
            $conditions['start_date'] = $this->data['filter']['start_date'];
        }
        if ($this->data['filter']['end_date'] != '') {
            $conditions['end_date'] = $this->data['filter']['end_date'];
        }

        $this->data['list'] = $this->room_use_model->get_ckeckin($conditions);

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
        $sheet->setTitle('checkin List');

        // 合併儲存格
        // $sheet->mergeCells('A1:D2');

        $row = 1;
        //訂單編號、訂單總額、訂單狀態、訂單成立時間
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $row, '申請單位');
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $row, '場地名稱');
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $row, '容納人數');
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, $row, '統計人數');
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4, $row, '單價');
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(5, $row, '統計金額');

        $row = 2;
        $objPHPExcel->getActiveSheet() -> getColumnDimension("A") -> setAutoSize(true);
        // jd($this->data['list'],1);
        $word = 'A';
        foreach ($this->data['list'] as $list_row) {
        	$objPHPExcel->getActiveSheet()->getCellByColumnAndRow(0, $row)->setValueExplicit($list_row['APP_NAME'], PHPExcel_Cell_DataType::TYPE_STRING);
        	$objPHPExcel->getActiveSheet()->getCellByColumnAndRow(1, $row)->setValueExplicit($list_row['room_name'], PHPExcel_Cell_DataType::TYPE_STRING);
        	$objPHPExcel->getActiveSheet()->getCellByColumnAndRow(2, $row)->setValueExplicit($list_row['room_cap'], PHPExcel_Cell_DataType::TYPE_STRING);
        	$objPHPExcel->getActiveSheet()->getCellByColumnAndRow(3, $row)->setValueExplicit($list_row['TOTCNT'], PHPExcel_Cell_DataType::TYPE_STRING);
        	$objPHPExcel->getActiveSheet()->getCellByColumnAndRow(4, $row)->setValueExplicit($list_row['UNITAMT'], PHPExcel_Cell_DataType::TYPE_STRING);
        	$objPHPExcel->getActiveSheet()->getCellByColumnAndRow(5, $row)->setValueExplicit($list_row['TOTAMT'], PHPExcel_Cell_DataType::TYPE_STRING);

	        $word++;
	        $objPHPExcel->getActiveSheet() -> getColumnDimension("{$word}") -> setAutoSize(true);
		    $row++;
		}

        // $objPHPExcel->getActiveSheet()->getStyle("A1:{$word}{$row}")->getAlignment()->setWrapText(true);//設定換行
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

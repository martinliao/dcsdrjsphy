<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Learn_time extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('student/learn_time_model');
        if (!isset($this->data['filter']['query_year'])) {
            $this->data['filter']['query_year'] = date('Y') - 1911;
        }
    }

    public function index()
    {
        $idno = $this->flags->user['idno'];
        $name = $this->flags->user['name'];
        //$conditions['idno']=$idno;

        /*if ($this->data['filter']['query_year'] !== '' ) {
        $conditions['year'] = $this->data['filter']['query_year'];
        }*/

        /*if($idno!=""){
        $this->data['list']=$this->learn_time_model->getRecord($conditions);
        }*/
        $this->data['link_export'] = base_url("student/learn_time/export");
        $this->data['link_refresh'] = base_url("student/learn_time/");
        $this->layout->view('student/learn_time/list', $this->data);
    }
    public function export()
    {
        $idno = $this->flags->user['idno'];
        $name = $this->flags->user['name'];
        $conditions['idno'] = $idno;
        $this->data['filter']['query_year'] = $this->input->post('query_year');

        if ($this->data['filter']['query_year'] !== '') {
            $conditions['year'] = $this->data['filter']['query_year'];
            $timeInterval = $this->data['filter']['query_year'] . "0101~" . $this->data['filter']['query_year'];
            $getMonth = date("m");
            $getDay = date("d");
            $post_year = $this->data['filter']['query_year'];
            if (intval($getMonth) <= 2) {
                $timeInterval .= "1231";
            } else {
                if (intval($getDay) >= 15) {
                    $a_date = sprintf("%s-%s-01", $post_year, $getMonth - 1);
                    $timeInterval .= date("m", strtotime($a_date)) . date("t", strtotime($a_date));
                } else {
                    $a_date = sprintf("%s-%s-01", $post_year, $getMonth - 2);
                    $timeInterval .= date("m", strtotime($a_date)) . date("t", strtotime($a_date));
                }
            }
            //die();
        }

        if ($idno != "") {
            $info = $this->learn_time_model->getRecord($conditions);
            //$info=$this->class_record_model->getStudentCourseInfo1($enter_id_number,$name);
        }

        $dataList = array();
        for ($k = 0; $k < count($info); $k++) {
            if ($info[$k]["is_assess"] == 1 && $info[$k]["is_mixed"] == 1) {
                $classType = "混成";
            } else {
                $classType = "實體";
            }
            if ($info[$k]["type"] == "A") {
                $category = "行政";
            } elseif ($info[$k]["type"] == "B") {
                $category = "發展";
            }
            if ($classType == "混成") {
                $tmpCol = ($info[$k]["range_real"] + $info[$k]["range_internet"]) . "(" . $info[$k]["range_internet"] . "+" . ($info[$k]["range_real"]) . ")";
            }else{
                $tmpCol = '';
            }
           

            $tmpAry = array($category, $info[$k]["req_beaurau"], $info[$k]["class_name"] . "(第" . $info[$k]["term"] . "期)",
                $classType, $name, $info[$k]["h2"] == 0 || $classType == "混成" ? "" : $info[$k]["h2"], $info[$k]["h1"] == 0 || $classType == "混成" ? "" : $info[$k]["h1"], $info[$k]["h3"] == 0 || $classType == "混成" ? "" : $row["h3"], $tmpCol);

            //
            array_push($dataList, $tmpAry);
        }
        $this->db->select('*');
        $this->db->where('stu_id', $idno);
        $this->db->where('year', $this->data['filter']['query_year']);
        $this->db->order_by('month,timecomplete');
        $query = $this->db->get('lux_elearn_record_log');
        $result = $query->result_array();

        for ($z = 0; $z < count($result); $z++) {
            $tmpAry = array("數位", "", $result[$z]["cname"], "數位", $name, "", "", $result[$z]["certhour"], "");
            array_push($dataList, $tmpAry);
        }
        //var_dump($dataList);
        //die();

        // 新增Excel物件
        /*$this->load->library('excel');
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

        /*$sheet->getDefaultStyle()
            ->getBorders()
            ->getTop()
            ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $sheet->getDefaultStyle()
            ->getBorders()
            ->getBottom()
            ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $sheet->getDefaultStyle()
            ->getBorders()
            ->getLeft()
            ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $sheet->getDefaultStyle()
            ->getBorders()
            ->getRight()
            ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $sheet->getStyle('A3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A3')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $sheet->getStyle('B3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('B3')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $sheet->getStyle('C3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('C3')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $sheet->getStyle('D3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('D3')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $sheet->getStyle('E3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('E3')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

        // 將工作表命名
        $sheet->setTitle('List');
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        //$sheet->getColumnDimension('A')->setWidth(50);
        $sheet->getStyle('A1')->getFont()->setSize(15);
        $sheet->setCellValue('A1', '學員研習時數統計表');
        $sheet->mergeCells('A1:C1');
        $sheet->setCellValue('A2', '統計區間' . $timeInterval);

        $sheet->setCellValue('A3', '類別');
        $sheet->mergeCells('A3:A4');
        $sheet->setCellValue('B3', '承辦機關');
        $sheet->mergeCells('B3:B4');
        $sheet->setCellValue('C3', '班期名稱(期數)');
        $sheet->mergeCells('C3:C4');
        $sheet->setCellValue('D3', '班期性質');
        $sheet->mergeCells('D3:D4');
        $sheet->setCellValue('E3', '結訓人');
        $sheet->mergeCells('E3:E4');
        $sheet->mergeCells('F3:G3');
        $sheet->setCellValue('F3', '實體課程');
        $sheet->setCellValue('F4', '無考核');
        $sheet->setCellValue('G4', '有考核');
        $sheet->setCellValue('H3', '數位課程');
        $sheet->setCellValue('H4', '臺北E大');
        $sheet->setCellValue('I3', '混成課程');
        $sheet->mergeCells('I3:J3');
        $sheet->setCellValue('I4', '總時數(E大+實體)');

        $k = 5;
        for ($i = 0; $i < count($dataList); $i++) {
            $sheet->setCellValue('A' . $k, $dataList[$i][0]);
            $sheet->setCellValue('B' . $k, $dataList[$i][1]);
            $sheet->setCellValue('C' . $k, $dataList[$i][2]);
            $sheet->setCellValue('D' . $k, $dataList[$i][3]);
            $sheet->setCellValue('E' . $k, $dataList[$i][4]);
            $sheet->setCellValue('F' . $k, $dataList[$i][5]);
            $sheet->setCellValue('G' . $k, $dataList[$i][6]);
            $sheet->setCellValue('H' . $k, $dataList[$i][7]);
            $sheet->setCellValue('I' . $k, $dataList[$i][8]);
            $k++;
        }

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');

        header('Content-Type:application/csv;charset=UTF-8');
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
        header("Content-Type:application/force-download");
        header("Content-Type:application/vnd.ms-excel;");
        header("Content-Type:application/octet-stream");
        header('Content-Disposition: attachment;filename="' . generatorRandom(10) . '.xls"');
        header("Content-Transfer-Encoding:binary");
        $objWriter->save('php://output');*/


        header("Content-type:application/vnd.ms-excel");
		header("Content-Disposition:filename=16H.xls");
		$body = "";
		for($i=0;$i<count($dataList);$i++) {
		$fontColor = "";
		if($dataList[$i][3]=="混成") {
			$fontColor = "style='color:red'";
		}
            $body .=
                "<tr>
				<td>".$dataList[$i][0]."</td>
				<td>".$dataList[$i][1]."</td>
				<td>".$dataList[$i][2]."</td>
				<td $fontColor>".$dataList[$i][3]."</td>
				<td>".$dataList[$i][4]."</td>
				<td>".$dataList[$i][5]."</td>
				<td>".$dataList[$i][6]."</td>
				<td>".$dataList[$i][7]."</td>
				<td>".$dataList[$i][8]."</td>
				</tr>";
		}
        echo "<html><body><h1>學員研習時數統計表</h1><div style='text-align:left'>統計區間：".htmlspecialchars($timeInterval, ENT_HTML5|ENT_QUOTES)."</div>
                <div style='text-align:right'>單位：小時／人次</div>
                    <table border='1' cellspacing='0' cellpadding='0'>
                        <tr>
                            <th width='100' rowspan='2'>類別</th>
                            <th width='245' rowspan='2'>承辦機關</th>
                            <th width='400' rowspan='2'>班期名稱(期別)</th>
                            <th width='125' rowspan='2'>班期性質</th>
                            <th width='125' rowspan='2'>結訓人</th>
                            <th width='250' colspan='2'>實體課程</th>
                            <th width='125' >數位課程</th>
                            <th width='125' >混成課程</th>
                        </tr>
                        <tr>
                            <td width='125' >無考核</td>
                            <td width='125' >有考核</td>
                            <td>臺北e大</td>
                            <td>總時數(e大+實體)</td>
                        </tr>
                        $body
                    </table>
                </body>
            </html>";

        exit;
        $refreshAfter = 5;

        //Send a Refresh header to the browser.
        $this->layout->view('student/learn_time/list', $this->data);

    }

}

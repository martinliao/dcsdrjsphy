<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Eat_money_count extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('meal/Eat_money_count_model');
    }

    public function index()
    {
        $type = isset($_GET['type'])?$_GET['type']:"";
        $start_date = isset($_GET['start_date'])?$_GET['start_date']:"";
        $end_date = isset($_GET['end_date'])?$_GET['end_date']:"";
        $action = isset($_GET['action'])?$_GET['action']:"";

        $this->load->library('pagination');
        $config['total_rows'] = 200;
        $config['per_page'] = 20;
        $this->pagination->initialize($config);

        $this->data['sess_type'] = $type;
        $this->data['sess_start_date'] = $start_date;
        $this->data['sess_end_date'] = $end_date;
        $this->data['sess_action'] = $action;
        $account = $this->flags->user['username'];

        $this->data['link_refresh'] = base_url("meal/eat_money_count/");
        if($action == "made"){
            $this->data['datas'] = $this->Eat_money_count_model->made($start_date,$end_date);
        }
        else if($action == "check"){
            $this->data['datas'] = $this->Eat_money_count_model->checked($start_date,$end_date,$account);
        }
        else if($action == "print1"){
            //echo json_encode($this->Eat_money_count_model->print1($start_date,$end_date));
            $this->layout->view('meal/eat_money_count/dining_statistic_rpt.php',$this->data);
        }
        else if($action == "print4"){
            echo json_encode($this->Eat_money_count_model->print4($start_date,$end_date));
        }
        else if($action == "print2"){
            
            $this->layout->view('meal/eat_money_count/dining_statistic_rpt1_edit.php',$this->data);
        }
        else if($action == "print3"){
            $this->layout->view('meal/eat_money_count/dining_statistic_rpt2.php',$this->data);
        }
        
        if($action != "print1" && $action != "print4"&& $action != "print2"&& $action != "print3"){
            $this->layout->view('meal/eat_money_count/list',$this->data);
        }
            
    }

    public function exportXlsx()
    {
        $start_date = $this->getFilterData('start_date');
        $end_date = $this->getFilterData('end_date');

        $this->load->library('excel');
        $objPHPExcel = new PHPExcel();

        // 設定操作中的工作表
        $objPHPExcel->setActiveSheetIndex(0);
        $sheet = $objPHPExcel->getActiveSheet();        

        $lunchClass = $this->getLunchClass($start_date, $end_date);

        $sheet->setCellValue('A1', '用餐人數計費統計表');
        $sheet->setCellValue('A2', '從 '.$start_date.' 至 '.$end_date);
        $sheet->setCellValue('A3', '承辦人');
        $sheet->setCellValue('A4', '班期');
        $sheet->setCellValue('A5', '調訓人數');

        $classLocations = [];

        $field = 'C';
        foreach ($lunchClass as $class){
            $classLocations[$class->year.'_'.$class->class_no.'_'.$class->term] = $field;
            $sheet->setCellValue($field.'3', $class->worker_name);
            $sheet->setCellValue($field.'4', $class->class_name.'第'.$class->term.'期');
            $sheet->setCellValue($field.'5', $class->no_persons);

            $field = $this->getExcelNextField($field);
        }

        $classKeys = array_map(function($class){
            return "(year = '".$class->year."' AND class_no = '".$class->class_no."' AND term = '".$class->term."')";
        }, $lunchClass);

        $bandons = $this->getArrivalBandonInfo($classKeys);
 
        $classLastField = $field;

        $days = $this->getAfterSevenDay($start_date);

        $weekNM = array("星期日","星期一","星期二","星期三","星期四","星期五","星期六");
        $row = 7;

        $tField = $this->getExcelNextField(end($classLocations), 3);
        $allTotal = 0;
        foreach ($days as $date){
            $sheet->setCellValue('A'.$row, $date->format('m/d')."\n".$weekNM[$date->format('w')]);
            $sheet->setCellValue('B'.$row, '午餐');

            $sheet->mergeCells('A'.$row.':'.'A'.($row+3));
            $sheet->mergeCells('B'.$row.':'.'B'.($row+3));
            
            $year = (new DateTime($start_date))->format('Y') - 1911;
            
            $dayInfo = $this->getDayInfo($start_date, $end_date, $date->format('Y-m-d'), $year);

            $lastClass = null;

            $teacher_total = 0;
            $total = 0;

            foreach ($dayInfo as $info){

                if ($lastClass == $info->year.'_'.$info->class_no.'_'.$info->term){
                    continue;
                }else{
                    $lastClass = $info->year.'_'.$info->class_no.'_'.$info->term;
                }

                $classLocation = $classLocations[$info->year.'_'.$info->class_no.'_'.$info->term];

                $diningInfo = $this->getDiningInfo($info->year, $info->class_no, $info->term, $date->format('Y-m-d'));

                $to_time = (empty($info->to_time)) ? '' : substr($info->to_time, 0, 2).':'.substr($info->to_time, 2, 2);                
                $to_time = (empty($diningInfo)) ? $to_time : $diningInfo->to_time;

                if (!empty($to_time)){

                    if ($info->l_name != '教務組') $teacher_total += $info->l_teach_cnt;

                    $sname = $info->sname;
                    $sname = str_replace("電腦教室", "電", $sname);
                    $sname = str_replace("國際會議廳", "國", $sname);
                    $sname = str_replace("大禮堂", "大", $sname);
                    $sname = str_replace("教室", "", $sname);

                    $sheet->setCellValue($classLocation.$row, $info->l_name);
                    $sheet->setCellValue($classLocation.($row + 1), $sname);
                    $sheet->setCellValue($classLocation.($row + 2), $to_time);

                    $l_cnt = (empty($diningInfo)) ? $info->l_cnt : $diningInfo->dining_count;
                    $total += $l_cnt;

                    $sheet->setCellValue($classLocation.($row + 3), $l_cnt); 
                    $sheet->setCellValue($classLocation.'37', $info->room_code); 

                    if (isset($bandons[$info->year.'_'.$info->class_no.'_'.$info->term.'_'.$date->format('Y-m-d')])){
                        $sheet->getStyle($classLocation.$row.':'.$classLocation.($row + 3))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FFFF33');
                    }
                    
                    if (!empty($diningInfo)){
                        if (substr($info->to_time, 0, 2).':'.substr($info->to_time, 2, 2) != $diningInfo->to_time){
                            $sheet->getStyle($classLocation.($row + 2))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FF8888');
                        }

                        if ($diningInfo->dining_count != $info->l_cnt){
                            $sheet->getStyle($classLocation.($row + 3))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FF8888');
                        }
                    }

                }

            }

            $sheet->setCellValue($tField.($row + 3), $teacher_total); 
            $sheet->setCellValue($this->getExcelNextField($tField).($row + 3), $total + $teacher_total); 

            $allTotal += $total + $teacher_total;

            $row = $row + 4;
        }

        $sheet->setCellValue($tField.'3', "長\n官\n暨\n講\n座"); 
        $sheet->setCellValue($this->getExcelNextField($tField).'3', "請\n款\n人\n數"); 
        $sheet->mergeCells($tField.'3:'.$tField.'4');
        $sheet->mergeCells($this->getExcelNextField($tField).'3:'.$this->getExcelNextField($tField).'4');

        $sheet->setCellValue($tField.'35', "午餐"); 
        $sheet->setCellValue($this->getExcelNextField($tField).'35', $allTotal);

        $lunchUnitPrice = $this->getLunchUnitPrice();

        $sheet->setCellValue('A37', "上課教室"); 
        $sheet->setCellValue($tField.'37', "金額"); 
        $sheet->setCellValue($this->getExcelNextField($tField).'37', $allTotal * $lunchUnitPrice); 

        $sheet->getStyle('A1:'.$this->getExcelNextField($tField, 10).'37')->getAlignment()->setWrapText(true); 

        $style = array(
            'alignment' => array(
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('rgb' => '000000') ),
            )            
        );
        $sheet->getStyle("A1:".$this->getExcelNextField($tField).'37')->applyFromArray($style);

        $sheet->mergeCells("A1:".$this->getExcelNextField($tField).'1');
        $sheet->mergeCells("A2:".$this->getExcelNextField($tField).'2');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');


        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
        header("Content-Type:application/force-download");
        header("Content-Type:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;");
        header("Content-Type:application/octet-stream");
        header('Content-Disposition: attachment;filename="'.generatorRandom(10).'.xlsx"');
        header("Content-Transfer-Encoding:binary");

        $objWriter->save('php://output');

        exit;
    }

    private function getLunchClass($start_date, $end_date)
    {
        $sql = "SELECT a.*,
                       b.seq_no, 
                       (
                         SELECT count('x') 
                         FROM online_app p 
                         WHERE yn_sel NOT IN ('2','6','7') AND 
                               p.year = a.year AND 
                               p.class_no = a.class_no AND 
                               p.term = a.term
                       ) as no_persons,
                       b.room_code, nvl(c.NAME,a.worker) as worker_name 
               FROM (   
                   SELECT DISTINCT year, class_no, term, class_name, worker 
                   FROM dining_student
                   WHERE use_date BETWEEN date(?) AND date(?)
               ) a  
               LEFT JOIN `require` b ON a.year = b.year AND a.class_no = b.class_no AND a.term = b.term 
               LEFT JOIN view_all_account c ON a.worker = c.personal_id
               WHERE IFNULL(b.is_cancel, '0') = '0'
               ORDER BY a.year, a.class_no, a.term";
        $query = $this->db->query($sql, [$start_date, $end_date]);        
        return $query->result();
    }

    private function getExcelNextField($char, $addnum = 1)
    {
        $charNum = [];

        if (strlen($char) == 1){
            $charNum[0] = ord($char);
            $charNum[1] = 64;
        }else if (strlen($char) == 2){
            $charNum[0] = ord(substr($char, 1, 1));
            $charNum[1] = ord(substr($char, 0, 1));  
        }

        $charNum[0] += $addnum;

        if ($charNum[0] > 90){
            $charNum[0] -= 26;
            $charNum[1] ++;
        }
        
        $newChar = chr($charNum[0]);

        if ($charNum[1] > 64){
            $newChar = chr($charNum[1]).$newChar;
        }

        return $newChar;
    }

    private function getAfterSevenDay($start_date)
    {
        $start_date = new DateTime($start_date);    
        $days = [];
        for($i=0; $i<7; $i++){
            $days[] = clone $start_date;
            $start_date->modify('+1day');
        }
        return $days;
    }

    private function getDayInfo($start_date, $end_date, $date, $year)
    {
        $sql = "SELECT distinct a.*, b.no_persons, b.room_code, b.is_cancel, cr.sname
                    ,a1.m_cnt, a1.l_cnt, a1.d_cnt, p.to_time
                    ,replace(c1.m_name,',','<br>') as m_name, c1.m_teach_cnt
                    ,replace(c2.l_name,',','、') as l_name, c2.l_teach_cnt
                    ,replace(c3.d_name,',','<br>') as d_name, c3.d_teach_cnt
                FROM
                (
                    SELECT distinct year, class_no, term, class_name, worker FROM dining_student 
                    WHERE use_date between ? AND ?
                )a
                LEFT JOIN 
                (
                    SELECT year, class_no, term, class_name
                    ,(nvl(persons_1,0) + nvl(add_persons_1,0)) as m_cnt
                    ,(nvl(persons_2,0) + nvl(add_persons_2,0)) as l_cnt
                    ,(nvl(persons_3,0) + nvl(add_persons_3,0)) as d_cnt
                    FROM dining_student 
                    WHERE use_date = date(?)
                )a1 on a.year = a1.year AND a.class_no = a1.class_no AND a.term = a1.term
                LEFT JOIN `require` b on a.year = b.year AND a.class_no = b.class_no AND a.term = b.term
                LEFT JOIN 
                (
                    SELECT R.year, R.class_id, R.term, use_date, min(R.use_period) as min_use_period 
                    FROM room_use R 
                    WHERE R.use_id!='O00003'
                    GROUP BY R.year, R.class_id, R.term, use_date having R.use_date = date(?) 
                )rr on a.year = rr.year AND a.class_no = rr.class_id AND a.term = rr.term   
                LEFT JOIN room_use ru on ru.year = ru.year AND ru.class_id = rr.class_id AND ru.term = rr.term AND ru.use_period=rr.min_use_period AND  ru.use_date = date(?)
                LEFT JOIN classroom cr on cr.room_id = ru.room_id
                LEFT JOIN
                (
                    SELECT year, class_no, term, use_date,GROUP_CONCAT(name) as m_NAME, count(*) as m_teach_cnt
                    FROM dining_teacher
                    WHERE use_date = date(?) AND dining_type = 'A'
                    GROUP BY year, class_no, term, use_date
                )c1 on a.year = c1.year AND a.class_no = c1.class_no AND a.term = c1.term
                LEFT JOIN
                (
                    SELECT year, class_no, term, use_date, GROUP_CONCAT(name) as l_name, count(*) as l_teach_cnt
                    FROM dining_teacher
                    WHERE use_date = date(?) AND dining_type = 'B'
                    GROUP BY year, class_no, term, use_date
                )c2 on a.year = c2.year AND a.class_no = c2.class_no AND a.term = c2.term
                LEFT JOIN
                (
                    SELECT year, class_no, term, use_date, GROUP_CONCAT(name) as d_NAME, count(*) as d_teach_cnt
                    FROM dining_teacher
                    WHERE use_date = date(?) AND dining_type = 'C'
                    GROUP BY year, class_no, term, use_date
                )c3 on a.year = c3.year AND a.class_no = c3.class_no AND a.term = c3.term
                LEFT JOIN (
                    SELECT * 
                    FROM (
                        SELECT year, class_no, term, case when '1130' between FROM_time AND to_time then 'Y' end is_lunch , to_time  
                        FROM periodtime 
                        WHERE year=? AND course_date= date(?)
                    )df 
                    WHERE is_lunch = 'Y'
                ) p on a.year = p.year AND a.class_no = p.class_no AND a.term = p.term
                WHERE IFNULL(b.is_cancel,'0')='0' order by a.year, a.class_no, a.term";

        $params = [$start_date, $end_date, $date, $date, $date, $date, $date, $date, $year, $date];
        $query = $this->db->query($sql, $params);      
        return $query->result();  
    }


    private function getDiningInfo($year, $class_no, $term, $date)
    {
        $sql = "SELECT dining_count, to_time FROM dining_info WHERE year = ? AND class_no = ? AND term = ? AND use_date = ?";
        $params = [$year, $class_no, $term, $date];
        $query = $this->db->query($sql, $params);
        return $query->row();
    }

    private function getLunchUnitPrice()
    {
        $sql = "SELECT add_val1 
                FROM code_table 
                WHERE type_id = '25' AND item_id = 'B'";
        $query = $this->db->query($sql);
        return $query->row()->add_val1;
    }

    private function getArrivalBandonInfo($classKeys){
        $sql = join(' OR ', $classKeys);

        $sql = "SELECT *
                FROM dining_info
                WHERE ({$sql}) AND is_bandon = 'Y'";

        $query = $this->db->query($sql);
        $result = $query->result();
        $arrivals = [];

        foreach ($result as $arrival){
            $arrivals[$arrival->year.'_'.$arrival->class_no.'_'.$arrival->term.'_'.$arrival->use_date] = $arrival;
        }

        return $arrivals;
    }
    
    public function bandon()
    {
        $post = $this->input->post(['bandonYear', 'bandonClass_no', 'bandonTerm', 'bandonCourse_date']);
        if (empty($post['bandonYear']) || empty($post['bandonClass_no']) || empty($post['bandonTerm']) || empty($post['bandonCourse_date']) ){
            die('發生錯誤');
        }else{
            $sql = "SELECT * FROM dining_info WHERE year = ".$this->db->escape(addslashes($post['bandonYear']))." AND class_no = ".$this->db->escape(addslashes($post['bandonClass_no']))." AND term = ".$this->db->escape(addslashes($post['bandonTerm']))." AND use_date = ".$this->db->escape(addslashes($post['bandonCourse_date']));

            $query = $this->db->query($sql);
            $diningInfo = $query->row();
            $is_bandon = 'N';
            if (!empty($diningInfo->is_bandon == 'N')){
                $is_bandon = 'Y';
            }

            $sql = "UPDATE dining_info SET is_bandon = ".$this->db->escape(addslashes($is_bandon))." WHERE year = ".$this->db->escape(addslashes($post['bandonYear']))." AND class_no = ".$this->db->escape(addslashes($post['bandonClass_no']))." AND term = ".$this->db->escape(addslashes($post['bandonTerm']))." AND use_date = ".$this->db->escape(addslashes($post['bandonCourse_date']));
     
            $this->db->query($sql);   
        }
        $message = ($is_bandon == 'Y') ? '登記便當完成' : '取消便當完成'; 
        $this->setAlert(2, $message);
        redirect($_SERVER['HTTP_REFERER']);
    }
}

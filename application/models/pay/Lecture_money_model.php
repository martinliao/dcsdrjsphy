<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

require_once APPPATH . "models/Common_model.php";
class Lecture_money_model extends Common_model
{
    public function exportLectureMoneyData($start,$end)
    {
        $d1 = $start;
        $d2 = $end;
        
        /*header("Content-type:application/vnd.ms-excel");
        header("Content-Disposition:filename=13M.xls");
        if(true) {
            $sql = "SELECT H.CLASS_NAME,H.CLASS_NO,H.YEAR,H.TERM,H.TEACHER_NAME,H.USE_DATE,H.HOUR_FEE,H.TRAFFIC_FEE,H.SUBTOTAL,a.app_seq
                    FROM hour_traffic_tax H
                    JOIN `require` r ON H.YEAR = r.YEAR
                    JOIN hour_app a on a.seq = H.seq AND H.term = r.term AND H.class_no = r.class_no
                    WHERE 1 = 1 AND date_format(a.bill_date, '%Y-%m-%d') BETWEEN date_format(date('".$d1."'),'%Y-%m-%d')AND date_format(date('".$d2."'),'%Y-%m-%d')AND H.status IS NOT NULL AND IFNULL(r.is_cancel, '0') = '0'
                    ORDER BY H.YEAR DESC,H.CLASS_NO,H.TERM,H.USE_DATE DESC";
            $rs = $this->db->query($sql);
            $rs = $this->QueryToArray($rs);
            $data = $rs;
       
            $sql = "SELECT x.CLASS_NAME,x.CLASS_NO,x.YEAR,x.TERM,x.APP_SEQ,COUNT(1) cnt
                    FROM (SELECT H.CLASS_NAME,H.CLASS_NO,H.YEAR,H.TERM,H.TEACHER_NAME,H.USE_DATE,H.HOUR_FEE,H.TRAFFIC_FEE,H.SUBTOTAL,A.app_seq,H.seq
                          FROM hour_traffic_tax H
                          JOIN `require` r ON H.YEAR = r.YEAR
                          JOIN hour_app A ON A.seq = H.seq AND H.term = r.term AND H.class_no = r.class_no
                          WHERE 1 = 1 AND date_format(A.bill_date, '%Y-%m-%d') BETWEEN date_format(date('".$d1."'),'%Y-%m-%d') AND date_format(date('".$d2."'),'%Y-%m-%d') AND H.status IS NOT NULL AND IFNULL(r.is_cancel, '0') = '0'
                          ORDER BY H.YEAR DESC,H.CLASS_NO,H.TERM,H.USE_DATE DESC) x
                          GROUP BY x.APP_SEQ,x.CLASS_NO,x.YEAR,x.TERM,x.CLASS_NAME
                          ORDER BY x. YEAR DESC,x.CLASS_NO,x.TERM";
            $rs = $this->db->query($sql);
            $rs = $this->QueryToArray($rs);
            $class = $rs;
           
            $cnt = 0;
            for($i=0;$i<sizeof($class);$i++){
                $cnt += ceil($class[$i]['cnt']/3);
            }

            $hour_fee_total = 0;
            $traffic_fee_total = 0;
            for($i=0;$i<sizeof($data);$i++){
                $hour_fee_total += $data[$i]['HOUR_FEE'];
                $traffic_fee_total += $data[$i]['TRAFFIC_FEE'];
            }

            $total = $hour_fee_total + $traffic_fee_total;

            $mid = ceil($cnt/2);
            $k = 0;
            $serial = 0;
            $rep = 0;

            $list = array();
            // print_r($class);
            // print_r($data);
            for($i=0;$i<sizeof($class);$i++){
                if($k > 0){
                    while($x < 3){
                        if($a == 5){
                            $a = 3;
                        } else {
                            $a++;
                        }
                        if(isset($list[$k][$a])){
                            $list[$k][$a] .= mb_convert_encoding('',"UTF-8","UTF-8")."\t";
                        }else{
                            $list[$k][$a] = mb_convert_encoding('',"UTF-8","UTF-8")."\t";
                        }
                        $list[$k][$a] .= mb_convert_encoding('',"UTF-8","UTF-8")."\t";
                        $list[$k][$a] .= mb_convert_encoding('',"UTF-8","UTF-8")."\t";
                        $list[$k][$a] .= mb_convert_encoding('',"UTF-8","UTF-8")."\t";
                        $list[$k][$a] .= mb_convert_encoding('',"UTF-8","UTF-8")."\t";
                        $list[$k][$a] .= mb_convert_encoding('',"UTF-8","UTF-8")."\t";
                        $x++;
                    }

                    if(isset($list[$k][6])){
                        $list[$k][6] .= mb_convert_encoding('',"UTF-8","UTF-8")."\t";
                    }else{
                        $list[$k][6] = mb_convert_encoding('',"UTF-8","UTF-8")."\t";
                    }
                    $list[$k][6] .= mb_convert_encoding('',"UTF-8","UTF-8")."\t";
                    $list[$k][6] .= mb_convert_encoding('',"UTF-8","UTF-8")."\t";
                    $list[$k][6] .= iconv('UTF-8', 'BIG5', "═════════════════ \t");
                    $list[$k][6] .= mb_convert_encoding('',"UTF-8","UTF-8")."\t";
                    $list[$k][6] .= mb_convert_encoding('',"UTF-8","UTF-8")."\t";

                    if(isset($list[$k][7])){
                        $list[$k][7] .= mb_convert_encoding('',"UTF-8","UTF-8")."\t";
                    }else{
                        $list[$k][7] = mb_convert_encoding('',"UTF-8","UTF-8")."\t";
                    }
                    $list[$k][7] .= mb_convert_encoding('',"UTF-8","UTF-8")."\t";
                    $list[$k][7] .= mb_convert_encoding('',"UTF-8","UTF-8")."\t";
                    $list[$k][7] .= iconv('UTF-8', 'BIG5', $y." \t");
                    $list[$k][7] .= iconv('UTF-8', 'BIG5', $z." \t");
                    $list[$k][7] .= iconv('UTF-8', 'BIG5', $y+$z." \t");
                }

                $k++;
                $serial++;
                if(isset($list[$k][1])){
                    $list[$k][1] .= iconv('UTF-8', 'BIG5', $class[$i]['app_seq']." \t");
                }else{
                    $list[$k][1] = iconv('UTF-8', 'BIG5', $class[$i]['app_seq']." \t");
                }
                $list[$k][1] .= iconv('UTF-8', 'BIG5', $class[$i]['CLASS_NAME']." \t");
                $list[$k][1] .= iconv('UTF-8', 'BIG5', "第".$class[$i]['TERM']."期 \t");
                $list[$k][1] .= mb_convert_encoding('',"UTF-8","UTF-8")."\t";
                $list[$k][1] .= mb_convert_encoding('',"UTF-8","UTF-8")."\t";
                $list[$k][1] .= mb_convert_encoding('',"UTF-8","UTF-8")."\t";
                $list[$k][1] .= iconv('UTF-8', 'BIG5', $serial." \t");

                if(isset($list[$k][2])){
                    $list[$k][2] .= mb_convert_encoding('',"UTF-8","UTF-8")."\t";
                }else{
                    $list[$k][2] = mb_convert_encoding('',"UTF-8","UTF-8")."\t";
                }
                $list[$k][2] .= iconv('UTF-8', 'BIG5', "講座 \t");
                $list[$k][2] .= iconv('UTF-8', 'BIG5', "日期 \t");
                $list[$k][2] .= iconv('UTF-8', 'BIG5', "鐘點費 \t");
                $list[$k][2] .= iconv('UTF-8', 'BIG5', "交通費 \t");
                $list[$k][2] .= iconv('UTF-8', 'BIG5', "合計 \t");
                
                $x = 0;
                $y = 0;
                $z = 0;
                $a = 2;
                for($j=0;$j<sizeof($data);$j++){
                    if($data[$j]['app_seq'] == $class[$i]['app_seq']){
                        if($x == 3){
                            if(isset($list[$k][6])){
                                $list[$k][6] .= mb_convert_encoding('',"UTF-8","UTF-8")."\t";
                            }else{
                                $list[$k][6] = mb_convert_encoding('',"UTF-8","UTF-8")."\t";
                            }
                            $list[$k][6] .= mb_convert_encoding('',"UTF-8","UTF-8")."\t";
                            $list[$k][6] .= mb_convert_encoding('',"UTF-8","UTF-8")."\t";
                            $list[$k][6] .= iconv('UTF-8', 'BIG5', "═════════════════ \t");
                            $list[$k][6] .= mb_convert_encoding('',"UTF-8","UTF-8")."\t";
                            $list[$k][6] .= mb_convert_encoding('',"UTF-8","UTF-8")."\t";

                            if(isset($list[$k][7])){
                                $list[$k][7] .= mb_convert_encoding('',"UTF-8","UTF-8")."\t";
                            }else{
                                $list[$k][7] = mb_convert_encoding('',"UTF-8","UTF-8")."\t";
                            }
                            $list[$k][7] .= mb_convert_encoding('',"UTF-8","UTF-8")."\t";
                            $list[$k][7] .= mb_convert_encoding('',"UTF-8","UTF-8")."\t";
                            $list[$k][7] .= iconv('UTF-8', 'BIG5', $y." \t");
                            $list[$k][7] .= iconv('UTF-8', 'BIG5', $z." \t");
                            $list[$k][7] .= iconv('UTF-8', 'BIG5', $y+$z." \t");

                            $k++;
                            $rep++;	

                            if(isset($list[$k][1])){
                                $list[$k][1] .= iconv('UTF-8', 'BIG5', $class[$i]['app_seq']." \t");
                            }else{
                                $list[$k][1] = iconv('UTF-8', 'BIG5', $class[$i]['app_seq']." \t");
                            }
                            $list[$k][1] .= iconv('UTF-8', 'BIG5', $class[$i]['CLASS_NAME']." \t");
                            $list[$k][1] .= iconv('UTF-8', 'BIG5', "第".$class[$i]['TERM']."期 \t");
                            $list[$k][1] .= mb_convert_encoding('',"UTF-8","UTF-8")."\t";
                            $list[$k][1] .= mb_convert_encoding('',"UTF-8","UTF-8")."\t";
                            $list[$k][1] .= mb_convert_encoding('',"UTF-8","UTF-8")."\t";
                            $list[$k][1] .= iconv('UTF-8', 'BIG5', $serial." \t");

                            if(isset($list[$k][2])){
                                $list[$k][2] .= mb_convert_encoding('',"UTF-8","UTF-8")."\t";
                            }else{
                                $list[$k][2] = mb_convert_encoding('',"UTF-8","UTF-8")."\t";
                            }
                            $list[$k][2] .= iconv('UTF-8', 'BIG5', "講座 \t");
                            $list[$k][2] .= iconv('UTF-8', 'BIG5', "日期 \t");
                            $list[$k][2] .= iconv('UTF-8', 'BIG5', "鐘點費 \t");
                            $list[$k][2] .= iconv('UTF-8', 'BIG5', "交通費 \t");
                            $list[$k][2] .= iconv('UTF-8', 'BIG5', "合計 \t");


                            $x = 0;
                            $y = 0;
                            $z = 0;


                        }

                        if($a == 5){
                            $a = 3;
                        } else {
                            $a++;
                        }
                        
                        if(isset($list[$k][$a])){
                            $list[$k][$a] .= mb_convert_encoding('',"UTF-8","UTF-8")."\t";
                        }else{
                            $list[$k][$a] = mb_convert_encoding('',"UTF-8","UTF-8")."\t";
                        }
                        //$list[$k][$a] .= iconv('UTF-8', 'BIG5', $data[$j]['TEACHER_NAME']." \t");
                        $list[$k][$a] .= mb_convert_encoding($data[$j]['TEACHER_NAME']." \t","BIG5","UTF-8");
                        $list[$k][$a] .= iconv('UTF-8', 'BIG5', date('m-d',strtotime($data[$j]['USE_DATE']))." \t");
                        $list[$k][$a] .= iconv('UTF-8', 'BIG5', $data[$j]['HOUR_FEE']." \t");
                        $list[$k][$a] .= iconv('UTF-8', 'BIG5', $data[$j]['TRAFFIC_FEE']." \t");
                        $list[$k][$a] .= iconv('UTF-8', 'BIG5', $data[$j]['SUBTOTAL']." \t");
                        $y += $data[$j]['HOUR_FEE'];
                        $z += $data[$j]['TRAFFIC_FEE'];
                        $x++;
                    }
                }

                if($i == (sizeof($class)-1)){
                    if(isset($list[$k][6])){
                        $list[$k][6] .= mb_convert_encoding('',"UTF-8","UTF-8")."\t";
                    }else{
                        $list[$k][6] = mb_convert_encoding('',"UTF-8","UTF-8")."\t";
                    }
                    $list[$k][6] .= mb_convert_encoding('',"UTF-8","UTF-8")."\t";
                    $list[$k][6] .= mb_convert_encoding('',"UTF-8","UTF-8")."\t";
                    $list[$k][6] .= iconv('UTF-8', 'BIG5', "═════════════════ \t");
                    $list[$k][6] .= mb_convert_encoding('',"UTF-8","UTF-8")."\t";
                    $list[$k][6] .= mb_convert_encoding('',"UTF-8","UTF-8")."\t";
                    
                    if(isset($list[$k][7])){
                        $list[$k][7] .= mb_convert_encoding('',"UTF-8","UTF-8")."\t";
                    }else{
                        $list[$k][7] = mb_convert_encoding('',"UTF-8","UTF-8")."\t";
                    }
                    $list[$k][7] .= mb_convert_encoding('',"UTF-8","UTF-8")."\t";
                    $list[$k][7] .= mb_convert_encoding('',"UTF-8","UTF-8")."\t";
                    $list[$k][7] .= iconv('UTF-8', 'BIG5', $y." \t");
                    $list[$k][7] .= iconv('UTF-8', 'BIG5', $z." \t");
                    $list[$k][7] .= iconv('UTF-8', 'BIG5', $y+$z." \t");
                }
                
            }

            //print_r($list);
            
            for($i=1;$i<=$mid;$i++){
                if(isset($list[$i+$mid])){
                    for($j=1;$j<=sizeof($list[$i]);$j++){
                        if($j == '1'){
                            //echo iconv('UTF-8', 'BIG5', $list[$i][$j].", \t");
                            //echo mb_convert_encoding('',"UTF-8","UTF-8")."\t".$list[$i][$j];
                            echo $list[$i][$j]."\t";
                        } else {
                            echo $list[$i][$j]."\t";
                        }
                        if(!isset($list[$i+$mid][$j])){
                            $list[$i+$mid][$j] = mb_convert_encoding('',"UTF-8","UTF-8")."\t";
                            $list[$i+$mid][$j] .=  mb_convert_encoding('',"UTF-8","UTF-8")."\t";
                            $list[$i+$mid][$j] .=  mb_convert_encoding('',"UTF-8","UTF-8")."\t";
                            $list[$i+$mid][$j] .=  mb_convert_encoding('',"UTF-8","UTF-8")."\t";
                            $list[$i+$mid][$j] .=  mb_convert_encoding('',"UTF-8","UTF-8")."\t";
                            $list[$i+$mid][$j] .= mb_convert_encoding('',"UTF-8","UTF-8")."\t";
                        }
                        echo $list[$i+$mid][$j]."\n";
                    }
                    
                } elseif($i == $mid) {
                    for($j=1;$j<=sizeof($list[$i]);$j++){
                        if(isset($list[$i][$j])){
                            echo $list[$i][$j]."\n";
                        }
                    }
                }
                
            }

            echo "\n";
            echo mb_convert_encoding('',"UTF-8","UTF-8")."\t";
            echo mb_convert_encoding('',"UTF-8","UTF-8")."\t";
            echo mb_convert_encoding('',"UTF-8","UTF-8")."\t";
            echo iconv('UTF-8', 'BIG5', "附1 \t");
            echo iconv('UTF-8', 'BIG5', "合計 \t");
            echo mb_convert_encoding('',"UTF-8","UTF-8")."\t";
            echo mb_convert_encoding('',"UTF-8","UTF-8")."\t";
            echo mb_convert_encoding('',"UTF-8","UTF-8")."\t";
            echo mb_convert_encoding('',"UTF-8","UTF-8")."\t";
            echo mb_convert_encoding('',"UTF-8","UTF-8")."\t";
            echo iconv('UTF-8', 'BIG5', "附1 \t");
            echo iconv('UTF-8', 'BIG5', "合計 \t\n");

            echo mb_convert_encoding('',"UTF-8","UTF-8")."\t";
            echo iconv('UTF-8', 'BIG5', "鐘點費 \t");
            echo iconv('UTF-8', 'BIG5', $hour_fee_total." \t");
            echo iconv('UTF-8', 'BIG5', "0 \t");
            echo iconv('UTF-8', 'BIG5', $hour_fee_total." \t");
            echo mb_convert_encoding('',"UTF-8","UTF-8")."\t";
            echo mb_convert_encoding('',"UTF-8","UTF-8")."\t";
            echo mb_convert_encoding('',"UTF-8","UTF-8")."\t";
            echo iconv('UTF-8', 'BIG5', "郵局 \t\n");

            echo mb_convert_encoding('',"UTF-8","UTF-8")."\t";
            echo iconv('UTF-8', 'BIG5', "交通費 \t");
            echo iconv('UTF-8', 'BIG5', $traffic_fee_total." \t");
            echo iconv('UTF-8', 'BIG5', "0 \t");
            echo iconv('UTF-8', 'BIG5', $traffic_fee_total." \t");
            echo mb_convert_encoding('',"UTF-8","UTF-8")."\t";
            echo mb_convert_encoding('',"UTF-8","UTF-8")."\t";
            echo mb_convert_encoding('',"UTF-8","UTF-8")."\t";
            echo iconv('UTF-8', 'BIG5', "銀行 \t\n");

            echo mb_convert_encoding('',"UTF-8","UTF-8")."\t";
            //echo "════════════════════════════════════════════════════════════════════════════════════════════════\t\n";

            //echo mb_convert_encoding('',"UTF-8","UTF-8")."\t";
            echo iconv('UTF-8', 'BIG5', "合計 \t");
            echo iconv('UTF-8', 'BIG5', $total." \t");
            echo iconv('UTF-8', 'BIG5', "0 \t");
            echo iconv('UTF-8', 'BIG5', $total." \t");
            echo mb_convert_encoding('',"UTF-8","UTF-8")."\t";
            echo mb_convert_encoding('',"UTF-8","UTF-8")."\t";
            echo mb_convert_encoding('',"UTF-8","UTF-8")."\t";
            echo mb_convert_encoding('',"UTF-8","UTF-8")."\t";
            echo iconv('UTF-8', 'BIG5', "0 \t");
            echo iconv('UTF-8', 'BIG5', "0 \t");
            echo iconv('UTF-8', 'BIG5', "0 \t\n");

            echo "\n";

            echo mb_convert_encoding('',"UTF-8","UTF-8")."\t";
            echo iconv('UTF-8', 'BIG5', "共.($cnt-$rep).份 \t");
            echo iconv('UTF-8', 'BIG5', "出單日期 \t");
            echo iconv('UTF-8', 'BIG5', date('Y/m/d')." \t");
            echo iconv('UTF-8', 'BIG5', count($data)." \t");
            echo iconv('UTF-8', 'BIG5', "次 \t\n");

            echo "\n";

            echo mb_convert_encoding('',"UTF-8","UTF-8")."\t";
            echo iconv('UTF-8', 'BIG5', "    /     傳送，已登所得 \t");
            echo mb_convert_encoding('',"UTF-8","UTF-8")."\t";
            echo mb_convert_encoding('',"UTF-8","UTF-8")."\t";
            echo mb_convert_encoding('',"UTF-8","UTF-8")."\t";
            echo mb_convert_encoding('',"UTF-8","UTF-8")."\t";
            echo mb_convert_encoding('',"UTF-8","UTF-8")."\t";
            echo mb_convert_encoding('',"UTF-8","UTF-8")."\t";
            echo mb_convert_encoding('',"UTF-8","UTF-8")."\t";
            echo iconv('UTF-8', 'BIG5', "   /      電連清單 \t\n");

            echo mb_convert_encoding('',"UTF-8","UTF-8")."\t";
            echo iconv('UTF-8', 'BIG5', " 行政管理系統入帳日期已設定 \t");
            echo mb_convert_encoding('',"UTF-8","UTF-8")."\t";
            echo mb_convert_encoding('',"UTF-8","UTF-8")."\t";
            echo mb_convert_encoding('',"UTF-8","UTF-8")."\t";
            echo mb_convert_encoding('',"UTF-8","UTF-8")."\t";
            echo mb_convert_encoding('',"UTF-8","UTF-8")."\t";
            echo mb_convert_encoding('',"UTF-8","UTF-8")."\t";
            echo mb_convert_encoding('',"UTF-8","UTF-8")."\t";
            echo iconv('UTF-8', 'BIG5', "   /      已登所得 \t\n");

            // echo '<pre>';
            // print_r($list);
        }*/

    
        
        $sql = "SELECT H.CLASS_NAME,H.CLASS_NO,H.YEAR,H.TERM,H.TEACHER_NAME,H.USE_DATE,H.HOUR_FEE,H.TRAFFIC_FEE,H.SUBTOTAL,a.app_seq
                    FROM hour_traffic_tax H
                    JOIN `require` r ON H.YEAR = r.YEAR
                    JOIN hour_app a on a.seq = H.seq AND H.term = r.term AND H.class_no = r.class_no
                    WHERE 1 = 1 AND date_format(a.bill_date, '%Y-%m-%d') BETWEEN date_format(date(".$this->db->escape(addslashes($d1))."),'%Y-%m-%d')AND date_format(date(".$this->db->escape(addslashes($d2))."),'%Y-%m-%d')AND H.status IS NOT NULL AND IFNULL(r.is_cancel, '0') = '0'
                    ORDER BY a.app_seq asc,  H.YEAR DESC,H.CLASS_NO,H.TERM,H.USE_DATE DESC";
        $rs = $this->db->query($sql);
        $rs = $this->QueryToArray($rs);
        $data = $rs;

        $sql = "SELECT x.CLASS_NAME,x.CLASS_NO,x.YEAR,x.TERM,x.APP_SEQ,COUNT(1) cnt
                    FROM (SELECT H.CLASS_NAME,H.CLASS_NO,H.YEAR,H.TERM,H.TEACHER_NAME,H.USE_DATE,H.HOUR_FEE,H.TRAFFIC_FEE,H.SUBTOTAL,A.app_seq,H.seq
                          FROM hour_traffic_tax H
                          JOIN `require` r ON H.YEAR = r.YEAR
                          JOIN hour_app A ON A.seq = H.seq AND H.term = r.term AND H.class_no = r.class_no
                          WHERE 1 = 1 AND date_format(A.bill_date, '%Y-%m-%d') BETWEEN date_format(date(".$this->db->escape(addslashes($d1))."),'%Y-%m-%d') AND date_format(date(".$this->db->escape(addslashes($d2))."),'%Y-%m-%d') AND H.status IS NOT NULL AND IFNULL(r.is_cancel, '0') = '0'
                          ORDER BY H.YEAR DESC,H.CLASS_NO,H.TERM,H.USE_DATE DESC) x
                          GROUP BY x.APP_SEQ,x.CLASS_NO,x.YEAR,x.TERM,x.CLASS_NAME
                          ORDER BY x.app_seq asc,x. YEAR DESC,x.CLASS_NO,x.TERM";
        $rs = $this->db->query($sql);
        $rs = $this->QueryToArray($rs);
        $class = $rs;

        

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
        
        $rows_1=1;//控制課程名稱、期數列(左半部)
        $rows_2=2;//控制講座、日期、鐘點費、交通費、合計列(左半部)
        $rows_3=3;//控制資料顯示列(左半部)
        $rows_4=0;//控制綠色區塊列(左半部)
        $row_1=1;//控制課程名稱、期數列(右半部)
        $row_2=2;//控制講座、日期、鐘點費、交通費、合計列(右半部)
        $row_3=3;//控制資料顯示列(右半部)
        $row_4=0;//控制綠色區塊列(右半部)
        $cnt=0;//總講座資料數
        for($z=0;$z<count($class);$z++){
            $cnt+=ceil($class[$z]['cnt']/3);
        }
        $mid=ceil($cnt/2);//將講座資料分成左右兩半
        $mid_cnt=0;//標題計數(分成左半右半)
        $data_mid_cnt=0;//資料計數(分成左半右半)
        
        $g_and_n=1;//欄位G and N
        
        
        for($i=0;$i<count($class);$i++){
            if($mid_cnt>=$mid){
                for($m=0;$m<ceil($class[$i]['cnt']/3);$m++){
                    $sheet->mergeCells("J{$row_1}:M{$row_1}");//;
                    $sheet->getStyle("J{$row_1}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    // 增加框線、文字顏色
                    $styleArray = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN,'color' => array('argb' =>                               '000000'),),),
                                        'font' => array('color'=>array('rgb'=>'FF0000'),),
                                        );
                    $sheet->getStyle("J{$row_1}:M{$row_1}")->applyFromArray($styleArray);

                    $sheet->getStyle("I{$row_1}")->applyFromArray(array('font' => array('color'=>array('rgb'=>'FF0000'))));

                    $sheet->setCellValue('I'.$row_1,$class[$i]['CLASS_NAME']);
                    $sheet->setCellValue('J'.$row_1,'第'.$class[$i]['TERM'].'期');

                    $sheet->setCellValue('I'.$row_2,"講座");
                    $sheet->setCellValue('J'.$row_2,"日期");
                    $sheet->setCellValue('K'.$row_2,"鐘點費");
                    $sheet->setCellValue('L'.$row_2,"交通費");
                    $sheet->setCellValue('M'.$row_2,"合計");
                    $sheet->setCellValue('N'.$row_2,$g_and_n);

                    $row_1=$row_1+7;
                    $row_2=$row_2+7;
                    $mid_cnt++;
                    $g_and_n++;
                }
            }else{
                for($k=0;$k<ceil($class[$i]['cnt']/3);$k++){
                    $sheet->mergeCells("C{$rows_1}:F{$rows_1}");//;
                    $sheet->getStyle("C{$rows_1}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    // 增加框線
                    $styleArray = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN,'color' => array('argb' =>                               '000000'),),),
                                        'font' => array('color'=>array('rgb'=>'FF0000'),),
                                        );
                    $sheet->getStyle("C{$rows_1}:F{$rows_1}")->applyFromArray($styleArray);

                    $sheet->getStyle("B{$rows_1}")->applyFromArray(array('font' => array('color'=>array('rgb'=>'FF0000'))));
                    

                    $sheet->setCellValue('B'.$rows_1,$class[$i]['CLASS_NAME']);
                    $sheet->setCellValue('C'.$rows_1,'第'.$class[$i]['TERM'].'期');
                    $sheet->setCellValue('B'.$rows_2,"講座");
                    $sheet->setCellValue('C'.$rows_2,"日期");
                    $sheet->setCellValue('D'.$rows_2,"鐘點費");
                    $sheet->setCellValue('E'.$rows_2,"交通費");
                    $sheet->setCellValue('F'.$rows_2,"合計");
                    $sheet->setCellValue('G'.$rows_2,$g_and_n);
                    
                    $rows_1=$rows_1+7;
                    $rows_2=$rows_2+7;
                    $mid_cnt++;
                    $g_and_n++;
                }
                
            }


                
            $count=0;//計算一個課程區塊顯示講座個數是否達3
            $total=0;//左半部合計總數
            $total_hour_fee=0;//左半部鐘點費合計
            $total_traffic_fee=0;//左半部交通費合計
            $total_1=0;//右半部合計總數
            $total_hour_fee_1=0;//右半部鐘點費合計
            $total_traffic_fee_1=0;//右半部交通費合計
            for($j=0;$j<count($data);$j++){
                //分成兩半
                if($data_mid_cnt>=$mid){
                    $total_row_1 = $row_3+3;//控制合計的列

                    if($class[$i]['app_seq']==$data[$j]['app_seq']){
                        $temp_row=$row_3+$count;
                        $sheet->setCellValue('I'.$temp_row,$data[$j]['TEACHER_NAME']);
                        $sheet->setCellValue('J'.$temp_row,substr($data[$j]['USE_DATE'],5,5));
                        $sheet->setCellValue('K'.$temp_row,$data[$j]['HOUR_FEE']);
                        if($data[$j]['TRAFFIC_FEE']<0){
                            $data[$j]['TRAFFIC_FEE']=0;
                        }
                        $sheet->setCellValue('L'.$temp_row,$data[$j]['TRAFFIC_FEE']);
                        $sheet->setCellValue('M'.$temp_row,$data[$j]['SUBTOTAL']);                
                        
                        $total_1+=$data[$j]['SUBTOTAL'];

                        $total_hour_fee_1=$total_hour_fee_1+$data[$j]['HOUR_FEE'];
                        $total_traffic_fee_1+=$data[$j]['TRAFFIC_FEE'];
                        
                        $sheet->setCellValue('K'.$total_row_1,$total_hour_fee_1);
                        $sheet->setCellValue('L'.$total_row_1,$total_traffic_fee_1);
                        $sheet->setCellValue('M'.$total_row_1,$total_1);
                        //將區塊塗成綠色
                        $sheet->getStyle("K{$total_row_1}")->applyFromArray(array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,
                                                                                                  'color' => array('rgb' => '98FB98'))));
                        $sheet->getStyle("L{$total_row_1}")->applyFromArray(array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,
                                                                                                  'color' => array('rgb' => '98FB98'))));
                        $sheet->getStyle("M{$total_row_1}")->applyFromArray(array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,
                                                                                                  'color' => array('rgb' => '98FB98'))));
                        
                        //var_dump($total_hour_fee_1);
                        $count++;
                        //若此堂課會顯示超過三個講座 則在創造一個同班期標題 並且顯示在新創的同班期標題
                        if($count>=3){
                            $temp_row=$temp_row+5;
                            $row_3=$temp_row;
                            $count=0;
                            $total_1=0;
                            $total_hour_fee_1=0;
                            $total_traffic_fee_1=0;
                        }
                        $data_mid_cnt+=ceil($class[$i]['cnt']/3);
                    }
                }else{
                    $total_row = $rows_3+3; //控制合計的列
                    if($class[$i]['app_seq']==$data[$j]['app_seq']){
                        $temp_row=$rows_3+$count;
                        
                        $sheet->setCellValue('B'.$temp_row,$data[$j]['TEACHER_NAME']);
                        $sheet->setCellValue('C'.$temp_row,substr($data[$j]['USE_DATE'],5,5));
                        $sheet->setCellValue('D'.$temp_row,$data[$j]['HOUR_FEE']);
                        if($data[$j]['TRAFFIC_FEE']<0){
                            $data[$j]['TRAFFIC_FEE']=0;
                        }
                        $sheet->setCellValue('E'.$temp_row,$data[$j]['TRAFFIC_FEE']);
                        $sheet->setCellValue('F'.$temp_row,$data[$j]['SUBTOTAL']);


                        $total+=$data[$j]['SUBTOTAL'];

                        $total_hour_fee=$total_hour_fee+$data[$j]['HOUR_FEE'];

                        $total_traffic_fee+=$data[$j]['TRAFFIC_FEE'];

        
                        $sheet->setCellValue('D'.$total_row,$total_hour_fee);
                        $sheet->setCellValue('E'.$total_row,$total_traffic_fee);
                        $sheet->setCellValue('F'.$total_row,$total);

                        //將區塊塗成綠色
                        $sheet->getStyle("D{$total_row}")->applyFromArray(array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,
                                                                                                  'color' => array('rgb' => '98FB98'))));
                        $sheet->getStyle("E{$total_row}")->applyFromArray(array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,
                                                                                                  'color' => array('rgb' => '98FB98'))));
                        $sheet->getStyle("F{$total_row}")->applyFromArray(array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,
                                                                                                  'color' => array('rgb' => '98FB98'))));
                        $count++;
                        //若此堂課會顯示超過三個講座 則在創造一個同班期標題 並且顯示在新創的同班期標題
                        if($count>=3){
                            $temp_row=$temp_row+5;
                            $rows_3=$temp_row;
                            $count=0;
                            $total=0;
                            $total_hour_fee=0;
                            $total_traffic_fee=0;
                        }
                    }
                }
            }
                $data_mid_cnt+=ceil($class[$i]['cnt']/3);
                $row_3=$row_2+1;//控制右半，將資料顯示在 講座、日期...的標題下方
                $rows_3=$rows_2+1;//控制左半，將資料顯示在 講座、日期...的標題下方
                $total=0;
                $total_hour_fee=0;
                $total_traffic_fee=0;
                $total_1=0;
                $total_hour_fee_1=0;
                $total_traffic_fee_1=0;
        }  


        $final_total_trafic_fee=0;//所有資料的總交通費
        $final_total_hour_fee=0;//所有資料的總鐘點費費

        for($l=0;$l<count($data);$l++){
            $final_total_trafic_fee+=$data[$l]['TRAFFIC_FEE'];
            $final_total_hour_fee+=$data[$l]['HOUR_FEE'];
        }
        $really_final_total=$final_total_hour_fee+$final_total_trafic_fee;//所有資料的總共費用

        //如果是右半邊資料比較多 則以右半邊資料 控制欄位數的變數當作基準
        if($rows_2>$row_2){
            $rows_3=$rows_2+1;
            $rows_4=$rows_3+1;
            $sheet->setCellValue('D'.$rows_2,"附1");
            $sheet->setCellValue('E'.$rows_2,"合計");
            $sheet->setCellValue('B'.$rows_3,"鐘點費");
            $sheet->setCellValue('C'.$rows_3,$final_total_hour_fee);
            $sheet->setCellValue('E'.$rows_3,$final_total_hour_fee);
            $sheet->setCellValue('D'.$rows_3,"0");
            $sheet->setCellValue('B'.$rows_4,"交通費");
            $sheet->setCellValue('C'.$rows_4,$final_total_trafic_fee);
            $sheet->setCellValue('E'.$rows_4,$final_total_trafic_fee);
            $sheet->setCellValue('D'.$rows_4,"0");
            $rows_5=$rows_4+1;
            $rows_6=$rows_5+1;
            $sheet->mergeCells("B{$rows_5}:E{$rows_5}");
            $sheet->setCellValue('B'.$rows_5,"═══════════════════════");
            $sheet->setCellValue('B'.$rows_6,"合計");
            $sheet->setCellValue('C'.$rows_6,$really_final_total);
            $sheet->setCellValue('D'.$rows_6,"0");
            $sheet->setCellValue('E'.$rows_6,$really_final_total);
            $sheet->setCellValue('J'.$rows_5,"0");
            $sheet->setCellValue('K'.$rows_5,"0");
            $sheet->setCellValue('L'.$rows_5,"0");
            $rows_7=$rows_6+2;
            $sheet->setCellValue('B'.$rows_7,"共".count($class)."份");
            $sheet->setCellValue('C'.$rows_7,"出單日期");
            $time=substr($start,5,5);
            $sheet->setCellValue('D'.$rows_7,$time);
            $sheet->setCellValue('F'.$rows_7,count($data)."次");
            $rows_8=$rows_7+2;
            $sheet->setCellValue('B'.$rows_8,"    /     傳送，已登所得 ");
            $rows_9=$rows_8+1;
            $sheet->setCellValue('B'.$rows_9,"行政管理系統入帳日期已設定");
            $sheet->setCellValue('J'.$rows_8,"   /      電連清單 ");
            $sheet->setCellValue('J'.$rows_9,"   /      已登所得 ");
            $sheet->setCellValue('B'.$rows_6,"合計");
            $sheet->setCellValue('I'.$rows_3,"銀行");
            $sheet->setCellValue('I'.$rows_4,"郵局");
            $sheet->setCellValue('K'.$rows_2,"附1");
            $sheet->setCellValue('L'.$rows_2,"合計");
        }else{     
            $row_3=$row_2+1;
            $row_4=$row_3+1;
            $sheet->setCellValue('D'.$row_2,"附1");
            $sheet->setCellValue('E'.$row_2,"合計");

            $sheet->setCellValue('B'.$row_3,"鐘點費");
            $sheet->setCellValue('C'.$row_3,$final_total_hour_fee);
            $sheet->setCellValue('E'.$row_3,$final_total_hour_fee);
            $sheet->setCellValue('D'.$row_3,"0");
            $sheet->setCellValue('B'.$row_4,"交通費");
            $sheet->setCellValue('C'.$row_4,$final_total_trafic_fee);
            $sheet->setCellValue('E'.$row_4,$final_total_trafic_fee);
            $sheet->setCellValue('D'.$row_4,"0");
            $row_5=$row_4+1;
            $row_6=$row_5+1;

            $sheet->mergeCells("B{$row_5}:E{$row_5}");
            $sheet->setCellValue('B'.$row_5,"═══════════════════════");
            $sheet->setCellValue('B'.$row_6,"合計");
            $sheet->setCellValue('C'.$row_6,$really_final_total);
            $sheet->setCellValue('D'.$row_6,"0");
            $sheet->setCellValue('E'.$row_6,$really_final_total);
            $sheet->setCellValue('J'.$row_5,"0");
            $sheet->setCellValue('K'.$row_5,"0");
            $sheet->setCellValue('L'.$row_5,"0");
            $row_7=$row_6+2;
            $sheet->setCellValue('B'.$row_7,"共".count($class)."份");
            $sheet->setCellValue('C'.$row_7,"出單日期");
            $time=substr($start,5,5);
            $sheet->setCellValue('D'.$row_7,$time);
            $sheet->setCellValue('F'.$row_7,count($data)."次");
            $row_8=$row_7+2;
            $sheet->setCellValue('B'.$row_8,"    /     傳送，已登所得 ");
            $row_9=$row_8+1;
            $sheet->setCellValue('B'.$row_9,"行政管理系統入帳日期已設定");
            $sheet->setCellValue('J'.$row_8,"   /      電連清單 ");
            $sheet->setCellValue('J'.$row_9,"   /      已登所得 ");
            //$sheet->setCellValue('B'.$rows_3,"交通費");
            //$sheet->setCellValue('B'.$rows_3,"合計");
            $sheet->setCellValue('I'.$row_3,"銀行");
            $sheet->setCellValue('I'.$row_4,"郵局");
            $sheet->setCellValue('K'.$row_2,"附1");
            $sheet->setCellValue('L'.$row_2,"合計");
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

}

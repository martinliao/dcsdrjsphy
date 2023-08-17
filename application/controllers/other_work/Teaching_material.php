<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Teaching_material extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model(['require_model', 'room_use_model', 'teacher_auth_model']);
    }



    public function index() //pdf_range
    {
        $condition = [];
        $condition['start_date'] = $this->getFilterData('start_date', date("Y-m-d"));
        $condition['end_date'] = $this->getFilterData('end_date', date("Y-m-d"));
        $this->data['materials'] = $this->room_use_model->getMaterial($condition);
        $this->data['link_export_authlist'] = base_url("other_work/teaching_material/pdf_range?{$_SERVER['QUERY_STRING']}");
        $this->data['link_export_pdf_range'] = base_url("other_work/teaching_material/authlist?{$_SERVER['QUERY_STRING']}");
    

        $this->layout->view("other_work/teaching_material/index", $this->data);
    }

    public function pdf_range()
    {

        $condition = [];
        $condition['start_date'] = $this->getFilterData('start_date', date("Y-m-d"));
        $condition['end_date'] = $this->getFilterData('end_date', date("Y-m-d"));
        $this->data['materials'] = $this->room_use_model->getMaterial($condition);

        $this->load->library('pdf/PDF_Chinesess');
        $pdf = new PDF_Chinesess();
        $pdf->AddBig5Font('kaiu','標楷體');
        $pdf->SetFont('kaiu');
        $pdf->AddPage();
        $pdf->SetMargins(10,10,10,10);
        $pdf->SetAutoPageBreak(false);
        foreach ($this->data['materials'] as $row){
            if(isset($row->auth_id)&&$row->auth_id>0){
           
                $class_info = array();
                $class_info['year'] =$row->year;
                $class_info['class_no'] =$row->class_id;
                $class_info['term'] =$row->term;
                $class_info['teacher_id'] =$row->t_id;
                $teacher_info = $this->require_model->find($class_info);
                $pdf->Cell(180, 5, mb_convert_encoding('臺北市政府公務人員訓練處','big5','utf8'),0,1,'C');   
                $pdf->Cell(180, 7, mb_convert_encoding('講座教材著作授權使用同意書','big5','utf8'),0,1,'C');   
                
                $pdf->Cell(185, 5, mb_convert_encoding('','big5','utf8'),0,1,'C');// 排版用
        
                $str = "本人擔任 ".$teacher_info->year."年度 ".$teacher_info->class_name."第 ".$teacher_info->term."期課程講座，所提供之教材著作同意供貴處無償使用，範圍如下：(以下請勾選)";
                $pdf->MultiCell(185, 5, mb_convert_encoding($str,'big5','utf8'),0,'L');
                $pdf->Cell(185, 5, mb_convert_encoding('','big5','utf8'),0,1,'C');// 排版用
         
                $str = "        1、同意將數位教材掛置於臺北e大-實體班期作業專區，供本人授課班期之學員下載及列印參考。(掛置時間至結訓日後一周)。";
                $pdf->MultiCell(185, 5, mb_convert_encoding($str,'big5','utf8'),0,'L');
                $pdf->Cell(185, 5, mb_convert_encoding('','big5','utf8'),0,1,'C');// 排版用
         
                $str = "        2、同意未來相同課程之教材同第一項說明。";
                $pdf->MultiCell(185, 5, mb_convert_encoding($str,'big5','utf8'),0,'L');
                
                $pdf->Cell(185, 5, mb_convert_encoding('','big5','utf8'),0,1,'C');// 排版用
         
                $str = "此致     臺北市政府公務人員訓練處";
                $pdf->MultiCell(185, 5, mb_convert_encoding($str,'big5','utf8'),0,'L');
                $pdf->Cell(185, 5, mb_convert_encoding('','big5','utf8'),0,1,'C');// 排版用
                $pdf->Cell(185, 5, mb_convert_encoding('','big5','utf8'),0,1,'C');// 排版用
                $pdf->Cell(185, 5, mb_convert_encoding('','big5','utf8'),0,1,'C');// 排版用
        
                $str = "授權人：                                                                                                   （簽名）";
                $pdf->MultiCell(185, 5, mb_convert_encoding($str,'big5','utf8'),0,'L');
        
                // 電子簽章
                $teacher_auth = $this->teacher_auth_model->find($class_info);
                if (!empty($teacher_auth)){
                    $teacher_auth->signature = explode(",", $teacher_auth->signature);
                    if (count($teacher_auth->signature) != 2){
                        die("簽章格式錯誤");
                    }
                    $teacher_auth->signature = 'data://text/plain;base64,'.$teacher_auth->signature[1];
                    $pdf->Image($teacher_auth->signature, 80, 80, -350, 0, 'png');
        
                    if ($teacher_auth->auth_1 == 1){
                        $pdf->Image(FCPATH."/static/img/checked.png", 12, 41, 5, 0, 'png');
                    }else{
                        $pdf->Image(FCPATH."/static/img/check.png", 12, 41, 5, 0, 'png');
                    }
        
                    if ($teacher_auth->auth_2 == 1){
                        $pdf->Image(FCPATH."/static/img/checked.png", 12, 56, 5, 0, 'png');
                    }else{
                        $pdf->Image(FCPATH."/static/img/check.png", 12, 56, 5, 0, 'png');
                    }
                    $teacher_auth->created_at = "中華民國 ".((int)date("Y", $teacher_auth->created_at)-1911).date(" 年 m 月 d 日", $teacher_auth->created_at);
        
                    $pdf->Cell(185, 40, mb_convert_encoding($teacher_auth->created_at,'big5','utf8'),0,1,'L');          
                }
                $pdf->AddPage(); //增加一頁          
            }
     
        }
        
        $pdf->Output();  
    }



    public function authlist()
    {

        $condition = [];
        $condition['start_date'] = $this->getFilterData('start_date', date("Y-m-d"));
        $condition['end_date'] = $this->getFilterData('end_date', date("Y-m-d"));
        $this->data['materials'] = $this->room_use_model->getMaterial($condition);
        // 新增Excel物件
        $this->load->library('excel');
        $objPHPExcel = new PHPExcel();

        // 設定操作中的工作表
        $objPHPExcel->setActiveSheetIndex(0);
        $sheet = $objPHPExcel->getActiveSheet();

        // 將工作表命名
        $sheet->setTitle('教材授權電子簽名管理');

        // 合併儲存格
        // $sheet->mergeCells('A1:D2');
        $sheet->setCellValue('A1','序號'); 
        $sheet->setCellValue('B1','上課日'); 
        $sheet->setCellValue('C1','班期名稱'); 
        $sheet->setCellValue('D1','期別	'); 
        $sheet->setCellValue('E1','講座名稱'); 
        $sheet->setCellValue('F1','同意授權簽名'); 
        $row = 2;
        foreach ($this->data['materials'] as $data){
            $sheet->setCellValue('A'.$row,$row-1); 
            $sheet->setCellValue('B'.$row,$data->use_date); 
            $sheet->setCellValue('C'.$row,$data->class_name); 
            $sheet->setCellValue('D'.$row,$data->term); 
            $sheet->setCellValue('E'.$row,$data->teacher_name); 
            $sheet->setCellValue('F'.$row,isset($data->auth_id)?"Y":"N"); 
            $row++;
        }

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');

        header('Content-Type:application/csv;charset=UTF-8');
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
        header("Content-Type:application/force-download");
        header("Content-Type:application/vnd.ms-excel;");
        header("Content-Type:application/octet-stream");
        header('Content-Disposition: attachment;filename=教材授權電子簽名管理"'.date("Y-m-d_H:i:s").'.xls');
        header("Content-Transfer-Encoding:binary");
        $objWriter->save('php://output');

        exit;
    }


    // public function pdf()
    // {
    //     if($this->flags->user['idno'] == 'T222291880') {
    //         $this->devPdf();
    //         die;
    //     }
    //     $class_info = $this->getFilterData(['year', 'class_no', 'term', 'teacher_id']);
    //     $teacher_info = $this->require_model->find($class_info);
    //     // var_dump($teacher_info);
    //     // die();
        
    //     $pdf = new PDF_Chinese();
    //     $pdf->AddBig5Font('kaiu','標楷體');
    //     $pdf->SetFont('kaiu');

    //     $pdf->AddPage();
    //     $pdf->SetMargins(10,10,10,10);
    //     $pdf->SetAutoPageBreak(false);

    //     $pdf->SetFont('kaiu');


    //     $pdf->Cell(180, 5, mb_convert_encoding('臺北市政府公務人員訓練處','big5','utf8'),0,1,'C');   
    //     $pdf->Cell(180, 7, mb_convert_encoding('講座教材著作授權使用同意書','big5','utf8'),0,1,'C');   
        
    //     $pdf->Cell(185, 5, mb_convert_encoding('','big5','utf8'),0,1,'C');// 排版用

    //     $str = "本人擔任 ".$teacher_info->year."年度 ".$teacher_info->class_name."第 ".$teacher_info->term."期課程講座，所提供之教材著作同意供貴處無償使用，範圍如下：(以下請勾選)";
    //     $pdf->MultiCell(185, 5, mb_convert_encoding($str,'big5','utf8'),0,'L');
    //     $pdf->Cell(185, 5, mb_convert_encoding('','big5','utf8'),0,1,'C');// 排版用
 
    //     $str = "        1、同意將數位教材掛置於臺北e大-實體班期作業專區，供本人授課班期之學員下載及列印參考。(掛置時間至結訓日後一周)。";
    //     $pdf->MultiCell(185, 5, mb_convert_encoding($str,'big5','utf8'),0,'L');
    //     $pdf->Cell(185, 5, mb_convert_encoding('','big5','utf8'),0,1,'C');// 排版用
 
    //     $str = "        2、同意未來相同課程之教材同第一項說明。";
    //     $pdf->MultiCell(185, 5, mb_convert_encoding($str,'big5','utf8'),0,'L');
        
    //     $pdf->Cell(185, 5, mb_convert_encoding('','big5','utf8'),0,1,'C');// 排版用
 
    //     $str = "此致     臺北市政府公務人員訓練處";
    //     $pdf->MultiCell(185, 5, mb_convert_encoding($str,'big5','utf8'),0,'L');
    //     $pdf->Cell(185, 5, mb_convert_encoding('','big5','utf8'),0,1,'C');// 排版用
    //     $pdf->Cell(185, 5, mb_convert_encoding('','big5','utf8'),0,1,'C');// 排版用
    //     $pdf->Cell(185, 5, mb_convert_encoding('','big5','utf8'),0,1,'C');// 排版用

    //     $str = "授權人：                                                                                                   （簽名）";
    //     $pdf->MultiCell(185, 5, mb_convert_encoding($str,'big5','utf8'),0,'L');


    //     // 電子簽章
    //     $teacher_auth = $this->teacher_auth_model->find($class_info);
    //     if (!empty($teacher_auth)){
    //         $teacher_auth->signature = explode(",", $teacher_auth->signature);
    //         if (count($teacher_auth->signature) != 2){
    //             die("簽章格式錯誤");
    //         }
    //         $teacher_auth->signature = 'data://text/plain;base64,'.$teacher_auth->signature[1];
    //         $pdf->Image($teacher_auth->signature, 25, 80, -500, 0, 'png');

    //         if ($teacher_auth->auth_1 == 1){
    //             $pdf->Image("/var/www/html/base/admin/static/img/checked.png", 12, 41, 5, 0, 'png');
    //         }else{
    //             $pdf->Image("/var/www/html/base/admin/static/img/check.png", 12, 41, 5, 0, 'png');
    //         }

    //         if ($teacher_auth->auth_2 == 1){
    //             $pdf->Image("/var/www/html/base/admin/static/img/checked.png", 12, 56, 5, 0, 'png');
    //         }else{
    //             $pdf->Image("/var/www/html/base/admin/static/img/check.png", 12, 56, 5, 0, 'png');
    //         }
    //         $teacher_auth->created_at = "中華民國 ".((int)date("Y", $teacher_auth->created_at)-1911).date(" 年 m 月 d 日", $teacher_auth->created_at);

    //         $pdf->Cell(185, 40, mb_convert_encoding($teacher_auth->created_at,'big5','utf8'),0,1,'L');          
    //     }

    //     $pdf->Output();  
    // }

    public function pdf()
    {
        $class_info = $this->getFilterData(['year', 'class_no', 'term', 'teacher_id']);
        $teacher_info = $this->require_model->find($class_info);
        // var_dump($teacher_info);
        // die();
        
        $this->load->library('pdf/PDF_Chinesess');
        $pdf = new PDF_Chinesess();
        $pdf->AddBig5Font('kaiu','標楷體');
        $pdf->SetFont('kaiu');

        $pdf->AddPage();
        $pdf->SetMargins(10,10,10,10);
        $pdf->SetAutoPageBreak(false);

        $pdf->SetFont('kaiu');


        $pdf->Cell(180, 5, mb_convert_encoding('臺北市政府公務人員訓練處','big5','utf8'),0,1,'C');   
        $pdf->Cell(180, 7, mb_convert_encoding('講座教材著作授權使用同意書','big5','utf8'),0,1,'C');   
        
        $pdf->Ln(5);

        $str = "本人擔任 ".$teacher_info->year."年度 ".$teacher_info->class_name."第 ".$teacher_info->term."期課程講座，所提供之教材著作同意供貴處無償使用，範圍如下：(以下得複式勾選、授權範圍隨項次逐項擴大)。";
        $pdf->MultiCell(185, 5, mb_convert_encoding($str,'big5','utf8'),0,'L');
        $pdf->Ln(5);
 
        $str = "        1、同意將數位教材掛置於臺北e大-實體班期作業專區，供本人授課班期之學員下載及列印參考。(掛置時間至結訓日後一周)。";
        $pdf->MultiCell(185, 5, mb_convert_encoding($str,'big5','utf8'),0,'L');
        $pdf->Ln(5);
 
        $str = "        2、同意未來相同課程之教材同第一項說明。";
        $pdf->MultiCell(185, 5, mb_convert_encoding($str,'big5','utf8'),0,'L', 0, 0);
        $pdf->Ln(5);

        $str = "        3、同意供貴處班期講座參考之用，但除提供予講座學員參考及基於廣宣部份截錄公示之外，不得修改、重製及做商業使用。";
        $pdf->MultiCell(185, 5, mb_convert_encoding($str,'big5','utf8'),0,'L');
        $pdf->Ln(5);
 
        $str = "        4、同意提供議員問政之用，但除問政基礎之公益目的外，不得修改、重製及做商業使用。";
        $pdf->MultiCell(185, 5, mb_convert_encoding($str,'big5','utf8'),0,'L');
        $pdf->Ln(5);

        $str = "        5、採本人名義以「CC授權 姓名標示-非商業性-禁止改作 3.0 台灣版本」提供，後續貴處得以本人名義再行發布，然貴處及收受著作之人，皆必須於標註本人姓名、不得修改、不得商業使用的前提下方得利用該著作。";
        $pdf->MultiCell(185, 5, mb_convert_encoding($str,'big5','utf8'),0,'L');
        $pdf->Ln(5);


        $str = "此致     臺北市政府公務人員訓練處";
        $pdf->MultiCell(185, 5, mb_convert_encoding($str,'big5','utf8'),0,'L');
        $pdf->Ln(15);

        $str = "授權人：                                                                                                   （簽名）";
        $pdf->MultiCell(185, 5, mb_convert_encoding($str,'big5','utf8'),0,'L');


        // 電子簽章
        $teacher_auth = $this->teacher_auth_model->find($class_info);
        if (!empty($teacher_auth)){
            $teacher_auth->signature = explode(",", $teacher_auth->signature);
            if (count($teacher_auth->signature) != 2){
                die("簽章格式錯誤");
            }
            $teacher_auth->signature = 'data://text/plain;base64,'.$teacher_auth->signature[1];
            $pdf->Image($teacher_auth->signature, 25, 125, -500, 0, 'png');

            if ($teacher_auth->auth_1 == 1){
                $pdf->Image(FCPATH."/static/img/checked.png", 12, 41, 5, 0, 'png');
            }else{
                $pdf->Image(FCPATH."/static/img/check.png", 12, 41, 5, 0, 'png');
            }

            if ($teacher_auth->auth_2 == 1){
                $pdf->Image(FCPATH."/static/img/checked.png", 12, 56, 5, 0, 'png');
            }else{
                $pdf->Image(FCPATH."/static/img/check.png", 12, 56, 5, 0, 'png');
            }

            if ($teacher_auth->auth_3 == 1){
                $pdf->Image(FCPATH."/static/img/checked.png", 12, 67, 5, 0, 'png');
            }else{
                $pdf->Image(FCPATH."/static/img/check.png", 12, 67, 5, 0, 'png');
            }

            if ($teacher_auth->auth_4 == 1){
                $pdf->Image(FCPATH."/static/img/checked.png", 12, 82, 5, 0, 'png');
            }else{
                $pdf->Image(FCPATH."/static/img/check.png", 12, 82, 5, 0, 'png');
            }

            if ($teacher_auth->auth_5 == 1){
                $pdf->Image(FCPATH."/static/img/checked.png", 12, 92, 5, 0, 'png');
            }else{
                $pdf->Image(FCPATH."/static/img/check.png", 12, 92, 5, 0, 'png');
            }

            $teacher_auth->created_at = "中華民國 ".((int)date("Y", $teacher_auth->created_at)-1911).date(" 年 m 月 d 日", $teacher_auth->created_at);

            $pdf->Cell(185, 40, mb_convert_encoding($teacher_auth->created_at,'big5','utf8'),0,1,'L');          
        }

        $pdf->Output();  
    }
}

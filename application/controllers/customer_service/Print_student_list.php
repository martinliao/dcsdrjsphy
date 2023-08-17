<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Print_student_list extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        if ($this->flags->is_login === FALSE) {
            redirect(base_url('welcome'));
        }
        $this->load->model('customer_service/Lux_account_log_model');
        $this->load->model('customer_service/Regist_personnel_model');
        $this->load->model('customer_service/online_app_model');
        $this->load->model('customer_service/BS_user_model');
        $this->load->model('management/code_table_model');
        $this->load->model('customer_service/print_student_list_model');


        $this->data['choices']['year'] = $this->_get_year_list();

        if (!isset($this->data['filter']['page'])) {
            $this->data['filter']['page'] = '1';
        }
        if (!isset($this->data['filter']['sort'])) {
            $this->data['filter']['sort'] = '';
        }
        if (!isset($this->data['filter']['class_no'])) {
            $this->data['filter']['class_no'] = '';
        }
        if (!isset($this->data['filter']['class_name'])) {
            $this->data['filter']['class_name'] = '';
        }
        //allQueryChecked**//
        if (!isset($this->data['filter']['checkAllClass'])) {
             $this->data['filter']['checkAllClass'] = '';
        }
        if (!isset($this->data['filter']['csv'])) {
             $this->data['filter']['csv'] = '0';
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
        $allQueryChecked = ($this->data['filter']['checkAllClass'] != '')? $this->data['filter']['checkAllClass']:0 ;
        $this->data['page_name'] = 'list';
        $page = $this->data['filter']['page'];
        $rows = $this->data['filter']['rows'];
        $conditions = array();

        if($allQueryChecked == 0){
            /*$this->data['list'] = array();
            $this->data['filter']['total'] = $total = 0;
            $this->data['filter']['offset'] = $offset = ($page -1) * $rows;*/
            $conditions['worker']=$this->flags->user['idno'];
            if($this->data['filter']['year']!=""){
                $conditions['year']=$this->data['filter']['year'];
            }
            if($this->data['filter']['class_no']!=""){
                $conditions['class_no']=$this->data['filter']['class_no'];
            }
           
            $attrs = array(
                'conditions' => $conditions,
            );

            $this->data['filter']['total'] = $total = $this->print_student_list_model->getListCount($attrs);
            $this->data['filter']['offset'] = $offset = ($page -1) * $rows;
            $attrs = array(
                'class_name' => $this->data['filter']['class_name'],
                'conditions' => $conditions,
                'rows' => $rows,
                'offset' => $offset,
            );

            $this->data['list']=$this->print_student_list_model->getList($attrs);


            foreach ($this->data['list'] as & $row) {
              // $row['link_regist'] = base_url("customer_service/print_student_list/show/{$row['seq_no']}");
               $row['link_regist'] = $row['seq_no'];
            }
            $this->load->library('pagination');
            $config['base_url'] = base_url("customer_service/print_student_list?". $this->getQueryString(array(), array('page')));

        }else{
            /*$conditions = array();
            $conditions['year'] = $this->data['filter']['year'];
            $attrs = array();
            $attrs['class_status'] = array('2','3');
            $attrs['conditions'] = $conditions;
            if ($this->data['filter']['class_name'] !== '' ) {
                $attrs['class_name'] = $this->data['filter']['class_name'];
            }else{
                $attrs['class_name'] = '';
            }
            if ($this->data['filter']['class_no'] != '' ) {
                $attrs['class_no'] = $this->data['filter']['class_no'];
            }else{
                $attrs['class_no'] = '';
            }
            $this->data['filter']['total'] = $total = $this->Regist_personnel_model->getListCount($attrs);
            $this->data['filter']['offset'] = $offset = ($page -1) * $rows;
            $attrs['rows'] = $rows;
            $attrs['offset'] = $offset;
            if ($this->data['filter']['sort'] != '' ) {
                $attrs['sort'] = $this->data['filter']['sort'];
            }
            $this->data['list'] = $this->Regist_personnel_model->getList($attrs);*/
            if($this->data['filter']['year']!=""){
                $conditions['year']=$this->data['filter']['year'];
            }
            if($this->data['filter']['class_no']!=""){
                $conditions['class_no']=$this->data['filter']['class_no'];
            }

            $attrs = array(
                'conditions' => $conditions,
            );

            if ($this->data['filter']['class_name'] !== '' ) {
                $attrs['class_name'] = $this->data['filter']['class_name'];
            }

            $this->data['filter']['total'] = $total = $this->print_student_list_model->getListCount($attrs);
            $this->data['filter']['offset'] = $offset = ($page -1) * $rows;

            


            $attrs = array(
                'conditions' => $conditions,
                'rows' => $rows,
                'offset' => $offset,
            );

            if ($this->data['filter']['class_name'] !== '' ) {
                $attrs['class_name'] = $this->data['filter']['class_name'];
            }

            $this->data['list']=$this->print_student_list_model->getList($attrs);


            foreach ($this->data['list'] as & $row) {
              // $row['link_regist'] = base_url("customer_service/print_student_list/show/{$row['seq_no']}");
               $row['link_regist'] = $row['seq_no'];
            }
            $this->load->library('pagination');
            $config['base_url'] = base_url("customer_service/print_student_list?". $this->getQueryString(array(), array('page')));
        }

        $config['total_rows'] = $total;
        $config['per_page'] = $rows;
        $this->pagination->initialize($config);
        $this->data['link_refresh'] = base_url("customer_service/print_student_list/");
        $this->layout->view('customer_service/print_student_list/list',$this->data);
    }

    public function show($seq_no=NULL)
    {
        if($_GET['csv'] == 1) $this->__result('3', '操作錯誤');

        $retirement = $_GET['retirement'];
        $ShowTel = $this->data['filter']['ShowTelChecked'] = $_GET['ShowTel'];
        $html = isset($_GET['html'])?$_GET['html']:'0';
        $this->data['class'] = $this->Regist_personnel_model->getConditions($seq_no);  //選定的class

        

        if(!isset($this->data['class'])){
            $this->__result('3', '操作錯誤!');
            redirect(base_url('customer_service/print_student_list/'));
        }
        $this->data['class']['yn_sel'] = array('1','3','4','5','8');
        $select = 'id,st_no,stop_reason,yn_sel,group_no';
        $memberId = $this->online_app_model->getMemberId($this->data['class'],$select);

        $st_no = array();
        foreach ($memberId as $key => $value) {
            $memberId[$key] = $value['id'];
            $st_no[$value['id']]['stno'] = $value['st_no'];
            $st_no[$value['id']]['group_no'] = $value['group_no'];
            $st_no[$value['id']]['stop_reason'] = is_null($value['stop_reason'])? '':$value['stop_reason'];
            $st_no[$value['id']]['yn_sel'] = is_null($value['yn_sel'])? '':$value['yn_sel'];
        }

       

        $select = 'idno,bureau_name,out_gov_name,bureau_id,job_title,`name`,cellphone,office_tel,gender,retirement';
        $memberData = $this->BS_user_model->getMemberData($memberId,$select);
        $member = array();

        foreach ($memberData as $key => $value) {
            $stno = $st_no[$value['idno']]['stno'];
            $member[$stno]['group_no'] = $st_no[$value['idno']]['group_no'];
            $member[$stno]['phone'] = is_null($value['office_tel'])? '': $value['office_tel'];
            $member[$stno]['bureau_name'] = !empty($value['out_gov_name'])? $value['out_gov_name']:$this->online_app_model->getBureau($value['bureau_id']);  //單位
            $member[$stno]['name'] = $value['name'];
            $member[$stno]['gender'] = $value['gender']=='F'? '女': '男';
            $member[$stno]['job_title'] =  $this->code_table_model->getJobTitle($value['job_title']);  //職稱
            $value['stop_reason'] = !isset($st_no[$value['idno']]['stop_reason'])? '':$st_no[$value['idno']]['stop_reason'];
            if($st_no[$value['idno']]['yn_sel']=='4'){
                $value['stop_reason'] = '退訓';  
            }elseif($st_no[$value['idno']]['yn_sel']=='5'){
                $value['stop_reason'] = '未報到';  
            }
            if($retirement==1){//檢查退休
                $value['retirement'] = ($value['retirement']=='0')? '退休':'';
                if($html==1){
                    $member[$stno]['stop_reason'] = ($value['retirement']=='')? $value['stop_reason']:'退休&nbsp;'.$value['stop_reason'];
                }else{
                    $member[$stno]['stop_reason'] = ($value['retirement']=='')? $value['stop_reason']:'退休 '.$value['stop_reason'];
                }
            }else{
                $member[$stno]['stop_reason'] = isset($value['stop_reason'])? $value['stop_reason']: '';
            }
        }
        //var_dump($member);
        ksort($member);
        if ($html==1){
            $this->data['memberData'] = $member;
            $this->data['link_refresh'] = base_url("customer_service/print_student_list/");
            $this->layout->view('customer_service/print_student_list/show', $this->data);

        }else{

            //--star PDF--//
            $this->load->library('pdf/PHP_TCPDF');
            ob_end_clean(); 
            $pdf = new PHP_TCPDF();
            $pdf->setPrintHeader(false); //不要頁首 背景滿版必須參數
            $pdf->setPrintFooter(false); //不要頁尾 背景滿版必須參數
            $pdf->setFontSubsetting(true);//有用到的字才放到文件中
            $pdf->SetFont('droidsansfallback', '', 12, '', false); //設定字型  設定true標楷體會破碎

            // set margins 背景滿版必須參數
            //$pdf->SetMargins(0, 0, 0, true); 
            $pdf->SetMargins(7,5,10,10);

            // set auto page breaks false  背景滿版必須參數
            $pdf->SetAutoPageBreak(false, 0); 

            $pdf->AddPage('P', 'A4');



            $title="臺北市政府公務人員訓練處           研習人員名冊";
            $title1=$this->data['class']['year']."年度  ".$this->data['class']['class_name']."  第".$this->data['class']['term']."期";
            //表頭
            $pdf->SetFont('droidsansfallback', '', 12, '', false);
            $pdf->Cell(180,5,$title,0,1,'C');

            $pdf->SetFont('droidsansfallback', '', 11, '', false);
            $pdf->Cell(180,5,$title1,0,1,'C');

            $pdf->SetFont('droidsansfallback', '', 10, '', false);//設定文字格式SetFont('字體名稱', '粗體', SIZE )


                        //沒組別沒電話
            $layoutParameter[0] = array(
                array(
                    'fieldName' => 'group_no',
                    'titileName' => '組別',
                    'width' => 10,
                    'align' => 'C',
                    'skip' => 0,
                    'end' => 0
                ),                
                array(
                    'fieldName' => 'ST_NO',
                    'titileName' => '學號',
                    'width' => 10,
                    'align' => 'C',
                    'skip' => 0,
                    'end' => 0
                ),
                array(
                    'fieldName' => 'bureau_name',
                    'titileName' => '服務機關',
                    'width' => 80,
                    'align' => 'L',
                    'skip' => 0,
                    'end' => 0
                ),
                array(
                    'fieldName' => 'job_title',
                    'titileName' => '職稱',
                    'width' => 40,
                    'align' => 'L',
                    'skip' => 0,
                    'end' => 0
                ),
                array(
                    'fieldName' => 'name',
                    'titileName' => '姓名',
                    'width' => 20,
                    'align' => 'L',
                    'skip' => 0,
                    'end' => 0
                ),
                array(
                    'fieldName' => 'gender',
                    'titileName' => '性別',
                    'width' => 10,
                    'align' => 'C',
                    'skip' => 0,
                    'end' => 0
                ),
                array(
                    'fieldName' => 'stop_reason',
                    'titileName' => '備註',
                    'width' => 20,
                    'align' => 'L',
                    'skip' => 0,
                    'end' => 1
                )
            );
            //沒組別有電話
            $layoutParameter[1] = array(
                array(
                    'fieldName' => 'group_no',
                    'titileName' => '組別',
                    'width' => 10,
                    'align' => 'C',
                    'skip' => 0,
                    'end' => 0
                ),                    
                array(
                    'fieldName' => 'ST_NO',
                    'titileName' => '學號',
                    'width' => 10,
                    'align' => 'C',
                    'skip' => 0,
                    'end' => 0
                ),
                array(
                    'fieldName' => 'bureau_name',
                    'titileName' => '服務機關',
                    'width' => 60,
                    'align' => 'L',
                    'skip' => 0,
                    'end' => 0
                ),
                array(
                    'fieldName' => 'job_title',
                    'titileName' => '職稱',
                    'width' => 40,
                    'align' => 'L',
                    'skip' => 0,
                    'end' => 0
                ),
                array(
                    'fieldName' => 'name',
                    'titileName' => '姓名',
                    'width' => 20,
                    'align' => 'L',
                    'skip' => 0,
                    'end' => 0
                ),
                array(
                    'fieldName' => 'gender',
                    'titileName' => '性別',
                    'width' => 10,
                    'align' => 'C',
                    'skip' => 0,
                    'end' => 0
                ),
                array(
                    'fieldName' => 'phone',
                    'titileName' => '電話',
                    'width' => 30,
                    'align' => 'C',
                    'skip' => 0,
                    'end' => 0
                ) ,
                array(
                    'fieldName' => 'stop_reason',
                    'titileName' => '備註',
                    'width' => 20,
                    'align' => 'L',
                    'skip' => 0,
                    'end' => 1
                )
            );
            if ($ShowTel==1) {
                $contentLayout = $layoutParameter[1];
            } else {
                $contentLayout = $layoutParameter[0];
            }

            $pdf->SetFont('droidsansfallback', '', 10, '', false);  //粗體

  
            foreach ($contentLayout as $key => $value) {
                if ($value['skip'] == 1) {
                    continue;
                }
                if ($value['end'] == 0) {
                    $pdf->Cell($value['width'],10,$value['titileName'],1,0,'C');
                } else {
                    $pdf->Cell($value['width'],10,$value['titileName'],1,1,'C');
                    break;
                }
            }
            $page_num=38;//一頁顯示的資料筆數
            $i=1;
            $page=1;//頁碼
            $total=count($member);//總筆數
            $page_total=ceil($total/$page_num);//總頁數
            foreach ($member as $stno => $data) {
                if($i>$page_num){
                    $pdf->Cell(180,15,"第".$page."/".$page_total."頁",0,1,"C");
                    $pdf->AddPage();
                    $pdf->Cell(180,5,"",0,1,'C');
                    //start 表頭
                    $pdf->SetFont('droidsansfallback', '', 12, '', false); //粗體
                    $pdf->Cell(180,5,$title,0,1,'C');

                    $pdf->SetFont('droidsansfallback', '', 11, '', false); //粗體
                    $pdf->Cell(180,5,$title1,0,1,'C');

                    $pdf->SetFont('droidsansfallback', '', 10, '', false); //粗體

                    foreach ($contentLayout as $key => $value) {
                        if ($value['skip'] == 1) {
                            continue;
                        }
                        if ($value['end'] == 0) {
                            $pdf->Cell($value['width'],10,$value['titileName'],1,0,'C');
                        } else {
                            $pdf->Cell($value['width'],10,$value['titileName'],1,1,'C');
                            break;
                        }
                            }
                    //end 表頭
                    $i=1;
                    $page++;
                }
                $pdf->SetFont('droidsansfallback', '', 10, '', false); //粗體
                foreach ($contentLayout as $key => $value) {
                    if ($value['skip'] == 1) {
                       continue;
                        }
                    if ($value["fieldName"]=='ST_NO') {
                        $data[$value["fieldName"]] = $stno;
                    }
                    if ($value['end'] == 0) {
                        $pdf->Cell($value["width"],6,$data[$value["fieldName"]],0,0,$value["align"]);
                    } else {
                        $pdf->Cell($value["width"],6,$data[$value["fieldName"]],0,1,$value["align"]);
                        break;
                    }

                }
                $i++;
            }
            $pdf->Cell(180,15,"第".$page."/".$page_total."頁",0,1,"C");
            $pdf->Output();
            ob_end_flush();
            //--end PDF--//
        }
            //    $this->data['memberData'] = $memberData;
            //    $this->data['link_refresh'] = base_url("customer_service/print_student_list/");
            //      $this->layout->view('customer_service/print_student_list/show', $this->data);
            //    $this->__result('0', $this->data);
    }

    public function csv()
    { //下載CSV
        if($_GET['csv'] == 0) $this->__result('3', '');
        $seq_no = $_GET['seq_no'];
        $this->data['class'] = $this->Regist_personnel_model->getConditions($seq_no);  //選定的class
        $ShowTel = $_GET['ShowTel'];
        $retirement = $_GET['retirement'];
         //設定瀏覽器讀取此份資料為不快取，與解讀行為是下載 CSV 檔案
        header("Pragma: no-cache");
        header("Expires: 0");
        header("Content-type: application/csv");
        //檔案名稱
        header("Content-Disposition: attachment; filename=".$this->data['class']['year'].iconv("UTF-8","big-5","年")."-".iconv("UTF-8","big-5",$this->data['class']['class_name'])."-".$this->data['class']['term'].".csv");
        //head
        $title[] = array($this->data['class']['year']."年 ".$this->data['class']['class_name']." 第".$this->data['class']['term']."期");
        $title[] = ($ShowTel ==1)? array('組別', '學號','服務機關','職稱','姓名','性別','電話','備註'):array('組別', '學號','服務機關','職稱','姓名','性別','備註');
        $this->data['class']['yn_sel'] = array('1','3','4','5','8');
        $select = 'id,st_no,stop_reason,yn_sel,group_no';
        $memberId = $this->online_app_model->getMemberId($this->data['class'],$select);
        
        $st_no = array();
        foreach ($memberId as $key => $value) {
            $memberId[$key] = $value['id'];
            $st_no[$value['id']]['stno'] = $value['st_no'];
            $st_no[$value['id']]['group_no'] = $value['group_no'];
            if($value['yn_sel']=='4'){
                $value['stop_reason']='退訓';
            }
            if($value['yn_sel']=='5'){
                $value['stop_reason']='未報到';
            }
            $st_no[$value['id']]['stop_reason'] = is_null($value['stop_reason'])? '':$value['stop_reason'];
        }
        

        $select = 'idno,bureau_name,out_gov_name,bureau_id,job_title,`name`,cellphone,office_tel,gender,retirement';
        $memberData = $this->BS_user_model->getMemberData($memberId,$select);

        foreach ($memberData as $k => $v) {
            $member['group_no'] = $st_no[$v['idno']]['group_no'];
            $member['st_no'] = $st_no[$v['idno']]['stno'];
            $member['stop_reason'] = $st_no[$v['idno']]['stop_reason'];
            $member['job_title'] =  $this->code_table_model->getJobTitle($v['job_title']);
            $member['bureau_name'] = !empty($v['out_gov_name'])? $v['out_gov_name'] : $this->online_app_model->getBureau($v['bureau_id']) ;
            $member['name'] = isset($v['name'])? $v['name'] : '';
            $member['stop_reason'] = !isset($member['stop_reason'])? '':$member['stop_reason'];
            if($retirement==1){
                $v['retirement'] = ($v['retirement']=='0')? '退休':'';
                $member['stop_reason'] = ($v['retirement']=='')? $member['stop_reason']:'退休 '.$member['stop_reason'];
            }else{
                $member['stop_reason'] = $member['stop_reason'];
            }
            if(isset($v['gender']) ){
                $member['gender'] = ($v['gender']=="M")?"男":"女";
            }else{
                $member['gender'] = '';
            }
            if($ShowTel==1){
                if(is_null($v['office_tel']) ){
                    $member['phone'] = '';
                }else{
                    $member['phone'] = $v['office_tel'];
                }
               //$csv_arr[] = array($member['st_no'],$member['bureau_name'],$member['job_title'],$member['name'],$member['gender'],$member['phone'],$member['stop_reason']);
                $csv_arr[$member['st_no']] = array($member['group_no'], $member['st_no'],$member['bureau_name'],$member['job_title'],$member['name'],$member['gender'],$member['phone'],$member['stop_reason']);
            }else{
               //$csv_arr[] = array($member['st_no'],$member['bureau_name'],$member['job_title'],$member['name'],$member['gender'],$member['stop_reason']);
                $csv_arr[$member['st_no']] = array($member['group_no'], $member['st_no'],$member['bureau_name'],$member['job_title'],$member['name'],$member['gender'],$member['stop_reason']);
            }
        }
        //檔案標頭如果沒補上 UTF-8 BOM 資訊的話，Excel 會解讀錯誤，偏向輸出給程式觀看的檔案
        echo "\xEF\xBB\xBF";
        echo $this->csvstr($title[0]) . PHP_EOL;
        echo $this->csvstr($title[1]) . PHP_EOL;

        ksort($csv_arr);
        $csv_arr = array_values($csv_arr);
        //正式循環輸出陣列內容
        for ($j = 0; $j < count($csv_arr); $j++) {

            //輸出符合規範的 CSV 字串以及斷行
            if(isset($csv_arr[$j]) ){
                echo $this->csvstr($csv_arr[$j]) . PHP_EOL;
            }
            //echo $this->csvstr($csv_arr[$j]) . PHP_EOL;
        }
    }

    //確保輸出內容符合 CSV 格式，定義下列方法來處理
    private function csvstr(array $fields): string{
        $f = fopen('php://memory', 'r+');
        if (fputcsv($f, $fields) === false) {
            return false;
        }
        rewind($f);
        $csv_line = stream_get_contents($f);
        return rtrim($csv_line);
    }

    public function _get_year_list()
    {
        $year_list = array();

        $date_now = new DateTime('now');
        $year_now = $date_now->format('Y');
        $this_yesr = $year_now - 1910;

        for($i=$this_yesr; $i>=90; $i--){
            $year_list[$i] = $i;
        }
        // jd($year_list,1);
        return $year_list;
    }
    private function __result( $code,$msg ){
        echo json_encode(array('status' => $code , 'msg' => $msg));
        exit;
    }

}

<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Print_student_list extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        if ($this->flags->is_login === FALSE) {
            redirect(base_url('welcome'));
        }

        $this->load->model('management/regist_contractors_model');
        $this->load->model('management/beaurau_persons_model');
        $this->load->model('management/code_table_model');
        $this->load->model('management/online_app_model');
        $this->load->model('customer_service/BS_user_model');

        $this->data['choices']['year'] = $this->_get_year_list();

        if (!isset($this->data['filter']['page'])) {
            $this->data['filter']['page'] = '1';
        }
        if (!isset($this->data['filter']['sort'])) {
            $this->data['filter']['sort'] = '';
        }
        if (!isset($this->data['filter']['term'])) {
            $this->data['filter']['term'] = '';
        }
        if (!isset($this->data['filter']['class_no'])) {
            $this->data['filter']['class_no'] = '';
        }
        if (!isset($this->data['filter']['class_name'])) {
            $this->data['filter']['class_name'] = '';
        }
        if (!isset($this->data['filter']['allQueryChecked'])) {
             $this->data['filter']['allQueryChecked'] = '';
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
        //$allQueryChecked = ($this->data['filter']['allQueryChecked'] != '')? $this->data['filter']['allQueryChecked']:0 ;
        if($this->data['filter']['allQueryChecked']!=''){
            $allQueryChecked=$this->data['filter']['allQueryChecked'];
        }else{
            $allQueryChecked=0;
        }
        $this->data['page_name'] = 'list';
        $page = $this->data['filter']['page'];
        $rows = $this->data['filter']['rows'];
        $idno=$this->flags->user['idno'];
        if($allQueryChecked == 0){
            $conditions = array();
            $conditions['year'] = $this->data['filter']['year'];
            //$conditions['outtray'] = 'Y';
            $conditions['worker']=$idno;

            $attrs = array(
                'conditions' => $conditions,
            );
            $attrs['class_status'] = array('2','3');
            if ($this->data['filter']['class_name'] !== '' ) {
                $attrs['class_name'] = $this->data['filter']['class_name'];
            }
            if ($this->data['filter']['class_no'] != '' ) {
                $attrs['class_no'] = $this->data['filter']['class_no'];
            }
            $this->data['filter']['total'] = $total = $this->regist_contractors_model->getListCount($attrs);
            $this->data['filter']['offset'] = $offset = ($page -1) * $rows;
            $attrs = array(
                'conditions' => $conditions,
                'rows' => $rows,
                'offset' => $offset,
            );
            $attrs['class_status'] = array('2','3');
            if ($this->data['filter']['class_name'] != '' ) {
                $attrs['class_name'] = $this->data['filter']['class_name'];
            }
            if ($this->data['filter']['class_no'] != '' ) {
                $attrs['class_no'] = $this->data['filter']['class_no'];
            }
            if ($this->data['filter']['sort'] != '' ) {
                $attrs['sort'] = $this->data['filter']['sort'];
            }
            $this->data['list'] = $this->regist_contractors_model->getList($attrs);
            // jd($this->data['list'],1);
            foreach ($this->data['list'] as & $row) {
                $row['link_regist'] = base_url("management/print_student_list/show/{$row['seq_no']}");
            }
            $this->load->library('pagination');
            $config['base_url'] = base_url("management/print_student_list?". $this->getQueryString(array(), array('page'))); 
            /*$this->data['list'] = array();
            $this->data['filter']['total'] = $total = 0;
            $this->data['filter']['offset'] = $offset = ($page -1) * $rows;*/
        }else{
            $conditions = array();
            $conditions['year'] = $this->data['filter']['year'];
            //$conditions['outtray'] = 'Y';
            $attrs = array(
                'conditions' => $conditions,
            );
            $attrs['class_status'] = array('2','3');
            if ($this->data['filter']['class_name'] !== '' ) {
                $attrs['class_name'] = $this->data['filter']['class_name'];
            }
            if ($this->data['filter']['class_no'] != '' ) {
                $attrs['class_no'] = $this->data['filter']['class_no'];
            }
            $this->data['filter']['total'] = $total = $this->regist_contractors_model->getListCount($attrs);
            $this->data['filter']['offset'] = $offset = ($page -1) * $rows;
            $attrs = array(
                'conditions' => $conditions,
                'rows' => $rows,
                'offset' => $offset,
            );
            $attrs['class_status'] = array('2','3');
            if ($this->data['filter']['class_name'] != '' ) {
                $attrs['class_name'] = $this->data['filter']['class_name'];
            }
            if ($this->data['filter']['class_no'] != '' ) {
                $attrs['class_no'] = $this->data['filter']['class_no'];
            }
            if ($this->data['filter']['sort'] != '' ) {
                $attrs['sort'] = $this->data['filter']['sort'];
            }
            $this->data['list'] = $this->regist_contractors_model->getList($attrs);
            // jd($this->data['list'],1);
            foreach ($this->data['list'] as & $row) {
                $row['link_regist'] = base_url("management/print_student_list/show/{$row['seq_no']}");
            }
            $this->load->library('pagination');
            $config['base_url'] = base_url("management/print_student_list?". $this->getQueryString(array(), array('page'))); 
        }    
        $config['total_rows'] = $total;
        $config['per_page'] = $rows;
        $this->pagination->initialize($config);
        $this->data['link_refresh'] = base_url("management/print_student_list/");
        $this->layout->view('management/print_student_list/list',$this->data);
    }
    public function show($seq_no=NULL)
    {   
        $this->load->library('pdf/PDF_Chinesess');
        $this->load->library('pdf/font/makefont/Makefont123');
        
        $ShowTel = $this->data['filter']['ShowTelChecked'] = $_GET['ShowTel'];
    	if($_GET['csv'] == 1) $this->setAlert(3, '操作錯誤');

    	$class = $this->data['class'] = $this->regist_contractors_model->get($seq_no);
        if(!isset($this->data['class'])){
            $this->setAlert(3, '操作錯誤');
            redirect(base_url('management/print_student_list/'));
        }
        $attrs = array();
        $attrs['year'] = $class['year'];
        $attrs['class_no'] = $class['class_no'];
        $attrs['term'] = $class['term'];
        $attrs['where_not_in'] = array('6','2','7') ;
        $class_list = $this->online_app_model->getList($attrs);
        $idarray=array();
        $st_no = array();
        foreach ($class_list as $key => $value) {//get idno
            $idarray[$key] = $value['id'];
            $st_no[$value['id']]['st_no'] = $value['st_no'];
        }
        $select = 'idno,bureau_name,bureau_id,job_title,`name`,cellphone,office_tel,gender';
        $memberData = $this->BS_user_model->getMemberData($idarray,$select);
        $member = array();
        foreach ($memberData as $key => $value) {
        	$stno = $st_no[$value['idno']]['st_no'];
            $member[$stno]['phone'] = is_null($value['cellphone'])? $value['office_tel']: $value['cellphone'];
            $member[$stno]['bureau_name'] = is_null($value['bureau_name'])? $this->online_app_model->getBureau($value['bureau_id']): $value['bureau_name'] ;  //單位
            $member[$stno]['name'] = $value['name'];
            $member[$stno]['gender'] = $value['gender']=='F'? '女': '男';
            $member[$stno]['job_title'] =  $this->code_table_model->getJobTitle($value['job_title']);  //職稱
        }
        ksort($member);
        //--star PDF--//

        $pdf=new PDF_Chinesess();
        $pdf->AddPage();
        $pdf->AddBig5Font('uni', '黑体');
//        $pdf->AddGBFont('uni', '黑体');
        $pdf->SetFont('uni', '', 13);
        $pdf->SetMargins(7,5,10,10);
        /*
        $pdf=new PDF_Unicode();
        $pdf->Open();
        $pdf->AddPage();
        
        $pdf->AddUniCNShwFont('uni');  //fontA 可用習慣名稱
        $pdf->SetAutoPageBreak(false);
        */

        $title="臺北市政府公務人員訓練處           研習人員名冊"; 
        $title1=$attrs['year']."年度  ".$class['class_name']."  第".$attrs['term']."期";
        //表頭
        $pdf->SetFont('uni', 'B', 12 );
        $pdf->Cell(180,5,iconv("utf-8","big5",$title),0,1,'C');
        $pdf->SetFont('uni', 'B', 11 );
        $pdf->Cell(180,5,iconv("utf-8","big5",$title1),0,1,'C' );
        $pdf->SetFont('uni', 'B', 10 );          //設定文字格式SetFont('字體名稱', '粗體', SIZE )
        //沒組別沒電話
        $layoutParameter[0] = array(
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
                'titileName' => '服務單位',
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
                'end' => 1
            )
        );
        //沒組別有電話
        $layoutParameter[1] = array(
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
                'titileName' => '服務單位',
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
                'end' => 1
            )          
        );
        if ($ShowTel) {
            $contentLayout = $layoutParameter[1];
        } else {
            $contentLayout = $layoutParameter[0];
        }
        $pdf->SetFont('uni', 'B', 10 );
        //$pdf->SetFontSize(10);
        foreach ($contentLayout as $key => $value) {
            if ($value['skip'] == 1) {
                continue;
            }
            if ($value['end'] == 0) {
                $pdf->Cell($value['width'],10,iconv("utf-8","big5",$value['titileName']),1,0,'C');
            } else {
                $pdf->Cell($value['width'],10,iconv("utf-8","big5",$value['titileName']),1,1,'C');
                break;
            }
        }
        $page_num=38;//一頁顯示的資料筆數
        $i=1;
        $page=1;//頁碼
        $total=count($member);//總筆數
        $page_total=ceil($total/$page_num);//總頁數
        
   //     while ($arr = $member->FetchRow()):
        foreach ($member as $stno => $data) {
            if($i>$page_num){
                $pdf->Cell(180,15,iconv("utf-8","big5","第".$page."/".$page_total."頁"),0,1,"C");
                $pdf->AddPage();
                $pdf->Cell(180,5,"",0,1,'C');
                //start 表頭
                $pdf->SetFont('uni', 'B', 12 );
                //$pdf->SetFontSize(12);
                $pdf->Cell(180,5,iconv("utf-8","big5",$title),0,1,'C');
                $pdf->SetFont('uni', 'B', 11 );
                //$pdf->SetFontSize(10);
                $pdf->Cell(180,5,iconv("utf-8","big5",$title1),0,1,'C');
                $pdf->SetFont('uni', 'B', 10 );
                foreach ($contentLayout as $key => $value) {
                    if ($value['skip'] == 1) {
                        continue;
                    }
                    if ($value['end'] == 0) {
                        $pdf->Cell($value['width'],10,iconv("utf-8","big5",$value['titileName']),1,0,'C');
                    } else {
                        $pdf->Cell($value['width'],10,iconv("utf-8","big5",$value['titileName']),1,1,'C');
                        break;
                    }
                        }
                //end 表頭
                $i=1;
                $page++;
            }
            $pdf->SetFont('uni', 'B', 10 );
            // if ($tmp_seq!='0') {
            //      if ($beaurau_id==$arr["BEAURAU_ID"]) {
            //         $pdf->SetFont('msungstdlight', 'U', 11 );          //設定文字格式SetFont('字體名稱', '底線', SIZE )
            //         $arr["ST_NO"] = "*".$arr["ST_NO"];
            //     } else {
            //         $arr["ST_NO"] = $arr["ST_NO"];
            //         $pdf->SetFont('msungstdlight', 'BI', 10 );          //設定文字格式SetFont('字體名稱', '粗體', SIZE )
            //     }
            // }
            //$pdf->SetFont('msungstdlight', 'BI', 10 );
            // $arr['NOTE'] = '';
            // if ($arr["YN_SEL"] == 4) {
            //     $arr['NOTE'] = '退訓';
            // } else if ($arr["YN_SEL"] == 5) {
            //     $arr['NOTE'] = '未報到';
            // }
        
            foreach ($contentLayout as $key => $value) {
                if ($value['skip'] == 1) {
                   continue;
                    }
                if ($value["fieldName"]=='ST_NO') {
                    $data[$value["fieldName"]] = $stno;
                }
                // truncate str if strlen is large
                // if ($value["fieldName"]=='job_title') {
                //     $titlename = mb_substr($data[$value["fieldName"]],0,MAX_TITLE_LEN,'utf8');
                //     if ($titlename!=$data[$value["fieldName"]]) {
                //         $data[$value["fieldName"]] = $titlename."#";
                //     }
                // }   
                // if ($value["fieldName"]=='bureau_name') {
                //     $bureau = mb_substr($data[$value["fieldName"]],0,MAX_BUREAU_LEN,'utf8');
                //     if ($bureau!=$data[$value["fieldName"]]) {
                //         $data[$value["fieldName"]] = $bureau."#";
                //     }
                // }   
                if ($value['end'] == 0) {
                    $pdf->Cell($value["width"],6,iconv("utf-8","big5//TRANSLIT",$data[$value["fieldName"]]),0,0,$value["align"]);
                } else {
                    $pdf->Cell($value["width"],6,iconv("utf-8","big5//TRANSLIT",$data[$value["fieldName"]]),0,1,$value["align"]);
                    break;
                }
                
            }
            $i++;
        }   
        $pdf->Cell(180,15,iconv("utf-8","big5","第".$page."/".$page_total."頁"),0,1,"C");
        $pdf->Output();
        ob_end_flush();
        // $this->data['list'] = $member;
        // $this->load->library('pagination');
        // $this->data['filter']['total'] = count($idarray);
        // $this->data['link_refresh'] = base_url("management/print_student_list/");
        // $this->layout->view('management/print_student_list/show',$this->data);
    }
    public function csv(){ //下載CSV
        if($_GET['csv'] == 0){
            $this->setAlert(3, '操作錯誤');
            redirect(base_url('management/print_student_list/'));
        }
        $seq_no = $_GET['seq_no'];
        $ShowTel = $_GET['ShowTel'];
        $class = $this->regist_contractors_model->get($seq_no);  //選定的class
        $attrs = array();
        $attrs['year'] = $class['year'];
        $attrs['class_no'] = $class['class_no'];
        $attrs['term'] = $class['term'];
       
         //設定瀏覽器讀取此份資料為不快取，與解讀行為是下載 CSV 檔案
        header("Pragma: no-cache"); 
        header("Expires: 0"); 
        header("Content-type: application/csv");
        //檔案名稱
        $filename = $class['year'].iconv("UTF-8","big-5","年")."-".iconv("UTF-8","big-5",$class['class_name'])."-".$class['term'];
        header("Content-Disposition: attachment; filename=". $filename.".csv"); 
        //head
        $csv_arr[] = array($class['year']."年 ".$class['class_name']." 第".$class['term']."期");
        $csv_arr[] = ($ShowTel ==1)? array('學號','服務單位','職稱','姓名','性別','電話'):array('學號','服務單位','職稱','姓名','性別');
        $attrs['where_not_in'] = array('6','2','7') ;
        $class_list = $this->online_app_model->getList($attrs);
        $idarray=array();
        $st_no = array();
        foreach ($class_list as $key => $value) {//get idno
            $idarray[$key] = $value['id'];
            $st_no[$value['id']]['st_no'] = $value['st_no'];
        }
        $select = 'idno,bureau_name,bureau_id,job_title,`name`,cellphone,office_tel,gender';
        $memberData = $this->BS_user_model->getMemberData($idarray,$select);
        $member = array();
        foreach ($memberData as $key => $value) {
            $stno = $st_no[$value['idno']]['st_no'];
            $member[$stno]['phone'] = is_null($value['cellphone'])? $value['office_tel']: $value['cellphone'];
            $member[$stno]['bureau_name'] = is_null($value['bureau_name'])? $this->online_app_model->getBureau($value['bureau_id']): $value['bureau_name'] ;  //單位
            $member[$stno]['name'] = $value['name'];
            $member[$stno]['gender'] = $value['gender']=='F'? '女': '男';
            $member[$stno]['job_title'] =  $this->code_table_model->getJobTitle($value['job_title']);  //職稱
        }
        ksort($member);

        foreach ($member as $stno => $data) {
        	if($ShowTel==1){
            	$csv_arr[] = array($stno,$data['bureau_name'],$data['job_title'],$data['name'],$data['gender'],$data['phone']);
            }else{
            	$csv_arr[] = array($stno,$data['bureau_name'],$data['job_title'],$data['name'],$data['gender']);
            }
        }
        //正式循環輸出陣列內容
        for ($j = 0; $j < count($csv_arr); $j++) {
            if ($j == 0) {
                //檔案標頭如果沒補上 UTF-8 BOM 資訊的話，Excel 會解讀錯誤，偏向輸出給程式觀看的檔案
                echo "\xEF\xBB\xBF";
            }
            //輸出符合規範的 CSV 字串以及斷行
            echo $this->csvstr($csv_arr[$j]) . PHP_EOL;
        }
    }
        
    //確保輸出內容符合 CSV 格式，定義下列方法來處理
    private function csvstr(array $fields): string{
        $f = fopen('php://memory', 'r+');
        $fields = array_map(function($field){
            return str_replace(array("\r\n", "\n", "\r"), '', $field); 
        }, $fields);
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
}
 ?>
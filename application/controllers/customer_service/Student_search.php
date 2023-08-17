<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Student_search extends MY_Controller
{
    private $searchValue = 0;

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
        if (!isset($this->data['filter']['bureau_name'])) {
            $this->data['filter']['bureau_name'] = '';
        }
        if (!isset($this->data['filter']['term'])) {
            $this->data['filter']['term'] = '';
        }
        if (!isset($this->data['filter']['name'])) {
            $this->data['filter']['name'] = '';
        }
        if (!isset($this->data['filter']['idno'])) {
            $this->data['filter']['idno'] = '';
        }
        if (!isset($this->data['filter']['classday_s'])) {
            $this->data['filter']['classday_s'] = '';
        }
        if (!isset($this->data['filter']['classday_e'])) {
            $this->data['filter']['classday_e'] = '';
        }
        if (!isset($this->data['filter']['birthday_s'])) {
            $this->data['filter']['birthday_s'] = '';
        }
        if (!isset($this->data['filter']['birthday_e'])) {
            $this->data['filter']['birthday_e'] = '';
        }
        //allQueryChecked**//
        if (!isset($this->data['filter']['allQueryChecked'])) {
             $this->data['filter']['allQueryChecked'] = '';
        }
        if (!isset($this->data['filter']['csv'])) {
             $this->data['filter']['csv'] = '0';
        }
        if (!isset($this->data['filter']['gender'])) {
             $this->data['filter']['gender'] = '';
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
        $allQueryChecked = ($this->data['filter']['allQueryChecked'] != '')? $this->data['filter']['allQueryChecked']:0 ;
        $this->data['page_name'] = 'list';
        $page = $this->data['filter']['page'];
        $rows = $this->data['filter']['rows'];
        if($allQueryChecked == 0){
            $this->data['list'] = array();

            $attrs = array();  
            $attrs['class_status'] = array('2','3');
            //$attrs['worker']=$this->flags->user['idno'];
            if ($this->data['filter']['class_no'] != '' ) {
                $attrs['class_no'] = $this->data['filter']['class_no'];
            }else{
                $attrs['class_no'] = '';
            }
            if ($this->data['filter']['term'] !== '' ) {
                $attrs['term'] = $this->data['filter']['term'];
            }else{
                $attrs['term'] = '';
            }
            if ($this->data['filter']['sort'] != '' ) {
                $attrs['sort'] = $this->data['filter']['sort'];
            }else{
                $attrs['sort'] = '';
            }
            $conditions = array('year' =>$this->data['filter']['year']);   
            $attrs['conditions'] = $conditions;
            $user_conditions = array(   'bureau_name'=>$this->data['filter']['bureau_name'],
                                        'conditions' =>array(),
                                        'name'       =>$this->data['filter']['name'] );
            if($this->data['filter']['gender']!=='')     $user_conditions['conditions'] += array('gender' =>$this->data['filter']['gender']);  

            if($this->data['filter']['idno']!=='')       $user_conditions['conditions'] += array('idno' =>$this->data['filter']['idno']); 

            if($this->data['filter']['birthday_s']!=='') $user_conditions['conditions'] += array('birthday >=' =>$this->data['filter']['birthday_s']); 

            if($this->data['filter']['birthday_e']!=='') $user_conditions['conditions'] += array('birthday <=' =>$this->data['filter']['birthday_e']);
            foreach ($user_conditions as $key => $value) {
                if(is_array($value) ){
                    if(count($value) ==1 && isset($value['gender']) ){
                        $this->searchValue +=1;
                    }elseif(count($value) >0){ //只選擇性別資料過多不篩選會員
                        $this->searchValue +=2;
                    }
                }elseif($value !=='' ){
                    $this->searchValue +=2;
                }
            }
            $this->data['filter']['offset'] = $offset = ($page -1) * $rows;
            $attrs['conditions']['worker']=$this->flags->user['idno'];
            if ($this->searchValue > 1 ){ //使用人員資料查詢
                $user_conditions['select'] = 'idno'; //select what
                $userdata = $this->BS_user_model->getList($user_conditions); 
                $idarray = array(); 
                if(count($userdata)==0){ //無符合的使用者
                    $this->data['list']=array();
                    $this->data['filter']['total'] = $total = 0;
                }else{
                    foreach ($userdata as $key => $value) {
                        $idarray[$key] = $value['idno'];
                    }
                    $attrs += array('id'=>$idarray);
                    $this->data['filter']['total'] = $total = $this->online_app_model->getMemberCount($attrs); //查詢會員資料
                    if($total ==0){
                        $this->data['list']=array();
                    }else{
                        $attrs['rows'] = $rows;
                        $attrs['offset'] = $offset;
                        $MemberClassData = $this->online_app_model->getMemberClassData($attrs); //查詢會員資料
                        foreach ($MemberClassData as $key => $value) {
                            $classData = $this->Regist_personnel_model->getClassData($value);
                            foreach ($classData as $v) {
                                $seq_no = $v['seq_no'];
                                $class_name = $v['class_name']; 
                            }
                            $this->data['list'][$seq_no] = array('year'        =>$value['year'],
                                                                 'class_no'    =>$value['class_no'],
                                                                 'term'        =>$value['term'],
                                                                 'class_name'  =>$class_name,
                                                                 'link_regist' =>$seq_no);
                        }
                    }
                }
            }else{            
                if($this->data['filter']['classday_s']!=='') $conditions += array('start_date1' =>$this->data['filter']['classday_s']);   
                
                if($this->data['filter']['classday_e']!=='') $conditions += array('end_date1'  =>$this->data['filter']['classday_e']); 
                
                
                if ($this->data['filter']['class_name'] !== '' ) {
                    $attrs['class_name'] = $this->data['filter']['class_name'];
                }else{
                    $attrs['class_name'] = '';
                }

                

                $this->data['filter']['total'] = $total = $this->Regist_personnel_model->getListCount($attrs);  
                $attrs['rows'] = $rows;
                $attrs['offset'] = $offset;
                $this->data['list'] = $this->Regist_personnel_model->getList($attrs);
                foreach ($this->data['list'] as & $row) {
                // $row['link_regist'] = base_url("customer_service/student_search/show/{$row['seq_no']}");
                    $row['link_regist'] = $row['seq_no'];
                }
            }
            $this->load->library('pagination');
            $config['base_url'] = base_url("customer_service/student_search?". $this->getQueryString(array(), array('page')));
            //$this->data['filter']['total'] = $total = 0;原本
            //$this->data['filter']['offset'] = $offset = ($page -1) * $rows;原本
        }else{
            $attrs = array();  
            $attrs['class_status'] = array('2','3');
            if ($this->data['filter']['class_no'] != '' ) {
                $attrs['class_no'] = $this->data['filter']['class_no'];
            }else{
                $attrs['class_no'] = '';
            }
            if ($this->data['filter']['term'] !== '' ) {
                $attrs['term'] = $this->data['filter']['term'];
            }else{
                $attrs['term'] = '';
            }
            if ($this->data['filter']['sort'] != '' ) {
                $attrs['sort'] = $this->data['filter']['sort'];
            }else{
                $attrs['sort'] = '';
            }
            $conditions = array('year' =>$this->data['filter']['year']);   
            $attrs['conditions'] = $conditions;
            $user_conditions = array(   'bureau_name'=>$this->data['filter']['bureau_name'],
                                        'conditions' =>array(),
                                        'name'       =>$this->data['filter']['name'] );
            if($this->data['filter']['gender']!=='')     $user_conditions['conditions'] += array('gender' =>$this->data['filter']['gender']);  

            if($this->data['filter']['idno']!=='')       $user_conditions['conditions'] += array('idno' =>$this->data['filter']['idno']); 

            if($this->data['filter']['birthday_s']!=='') $user_conditions['conditions'] += array('birthday >=' =>$this->data['filter']['birthday_s']); 

            if($this->data['filter']['birthday_e']!=='') $user_conditions['conditions'] += array('birthday <=' =>$this->data['filter']['birthday_e']);
            foreach ($user_conditions as $key => $value) {
                if(is_array($value) ){
                    if(count($value) ==1 && isset($value['gender']) ){
                        $this->searchValue +=1;
                    }elseif(count($value) >0){ //只選擇性別資料過多不篩選會員
                        $this->searchValue +=2;
                    }
                }elseif($value !=='' ){
                    $this->searchValue +=2;
                }
            }
            $this->data['filter']['offset'] = $offset = ($page -1) * $rows;
            
            if ($this->searchValue > 1 ){ //使用人員資料查詢
                $user_conditions['select'] = 'idno'; //select what
                $userdata = $this->BS_user_model->getList($user_conditions); 
                $idarray = array(); 
                if(count($userdata)==0){ //無符合的使用者
                    $this->data['list']=array();
                    $this->data['filter']['total'] = $total = 0;
                }else{
                    foreach ($userdata as $key => $value) {
                        $idarray[$key] = $value['idno'];
                    }
                    $attrs += array('id'=>$idarray);
                    $this->data['filter']['total'] = $total = $this->online_app_model->getMemberCount($attrs); //查詢會員資料
                    if($total ==0){
                        $this->data['list']=array();
                    }else{
                        $attrs['rows'] = $rows;
                        $attrs['offset'] = $offset;
                        $MemberClassData = $this->online_app_model->getMemberClassData($attrs); //查詢會員資料
                        foreach ($MemberClassData as $key => $value) {
                            $classData = $this->Regist_personnel_model->getClassData($value);
                            foreach ($classData as $v) {
                                $seq_no = $v['seq_no'];
                                $class_name = $v['class_name']; 
                            }
                            $this->data['list'][$seq_no] = array('year'        =>$value['year'],
                                                                 'class_no'    =>$value['class_no'],
                                                                 'term'        =>$value['term'],
                                                                 'class_name'  =>$class_name,
                                                                 'link_regist' =>$seq_no);
                        }
                    }
                }
            }else{            
                if($this->data['filter']['classday_s']!=='') $conditions += array('start_date1' =>$this->data['filter']['classday_s']);   
                
                if($this->data['filter']['classday_e']!=='') $conditions += array('end_date1'  =>$this->data['filter']['classday_e']); 
                
                
                if ($this->data['filter']['class_name'] !== '' ) {
                    $attrs['class_name'] = $this->data['filter']['class_name'];
                }else{
                    $attrs['class_name'] = '';
                }

                $this->data['filter']['total'] = $total = $this->Regist_personnel_model->getListCount($attrs);  
                $attrs['rows'] = $rows;
                $attrs['offset'] = $offset;
                $this->data['list'] = $this->Regist_personnel_model->getList($attrs);
                foreach ($this->data['list'] as & $row) {
                // $row['link_regist'] = base_url("customer_service/student_search/show/{$row['seq_no']}");
                    $row['link_regist'] = $row['seq_no'];
                }
            }
            $this->load->library('pagination');
            $config['base_url'] = base_url("customer_service/student_search?". $this->getQueryString(array(), array('page')));
        }
        $config['total_rows'] = $total;
        $config['per_page'] = $rows;
        $this->pagination->initialize($config);
        $this->data['link_refresh'] = base_url("customer_service/student_search/");
        $this->layout->view('customer_service/student_search/list',$this->data);
    
    }
    public function show($seq_no=NULL){
        $this->load->library('pdf/PDF_Chinesess');

        if($_GET['csv'] == 1) $this->__result('3', '操作錯誤');
        
        $ShowTel = $this->data['filter']['ShowTelChecked'] = $_GET['ShowTel'];
        $this->data['class'] = $this->Regist_personnel_model->getConditions($seq_no);  //選定的class
        if(!isset($this->data['class'])){
            $this->setAlert(3, '操作錯誤');
            redirect(base_url('customer_service/student_search/'));
        }
        $select = 'id,st_no,stop_reason';
        $this->data['class']['yn_sel'] = array('1','3','4','5','8');
        $memberId = $this->online_app_model->getMemberId($this->data['class'],$select); //get idno
        $st_no = array();
        foreach ($memberId as $key => $value) {
            $memberId[$key] = $value['id'];
            $st_no[$value['id']]['stno'] = $value['st_no'];
            $st_no[$value['id']]['stop_reason'] = is_null($value['stop_reason'])? '':$value['stop_reason'];
        }
        $select = 'idno,bureau_name,bureau_id,job_title,`name`,cellphone,office_tel,gender';
        $memberData = $this->BS_user_model->getMemberData($memberId,$select);
        $member = array();
        foreach ($memberData as $key => $value) {
            $stno = $st_no[$value['idno']]['stno'];
            $member[$stno]['phone'] = is_null($value['office_tel'])? '': $value['office_tel'];
            $member[$stno]['bureau_name'] = is_null($value['bureau_name'])? $this->online_app_model->getBureau($value['bureau_id']): $value['bureau_name'] ;  //單位
            $member[$stno]['name'] = $value['name'];
            $member[$stno]['gender'] = $value['gender']=='F'? '女': '男';
            $member[$stno]['job_title'] =  $this->code_table_model->getJobTitle($value['job_title']);  //職稱
            $member[$stno]['stop_reason'] = $st_no[$value['idno']]['stop_reason'];
        }
        ksort($member);
        //--star PDF--//

        $pdf=new PDF_Chinesess();
        $pdf->AddPage();
        $pdf->AddBig5Font('uni', '黑体');
        //$pdf->AddGBFont('uni', '黑体');
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
        $title1=$this->data['class']['year']."年度  ".$this->data['class']['class_name']."  第".$this->data['class']['term']."期";
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
            foreach ($contentLayout as $key => $value) {
                if ($value['skip'] == 1) {
                   continue;
                    }
                if ($value["fieldName"]=='ST_NO') {
                    $data[$value["fieldName"]] = $stno;
                }
                if ($value['end'] == 0) {
                    $pdf->Cell($value["width"],6,iconv("utf-8","big5//TRANSLIT",$data[$value["fieldName"]]),0,0,$value["align"]);
                } else {
                    $pdf->Cell($value["width"],6,iconv("utf-8","big5",$data[$value["fieldName"]]),0,1,$value["align"]);
                    break;
                }
                
            }
            $i++;
        }   
        $pdf->Cell(180,15,iconv("utf-8","big5","第".$page."/".$page_total."頁"),0,1,"C");
        $pdf->Output();
        ob_end_flush();
    //    $this->data['memberData'] = $member;
    //    $this->data['link_refresh'] = base_url("customer_service/student_search/");
    //      $this->layout->view('customer_service/student_search/show', $this->data);
           
    }

    public function csv(){ //下載CSV
        if($_GET['csv'] == 0) $this->__result('3', '');
        $seq_no = $_GET['seq_no'];
        $this->data['class'] = $this->Regist_personnel_model->getConditions($seq_no);  //選定的class
        $ShowTel = $_GET['ShowTel'];
       
         //設定瀏覽器讀取此份資料為不快取，與解讀行為是下載 CSV 檔案
        header("Pragma: no-cache"); 
        header("Expires: 0"); 
        header("Content-type: application/csv");
        //檔案名稱
        header("Content-Disposition: attachment; filename=".$this->data['class']['year'].iconv("UTF-8","big-5","年")."-".iconv("UTF-8","big-5",$this->data['class']['class_name'])."-".$this->data['class']['term'].".csv"); 
        //head
        $csv_arr[] = array($this->data['class']['year']."年 ".$this->data['class']['class_name']." 第".$this->data['class']['term']."期");
        $csv_arr[] = ($ShowTel ==1)? array('學號','服務單位','職稱','姓名','性別','電話','備註'):array('學號','服務單位','職稱','姓名','性別','備註');
        $select = 'id,st_no,stop_reason';
        $this->data['class']['yn_sel'] = array('1','3','4','5','8');
        $memberId = $this->online_app_model->getMemberId($this->data['class'],$select); 
        foreach ($memberId as $key => $value) {
            $st_no[$value['id']]['stno'] = $value['st_no'];
            $st_no[$value['id']]['stop_reason'] = is_null($value['stop_reason'])? '':$value['stop_reason'];
            $memberId[$key] = $value['id'];
        }
        $MemberData = $this->BS_user_model->getMemberData($memberId);
        foreach ($MemberData as $k => $v) {
            $Membe = array();
            $Membe['st_no'] = $st_no[$v['idno']]['stno'];
            $Membe['job_title'] =  $this->code_table_model->getJobTitle($v['job_title']);
            $Membe['bureau_name'] = isset($v['bureau_name'])? $v['bureau_name'] : $this->online_app_model->getBureau($v['bureau_id']) ;
            $Membe['name'] = isset($v['name'])? $v['name'] : '';
            $Membe['stop_reason'] = $st_no[$v['idno']]['stop_reason'];
            if(isset($v['gender']) ){
                $Membe['gender'] = ($v['gender']=="M")?"男":"女";
            }else{
                $Membe['gender'] = '';
            }
            if($ShowTel==1){
                if(is_null($v['office_tel']) ){
                    $Membe['phone'] = '';
                }else{
                    $Membe['phone'] = $v['office_tel'];
                }
               $csv_arr[] = array($Membe['st_no'],$Membe['bureau_name'],$Membe['job_title'],$Membe['name'],$Membe['gender'],$Membe['phone'],$Membe['stop_reason']);
            }else{
               $csv_arr[] = array($Membe['st_no'],$Membe['bureau_name'],$Membe['job_title'],$Membe['name'],$Membe['gender'],$Membe['stop_reason']);
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

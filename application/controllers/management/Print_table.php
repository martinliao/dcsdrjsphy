<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Print_table extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        if ($this->flags->is_login === FALSE) {
            redirect(base_url('welcome'));
        }
        $this->data['username'] = $this->input->cookie('username');
        $this->load->model('management/regist_contractors_model');
        $this->load->model('management/beaurau_persons_model');
        $this->load->model('management/code_table_model');
        $this->load->model('management/online_app_model');
        $this->load->model('management/classroom_model');
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
        if (!isset($this->data['filter']['serchChecked'])) {
             $this->data['filter']['serchChecked'] = '0';
        }
        if (!isset($this->data['filter']['printAll'])) {
             $this->data['filter']['printAll'] = '0';
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
        
        $workerid = $this->BS_user_model->getWorkerID($this->data['username'] );
        $this->data['page_name'] = 'list';
        $serchChecked = $this->data['filter']['serchChecked'];
        $page = $this->data['filter']['page'];
        $rows = $this->data['filter']['rows'];
        $conditions = array();
        $conditions['year'] = $this->data['filter']['year'];
        if($serchChecked!=1){
            $conditions['worker'] = $workerid;
        }
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
        if($serchChecked!==1){
            $attrs['worker'] = $workerid;
        }
        
        if($this->data['filter']['printAll']!=0){
            $this->data['list'] = $this->regist_contractors_model->getList($attrs);
        
            $attr = array();
            foreach ($this->data['list'] as & $row) {
                $row['link_regist'] = base_url("management/Print_table/CheckinFrom/{$row['seq_no']}");
                $row['seq_no'] = $row['seq_no'];
                $seat =  $this->classroom_model->getList($row);
                $i=0;
                foreach ($seat as $key => $value) {
                    $row['room'][$i]['room_id'] =$value['ROOM_ID']; 
                    $row['room'][$i]['name']    =$value['NAME'];
                    $row['room'][$i]['is_seat'] =$value['IS_SEAT'];
                    $i = $i+1;
                }
            }
        }else{
            $this->data['list']=null;
        }


        $this->load->library('pagination');
        $config['base_url'] = base_url("management/Print_table?". $this->getQueryString(array(), array('page'))); 
        
        $config['total_rows'] = $total;
        $config['per_page'] = $rows;
        $this->pagination->initialize($config);
        $this->data['link_refresh'] = base_url("management/Print_table/");
        $this->layout->view('management/print_table/list',$this->data);
    }

    public function checkinFrom($seq_no=NULL){
        $this->load->library('pdf/PDF_Chinesess');

        $class = $this->regist_contractors_model->get($seq_no);
        $type =  $_GET['type'];
        if(!isset($class) || !isset($type) ){
            $this->setAlert(3, '操作錯誤');
            redirect(base_url('management/Print_table/'));
        }
        $attrs = array();
        $attrs['year'] = $class['year'];
        $attrs['class_no'] = $class['class_no'];
        $attrs['term'] = $class['term'];
        $attrs['where_in']['field'] = 'yn_sel';
        $attrs['where_in']['value'] = array('1','3','4','5','8');
        $class_list = $this->online_app_model->getList($attrs);
        $idarray=array();
        $st_no = array();
        $group = 0;
        foreach ($class_list as $key => $value) {//get idno
            $idarray[$key] = $value['id'];
            $st_no[$value['id']]['st_no'] = $value['st_no'];
            $st_no[$value['id']]['group_no'] = $value['group_no'];
            if ( $value['group_no'] =='' ||is_null( $value['group_no']))  $value['group_no'] = 0;
            
            $group = $group + $value['group_no'];
        }
        $select = 'idno,bureau_name,bureau_id,job_title,`name`,cellphone,office_tel,gender';
        $memberData = $this->BS_user_model->getMemberData($idarray,$select);
        $member = array();
        foreach ($memberData as $key => $value) {
            $stno = $st_no[$value['idno']]['st_no'];
            $member[$stno]['group_no'] = $st_no[$value['idno']]['group_no'];  //分組
            $member[$stno]['phone'] = is_null($value['cellphone'])? $value['office_tel']: $value['cellphone'];
            $member[$stno]['bureau_name'] = is_null($value['bureau_name'])? $this->online_app_model->getBureau($value['bureau_id']): $value['bureau_name'] ;  //單位
            $member[$stno]['name'] = $value['name'];
            $member[$stno]['gender'] = $value['gender']=='F'? '女': '男';
            $member[$stno]['job_title'] =  $this->code_table_model->getJobTitle($value['job_title']);  //職稱
        }
        ksort($member);

        $tmp_group_array = array();
        foreach ($member as $key => $value) {
            if(empty($tmp_group_array)){
                $tmp_group_array[] = $value['group_no'];
            } else {
                if(!in_array($value['group_no'], $tmp_group_array)){
                    $tmp_group_array[] = $value['group_no'];
                }
            }
        }

        $tmp_member = $member;
        $member = array();
        foreach($tmp_group_array as $key => $value){
            foreach ($tmp_member as $key2 => $value2) {
                if($value2['group_no'] == $value){
                    $member[$key2] = $value2;
                }
            }
        }
        
        
        //--star PDF--//

        $pdf=new PDF_Chinesess();
        $pdf->AddPage();
        $pdf->AddBig5Font('uni', '黑体');
//        $pdf->AddGBFont('uni', '黑体');
        $pdf->SetFont('uni', '', 13);
        $pdf->SetMargins(15,5,15,10);
        /*
        $pdf=new PDF_Unicode();
        $pdf->Open();
        $pdf->AddPage();
        
        $pdf->AddUniCNShwFont('uni');  //fontA 可用習慣名稱
        $pdf->SetAutoPageBreak(false);
        */
        if($type==0){ //無茹素簽到表
            $title="臺北市政府公務人員訓練處 ".$attrs['year']."年度 ".$class['class_name']." 第".$attrs['term']."期 研習人員簽到表"; 

            $pdf->SetFont('uni', 'B', 12 );

            $pdf->Cell(180,10,iconv("utf-8","big5",$title),0,1,'C');
            if($group>0){
                $pdf->Cell(10,10,iconv("utf-8","big5","組別"),1,0,'C');
                $pdf->Cell(10,10,iconv("utf-8","big5","學號"),1,0,'C');
            }else{
                $pdf->Cell(20,10,iconv("utf-8","big5","學號"),1,0,'C');
            }
            $pdf->Cell(20,10,iconv("utf-8","big5","姓名"),1,0,'C');
            $x=$pdf->GetX();
            $y=$pdf->GetY();
            $pdf->Cell(70,5,iconv("utf-8","big5","月             日"),1,0,'C');
            $pdf->Cell(70,5,iconv("utf-8","big5","月             日"),1,1,'C');
            $pdf->SetXY($x,$y+5);
            $pdf->Cell(35,5,iconv("utf-8","big5","上午"),1,0,"C");
            $pdf->Cell(35,5,iconv("utf-8","big5","下午"),1,0,"C");
            $pdf->Cell(35,5,iconv("utf-8","big5","上午"),1,0,"C");
            $pdf->Cell(35,5,iconv("utf-8","big5","下午"),1,1,"C");
            $page_num=25;//一頁顯示的資料筆數
            $i=1;
            $page=1;//頁碼
            $total=count($member);//總筆數
            $page_total=ceil($total/$page_num);//總頁數
            if(!$page_total) $page_total=1;
            
            foreach ($member as $stno => $data) {
                if($i>$page_num){
                    $pdf->Cell(180,15,iconv("utf-8","big5","第".$page."/".$page_total."頁"),0,1,"C");
                    $pdf->AddPage();
                    //表頭
                    $pdf->SetFont('uni', 'B', 12 );
                    $pdf->Cell(180,10,iconv("utf-8","big5",$title),0,1,'C');
                    if($group>0){
                        $pdf->Cell(10,8,iconv("utf-8","big5","組別"),1,0,'C');
                        $pdf->Cell(10,8,iconv("utf-8","big5","學號"),1,0,'C');
                    }else{
                        $pdf->Cell(20,8,iconv("utf-8","big5","學號"),1,0,'C');
                    }
                    $pdf->Cell(20,8,iconv("utf-8","big5","姓名"),1,0,'C');
                    $x=$pdf->GetX();
                    $y=$pdf->GetY();
                    $pdf->Cell(70,4,iconv("utf-8","big5","月       日"),1,0,'C');
                    $pdf->Cell(70,4,iconv("utf-8","big5","月       日"),1,1,'C');
                    $pdf->SetXY($x,$y+4);
                    $pdf->Cell(35,4,iconv("utf-8","big5","上午"),1,0,"C");
                    $pdf->Cell(35,4,iconv("utf-8","big5","下午"),1,0,"C");
                    $pdf->Cell(35,4,iconv("utf-8","big5","上午"),1,0,"C");
                    $pdf->Cell(35,4,iconv("utf-8","big5","下午"),1,1,"C");
                    //end 表頭
                    $i=1;
                    $page++;
                }
                if($group>0){
                    $pdf->Cell(10,9,iconv("utf-8","big5",trim($data['group_no'])),1,0,'C');
                    $pdf->Cell(10,9,iconv("utf-8","big5",trim($stno)),1,0,'C');
                }else{
                    $pdf->Cell(20,9,iconv("utf-8","big5",trim($stno)),1,0,'C');
                }
                $pdf->Cell(20,9,iconv("utf-8","big5//TRANSLIT",trim($data['name'])),1,0,'C');
                $pdf->Cell(35,9,"",1,0,"C");
                $pdf->Cell(35,9,"",1,0,"C");
                $pdf->Cell(35,9,"",1,0,"C");
                $pdf->Cell(35,9,"",1,1,"C");
                $i++;
            }
            //補空白
            while($i<=$page_num){
                if($group>0){
                    $pdf->Cell(10,9,"",1,0,'C');
                    $pdf->Cell(10,9,"",1,0,'C');
                }else{
                    $pdf->Cell(20,9,"",1,0,'C');
                }
                $pdf->Cell(20,9,"",1,0,'C');
                $pdf->Cell(35,9,"",1,0,"C");
                $pdf->Cell(35,9,"",1,0,"C");
                $pdf->Cell(35,9,"",1,0,"C");
                $pdf->Cell(35,9,"",1,1,"C");
                $i++;
            }
            $pdf->Cell(180,15,iconv("utf-8","big5","第".$page."/".$page_total."頁"),0,1,"C");
        }elseif($type==1){ //簽到表
            $title="臺北市政府公務人員訓練處 ".$attrs['year']."年度 ".$class['class_name']." 第".$attrs['term']."期 研習人員簽到表"; 

            $pdf->SetFont('uni', 'B', 12 );

            $pdf->Cell(180,10,iconv("utf-8","big5",$title),0,1,'C');
            $pdf->Cell(10,10,iconv("utf-8","big5","素食"),1,0,'C');
            if($group>0){
                $pdf->Cell(10,10,iconv("utf-8","big5","組別"),1,0,'C');
                $pdf->Cell(10,10,iconv("utf-8","big5","學號"),1,0,'C');
            }else{
                $pdf->Cell(20,10,iconv("utf-8","big5","學號"),1,0,'C');
            }
            $pdf->Cell(20,10,iconv("utf-8","big5","姓名"),1,0,'C');
            $x=$pdf->GetX();
            $y=$pdf->GetY();
            $pdf->Cell(70,5,iconv("utf-8","big5","月             日"),1,0,'C');
            $pdf->Cell(70,5,iconv("utf-8","big5","月             日"),1,1,'C');
            $pdf->SetXY($x,$y+5);
            $pdf->Cell(35,5,iconv("utf-8","big5","上午"),1,0,"C");
            $pdf->Cell(35,5,iconv("utf-8","big5","下午"),1,0,"C");
            $pdf->Cell(35,5,iconv("utf-8","big5","上午"),1,0,"C");
            $pdf->Cell(35,5,iconv("utf-8","big5","下午"),1,1,"C");
            $page_num=25;//一頁顯示的資料筆數
            $i=1;
            $page=1;//頁碼
            $total=count($member);//總筆數
            $page_total=ceil($total/$page_num);//總頁數
            if(!$page_total) $page_total=1;
            
            foreach ($member as $stno => $data) {
                if($i>$page_num){
                    $pdf->Cell(180,15,iconv("utf-8","big5","第".$page."/".$page_total."頁"),0,1,"C");
                    $pdf->AddPage();
                    //表頭
                    $pdf->SetFont('uni', 'B', 12 );
                    $pdf->Cell(180,10,iconv("utf-8","big5",$title),0,1,'C');
                    $pdf->Cell(10,8,iconv("utf-8","big5","素食"),1,0,'C');
                    if($group>0){
                        $pdf->Cell(10,8,iconv("utf-8","big5","組別"),1,0,'C');
                        $pdf->Cell(10,8,iconv("utf-8","big5","學號"),1,0,'C');
                    }else{
                        $pdf->Cell(20,8,iconv("utf-8","big5","學號"),1,0,'C');
                    }
                    $pdf->Cell(20,8,iconv("utf-8","big5","姓名"),1,0,'C');
                    $x=$pdf->GetX();
                    $y=$pdf->GetY();
                    $pdf->Cell(70,4,iconv("utf-8","big5","月       日"),1,0,'C');
                    $pdf->Cell(70,4,iconv("utf-8","big5","月       日"),1,1,'C');
                    $pdf->SetXY($x,$y+4);
                    $pdf->Cell(35,4,iconv("utf-8","big5","上午"),1,0,"C");
                    $pdf->Cell(35,4,iconv("utf-8","big5","下午"),1,0,"C");
                    $pdf->Cell(35,4,iconv("utf-8","big5","上午"),1,0,"C");
                    $pdf->Cell(35,4,iconv("utf-8","big5","下午"),1,1,"C");
                    //end 表頭
                    $i=1;
                    $page++;
                }
                $pdf->Cell(10,9,iconv("utf-8","big5",'□'),1,0,'C');
                if($group>0){
                    $pdf->Cell(10,9,iconv("utf-8","big5",trim($data['group_no'])),1,0,'C');
                    $pdf->Cell(10,9,iconv("utf-8","big5",trim($stno)),1,0,'C');
                }else{
                    $pdf->Cell(20,9,iconv("utf-8","big5",trim($stno)),1,0,'C');
                }
                $pdf->Cell(20,9,iconv("utf-8","big5//TRANSLIT",trim($data['name'])),1,0,'C');
                $pdf->Cell(35,9,"",1,0,"C");
                $pdf->Cell(35,9,"",1,0,"C");
                $pdf->Cell(35,9,"",1,0,"C");
                $pdf->Cell(35,9,"",1,1,"C");
                $i++;
            }
            //補空白
            while($i<=$page_num){
                $pdf->Cell(10,9,"",1,0,'C');
                if($group>0){
                    $pdf->Cell(10,9,"",1,0,'C');
                    $pdf->Cell(10,9,"",1,0,'C');
                }else{
                    $pdf->Cell(20,9,"",1,0,'C');
                }
                $pdf->Cell(20,9,"",1,0,'C');
                $pdf->Cell(35,9,"",1,0,"C");
                $pdf->Cell(35,9,"",1,0,"C");
                $pdf->Cell(35,9,"",1,0,"C");
                $pdf->Cell(35,9,"",1,1,"C");
                $i++;
            }
            $pdf->Cell(180,15,iconv("utf-8","big5","第".$page."/".$page_total."頁"),0,1,"C");
        }elseif($type==2) {//查堂簽到表
            $title="臺北市政府公務人員訓練處 ".$attrs['year']."年度 ".$class['class_name']." 第".$attrs['term']."期"; 
            if(strlen($title)>34){
                $title1=mb_substr($title,0,33);
                $title2=mb_substr($title,33,strlen($title)-33);
                $cut=1;
            }
            $pdf->SetFont('uni', 'B', 10 );
            // $pdf->Cell(180,10,iconv("utf-8","big5",$title),0,2,'C');
           // $pdf->Ln();//换行
            // $pdf->Cell(180,10,iconv("utf-8","big5","查堂、抽查紀錄表"),0,1,'C');
            $pdf->SetFontSize(12);
            // $pdf->Cell(45,10,iconv("utf-8","big5","日期/時間"),1,0,'C');
            // $pdf->Cell(45,10,iconv("utf-8","big5","實到人數"),1,0,'C');
            // $pdf->Cell(45,10,iconv("utf-8","big5","查堂、抽查人員簽名"),1,0,'C');
            // $pdf->Cell(45,10,iconv("utf-8","big5","備註"),1,1,"C");
            // $page_num=23;//一頁顯示的資料筆數
            // $i = 1;
            // while($i<=$page_num){
            //     $pdf->Cell(45,10,"",1,0,'C');
            //     $pdf->Cell(45,10,"",1,0,'C');
            //     $pdf->Cell(45,10,"",1,0,'C');
            //     $pdf->Cell(45,10,"",1,1,'C');
            //     $i++;
            // }
            //page2
            // $pdf->AddPage();
            $pdf->SetMargins(15,5,15,10);
            $title .= "無刷卡資料簽到表"; 
            $pdf->SetFont('uni', 'B', 14 );
            if($cut==1){
                $pdf->Cell(180,10,iconv("utf-8","big5",$title1),0,2,'C');
                $pdf->Cell(180,10,iconv("utf-8","big5",$title2),0,2,'C');
            }else{
               $pdf->Cell(180,10,iconv("utf-8","big5",$title),0,2,'C'); 
            }
            
            $x=$pdf->GetX();
            $y=$pdf->GetY();
            $pdf->SetXY($x,$y+5);   
            $pdf->SetFontSize(16);
            $pdf->Cell(180,10,iconv("utf-8","big5","※刷卡後出現【查無資料】者請於下表簽到"),0,1,'C');
            $pdf->SetFontSize(12);
            $pdf->Cell(15,10,iconv("utf-8","big5","序號"),1,0,'C');
            $pdf->Cell(15,10,iconv("utf-8","big5","日期"),1,0,'C');
            $pdf->Cell(15,10,iconv("utf-8","big5","學號"),1,0,'C');
            $pdf->Cell(45,10,iconv("utf-8","big5","機關名稱"),1,0,'C');
            $pdf->Cell(30,10,iconv("utf-8","big5","身分證後四碼"),1,0,"C");
            $pdf->Cell(30,10,iconv("utf-8","big5","姓名(上課)"),1,0,'C');
            $pdf->Cell(30,10,iconv("utf-8","big5","姓名(下課)"),1,1,"C");
            $page_num=15;//一頁顯示的資料筆數
            $i = 1;
            while($i<=$page_num){
                $pdf->Cell(15,14,iconv("utf-8","big5",$i),1,0,'C');
                $pdf->Cell(15,14,"",1,0,'C');
                $pdf->Cell(15,14,"",1,0,'C');
                $pdf->Cell(45,14,"",1,0,'C');
                $pdf->Cell(30,14,"",1,0,'C');
                $pdf->Cell(30,14,"",1,0,'C');
                $pdf->Cell(30,14,"",1,1,'C');
                $i++;
            }
        }elseif($type==3) {//查堂素食登記與人工簽到表
            $title="臺北市政府公務人員訓練處 ".$attrs['year']."年度 ".$class['class_name']." 第".$attrs['term']."期"; 
            $pdf->SetFont('uni', 'B', 12 );
            $pdf->Cell(180,5,iconv("utf-8","big5",$title),0,2,'C');
            $pdf->Ln();//换行
            $pdf->Cell(180,5,iconv("utf-8","big5","查堂、抽查紀錄表"),0,1,'C');
            $pdf->SetFontSize(12);
            $pdf->Cell(45,10,iconv("utf-8","big5","日期/時間"),1,0,'C');
            $pdf->Cell(45,10,iconv("utf-8","big5","實到人數"),1,0,'C');
            $pdf->Cell(45,10,iconv("utf-8","big5","查堂、抽查人員簽名"),1,0,'C');
            $pdf->Cell(45,10,iconv("utf-8","big5","備註"),1,1,"C");
            $page_num=24;//一頁顯示的資料筆數
            $i = 1;
            while($i<=$page_num){
                $pdf->Cell(45,10,"",1,0,'C');
                $pdf->Cell(45,10,"",1,0,'C');
                $pdf->Cell(45,10,"",1,0,'C');
                $pdf->Cell(45,10,"",1,1,'C');
                $i++;
            }
            //page2-1
            $pdf->AddPage();
            $pdf->SetMargins(15,5,15,10);
            $x=$pdf->GetX();
            $y=$pdf->GetY();
            $pdf->SetXY($x,$y+5);   
            $title .= " 素食登記與人工簽到表";
            $pdf->Cell(180,10,iconv("utf-8","big5",$title),0,1,'C');
            $pdf->Cell(30,10,iconv("utf-8","big5","日期"),1,0,'C');
            $pdf->Cell(150,10,iconv("utf-8","big5","上課當日食用素食者請於下表填具學號，憑素券領餐"),1,1,'L');

            $page_num = 6;//一頁顯示的資料筆數
            $i = 1;
            while($i<=$page_num){
                $pdf->Cell(30,10,"",1,0,'C');
                $pdf->Cell(10,10,"",1,0,'C');
                $pdf->Cell(10,10,"",1,0,'C');
                $pdf->Cell(10,10,"",1,0,'C');
                $pdf->Cell(10,10,"",1,0,'C');
                $pdf->Cell(10,10,"",1,0,'C');
                $pdf->Cell(10,10,"",1,0,'C');
                $pdf->Cell(10,10,"",1,0,'C');
                $pdf->Cell(10,10,"",1,0,'C');
                $pdf->Cell(10,10,"",1,0,'C');
                $pdf->Cell(10,10,"",1,0,'C');
                $pdf->Cell(10,10,"",1,0,'C');
                $pdf->Cell(10,10,"",1,0,'C');
                $pdf->Cell(10,10,"",1,0,'C');
                $pdf->Cell(10,10,"",1,0,'C');
                $pdf->Cell(10,10,"",1,1,'C');
                $i++;
            }
            //page2-2
            $x=$pdf->GetX();
            $y=$pdf->GetY();
            $pdf->SetXY($x,$y+5);   
            $pdf->SetFontSize(16);
            $pdf->Cell(180,10,iconv("utf-8","big5","※刷卡後出現【查無資料】者請於下表簽到"),0,1,'L');
            $pdf->SetFontSize(12);
            $pdf->Cell(20,10,iconv("utf-8","big5","日期"),1,0,'C');
            $pdf->Cell(20,10,iconv("utf-8","big5","學號"),1,0,'C');
            $pdf->Cell(50,10,iconv("utf-8","big5","單位名稱"),1,0,'C');
            $pdf->Cell(30,10,iconv("utf-8","big5","身分證後四碼"),1,0,"C");
            $pdf->Cell(30,10,iconv("utf-8","big5","姓名(上課)"),1,0,'C');
            $pdf->Cell(30,10,iconv("utf-8","big5","姓名(下課)"),1,1,"C");
            $page_num=16;//一頁顯示的資料筆數
            $i = 1;
            while($i<=$page_num){
                $pdf->Cell(20,10,"",1,0,'C');
                $pdf->Cell(20,10,"",1,0,'C');
                $pdf->Cell(50,10,"",1,0,'C');
                $pdf->Cell(30,10,"",1,0,'C');
                $pdf->Cell(30,10,"",1,0,'C');
                $pdf->Cell(30,10,"",1,1,'C');
                $i++;
            }
        }else{
            $this->setAlert(3, '操作錯誤');
            redirect(base_url('management/Print_table/'));
        }   
        $pdf->Output();
        ob_end_flush();
    }

    public function rating(){
        $seq_no = $_GET['seq_no'];
        $class = $this->regist_contractors_model->get($seq_no);  //選定的class
        $attrs = array();
        $attrs['year'] = $class['year'];
        $attrs['class_no'] = $class['class_no'];
        $attrs['term'] = $class['term'];
        $attrs['where_in']['field'] = 'yn_sel';
        $attrs['where_in']['value'] = array('1','3','4','5','8');
        $class_list = $this->online_app_model->getList($attrs);
        $idarray=array();
        $st_no = array();
        foreach ($class_list as $key => $value) {//get idno
            $idarray[$key] = $value['id'];
            $st_no[$value['id']]['st_no'] = $value['st_no'];
            $st_no[$value['id']]['group_no'] = $value['group_no'];
        }
        //$select = 'idno,bureau_name,bureau_id,job_title,`name`,cellphone,office_tel,gender';
        //$memberData = $this->BS_user_model->getMemberData($idarray,$select);
        $select_test='idno,bureau_name,bureau_id,job_title,`name`,cellphone,office_tel,gender,og.ou_gov,out_gov_name';
        $memberData = $this->BS_user_model->getMemberDataWithOutGov($idarray,$select_test);
        $member = array();
        foreach ($memberData as $key => $value) {
            $stno = $st_no[$value['idno']]['st_no'];
            $member[$stno]['group_no'] = $st_no[$value['idno']]['group_no'];  //分組
            $member[$stno]['phone'] = is_null($value['cellphone'])? $value['office_tel']: $value['cellphone'];
            //$member[$stno]['bureau_name'] = is_null($value['bureau_name'])? $this->online_app_model->getBureau($value['bureau_id']): $value['bureau_name'] ;  //單位
            if(is_null($value['bureau_name'])){
                if($value['bureau_id'] == 'D0004'){
                    $member[$stno]['bureau_name'] = $value['out_gov_name'];
                } else {
                    $member[$stno]['bureau_name']=$this->online_app_model->getBureau($value['bureau_id']);
                }
            }elseif($value['bureau_name']=='其他'){
                $member[$stno]['bureau_name']=$value['ou_gov'];
            }else{
                $member[$stno]['bureau_name']=$value['bureau_name'];
            }
            $member[$stno]['name'] = $value['name'];
            $member[$stno]['gender'] = $value['gender']=='F'? '女': '男';
            $member[$stno]['job_title'] =  $this->code_table_model->getJobTitle($value['job_title']);  //職稱
            $member[$stno]['stno']=$stno;
        }
        ksort($member);
        $num = 0;
        $times = 1;
        foreach ($member as $k=> $v) {
            if($num==0) {
                $num = $this->quantityGroup($v["group_no"], $member,$times);


                $member[$k][0] = $num;
            }
            else {
                $member[$k][0] = 0;
            }
            $num--;
            $times++;
        }

        $member=$this->sortArrayByGroup($member);
       
       
        //head
        header("Pragma:public");
        header("Content-type:application/vnd.ms-excel");
        $filename = $class['year']."-".$class['class_no']."-".$class['term'];
        header("Content-Disposition:filename=". str_replace(array("\n", "\r"), '', urlencode($filename)).".xls"); ?>
        <table width="850">
        <tr><td colspan="8" style="text-align:center;"><font style="font-size:16px"><b>臺北市政府公務人員訓練處 學員學習評量表</b></font></td></tr>
        <tr><td colspan="8" style="text-align:center;"><font style="font-size:16px"><b><?=htmlspecialchars($class['year'], ENT_HTML5|ENT_QUOTES);?>年度 <?=htmlspecialchars($class['class_name'], ENT_HTML5|ENT_QUOTES);?> 第<?=htmlspecialchars($class['term'], ENT_HTML5|ENT_QUOTES);?>期(____月____日)________________課程</b></font></td></tr>
        <tr><td colspan="8"><font style="font-size:16px"><b>為達多元教學目的及發揮訓練效能，所授課程採下列教學評量方法(請勾選)</b></font></td></tr>
        <tr><td colspan="8"><font style="font-size:16px"><b>□測驗 □書面報告  □成果發表  □實作演練  □心得分享  □案例研討 □意見交流 □其他:</b></font></td></tr>
        </table>
        <table border="1">
          <tr>
            <th rowspan="2" width="40" style="text-align:center;">組別</th>
            <th rowspan="2" width="40" style="text-align:center;">學號</th>
            <th rowspan="2" width="200" style="text-align:center;">服務單位</th>
            <th rowspan="2" width="150" style="text-align:center;">職稱</th>
            <th rowspan="2" width="100" style="text-align:center;">姓名</th>
            <th colspan="3" width="250" style="text-align:center;">學習評量結果</th>
          </tr>
          <tr>
            <td style="text-align:center;"><b>通過</b></td>
            <td colspan="2" style="text-align:center;"><b>未通過註記<br>(請備註原因)</b></td>
          </tr>
            <?php
                $i=0;
                foreach ($member as $stno=> $v) {
                    echo "<tr width='50'>";
                  //  if($i==0) {
                    //if($v[0]>0) {
                        // echo "<td style='text-align:center;' rowspan='".($v[0])." '>".$v["group_no"]."</td>";
                    //    echo "<td style='text-align:center;' rowspan=".count($member)."></td>";
                    //}
                    if(isset($v[1])){
                        echo "<td style='text-align:center;' rowspan='".($v[1])." '>".$v["group_no"]."</td>";
                    }
                    //echo "<td style='text-align:center;'>".$stno."</td>";
                    echo "<td style='text-align:center;'>".$v["stno"]."</td>";
                    echo "<td>".$v["bureau_name"]."</td>";
                    echo "<td>".$v["job_title"]."</td>";
                    echo "<td>".$v["name"]."</td>";
                    if($i==0) {
                        echo "<td rowspan=".count($member).">□<br>全班<br>通過</td>
                                <td rowspan=".count($member).">□<br>部分<br>未通過<br>(勾選<br>學號)</td>";
                    }
                    echo "<td width='180'>".$v["stno"]."□　　　　　</td>";
                    echo "</tr>";
                    $i++;
                }
            ?>
            <tr>
                <td colspan="8"><font style="font-size:16px">講座回饋意見：</font></td>
            </tr>
        </table>
        <font style="font-size:16px">授課教師：　　　　　　　　(請簽名) </font>
<?php       
    }
    public function roomSeat($seq_no=null){
        $classroom = $_GET['classroom'];
        $type = $_GET['type'] ;
        $class = $this->regist_contractors_model->get($seq_no);  //選定的class
        if(!isset($class) || !isset($type) ){
            $this->setAlert(3, '操作錯誤!');
            redirect(base_url('management/Print_table/'));
        }
        
//        $attrs = $this->classroom_model->get($class['room_code']);//教室資料
        
        //座位表設定
        //------------------------------------------------------------------------------------
        //最大位置限
        $attrs = array();
        $attrs['room_id'] = $classroom;
        $seat =  $this->classroom_model->getRoomseat($attrs,'MAX(X) as arrX,MAX(Y) as arrY');//取出XY軸
        $attrs['is_set'] = 'Y';
        $maxSeat =  $this->classroom_model->getRoomseat($attrs,'COUNT(*)');//最大位置限制
        $attrs['is_set'] = '';
        $attrs['special'] = 'SET_NO IS NOT NULL';
        $cntSetNo = $this->classroom_model->getRoomseat($attrs,'COUNT(*)');//是否顯示座號
        if ($cntSetNo > 0){
            $openSeat = "Y";
        }
        else{
            $openSeat = "N";
        }
        $set1 = "50";//設定寬度
        $set2 = "30";//設定高度
        $direction = "B"; //設定顯示方向(A:學生方向,B:講師方向, 預設為B)
        if ($type==6){
          $direction = "A";
        }elseif ($type==5){
          $direction = "B";
        }else{
            $this->setAlert(3, '操作錯誤');
            redirect(base_url('management/Print_table/'));
        }
        $attrs = array('set_no'=>'1','room_id'=>$classroom);
        $rowSeat = $this->classroom_model->getRoomseat($attrs);//載入座位資料
        
        if ($rowSeat['y'] == 1) {// 座位序號由右到左增加
            $seatOrder ="isnull(sort),X,Y";
          //  $seatOrder = "cast(sort as unsigned int),X,Y";
        } else {// 座位序號由右到左減少
            $seatOrder = "cast(set_no as unsigned int)";
        }
        $attrs = array('room_id'    =>$classroom,
                        'seatOrder' =>$seatOrder,
                        'year'      =>$class['year'],
                        'class_no'  =>$class['class_no'],
                        'term'      =>$class['term']);
        $fields =  $this->classroom_model->loadingseat($attrs);//載入學員座位資料
        $seatArry = array();
        foreach ($fields as $key => $value) {
            $seatArry[$value['x']][$value['y']][1] = $value['set_no'];
            $seatArry[$value['x']][$value['y']][2] = $value['is_set'];
            $seatArry[$value['x']][$value['y']][3] = $value['ST_NO'];
            $seatArry[$value['x']][$value['y']][4] = $value['name'];
        }
        ?>
        <html>
        <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title>座位表</title>
        </head>
        <style type="text/css">
        body {font:0.8em Verdana, Arial, Helvetica, sans-serif}
        </style>
        <body>

        <center>
        <table>
          <tr>
            <td align="center">
              <font size="4">
              <?php
                echo htmlspecialchars($class['class_name'], ENT_HTML5|ENT_QUOTES).'第'.htmlspecialchars($class['term'], ENT_HTML5|ENT_QUOTES).'期  '.htmlspecialchars($attrs['room_id'], ENT_HTML5|ENT_QUOTES).'教室  座位表';
                //echo $data['ROOM_ID']. "-" . $data['NAME'] . "-" . $classData['CLASS_NAME'];
              ?>
              </font>
            </td>
          </tr>
        </table>
        </center>
        <br>

        <?php
        if ($direction=="A"){
          echo '<table cellspacing="0" cellpadding="0" style="FONT-SIZE:14px;">';
          echo '<tr><td>';

          echo '<center>';
          echo '<table cellspacing="0" cellpadding="0" style="FONT-SIZE:12px;">';
          echo '<td>';
          //echo '<td nowrap align="center" width="250" height="35" style="border:solid #000 1pt;">&nbsp;</td>';
          echo '<td nowrap align="center" width="250" height="35" style="border:solid #000 1pt;">講　　　　　　台</td>';
          echo '</table>';
          echo '</center>';
          echo '<br>';

          echo '<table cellspacing="0" cellpadding="0" style="FONT-SIZE:16px;">';
          for ($x=1;$x<=$seat['arrX'];$x++){

            //座號
            if ($openSeat=="Y"){
              echo '<tr>';
              for ($y=1;$y<=$seat['arrY'];$y++){
                $show = "&nbsp;";
                if ($seatArry[$x][$y][1]!=""){
                  $show = htmlspecialchars($seatArry[$x][$y][1], ENT_HTML5|ENT_QUOTES);
                }
                if ($seatArry[$x][$y][2]=="N"){
                  echo '<td nowrap align="center" width="' . htmlspecialchars($set1, ENT_HTML5|ENT_QUOTES) . '" height="1" style="border:solid #FFFFFF 1pt;">';
                  echo $show;
                  echo '</td>';
                }elseif($y==1){
                    echo '<td nowrap bgColor="yellow" align="center" width="' . htmlspecialchars($set1, ENT_HTML5|ENT_QUOTES) . '" height="1" style="border:solid #000 1pt;">';
                    echo $show;
                    echo '</td>';
                }else{
                  if ($seatArry[$x][$y-1][2]=="Y"){
                    echo '<td nowrap bgColor="yellow" align="center" width="' . htmlspecialchars($set1, ENT_HTML5|ENT_QUOTES) . '" height="1" style="border:solid #000 1pt;border-left:none;">';
                  }
                  else{
                    echo '<td nowrap bgColor="yellow" align="center" width="' . htmlspecialchars($set1, ENT_HTML5|ENT_QUOTES) . '" height="1" style="border:solid #000 1pt;">';
                  }
                  echo $show;
                  echo '</td>';
                }
              }
              echo '</tr>';
            }

            //學號
            echo '<tr>';
            for ($y=1;$y<=$seat['arrY'];$y++){
              $show = "&nbsp;";
              if ($seatArry[$x][$y][3]!=""){
                $show = htmlspecialchars($seatArry[$x][$y][3], ENT_HTML5|ENT_QUOTES);
              }
              if ($seatArry[$x][$y][2]=="N"){
                if ($openSeat=="Y"){
                  echo '<td nowrap align="center" width="' . htmlspecialchars($set1, ENT_HTML5|ENT_QUOTES) . '" height="22" style="border:solid #FFFFFF 1pt;border-top:none;">';
                }
                else{
                  echo '<td nowrap align="center" width="' . htmlspecialchars($set1, ENT_HTML5|ENT_QUOTES) . '" height="22" style="border:solid #FFFFFF 1pt;">';
                }
                echo $show;
                echo '</td>';
              }elseif($y==1){
                if ($openSeat=="Y"){
                    echo '<td nowrap align="center" width="' . htmlspecialchars($set1, ENT_HTML5|ENT_QUOTES) . '" height="22" style="border:solid #000 1pt;border-top:none;">';
                }else{
                    echo '<td nowrap align="center" width="' . htmlspecialchars($set1, ENT_HTML5|ENT_QUOTES) . '" height="22" style="border:solid #000 1pt;">';
                }
                echo $show;
                echo '</td>';  
              }else{
                if ($seatArry[$x][$y-1][2]=="Y"){
                  if ($openSeat=="Y"){
                    echo '<td nowrap align="center" width="' . htmlspecialchars($set1, ENT_HTML5|ENT_QUOTES) . '" height="22" style="border:solid #000 1pt;border-left:none;border-top:none;">';
                  }
                  else{
                    echo '<td nowrap align="center" width="' . htmlspecialchars($set1, ENT_HTML5|ENT_QUOTES) . '" height="22" style="border:solid #000 1pt;border-left:none;">';
                  }
                }
                else{
                  if ($openSeat=="Y"){
                    echo '<td nowrap align="center" width="' . htmlspecialchars($set1, ENT_HTML5|ENT_QUOTES) . '" height="22" style="border:solid #000 1pt;border-top:none;">';
                  }
                  else{
                    echo '<td nowrap align="center" width="' . htmlspecialchars($set1, ENT_HTML5|ENT_QUOTES) . '" height="22" style="border:solid #000 1pt;">';
                  }
                }
                echo $show;
                echo '</td>';
              }
            }
            echo '</tr>';

            //姓名
            echo '<tr>';
            for ($y=1;$y<=$seat['arrY'];$y++){
              $show = "&nbsp;";
              if ($seatArry[$x][$y][4]!=""){
                $show = htmlspecialchars($seatArry[$x][$y][4], ENT_HTML5|ENT_QUOTES);
              }
              if ($seatArry[$x][$y][2]=="N"){
                echo '<td nowrap align="center" width="' . htmlspecialchars($set1, ENT_HTML5|ENT_QUOTES) . '" height="' . htmlspecialchars($set2, ENT_HTML5|ENT_QUOTES) . '" style="border:solid #FFFFFF 1pt;border-top:none;">';
                echo $show;
                echo '</td>';
              }elseif($y==1){
                echo '<td nowrap align="center" width="' . htmlspecialchars($set1, ENT_HTML5|ENT_QUOTES) . '" height="' . htmlspecialchars($set2, ENT_HTML5|ENT_QUOTES) . '" style="border:solid #000 1pt;border-top:none;">';
                echo $show;
                echo '</td>';
              }else{
                if ($seatArry[$x][$y-1][2]=="Y"){
                  echo '<td nowrap align="center" width="' . htmlspecialchars($set1, ENT_HTML5|ENT_QUOTES) . '" height="' . htmlspecialchars($set2, ENT_HTML5|ENT_QUOTES) . '" style="border:solid #000 1pt;border-top:none;border-left:none;">';
                }
                else{
                  echo '<td nowrap align="center" width="' . htmlspecialchars($set1, ENT_HTML5|ENT_QUOTES) . '" height="' . htmlspecialchars($set2, ENT_HTML5|ENT_QUOTES) . '" style="border:solid #000 1pt;border-top:none;">';
                }
                echo $show;
                echo '</td>';
              }
            }
            echo '</tr>';

            //座位間格
            echo '<tr>';
            for ($y=1;$y<=$seat['arrY'];$y++){
              $show = "&nbsp;";
              echo '<td nowrap align="center" width="' . htmlspecialchars($set1, ENT_HTML5|ENT_QUOTES) . '" height="2" style="border:none;">';
              echo $show;
              echo '</td>';
            }
            echo '</tr>';
          }
          echo '</table>';

          echo '</td></tr>';
          echo '</table>';
        }

        if ($direction=="B"){
          echo '<table cellspacing="0" cellpadding="0">';
          echo '<tr><td>';

          echo '<table cellspacing="0" cellpadding="0" style="FONT-SIZE:12px;">';
          for ($x=$seat['arrX'];$x>=1;$x--){

            //座號
            if ($openSeat=="Y"){
              echo '<tr>';
              for ($y=$seat['arrY'];$y>=1;$y--){
                $show = "&nbsp;";
                if ($seatArry[$x][$y][1]!=""){
                  $show = htmlspecialchars($seatArry[$x][$y][1], ENT_HTML5|ENT_QUOTES);
                }

                if ($seatArry[$x][$y][2]=="N"){
                  echo '<td nowrap align="center" width="' . htmlspecialchars($set1, ENT_HTML5|ENT_QUOTES) . '" height="1" style="border:solid #FFFFFF 1pt;">';
                  echo $show;
                  echo '</td>';
                }
                else{
                  if (isset($seatArry[$x][$y+1][2]) && $seatArry[$x][$y+1][2]=="Y"){
                    echo '<td nowrap bgColor="yellow" align="center" width="' . htmlspecialchars($set1, ENT_HTML5|ENT_QUOTES) . '" height="1" style="border:solid #000 1pt;border-left:none;">';
                  }
                  else{
                    echo '<td nowrap bgColor="yellow" align="center" width="' . htmlspecialchars($set1, ENT_HTML5|ENT_QUOTES) . '" height="1" style="border:solid #000 1pt;">';
                  }

                  echo $show;

                  echo '</td>';

                }
              }
              echo '</tr>';
            }

            //學號
            echo '<tr>';
            for ($y=$seat['arrY'];$y>=1;$y--){
              $show = "&nbsp;";
              if ($seatArry[$x][$y][3]!=""){
                $show = htmlspecialchars($seatArry[$x][$y][3], ENT_HTML5|ENT_QUOTES);
              }
              if ($seatArry[$x][$y][2]=="N"){
                if ($openSeat=="Y"){
                  echo '<td nowrap align="center" width="' . htmlspecialchars($set1, ENT_HTML5|ENT_QUOTES) . '" height="22" style="border:solid #FFFFFF 1pt;border-top:none;">';
                }
                else{
                  echo '<td nowrap align="center" width="' . htmlspecialchars($set1, ENT_HTML5|ENT_QUOTES) . '" height="22" style="border:solid #FFFFFF 1pt;">';
                }
                echo $show;
                echo '</td>';
              }
              else{
                if (isset($seatArry[$x][$y+1][2]) && $seatArry[$x][$y+1][2]=="Y"){
                  if ($openSeat=="Y"){
                    echo '<td nowrap align="center" width="' . htmlspecialchars($set1, ENT_HTML5|ENT_QUOTES) . '" height="22" style="border:solid #000 1pt;border-left:none;border-top:none;">';
                  }
                  else{
                    echo '<td nowrap align="center" width="' . htmlspecialchars($set1, ENT_HTML5|ENT_QUOTES) . '" height="22" style="border:solid #000 1pt;border-left:none;">';
                  }
                }
                else{
                  if ($openSeat=="Y"){
                    echo '<td nowrap align="center" width="' . htmlspecialchars($set1, ENT_HTML5|ENT_QUOTES) . '" height="22" style="border:solid #000 1pt;border-top:none;">';
                  }
                  else{
                    echo '<td nowrap align="center" width="' . htmlspecialchars($set1, ENT_HTML5|ENT_QUOTES) . '" height="22" style="border:solid #000 1pt;">';
                  }
                }

                echo $show;

                echo '</td>';
              }
            }
            echo '</tr>';

            //姓名
            echo '<tr>';
            for ($y=$seat['arrY'];$y>=1;$y--){
              $show = "&nbsp;";
              if ($seatArry[$x][$y][4]!=""){
                $show = htmlspecialchars($seatArry[$x][$y][4], ENT_HTML5|ENT_QUOTES);
              }
              if ($seatArry[$x][$y][2]=="N"){
                echo '<td nowrap align="center" width="' . htmlspecialchars($set1, ENT_HTML5|ENT_QUOTES) . '" height="' . htmlspecialchars($set2, ENT_HTML5|ENT_QUOTES) . '" style="border:solid #FFFFFF 1pt;border-top:none;FONT-SIZE:15px;">';
                echo $show;
                echo '</td>';
              }
              else{
                if (isset($seatArry[$x][$y+1][2]) && $seatArry[$x][$y+1][2]=="Y"){
                  echo '<td nowrap align="center" width="' . htmlspecialchars($set1, ENT_HTML5|ENT_QUOTES) . '" height="' . htmlspecialchars($set2, ENT_HTML5|ENT_QUOTES) . '" style="border:solid #000 1pt;border-top:none;border-left:none;FONT-SIZE:15px;">';
                }
                else{
                  echo '<td nowrap align="center" width="' . htmlspecialchars($set1, ENT_HTML5|ENT_QUOTES) . '" height="' . htmlspecialchars($set2, ENT_HTML5|ENT_QUOTES) . '" style="border:solid #000 1pt;border-top:none;FONT-SIZE:15px;">';
                }
                echo $show;
                echo '</td>';
              }
            }
            echo '</tr>';

            //座位間格
            echo '<tr>';
            for ($y=$seat['arrY'];$y>=1;$y--){
              $show = "&nbsp;";
              echo '<td nowrap align="center" width="' . htmlspecialchars($set1, ENT_HTML5|ENT_QUOTES) . '" height="2" style="border:none;">';
              echo $show;
              echo '</td>';
            }
            echo '</tr>';
          }
          echo '</table>';

          echo '<br>';
          echo '<center>';
          echo '<table cellspacing="0" cellpadding="0" style="FONT-SIZE:15px;"><tr>';
          echo '<td nowrap align="center" width="250" height="35" style="border:solid #000 1pt;">講　　　　　　台</td>';
          echo '</tr>';
          echo '</table>';
          echo '</center>';

          echo '</td></tr>';
          echo '</table>';
        }
        ?>

        </body>
        </html>


    <?php
    }
    //傳入群組編號, 整個陣列
    private function quantityGroup($gNo, $ary,$times) {
        $return_val = 0;
        for($i=1;$i<$times;$i++){
            array_shift($ary);
        }
        foreach ($ary as $k => $v) {
            if($v["group_no"]==$gNo) {
                $return_val++;
            }else{
                break;
            }
        }

        return $return_val;
    }
    private function sortArrayByGroup($attrs=array())
    {
        $member=$attrs;
        $member_temp1=[];
        $i=0;
        foreach ($member as $sort_group) {
            $member_temp1[$sort_group['group_no']][$i]['group_no']=$sort_group['group_no'];
            $member_temp1[$sort_group['group_no']][$i]['phone']=$sort_group['phone'];
            $member_temp1[$sort_group['group_no']][$i]['bureau_name']=$sort_group['bureau_name'];
            $member_temp1[$sort_group['group_no']][$i]['name']=$sort_group['name'];
            $member_temp1[$sort_group['group_no']][$i]['gender']=$sort_group['gender'];
            $member_temp1[$sort_group['group_no']][$i]['job_title']=$sort_group['job_title'];
            $member_temp1[$sort_group['group_no']][$i]['stno']=$sort_group['stno'];
            $member_temp1[$sort_group['group_no']][$i][0]=$sort_group[0];
            $i++;
        }
        $member_temp2=[];
        $index=0;
        $group_num=1;
        $temp_group='';
        foreach($member_temp1 as $temp1){
            foreach ($temp1 as $temp2) {
                $member_temp2[$index]=$temp2;
                $index++;
            }
        }
        $test=[];
        $j=0;
        for($i=0;$i<count($member_temp2);$i++){
            if($member_temp2[$i]['group_no']==$temp_group){
                $group_num=$group_num+1;
                $member_temp2[$j][1]=$group_num;
            }else{
                $j=$i;
                $group_num=1;
                $temp_group=$member_temp2[$i]['group_no'];
            }
        }
        return $member_temp2;

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

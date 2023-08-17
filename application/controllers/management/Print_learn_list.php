<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Print_learn_list extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        if ($this->flags->is_login === FALSE) {
            redirect(base_url('welcome'));
        }
        $this->data['username'] = $this->input->cookie('username');
        $this->load->model(['require_model', 'online_app_model']);
        $this->load->model('customer_service/BS_user_model');
        $this->load->model('create_class/progress_model');
        $this->load->model('data/template_list_model');
        $this->load->model('management/leave_model');
        $this->data['choices']['year'] = $this->_get_year_list();
        $date_now = new DateTime('now');
        $year_now = $date_now->format('Y');
        $this_yesr = $year_now - 1911;

        if (!isset($this->data['filter']['year'])) {
            $this->data['filter']['year'] = $this_yesr;
        }

        if (!isset($this->data['filter']['class_name'])) {
            $this->data['filter']['class_name'] = '';
        }

        if (!isset($this->data['filter']['class_no'])) {
            $this->data['filter']['class_no'] = '';
        }
    }

    public function index()
    {   
        $condition = $this->getFilterData(['term', 'class_no']);
        $idno=$this->flags->user['idno'];
        $group_id=$this->flags->user['group_id'];
        $key1 = in_array('1', $group_id);
        $key2 = in_array('9', $group_id);

        if(!$key1 && !$key2){
            $condition['idno']=$idno;
        }
        $condition['year'] = $this->data['filter']['year'];
        $condition['class_name'] = $this->data['filter']['class_name'];
        //$condition['idno'] = $this->BS_user_model->getWorkerID($this->data['username'] );//承辦人
        $this->data['requires'] = $this->require_model->getList($condition);

        $this->data['link_refresh'] = base_url("management/print_learn_list/");
        $this->layout->view('management/print_learn_list/list',$this->data);
    }

    public function print(){
        $condition = $this->getFilterData(['year', 'class_no', 'term']);
        $this->data['class_info'] = $this->require_model->find($condition);
        $this->data['learns'] = $this->online_app_model->getLearnList($condition);
        
        //將未報到的人的資料整理成一筆
        $index=[];//紀錄學員狀態是未報到的index
        for($i=0;$i<count($this->data['learns']);$i++){
            if(!empty($this->data['learns'][$i]->out_gov_name)){
                $this->data['learns'][$i]->description = $this->data['learns'][$i]->out_gov_name;
            }
            if($this->data['learns'][$i]->yn_sel=='5'){
                $index[$i]=$this->data['learns'][$i]->name;
            }
        }
        

        
            //var_dump(array_count_values($index));
            $index_name=array_count_values($index);//記錄這個人有幾筆重複
            //var_dump($index_name);
        //}

        //大於一筆才做unset重複的資料
        $index2=array_unique($index);
        for($j=0;$j<count($this->data['learns']);$j++){
            if(isset($index2[$j])&&$index_name[$index2[$j]]>=2){
                unset($this->data['learns'][$j]);
            }
        }
        $this->data['learns']=array_values($this->data['learns']);

        
        $pdf = $this->getFilterData('pdf');
        if ($pdf == null){
            $this->load->view('management/print_learn_list/print', $this->data);
        }else{
            $this->print_pdf();
        }
        // dd($this->data['learns']);
        
    }
    private function print_pdf(){
        $this->load->library('pdf/PDF_Chinesess');
        $pdf=new PDF_Chinesess();
        $pdf->AddBig5Font('simhei', '黑体');
        $pdf->AddPage();
        $pdf->SetMargins(7,5,10,10);
        $pdf->SetAutoPageBreak(false);
        $pdf->SetFont('simhei', '', 12);
        $pdf->Cell(180, 5, mb_convert_encoding('臺北市政府公務人員訓練處 研習紀錄表','big5','utf8'),0,1,'C');   
        $pdf->Cell(180, 7, mb_convert_encoding("{$this->data['class_info']->year} 年度 {$this->data['class_info']->class_name} 第 {$this->data['class_info']->term} 期",'big5','utf8'),0,1,'C');

        $pdf->SetFont('simhei', '', 10);
        // pdf 寬度 195
        $pdf->Cell(15,10,mb_convert_encoding('學號','big5','utf8'),1,0,'C');
        $pdf->Cell(60,10,mb_convert_encoding('局處名稱','big5','utf8'),1,0,'C');
        $pdf->Cell(20,10,mb_convert_encoding('姓名','big5','utf8'),1,0,'C');
        $pdf->Cell(20,10,mb_convert_encoding('缺席情形','big5','utf8'),1,0,'C');
        $pdf->Cell(20,10,mb_convert_encoding('請假日期','big5','utf8'),1,0,'C');
        $pdf->Cell(20,10,mb_convert_encoding('請假時間','big5','utf8'),1,0,'C');
        $pdf->Cell(10,10,mb_convert_encoding('時數','big5','utf8'),1,0,'C');
        $pdf->Cell(30,10,mb_convert_encoding('備註','big5','utf8'),1,1,'C');
        
        /*  測試用
            $learn = new stdClass();
            $learn->st_no = 123;
            $learn->description = 123;
            $learn->name = 123;
            $learn->va_code_text = 123;
            $learn->vacation_date = 123;
            $learn->time = 123;
            $learn->hours = 123;
            $learn->memo = 123;
            $learn->yn_sel = 123;
            $learn->va_sn = 123;
            $learn->v_count = 123;
            $learn->retirement = 123;

            $this->data['learns'] = [$learn, $learn];
        */

        $last_st_no=$last_description=$last="";

        foreach($this->data['learns'] as $learn){
            if ($last_st_no != $learn->st_no){
                $last_st_no = $learn->st_no;
            }else{  
                $learn->st_no = '';  
                $learn->description = '';
                $learn->name = '';
            }

            $pdf->Cell(15,10,mb_convert_encoding($learn->st_no,'big5','utf8'),1,0,'C');
            $pdf->Cell(60,10,mb_convert_encoding($learn->description,'big5','utf8'),1,0,'C');
            $pdf->Cell(20,10,mb_convert_encoding($learn->name,'big5','utf8'),1,0,'C');

            if($learn->yn_sel == 5){
                $learn->va_code_text = "未報到";
            }

            $pdf->Cell(20,10,mb_convert_encoding($learn->va_code_text,'big5','utf8'),1,0,'C');
            $pdf->Cell(20,10,mb_convert_encoding($learn->vacation_date,'big5','utf8'),1,0,'C');
            $pdf->Cell(20,10,mb_convert_encoding($learn->time,'big5','utf8'),1,0,'C');
            $pdf->Cell(10,10,mb_convert_encoding($learn->hours,'big5','utf8'),1,0,'C');
       
            $remark = "";
            if($learn->yn_sel == 4){
                $remark .= '退訓';
            }

            if (!empty($learn->memo) && $learn->va_sn == $learn->v_count) {
                $remark .= "({$learn->memo})";
                }

            if ($learn->retirement === "0"){
                $remark .= "(退休)";
            }

            $pdf->Cell(30,10,mb_convert_encoding($remark,'big5','utf8'),1,1,'C');

        }

        $pdf->AddPage(); 
        $pdf->Output(); 
        
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

    public function mail_select($who)
    {
        // 檢查以下參數若無則給予 null 避免error
        $this->getFilterData(['year', 'class_no', 'term', 'start_date', 'end_date', 'class_name', 'checkAllClass']);
        $params = $this->getFilterData(['year', 'class_no', 'term']);

        $config = [
            [
                'field' => 'year',
                'label' => 'year',
                'rules' => 'required',
            ], [
                'field' => 'class_no',
                'label' => 'class_no',
                'rules' => 'required',
            ], [
                'field' => 'term',
                'label' => 'term',
                'rules' => 'required',
            ],
        ];


        $this->form_validation->set_data($params);
        $this->form_validation->set_rules($config);

        if ($this->form_validation->run() != false) {
            $this->data['require'] = $this->progress_model->getRequire($params);
            if (!empty($this->data['require'])) {
                $this->data['link_confirm'] = base_url("management/print_learn_list/mail_to/" . $who . "?{$_SERVER['QUERY_STRING']}");

                switch ($who) {
                    case '3':
                        $this->data['list'] = $this->progress_model->getPersonnelByLeave($params['class_no'], $params['year'], $params['term']);
                        $this->data['list_header'] = ['姓名', '局處', '電話', 'E-mail'];
                        $this->data['email_who'] = "人事";
                        $this->data['_LOCATION']['name'] = "課程表及名冊mail給人事作業";
                        break;
                    default:
                        die("非法操作");
                        break;
                }

                $this->data['link_cancel'] = base_url("management/print_learn_list?{$_SERVER['QUERY_STRING']}");
                $this->data['who'] = $who;
                $this->data['condition'] = $params;
                // $this->data['_LOCATION']['name'] = "mail給".$this->data['email_who']."作業";
                $this->layout->view('management/print_learn_list/mail_select', $this->data);
            } else {
                $this->setAlert(3, "找不到該班期資訊");
                redirect(base_url("management/print_learn_list"));
            }
        } else {
            $this->setAlert(3, "非法操作 !");
            redirect(base_url("management/print_learn_list"));
        }

    } 

    public function mail_to($who)
    {
        $params = ['year', 'class_no', 'term'];
        $params = $this->getFilterData($params);
        $post = $this->input->post();

        if (!isset($post['email'])) {
            if (!empty($this->input->get("email"))) {
                $post['email'] = [$this->input->get("email")];
            }
        }


        if (!empty($post['email'])) {
            $require = $this->progress_model->getRequire($params);
            $class_name = $require->class_name;
            $class_total_name = $params['year'] . " 年度 " . $class_name . " 第 " . $params['term'];
            $mail_title = [
                '3' => "公訓處研習記錄-請列入差假管理參考",
            ];

            // 信件標題
            $mail_title = (empty($mail_title[$who])) ? $mail_title['0'] : $mail_title[$who];
            // 收件人名單
            $email_array = array();
            $username_array = array();
            for($i=0;$i<count($post['email']);$i++){
                $tmp_email = explode(',', $post['email'][$i]);
                
                if(isset($tmp_email[0]) && isset($tmp_email[1]) && !empty($tmp_email[0]) && !empty($tmp_email[1])){
                    $email_array[] = $tmp_email[0];
                    $username_array[] = $tmp_email[1];
                }
            }
            $email = (!empty($email_array)) ? join(",", $email_array) : "";
            $username = (!empty($username_array)) ? join(",", $username_array) : "";
            // 範本標題
            $this->data['templates']['mail_content_template'] = $this->template_list_model->getData(['conditions' => ['item_id' => '01', 'is_open' => 1], 'order_by' => 'tmp_seq'], 'object');

            

            $this->data['templates']['course_content_template'] = $this->template_list_model->getData(['conditions' => ['item_id' => '02', 'is_open' => 1], 'order_by' => 'tmp_seq'], 'object');

            $this->data['send_email'] = base_url('management/print_learn_list/send_email/' . $who . "?{$_SERVER['QUERY_STRING']}");
            $this->data['who'] = $who;
            $this->data['condition'] = $params;
            $this->data['data']['email'] = $email;
            $this->data['data']['username'] = $username;
            $this->data['data']['mail_title'] = $mail_title;
            $this->data['link_cancel'] = "history_go_back";

            if (isset($post['signatures'])) {
                $this->data['signatures'] = $post['signatures'];
            }
           


            $this->layout->view('management/print_learn_list/mail_to', $this->data);

        } else {
            $this->setAlert(3, "請選擇Email !");
            redirect(base_url("management/print_learn_list/mail_select/" . $who . "?{$_SERVER['QUERY_STRING']}"));
        }

    }

    public function send_email($who)
    {
        $this->load->helper("progress");
        $progress_helper = new progress_helper();

        $params = ['year', 'class_no', 'term'];
        $params = $this->getFilterData($params);
        
        $post = $this->input->post();
        $class_info = $this->progress_model->getRequire($params);
        $email_list = explode(",", $post['email']);
        $email_list = array_filter($email_list);
        $username_list = explode(",", $post['username']);
        $username_list = array_filter($username_list);
        $worker_email = '';
        if (!empty($class_info)) {
            $worker_email = $class_info->worker_email;
            $mail_data = [
                'mail_content' => $post['mail_content'],
                'class_info' => $class_info,
            ];
          
            switch ($who) {
                case '3':
                    $mail_data['leaves'] = $this->online_app_model->getLearnList($params);
                    for($i=0;$i<count($mail_data['leaves']);$i++){
                        $mail_data['leaves'][$i]->bureau_name = $this->progress_model->getBureauName($mail_data['leaves'][$i]->beaurau_id);
                        $mail_data['leaves'][$i]->online_leave = $this->progress_model->checkOnlineLeave($params,$mail_data['leaves'][$i]->id,$mail_data['leaves'][$i]->vacation_date);
                    }
                    $params2 = $params;
                    $params2['class_id'] = $params2['class_no'];
                    unset($params2['class_no']);
                    $mail_data['room_uses'] = $this->leave_model->getRoomUse($params2);
                    break;
                default:

                    break;
            }
            $replace_data = $this->progress_model->getReplaceData($params);

            // $mail_data['course_content'] = replaceEmailContent($mail_data['course_content'], $replace_data);
            $mail_data['mail_content'] = replaceEmailContent($mail_data['mail_content'], $replace_data);
            $pre_bureau_id = $this->BS_user_model->getBureauIdByUsername($username_list[0]);

            $email_content = arrangeEmailContent2($mail_data,$pre_bureau_id); // progress helper

        } else {
            $this->setAlert(2, "找不到該班期資訊");
            redirect(base_url("management/print_learn_list"));
        }

        if ($post['send'] == "true") {
            $config['allowed_types'] = '*';

            // 開始寄信
            $this->load->library('email');

            // 如果沒有設定 信件來自誰
            if (!isset($from)) {
                $from = "pstc_member@gov.taipei";
            }

            $send = true;
            $error_email = [];
            $only_one = false; //只cc第一次
            foreach ($email_list as $key => $email) {
                $email = trim($email);
               
                $bureau_id = $this->BS_user_model->getBureauIdByUsername($username_list[$key]);
                $email_content = arrangeEmailContent2($mail_data, $bureau_id); // progress helper
    
                // 清除紀錄
                $this->email->clear(true);
                // 設定標題及內容
                $this->email->from($from, '臺北市政府公務人員訓練處');
                
                $this->email->to($email);

                if(!empty($worker_email) && !$only_one){
                    $this->email->cc($worker_email);
                    $only_one = true;
                }

                $this->email->subject($post['title']);
                // if ($key === "worker") {
                //     $this->email->message($worker_content.$email_content);
                // }else{
                    $this->email->message($email_content);
                // }

                $send =  $this->email->send();
                if (!$send) $error_email[] = $email;
            }

            $email_content = arrangeEmailContent2($mail_data); // progress helper
            $query_string = "class_no=" . $params['class_no'] . "&year=" . $params['year'] . "&term=" . $params['term'];

            //dd($this->email->print_debugger());
            if (count($error_email) == 0){
                $this->setAlert(2, "寄送成功");
            }else{
                $error_message = "部份信箱寄送失敗，請修正後再重新補發!\\n".htmlspecialchars(join("\\n", $error_email), ENT_HTML5|ENT_QUOTES);
            }
            
            // 新增寄送信件紀錄
            $mail_info = ['title' => $post['title'], 'content' => $email_content];
            if ($who != 10) { // 報名資訊不用紀錄
                $progress_helper->insertMailLog($params, $mail_info, 11);
            }
            $filterQueryString = filterQueryString($_SERVER['QUERY_STRING']);
            $redirect_url = base_url("management/print_learn_list?{$filterQueryString}");
            // 後續處理
            switch ($who) {
                case '3': //人事
                    $this->require_model->updateLearnSend($params);

                    break;
                default:
                    # code...
                    break;
            }

            if (count($error_email) > 0){
                echo "<script>alert('{$error_message}')</script>";
                echo "<script>location.href='".$redirect_url."';</script>";
                die;
            }

            redirect($redirect_url);
            

        } else if ($post['send'] == "false") {
            // 預覽
            // $error_email = ["pstc_pppiii@mail.taipei.gov.tw","pstc_pif999@mail.taipei.gov.tw","fetbrook@gmail.com"];
            // $error_message = "部份信箱寄送失敗，請修正後再重新補發!\\n".join("\\n", $error_email);
            // echo "<script>alert('{$error_message}')</script>";
            echo xss_clean($email_content);
        }

    }
}

<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Set_course extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();

		if ($this->flags->is_login === FALSE) {
			redirect(base_url('welcome'));
		}

		$this->load->model('create_class/set_course_model');
        $this->load->model('create_class/volunteer_model');
        $this->load->model('planning/createclass_model');
        $this->load->model('management/handouts_status_model');
        $this->load->model('create_class/course_sch_model'); //20210712加入
        $this->load->model('create_class/progress_model');//20210712加入
	
        if (!isset($this->data['filter']['page'])) {
            $this->data['filter']['page'] = '1';
        }
        if (!isset($this->data['filter']['sort'])) {
            $this->data['filter']['sort'] = '';
        }
        if (!isset($this->data['filter']['query_year'])) {
            $this->data['filter']['query_year'] = date('Y')-1911;
        }
        if (!isset($this->data['filter']['query_class_no'])) {
            $this->data['filter']['query_class_no'] = '';
        }
        if (!isset($this->data['filter']['query_class_name'])) {
            $this->data['filter']['query_class_name'] = '';
        }
        if (!isset($this->data['filter']['checkAllClass'])) {
            $this->data['filter']['checkAllClass'] = '';
        }
	}

	public function index()
	{
		$this->data['page_name'] = 'list';
    
        $conditions = array();

        $check_admin = $this->set_course_model->chechAdmin($this->flags->user['username']);
        if(!$check_admin && $this->data['filter']['checkAllClass'] != 'on'){
            $conditions['worker'] = $this->flags->user['idno'];
        }

        if ($this->data['filter']['query_year'] !== '' ) {
            $conditions['year'] = $this->data['filter']['query_year'];
        }

        if ($this->data['filter']['query_class_no'] !== '' ) {
            $conditions['class_no'] = $this->data['filter']['query_class_no'];
        }

        $page = $this->data['filter']['page'];
        $rows = $this->data['filter']['rows'];

		$attrs = array(
            'conditions' => $conditions,
        );

        if ($this->data['filter']['query_class_name'] !== '' ) {
            $attrs['query_class_name'] = $this->data['filter']['query_class_name'];
        }

        $this->data['filter']['total'] = $total = $this->set_course_model->getListCount($attrs);
        $this->data['filter']['offset'] = $offset = ($page -1) * $rows;

        $attrs = array(
            'conditions' => $conditions,
            'rows' => $rows,
            'offset' => $offset,
        );
        
        if ($this->data['filter']['sort'] !== '' ) {
            $attrs['sort'] = $this->data['filter']['sort'];
        }

        if ($this->data['filter']['query_class_name'] !== '' ) {
            $attrs['query_class_name'] = $this->data['filter']['query_class_name'];
        }

		$this->data['list'] = $this->set_course_model->getList($attrs);
		foreach ($this->data['list'] as & $row) {
            $row['link_edit'] = base_url("create_class/set_course/edit/{$row['seq_no']}/?{$_SERVER['QUERY_STRING']}");
        }
		$this->load->library('pagination');
        $config['base_url'] = base_url("create_class/set_course?". $this->getQueryString(array(), array('page')));
        $config['total_rows'] = $total;
        $config['per_page'] = $rows;
        $this->pagination->initialize($config);

		$this->data['link_refresh'] = base_url("create_class/set_course");

		$this->layout->view('create_class/set_course/list', $this->data);
	}


    public function list_sign()  //20210621 線上簽核列表
	{
		$this->data['page_name'] = 'list_sign';
    
        $conditions = array();

        $check_admin = $this->set_course_model->chechAdmin($this->flags->user['username']);
        if(!$check_admin && $this->data['filter']['checkAllClass'] != 'on'){
            $conditions['worker'] = $this->flags->user['idno'];
        }

        /* if ($this->data['filter']['query_year'] !== '' ) {
            $conditions['require.year'] = $this->data['filter']['query_year'];
        } */

        if ($this->data['filter']['query_class_no'] !== '' ) {
            $conditions['class_no'] = $this->data['filter']['query_class_no'];
        }
        

            if ($_SESSION['sign_sl'] == 'boss' ){
                if ($this->data['filter']['boss'] !== '' ) {
                    $conditions['course_sch_app.boss'] = $_SESSION['user_idno'];
                    $conditions['course_sch_app.status'] = 2;
                }
            }elseif ($_SESSION['sign_sl'] == 'leader' ){
                if ($this->data['filter']['leader'] !== '' ) {
                    $conditions['course_sch_app.leader'] = $_SESSION['user_idno'];
                    $conditions['course_sch_app.status'] = 3;
                }
            }
                
        

        $page = $this->data['filter']['page'];
        $rows = $this->data['filter']['rows'];

		$attrs = array(
            'conditions' => $conditions,
        );

        if ($this->data['filter']['query_class_name'] !== '' ) {
            $attrs['query_class_name'] = $this->data['filter']['query_class_name'];
        }

        $this->data['filter']['total'] = $total = $this->set_course_model->getListCount2($attrs);
        $this->data['filter']['offset'] = $offset = ($page -1) * $rows;

        $attrs = array(
            'conditions' => $conditions,
            'rows' => $rows,
            'offset' => $offset,
        );
        
        if ($this->data['filter']['sort'] !== '' ) {
            $attrs['sort'] = $this->data['filter']['sort'];
        }

        if ($this->data['filter']['query_class_name'] !== '' ) {
            $attrs['query_class_name'] = $this->data['filter']['query_class_name'];
        }

		$this->data['list'] = $this->set_course_model->getsignList($attrs);
		foreach ($this->data['list'] as & $row) {
            $row['link_edit'] = base_url("create_class/set_course/edit/{$row['seq_no']}/?{$_SERVER['QUERY_STRING']}");
        }
        
        //如果沒資料了跳回9B
        if ($total==0){
            $_SESSION['sign_sl'] = "";
            redirect("create_class/set_course/",'refresh');
        }


		$this->load->library('pagination');
        $config['base_url'] = base_url("create_class/set_course/list_sign?". $this->getQueryString(array(), array('page')));
        $config['total_rows'] = $total;
        $config['per_page'] = $rows;
        $this->pagination->initialize($config);

		$this->data['link_refresh'] = base_url("create_class/set_course/list_sign");

		$this->layout->view('create_class/set_course/list_sign', $this->data);
	}




    public function course_sch_app()  //20210621 線上簽核使用
    {
        $this->load->helper("progress");   
        $progress_helper = new progress_helper();
        
        $this->data['page_name'] = 'course_sch_app';
        $seq_no = $_GET['seq_nos'];
        $ppost = $_GET['post'];
        $this->data['form'] = $this->set_course_model->getFormDefault($this->set_course_model->get($seq_no));
        $this->data['form']['signuser'] = $this->course_sch_model-> getsignuser();
        $post = $this->input->post();
        $class_txt = $this->course_sch_model-> get_class_name($seq_no);

        if(isset($this->data['form']['worker']) && !empty($this->data['form']['worker'])){
            $this->data['form']['worker_name'] = $this->set_course_model->getWorkerName($this->data['form']['worker']);
        } else {
            $this->data['form']['worker_name'] = '';
        }
        $this->data['form1'] = $this->course_sch_model->get_course_sch_Count($seq_no);

        //送陳成功 之後判斷是新增還是更新
        $cou_sch_form =  $this->course_sch_model->get_course_sch_Count($seq_no);
        if (count($cou_sch_form) == 0){
            $atv = "new";
        }else{
            $atv = "update";
        }
        //生成mail樣版內容使用
        $params['year'] = $cou_sch_form[0]['year'];
        $params['class_no'] =$cou_sch_form[0]['class_no'];
        $params['term'] = $cou_sch_form[0]['term'];
        $replace_data = $this->progress_model->getReplaceData($params);
        
        //email設定開始
        $this->load->library('email');
        $this->email->from('pstc_member@gov.taipei', '臺北市政府公務人員訓練處');
        
        $worker_mail = $this->course_sch_model->get_usermail($this->data['form']['worker']); //取得簽核主管mail
        $worker_mail = $worker_mail[0]['office_email'];
        $boss_mail = $this->course_sch_model->get_usermail($this->data['form1'][0]['boss']); //取得簽核主管mail
        $boss_mail = $boss_mail[0]['office_email'];
        $boss_name = $this->course_sch_model->get_usermail($this->data['form1'][0]['boss']);
        $this->data['form1'][0]['boss_name'] = $boss_name[0]['name'];
        $leader_mail = $this->course_sch_model->get_usermail($this->data['form1'][0]['leader']); //取得簽核主管mail
        $leader_mail = $leader_mail[0]['office_email'];
        $leader_name = $this->course_sch_model->get_usermail($this->data['form1'][0]['leader']);
        $this->data['form1'][0]['leader_name']  = $leader_name[0]['name'];

        if ($this->data['form1'][0]['boss'] == ""){
            $this->data['form1'][0]['boss_name'] = "　　　";
        }
        if ($this->data['form1'][0]['to_leader'] == 0){
            $this->data['form1'][0]['leader_name'] = "　　　";
        }

        //echo $worker_mail, $boss_mail, $leader_mail;


        //var_dump($this->date);die();

        //--------------------用來新增第一筆送陳資料
        if (($atv == 'new')&&($ppost == 1)){
            $course_code = $seq_no;
            $year = $this->data['form']['year'];
            $class_no = $this->data['form']['class_no'];
            $term = $this->data['form']['term'];
            $pot1 = $post['pot1'];
            $pot2 = $post['pot2'];
            $pot3 = $post['pot3'];
            $fix1 = $post['fix1'];
            $fix2 = $post['fix2'];
            $fix3 = $post['fix3'];
            $bef1 = $post['bef1'];
            $bef2 = $post['bef2'];
            $bef3 = $post['bef3'];
            $aft1 = $post['aft1'];
            $aft2 = $post['aft2'];
            $aft3 = $post['aft3'];
            $rem1 = $post['rem1'];
            $rem2 = $post['rem2'];
            $rem3 = $post['rem3'];
            $opinion = $post['opinion'];
            $worker = $post['worker'];
            $boss = $post['boss'];
            $leader = $post['leader'];
            $status = 2;
            $to_leader = $post['to_leader'];
            $training_text = $post['training_text'];
            $boss_op = $post['boss_op'];
            $boss_centext = $post['boss_centext'];
            $leader_op = $post['leader_op'];
            $leader_centext = $post['leader_centext'];
            $cre_date = date('Y-m-d H:i:s');



            $this->course_sch_model->insertcoursesch($course_code,$year,$class_no,$term,$pot1,$pot2,$pot3,$fix1,$fix2,$fix3,$bef1,$bef2,$bef3,$aft1,$aft2,$aft3,$rem1,$rem2,$rem3,$opinion,$worker,$boss,$leader,$status,$to_leader,$cre_date,$training_text);
            
            $id = 49;//email樣版編號
            $mail_template = $this->course_sch_model->get_mail_template($id);
            $mts = replaceEmailContent($mail_template[0]['content'],$replace_data); //生成email樣版內容
            
            $mail_txt= "
            
            <table>
                <tr>
                    <td width=600>
                        <br>".$mts."
                        <br>
                    </td>
                </tr>
                <tr>
                    <td align=center >
                        <br>".$training_text."
                        <br>
                    </td>
                </tr>
            </table>";
            $boss_mail = $this->course_sch_model->get_usermail($post['boss']); //取得簽核主管mail
            $boss_mail = $boss_mail[0]['office_email'];
            //$this->email->to('roger@posboss.com.tw'); //測試
            $this->email->to($boss_mail); //主管mail
            //$this->email->cc('roger@posboss.com.tw'); //承辦人mail
            //$this->email->bcc('roger@posboss.com.tw, carlos.sixdotsit@gmail.com');

            
            $subject_txt ="公訓處課表-$class_txt->year 年度 $class_txt->class_name 第$class_txt->term 期-簽核通知";
            $this->email->subject($subject_txt);
            $this->email->message($mail_txt);
            $this->email->send();
            
            //echo '<script>alert("已送陳\n並將信件傳送至主管信箱：'.$boss_mail.'\n及副本傳送至承辦人信箱：'.$worker_mail.'")</script>';
            echo '<script>alert("已送陳\n並將信件傳送至主管信箱：'.$boss_mail.'")</script>';
            echo '<script>window.close();</script>';

        } 
            
        //--------------------被退回再次送陳資料 
        if (($atv == "update")&&($ppost == 1)){
                $post['status'] = 2; 

                if ($post['to_leader'] == 0){   //如果取消會辦承辦人，將承辦人欄位清空
                    $post['leader'] = NULL;
                    $post['leader_sign'] = NULL;
                }            
                 
            $rs = $this->course_sch_model->_update($seq_no, $post);//更新資料
            
        
            $id = 49;//email樣版編號
            $mail_template = $this->course_sch_model->get_mail_template($id);
            $mts = replaceEmailContent($mail_template[0]['content'],$replace_data); //生成email樣版內容
            
            $mail_txt= "
            
            <table>
                <tr>
                    <td width=600>
                        <br>".$mts."
                        <br>
                    </td>
                </tr>
                <tr>
                    <td align=center >
                        <br>".$this->data['form1'][0]['training_text']."
                        <br>
                    </td>
                </tr>
            </table>";
            //$this->email->to('roger@posboss.com.tw'); //測試
            $this->email->to($boss_mail); //主管mail
            //$this->email->cc('roger@posboss.com.tw'); //承辦人mail
            //$this->email->bcc('fyua@hotmail.com');
            //$this->email->bcc('roger@posboss.com.tw, carlos.sixdotsit@gmail.com');
            $subject_txt ="公訓處課表-$class_txt->year 年度 $class_txt->class_name 第$class_txt->term 期-簽核通知";
            $this->email->subject($subject_txt);
            //$this->email->subject('公訓處課表-簽核通知');
            $this->email->message($mail_txt);
            $this->email->send();
            
            //echo '<script>alert("已送陳\n並將信件傳送至主管信箱：'.$boss_mail.'\n及副本傳送至承辦人信箱：'.$worker_mail.'")</script>';
            echo '<script>alert("已送陳\n並將信件傳送至主管信箱：'.$boss_mail.'")</script>';
            echo '<script>window.close();</script>';

        }
        //var_dump($post['boss']);
        
        if (($atv == "update")&&($ppost == 2)){

            if ($post['boss_op'] == 1){

                if ($post['to_leader'] == 0){ //主管取消會辦承辦人員
                    $post['status'] = 4; //則簽核直接通過
                    $post['leader'] = NULL;  //會辦承辦人欄位清空
                    $post['leader_sign'] = NULL;  //會辦承辦人簽名欄位清空
                    $post['end_date'] = date('Y-m-d H:i:s');
                }else{
                    $post['status'] = 3;
                } 
                $post['boss_date'] = date('Y-m-d H:i:s');
            }else{
                $post['status'] = 1;
                $post['boss_date'] = NULL;
                $post['end_date'] = NULL;
            } 
                    
            $rs = $this->course_sch_model->_update($seq_no, $post);
            
            //判斷信要寄給誰與訊息是什麼
            if($post['status'] == 1){
                $id = 51;//email樣版編號
                $mail_template = $this->course_sch_model->get_mail_template($id);
                $mts = replaceEmailContent($mail_template[0]['content'],$replace_data); //生成email樣版內容
                
                $mail_txt= "
                
                <table>
                    <tr>
                        <td width=600>
                            <br>".$mts."
                            <br>
                        </td>
                    </tr>
                    <tr>
                        <td width=600>
                            <br><b><span style='font-size:20px'>退回內容：</span></b>    
                            <br>
                            <br>".$post['boss_centext']."
                        </td>
                    </tr>
                </table>";
                //$this->email->to('roger@posboss.com.tw'); //測試
                $this->email->to($worker_mail); //主管mail
                //$this->email->cc('roger@posboss.com.tw'); //承辦人mail
                //$this->email->bcc('fyua@hotmail.com');
                //$this->email->bcc('roger@posboss.com.tw, carlos.sixdotsit@gmail.com');
                $subject_txt ="公訓處課表-$class_txt->year 年度 $class_txt->class_name 第$class_txt->term 期-退回通知";
                $this->email->subject($subject_txt);
                //$this->email->subject('公訓處課表-退回通知');
                echo '<script>alert("已退回\n並將信件傳送至承辦人信箱：'.$worker_mail.'")</script>';

            }elseif($post['status'] == 3){  //------------將信送給會辦承辦人
                $id = 50;//email樣版編號
                $mail_template = $this->course_sch_model->get_mail_template($id);
                $mts = replaceEmailContent($mail_template[0]['content'],$replace_data); //生成email樣版內容
                
                $mail_txt= "
                
                <table>
                    <tr>
                        <td width=600>
                            <br>".$mts."
                            <br>
                        </td>
                    </tr>
                    <tr>
                        <td align=center >
                            <br>".$this->data['form1'][0]['training_text']."
                            <br>
                        </td>
                    </tr>
                </table>";
                //$this->email->to('roger@posboss.com.tw'); //測試
                $this->email->to($leader_mail); //會辮承辦人mail
                //$this->email->cc('roger@posboss.com.tw'); //承辦人mail
                //$this->email->bcc('fyua@hotmail.com');
                //$this->email->bcc('roger@posboss.com.tw, carlos.sixdotsit@gmail.com');
                
                $subject_txt ="公訓處課表-$class_txt->year 年度 $class_txt->class_name 第$class_txt->term 期-簽核通知";
                $this->email->subject($subject_txt);
                //$this->email->subject('公訓處課表-簽核通知');
                echo '<script>alert("簽核完成\n並將信件傳送至會辦承辦人信箱：'.$leader_mail.'")</script>';
            }

            $this->email->message($mail_txt);
            $this->email->send();



            
            echo '<script>self.opener.location.reload(true);</script>';
            echo '<script>window.close();</script>';
        }
        if (($atv == "update")&&($ppost == 3)){   // 已送至會辦承辦人核閱

               
            if ($post['leader_op'] == 1){
                $post['status'] = 4;
                $post['leader_date'] = date('Y-m-d H:i:s');
                $post['end_date'] = date('Y-m-d H:i:s');
                echo '<script>alert("核閱成功")</script>';
            }else{
                $post['status'] = 1;
                $post['boss_date'] = NULL;
                $post['leader_date'] = NULL;
                $post['end_date'] = NULL;
            }
             
            $rs = $this->course_sch_model->_update($seq_no, $post);
            
            
            //判斷信要寄給誰與訊息是什麼
            if($post['status'] == 1){
                $id = 51;//email樣版編號
                $mail_template = $this->course_sch_model->get_mail_template($id);
                $mts = replaceEmailContent($mail_template[0]['content'],$replace_data); //生成email樣版內容
                
                $mail_txt= "
                
                <table>
                    <tr>
                        <td width=600>
                            <br>".$mts."
                            <br>
                        </td>
                    </tr>
                    <tr>
                        <td width=600>
                            <br><b><span style='font-size:20px'>退回內容：</span></b>    
                            <br>
                            <br>".$post['leader_centext']."
                        </td>
                    </tr>
                </table>";
                //$this->email->to('roger@posboss.com.tw'); //測試
                $this->email->to($worker_mail); //主管mail
                //$this->email->cc('roger@posboss.com.tw'); //承辦人mail
                //$this->email->bcc('fyua@hotmail.com');
                //$this->email->bcc('roger@posboss.com.tw, carlos.sixdotsit@gmail.com');
                $subject_txt ="公訓處課表-$class_txt->year 年度 $class_txt->class_name 第$class_txt->term 期-退回通知";
                $this->email->subject($subject_txt);
                //$this->email->subject('公訓處課表-退回通知');
                echo '<script>alert("已退回\n並將信件傳送至承辦人信箱：'.$worker_mail.'")</script>';

            }

            $this->email->message($mail_txt);
            $this->email->send();


            echo '<script>self.opener.location.reload(true);</script>';
            echo '<script>window.close();</script>';

        }

            
        //var_dump($subject_txt);die();
        $this->load->view('create_class/set_course/course_sch_app', $this->data);
    }

    public function edit($id=NULL)
    {
        $this->data['page_name'] = 'edit';
        $this->data['u_id'] = $this->flags->user['id'];

        $validate = true;
        if ($post = $this->input->post()){
            if (!empty($_FILES['course_schedule_file']['name'])){
                if (!fileExtensionCheck($_FILES['course_schedule_file']['name'], ['jpg', 'png'])){
                    echo "<script>alert('儲存失敗，課程表不允許的檔案格式');</script>";
                    $validate = false;
                }                
            }

            if (!empty(array_filter($_FILES['myfile']['name']))){
                if (!fileExtensionCheck($_FILES['myfile']['name'], ['odt', 'ods', 'odp', 'docx', 'xlsx', 'pptx', 'doc', 'xls', 'ppt', 'zip', 'rar', 'jpg', 'png', 'gif', 'pdf'])){
                    echo "<script>alert('儲存失敗，上傳檔案不允許的檔案格式');</script>";
                    $validate = false;
                }                
            }            
        }
        $post = $this->input->post();
        if ($post && $validate == true) {
            if($post['class_attribute'] == '1'){
                $post['is_assess'] = '1';
                $post['is_mixed'] = '0';
            } else if($post['class_attribute'] == '2'){
                $post['is_assess'] = '1';
                $post['is_mixed'] = '1';
            } else {
                $post['is_assess'] = '0';
                $post['is_mixed'] = '0';
            }

            if(isset($post['is_volunteer']) && $post['is_volunteer'] == '1'){
                $post['is_volunteer'] = '1';
            } else {
                $post['is_volunteer'] = '0';
            }

            if(isset($post['is_longrange']) && $post['is_longrange'] == '1'){
                $post['is_longrange'] = '1';
            } else {
                $post['is_longrange'] = '0';
            }

            if(isset($post['copy_status']) && $post['copy_status'] == 'copy_course'){
                $copy_course = $this->set_course_model->copyCourse($post['copy_year'],$post['copy_class_no'],$post['copy_term']);
                for ($i=0;$i<count($copy_course);$i++) { 
                    $post['course'] .= $copy_course[$i]['course_code'].',';
                }
                $copy_course = true;
            }

            if(!empty($post['course'])){
                $post['course'] = substr($post['course'], 0, -1);
                $course_list = explode(',', $post['course']);
            }

            if(isset($_FILES['course_schedule_file']['name'])){
                $uploaddir = DIR_UPLOAD_COURSE_SCHEDULE;
                $filename = date('YmdHis').'_'.basename($_FILES['course_schedule_file']['name']);
                $uploadfile = $uploaddir.$filename;
                $check_format = explode('.', $_FILES['course_schedule_file']['name']);
                if (isset($check_format[1]) && (strtolower($check_format[1]) == 'jpg' or strtolower($check_format[1]) == 'png') && move_uploaded_file($_FILES['course_schedule_file']['tmp_name'], iconv("utf-8", "big5", $uploadfile))){ 
                    $post['course_schedule_file_path'] = $filename;
                }
            }

            if(isset($post['notice_elearn']) && $post['notice_elearn'] == '1'){
                $notice_elearn = '1';
                $notice_start = $post['notice_start'];
                $notice_end = $post['notice_end'];
            } else {
                $notice_elearn = '0';
                $notice_start = NULL;
                $notice_end = NULL;
            }

            if ($this->_isVerify('edit', $post['class_attribute']) == TRUE) {
                $year = $post['year'];
                $class_no = $post['class_no'];
                $term = $post['term'];
                $del_file = $post['del_file'];

                if($post['del_schedule'] == '1'){
                    $post['course_schedule_file_path'] = null;
                }

                if(isset($_FILES['course_schedule_file']['name'])){
                    $uploaddir = DIR_UPLOAD_COURSE_SCHEDULE;
                    $filename = date('YmdHis').'_'.basename($_FILES['course_schedule_file']['name']);
                    $uploadfile = $uploaddir.$filename;
                    $check_format = explode('.', $_FILES['course_schedule_file']['name']);
                    if (isset($check_format[1]) && (strtolower($check_format[1]) == 'jpg' or strtolower($check_format[1]) == 'png') && move_uploaded_file($_FILES['course_schedule_file']['tmp_name'], iconv("utf-8", "big5", $uploadfile))){ 
                        $post['course_schedule_file_path'] = $filename;
                    }
                }

                if(!empty($post['start_date1'])){
                    $start_month = explode('-', $post['start_date1']);
                    if($start_month[1] >= 1 && $start_month[1] <= 3){
                        $post['reason'] = '1';
                    } else if($start_month[1] >= 4 && $start_month[1] <= 6){
                        $post['reason'] = '2';
                    } else if($start_month[1] >= 7 && $start_month[1] <= 9){
                        $post['reason'] = '3';
                    } else if($start_month[1] >= 10 && $start_month[1] <= 12){
                        $post['reason'] = '4';
                    }
                }

                if(isset($post['r_start_date']) && !empty($post['r_start_date'])){
                    $r_start_date = $post['r_start_date'];
                } else {
                    $r_end_date = array();
                }
                
                if(isset($post['r_end_date']) && !empty($post['r_end_date'])){
                    $r_end_date = $post['r_end_date'];
                } else {
                    $r_end_date = array();
                }

                if(isset($post['online_course_name']) && !empty($post['online_course_name'])){
                    $online_course_name = $post['online_course_name'];
                }

                if(isset($post['hours']) && !empty($post['hours'])){
                    $hours = $post['hours'];
                }

                if(isset($post['teacher_name']) && !empty($post['teacher_name'])){
                    $teacher_name = $post['teacher_name'];
                }

                if(isset($post['place']) && !empty($post['place'])){
                    $place = $post['place'];
                }

                if(isset($post['elrid']) && !empty($post['elrid'])){
                    $elrid = $post['elrid'];
                }

                if(isset($post['listCourse'])){
                    unset($post['listCourse']);
                }
                unset($post['year']);
                unset($post['class_no']);
                unset($post['term']);
                unset($post['del_file']);
                unset($post['del_schedule']);
                unset($post['room_name']);
                unset($post['class_attribute']);
                unset($post['copy_status']);
                unset($post['copy_year']);
                unset($post['copy_class_no']);
                unset($post['copy_term']);
                unset($post['course']);
                unset($post['notice_elearn']);
                unset($post['notice_start']);
                unset($post['notice_end']);
                unset($post['r_start_date']);
                unset($post['r_end_date']);
                unset($post['online_course_name']);
                unset($post['hours']);
                unset($post['teacher_name']);
                unset($post['place']);
                unset($post['elrid']);

                
                    if(isset($post['not_next'])){
                        if($post['not_next'] == '1'){
                            $not_next = 1;
                        }
                        
                        unset($post['not_next']);
                    }
                
                
                $rs = $this->set_course_model->_update($id, $post);
                if ($rs) {
                    $this->set_course_model->insertRequireOnline($year,$class_no,$term,$post['is_assess'],$post['is_mixed'],$r_start_date,$r_end_date,$online_course_name,$hours,$teacher_name,$place,$elrid);
                    
                    $total_size = 0;

                    if(!empty($del_file)){
                        $del_file = substr($del_file, 0, -1);
                        $del_file_list = explode(',',$del_file);
                        for($i=0;$i<count($del_file_list);$i++){
                            $this->set_course_model->deleteRequireFile($year,$class_no,$term,$del_file_list[$i]);
                        }
                    }

                    if(isset($_FILES['myfile']['name'])){
                        $uploaddir = DIR_UPLOAD_REQUIRE;
                        for($i=0;$i<count($_FILES['myfile']['name']);$i++){
                            $filename = basename($_FILES['myfile']['name'][$i]);
                            $uploadfile = $uploaddir.$filename;
                            $total_size += $_FILES['myfile']['size'][$i];
                            if ($total_size < 1000000 && move_uploaded_file(sys_get_temp_dir().DIRECTORY_SEPARATOR.basename($_FILES['myfile']['tmp_name'][$i]), iconv("utf-8", "big5", $uploadfile))){ 
                                $check_require_file = $this->set_course_model->insertRequireFile($year,$class_no,$term,$filename,$this->flags->user['id']);
                            }
                        }
                    }

                    $check_announcement_log = $this->set_course_model->checkAnnouncementLog($year,$class_no,$term);
                    if($check_announcement_log){
                        $this->set_course_model->updateAnnouncementLog($year,$class_no,$term,$notice_elearn,$notice_start,$notice_end);
                    } else {
                        $this->set_course_model->insertAnnouncementLog($year,$class_no,$term,$notice_elearn,$notice_start,$notice_end);
                    }

                    if(isset($course_list) && !empty($course_list)){
                        $this->set_course_model->insertCourse($year,$class_no,$term,$course_list,$this->flags->user['id']);
                    } else {
                        $this->set_course_model->deleteCourse($year,$class_no,$term);
                    }
                }

                if(isset($copy_course) && $copy_course){
                    redirect(base_url("create_class/set_course/edit/{$id}/?{$_SERVER['QUERY_STRING']}"));
                } else if(isset($not_next) && $not_next == '1') {
                    redirect(base_url("create_class/set_course/edit/{$id}/?{$_SERVER['QUERY_STRING']}"));
                } else {
                    redirect(base_url("create_class/set_course/edit2/{$id}/?{$_SERVER['QUERY_STRING']}"));
                }
            }
        }

        $this->data['form'] = $this->set_course_model->getFormDefault($this->set_course_model->get($id));
        $this->data['choices']['quit_class'] = array('2'=>'2','3'=>'3','4'=>'4','5'=>'5','6'=>'6','7'=>'7','8'=>'8','9'=>'9','10'=>'10'); 

        $this->data['form']['course_list'] = $this->set_course_model->getCourse($this->data['form']['year'],$this->data['form']['class_no'],$this->data['form']['term']);

        $this->data['form']['myfile'] = $this->set_course_model->getUploadFile($this->data['form']['year'],$this->data['form']['class_no'],$this->data['form']['term']);

        $this->data['form']['course'] = '';
        if(!empty($this->data['form']['course_list'])){
            for ($i=0;$i<count($this->data['form']['course_list']);$i++) { 
                $this->data['form']['course'] .= $this->data['form']['course_list'][$i]['course_code'].',';
            }
        }

        if(isset($this->data['form']['room_code']) && !empty($this->data['form']['room_code'])){
            $this->data['form']['room_name'] = $this->createclass_model->getRoomName($this->data['form']['room_code']);
        }

        if(isset($this->data['form']['worker']) && !empty($this->data['form']['worker'])){
            $this->data['form']['worker_name'] = $this->set_course_model->getWorkerName($this->data['form']['worker']);
        } else {
            $this->data['form']['worker_name'] = '';
        }

        if($this->data['form']['range_real'] > 0){
            $this->data['choices']['quit_class2'][''] = '請選擇';
            for($i=1;$i<=$this->data['form']['range_real'];$i++){
                $this->data['choices']['quit_class2'][$i] = $i;
            }
        } else {
            $this->data['choices']['quit_class2'][''] = '請選擇';
            for($i=1;$i<=10;$i++){
                $this->data['choices']['quit_class2'][$i] = $i;
            }
        }

        if($this->data['form']['quit_class2'] > 0){
            $this->data['choices']['quit_class_hours'] = $this->data['form']['quit_class2'];
        } else {
            if($this->data['form']['quit_class'] > 0){
                $this->data['choices']['quit_class_hours'] = ceil($this->data['form']['range_real']/$this->data['form']['quit_class']);
            } else {
                $this->data['choices']['quit_class_hours'] = 0;
            }
            
        }

        $this->data['choices']['class_attribute'] = array('-1'=>'請選擇','0'=>'無','1'=>'考核班期','2'=>'考核+混成班期'); 

        if($this->data['form']['is_assess'] == '1' && $this->data['form']['is_mixed'] == '1'){
            $this->data['form']['class_attribute'] = 2;
        } else if($this->data['form']['is_assess'] == '1'){
            $this->data['form']['class_attribute'] = 1;
        } else if(empty($this->data['form']['is_assess'])){
            $this->data['form']['class_attribute'] = 0;
        } else {
            $this->data['form']['class_attribute'] = -1;
        }

        if($this->data['form']['class_attribute'] == 2){
            $this->data['form']['online_course'] = $this->set_course_model->getRequireOnline($this->data['form']['year'],$this->data['form']['class_no'],$this->data['form']['term']);
        }

        if(isset($this->data['form']['ecpa_class_id']) && !empty($this->data['form']['ecpa_class_id'])){
            $this->data['form']['ecpa_class_name'] = $this->createclass_model->getEcpaClassName($this->data['form']['ecpa_class_id']);
        }

        if(isset($this->data['form']['term']) && $this->data['form']['term'] > 1){
            $edu_code = $this->set_course_model->getEduCode($this->data['form']['year'],$this->data['form']['class_no'],'1');
            if(!empty($edu_code)){
                $this->data['form']['new_env_r1'] = (!empty($edu_code[0]['env_r1'])?$edu_code[0]['env_r1']:'0');
                $this->data['form']['new_env_r2'] = (!empty($edu_code[0]['env_r2'])?$edu_code[0]['env_r2']:'0');
                $this->data['form']['new_env_r3'] = (!empty($edu_code[0]['env_r3'])?$edu_code[0]['env_r3']:'0');
                $this->data['form']['new_env_r4'] = (!empty($edu_code[0]['env_r4'])?$edu_code[0]['env_r4']:'0');
            } 
        } else {
            $this->data['form']['new_env_r1'] = '0';
            $this->data['form']['new_env_r2'] = '0';
            $this->data['form']['new_env_r3'] = '0';
            $this->data['form']['new_env_r4'] = '0';
        }

        $mix_publish_info =  $this->set_course_model->getMixPublishInfo($this->data['form']['year'],$this->data['form']['class_no'],$this->data['form']['term']);

        if(!empty($mix_publish_info)){
            $this->data['form']['notice_elearn'] = $mix_publish_info[0]['notice_elearn'];
            $this->data['form']['notice_start'] = $mix_publish_info[0]['notice_start'];
            $this->data['form']['notice_end'] = $mix_publish_info[0]['notice_end'];
        } else {
            $this->data['form']['notice_elearn'] = '';
            $this->data['form']['notice_start'] = '';
            $this->data['form']['notice_end'] = '';
        }

        $this->data['form']['preq_main'] = $this->set_course_model->getPreqMainData($this->data['form']['year'],$this->data['form']['class_no'],$this->data['form']['term']);

        if(!empty($this->data['form']['preq_main'])){
            $preq_item = $this->set_course_model->getPreqItem($this->data['form']['preq_main'][0]['preq_id']);
            $this->data['form']['preq_item'] = '';
            for($i=0;$i<count($preq_item);$i++){
                $this->data['form']['preq_item'] .= $preq_item[$i]['item_title'].'<br>';
            }
            $this->data['form']['preq_count'] = $this->set_course_model->getPreqCount($this->data['form']['preq_main'][0]['preq_id']);
            $preq_enter_link = 'preq_enter.php?qid='.$this->data['form']['preq_main'][0]['preq_id'].'&sno=preview';
            $this->data['link_preq_enter'] = base_url($preq_enter_link);
            $this->data['link_preq_export'] = base_url("create_class/set_course/exportExcel/".$this->data['form']['preq_main'][0]['preq_id']);
        }

        $this->data['link_save_not_next'] = base_url("create_class/set_course/edit/{$id}/?{$_SERVER['QUERY_STRING']}");
        $this->data['link_get_ecpa_name'] = base_url("create_class/set_course/getEcpaClassName");
        $preq_template_link = 'preq_template.php?query_year='.$this->data['form']['year'].'&query_class_no='.$this->data['form']['class_no'].'&query_term='.$this->data['form']['term'].'&uid='.$this->data['u_id'];
        $this->data['link_preq'] = base_url($preq_template_link);
        $this->data['link_save_next'] = base_url("create_class/set_course/edit/{$id}/?{$_SERVER['QUERY_STRING']}");
        $this->data['link_cancel'] = base_url("create_class/set_course/?{$_SERVER['QUERY_STRING']}");
        $this->layout->view('create_class/set_course/edit', $this->data);
    }

    public function edit2($id=NULL)
    {
        $mode = addslashes($this->input->post('mode'));
        $query_year = intval($this->input->post('query_year'));
        $query_class_no = addslashes($this->input->post('query_class_no'));
        $query_term = intval($this->input->post('query_term'));
        $query_class_name = addslashes($this->input->post('query_class_name'));

        if($mode == 'delBookingData' && !empty($query_year) && !empty($query_class_no) && !empty($query_term)){
            $this->set_course_model->delBookingData($query_year,$query_class_no,$query_term);
        }
        
        if($mode == 'add'){
            $this->data['postdata']['listCourse'] = $this->input->post('listCourse');
            $this->data['postdata']['teach1'] = $this->input->post('teach1');
            $this->data['postdata']['teach1_ID'] = $this->input->post('teach1_ID');
            $this->data['postdata']['teach1_TIT'] = $this->input->post('teach1_TIT');
            $this->data['postdata']['teach1_SORT'] = $this->input->post('teach1_SORT');
            $this->data['postdata']['teach2'] = $this->input->post('teach2');
            $this->data['postdata']['teach2_ID'] = $this->input->post('teach2_ID');
            $this->data['postdata']['teach2_TIT'] = $this->input->post('teach2_TIT');
            $this->data['postdata']['teach2_SORT'] = $this->input->post('teach2_SORT');
            $this->data['postdata']['selType'] = $this->input->post('selType');
            $this->data['postdata']['booking_date'] = $this->input->post('booking_date');
            $this->data['postdata']['booking_room_id'] = $this->input->post('booking_room_id');

            if(!empty($this->data['postdata']['booking_room_id'])){
                $this->data['postdata']['booking_room_name'] = $this->createclass_model->getRoomName($this->data['postdata']['booking_room_id']);
            } else {
                $this->data['postdata']['booking_room_name'] = '';
            }

            $this->data['postdata']['old_room_use_date'] = $this->input->post('old_room_use_date');
            $this->data['postdata']['old_room_use_id'] = $this->input->post('old_room_use_id');

            if(!empty($this->data['postdata']['old_room_use_id'])){
                $this->data['postdata']['old_room_use_name'] = $this->createclass_model->getRoomName($this->data['postdata']['old_room_use_id']);
            } else {
                $this->data['postdata']['old_room_use_name'] = '';
            }

            $this->data['postdata']['class_description'] = $this->input->post('class_description');

            if($this->data['postdata']['class_description'] == '1'){
                $this->data['postdata']['classFirst'] = $this->input->post('classFirst');
            }
            
            $this->data['postdata']['room_use_date'] = $this->input->post('room_use_date');
            $this->data['postdata']['room_id'] = $this->input->post('room_id');
            $this->data['postdata']['room_name'] = $this->input->post('room_name');
            $this->data['postdata']['pre_start_time'] = $this->input->post('pre_start_time');
            $this->data['postdata']['pre_end_time'] = $this->input->post('pre_end_time');

            $condition=[];
            $condition['class_no']=$query_class_no;
            $condition['year']=$query_year;
            $condition['term']=$query_term;
            $condition['booking_date']=$this->input->post('room_use_date');
            $condition['room_id']=$this->input->post('room_id');
            $condition['pre_start_time']=$this->input->post('pre_start_time');
            $condition['pre_end_time']=$this->input->post('pre_end_time');
            $room_use_status=$this->handouts_status_model->getBookRoom($condition);

            

                //檢查教室是否被其他班期預約使用
            if(!empty($room_use_status)){
                $this->setAlert(4,"該時段已有班期預約使用");
                $url=base_url("create_class/set_course/edit2/{$id}/?").$_SERVER['QUERY_STRING'];
                return redirect($url,'refresh');
            }

            $room_red_status=$this->handouts_status_model->getUseRoom($condition);
            if(!empty($room_red_status)){
                $this->setAlert(4,"該時段已有班期使用");
                $url=base_url("create_class/set_course/edit2/{$id}/?").$_SERVER['QUERY_STRING'];
                return redirect($url,'refresh');
            }
            
            

            if(strlen($this->data['postdata']['pre_start_time']) != 5){
                $this->data['postdata']['pre_start_time'] = substr($this->data['postdata']['pre_start_time'], 0, 2).':'.substr($this->data['postdata']['pre_start_time'], -2);
            }

            if(strlen($this->data['postdata']['pre_end_time']) != 5){
                $this->data['postdata']['pre_end_time'] = substr($this->data['postdata']['pre_end_time'], 0, 2).':'.substr($this->data['postdata']['pre_end_time'], -2);
            }

            $f1 = $this->input->post('f1');
            $f2 = $this->input->post('f2');
            $f3 = $this->input->post('f3');
            $f4 = $this->input->post('f4');
            $f5 = $this->input->post('f5');

            $chk1    = "12:30";   //超過此時間從下午起
            $chk2    = "18:00";   //超過此時間從晚上起
            $ftMin   = 30;      //晨間為開始時間前多少分

            $this->data['postdata']['pre_start_time'] = date("H:i",strtotime($this->data['postdata']['pre_end_time']."+".$f5." min"));
            $this->data['postdata']['pre_end_time'] = date("H:i",strtotime($this->data['postdata']['pre_end_time']."+".($f4+$f5)." min"));;
            
            if(strtotime( $this->data['postdata']['pre_start_time']) < strtotime($f1)){
                 $this->data['postdata']['pre_start_time'] = '';
            } else if((strtotime( $this->data['postdata']['pre_start_time']) > strtotime($chk1) && strtotime( $this->data['postdata']['pre_start_time']) < strtotime($f2)) || (strtotime($this->data['postdata']['pre_end_time']) > strtotime($chk1) && strtotime($this->data['postdata']['pre_end_time']) < strtotime($f2))){
                 $this->data['postdata']['pre_start_time'] = $f2;
            } else if((strtotime( $this->data['postdata']['pre_start_time']) > strtotime($chk2) && strtotime( $this->data['postdata']['pre_start_time']) < strtotime($f3)) || (strtotime($this->data['postdata']['pre_end_time']) > strtotime($chk2)) && strtotime($this->data['postdata']['pre_end_time']) < strtotime($f3)){
                 $this->data['postdata']['pre_start_time'] = $f3;
            } else if(strtotime( $this->data['postdata']['pre_start_time']) > strtotime('23:59')){
                 $this->data['postdata']['pre_start_time'] = '';
            }
           
            if((strtotime( $this->data['postdata']['pre_start_time']) > strtotime($chk1) && strtotime( $this->data['postdata']['pre_start_time']) < strtotime($f2)) || (strtotime($this->data['postdata']['pre_end_time']) > strtotime($chk1) && strtotime($this->data['postdata']['pre_end_time']) < strtotime($f2))){
                $this->data['postdata']['pre_end_time'] = date("H:i",strtotime($f2."+".$f4." min"));
            } else if((strtotime( $this->data['postdata']['pre_start_time']) > strtotime($chk2) && strtotime( $this->data['postdata']['pre_start_time']) < strtotime($f3)) || (strtotime($this->data['postdata']['pre_end_time']) > strtotime($chk2)) && strtotime($this->data['postdata']['pre_end_time']) < strtotime($f3)){
                $this->data['postdata']['pre_end_time'] = date("H:i",strtotime($f3."+".$f4." min"));
            } else if(strtotime($this->data['postdata']['pre_end_time']) > strtotime('23:59')){
                $this->data['postdata']['pre_end_time'] = '';
            }

            $this->data['postdata']['hours'] = $this->input->post('hours');

            // echo '<pre>';
            // print_r($this->input->post());
            // die();
            $hours = $this->input->post('hours');
            $teacher_list = $this->input->post('teach1_ID');
            $teacher_title = $this->input->post('teach1_TIT');
            $teacher_sort = $this->input->post('teach1_SORT');
            $course_code = $this->input->post('listCourse');

            if(!empty($teacher_list)){
                $teacher_list = explode(',', $teacher_list);
                for($i=0;$i<count($teacher_list);$i++){
                    $chk_cantech_exist = $this->set_course_model->checkCanteachExist($teacher_list[$i],$course_code,'1');
                    if(!$chk_cantech_exist){
                        $this->set_course_model->insertCanteach($teacher_list[$i],$course_code,'1',$this->flags->user['id']);
                    }
                    $this->handouts_status_model->insertHandoutsStatus($query_year,$query_class_no,$course_code,$teacher_list[$i]);//講義上傳預設無講義

                }
            }
            
            $assistant_list = $this->input->post('teach2_ID');
            $assistant_title = $this->input->post('teach2_TIT');
            $assistant_sort = $this->input->post('teach2_SORT');
            $course_code = $this->input->post('listCourse');



            if(!empty($assistant_list)){
                $assistant_list = explode(',', $assistant_list);
                for($i=0;$i<count($assistant_list);$i++){
                    $chk_cantech_exist = $this->set_course_model->checkCanteachExist($assistant_list[$i],$course_code,'2');
                    if(!$chk_cantech_exist){
                        $this->set_course_model->insertCanteach($assistant_list[$i],$course_code,'2',$this->flags->user['id']);
                    }
                }
            }

            //講義上傳預設無講義
            if(empty($teacher_list)){
                $emp=null;
                $this->handouts_status_model->insertHandoutsStatus($query_year,$query_class_no,$course_code,$emp);
            }

            


            $class_description = $this->input->post('class_description'); 
            if($class_description == '1'){
                $class_first = $this->input->post('classFirst');
                $chk_classfirst_exist = $this->set_course_model->checkClassFirstExist($query_year,$query_class_no,$query_term,$class_first);
                if(!$chk_classfirst_exist){
                    $this->set_course_model->insertSingleCourse($query_year,$query_class_no,$query_term,$class_first,$this->flags->user['id']);
                }

                $course_code = $class_first;
            }
            

            $from_time = $this->input->post('pre_start_time');
            $to_time = $this->input->post('pre_end_time');

            if(strlen($from_time) != 5){
                $from_time = substr($from_time, 0, 2).':'.substr($from_time, -2);
            }

            if(strlen($to_time) != 5){
                $to_time = substr($to_time, 0, 2).':'.substr($to_time, -2);
            }

            $booking_date = $this->input->post('final_course_date');
            $room_id = $this->input->post('final_room_id');

            $PIDNM   = array("班務說明","第一節","第二節","第三節","第四節","第五節","第六節","第七節","第八節","第九節","第十節","第11節","第12節","第13節","第14節","第15節","第16節");


            if($class_description == '1'){
                $chk_class_description = $this->set_course_model->checkClassDescription($query_year,$query_class_no,$query_term,$booking_date);
            } else {
                $chk_class_description = 0;
            }

            
            //$chk_room_use = $this->set_course_model->checkRoomUse($booking_date,$room_id,$from_time,$to_time);
            $chk_room_use = $this->set_course_model->checkRoom_use($booking_date,$room_id,$from_time,$to_time,$query_year,$query_class_no,$query_term);//新增課表用來檢查教室的預約是否跟此課程相同
            $chk_appinfo = $this->set_course_model->checkRoomUseForAppinfo($booking_date,$room_id,$from_time,$to_time);
            $chk_booking = $this->set_course_model->checkBookingExist($booking_date,$room_id,$from_time,$to_time,$query_year,$query_class_no,$query_term);
            
            
            if($from_time == '' || $to_time == '' || $chk_class_description > 0 || $chk_room_use || $chk_appinfo || $chk_booking){
               
                if($chk_class_description > 0){
                    // $this->setAlert(2, '該日期已有班務說明課表,請重新輸入');
                    echo '<script>alert("該日期已有班務說明課表,請重新輸入")</script>';
                } else if($chk_room_use || $chk_appinfo || $chk_booking){
                    $chk_error_message = htmlspecialchars($query_year, ENT_HTML5|ENT_QUOTES).'年度'.htmlspecialchars($query_class_name, ENT_HTML5|ENT_QUOTES).'第'.htmlspecialchars($query_term, ENT_HTML5|ENT_QUOTES).'期該教室時段已有使用,請重新輸入';
                    
                    // $this->setAlert(2, $chk_error_message);
                    echo "<script>alert('$chk_error_message')</script>";
                    //echo "<script>alert({$chk_error_message})</script>";
                } else {
                    echo '<script>alert("該日課表已滿,請重新輸入")</script>';
                    // $this->setAlert(2, '該日課表已滿,請重新輸入');
                }
                // redirect(base_url("create_class/set_course/edit2/{$id}/?{$_SERVER['QUERY_STRING']}"));
            } else {
                
                if($class_description == '1'){
                    if($chk_class_description == 0){
                        $pno = '00';
                        $pid = '0';
                        
                    }
                    
                } else {

                    $get_period = $this->set_course_model->getCourseTime($query_year,$query_class_no,$query_term,$booking_date,$class_description);
                    if(!empty($get_period) && intval($get_period[0]['id']) >= 10){
                        $pno = intval($get_period[0]['id']) + 1;
                        $pid = $pno;
                    } else if(!empty($get_period) && intval($get_period[0]['id']) > 0){
                        $pno = intval($get_period[0]['id']) + 1;

                        if($pno < 10){
                            $pno = '0'.$pno;
                        }

                        $pid = intval($pno);
                    } else {
                        $pno = '01';
                        $pid = '1';
                    }
                }
                
               
                $this->set_course_model->insertPeriodtime($query_year,$query_class_no,$query_term,$booking_date,$room_id,$course_code,$from_time,$to_time,$pno,$PIDNM[$pid]);

                if(empty($teacher_list)){
                    $this->set_course_model->insertRoomuse($query_year,$query_class_no,$query_term,$booking_date,$room_id,$course_code,$pno,$hours);
                } else {
                    $teacher_title = explode(",",$teacher_title);
                    $teacher_sort = explode(",",$teacher_sort);

                    for($i=0;$i<count($teacher_list);$i++){
                        $this->set_course_model->insertRoomuse($query_year,$query_class_no,$query_term,$booking_date,$room_id,$course_code,$pno,$hours,$teacher_list[$i],$teacher_title[$i],$teacher_sort[$i],'Y');
                    }

                    if(is_array($assistant_list) && count($assistant_list) > 0){
                        $assistant_title = explode(",",$assistant_title);
                        $assistant_sort = explode(",",$assistant_sort);

                        for($i=0;$i<count($assistant_list);$i++){
                            $this->set_course_model->insertRoomuse($query_year,$query_class_no,$query_term,$booking_date,$room_id,$course_code,$pno,$hours,$assistant_list[$i],$assistant_title[$i],$assistant_sort[$i],'N');
                        }
                    }
                }

                $chk_hour_traffic = $this->set_course_model->chkHourTrafficTax($query_year,$query_class_no,$query_term,$booking_date);

                if(!$chk_hour_traffic){
                    // $this->set_course_model->delHourTrafficTax($query_year,$query_class_no,$query_term,$booking_date);
                    $this->set_course_model->insertHourTrafficTax($query_year,$query_class_no,$query_term,$booking_date);
                    $this->set_course_model->updateHourTrafficTax($query_year,$query_class_no,$query_term,$booking_date);
                } 
                

                $this->set_course_model->insertDiningTeacher($query_year,$query_class_no,$query_term,$booking_date,$this->flags->user['username']);
                $this->set_course_model->insertDiningStudent($query_year,$query_class_no,$query_term,$booking_date,$this->flags->user['username']);
                $this->set_course_model->updateRequireStartEndDate($query_year,$query_class_no,$query_term);
                $this->set_course_model->updateRequireSeason($query_year,$query_class_no,$query_term);
                $this->set_course_model->updateRequireRoom($query_year,$query_class_no,$query_term);
                $this->set_course_model->insertCourseTeacher($query_year,$query_class_no,$query_term,$this->flags->user['username']);

                // redirect(base_url("create_class/set_course/edit2/{$id}/?{$_SERVER['QUERY_STRING']}"));
            }
        }

        if($mode == 'upd'){ 
            $this->data['postdata']['listCourse'] = $this->input->post('listCourse');
            $this->data['postdata']['teach1'] = $this->input->post('teach1');
            $this->data['postdata']['teach1_ID'] = $this->input->post('teach1_ID');
            $this->data['postdata']['teach1_TIT'] = $this->input->post('teach1_TIT');
            $this->data['postdata']['teach1_SORT'] = $this->input->post('teach1_SORT');
            $this->data['postdata']['teach2'] = $this->input->post('teach2');
            $this->data['postdata']['teach2_ID'] = $this->input->post('teach2_ID');
            $this->data['postdata']['teach2_TIT'] = $this->input->post('teach2_TIT');
            $this->data['postdata']['teach2_SORT'] = $this->input->post('teach2_SORT');
            $this->data['postdata']['selType'] = $this->input->post('selType');
            $this->data['postdata']['booking_date'] = $this->input->post('booking_date');
            $this->data['postdata']['booking_room_id'] = $this->input->post('booking_room_id');
            //var_dump($this->data['postdata']);
            //die();

            if(!empty($this->data['postdata']['booking_room_id'])){
                $this->data['postdata']['booking_room_name'] = $this->createclass_model->getRoomName($this->data['postdata']['booking_room_id']);
            } else {
                $this->data['postdata']['booking_room_name'] = '';
            }

            $this->data['postdata']['old_room_use_date'] = $this->input->post('old_room_use_date');
            $this->data['postdata']['old_room_use_id'] = $this->input->post('old_room_use_id');

            if(!empty($this->data['postdata']['old_room_use_id'])){
                $this->data['postdata']['old_room_use_name'] = $this->createclass_model->getRoomName($this->data['postdata']['old_room_use_id']);
            } else {
                $this->data['postdata']['old_room_use_name'] = '';
            }

            $this->data['postdata']['class_description'] = $this->input->post('class_description');

            if($this->data['postdata']['class_description'] == '1'){
                $this->data['postdata']['classFirst'] = $this->input->post('classFirst');
            }
            
            $this->data['postdata']['room_use_date'] = $this->input->post('room_use_date');
            $this->data['postdata']['room_id'] = $this->input->post('room_id');
            $this->data['postdata']['room_name'] = $this->input->post('room_name');
            $this->data['postdata']['pre_start_time'] = $this->input->post('pre_start_time');
            $this->data['postdata']['pre_end_time'] = $this->input->post('pre_end_time');

            $condition=[];
            $condition['class_no']=$query_class_no;
            $condition['year']=$query_year;
            $condition['term']=$query_term;
            $condition['booking_date']=$this->input->post('room_use_date');
            $condition['room_id']=$this->input->post('room_id');
            $condition['pre_start_time']=$this->input->post('pre_start_time');
            $condition['pre_end_time']=$this->input->post('pre_end_time');
            $room_use_status=$this->handouts_status_model->getBookRoom($condition);

            

            
                //檢查教室是否被其他班期預約使用
            if(!empty($room_use_status)){
                $this->setAlert(4,"該時段已有班期預約使用");
                $url=base_url("create_class/set_course/edit2/{$id}/?").$_SERVER['QUERY_STRING'];
                return redirect($url,'refresh');
            }

            $room_red_status=$this->handouts_status_model->getUseRoomForEdit($condition);
            if(!empty($room_red_status)){
                $this->setAlert(4,"該時段已有班期使用");
                $url=base_url("create_class/set_course/edit2/{$id}/?").$_SERVER['QUERY_STRING'];
                return redirect($url,'refresh');
            }

            if(strlen($this->data['postdata']['pre_start_time']) != 5){
                $this->data['postdata']['pre_start_time'] = substr($this->data['postdata']['pre_start_time'], 0, 2).':'.substr($this->data['postdata']['pre_start_time'], -2);
            }

            if(strlen($this->data['postdata']['pre_end_time']) != 5){
                $this->data['postdata']['pre_end_time'] = substr($this->data['postdata']['pre_end_time'], 0, 2).':'.substr($this->data['postdata']['pre_end_time'], -2);
            }

            $f1 = $this->input->post('f1');
            $f2 = $this->input->post('f2');
            $f3 = $this->input->post('f3');
            $f4 = $this->input->post('f4');
            $f5 = $this->input->post('f5');

            $chk1    = "12:30";   //超過此時間從下午起
            $chk2    = "18:00";   //超過此時間從晚上起
            $ftMin   = 30;      //晨間為開始時間前多少分

            $this->data['postdata']['pre_start_time'] = date("H:i",strtotime($this->data['postdata']['pre_end_time']."+".$f5." min"));
            $this->data['postdata']['pre_end_time'] = date("H:i",strtotime($this->data['postdata']['pre_end_time']."+".($f4+$f5)." min"));;
            
            if(strtotime( $this->data['postdata']['pre_start_time']) < strtotime($f1)){
                 $this->data['postdata']['pre_start_time'] = '';
            } else if((strtotime( $this->data['postdata']['pre_start_time']) > strtotime($chk1) && strtotime( $this->data['postdata']['pre_start_time']) < strtotime($f2)) || (strtotime($this->data['postdata']['pre_end_time']) > strtotime($chk1) && strtotime($this->data['postdata']['pre_end_time']) < strtotime($f2))){
                 $this->data['postdata']['pre_start_time'] = $f2;
            } else if((strtotime( $this->data['postdata']['pre_start_time']) > strtotime($chk2) && strtotime( $this->data['postdata']['pre_start_time']) < strtotime($f3)) || (strtotime($this->data['postdata']['pre_end_time']) > strtotime($chk2)) && strtotime($this->data['postdata']['pre_end_time']) < strtotime($f3)){
                 $this->data['postdata']['pre_start_time'] = $f3;
            } else if(strtotime( $this->data['postdata']['pre_start_time']) > strtotime('23:59')){
                 $this->data['postdata']['pre_start_time'] = '';
            }
           
            if((strtotime( $this->data['postdata']['pre_start_time']) > strtotime($chk1) && strtotime( $this->data['postdata']['pre_start_time']) < strtotime($f2)) || (strtotime($this->data['postdata']['pre_end_time']) > strtotime($chk1) && strtotime($this->data['postdata']['pre_end_time']) < strtotime($f2))){
                $this->data['postdata']['pre_end_time'] = date("H:i",strtotime($f2."+".$f4." min"));
            } else if((strtotime( $this->data['postdata']['pre_start_time']) > strtotime($chk2) && strtotime( $this->data['postdata']['pre_start_time']) < strtotime($f3)) || (strtotime($this->data['postdata']['pre_end_time']) > strtotime($chk2)) && strtotime($this->data['postdata']['pre_end_time']) < strtotime($f3)){
                $this->data['postdata']['pre_end_time'] = date("H:i",strtotime($f3."+".$f4." min"));
            } else if(strtotime($this->data['postdata']['pre_end_time']) > strtotime('23:59')){
                $this->data['postdata']['pre_end_time'] = '';
            }

            $this->data['postdata']['hours'] = $this->input->post('hours');

            $hours = $this->input->post('hours');
            $teacher_list = $this->input->post('teach1_ID');
            $teacher_title = $this->input->post('teach1_TIT');
            $teacher_sort = $this->input->post('teach1_SORT');
            $course_code = $this->input->post('listCourse');


            if(!empty($teacher_list)){
                $teacher_list = explode(',', $teacher_list);
                for($i=0;$i<count($teacher_list);$i++){
                    $chk_cantech_exist = $this->set_course_model->checkCanteachExist($teacher_list[$i],$course_code,'1');
                    if(!$chk_cantech_exist){
                        $this->set_course_model->insertCanteach($teacher_list[$i],$course_code,'1',$this->flags->user['id']);
                    }
                }
            }
            
            $assistant_list = $this->input->post('teach2_ID');
            $assistant_title = $this->input->post('teach2_TIT');
            $assistant_sort = $this->input->post('teach2_SORT');
            $course_code = $this->input->post('listCourse');

            if(!empty($assistant_list)){
                $assistant_list = explode(',', $assistant_list);
                for($i=0;$i<count($assistant_list);$i++){
                    $chk_cantech_exist = $this->set_course_model->checkCanteachExist($assistant_list[$i],$course_code,'2');
                    if(!$chk_cantech_exist){
                        $this->set_course_model->insertCanteach($assistant_list[$i],$course_code,'2',$this->flags->user['username']);
                    }
                }
            }

            $class_description = $this->input->post('class_description'); 
            if($class_description == '1'){
                $class_first = $this->input->post('classFirst');
                $chk_classfirst_exist = $this->set_course_model->checkClassFirstExist($query_year,$query_class_no,$query_term,$class_first);
                if(!$chk_classfirst_exist){
                    $this->set_course_model->insertSingleCourse($query_year,$query_class_no,$query_term,$class_first,$this->flags->user['id']);
                }

                $course_code = $class_first;
            }

            $old_period = $this->input->post('per1');
            $old_course_code = $this->input->post('per7');
            $old_room_id = $this->input->post('per6');
            $old_course_date = $this->input->post('per5');
            $upd_from_time = $this->input->post('pre_start_time');
            $upd_to_time = $this->input->post('pre_end_time');
            $upd_room_id = $this->input->post('final_room_id');
            $upd_course_code = $this->input->post('listCourse');
            $upd_hours = $this->input->post('hours');
            $chkA1 = 0;
            $chkA2 = 0;

            if ($upd_from_time != "" && $upd_to_time != ""){
               
            } else{
                $chkA2 = 1;
            }

            if($chkA2==0){
                $chk_room_use = $this->set_course_model->checkRoomUse($old_course_date,$upd_room_id,$upd_from_time,$upd_to_time,$query_year,$query_class_no,$query_term);
                $chk_appinfo = $this->set_course_model->checkRoomUseForAppinfo($old_course_date,$upd_room_id,$upd_from_time,$upd_to_time);
                $chk_booking = $this->set_course_model->checkBookingExist($old_course_date,$upd_room_id,$upd_from_time,$upd_to_time,$query_year,$query_class_no,$query_term);
            }

            if($chk_room_use || $chk_appinfo || $chk_booking){
                // $this->setAlert(2, '該時間衝堂,請重新輸入');
                echo '<script>alert("該時間衝堂,請重新輸入")</script>';
                // redirect(base_url("create_class/set_course/edit2/{$id}/?{$_SERVER['QUERY_STRING']}"));
            } else if($chkA2 > 0){
                // $this->setAlert(2, '預排時間不可為空白');
                echo '<script>alert("預排時間不可為空白")</script>';
                // redirect(base_url("create_class/set_course/edit2/{$id}/?{$_SERVER['QUERY_STRING']}"));
            } else {
                $this->set_course_model->updatePeriodtime($query_year,$query_class_no,$query_term,$upd_course_code,$upd_room_id,$upd_from_time,$upd_to_time,$old_room_id,$old_course_code,$old_period,$old_course_date);

                $this->set_course_model->deleteRoomUse($query_year,$query_class_no,$query_term,$old_course_date,$old_period,$old_room_id,$old_course_code);

                if(empty($teacher_list)){
                    $this->set_course_model->insertRoomuse($query_year,$query_class_no,$query_term,$old_course_date,$upd_room_id,$upd_course_code,$old_period,$upd_hours);
                } else {
                    $teacher_title = explode(",",$teacher_title);
                    $teacher_sort = explode(",",$teacher_sort);
                    


                    for($i=0;$i<count($teacher_list);$i++){
                        if(!isset($teacher_sort[$i])){
                            $teacher_sort[$i]=null;
                        }
                        if(!isset($teacher_title[$i])){
                            $teacher_title[$i]=null;
                        }
                        $this->set_course_model->insertRoomuse($query_year,$query_class_no,$query_term,$old_course_date,$upd_room_id,$upd_course_code,$old_period,$upd_hours,$teacher_list[$i],$teacher_title[$i],$teacher_sort[$i],'Y');
                    }

                    if(is_array($assistant_list) && count($assistant_list) > 0){
                        $assistant_title = explode(",",$assistant_title);
                        $assistant_sort = explode(",",$assistant_sort);
                        
                        for($i=0;$i<count($assistant_list);$i++){
                            if(!isset($assistant_sort[$i])){
                                $assistant_sort[$i]=null;
                            }
                            if(!isset($assistant_title[$i])){
                                $assistant_title[$i]=null;
                            }
                            $this->set_course_model->insertRoomuse($query_year,$query_class_no,$query_term,$old_course_date,$upd_room_id,$upd_course_code,$old_period,$upd_hours,$assistant_list[$i],$assistant_title[$i],$assistant_sort[$i],'N');
                        }
                    }
                    
                }

                
                $chk_is_volunteer = $this->set_course_model->checkIsVolunteer($query_year,$query_class_no,$query_term);

                if($chk_is_volunteer){

                    $volunteer_class_id = $this->volunteer_model->getVolunteerClassId($query_year,$query_class_no,$query_term);

                    if($volunteer_class_id > 0){
                        $old_upd_room_id = $old_room_id;
                        $old_upd_course_date = $old_course_date; 
                        $old_upd_to_time = intval($this->input->post('per4'));
                        if($old_upd_to_time > 1300){
                            $volunteer_type = 2;
                        } else {
                            $volunteer_type = 1;
                        }
                        if($old_upd_room_id != $upd_room_id){
                            $classroom_id = $this->volunteer_model->getClassRoomId($upd_room_id);
                            if($classroom_id < 0){
                                $room_info = $this->set_course_model->getRoomInfo($upd_room_id);
                                $vcid = $this->volunteer_model->insertClassRoom($upd_room_id,$room_info);
                            } else {
                                $vcid = $this->volunteer_model->getVcid($upd_room_id);
                            }
                            
                            $old_vcid = $this->volunteer_model->getVcid($old_upd_room_id);

                            if($vcid > 0 && $old_vcid > 0){
                                $chk_update_volunteer_calendar = $this->volunteer_model->updateVolunteerCalendarRoom($volunteer_class_id,$vcid,$old_vcid,$old_upd_course_date,$volunteer_type);
                                if(!$chk_update_volunteer_calendar){
                                    echo '<script>alert("同步志工資料失敗")</script>';
                                }
                            } else {
                                echo '<script>alert("同步志工資料失敗")</script>';
                            }
                        }  
                    }
                }
               

                $chk_hour_traffic = $this->set_course_model->chkHourTrafficTax($query_year,$query_class_no,$query_term,$old_course_date);

                if(!$chk_hour_traffic){
                    // $this->set_course_model->delHourTrafficTax($query_year,$query_class_no,$query_term,$old_course_date);
                    $this->set_course_model->insertHourTrafficTax($query_year,$query_class_no,$query_term,$old_course_date);
                    $this->set_course_model->updateHourTrafficTax($query_year,$query_class_no,$query_term,$old_course_date);
                } 

                $this->set_course_model->insertDiningTeacher($query_year,$query_class_no,$query_term,$old_course_date,$this->flags->user['username']);
                $this->set_course_model->insertDiningStudent($query_year,$query_class_no,$query_term,$old_course_date,$this->flags->user['username']);
                $this->set_course_model->updateRequireStartEndDate($query_year,$query_class_no,$query_term);
                $this->set_course_model->updateRequireSeason($query_year,$query_class_no,$query_term);
                $this->set_course_model->updateRequireRoom($query_year,$query_class_no,$query_term);
                $this->set_course_model->insertCourseTeacher($query_year,$query_class_no,$query_term,$this->flags->user['username']);

                // redirect(base_url("create_class/set_course/edit2/{$id}/?{$_SERVER['QUERY_STRING']}"));
            }
        }   

        if($mode == 'del'){
            $del_course_date = $_POST['delKey1'];
            $del_period = $_POST['delKey2'];
            $del_room_id = $_POST['delKey3'];

            $chk_hour_traffic = $this->set_course_model->chkHourTrafficTax($query_year,$query_class_no,$query_term,$del_course_date);

            if($chk_hour_traffic){
                $del_message = $del_course_date.'已進入請款流程，不得刪除，若欲刪除，請先到請款作業將「處理狀態」回復成「空值」';
                $this->setAlert(2, $del_message);
            } else {
                $this->set_course_model->delHourTrafficTax($query_year,$query_class_no,$query_term,$del_course_date);
                $this->set_course_model->deleteRoomUse($query_year,$query_class_no,$query_term,$del_course_date,$del_period,$del_room_id);
                $this->set_course_model->delPeriodtime($query_year,$query_class_no,$query_term,$del_course_date,$del_period,$del_room_id);

                $this->set_course_model->insertHourTrafficTax($query_year,$query_class_no,$query_term,$del_course_date);
                $this->set_course_model->updateHourTrafficTax($query_year,$query_class_no,$query_term,$del_course_date);

                $this->set_course_model->insertDiningTeacher($query_year,$query_class_no,$query_term,$del_course_date,$this->flags->user['username']);
                $this->set_course_model->insertDiningStudent($query_year,$query_class_no,$query_term,$del_course_date,$this->flags->user['username']);
                $this->set_course_model->updateRequireStartEndDate($query_year,$query_class_no,$query_term);
                $this->set_course_model->updateRequireSeason($query_year,$query_class_no,$query_term);
                $this->set_course_model->updateRequireRoom($query_year,$query_class_no,$query_term);
                
                $this->handouts_status_model->deleteHandoutsStatus($query_year,$query_class_no,$course_code);//刪除講義上傳handout_status的資料

                redirect(base_url("create_class/set_course/edit2/{$id}/?{$_SERVER['QUERY_STRING']}"));
            }
        }

        if($mode == 'check_update'){
            $retrun_url = base_url("create_class/set_course/edit2/{$id}/?{$_SERVER['QUERY_STRING']}");

            $this->set_course_model->updateRequireStartEndDate($query_year,$query_class_no,$query_term);
            $this->set_course_model->updateRequireRoom($query_year,$query_class_no,$query_term);

            $check_result = $this->chkHoursNew($query_year,$query_class_no,$query_term);
            
            $range_real = 0;
            $hrs = 0;
            if(intval($check_result[0]['range_real']) > 0){
                $range_real = intval($check_result[0]['range_real']);
            } 

            if(intval($check_result[0]['hrs']) > 0){
                $hrs = $check_result[0]['hrs'];
            } 

            if($range_real == $hrs){
                echo '<script>';
                $msg_result = '檢核正常(鐘點時數:'.$hrs.'小時，'.'實體時數:'.$range_real.'小時)';
                echo "alert('$msg_result');";
                echo "location.href='$retrun_url'";
                echo '</script>';
            } else {
                echo '<script>';
                $msg_result = '鐘點時數加總與實體時數不符(鐘點時數:'.$hrs.'小時，'.'實體時數:'.$range_real.'小時)';
                echo "alert('$msg_result');";
                echo "location.href='$retrun_url'";
                echo '</script>';
            }
        }

        $this->data['page_name'] = 'edit';
        $this->data['form'] = $this->set_course_model->getFormDefault($this->set_course_model->get($id));

        $this->data['form']['course_list'] = $this->set_course_model->getCourse($this->data['form']['year'],$this->data['form']['class_no'],$this->data['form']['term']);
        
        if(isset($this->data['form']['worker']) && !empty($this->data['form']['worker'])){
            $this->data['form']['worker_name'] = $this->set_course_model->getWorkerName($this->data['form']['worker']);
        } else {
            $this->data['form']['worker_name'] = '';
        }

        if(isset($this->data['form']['room_code']) && !empty($this->data['form']['room_code'])){
            $this->data['form']['room_name'] = $this->createclass_model->getRoomName($this->data['form']['room_code']);
        }

        if($this->data['form']['quit_class2'] > 0){
            $this->data['choices']['quit_class_hours'] = $this->data['form']['quit_class2'];
        } else {
            if($this->data['form']['quit_class'] > 0){
                $this->data['choices']['quit_class_hours'] = ceil($this->data['form']['range_real']/$this->data['form']['quit_class']);
            } else {
                $this->data['choices']['quit_class_hours'] = 0;
            }
        }


        $this->data['form']['isready'] = $this->set_course_model->getClassIsready($this->data['form']['year'],$this->data['form']['class_no'],$this->data['form']['term']);
        $course_schedule = $this->set_course_model->getCourseSchedule($this->data['form']['year'],$this->data['form']['class_no'],$this->data['form']['term']);

        // echo '<pre>';
        // print_r($course_schedule);
        // die();
        
        for($i=0;$i<count($course_schedule);$i++){
            $end_tag = FALSE;
            if($i == 0 || $course_schedule[$i]['use_date'] != $course_schedule[$i-1]['use_date']){
                $this->data['form']['course_schedule'][$course_schedule[$i]['use_date']][] = $course_schedule[$i];
            } else {
                if(isset($this->data['form']['course_schedule'][$course_schedule[$i]['use_date']]) && !empty($this->data['form']['course_schedule'][$course_schedule[$i]['use_date']])){
                    for($j=0;$j<count($this->data['form']['course_schedule'][$course_schedule[$i]['use_date']]);$j++){
                        if($this->data['form']['course_schedule'][$course_schedule[$i]['use_date']][$j]['from_time'] == $course_schedule[$i]['from_time'] && $this->data['form']['course_schedule'][$course_schedule[$i]['use_date']][$j]['to_time'] == $course_schedule[$i]['to_time'] && $this->data['form']['course_schedule'][$course_schedule[$i]['use_date']][$j]['course_name'] == $course_schedule[$i]['course_name'] && $this->data['form']['course_schedule'][$course_schedule[$i]['use_date']][$j]['room_name'] == $course_schedule[$i]['room_name']){
                            $this->data['form']['course_schedule'][$course_schedule[$i]['use_date']][$j]['teacher_list'][] = $course_schedule[$i]['teacher_name'];
                            $this->data['form']['course_schedule'][$course_schedule[$i]['use_date']][$j]['teacher_id_list'][] = $course_schedule[$i]['teacher_id'];
                            $this->data['form']['course_schedule'][$course_schedule[$i]['use_date']][$j]['isteacher_list'][] = $course_schedule[$i]['isteacher'];
                            $this->data['form']['course_schedule'][$course_schedule[$i]['use_date']][$j]['title_list'][] = $course_schedule[$i]['title'];

                            $end_tag = true;
                            break;
                        }
                    } 
                }

                if($end_tag){
                    continue;
                } else {
                    $this->data['form']['course_schedule'][$course_schedule[$i]['use_date']][] = $course_schedule[$i];
                }
            }
        }

        $this->data['choices']['booking_date'] = $this->set_course_model->getBookingDate($this->data['form']['year'],$this->data['form']['class_no'],$this->data['form']['term']);
        $this->data['choices']['booking_date'][''] = '請選擇';

        $this->data['choices']['room_use_date'] = $this->set_course_model->getRoomUseDate($this->data['form']['year'],$this->data['form']['class_no'],$this->data['form']['term']);
        $this->data['choices']['room_use_date'][''] = '請選擇';

        $section_time_list = $this->set_course_model->getSectionTime();
        $volunteer_class_id = $this->volunteer_model->getVolunteerClassId($this->data['form']['year'],$this->data['form']['class_no'],$this->data['form']['term']);

        $this->data['form']['f1'] = $section_time_list[0]['remark'];
        $this->data['form']['f2'] = $section_time_list[1]['remark'];
        $this->data['form']['f3'] = $section_time_list[2]['remark'];
        $this->data['form']['f4'] = $section_time_list[3]['remark'];
        $this->data['form']['f5'] = $section_time_list[4]['remark'];

        $this->data['link_get_booking_room'] = base_url("create_class/set_course/getBookingRoom/");
        $this->data['link_get_room_use'] = base_url("create_class/set_course/getOldBookingRoom/");
        $this->data['link_get_course_time'] = base_url("create_class/set_course/getCourseTime/");
        $this->data['link_check_hours'] = base_url("create_class/set_course/chkHours/");

        if($volunteer_class_id > 0){
            $this->data['link_change'] = base_url("create_class/set_course/change/{$volunteer_class_id}");
        }
        
        $this->data['link_check_update'] = base_url("create_class/set_course/edit2/{$id}/?{$_SERVER['QUERY_STRING']}");
        $this->data['link_save_t'] = base_url("create_class/set_course/edit2/{$id}/?{$_SERVER['QUERY_STRING']}");
        $this->data['link_cancel'] = base_url("create_class/set_course/edit/{$id}/?{$_SERVER['QUERY_STRING']}");
        $this->layout->view('create_class/set_course/edit2', $this->data);
    }

    public function getEcpaClassName()
    {
        $ecpa_class_id = $this->input->post('ecpa_class_id');
        $data = $this->createclass_model->getEcpaClassName($ecpa_class_id);

        echo htmlspecialchars($data, ENT_HTML5|ENT_QUOTES);
    }

    public function getBookingRoom(){
        $year = $this->input->post('year');
        $class_no = $this->input->post('class_no');
        $term = $this->input->post('term');
        $booking_date = $this->input->post('booking_date');

        $data = $this->set_course_model->getBookingRoom($year,$class_no,$term,$booking_date);
        print_r(json_encode($data));
    }

    public function getOldBookingRoom(){
        $year = $this->input->post('year');
        $class_no = $this->input->post('class_no');
        $term = $this->input->post('term');
        $use_date = $this->input->post('use_date');

        $data = $this->set_course_model->getOldBookingRoom($year,$class_no,$term,$use_date);
        print_r(json_encode($data));
    }

    public function getCourseTime(){
        $year = $this->input->post('year');
        $class_no = $this->input->post('class_no');
        $term = $this->input->post('term');
        $course_date = $this->input->post('course_date');
        $class_description = $this->input->post('class_description');
        $f1 = $this->input->post('f1');
        $f2 = $this->input->post('f2');
        $f3 = $this->input->post('f3');
        $f4 = $this->input->post('f4');
        $f5 = $this->input->post('f5');

        $chk1    = "12:30";   //超過此時間從下午起
        $chk2    = "18:00";   //超過此時間從晚上起
        $ftMin   = 30;      //晨間為開始時間前多少分
        $result = array('from_time'=>'','to_time'=>'');
        if($class_description != '1'){
            $data = $this->set_course_model->getCourseTime($year,$class_no,$term,$course_date,$class_description);
            if(!empty($data)){
                $result['from_time'] = date("H:i",strtotime($data[0]['to_time']."+".$f5." min"));
                $result['to_time'] = date("H:i",strtotime($data[0]['to_time']."+".($f4+$f5)." min"));;
                
                if(strtotime($result['from_time']) < strtotime($f1)){
                    $result['from_time'] = '';
                } else if((strtotime($result['from_time']) > strtotime($chk1) && strtotime($result['from_time']) < strtotime($f2)) || (strtotime($result['to_time']) > strtotime($chk1) && strtotime($result['to_time']) < strtotime($f2))){
                    $result['from_time'] = $f2;
                } else if((strtotime($result['from_time']) > strtotime($chk2) && strtotime($result['from_time']) < strtotime($f3)) || (strtotime($result['to_time']) > strtotime($chk2)) && strtotime($result['to_time']) < strtotime($f3)){
                    $result['from_time'] = $f3;
                } else if(strtotime($result['from_time']) > strtotime('23:59')){
                    $result['from_time'] = '';
                }
               
                if((strtotime($result['from_time']) > strtotime($chk1) && strtotime($result['from_time']) < strtotime($f2)) || (strtotime($result['to_time']) > strtotime($chk1) && strtotime($result['to_time']) < strtotime($f2))){
                    $result['to_time'] = date("H:i",strtotime($f2."+".$f4." min"));
                } else if((strtotime($result['from_time']) > strtotime($chk2) && strtotime($result['from_time']) < strtotime($f3)) || (strtotime($result['to_time']) > strtotime($chk2)) && strtotime($result['to_time']) < strtotime($f3)){
                    $result['to_time'] = date("H:i",strtotime($f3."+".$f4." min"));
                } else if(strtotime($result['to_time']) > strtotime('23:59')){
                    $result['to_time'] = '';
                }
            } else {
                $result['from_time'] = $f1;
                $result['to_time'] = date("H:i",strtotime($f1."+".$f4." min")); 
            }
        } else {
            $chk_class_description = $this->set_course_model->checkClassDescription($year,$class_no,$term,$course_date);
            if($chk_class_description == '0'){
                $result['from_time'] = date("H:i",strtotime($f1."-".($f5+$ftMin)." min")); 
                $result['to_time'] = date("H:i",strtotime($f1."-".$f5." min")); 
            }
        }
       
        print_r(json_encode($result));
    }

    public function change($id){
        $change_date = $this->input->get('change_date');
        $change_room_id = $this->input->get('room_id');
        $change_from_time = $this->input->get('from_time');
        $change_to_time = $this->input->get('to_time');

        $vid = $this->input->post('vid');
        if($vid > 0 && !empty($change_date) && !empty($change_room_id) && !empty($change_from_time) && !empty($change_to_time)){
            $classroom_id = $this->volunteer_model->getClassRoomId($change_room_id);
            if($classroom_id < 0){
                $room_info = $this->set_course_model->getRoomInfo($change_room_id);
                $vcid = $this->volunteer_model->insertClassRoom($change_room_id,$room_info);
            } else {
                $vcid = $this->volunteer_model->getVcid($change_room_id);
            }

            $chk_update = $this->volunteer_model->updateVolunteerCalendar($vid,$vcid,$change_date,$change_from_time,$change_to_time);

            if($chk_update){
                $this->setAlert(1, '更新成功');
                redirect(base_url("create_class/set_course/change/{$id}/?{$_SERVER['QUERY_STRING']}"));
            } else {
                $this->setAlert(1, '更新失敗');
                redirect(base_url("create_class/set_course/change/{$id}/?{$_SERVER['QUERY_STRING']}"));
            }
        }

        $this->data['list'] = $this->volunteer_model->getCourseList($id);
        $this->data['get'] = $this->input->get();

        if(!empty($this->input->get('room_id'))){
            $this->data['room_name'] = $this->set_course_model->getRoomName($this->input->get('room_id'));
        } else {
            $this->data['room_name'] = '';
        }
        
        $this->data['link_save'] = base_url("create_class/set_course/change/{$id}/?{$_SERVER['QUERY_STRING']}");;
        $this->layout->view('create_class/set_course/change', $this->data);
    }

    public function copySchedulePre(){
        $this->data['class_no'] = $this->input->get('c');
        $this->layout->view('create_class/set_course/copyschedulepre',$this->data);
    }

    public function copySchedule(){
        $year = $this->input->get('y');
        $class_no = $this->input->get('c');
        $term = $this->input->get('t');
        $copyterm = $this->input->get('cct');
        $mode = $this->input->post('mode');
        $copyyear = $this->input->get('ccy');

        if(isset($mode) && $mode == "copy"){
            $this->db->trans_start();

            $list = $this->set_course_model->getCourseSchedule($copyyear,$class_no,$copyterm);
            
            $pass_date = array();
            $deny_date = array();
            for($i=0;$i<count($list);$i++){
                $condition=[];
                $condition['class_no'] = $class_no;
                $condition['year'] = $year;
                $condition['term'] = $term;

                $use_date_key = 'use_date_'.$list[$i]['use_date'];
                $room_key = 'room_'.$list[$i]['use_date'];
                if(!empty($this->input->post($use_date_key))){
                    $list[$i]['use_date'] = $this->input->post($use_date_key);
                    $condition['booking_date'] = $this->input->post($use_date_key);
                }

                if(!empty($this->input->post($room_key))){
                    $list[$i]['room_id'] = $this->input->post($room_key);
                    $condition['room_id'] = $this->input->post($room_key);
                }

                $condition['pre_start_time'] = substr($list[$i]['from_time'], 0, 2).':'.substr($list[$i]['from_time'], 2, 2);
                $condition['pre_end_time'] = substr($list[$i]['to_time'], 0, 2).':'.substr($list[$i]['to_time'], 2, 2);
                
                $room_use_status = $this->handouts_status_model->getBookRoom($condition);
                //檢查教室是否被其他班期預約使用
                if(!empty($room_use_status)){
                    $this->setAlert(4,"該時段已有班期預約使用");
                    redirect(base_url("create_class/set_course/copySchedule?{$_SERVER['QUERY_STRING']}"));
                    exit;
                }

                if($i != 0 && $list[$i]['use_date'] == $list[$i-1]['use_date'] && $list[$i]['to_time'] == $list[$i-1]['to_time'] && $list[$i]['from_time'] == $list[$i-1]['from_time'] && $list[$i]['room_id'] == $list[$i-1]['room_id'] && $list[$i]['use_id'] == $list[$i-1]['use_id'] && $list[$i]['teacher_id'] != $list[$i-1]['teacher_id']){
                    $room_red_status = $this->handouts_status_model->getUseRoomForEdit($condition);
                }  else {
                    $room_red_status = $this->handouts_status_model->getUseRoom($condition);
                }
                
                if(!empty($room_red_status)){
                    if(!in_array($list[$i]['use_date'],$deny_date)){
                        $deny_date[] = $list[$i]['use_date'];
                    }
                    continue;
                    // $this->setAlert(4,"該時段已有班期使用");
                    // redirect(base_url("create_class/set_course/copySchedule?{$_SERVER['QUERY_STRING']}"));
                    // exit;
                }

                $chk_appinfo = $this->set_course_model->checkRoomUseForAppinfo($list[$i]['use_date'],$list[$i]['room_id'],$list[$i]['from_time'],$list[$i]['to_time']);
                if(!empty($chk_appinfo)){
                    $this->setAlert(4,"該時段已有被申請使用");
                    redirect(base_url("create_class/set_course/copySchedule?{$_SERVER['QUERY_STRING']}"));
                    exit;
                }
                
                $check_periods = $this->set_course_model->checkPeriodtimeNew($year,$class_no,$term,($list[$i]['use_date'].' 00:00:00'),$list[$i]['room_id'],$list[$i]['from_time'],$list[$i]['to_time']);

                if(!$check_periods){
                    $this->set_course_model->insertPeriodtime($year,$class_no,$term,($list[$i]['use_date'].' 00:00:00'),$list[$i]['room_id'],$list[$i]['use_id'],$list[$i]['from_time'],$list[$i]['to_time'],$list[$i]['pno'],$list[$i]['pidnm']);
                } 
                // else {
                //     $checkCourseScheduleExist = $this->set_course_model->checkCourseScheduleExist($year, $class_no, $term, $list[$i]['use_date'], $list[$i]['room_id'], $list[$i]['use_id'], $list[$i]['teacher_id'], $list[$i]['use_period']);

                //     if($checkCourseScheduleExist){
                //         if(!in_array($list[$i]['use_date'],$deny_date)){
                //             $deny_date[] = $list[$i]['use_date'];
                //         }
                //         continue;
                //     } 
                // }
                
                $this->set_course_model->insertRoomuse($year,$class_no,$term,($list[$i]['use_date'].' 00:00:00'),$list[$i]['room_id'],$list[$i]['use_id'],$list[$i]['use_period'],$list[$i]['hrs'],$list[$i]['teacher_id'],$list[$i]['title'],$list[$i]['sort'],$list[$i]['isteacher']);


                $chk_hour_traffic = $this->set_course_model->chkHourTrafficTax($year,$class_no,$term,($list[$i]['use_date'].' 00:00:00'));

                if(!$chk_hour_traffic){
                    $this->set_course_model->insertHourTrafficTax($year,$class_no,$term,($list[$i]['use_date'].' 00:00:00'));
                    $this->set_course_model->updateHourTrafficTax($year,$class_no,$term,($list[$i]['use_date'].' 00:00:00'));
                } 

                $this->set_course_model->insertDiningTeacher($year,$class_no,$term,($list[$i]['use_date'].' 00:00:00'),$this->flags->user['username']);
                $this->set_course_model->insertDiningStudent($year,$class_no,$term,($list[$i]['use_date'].' 00:00:00'),$this->flags->user['username']);
            }

            $this->set_course_model->updateRequireStartEndDate($year,$class_no,$term);
            $this->set_course_model->updateRequireSeason($year,$class_no,$term);
            $this->set_course_model->updateRequireRoom($year,$class_no,$term);
            $this->set_course_model->insertCourseTeacher($year,$class_no,$term,$this->flags->user['username']);
            
            $this->db->trans_complete();

            if($this->db->trans_status() === TRUE){
                if(count($deny_date) > 0){
                    $deny_date_list = '';
                    for($i=0;$i<count($deny_date);$i++){
                        if($i == count($deny_date)-1){
                            $deny_date_list .= $deny_date[$i];
                        } else {
                            $deny_date_list .= $deny_date[$i].'、';
                        }
                    }
                    $message = $deny_date_list.'有重複時段課表請注意！其餘未重複課表複製成功！';
                } else {
                    $message = '課表複製完成';
                }

                echo '<script>';
                echo 'alert("'.$message.'");';
                echo 'window.close();';
                echo 'window.onunload = function(){ 
                        window.opener.location.reload(); 
                    }';
                echo '</script>';
                exit;
            } 
        }

        $this->data['date_list'] = $this->set_course_model->getCopyScheduleDate($copyyear,$class_no,$copyterm);
        
        $room_list_tmp = $this->set_course_model->getCopyScheduleRoom();

        $tmp_array = array();
        for($i=0;$i<count($room_list_tmp);$i++){
            if(substr($room_list_tmp[$i]['room_id'], 0, 1) == 'B' || substr($room_list_tmp[$i]['room_id'], 0, 1) == 'C' || substr($room_list_tmp[$i]['room_id'], 0, 1) == 'E'){
                array_push($tmp_array, $room_list_tmp[$i]);
                unset($room_list_tmp[$i]);
            }
        }

        asort($tmp_array);

        $this->data['room_list'] = array_merge($tmp_array,$room_list_tmp);
    
        $this->layout->view('create_class/set_course/copyschedule', $this->data);
    }

    public function chkHours(){
        $year = $this->input->post('year');
        $class_no = $this->input->post('class_no');
        $term = $this->input->post('term');

        $data['status'] = $this->set_course_model->chkHours($year,$class_no,$term);

        print_r(json_encode($data));
    }

    public function chkHoursNew($year,$class_no,$term){
        $data = $this->set_course_model->chkHoursNew($year,$class_no,$term);

        return $data;
    }

    public function exportExcel($qid){
        if($qid > 0){
            $preq_result_data = $this->set_course_model->getPreqResult($qid);
        }

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
        $sheet->setTitle('preQ_result');

        // 合併儲存格
        $sheet->mergeCells('A1:J1');
        $objPHPExcel->getActiveSheet()->setCellValue("A1", "課前問卷調查結果一覽表");
        $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $objPHPExcel->getActiveSheet()->setCellValue("A2", "年度");
        $objPHPExcel->getActiveSheet()->setCellValue("B2", "期別");
        $objPHPExcel->getActiveSheet()->setCellValue("C2", "班期代號");
        $objPHPExcel->getActiveSheet()->setCellValue("D2", "班期名稱");
        $objPHPExcel->getActiveSheet()->setCellValue("E2", "學號");
        $objPHPExcel->getActiveSheet()->setCellValue("F2", "姓名");
        $objPHPExcel->getActiveSheet()->setCellValue("G2", "題目");
        $objPHPExcel->getActiveSheet()->setCellValue("H2", "學員答覆內容");
        $objPHPExcel->getActiveSheet()->setCellValue("I2", "建立日期");
        $objPHPExcel->getActiveSheet()->setCellValue("J2", "修改日期");

        $row = 3;

        for($i=0;$i<count($preq_result_data);$i++){
            $objPHPExcel->getActiveSheet()->setCellValue("A".$row, $preq_result_data[$i]['year']);
            $objPHPExcel->getActiveSheet()->setCellValue("B".$row, $preq_result_data[$i]['term']);
            $objPHPExcel->getActiveSheet()->setCellValue("C".$row, $preq_result_data[$i]['class_no']);
            $objPHPExcel->getActiveSheet()->setCellValue("D".$row, $preq_result_data[$i]['class_name']);
            $objPHPExcel->getActiveSheet()->setCellValue("E".$row, $preq_result_data[$i]['sno']);
            $objPHPExcel->getActiveSheet()->setCellValue("F".$row, $preq_result_data[$i]['first_name']);
            $objPHPExcel->getActiveSheet()->setCellValue("G".$row, $preq_result_data[$i]['item_title']);
            $objPHPExcel->getActiveSheet()->setCellValue("H".$row, nl2br($preq_result_data[$i]['content']));
            $objPHPExcel->getActiveSheet()->setCellValue("I".$row, date('Y-m-d',strtotime($preq_result_data[$i]['create_date'])));
            if (!empty($preq_result_data[$i]['update_date'])){
                $objPHPExcel->getActiveSheet()->setCellValue("J".$row, date('Y-m-d',strtotime($preq_result_data[$i]['update_date'])));
            }
            $row++;
        }

        $objPHPExcel->getActiveSheet()->getStyle('A1:J'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');

        header('Content-Type:application/csv;charset=UTF-8');
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
        header("Content-Type:application/force-download");
        header("Content-Type:application/vnd.ms-excel;");
        header("Content-Type:application/octet-stream");
        header('Content-Disposition: attachment;filename="preQ_result.xls"');
        header("Content-Transfer-Encoding:binary");
        $objWriter->save('php://output');

        exit;
    }

    private function _isVerify($action='add', $class_attribute)
    {
        $config = $this->set_course_model->getVerifyConfig($class_attribute);

        $this->form_validation->set_rules($config);
        $this->form_validation->set_error_delimiters('<p class="text-danger">', '</p>');
        // $this->form_validation->set_message('required', '請勿空白');

        return ($this->form_validation->run() == FALSE)? FALSE : TRUE;
    }

    /*public function ajax()
    {
        $sql = sprintf("SELECT
                              `require`.class_name,reservation_time.start_time,reservation_time.end_time
                            FROM
                              booking_place
                            JOIN `require` ON `require`.year = booking_place.year
                            AND `require`.class_no = booking_place.class_no
                            AND `require`.term = booking_place.term
                            AND `require`.class_status IN (2, 3)
                            JOIN reservation_time ON reservation_time.item_id = booking_place.booking_period
                            WHERE
                              booking_place.booking_date = '%s'
                            AND booking_place.room_id = '%s'
                            AND (
                              `require`.is_cancel != '1'
                              OR `require`.is_cancel IS NULL
                            ) order by reservation_time.start_time",$course_date,$list[$i]['room_id']);
    }*/
}
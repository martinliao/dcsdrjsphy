<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Class_record extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        if ($this->flags->is_login === FALSE) {
            redirect(base_url('welcome'));
        }        
        $this->load->model('student/class_record_model');
        $this->load->model('management/certificate_user_list_model'); //2021-07-15
        if (!isset($this->data['filter']['query_year'])) {
            $this->data['filter']['query_year'] = date('Y')-1911;
        }
        if (!isset($this->data['filter']['query_class_no'])) {
            $this->data['filter']['query_class_no'] = '';
        }
        if (!isset($this->data['filter']['query_class_name'])) {
            $this->data['filter']['query_class_name'] = '';
        }
        if (!isset($this->data['filter']['query_student_name'])) {
            $this->data['filter']['query_student_name'] = '';
        }
        if (!isset($this->data['filter']['query_bureau_name'])) {
            $this->data['filter']['query_bureau_name'] = '';
        }
        if (!isset($this->data['filter']['query_idno'])) {
            $this->data['filter']['query_idno'] = '';
        }

    }

    public function index()
    {
        $this->data['cer_user_list']=$this->certificate_user_list_model->get_user_cer_by_idno($this->flags->user['idno']); //2021-07-15
        $this->data['userOtherCert'] = $this->certificate_user_list_model->getUserOtherCert($this->flags->user['idno']);
        $this->data['uid'] = $this->flags->user['id'];
        $enter_id_number=$this->flags->user['idno'];
        $username=$this->flags->user['username'];
        $name=$this->flags->user['name'];
        $now = date('Y/m/d H:i');
        

        if($this->data['filter']['query_idno'] !== ''||$this->data['filter']['query_student_name'] !== ''||$this->data['filter']['query_class_no'] !== ''||$this->data['filter']['query_class_name'] !== ''){
            $idno=$this->data['filter']['query_idno'];
            $student_name=$this->data['filter']['query_student_name'];
            $class_no=$this->data['filter']['query_class_no'];
            $class_name=$this->data['filter']['query_class_name'];
            $year=$this->data['filter']['query_year'];
            $this->data['list']=$this->class_record_model->getStudentCourseInformation_byAns($idno,$student_name,$year,$class_no,$class_name);
        }else{
            $this->data['list']=$this->class_record_model->getStudentCourseInformation($enter_id_number,$name);
        }

        $this->data['check_id']=$this->getCheckId($username);
        $this->data['enter_id_number']=$enter_id_number;
        $this->data['now']=$now;
        $this->data['link_export']= base_url("student/class_record/export/");
        $this->data['link_refresh'] = base_url("student/class_record");
        $this->layout->view('student/class_record/list',$this->data);
    }

    public function export()
    {
        $enter_id_number=$this->flags->user['idno'];
        $username=$this->flags->user['username'];
        $name=$this->flags->user['name'];

        $info=$this->class_record_model->getStudentCourseInfo1($enter_id_number,$name);
        //dd($info);

            header("Content-type: application/csv");
            header("Content-Disposition: attachment; filename=student_list.csv");
            header("Pragma: no-cache");
            header("Expires: 0");
            $filename = 'student_list.csv';
            echo iconv("UTF-8", "BIG5", "臺北市政府公務人員訓練處 單一學員上課紀錄查詢\r\n");
            echo iconv("UTF-8", "BIG5", "編號,");
            echo iconv("UTF-8", "BIG5", "身分證字號,");
            echo iconv("UTF-8", "BIG5", "姓名,");
            echo iconv("UTF-8", "BIG5", "年度/班期名稱/期別,");
            echo iconv("UTF-8", "BIG5", "職稱,");
            echo iconv("UTF-8", "BIG5", "報名單位,");
            echo iconv("UTF-8", "BIG5", "教室(課程表),");            
            echo iconv("UTF-8", "BIG5", "開課日期　　　　,\r\n");
            

            $k=1;
            for ($i=0;$i<count($info);$i++) {

                echo "\"".iconv("UTF-8", "BIG5", $k."\",");
                echo "\"".iconv("UTF-8", "BIG5", $info[$i]['id']."\",");
                echo "\"".iconv("UTF-8", "BIG5", $info[$i]['pname']."\",");
                $des=$info[$i]['year']."年".$info[$i]['class_name'].'(第'.$info[$i]['term'].'期)';
                echo "\"".iconv("UTF-8", "BIG5", $des."\",");
                echo "\"".iconv("UTF-8", "BIG5", $info[$i]['name']."\",");
                echo "\"".iconv("UTF-8", "BIG5", $info[$i]['unit_name']."\",");
                echo "\"".iconv("UTF-8", "BIG5", $info[$i]['room_code']."\",");
                echo "\"".iconv("UTF-8", "BIG5", substr($info[$i]['start_date1'],0,10)."\"\r\n");
       
               $k++;
            }
            
    }
    

    public function getCheckId($username)
    {
        $this->db->select('account_role.role_id');
        $this->db->join('account_role','account_role.username = BS_user.username')
                    ->join('role_right as rr','rr.fun_id = "students_transaction_bureaus" and rr.role_id=account_role.role_id');
        $this->db->where('BS_user.username',$username);
        $query=$this->db->get('BS_user');
        $result=$query->result_array();
        $check_id=1;
        if(!empty($result)){
            $check_id=$result[0]['role_id'];
        }
        
        return $check_id;
    }

    public function modify_upload($seq){
        $files = $_FILES['files'];

        $info = $this->class_record_model->getRequireInfo($seq, $this->flags->user['id']);

        if(intval($seq) == 0 || empty($info)){
            die('無此班期');
        }

        if(!empty($files) && intval($seq) > 0){
            $expensions = array("jpg","png","pdf");
            $exploded   = explode('.',$files['name']);
			$file_ext   = strtolower(end($exploded));
            $upload_type = false;
            $upload_size = false;
			if(in_array($file_ext,$expensions)=== false){
				echo '<script>';
				echo 'alert("只能上傳圖片");';
		        echo '</script>';
		    } else {    
                $upload_type = true;
            }

		    $file_size = $files['size'];
		    if($file_size > 10000000) {
		    	echo '<script>';
				echo 'alert("檔案大小不能超過10MB");';
		        echo '</script>';
		    } else {
                $upload_size = true;
            }

            if($upload_type && $upload_size && is_uploaded_file($files['tmp_name'])){
                //$upload_path = '/var/www/html/base/admin/files/upload_modify/';
                $file_store_path = DIR_MODIFY.time().'_'.intval($seq).'_'.$this->flags->user['id'].'.'.$file_ext;
				if(move_uploaded_file($files['tmp_name'],$file_store_path)){
                    $status = false;
                    $check = $this->class_record_model->checkModifyTableUploadExist($seq, $this->flags->user['id']);

                    if($check){
                        $status = $this->class_record_model->uploadModifyTableUploadExist($seq, $this->flags->user['id'], $files['name'], $file_store_path);
                    } else {
                        $status = $this->class_record_model->insertModifyTableUploadExist($seq, $this->flags->user['id'], $files['name'], $file_store_path);
                    }

                    if($status){
                        $mail = $this->class_record_model->getWorkerEmail($info[0]['worker']);

                        if(!empty($mail)){
                            $message = '親愛的承辦人：<br><br>'.$info[0]['year'].'年度'.$info[0]['class_name'].'第'.$info[0]['term'].'期 學號：'.$info[0]['st_no'].' '.$info[0]['name'].'已上傳異動表如附檔，請查收。';
                            $this->load->library('email');
                            $this->email->from('pstc_member@gov.taipei', '臺北市政府公務人員訓練處');
                            $this->email->subject('學員異動表上傳通知');
                            $this->email->message($message);
                            $this->email->attach($file_store_path, '', $files['name']);
                            $this->email->to($mail);
                            $this->email->send();
                        }

                        echo '<script>';
                        echo 'alert("上傳成功");';
                        echo 'window.close();';
                        echo '</script>';
                    }
                }
            }
        }

        $this->data['title'] = $info[0]['year'].'年度'.$info[0]['class_name'].'第'.$info[0]['term'].'期';
        $this->layout->view('student/class_record/modify_upload',$this->data);
    }

    public function download($seq)
    {
        $info = $this->class_record_model->getModifyTableUploadInfo($seq, $this->flags->user['id']);

        if(!empty($info)){
            $path = DIR_MODIFY.''.basename($info[0]['path']); 

            if(!is_file($path)||!is_readable($path)){
                die("檔案無法讀取");
            }		   

            $file_name = preg_replace('/^.+[\\\\\\/]/', '', $info[0]['filename']) ;
            
            header("Content-Type: application/octet-stream");
            header("Content-Disposition: attachment; filename={$file_name}");
            header("Content-Transfer-Encoding:binary");
            header("Expires:0");
            header("Content-Length:"+filesize($path));
            ob_end_clean();
            readfile($path);
                    
            exit;
        }
    }

}

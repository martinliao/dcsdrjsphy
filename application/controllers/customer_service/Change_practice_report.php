<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Change_practice_report extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model(["require_model", "online_app_model", "send_mail_file_model", "mail_log_model"]);
        $this->load->model('create_class/progress_model');
        if(!isset($this->data['filter']['year'])){
            $this->data['filter']['year']=date('Y')-1911;
        }
        if(!isset($this->data['filter']['class_no'])){
            $this->data['filter']['class_no']='';
        }
        if(!isset($this->data['filter']['class_name'])){
            $this->data['filter']['class_name']='';
        }
    }

    public function index()
    {

        $condition = $this->getFilterData(['year', 'class_no', 'class_name']);
        $this->data['requires'] = $this->require_model->getList($condition);
        $this->data['link_refresh'] = base_url("customer_service/change_practice_report/");
        $this->layout->view('customer_service/change_practice_report/list',$this->data);
    }
    public function detail()
    {
        $class_info = $this->getFilterData(['year', 'class_no', 'term']);
        if ($post = $this->input->post()){
            if (!empty($post['mail'])){
                $this->mailtoHA($post['mail'], $class_info);
            }
        }
        
        $this->data['require'] = $this->require_model->find($class_info);
        $this->data['has'] = $this->online_app_model->getHA($class_info);
        $this->data['link_refresh'] = base_url("customer_service/change_practice_report/");
        $this->layout->view('customer_service/change_practice_report/detail',$this->data);
    }

    public function mailtoHA($emails, $class_info)
    {
        //$emails = array(0=>'wei90473@yahoo.com.tw',1=>'wei90473@gmail.com');
        //$emails = ['blin9533@gmail.com'];
        $require = $this->require_model->find($class_info);
        if (!empty($require)){
        // 取得調訓附加檔案
            $files = $this->send_mail_file_model->getList($class_info);
            // if (!empty($files)){
            //     dd($files, false);
            // }

			foreach($files as $key => $file){
				if (file_exists("./".$file->file_path)){
					$this->email->attach("./".$file->file_path);	
				}
            }
            // 取得該班期上次發送的信件內容
            $last_mail = $this->mail_log_model->find($class_info,3);

            $user_list = $this->progress_model->getCourseUserList(3, $class_info);
            if(!empty($user_list) && $mail_log->chk_cre_date > 1604246400){
                $this->load->helper("progress");
                $user_list_table = getUserList($user_list);
                $last_mail->body = $last_mail->body2.$user_list_table;
            }

			$this->load->library('email');

			$this->email->from('from@elearning.taipei', '臺北e大');
			$this->email->to($emails);
			$this->email->subject($last_mail->subject);
            $this->email->message($last_mail->body);
            if ($this->email->send()){
                echo '發送成功<br>';
                echo '<button class="btn" onclick="history.back(-1)">返回</button>';
                die("");

            }else{
                echo '發送失敗<br>';
                echo '<button class="btn" onclick="history.back(-1)">返回</button>';
                die("");
            }
        }
    }
}

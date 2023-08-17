<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Hourfee_mail_log extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('pay/Lecture_money_search_model');

        if (!isset($this->data['filter']['page'])) {
            $this->data['filter']['page'] = '1';
        }
    }

    public function index()
    {   
        $post = $this->input->post('resend');
        if(!empty($post) && count($post) > 0){
            for($i=0;$i<count($post);$i++){
                $hourfee_detail = array();
                $hourfee_detail = $this->Lecture_money_search_model->getHourFeeMailDetail($post[$i]);
                
                if(!empty($hourfee_detail)){
                    $content = '敬愛的老師 您好，<br><br>';
                    $content .= '感謝您參與指導臺北市政府公訓處教育訓練課程，<br>';
                    $content .= '講座授課鐘點費已撥匯入帳，敬請老師撥冗查閱帳戶，感謝您！<br><br>';
                    $content .= '上課日期：民國'.(substr($hourfee_detail[0]['use_date'], 0, 4)-1911).'年'.substr($hourfee_detail[0]['use_date'], 5, 2).'月'.substr($hourfee_detail[0]['use_date'], 8, 2).'日<br>';
                    $content .= '班期名稱：'.$hourfee_detail[0]['year'].'年度 '.$hourfee_detail[0]['class_name'].' 第'.$hourfee_detail[0]['term'].'期<br>';
                    $content .= '入帳日期：民國'.(substr($hourfee_detail[0]['entry_date'], 0, 4)-1911).'年'.substr($hourfee_detail[0]['entry_date'], 5, 2).'月'.substr($hourfee_detail[0]['entry_date'], 8, 2).'日<br>';
                    $content .= '金額：'.$hourfee_detail[0]['subtotal'].'元整<br><br>';

                    $content .= '公務人員訓練處 教務組<br>';
                    $content .= '客服中心電話：29320212分機341<br><br>';
                    $content .= '<font style="color:red">《溫馨提醒》入帳日期因受匯款作業時間影響，可能有1天落差，如實際入帳日期與本通知日期未盡相符，尚祈見諒，如您對以上通知有任何問題，請通知本處承辦同仁詢問(2932-0212)。謝謝！</font><br>';
                    $content .= '<font style="color:red">本信件為系統自動發送，請不要直接回覆</font>';
                    $content .= '<img style="display:none" src="https://dcsdcourse.taipei.gov.tw/base/api/trace.php?id='.intval($post[$i]).'">';

                    if($this->ValidateEmailAddress($hourfee_detail[0]['email'])){
                        $send_status = $this->sendHourFeeMail($hourfee_detail[0]['email'], $content);

                        if($send_status){
                            $this->Lecture_money_search_model->updateHourFeeMailErronInfo($post[$i], null);
                        } else {
                            $this->Lecture_money_search_model->updateHourFeeMailErronInfo($post[$i], '寄送失敗');
                        }
                    } else {
                        $this->Lecture_money_search_model->updateHourFeeMailErronInfo($post[$i], 'mail不正確');
                    }
                }
            }
        }

        $teacher = isset($_GET['nteacher'])?$_GET['nteacher']:"";
        $id = isset($_GET['nid'])?$_GET['nid']:"";
        $start = isset($_GET['nstart'])?$_GET['nstart']:"";
        $end = isset($_GET['nend'])?$_GET['nend']:"";
        $perpage = isset($_GET['nperpage'])?$_GET['nperpage']:"";

        $this->data['sess_nteacher'] = $teacher;
        $this->data['sess_nid'] = $id;
        $this->data['sess_nstart'] = $start;
        $this->data['sess_nend'] = $end;
        $this->data['sess_nperpage'] = $perpage;

        $page = $this->data['filter']['page'];
        $rows = $this->data['filter']['rows'];
        $this->data['filter']['rows'] = $this->data['filter']['rows']==10?$this->data['filter']['rows']:10;

        if($start !="" && $end != ""){
            $this->data['datas'] = $this->Lecture_money_search_model->getHourFeeTax($teacher, $id, $start, $end, $perpage);
            

        }
        else {
            $this->data['datas'] = array();
        }

        $conditions = array();

        $attrs = array(
            'conditions' => $conditions,
        );

        $this->data['filter']['total'] = $total = count($this->data['datas']);
        $this->data['filter']['offset'] = $offset = ($page -1) * $rows;

        if($total > 0) {
            $this->data['datas'] = $this->Lecture_money_search_model->getHourFeeTax($teacher, $id, $start, $end, $perpage, $rows, $offset);
        }

        $attrs = array(
            'conditions' => $conditions,
            'rows' => $rows,
            'offset' => $offset,
        );

        $this->load->library('pagination');
        $config['base_url'] = base_url("pay/hourfee_mail_log?". $this->getQueryString(array(), array('page')));
        $config['total_rows'] = $total;
        $config['per_page'] = $rows;
        $this->pagination->initialize($config);

        $this->data['link_refresh'] = base_url("pay/hourfee_mail_log/");
        $this->layout->view('pay/hourfee_mail_log/list',$this->data);
    }

    private function sendHourFeeMail($mail, $content)
    {
        $body = stripslashes($content);

        $this->load->library('email');
        $this->email->from('pstc_member@gov.taipei', '臺北市政府公務人員訓練處');
        $this->email->to($mail);
        $this->email->subject('臺北市政府公務人員訓練處-鐘點費入帳通知');
        $this->email->message($body);

        if($this->email->send()){
            return true;
        }
        
        return false;
    }

    private function ValidateEmailAddress($email) {
        return (preg_match("|^[-_.0-9a-z]+@([-_0-9a-z][-_0-9a-z]+\.)+[a-z]{2,3}$|i",$email));
    }

}

<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Point_send extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        if ($this->flags->is_login === FALSE) {
            redirect(base_url('welcome'));
        }

        $this->load->model('management/point_send_model');
        $this->load->model('management/point_create_model');

        if (!isset($this->data['filter']['page'])) {
            $this->data['filter']['page'] = '1';
        }
        if (!isset($this->data['filter']['class_name'])) {
            $this->data['filter']['class_name'] = '';
        }
        if (!isset($this->data['filter']['class_no'])) {
            $this->data['filter']['class_no'] = '';
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
        $this->data['page_name'] = 'list';

        $page = $this->data['filter']['page'];
        $rows = $this->data['filter']['rows'];

        $conditions = array();

        $conditions['year'] = $this->data['filter']['year'];

        if(!in_array("1", $this->flags->user['group_id'])){
            if(in_array("8", $this->flags->user['group_id'])){
                $conditions['worker'] = $this->flags->user['idno'];
            }
        }

        $attrs = array(
            'conditions' => $conditions,
        );
        if ($this->data['filter']['class_name'] !== '' ) {
            $attrs['class_name'] = $this->data['filter']['class_name'];
        }
        if ($this->data['filter']['class_no'] !== '' ) {
            $attrs['class_no'] = $this->data['filter']['class_no'];
        }
        $attrs['where_special'] = " class_status in ('2','3') and IFNULL(is_cancel, '0') = '0' ";
        $this->data['filter']['total'] = $total = $this->point_send_model->getListCount($attrs);
        $this->data['filter']['offset'] = $offset = ($page -1) * $rows;

        $attrs = array(
            'conditions' => $conditions,
            'rows' => $rows,
            'offset' => $offset,
        );
        if ($this->data['filter']['class_name'] !== '' ) {
            $attrs['class_name'] = $this->data['filter']['class_name'];
        }
        if ($this->data['filter']['class_no'] !== '' ) {
            $attrs['class_no'] = $this->data['filter']['class_no'];
        }
        $attrs['where_special'] = " class_status in ('2','3') and IFNULL(is_cancel, '0') = '0' ";
        $this->data['list'] = $this->point_send_model->getList($attrs);
        foreach ($this->data['list'] as & $row) {
            $row['url'] = base_url("management/point_send/detail/{$row['seq_no']}/?{$_SERVER['QUERY_STRING']}");
        }
        $this->load->library('pagination');
        $config['base_url'] = base_url("management/point_send?". $this->getQueryString(array(), array('page')));
        $config['total_rows'] = $total;
        $config['per_page'] = $rows;
        $this->pagination->initialize($config);

        $this->data['link_refresh'] = base_url("management/point_send/");
        $this->layout->view('management/point_send/list',$this->data);
    }

    public function detail($seq_no)
    {
        $require_data = $this->point_send_model->get($seq_no);

        if(empty($require_data)){
            $this->setAlert(3, '操作錯誤');
            redirect(base_url('management/point_send/'));
        }

        $this->data['require_data'] = $require_data;

        $this->data['list'] = $this->point_create_model->getScoreInfoByPkey($require_data['year'], $require_data['class_no'], $require_data['term']);

        $this->data['link_cancel'] = base_url("management/point_send/?{$_SERVER['QUERY_STRING']}");
        $this->data['send_url'] = base_url("management/point_send/send_score_mail/?{$_SERVER['QUERY_STRING']}");
        $this->layout->view('management/point_send/detail',$this->data);
    }

    public function send_score_mail()
    {
        if($post = $this->input->post()){

            $require_data = $this->point_send_model->get(addslashes($post['seq_no']));
            $remark = isset($post['remark']) ? htmlspecialchars(addslashes($post['remark']), ENT_HTML5|ENT_QUOTES) : null;

            if(empty($require_data) ){
                $this->setAlert(3, '操作錯誤');
                redirect(base_url('management/point_send/'));
            }
            $this->data['require_data'] = $require_data;
            $model = $this->point_create_model->getScoreInfoByPkey($require_data['year'], $require_data['class_no'], $require_data['term'], false, $post['chk']);
            $list = array();
            $this->data['mailFlag'] = '3';
            $point_send_msg = '';
            foreach ($model as $arr) {
                $endDate = substr($arr['end_date1'], 0, 4).'年'.substr($arr['end_date1'], 5, 2).'月'.substr($arr['end_date1'], 8, 2);
                $startDate = substr($arr['start_date1'], 0, 4).'年'.substr($arr['start_date1'], 5, 2).'月'.substr($arr['start_date1'], 8, 2);
                $context = array(
                    'year' => $arr['year'],
                    'class_no' => $arr['class_no'],
                    'term' => $arr['term'],
                    'class_name' => $arr['class_name'],
                    'name' => $arr['name'],
                    'final_score' => $arr['final_score'],
                    'pscore' => $arr['p_score'],
                    'st_no' => $arr['st_no'],
                    'title' => $arr['title_name'],
                    'bureau' => $arr['beaurau_name'],
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'contactor' => $arr['boss_name'],
                    'tel' => $arr['boss_tel'],
                    'notpass_desc' => $arr['notpass_desc'],
                    'remark' => $remark
                );

                $bod_con = $this->load->view('management/point_send/8F', $context, TRUE);

                if (isset($post['status']) && $post['status']==='send') {
                    $title = '學員成績寄送通知!';
                    $subject = sprintf('公訓處%s年%s第%s期（個人）總成績', $require_data['year'], $require_data['class_name'], $require_data['term']);
                    $recipient = $arr['email'];

                    if(!empty($recipient)){
                        $result = $this->_sent_mail($recipient, $subject, $bod_con);
                        if (is_string($result)) {
                            //失敗顯示訊息
                            $point_send_msg .= $result;
                            $this->data['mailFlag'] = '1';
                        } else {

                            $point_send_msg .= 'Email 發送成功('.$arr['email'].')<br />';
                            $this->data['mailFlag'] = '0';
                        }
                    }else{
                        $point_send_msg .= 'Email 發送失敗 ('.$arr['email'].')<br />';
                        $this->data['mailFlag'] = '1';
                    }

                    
                        // 寄送公司信箱
                        $recipient = $arr['office_email'];

                        if(!empty($recipient)){
                            $result = $this->_sent_mail($recipient, $subject, $bod_con);
                            if (is_string($result)) {
                                //失敗顯示訊息
                                $point_send_msg .= $result;
                                $this->data['mailFlag'] = '1';
                            } else {

                                $point_send_msg .= 'Email 發送成功('.$recipient.')<br />';
                                $this->data['mailFlag'] = '0';
                            }
                        }else{
                            $point_send_msg .= 'Email 發送失敗 ('.$recipient.')<br />';
                            $this->data['mailFlag'] = '1';
                        }                        
                    // }


                } else {
                    // 準備要在頁面呈現的model
                    $list[$arr['id']] = array (
                        'body' => $bod_con,
                        'email' => $arr['email'],
                        'office_email' => $arr['office_email'],
                        'id' => $arr['id'],
                        'remark' => $remark
                    );
                }
            }
            $this->data['list'] = $list;
            $this->data['point_send_msg'] = $point_send_msg;
            // jd($model,1);
        }else{
            $this->setAlert(3, '操作錯誤');
            redirect(base_url('management/point_send/'));
        }
        $this->data['link_cancel'] = base_url("management/point_send/?{$_SERVER['QUERY_STRING']}");
        $this->layout->view('management/point_send/send_score_mail',$this->data);
    }

    public function _sent_mail($recipient, $subject, $message)
    {
        $recipients = explode(',', $recipient);
        $this->email->from('pstc_apdd@mail.taipei.gov.tw', '臺北市政府公務人員訓練處');
        $this->email->to($recipients);
        $this->email->subject($subject);
        $this->email->message($message);
        $rs = $this->email->send();
        if ($rs) {
            return TRUE;
        }else{
            return 'Email 發送失敗 ('.$recipients.')<br />';
        }

    }

}

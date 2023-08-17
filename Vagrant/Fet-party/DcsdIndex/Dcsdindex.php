<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Dcsdindex extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        //if ($this->flags->is_login === FALSE) {
        //    redirect(base_url('welcome'));
        //}
        $this->load->library('smarty_acl');
        if (!$this->smarty_acl->logged_in(FALSE)) {
            return redirect(base_url('/Auth/login'));
        }

        $this->load->model('dcsdindex_model');
        $this->load->model('create_class/course_sch_model');
    }

    public function index()
    {
        $this->data['page_name'] = 'index';

        $appseqArr = $this->dcsdindex_model->checkNotSendPay();
        $username = $this->flags->user['username'];
        //debugBreak();
        $today = date("Y-m-d", strtotime("-2 day"));
        if (!empty($appseqArr)) {
            for ($i = 0; $i < count($appseqArr); $i++) {
                $appseqArr[$i]['use_date'] = date("Y-m-d", strtotime($appseqArr[$i]['use_date']));
                $month = date("m", strtotime($appseqArr[$i]['use_date']));
                $day = date("d", strtotime($appseqArr[$i]['use_date']));
                if ($username == $appseqArr[$i]['account'] && $appseqArr[$i]['use_date'] == $today) {
                    $str = '系統提醒！請款單據尚未送出\n\n' . (string)$appseqArr[$i]['year'] . '年' . (string)$appseqArr[$i]['class_name'] . '第' . (string)$appseqArr[$i]['term'] . '期' . '\n上課日期：' . (string)$month . '月' . (string)$day . '日' . '\n承辦人：' . (string)$appseqArr[$i]['name'];
                    echo "<script>alert('$str');</script>";
                } elseif (($username == 'w8881026' || $username == 'N220613829' || $username == 'avalee01' || $username == 'eda_2603285' || $username == 'eventseng' || $username == 'pstc0304' || $username == 'pif999' || $username == $appseqArr[$i]['account']) && $appseqArr[$i]['use_date'] < $today) {

                    $str = '系統提醒！請款單據尚未送出\n\n' . (string)$appseqArr[$i]['year'] . '年' . (string)$appseqArr[$i]['class_name'] . '第' . (string)$appseqArr[$i]['term'] . '期' . '\n上課日期：' . (string)$month . '月' . (string)$day . '日' . '\n承辦人：' . (string)$appseqArr[$i]['name'];
                    echo "<script>alert('$str');</script>";
                }
            }
        }
        //20210623 加入簽核通知
        $user_idno = $this->course_sch_model->user_idno($username);
        $_SESSION['user_idno'] = $user_idno[0]['idno'];
        $boss_no = $user_idno[0]['idno'];
        $leader_no = $user_idno[0]['idno'];
        $boss_count = $this->course_sch_model->get_boss_Count($boss_no);
        $_SESSION['sign_sl'] = '';
        if ($boss_count == '0') {
        } else {
            $_SESSION['sign_sl'] = 'boss';
            //$this->setAlert(4, "<span style='font-size:20px'>你有 $boss_count 筆課表待簽核<BR>請至9B課程講座建檔批核</span>");
            echo '<script>if(confirm("你有 ' . $boss_count . ' 筆課表待簽核\n是否直接轉跳至9B課程講座建檔批核：")){document.location.href="create_class/set_course/list_sign";}</script>';
        }

        $leader_count = $this->course_sch_model->get_leader_Count($leader_no);
        if ($leader_count == '0') {
        } else {
            $_SESSION['sign_sl'] = 'leader';
            //echo '<script>alert("你有 '.$leader_count.' 筆課表待簽核\n是否直接轉跳至9B課程講座建檔批核！！")</script>';
            echo '<script>if(confirm("你有 ' . $leader_count . ' 筆課表待核閱\n是否直接轉跳至9B課程講座建檔批核！！")){document.location.href="create_class/set_course/list_sign";}</script>';
        }

        $this->layout->view('dcsdindex', $this->data);
    }
}

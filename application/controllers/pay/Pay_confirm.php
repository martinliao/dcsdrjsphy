<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Pay_confirm extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('pay/Pay_confirm_model');

        if (!isset($this->data['filter']['page'])) {
            $this->data['filter']['page'] = '1';
        }
    }

    public function index()
    {
        $start_date = isset($_GET['start_date'])?addslashes($_GET['start_date']):"";
        $send_date = isset($_GET['end_date'])?addslashes($_GET['end_date']):"";
        $count = isset($_GET['count'])?addslashes($_GET['count']):"";
        $act = isset($_GET['act'])?addslashes($_GET['act']):"";
        $chklist = isset($_GET['chklist'])?addslashes($_GET['chklist']):"";

        $this->data['s1']=$start_date;
        $this->data['s2']=$send_date;
        $this->data['result']=0;

        $page = $this->data['filter']['page'];
        $rows = $this->data['filter']['rows'];

        if($start_date !="" && $send_date != ""){
            $this->data['datas'] = $this->Pay_confirm_model->getPayConfirmData($start_date,$send_date,$count,$this->flags->user['idno'],$this->flags->user['group_id']);
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
            $this->data['datas'] = $this->Pay_confirm_model->getPayConfirmData($start_date,$send_date,$count,$this->flags->user['idno'],$this->flags->user['group_id'], $rows, $offset);
        }

        $attrs = array(
            'conditions' => $conditions,
            'rows' => $rows,
            'offset' => $offset,
        );

        $this->load->library('pagination');
        $config['base_url'] = base_url("pay/pay_confirm?". $this->getQueryString(array(), array('page')));
        $config['total_rows'] = $total;
        $config['per_page'] = $rows;
        $this->pagination->initialize($config);

        $this->data['link_showpdf'] = base_url("pay/print_pay_list");
        if($act!=""){
            if($act=='comfirm'){
                $chkDateList = explode(',', $chklist);
                for($i=0;$i<count($chkDateList);$i++){
                    $chkResult = $this->Pay_confirm_model->checkPayConfirmDate($chkDateList[$i]);

                    if(!$chkResult){
                        $this->setAlert(3, '請勿選擇明日以後的班期');
                        redirect(base_url("pay/pay_confirm/"));
                        exit;
                    }
                }
                   
                $this->data['result'] = $this->Pay_confirm_model->savePayConfirmData($start_date,$send_date,$count,$chklist);
                $this->data['datas'] = $this->Pay_confirm_model->getPayConfirmData($start_date,$send_date,$count,$this->flags->user['idno'],$this->flags->user['group_id'], $rows, $offset);
            }
            if($act=='insert'){
                $this->data['result'] = $this->Pay_confirm_model->insertPayConfirmData($start_date,$send_date,$count,$chklist,$this->flags->user["username"]);
            }
            if($act=='delete'){
                $this->data['result'] = $this->Pay_confirm_model->deletePayConfirmData($start_date,$send_date,$count,$chklist);
            }
            // if($act=='search'){
                
            // }            
        }
        if($act!="" && $act=='pdf'){
            // $paper_app_seq = isset($_GET['paper_app_seq'])?$_GET['paper_app_seq']:"";
            // $this->data['paper_app_seq']=$paper_app_seq;
            // $this->layout->view('pay/pay_confirm/pay03_2.php',$this->data);

            $paper_app_seq = isset($_GET['paper_app_seq'])?addslashes($_GET['paper_app_seq']):"";
            $mtList = isset($_GET['mtList'])?addslashes($_GET['mtList']):"";
            $this->data['paper_app_seq']=$paper_app_seq;
            $this->data['mtList']=$mtList;

            $this->layout->view('pay/print_pay_list/pay03_paperprn.php',$this->data);
        }else{
            $this->data['link_refresh'] = base_url("pay/pay_confirm/");
            $this->layout->view('pay/pay_confirm/list',$this->data);
        }        
    }

}

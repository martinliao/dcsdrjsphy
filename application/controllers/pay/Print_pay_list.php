<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Print_pay_list extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('pay/Print_pay_list_model');
        if (!isset($this->data['filter']['page'])) {
            $this->data['filter']['page'] = '1';
        }
    }

    public function index()
    {
        $start_date = isset($_GET['start_date'])?$_GET['start_date']:"";
        $send_date = isset($_GET['end_date'])?$_GET['end_date']:"";
        $count = isset($_GET['count'])?$_GET['count']:"";
        $act = isset($_GET['act'])?$_GET['act']:"";
        $chklist = isset($_GET['chklist'])?$_GET['chklist']:"";

        //var_dump($this->input->get());
        //var_dump($send_date);
        //echo'hello';

        $this->load->library('pagination');
        $config['total_rows'] = 200;
        $config['per_page'] = 20;
        $this->data['sess_start_date'] = $start_date;
        $this->data['sess_end_date'] = $send_date;
        $this->pagination->initialize($config);
        $this->data['result']=0;

        $page = $this->data['filter']['page'];
        $rows = $this->data['filter']['rows'];

        $conditions = array();

        $attrs = array(
            'conditions' => $conditions,
        );

        if($start_date != "" && $send_date != "") {
            $this->data['datas'] = $this->Print_pay_list_model->getPrintPayListData($start_date,$send_date,$count,$this->flags->user['idno'],$this->flags->user['group_id']);
        }
        else {
            $this->data['datas'] =array();
        }

        // if(isset($this->data['datas']))
            $this->data['filter']['total'] = $total = count($this->data['datas']);
        // else
        //     $this->data['filter']['total'] = $total = 0;
        $this->data['filter']['offset'] = $offset = ($page -1) * $rows;

        if($act!=""){
            if($act=='insert'){
                $this->data['result'] = $this->Print_pay_list_model->insertPrintPayListData($start_date,$send_date,$count,$chklist, $this->flags->user["username"]);
            }
            if($act=='delete'){
                $this->data['result'] = $this->Print_pay_list_model->deletePrintPayListData($start_date,$send_date,$count,$chklist);
            }
        }
        // echo $rows;
        if($total > 0) {
            $this->data['datas'] = $this->Print_pay_list_model->getPrintPayListData($start_date,$send_date,$count,$this->flags->user['idno'],$this->flags->user['group_id'], $rows, $offset);
        }

        $attrs = array(
            'conditions' => $conditions,
            'rows' => $rows,
            'offset' => $offset,
        );

        $this->load->library('pagination');
        $config['base_url'] = base_url("pay/print_pay_list?". str_replace('insert','',str_replace('delete','',$this->getQueryString(array(), array('page')))));
        $config['total_rows'] = $total;
        $config['per_page'] = $rows;
        $this->pagination->initialize($config);
        
        if($act!="" && $act=='pdf'){
            $paper_app_seq = isset($_GET['paper_app_seq'])?$_GET['paper_app_seq']:"";
            $is_status_ok = isset($_GET['is_status_ok'])?$_GET['is_status_ok']:"";
            $mtList = isset($_GET['mtList'])?$_GET['mtList']:"";
            $this->data['paper_app_seq']=$paper_app_seq;
            $this->data['is_status_ok']=$is_status_ok;
            $this->data['mtList']=$mtList;
            $this->layout->view('pay/print_pay_list/pay03_paper.php',$this->data);
        }
        else if($act!="" && $act=='pdf2'){
            $paper_app_seq = isset($_GET['paper_app_seq'])?$_GET['paper_app_seq']:"";
            $is_status_ok = isset($_GET['is_status_ok'])?$_GET['is_status_ok']:"";
            $mtList = isset($_GET['mtList'])?$_GET['mtList']:"";
            $this->data['paper_app_seq']=$paper_app_seq;
            $this->data['is_status_ok']=$is_status_ok;
            $this->data['mtList']=$mtList;
            $this->layout->view('pay/print_pay_list/pay03_paperprn.php',$this->data);
        }
        else{

            $app_seqs = array_map(function($data){
                return $data['app_seq'];
            }, $this->data['datas']);

            if (count($app_seqs) > 0){
                $this->data['hour_traffic_taxs'] = $this->Print_pay_list_model->getTaxByAppSeqs($app_seqs);
                $this->data['taxIsFinish'] = array_map(function($taxGroup){
                    $isFinish = true;
                    foreach ($taxGroup as $tax){
                        if ($tax->ischeck == 'N'){
                            $isFinish = null;
                        }
                    }
                    return $isFinish;
                },$this->data['hour_traffic_taxs']);
            }else{
                $this->data['hour_traffic_taxs'] = [];
            }

            // $this->data['datas'] = $this->Print_pay_list_model->getPrintPayListData($start_date,$send_date,$count);
            $this->data['link_refresh'] = base_url("pay/print_pay_list/");
            $this->data['link_checkpage'] = base_url("pay/print_pay_list/check");
            
            $this->layout->view('pay/print_pay_list/list',$this->data);
        } 
        
    }


    public function check()
    {
        $rules = array(
                    array(
                        'field'   => 'seq',
                        'label'   => '編號',
                        'rules'   => 'required'
                    )
                );


        $this->form_validation->set_rules($rules);   

        if ($this->form_validation->run()){
            $this->updateCheck();
        }       

        $this->data['taxkey'] = $this->input->get(['seq']);

        $this->data['hour_traffic_tax'] = $this->Print_pay_list_model->getBYKey($this->data['taxkey']);

        $this->data['hour_traffic_tax']->unit_hour_fee = ($this->data['hour_traffic_tax']->unit_hour_fee < 0) ? 0 : $this->data['hour_traffic_tax']->unit_hour_fee;
        $this->data['hour_traffic_tax']->hour_fee = ($this->data['hour_traffic_tax']->hour_fee < 0) ? 0 : $this->data['hour_traffic_tax']->hour_fee;
        $this->data['hour_traffic_tax']->traffic_fee = ($this->data['hour_traffic_tax']->traffic_fee < 0) ? 0 : $this->data['hour_traffic_tax']->traffic_fee;
        $this->data['hour_traffic_tax']->subtotal = ($this->data['hour_traffic_tax']->subtotal < 0) ? 0 : $this->data['hour_traffic_tax']->subtotal;
   
        return $this->load->view('pay/print_pay_list/check', $this->data);

    }

    function updateCheck(){
        if ($this->Print_pay_list_model->updateCheck($this->input->post('seq'))){
            echo "<script>alert('送出成功');window.opener.location.reload();window.close();window.close();</script>";
        }
    }
}

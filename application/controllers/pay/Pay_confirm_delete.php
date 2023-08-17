<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Pay_confirm_delete extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('pay/Pay_confirm_delete_model');

        if (!isset($this->data['filter']['page'])) {
            $this->data['filter']['page'] = '1';
        }
    }

    public function index()
    {
        $taker = isset($_GET['ntaker'])?$_GET['ntaker']:"";
        $applyid = isset($_GET['napplyid'])?$_GET['napplyid']:"";
        $start = isset($_GET['nstart'])?$_GET['nstart']:"";
        $end = isset($_GET['nend'])?$_GET['nend']:"";
        $count = isset($_GET['ncount'])?$_GET['ncount']:"";
        $act = isset($_GET['nact'])?$_GET['nact']:"";
        $chklist = isset($_GET['chklist'])?$_GET['chklist']:"";

        $this->data['taker']=$taker;
        $this->data['applyid']=$applyid;
        $this->data['start']=$start;
        $this->data['end']=$end;
        $this->data['count']=$count;

        $page = $this->data['filter']['page'];
        $rows = $this->data['filter']['rows'];

        if($start !="" && $end != ""){
            $this->data['datas'] = $this->Pay_confirm_delete_model->getPayConfirmDeleteData($taker,$applyid,$start,$end,$count);
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
            $this->data['datas'] = $this->Pay_confirm_delete_model->getPayConfirmDeleteData($taker,$applyid,$start,$end,$count, $rows, $offset);
        }

        $attrs = array(
            'conditions' => $conditions,
            'rows' => $rows,
            'offset' => $offset,
        );

        $this->load->library('pagination');
        $config['base_url'] = base_url("pay/pay_confirm_delete?". $this->getQueryString(array(), array('page')));
        $config['total_rows'] = $total;
        $config['per_page'] = $rows;
        $this->pagination->initialize($config);
        $this->data['result']=0;
        if($act!=""){
            if($act=='delete'){
                $this->data['result'] = $this->Pay_confirm_delete_model->deletePayConfirmDeleteData($taker,$applyid,$start,$end,$count,$chklist);
            }
            // if($act=='search'){
                
            // }
            
        } 

        $this->data['link_refresh'] = base_url("pay/pay_confirm_delete/");
        $this->layout->view('pay/pay_confirm_delete/list',$this->data);
    }
    public function detail()
    {
        $appseq = isset($_GET['appseq'])?$_GET['appseq']:"";
        $this->load->library('pagination');
        $config['total_rows'] = 200;
        $config['per_page'] = 20;
        $this->pagination->initialize($config);
        $this->data['datas'] = $this->Pay_confirm_delete_model->getPayConfirmDeleteDataBySeq($appseq);
        $this->data['link_refresh'] = base_url("pay/pay_confirm_delete/detail");
        $this->layout->view('pay/pay_confirm_delete/detail',$this->data);
    }

}

<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Epay extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('pay/Epay_model');

        if (!isset($this->data['filter']['page'])) {
            $this->data['filter']['page'] = '1';
        }
    }

    public function index()
    {
        $start_date = isset($_GET['start_date'])?$_GET['start_date']:"";
        $end_date = isset($_GET['end_date'])?$_GET['end_date']:"";
        $type = isset($_GET['type'])?$_GET['type']:"";


        $this->data['sess_start_date'] = $start_date;
        $this->data['sess_end_date'] = $end_date;

        if($type=="print"){
            $date = isset($_GET['date'])?$_GET['date']:"";
            $datas = $this->Epay_model->getprintData($date);
            echo json_encode($datas);
        }
        else{
            $page = $this->data['filter']['page'];
            $rows = $this->data['filter']['rows'];

            if($start_date !="" && $end_date != ""){
                $this->data['datas'] = $this->Epay_model->getEpayData($start_date, $end_date);
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
                $this->data['datas'] = $this->Epay_model->getEpayData($start_date, $end_date, $rows, $offset);
            }

            $attrs = array(
                'conditions' => $conditions,
                'rows' => $rows,
                'offset' => $offset,
            );

            $this->load->library('pagination');
            $config['base_url'] = base_url("pay/epay?". $this->getQueryString(array(), array('page')));
            $config['total_rows'] = $total;
            $config['per_page'] = $rows;
            $this->pagination->initialize($config);

            $this->data['link_refresh'] = base_url("pay/epay/");
            $this->layout->view('pay/epay/list',$this->data);
        }
    }

    public function detail()
    {
        $s1 = isset($_GET['s1'])?addslashes($_GET['s1']):"";
        $s2 = isset($_GET['s2'])?addslashes($_GET['s2']):"";
        $bldt = isset($_GET['bldt'])?addslashes($_GET['bldt']):"";
        $act = isset($_GET['act'])?addslashes($_GET['act']):"";
        $key = isset($_GET['key'])?addslashes($_GET['key']):"";
        $p_2htax = isset($_GET['p_2htax'])?addslashes($_GET['p_2htax']):"";

        $this->load->library('pagination');
        $config['total_rows'] = 200;
        $config['per_page'] = 20;
        $this->pagination->initialize($config);

        $this->data['sess_s1'] = $s1;
        $this->data['sess_s2'] = $s2;
        $this->data['sess_bldt'] = $bldt;

        $this->data['datas'] = $this->Epay_model->getEpayDetail($bldt);

        $this->data['link_refresh'] = base_url("pay/epay/detail");
        if($act=="rate"){
            $this->layout->view('pay/epay/pay_TaxChg.php',$this->data);
        }elseif($act=="hrate"){
            $this->layout->view('pay/epay/pay_2HTaxChg.php',$this->data);
        }else{
            $this->layout->view('pay/epay/detail',$this->data);
        }
    }

    public function printJson(){
        
    }
}

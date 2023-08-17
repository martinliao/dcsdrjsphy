<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Teach_pay_list2 extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('pay/Teach_pay_list2_model');

        if (!isset($this->data['filter']['page'])) {
            $this->data['filter']['page'] = '1';
        }
    }

    public function index()
    {
        $start_date = isset($_GET['start_date'])?$_GET['start_date']:"";
        $end_date = isset($_GET['end_date'])?$_GET['end_date']:"";

        $this->data['sess_start_date'] = $start_date;
        $this->data['sess_end_date'] = $end_date;

        $account = $this->flags->user['username'];

        $page = $this->data['filter']['page'];
        $rows = $this->data['filter']['rows'];

        if($start_date !="" && $end_date != ""){
            $this->data['datas'] = $this->Teach_pay_list2_model->getTeacherPayList2($start_date, $end_date, $account);
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
            $this->data['datas'] = $this->Teach_pay_list2_model->getTeacherPayList2($start_date, $end_date, $account, $rows, $offset);
        }

        $attrs = array(
            'conditions' => $conditions,
            'rows' => $rows,
            'offset' => $offset,
        );

        $this->load->library('pagination');
        $config['base_url'] = base_url("pay/teach_pay_list2?". $this->getQueryString(array(), array('page')));
        $config['total_rows'] = $total;
        $config['per_page'] = $rows;
        $this->pagination->initialize($config);

        $this->data['link_refresh'] = base_url("pay/teach_pay_list2/");
        $this->layout->view('pay/teach_pay_list2/list',$this->data);
    }
    public function detail()
    {
        $this->load->library('pagination');
        $config['total_rows'] = 200;
        $config['per_page'] = 20;
        $this->pagination->initialize($config);
        $this->data['link_refresh'] = base_url("pay/teach_pay_list2/");
        $this->layout->view('pay/teach_pay_list2/detail',$this->data);
    }

}

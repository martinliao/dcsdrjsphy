<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Apply_no_pay extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('pay/Apply_no_pay_model');

        if (!isset($this->data['filter']['page'])) {
            $this->data['filter']['page'] = '1';
        }
    }

    public function index()
    {
        $thisyear = date("Y")-1911;
        $year = isset($_GET['year'])?$_GET['year']:$thisyear;
        $month = isset($_GET['month'])?$_GET['month']:"";

        $this->data['sess_year'] = $year;
        $this->data['sess_month'] = $month;
        $this->data['link_refresh'] = base_url("pay/apply_no_pay/");

        $page = $this->data['filter']['page'];
        $rows = $this->data['filter']['rows'];

        if($year !="" && $month != ""){
            $this->data['datas'] = $this->Apply_no_pay_model->getApplyNoPaySearch($year, $month);
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
            $this->data['datas'] = $this->Apply_no_pay_model->getApplyNoPaySearch($year, $month, $rows, $offset);
        }

        $attrs = array(
            'conditions' => $conditions,
            'rows' => $rows,
            'offset' => $offset,
        );

        $this->load->library('pagination');
        $config['base_url'] = base_url("pay/apply_no_pay?". $this->getQueryString(array(), array('page')));
        $config['total_rows'] = $total;
        $config['per_page'] = $rows;
        $this->pagination->initialize($config);
        
        $this->layout->view('pay/apply_no_pay/list',$this->data);
    }

}

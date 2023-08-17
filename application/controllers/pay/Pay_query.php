<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Pay_query extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('pay/Pay_query_model');

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
        $this->data['link_refresh'] = base_url("pay/pay_query/");

        $page = $this->data['filter']['page'];
        $rows = $this->data['filter']['rows'];

        $conditions = array();

        $attrs = array(
            'conditions' => $conditions,
        );

        if($year != "" && !empty($this->input->get())){
            $this->data['datas'] = $this->Pay_query_model->getPayQuerySearch($year, $month);
        }
        else {
            $this->data['datas'] = array();
        }
        

        $this->data['filter']['total'] = $total = count($this->data['datas']);
        $this->data['filter']['offset'] = $offset = ($page -1) * $rows;

        if($total > 0) {
            $this->data['datas'] = $this->Pay_query_model->getPayQuerySearch($year, $month, $rows, $offset);
        }

        $attrs = array(
            'conditions' => $conditions,
            'rows' => $rows,
            'offset' => $offset,
        );

        $this->load->library('pagination');
        $config['base_url'] = base_url("pay/pay_query?". $this->getQueryString(array(), array('page')));
        $config['total_rows'] = $total;
        $config['per_page'] = $rows;
        $this->pagination->initialize($config);

        if($year != ""){
            $this->data['dayOfWeek'] = $this->Pay_query_model->getDayOfWeek();
            if(isset($_GET['iscsv'])  && $_GET['iscsv'] == 1  ){
                $this->Pay_query_model->csvexport(date("Y-m-d"),"","",$this->data['dayOfWeek'],$year, $month);
            }
            else{
                $this->layout->view('pay/pay_query/list',$this->data);
            }
        }
        else{
            $this->layout->view('pay/pay_query/list',$this->data);
        }
    }

}

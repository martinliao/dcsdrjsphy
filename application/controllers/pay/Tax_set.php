<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Tax_set extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('pay/Tax_set_model');
    }
    public function index()
    {
        $money = isset($_GET['nmoney'])?$_GET['nmoney']:"";
        $tax = isset($_GET['ntax'])?$_GET['ntax']:"";
        $healthmoney = isset($_GET['nhealthmoney'])?$_GET['nhealthmoney']:"";
        $healthtax = isset($_GET['nhealthtax'])?$_GET['nhealthtax']:"";
        $setup = isset($_GET['act'])?$_GET['act']:"";

        $this->load->library('pagination');
        $config['total_rows'] = 200;
        $config['per_page'] = 20;
        $this->pagination->initialize($config);
        $this->data['result']=0;
        if($setup=="setup"){
            $this->data['result'] = $this->Tax_set_model->insertTaxSetData($money,$tax,$healthmoney,$healthtax);
        }
        
        $this->data['datas'] = $this->Tax_set_model->getTaxSetData();
        $this->data['link_refresh'] = base_url("pay/tax_set/");
        $this->layout->view('pay/tax_set/list',$this->data);
    }
}

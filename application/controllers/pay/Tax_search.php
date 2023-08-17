<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Tax_search extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('pay/Tax_search_model');
    }

    public function index()
    {
        $thisyear = date("Y")-1911;
        $year = isset($_GET['year'])?addslashes($_GET['year']):$thisyear;
        $startMonth = isset($_GET['startMonth'])?addslashes($_GET['startMonth']):"";
        $endMonth = isset($_GET['endMonth'])?addslashes($_GET['endMonth']):"";
        $type = isset($_GET['type'])?addslashes($_GET['type']):"";

        $this->load->library('pagination');
        $config['total_rows'] = 200;
        $config['per_page'] = 20;
        $this->pagination->initialize($config);
        $this->data['sess_year'] = $year;
        $this->data['sess_startMonth'] = $startMonth;
        $this->data['sess_endMonth'] = $endMonth;

        $this->data['result']=0;
        if($type==""){
            $this->data['datas'] = $this->Tax_search_model->getTaxSearchData($year,$startMonth,$endMonth);
            $this->data['link_refresh'] = base_url("pay/tax_search/");
            $this->layout->view('pay/tax_search/list',$this->data);
        }else{
            if($type==0){
                $this->Tax_search_model->exportTaxSearchData0($year,$startMonth,$endMonth);
            }
            if($type==1){
                $this->Tax_search_model->exportTaxSearchData1($year,$startMonth,$endMonth);
            }
            if($type==2){
                $this->Tax_search_model->exportTaxSearchData2($year,$startMonth,$endMonth);
            }
        }        
        
    }

}

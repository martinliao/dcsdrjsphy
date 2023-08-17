<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Pay extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('pay/Pay_model');
    }

    public function index()
    {
        $type = isset($_GET['type'])?$_GET['type']:"";
        if($type == "update"){
            $thisyear = date("Y")-1911;
            $year = isset($_GET['year'])?$_GET['year']:$thisyear;
            $term = isset($_GET['term'])?$_GET['term']:"";
            $class_no = isset($_GET['class_no'])?$_GET['class_no']:"";
            $d1 = isset($_GET['rs'])?$_GET['rs']:"";
            $d2 = isset($_GET['re'])?$_GET['re']:"";
            $result = $this->Pay_model->setPay($year,$term,$class_no,$d1,$d2);
            if($result){
                echo json_encode(array("status"=>1));
            }
            else{
                echo json_encode(array("status"=>0));
            }
        }
        else if($type == "noupdate"){
            $thisyear = date("Y")-1911;
            $year = isset($_GET['year'])?$_GET['year']:$thisyear;
            $term = isset($_GET['term'])?$_GET['term']:"";
            $class_no = isset($_GET['class_no'])?$_GET['class_no']:"";
            $d1 = isset($_GET['rs'])?$_GET['rs']:"";
            $d2 = isset($_GET['re'])?$_GET['re']:"";
            $result = $this->Pay_model->deletePay($year,$term,$class_no,$d1,$d2);
            if($result){
                echo json_encode(array("status"=>1));
            }
            else{
                echo json_encode(array("status"=>0));
            }
        }
        else{
            $start_date = isset($_GET['start_date'])?$_GET['start_date']:"";
            $end_date = isset($_GET['end_date'])?$_GET['end_date']:"";
            $allQry = isset($_GET['allQry'])?$_GET['allQry']:"";

            $this->data['sess_start_date'] = $start_date;
            $this->data['sess_end_date'] = $end_date;
            $this->data['sess_allQry']=$allQry;

            $config['base_url'] = base_url("customer_service/mail_log_search");

            $conditions = array();

            $attrs = array(
                'conditions' => $conditions,
            );

            $this->data['datas'] = $this->Pay_model->getPayData($start_date,$end_date,$this->flags->user['idno'],$this->flags->user['group_id']);
            $this->data['link_refresh'] = base_url("pay/pay");
            $this->layout->view('pay/pay/list',$this->data);
        }
        
    }

    // public function deletePay($year,$term,$class_no,$d1,$d2){
       
    // }

    // public function setpay($year,$term,$class_no,$d1,$d2){
        
    // }
    
    public function detail()
    {
        $thisyear = date("Y")-1911;
        $year = isset($_GET['year'])?$_GET['year']:$thisyear;
        $class = isset($_GET['class'])?$_GET['class']:"";
        $term = isset($_GET['term'])?$_GET['term']:"";
        $classname = isset($_GET['classname'])?$_GET['classname']:"";
        $startdate = isset($_GET['startdate'])?$_GET['startdate']:"";
        $enddate = isset($_GET['enddate'])?$_GET['enddate']:"";
        $act = isset($_GET['act'])?$_GET['act']:"";
        $chklist = isset($_GET['chklist'])?$_GET['chklist']:"";
        $selectlist = isset($_GET['selectlist'])?$_GET['selectlist']:"";
        $umoney = isset($_GET['umoney'])?$_GET['umoney']:"";
        $hmoney = isset($_GET['hmoney'])?$_GET['hmoney']:"";
        $tmoney = isset($_GET['tmoney'])?$_GET['tmoney']:"";
        $editOne = isset($_GET['editOne'])?$_GET['editOne']:"";
        $editValue = isset($_GET['editValue'])?$_GET['editValue']:"";
        $tex_data = isset($_GET['tex_data'])?$_GET['tex_data']:"";
        $priceType = isset($_GET['priceType'])?$_GET['priceType']:"";
        $seq = isset($_GET['seq'])?$_GET['seq']:"";

        $this->data['sess_year']=$year;
        $this->data['sess_class']=$class;
        $this->data['sess_term']=$term;
        $this->data['sess_classname']=$classname;
        $this->data['sess_startdate']=$startdate;
        $this->data['sess_enddate']=$enddate;
        $this->data['result']=0;
        $this->data['datas']=[];

        

        if($act!=""){
            if($act=='invoice'){
                $this->data['result'] = $this->Pay_model->invoicePayDetailData($editOne,$editValue);
            }
            if($act=='comfirm'){
                $this->data['result'] = $this->Pay_model->confirmPayDetailData($selectlist,$chklist,$tex_data,$umoney,$hmoney,$tmoney);
            }
            if($act=='reenter'){
                $this->data['result'] = $this->Pay_model->reenterPayDetailData($year,$class,$term,$startdate,$enddate);
                $this->data['datas'] = $this->Pay_model->getPayDetailData($startdate,$enddate,$year,$class,$term);
            }
            if($act=='setPrice'){
                $this->data['result'] = $this->Pay_model->setPrice($priceType,$umoney,$hmoney,$tmoney,$seq);
                $this->data['datas'] = $this->Pay_model->getPayDetailData($startdate,$enddate,$year,$class,$term);
            }
            if($act=='search'){
                $this->data['datas'] = $this->Pay_model->getPayDetailData($startdate,$enddate,$year,$class,$term);
            }            
        }

        $this->data['trafficList'] = $this->Pay_model->searchTrafficList();

        // print_r($this->data);
        $this->data['link_refresh'] = base_url("pay/pay");
        $this->layout->view('pay/pay/detail',$this->data);
        
    }

}

<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Teach_pay_list extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('pay/Teach_pay_list_model');
    }

    public function index()
    {
        $appno = isset($_GET['appno'])?addslashes($_GET['appno']):"";
        $workname = isset($_GET['workname'])?addslashes($_GET['workname']):"";
        $start_date = isset($_GET['start_date'])?addslashes($_GET['start_date']):"";
        $end_date = isset($_GET['end_date'])?addslashes($_GET['end_date']):"";
        $mtlist = isset($_GET['mtlist'])?addslashes($_GET['mtlist']):"";
        $act = isset($_GET['act'])?addslashes($_GET['act']):"";
        $outdt = isset($_GET['outdt'])?addslashes($_GET['outdt']):"";
        $outimd = isset($_GET['outimd'])?addslashes($_GET['outimd']):"";

        $this->data['sess_appno'] = $appno;
        $this->data['sess_workname'] = $workname;
        $this->data['sess_start_date'] = $start_date;
        $this->data['sess_end_date'] = $end_date;
        $this->data['sess_outdt'] = $outdt;
        $this->data['sess_outimd'] = $outimd;
        $this->data['link_refresh'] = base_url("pay/teach_pay_list/");

        $this->data['result']=0;
        $this->data['datas']=[];

        if($act!=""){
            if($act=='search'){
                if(!empty($_GET)){
                    $this->data['datas'] = $this->Teach_pay_list_model->getTeachPayListSearch($appno, $workname, $start_date, $end_date);
                }
                else {
                    $this->data['datas'] = array();
                }
            }
            if($act=='setdt'){
                $this->data['result'] = $this->Teach_pay_list_model->setdt($outdt,$mtlist);
            }
            if($act=='canceldt'){
                $this->data['result'] = $this->Teach_pay_list_model->canceldt($mtlist);
            }
            if($act=='setimd'){
                $this->data['result'] = $this->Teach_pay_list_model->setimd($outimd,$mtlist);
            }
            if($act=='cancelimd'){
                $this->data['result'] = $this->Teach_pay_list_model->cancelimd($mtlist);
            }
            if($act=='getcash'){
                $appseq = isset($_GET['appseq'])?addslashes($_GET['appseq']):"";
                $this->data['result'] = $this->Teach_pay_list_model->update_13D_hourapp($appseq);
            }            
        }

        $this->layout->view('pay/teach_pay_list/list',$this->data);
    }
    public function detail()
    {
        $appno = isset($_GET['appno'])?addslashes($_GET['appno']):"";

        $this->data['sess_appno'] = $appno;
        $this->data['link_refresh'] = base_url("pay/teach_pay_list/detail");
        $this->data['datas'] = $this->Teach_pay_list_model->getTeachPayListByAppnoSearch($appno);
        $this->layout->view('pay/teach_pay_list/detail',$this->data);
    }

}

<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Bureau_participate_count extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('statistics_paper/Bureau_participate_count_model');
    }

    public function index()
    {
        $thisyear = date("Y")-1911;
        $year = isset($_GET['year'])?$_GET['year']:$thisyear;
        $season = isset($_GET['season'])?$_GET['season']:"";
        $type = isset($_GET['type'])?$_GET['type']:"";
        $startMonth = isset($_GET['startMonth'])?$_GET['startMonth']:"";
        $endMonth = isset($_GET['endMonth'])?$_GET['endMonth']:"";
        $start_date = isset($_GET['start_date'])?$_GET['start_date']:"";
        $end_date = isset($_GET['end_date'])?$_GET['end_date']:"";
        $count = isset($_GET['count'])?$_GET['count']:"";
        $act = isset($_GET['act'])?$_GET['act']:"";
        $cs = isset($_GET['cs'])?$_GET['cs']:"";
        $cp = isset($_GET['cp'])?$_GET['cp']:"";
        $ct = isset($_GET['ct'])?$_GET['ct']:"";
        $rm = isset($_GET['rm'])?$_GET['rm']:"";
        $ssd = $start_date;
        $sed = $end_date;
        $changemode=
        array(
            'cs'=>$cs,
            'cp'=>$cp,
            'ct'=>$ct,
            'rm'=>$rm
            );

        if($type == 1 || $type == 2  ){
            $dateRange = $this->Bureau_participate_count_model->getDataRange($year,$type,$season,$startMonth,$endMonth);
            $ssd =$dateRange[0]; 
            $sed =$dateRange[1]; 
        }
        if($type == 0){
            if($year != ""){
                $dateRange = $this->Bureau_participate_count_model->getOneYear($year);
                $ssd =$dateRange[0]; 
                $sed =$dateRange[1];
            }
        }

        $this->data['sess_year'] = $year;
        $this->data['sess_season'] = $season;
        $this->data['sess_type'] = $type;
        $this->data['sess_startMonth'] = $startMonth;
        $this->data['sess_endMonth'] = $endMonth;
        $this->data['sess_start_date'] = $start_date;
        $this->data['sess_end_date'] = $end_date;
        $this->data['sess_count'] = $count;
        $this->data['sess_cs'] = $cs;
        $this->data['sess_cp'] = $cp;
        $this->data['sess_ct'] = $ct;
        $this->data['sess_rm'] = $rm;
        $this->data['result']=0;
        $this->data['datas']['rows']=[];
        $this->data['datas'] = array();
        $this->data['datas']['rows'] =array();
        $this->data['datas']['hasdata'] =0;

        $conditions = array();

        $attrs = array(
            'conditions' => $conditions,
        );

        if($act!=""){
            if($act=='search'){
                $this->data['datas'] = $this->Bureau_participate_count_model->getBureauParticipateCountData($year,$ssd,$sed,$changemode);
                $this->data['link_refresh'] = base_url("statistics_paper/bureau_participate_count/");
                $this->layout->view('statistics_paper/bureau_participate_count/list',$this->data);
            }
            if($act=='csv'){
                $this->data['result'] = $this->Bureau_participate_count_model->exportBureauParticipateCountData($year,$ssd,$sed,$changemode);
            }
        }else{
            $this->data['link_refresh'] = base_url("statistics_paper/bureau_participate_count/");
            $this->layout->view('statistics_paper/bureau_participate_count/list',$this->data);
        }
    }

}

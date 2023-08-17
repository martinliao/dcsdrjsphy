<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Bureau_finish_count extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('statistics_paper/Bureau_finish_count_model');
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
        $ssd = $start_date;
        $sed = $end_date;

        if($type == 1 || $type == 2  ){
            $dateRange = $this->Bureau_finish_count_model->getDataRange($year,$type,$season,$startMonth,$endMonth);
            $ssd =$dateRange[0]; 
            $sed =$dateRange[1]; 
        }
        else if($type == 0){
            if($year != ""){
                $dateRange = $this->Bureau_finish_count_model->getOneYear($year);
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
        $this->data['link_refresh'] = base_url("statistics_paper/bureau_finish_count/");
        $this->data['datas'] = array();

        $conditions = array();

        $attrs = array(
            'conditions' => $conditions,
        );

        if($year != ""){
            $this->data['datas'] = $this->Bureau_finish_count_model->getBureauFinishCountData($year,$ssd,$sed,$type);
            $this->data['dayOfWeek'] = $this->Bureau_finish_count_model->getDayOfWeek();
            if(isset($_GET['iscsv'])  && $_GET['iscsv'] == 1  ){
                $this->data['datas'] = $this->Bureau_finish_count_model->getBureauFinishCountData($year,$ssd,$sed,$type);
                $this->Bureau_finish_count_model->csvexport(date("Y-m-d"),$ssd,$sed,$this->data['datas'],$this->data['dayOfWeek']);
            }
            else{
                $this->layout->view('statistics_paper/bureau_finish_count/list',$this->data);
            }
        }
        else{
            $this->layout->view('statistics_paper/bureau_finish_count/list',$this->data);
        }
        
    }

}

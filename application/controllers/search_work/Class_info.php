<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Class_info extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('search_work/Class_info_model');
    }

    public function index()
    {
        $schedule = isset($_GET['schedule'])?$_GET['schedule']:"";
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
            $dateRange = $this->Class_info_model->getDataRange($year,$type,$season,$startMonth,$endMonth);
            $ssd =$dateRange[0]; 
            $sed =$dateRange[1]; 
        }

        $this->data['sess_schedule'] = $schedule;
        $this->data['sess_year'] = $year;
        $this->data['sess_season'] = $season;
        $this->data['sess_type'] = $type;
        $this->data['sess_startMonth'] = $startMonth;
        $this->data['sess_endMonth'] = $endMonth;
        $this->data['sess_start_date'] = $start_date;
        $this->data['sess_end_date'] = $end_date;
        $this->data['link_refresh'] = base_url("search_work/class_info/");
        $this->data['datas'] = array();
        
        if($year != ""){
            $this->data['dayOfWeek'] = $this->Class_info_model->getDayOfWeek();

            if(isset($_GET['iscsv'])  && $_GET['iscsv'] == 1  ){
                $this->Class_info_model->csvexport(date("Y-m-d"),$ssd,$sed,$this->data['dayOfWeek'],$year, $start_date, $end_date, $schedule);
            }
            else if(isset($_GET['iscsv'])  && $_GET['iscsv'] == 0){
                $this->data['datas'] = $this->Class_info_model->getclass_info($year, $ssd, $sed, $schedule);
                $this->layout->view('search_work/class_info/list',$this->data);
            }
            else {
                $this->layout->view('search_work/class_info/list',$this->data);
            }
        }
        else{
            $this->layout->view('search_work/class_info/list',$this->data);
        }
    }

}

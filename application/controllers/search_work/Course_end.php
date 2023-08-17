<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Course_end extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('search_work/Course_end_model');
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
        $act = isset($_GET['act'])?$_GET['act']:"";
        $ssd = $start_date;
        $sed = $end_date;

        $this->data['sess_year'] = $year;
        $this->data['sess_season'] = $season;
        $this->data['sess_type'] = $type;
        $this->data['sess_startMonth'] = $startMonth;
        $this->data['sess_endMonth'] = $endMonth;
        $this->data['sess_start_date'] = $start_date;
        $this->data['sess_end_date'] = $end_date;
        $this->data['datas'] = array();

        if($type == 1 || $type == 2){
            $dateRange = $this->Course_end_model->getDataRange($year,$type,$season,$startMonth,$endMonth);

            $ssd =$dateRange[0]; 
            $sed =$dateRange[1]; 
        }
        else if($type == 3) {
            $ssd =$start_date; 
            $sed =$end_date;
        }
        else{
            $dateRange = $this->Course_end_model->getOneYear($year);
            $ssd =$dateRange[0]; 
            $sed =$dateRange[1]; 
        }
        
        if($act!=""){
            if($act=='search'){
                $this->data['link_refresh'] = base_url("search_work/course_end/");
                $this->data['datas'] = $this->Course_end_model->getCourseEndData($year, $ssd, $sed, $type);
                $this->layout->view('search_work/course_end/list',$this->data);
            }
            if($act=='csv'){
                $this->data['result'] = $this->Course_end_model->exportCourseEndData($year,$ssd,$sed,$type);
            }
        }else{
            $this->data['link_refresh'] = base_url("search_work/course_end/");
                $this->layout->view('search_work/course_end/list',$this->data);
        }
    }

}

<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Bureau_course_time extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('statistics_paper/Bureau_course_time_model');
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
        $ssd = $start_date;
        $sed = $end_date;

        if($type == 1 || $type == 2  ){
            $dateRange = $this->Bureau_course_time_model->getDataRange($year,$type,$season,$startMonth,$endMonth);
            $ssd =$dateRange[0]; 
            $sed =$dateRange[1]; 
        }
        if($type == 0){
            if($year != ""){
                $dateRange = $this->Bureau_course_time_model->getOneYear($year);
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
        $this->data['result']=0;
        $this->data['datas']['rows']=[];

        $conditions = array();

        $attrs = array(
            'conditions' => $conditions,
        );

        if($act!=""){
            if($act=='search'){
                $this->data['datas'] = $this->Bureau_course_time_model->getBureauCourseTimeData($year,$ssd,$sed);
                $this->data['link_refresh'] = base_url("statistics_paper/Bureau_course_time/");
                $this->layout->view('statistics_paper/bureau_course_time/list',$this->data);
            }
            if($act=='csv'){
                $this->data['result'] = $this->Bureau_course_time_model->exportBureauCourseTimeData($year,$ssd,$sed);
            }
        }else{
            $this->data['link_refresh'] = base_url("statistics_paper/Bureau_course_time/");
            $this->layout->view('statistics_paper/bureau_course_time/list',$this->data);
        }
    }

}

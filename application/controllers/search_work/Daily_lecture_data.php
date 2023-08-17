<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Daily_lecture_data extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('search_work/Daily_lecture_data_model');
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
        $detailcheck = isset($_GET['sd'])?$_GET['sd']:"";
        $teachercheck = isset($_GET['st'])?$_GET['st']:"";
        $site_B = isset($_GET['site_B'])?$_GET['site_B']:"";
        $site_C = isset($_GET['site_C'])?$_GET['site_C']:"";
        $site_E = isset($_GET['site_E'])?$_GET['site_E']:"";
        $ssd = $start_date;
        $sed = $end_date;

        $this->data['sess_year'] = $year;
        $this->data['sess_season'] = $season;
        $this->data['sess_type'] = $type;
        $this->data['sess_startMonth'] = $startMonth;
        $this->data['sess_endMonth'] = $endMonth;
        $this->data['sess_start_date'] = $start_date;
        $this->data['sess_end_date'] = $end_date;
        $this->data['sess_d'] = $detailcheck;
        // $this->data['sess_t'] = $teachercheck;
        $this->data['sess_site_B'] = $site_B;
        $this->data['sess_site_C'] = $site_C;
        $this->data['sess_site_E'] = $site_E;
        $this->data['sess_t'] = '';//每次頁面都清空
        $this->data['link_refresh'] = base_url("search_work/daily_lecture_data/");
        $this->data['datas'] = array();
        $sites = ($site_B !='Y')?'':"B,";
        $sites .= ($site_C !='Y')?'':"C,";
        $sites .= ($site_E !='Y')?'':"E,";
        $sites = ($sites=='')? '':substr($sites, 0,-1);

        if($type == 1 || $type == 2  ){
            $dateRange = $this->Daily_lecture_data_model->getDataRange($year,$type,$season,$startMonth,$endMonth);
            $ssd =$dateRange[0]; 
            $sed =$dateRange[1]; 
        }
        
        if($year != ""){
            $this->data['dayOfWeek'] = $this->Daily_lecture_data_model->getDayOfWeek();
            if(isset($_GET['iscsv'])  && $_GET['iscsv'] == 1  ){
                $this->Daily_lecture_data_model->csvexport(date("Y-m-d"),$ssd,$sed,$this->data['dayOfWeek'], $teachercheck, $year, $detailcheck,$sites);
            }
            else{
                $this->data['datas'] = $this->Daily_lecture_data_model->getDailyLectureData($year, $ssd, $sed, $detailcheck, $teachercheck,$sites);
            }
        }
        $this->layout->view('search_work/daily_lecture_data/list',$this->data);
    }

}

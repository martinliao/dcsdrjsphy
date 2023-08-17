<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Change_work_list extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('search_work/Change_work_list_model');
    }

    public function index()
    {
        $schedule = isset($_GET['nschedule'])?$_GET['nschedule']:"";
        $thisyear = date("Y")-1911;
        $year = isset($_GET['year'])?$_GET['year']:$thisyear;
        $season = isset($_GET['season'])?$_GET['season']:"";
        $type = isset($_GET['type'])?$_GET['type']:"";
        $startMonth = isset($_GET['startMonth'])?$_GET['startMonth']:"";
        $endMonth = isset($_GET['endMonth'])?$_GET['endMonth']:"";
        $start_date = isset($_GET['start_date'])?$_GET['start_date']:"";
        $end_date = isset($_GET['end_date'])?$_GET['end_date']:"";
        $sort = isset($_GET['sort'])?$_GET['sort']:"";
        $act = isset($_GET['act'])?$_GET['act']:"";
        $ssd = $start_date;
        $sed = $end_date;

        if($type == 1 || $type == 2  ){
            $dateRange = $this->Change_work_list_model->getDataRange($year,$type,$season,$startMonth,$endMonth);
            $ssd =$dateRange[0]; 
            $sed =$dateRange[1]; 
        }
        else if($type == 0){
            if($year != ""){
                $dateRange = $this->Change_work_list_model->getOneYear($year);
                $ssd =$dateRange[0]; 
                $sed =$dateRange[1]; 
            }
        }

        $this->data['sess_schedule'] = $schedule;
        $this->data['sess_year'] = $year;
        $this->data['sess_season'] = $season;
        $this->data['sess_type'] = $type;
        $this->data['sess_startMonth'] = $startMonth;
        $this->data['sess_endMonth'] = $endMonth;
        $this->data['sess_start_date'] = $start_date;
        $this->data['sess_end_date'] = $end_date;
        $this->data['sess_sort'] = $sort;
        $this->data['datas'] = array();
        
        if($act!=""){
            if($act=='search'){
                $this->data['datas'] = $this->Change_work_list_model->getChangeWorkListData($year,$ssd,$sed,$schedule,$sort);
                $this->data['link_refresh'] = base_url("search_work/change_work_list/");
                $this->layout->view('search_work/change_work_list/list',$this->data);
            }
            if($act=='csv'){
                $this->data['result'] = $this->Change_work_list_model->exportChangeWorkListData($year,$ssd,$sed,$schedule,$sort);
            }
        }else{
            $this->data['link_refresh'] = base_url("search_work/change_work_list/");
            $this->layout->view('search_work/change_work_list/list',$this->data);
        }
    }

    public function detail()
    {
        $class = isset($_GET['class'])?$_GET['class']:"";
        $year = isset($_GET['year'])?$_GET['year']:"";
        $term = isset($_GET['term'])?$_GET['term']:"";
        $type = isset($_GET['type'])?$_GET['type']:"";

        $this->data['sess_class'] = $class;
        $this->data['sess_year'] = $year;
        $this->data['sess_term'] = $term;
        $this->data['sess_type'] = $type;

        $this->data['datas'] = [];
        $this->data['datas'] = $this->Change_work_list_model->getDetailChangeWorkListData($year,$class,$term,$type);
        $this->data['link_refresh'] = base_url("search_work/change_work_list/detail");
        $this->layout->view('search_work/change_work_list/detail',$this->data);
    }

}

<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Person_list extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('search_work/Person_list_model');

        if (!isset($this->data['filter']['query_type'])) {
            $this->data['filter']['query_type'] = '';
        }
        if (!isset($this->data['filter']['query_second'])) {
            $this->data['filter']['query_second'] = '';
        }

        if (!isset($this->data['filter']['page'])) {
            $this->data['filter']['page'] = '1';
        }
    }

    public function index()
    {
        $thisyear = date("Y")-1911;
        $year = isset($_GET['year'])?$_GET['year']:$thisyear;
        $schedule = isset($_GET['nschedule'])?$_GET['nschedule']:"";
        $class_no = isset($_GET['class_no'])?$_GET['class_no']:"";
        
        $contactor = isset($_GET['contactor'])?$_GET['contactor']:"";

        $firstSeries = isset($_GET['firstSeries'])?$_GET['firstSeries']:"";
        $secondSeries = isset($_GET['secondSeries'])?$_GET['secondSeries']:"";

        $season = isset($_GET['season'])?$_GET['season']:"";
        $type = isset($_GET['type'])?$_GET['type']:"";
        $startMonth = isset($_GET['startMonth'])?$_GET['startMonth']:"";
        $endMonth = isset($_GET['endMonth'])?$_GET['endMonth']:"";
        $start_date = isset($_GET['start_date'])?$_GET['start_date']:"";
        $end_date = isset($_GET['end_date'])?$_GET['end_date']:"";
        
        $ssd = $start_date;
        $sed = $end_date;

        $this->data['sess_year'] = $year;
        $this->data['sess_class_no'] = $class_no;
        $this->data['sess_schedule'] = $schedule;
        $this->data['sess_contactor'] = $contactor;
        $this->data['sess_season'] = $season;
        $this->data['sess_type'] = $type;
        $this->data['sess_startMonth'] = $startMonth;
        $this->data['sess_endMonth'] = $endMonth;
        $this->data['sess_firstSeries'] = $firstSeries;
        $this->data['sess_secondSeries'] = $secondSeries;
        $this->data['sess_start_date'] = $start_date;
        $this->data['sess_end_date'] = $end_date;  
        $this->data['datas'] = array();

        $this->data['link_get_second_category'] = base_url("search_work/person_list/getSecondCategory");
        $this->data['link_refresh'] = base_url("search_work/person_list/");

        $this->data['choices']['query_type'] = $this->getSeriesCategory();
        $this->data['choices']['query_type'][''] = '請選擇系列別';
        $this->data['choices']['query_type'] = array_reverse($this->data['choices']['query_type']);

        $this->data['query_contactor'] = $this->Person_list_model->getContactor();

        $page = $this->data['filter']['page'];
        $rows = $this->data['filter']['rows'];

        $conditions = array();
		if ($this->data['filter']['query_type'] !== '' ) {
            $conditions['type'] = $this->data['filter']['query_type'];
            $this->data['choices']['query_second'] = $this->getSecondCategory($this->data['filter']['query_type']);
        }

        if ($this->data['filter']['query_second'] !== '' ) {
            $conditions['beaurau_id'] = $this->data['filter']['query_second'];
        }
		$attrs = array(
            'conditions' => $conditions,
        );

        if($type == 1 || $type == 2  ){
            $dateRange = $this->Person_list_model->getDataRange($year,$type,$season,$startMonth,$endMonth);
            $ssd =$dateRange[0]; 
            $sed =$dateRange[1]; 
        }
        else if($type == 0){
            if($year != ""){
                $dateRange = $this->Person_list_model->getOneYear($year);
                $ssd =$dateRange[0]; 
                $sed =$dateRange[1]; 
            } 
        }

        if($type != "") {
            if($year != "") {
                $this->data['datas'] = $this->Person_list_model->
                getPersonListData($year, $schedule, $class_no, $contactor, $firstSeries, $secondSeries , $ssd, $sed);
            }
            else {
                $this->data['datas'] = array();
            }
        }

        $this->data['filter']['total'] = $total = count($this->data['datas']);
        $this->data['filter']['offset'] = $offset = ($page -1) * $rows;

        if($total > 0) {
            $this->data['datas'] = $this->Person_list_model->
            getPersonListData($year, $schedule, $class_no, $contactor, $firstSeries, $secondSeries, $ssd, $sed, $rows, $offset);
        }

        $attrs = array(
            'conditions' => $conditions,
            'rows' => $rows,
            'offset' => $offset,
        );

        if(isset($_GET['iscsv'])  && $_GET['iscsv'] != 1  ) {
            $this->load->library('pagination');
            $config['base_url'] = base_url("search_work/person_list?". $this->getQueryString(array(), array('page')));
            $config['total_rows'] = $total;
            $config['per_page'] = $rows;
            $this->pagination->initialize($config);
        }
        
        
        if($year != ""){
            if(isset($_GET['iscsv'])  && $_GET['iscsv'] == 1  ){
                $this->Person_list_model->csvexport(date("Y-m-d"),$ssd,$sed,$year, $schedule, $class_no, $contactor, $firstSeries, $secondSeries);
            }
            else{
                $this->layout->view('search_work/person_list/list',$this->data);
            }
        }
        else{
            $this->layout->view('search_work/person_list/list',$this->data);   
        }

    }

}
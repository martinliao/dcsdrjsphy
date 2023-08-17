<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Bureau_person extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('search_work/Bureau_person_model');

        if (!isset($this->data['filter']['page'])) {
            $this->data['filter']['page'] = '1';
        }
    }
        
    

    public function index()
    { 
        $thisyear = date("Y")-1911;
        $year = isset($_GET['year'])?$_GET['year']:$thisyear;
        $schedule = isset($_GET['schedule'])?$_GET['schedule']:"";
        $class_no = isset($_GET['class_no'])?$_GET['class_no']:"";
        $contactor = isset($_GET['contactor'])?$_GET['contactor']:"";
        $type = isset($_GET['type'])?$_GET['type']:"";
        $season = isset($_GET['season'])?$_GET['season']:"";
        $startMonth = isset($_GET['startMonth'])?$_GET['startMonth']:"";
        $endMonth = isset($_GET['endMonth'])?$_GET['endMonth']:"";
        $start_date = isset($_GET['start_date'])?$_GET['start_date']:"";
        $end_date = isset($_GET['end_date'])?$_GET['end_date']:"";
        $ssd = $start_date;
        $sed = $end_date;

        $this->data['sess_year'] = $year;
        $this->data['sess_schedule'] = $schedule;
        $this->data['sess_class_no'] = $class_no;
        $this->data['sess_contactor'] = $contactor;
        $this->data['sess_season'] = $season;
        $this->data['sess_type'] = $type;
        $this->data['sess_startMonth'] = $startMonth;
        $this->data['sess_endMonth'] = $endMonth;
        $this->data['sess_start_date'] = $start_date;
        $this->data['sess_end_date'] = $end_date;

        $this->data['link_refresh'] = base_url("search_work/bureau_person/");

        $page = $this->data['filter']['page'];
        $rows = $this->data['filter']['rows'];

        $conditions = array();

        $attrs = array(
            'conditions' => $conditions,
        );

        if($type == 1 || $type == 2  ){
            $dateRange = $this->Bureau_person_model->getDataRange($year,$type,$season,$startMonth,$endMonth);
            $ssd =$dateRange[0]; 
            $sed =$dateRange[1]; 
        }
        else if($type == 0){
            if($year != ""){
                $dateRange = $this->Bureau_person_model->getOneYear($year);
                $ssd =$dateRange[0]; 
                $sed =$dateRange[1]; 
            } 
        }

        if($year != "") {
            $this->data['datas'] = $this->Bureau_person_model->
               getBureauPersonData($year, $schedule, $class_no, $contactor, $ssd, $sed);
        }
        else {
            $this->data['datas'] = array();
        }

        $this->data['filter']['total'] = $total = count($this->data['datas']);
        $this->data['filter']['offset'] = $offset = ($page -1) * $rows;

        if($total > 0) {
            $this->data['datas'] = $this->Bureau_person_model->
               getBureauPersonData($year, $schedule, $class_no, $contactor, $ssd, $sed, $rows, $offset);
        }

        $attrs = array(
            'conditions' => $conditions,
            'rows' => $rows,
            'offset' => $offset,
        );


        $this->load->library('pagination');
        $config['base_url'] = base_url("search_work/bureau_person?". $this->getQueryString(array(), array('page')));
        $config['total_rows'] = $total;
        $config['per_page'] = $rows;
        $this->pagination->initialize($config);

        if($year != ""){
            if(isset($_GET['iscsv'])  && $_GET['iscsv'] == 1  ){
                $this->Bureau_person_model->csvexport(date("Y-m-d"),$ssd,$sed,$year, $schedule, $class_no, $contactor);
            }
            else{
                $this->layout->view('search_work/bureau_person/list',$this->data);
            }
        }
        else{
            $this->layout->view('search_work/bureau_person/list',$this->data);
        }
    }

}
        
        
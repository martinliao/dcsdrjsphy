<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Course_count extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('statistics_paper/course_count_model');
        if (!isset($this->data['filter']['page'])) {
            $this->data['filter']['page'] = '1';
        }
    }

    public function index()
    {
        $thisyear = date("Y")-1911;
        $year = isset($_GET['year'])?addslashes($_GET['year']):$thisyear;
        $season = isset($_GET['season'])?addslashes($_GET['season']):"";
        $series = isset($_GET['series'])?addslashes($_GET['series']):"";
        $type = isset($_GET['type'])?addslashes($_GET['type']):"";
        $startMonth = isset($_GET['startMonth'])?addslashes($_GET['startMonth']):"";
        $endMonth = isset($_GET['endMonth'])?addslashes($_GET['endMonth']):"";
        $start_date = isset($_GET['start_date'])?addslashes($_GET['start_date']):"";
        $end_date = isset($_GET['end_date'])?addslashes($_GET['end_date']):"";
        $ssd = $start_date;
        $sed = $end_date;

        if($type == 1 || $type == 2  ){
            $dateRange = $this->course_count_model->getDataRange($year,$type,$season,$startMonth,$endMonth);
            $ssd =$dateRange[0]; 
            $sed =$dateRange[1]; 
        }
        else if($type == 0){
            if($year != ""){
                $dateRange = $this->course_count_model->getOneYear($year);
                $ssd =$dateRange[0]; 
                $sed =$dateRange[1]; 
            }
        }


        $this->load->library('pagination');
        $config['total_rows'] = 200;
        $config['per_page'] = 20;
        $this->pagination->initialize($config);
        $this->data['sess_year'] = $year;
        $this->data['sess_season'] = $season;
        $this->data['sess_series'] = $series;
        $this->data['sess_type'] = $type;
        $this->data['sess_startMonth'] = $startMonth;
        $this->data['sess_endMonth'] = $endMonth;
        $this->data['sess_start_date'] = $start_date;
        $this->data['sess_end_date'] = $end_date;
        $this->data['link_refresh'] = base_url("statistics_paper/course_count/");
        $this->data['datas'] = array();
        $page = $this->data['filter']['page'];
        $rows = $this->data['filter']['rows'];

        $conditions = array();

        $attrs = array(
            'conditions' => $conditions,
        );

        if($year != "") {
            $this->data['datas'] = $this->course_count_model->getCourseCountData($year,$ssd,$sed,$type,$series);
        }
        else {
            $this->data['datas'] =array();
        }

        if(isset($this->data['datas']))
            $this->data['filter']['total'] = $total = count($this->data['datas']);
        else
            $this->data['filter']['total'] = $total = 0;
        $this->data['filter']['offset'] = $offset = ($page -1) * $rows;

        if($total > 0) {
            $this->data['datas'] = $this->course_count_model->getCourseCountData($year,$ssd,$sed,$type,$series,$rows, $offset);
        }

        $attrs = array(
            'conditions' => $conditions,
            'rows' => $rows,
            'offset' => $offset,
        );


        $this->load->library('pagination');
        $config['base_url'] = base_url("statistics_paper/course_count?". $this->getQueryString(array(), array('page')));
        $config['total_rows'] = $total;
        $config['per_page'] = $rows;
        $this->pagination->initialize($config);

        if($year != ""){
            
            $this->data['dayOfWeek'] = $this->course_count_model->getDayOfWeek();
            if(isset($_GET['iscsv'])  && $_GET['iscsv'] == 1  ){
                $this->data['datas'] = $this->course_count_model->getCourseCountData($year,$ssd,$sed,$type,$series);
                $this->course_count_model->csvexport(date("Y-m-d"),$ssd,$sed,$series,$this->data['datas'],$this->data['dayOfWeek']);
            }
            else{
                $this->layout->view('statistics_paper/course_count/list',$this->data);
            }
        }
        else{
            $this->layout->view('statistics_paper/course_count/list',$this->data);
        }
        
    }

}

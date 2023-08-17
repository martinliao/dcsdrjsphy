<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Course_special_list extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('search_work/Course_special_list_model');

        if (!isset($this->data['filter']['page'])) {
            $this->data['filter']['page'] = '1';
        }
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

        if($type == 1 || $type == 2  ){
            $dateRange = $this->Course_special_list_model->getDataRange($year,$type,$season,$startMonth,$endMonth);
            $ssd =$dateRange[0]; 
            $sed =$dateRange[1]; 
        }
        else if($type == 0){
            if($year != ""){
                $dateRange = $this->Course_special_list_model->getOneYear($year);
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

        $page = $this->data['filter']['page'];
        $rows = $this->data['filter']['rows'];

        $conditions = array();

        $attrs = array(
            'conditions' => $conditions,
        );

        $this->data['datas'] = $this->Course_special_list_model->getCourseSpecialListData($ssd,$sed);

        $this->data['filter']['total'] = $total = count($this->data['datas']['rows']);
        $this->data['filter']['offset'] = $offset = ($page -1) * $rows;

        if($total > 0) {
            $this->data['datas'] = $this->Course_special_list_model->getCourseSpecialListData($ssd,$sed, $rows, $offset);
        }

        $attrs = array(
            'conditions' => $conditions,
            'rows' => $rows,
            'offset' => $offset,
        );

        $this->load->library('pagination');
        $config['base_url'] = base_url("search_work/course_special_list?". $this->getQueryString(array(), array('page')));
        $config['total_rows'] = $total;
        $config['per_page'] = $rows;
        $this->pagination->initialize($config);
        
        if($act!=""){
            if($act=='search'){
                $this->data['link_refresh'] = base_url("search_work/course_special_list/");
                $this->layout->view('search_work/course_special_list/list',$this->data);
            }
            if($act=='csv'){
                $this->data['result'] = $this->Course_special_list_model->exportCourseSpecialListData($ssd,$sed);
            }
        }else{
            $this->data['link_refresh'] = base_url("search_work/course_special_list/");
            $this->layout->view('search_work/course_special_list/list',$this->data);
        }
    }

}

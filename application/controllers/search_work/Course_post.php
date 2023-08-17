<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Course_post extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('search_work/Course_post_model');

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
        $month = isset($_GET['month'])?$_GET['month']:"";
        $firstSeries = isset($_GET['firstSeries'])?$_GET['firstSeries']:"";
        $secondSeries = isset($_GET['secondSeries'])?$_GET['secondSeries']:"";
        $start_date = isset($_GET['start_date'])?$_GET['start_date']:"";
        $end_date = isset($_GET['end_date'])?$_GET['end_date']:"";
        $checkArr = isset($_GET['checkArr'])?$_GET['checkArr']:"";
        if(isset($_GET['iscsv'])  && $_GET['iscsv'] == 1  ){
            $page = isset($_GET['page'])?$_GET['page']:1;
        }
        else {
            $page = $this->data['filter']['page'];
        }

        $this->data['sess_year'] = $year;
        $this->data['sess_season'] = $season;
        $this->data['sess_type'] = $type;
        $this->data['sess_month'] = $month;
        $this->data['sess_firstSeries'] = $firstSeries;
        $this->data['sess_secondSeries'] = $secondSeries;
        $this->data['sess_start_date'] = $start_date;
        $this->data['sess_end_date'] = $end_date;
        $this->data['sess_page'] = $page;

        $bureau_id = $secondSeries;
        $account = $this->flags->user['username'];
        $sort_filed ="";
        $sort_type = "";
        $this->data['link_refresh'] = base_url("search_work/course_post/");
        $this->data['category'] = $this->Course_post_model->getSeData();

        
        
        $rows = $this->data['filter']['rows'];

        if($year != "" && isset($_GET['iscsv'])  && $_GET['iscsv'] == 0) {
            $this->data['datas'] = $this->Course_post_model->getCoursePostData($year,$season,$firstSeries,$bureau_id,$month,$start_date,$end_date,$sort_filed,$sort_type);
        }
        else {
            $this->data['datas'] = array();
        }

        $conditions = array();

        $attrs = array(
            'conditions' => $conditions,
        );

        $this->data['filter']['total'] = $total = count($this->data['datas']);
        $this->data['filter']['offset'] = $offset = ($page -1) * $rows;

        if($total > 0) {
            $this->data['datas'] = $this->Course_post_model->getCoursePostData($year,$season,$firstSeries,$bureau_id,$month,$start_date,$end_date,$sort_filed,$sort_type, $rows, $offset);
        }

      

        $attrs = array(
            'conditions' => $conditions,
            'rows' => $rows,
            'offset' => $offset,
        );

        $this->load->library('pagination');
        $config['base_url'] = base_url("search_work/course_post?". $this->getQueryString(array(), array('page')));
        $config['total_rows'] = $total;
        $config['per_page'] = $rows;
        $this->pagination->initialize($config);

        if($year != ""){
            $this->data['dayOfWeek'] = $this->Course_post_model->getDayOfWeek();

            if(isset($_GET['iscsv'])  && $_GET['iscsv'] == 1 ){
                // $this->Course_post_model->csvexport($_GET['filename'],"","",$this->data['dayOfWeek'],$year,$season,$firstSeries,$bureau_id,$month,$start_date,$end_date,$sort_filed,$sort_type,$checkArr,$this->data['datas']);
                if (ctype_alnum(basename($_GET['filename']))){
                    $this->data['datas'] = $this->Course_post_model->getCoursePostData($year,$season,$firstSeries,$bureau_id,$month,$start_date,$end_date,$sort_filed,$sort_type);
                    $filename=str_replace(array("\r", "\n", "\r\n", "\n\r" , "%0a", "%0d"), "", basename($_GET['filename'])).".xls";
                    header("Content-type:application/vnd.ms-excel"); 
                    header("Content-Disposition:filename=".urlencode($filename));  
                }else{
                    $this->layout->view('search_work/course_post/list',$this->data);
                    exit();
                } 
                $this->load->view('search_work/course_post/exportView',$this->data);
            }
            else{
                $this->layout->view('search_work/course_post/list',$this->data);
            }
        }
        else{
            $this->layout->view('search_work/course_post/list',$this->data);
        }
    }

}

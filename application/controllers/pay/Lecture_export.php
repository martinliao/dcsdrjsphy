<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Lecture_export extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('pay/Lecture_export_model');

        if (!isset($this->data['filter']['page'])) {
            $this->data['filter']['page'] = '1';
        }
    }

    public function index()
    {
        $year = isset($_GET['year'])?$_GET['year']:date('Y')-1911;;
        $season = isset($_GET['season'])?$_GET['season']:"";
        $type = isset($_GET['type'])?$_GET['type']:"";
        $startMonth = isset($_GET['startMonth'])?$_GET['startMonth']:"";
        $endMonth = isset($_GET['endMonth'])?$_GET['endMonth']:"";
        $start_date = isset($_GET['start_date'])?$_GET['start_date']:"";
        $end_date = isset($_GET['end_date'])?$_GET['end_date']:"";
        $detailcheck = isset($_GET['sd'])?$_GET['sd']:"";
        $teachercheck = isset($_GET['st'])?$_GET['st']:"";
        $count = isset($_GET['count'])?$_GET['count']:"";
        $act = isset($_GET['act'])?$_GET['act']:"";
        $ssd = $start_date;
        $sed = $end_date;

        if($type == 1 || $type == 2  ){
            $dateRange = $this->Lecture_export_model->getDataRange($year,$type,$season,$startMonth,$endMonth);
            $ssd =$dateRange[0]; 
            $sed =$dateRange[1]; 
        }

        $this->data['sess_year'] = $year;
        $this->data['sess_season'] = $season;
        $this->data['sess_type'] = $type;
        $this->data['sess_startMonth'] = $startMonth;
        $this->data['sess_endMonth'] = $endMonth;
        $this->data['sess_start_date'] = $start_date;
        $this->data['sess_end_date'] = $end_date;
        $this->data['sess_d'] = $detailcheck;
        $this->data['sess_t'] = $teachercheck;
        $this->data['sess_count'] = $count;

        $page = $this->data['filter']['page'];
        $rows = $this->data['filter']['rows'];

        $conditions = array();

        $attrs = array(
            'conditions' => $conditions,
        );

        if($act != "" && $act == 'search'){
            $this->data['datas'] = $this->Lecture_export_model->getLectureExportData();
        }
        else {
            $this->data['datas'] = array();
        }
        
        $this->data['filter']['total'] = $total = count($this->data['datas']);
        $this->data['filter']['offset'] = $offset = ($page -1) * $rows;

        if($total > 0) {
            $this->data['datas'] = $this->Lecture_export_model->getLectureExportData($rows, $offset);
        }

        $attrs = array(
            'conditions' => $conditions,
            'rows' => $rows,
            'offset' => $offset,
        );

        $this->load->library('pagination');
        $config['base_url'] = base_url("pay/lecture_export?". $this->getQueryString(array(), array('page')));
        $config['total_rows'] = $total;
        $config['per_page'] = $rows;
        $this->pagination->initialize($config);
        
        
        if($act!=""){
            if($act=='search'){
                $this->data['link_refresh'] = base_url("pay/lecture_export/");
                $this->layout->view('pay/lecture_export/list',$this->data);
            }
            if($act=='csv'){
                $this->data['result'] = $this->Lecture_export_model->exportLectureExportData();
            }
        }else{
            $this->data['link_refresh'] = base_url("pay/lecture_export/");
            $this->layout->view('pay/lecture_export/list',$this->data);
        }
    }

    public function verify()
    {
        $name = isset($_GET['name'])?$_GET['name']:"";
        $id = isset($_GET['id'])?$_GET['id']:"";
        $code = isset($_GET['code'])?$_GET['code']:"";
        $uid = isset($_GET['uid'])?$_GET['uid']:"";
        $act = isset($_GET['act'])?$_GET['act']:"";

        $this->load->library('pagination');
        $config['total_rows'] = 200;
        $config['per_page'] = 20;
        $this->pagination->initialize($config);

        $this->data['result']=0;
        if($act!=null && $act=='update'){
            $this->data['result'] = $this->Lecture_export_model->updateLectureExportData($name,$id,$code,$uid);
        }
        $this->data['datas'] = $this->Lecture_export_model->getLectureExportDataById($uid);        
        $this->data['link_refresh'] = base_url("pay/lecture_export/verify");
        $this->layout->view('pay/lecture_export/verify',$this->data);
    }
    public function add()
    {
        $name = isset($_GET['name'])?$_GET['name']:"";
        $id = isset($_GET['id'])?$_GET['id']:"";
        $code = isset($_GET['code'])?$_GET['code']:"";
        $act = isset($_GET['act'])?$_GET['act']:"";

        $this->load->library('pagination');
        $config['total_rows'] = 200;
        $config['per_page'] = 20;
        $this->pagination->initialize($config);
        $this->data['sess_name'] = $name;
        $this->data['sess_id'] = $id;
        $this->data['sess_code'] = $code;

        $this->data['result']=0;
        if($act!=null && $act=='insert'){
            $this->data['result'] = $this->Lecture_export_model->insertLectureExportData($name,$id,$code);
        }

        $this->data['link_refresh'] = base_url("pay/lecture_export/add");
        $this->layout->view('pay/lecture_export/add',$this->data);
    }

}

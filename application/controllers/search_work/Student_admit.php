<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Student_admit extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('search_work/Student_admit_model');

        if (!isset($this->data['filter']['page'])) {
            $this->data['filter']['page'] = '1';
        }
    }

    public function index()
    {
        $thisyear = date("Y")-1911;
        $year = isset($_GET['year'])?$_GET['year']:$thisyear;
        $classno = isset($_GET['classno'])?$_GET['classno']:"";
        $type = isset($_GET['type'])?$_GET['type']:"";
        $classname = isset($_GET['classname'])?$_GET['classname']:"";
        $applyunit = isset($_GET['applyunit'])?$_GET['applyunit']:"";
        $act = isset($_GET['act'])?$_GET['act']:"";
        $p = isset($_GET['p'])?$_GET['p']:"";
        $action = "";

        $this->data['sess_year'] = $year;
        $this->data['sess_classno'] = $classno;
        $this->data['sess_type'] = $type;
        $this->data['sess_classname'] = $classname;
        $this->data['sess_applyunit'] = $applyunit;  //=   $block
        $this->data['sess_action'] = $action;
        $bureau_id = $this->flags->user['bureau_id'];
        $account = $this->flags->user['username'];
        $this->data['link_refresh'] = base_url("search_work/student_admit/");

        $page = $this->data['filter']['page'];
        $rows = $this->data['filter']['rows'];

        if($act=="search") {
            $this->data['datas'] = $this->Student_admit_model->getStudentAdmitData($account,$classno,$classname,$action,$type,$year,$bureau_id,$applyunit);
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
            $this->data['datas'] = $this->Student_admit_model->getStudentAdmitData($account,$classno,$classname,$action,$type,$year,$bureau_id,$applyunit, $rows, $offset);
        }

        $attrs = array(
            'conditions' => $conditions,
            'rows' => $rows,
            'offset' => $offset,
        );

        $this->load->library('pagination');
        $config['base_url'] = base_url("search_work/student_admit?". $this->getQueryString(array(), array('page')));
        $config['total_rows'] = $total;
        $config['per_page'] = $rows;
        $this->pagination->initialize($config);
        if($act=="detail"){
            $this->layout->view('search_work/student_admit/search10_detail.php',$this->data);
        }elseif($act=="schedule"){
            $this->data['link_refresh'] = base_url("search_work/student_admit");
            $this->data['term'] = isset($_GET['term'])?$_GET['term']:"";
            $this->data['tmp_seq'] = isset($_GET['tmp_seq'])?$_GET['tmp_seq']:"";
            $this->data['class_no'] = isset($_GET['class_no'])?$_GET['class_no']:"";
            $this->layout->view('search_work/student_query/Print_Class_Schedule_Detail.php',$this->data);
        }else{
            $this->layout->view('search_work/student_admit/list',$this->data);
        }
        
    }

}

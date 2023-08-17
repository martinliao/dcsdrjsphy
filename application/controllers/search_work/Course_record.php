<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Course_record extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('search_work/Course_record_model');
        if (!isset($this->data['filter']['page'])) {
            $this->data['filter']['page'] = '1';
        }
    }

    public function index()
    {
        $schedule = isset($_GET['nschedule'])?$_GET['nschedule']:"";
        $name = isset($_GET['nname'])?$_GET['nname']:"";
        $gender = isset($_GET['ngender'])?$_GET['ngender']:"";
        $id = isset($_GET['nid'])?$_GET['nid']:"";
        $location = isset($_GET['nlocation'])?$_GET['nlocation']:"";
        $birthday = isset($_GET['nbirthday'])?$_GET['nbirthday']:"";
        $classdate = isset($_GET['nclassdate'])?$_GET['nclassdate']:"";
        $act = isset($_GET['act'])?$_GET['act']:"";

        $this->load->library('pagination');
        $config['total_rows'] = 200;
        $config['per_page'] = 20;
        $this->pagination->initialize($config);

        $this->data['schedule']=$schedule;
        $this->data['name']=$name;
        $this->data['gender']=$gender;
        $this->data['id']=$id;
        $this->data['location']=$location;
        $this->data['birthday']=$birthday;
        $this->data['classdate']=$classdate;
        $this->data['datas'] = array();

        $page = $this->data['filter']['page'];
        $rows = $this->data['filter']['rows'];

        $conditions = array();

        $attrs = array(
            'conditions' => $conditions,
        );

        if($act!=""){
            if($act=='search'){ 
                $this->data['datas'] = $this->Course_record_model->getCourseRecordData($schedule,$name,$gender,$id,$location,$birthday,$classdate);
            }
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
            $this->data['datas'] = $this->Course_record_model->getCourseRecordData($schedule,$name,$gender,$id,$location,$birthday,$classdate, $rows, $offset);
        }

        $attrs = array(
            'conditions' => $conditions,
            'rows' => $rows,
            'offset' => $offset,
        );


        $this->load->library('pagination');
        $config['base_url'] = base_url("search_work/course_record?". $this->getQueryString(array(), array('page')));
        $config['total_rows'] = $total;
        $config['per_page'] = $rows;
        $this->pagination->initialize($config);


        if($act!=""){
            if($act=='search'){
                // $this->data['datas'] = $this->Course_record_model->getCourseRecordData($schedule,$name,$gender,$id,$location,$birthday,$classdate);
                $this->data['link_refresh'] = base_url("search_work/course_record/");
                $this->layout->view('search_work/course_record/list',$this->data);
            }
            if($act=='csv'){
                $this->data['result'] = $this->Course_record_model->exportCourseRecordData($schedule,$name,$gender,$id,$location,$birthday,$classdate);
            }
        }else{
            $this->data['link_refresh'] = base_url("search_work/course_record/");
            $this->layout->view('search_work/course_record/list',$this->data);
        }
    }

}

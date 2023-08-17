<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Student_query extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('search_work/Student_query_model');
        if(!isset($this->data['sess_class_start_date'])){
            $this->data['sess_class_start_date']=date('Y-m-d');
        }
        if(!isset($this->data['sess_class_end_date'])){
            $this->data['sess_class_end_date']=date('Y-m-d');
        }
    }

    public function index()
    {
        $thisyear = date("Y")-1911;
        $year = isset($_GET['year'])?addslashes($_GET['year']):$thisyear;
        $class_no = isset($_GET['class_no']) ? addslashes($_GET['class_no']) : "";
        $class_name = isset($_GET['class_name']) ? addslashes($_GET['class_name']) : "";
        $contactor = isset($_GET['contactor']) ? addslashes($_GET['contactor']) : "";
        $queryMix = isset($_GET['mix']) ? addslashes($_GET['mix']) : "";
        $preq = isset($_GET['preq']) ? addslashes($_GET['preq']) : "";
        $sort = isset($_GET['sort'])?addslashes($_GET['sort']):"";
        $act = isset($_GET['act'])?addslashes($_GET['act']):"";

        //開班日期
        $open_start_date = isset($_GET['open_start_date']) ? addslashes($_GET['open_start_date']) : "";
        $open_end_date = isset($_GET['open_end_date']) ? addslashes($_GET['open_end_date']) : "";
        //報名日期
        $apply_start_date = isset($_GET['apply_start_date']) ? addslashes($_GET['apply_start_date']) : "";
        $apply_end_date = isset($_GET['apply_end_date']) ? addslashes($_GET['apply_end_date']) : "";
        //上課日期
        //$class_start_date = isset($_GET['class_start_date']) ? $_GET['class_start_date'] : "";
       // $class_end_date = isset($_GET['class_end_date']) ? $_GET['class_end_date'] : "";
        $class_start_date=addslashes($this->input->get('class_start_date'));
        $class_end_date=addslashes($this->input->get('class_end_date'));
        $now_time=date('Y-m-d');

        if($act=='search'){
           if($this->input->get('class_start_date')==''){
                $this->data['sess_class_start_date']='';
            }else{
                $this->data['sess_class_start_date']=addslashes($this->input->get('class_start_date'));
            }

            if($this->input->get('class_end_date')==''){
                $this->data['sess_class_end_date']='';
            }else{
                $this->data['sess_class_end_date']=addslashes($this->input->get('class_end_date'));
            }
        }
        

        //var_dump($this->data['sess_class_start_date']);
        //var_dump($this->data['sess_class_start_date']);

        $this->load->library('pagination');
        $config['total_rows'] = 200;
        $config['per_page'] = 20;
        $this->pagination->initialize($config);

        $this->data['sess_year'] = $year;
        $this->data['sess_class_no'] = $class_no;
        $this->data['sess_class_name'] = $class_name;
        $this->data['sess_contactor'] = $contactor;
        $this->data['sess_mix'] = $queryMix;
        $this->data['sess_preq'] = $preq;
        $this->data['sess_open_start_date'] = $open_start_date;
        $this->data['sess_open_end_date'] = $open_end_date;
        $this->data['sess_apply_start_date'] = $apply_start_date;
        $this->data['sess_apply_end_date'] = $apply_end_date;
        //$this->data['sess_class_start_date'] = $class_start_date;
        //$this->data['sess_class_end_date'] = $class_end_date;
        $this->data['sess_sort'] = $sort;
        $this->data['query_contactor'] = $this->Student_query_model->getContactor();
        
        $has_rid = "N";
        if ((!empty($class_start_date) && !empty($class_end_date) && ($class_start_date == $class_end_date))|| $this->data['sess_class_end_date']==$this->data['sess_class_start_date']) {
            $has_rid = "Y";
        }

        $this->data['has_rid'] = $has_rid;

        $this->data['link_refresh'] = base_url("search_work/student_query/");

        $this->data['datas'] = array();
        $this->data['datas2'] = array();


       
            //if($act=='search'){
                $this->data['datas'] = $this->Student_query_model->getStudentQuery($year, $class_no, $class_name, $contactor, $queryMix, $preq, $open_start_date, $open_end_date, $apply_start_date, $apply_end_date,$this->data['sess_class_start_date'], $this->data['sess_class_end_date'],$sort);
                $this->data['datas2'] = $this->Student_query_model->getSdata2($year,$this->data['sess_class_start_date'], $this->data['sess_class_end_date']);

                


                //var_dump( $this->data['sess_class_start_date']);
                //$this->layout->view('search_work/student_query/list',$this->data);
            //}
         if($act!=""){
            if($act=='search'){
                //echo'hello';
                $this->layout->view('search_work/student_query/list',$this->data);
                //echo 'hello';
            }
            if (isset($_GET['iscsv'])  && $_GET['iscsv'] == 1) {
                $this->data['datas'] = $this->Student_query_model->getStudentQuery($year, $class_no, $class_name, $contactor, $queryMix, $preq, $open_start_date, $open_end_date, $apply_start_date, $apply_end_date, $class_start_date, $class_end_date,$sort);
                $this->Student_query_model->csvexport(date("Y-m-d"), $open_start_date, $open_end_date, $this->data['datas']);
                
	        }
            if($act=='detail'){
                $this->layout->view('search_work/student_query/Print_Class_Schedule_Detail',$this->data);
            }
            if($act=='dd'){
                $this->data['username']=$this->flags->user["username"];
                $this->layout->view('search_work/student_query/dd.pdf.php',$this->data);
            }

        }else{
            $this->data['link_refresh'] = base_url("search_work/student_query/");
            $this->layout->view('search_work/student_query/list',$this->data);

        }
    }
}

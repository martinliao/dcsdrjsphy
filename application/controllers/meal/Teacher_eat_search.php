<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Teacher_eat_search extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('meal/Teacher_eat_search_model');

        if (!isset($this->data['filter']['page'])) {
            $this->data['filter']['page'] = '1';
        }
    }

    public function index()
    {
        $classno = isset($_GET['classno'])?$_GET['classno']:"";
        $classname = isset($_GET['classname'])?$_GET['classname']:"";
        $start_date = isset($_GET['start_date'])?$_GET['start_date']:"";
        $end_date = isset($_GET['end_date'])?$_GET['end_date']:"";

        $this->data['sess_classno'] = $classno;
        $this->data['sess_classname'] = $classname;
        $this->data['sess_start_date'] = $start_date;
        $this->data['sess_end_date'] = $end_date;
        $this->data['link_refresh'] = base_url("meal/teacher_eat_search/");

        $page = $this->data['filter']['page'];
        $rows = $this->data['filter']['rows'];
        
        $counter = 0;
        if($classno!=""){
            $counter ++;
        }
        if($classname!=""){
            $counter ++;
        }
        if($start_date!=""){
            $counter ++;
        }
        if($end_date!=""){
            $counter ++;
        }

        if($counter != 0 ){
            $this->data['datas'] = $this->Teacher_eat_search_model->geTeacherEatSearch($classno, $classname, $start_date,$end_date);
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
            $this->data['datas'] = $this->Teacher_eat_search_model->geTeacherEatSearch($classno, $classname, $start_date,$end_date, $rows, $offset);
        }

        $attrs = array(
            'conditions' => $conditions,
            'rows' => $rows,
            'offset' => $offset,
        );

        $this->load->library('pagination');
        $config['base_url'] = base_url("meal/teacher_eat_search?". $this->getQueryString(array(), array('page')));
        $config['total_rows'] = $total;
        $config['per_page'] = $rows;
        $this->pagination->initialize($config);

        $this->layout->view('meal/teacher_eat_search/list',$this->data);
    }

}

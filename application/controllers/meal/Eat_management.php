<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Eat_management extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('meal/Eat_management_model');

        if (!isset($this->data['filter']['page'])) {
            $this->data['filter']['page'] = '1';
        }
    }

    // public function index()
    // {
    //     $classno = isset($_GET['classno'])?$_GET['classno']:"";
    //     $classname = isset($_GET['classname'])?$_GET['classname']:"";
    //     $start_date = isset($_GET['start_date'])?$_GET['start_date']:"";
    //     $end_date = isset($_GET['end_date'])?$_GET['end_date']:"";

    //     $this->load->library('pagination');
    //     $config['total_rows'] = 200;
    //     $config['per_page'] = 20;
    //     $this->pagination->initialize($config);
    //     $this->data['sess_classno'] = $classno;
    //     $this->data['sess_classname'] = $classname;
    //     $this->data['sess_start_date'] = $start_date;
    //     $this->data['sess_end_date'] = $end_date;
    //     $this->data['link_refresh'] = base_url("meal/eat_management/");

    //     if($start_date == "" || $end_date == "") {
    //         $this->data['datas'] = array();
    //     }
    //     else {
    //         $this->data['datas'] = $this->Eat_management_model->getEatCountSearch($classno, $classname, $start_date,$end_date);
    //     }

    //     $this->layout->view('meal/eat_management/list',$this->data);
    // }

    public function index()
    {
        $classno = isset($_GET['classno']) ? $_GET['classno'] : "";
        $classname = isset($_GET['classname']) ? $_GET['classname'] : "";
        $start_date = isset($_GET['start_date']) ? $_GET['start_date'] : "";
        $end_date = isset($_GET['end_date']) ? $_GET['end_date'] : "";
        $action = isset($_GET['action']) ? $_GET['action'] : "";

        $this->data['sess_classno'] = $classno;
        $this->data['sess_classname'] = $classname;
        $this->data['sess_start_date'] = $start_date;
        $this->data['sess_end_date'] = $end_date;

        $this->data['page_name'] = 'list';

        if ($action == "" || $action == "search") {
            $page = $this->data['filter']['page'];
            $rows = $this->data['filter']['rows'];

            $conditions = array();

            $attrs = array(
                'conditions' => $conditions,
            );

            if ($start_date == "" || $end_date == "") {
                $this->data['datas'] = array();
            } else {
                $this->data['datas'] = $this->Eat_management_model->getEatCountSearch($classno, $classname, $start_date, $end_date);
            }

            $this->data['filter']['total'] = $total = count($this->data['datas']);
            $this->data['filter']['offset'] = $offset = ($page - 1) * $rows;

            if ($total > 0) {
                $this->data['datas'] = $this->Eat_management_model->getEatCountSearch($classno, $classname, $start_date, $end_date, $rows, $offset);
            }

            $attrs = array(
                'conditions' => $conditions,
                'rows' => $rows,
                'offset' => $offset,
            );

            $this->load->library('pagination');
            $config['base_url'] = base_url("meal/eat_management?" . $this->getQueryString(array(), array('page')));
            $config['total_rows'] = $total;
            $config['per_page'] = $rows;
            $this->pagination->initialize($config);

            $this->data['link_refresh'] = base_url("meal/eat_management/");

            $this->layout->view('meal/eat_management/list', $this->data);
        }
        else if($action == "delete"){
            $id = isset($_GET['id']) ? $_GET['id'] : "";
            $result = $this->Eat_management_model->deleteDining_student($id);
            echo json_encode($result);
        }
        else if($action == "edit"){
            $id = isset($_GET['id']) ? $_GET['id'] : "";
            $this->data['datas']  = $this->Eat_management_model->selectDetailSql($id);
            // echo json_encode($result);
            $this->layout->view('meal/eat_management/detail', $this->data);
        }
        else if($action == "update"){
            $id = isset($_GET['id']) ? $_GET['id'] : "";
            $add1 = isset($_GET['add1']) ? $_GET['add1'] : "";
            $add2 = isset($_GET['add2']) ? $_GET['add2'] : "";
            $add3 = isset($_GET['add3']) ? $_GET['add3'] : "";
            $memo = isset($_GET['memo']) ? $_GET['memo'] : "";
            $account = $this->flags->user['username'];
            $result = $this->Eat_management_model->updateDetailSql($id,$add1,$add2,$add3,$memo,$account);
            echo json_encode($result);
        }

    }

}

<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Lecture_money_search extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('pay/Lecture_money_search_model');

        if (!isset($this->data['filter']['page'])) {
            $this->data['filter']['page'] = '1';
        }
    }

    public function index()
    {
        $teacher = isset($_GET['nteacher'])?$_GET['nteacher']:"";
        $id = isset($_GET['nid'])?$_GET['nid']:"";
        $start = isset($_GET['nstart'])?$_GET['nstart']:"";
        $end = isset($_GET['nend'])?$_GET['nend']:"";
        $perpage = isset($_GET['nperpage'])?$_GET['nperpage']:"";

        $this->data['sess_nteacher'] = $teacher;
        $this->data['sess_nid'] = $id;
        $this->data['sess_nstart'] = $start;
        $this->data['sess_nend'] = $end;
        $this->data['sess_nperpage'] = $perpage;

        $page = $this->data['filter']['page'];
        $rows = $this->data['filter']['rows'];
        $this->data['filter']['rows'] = $this->data['filter']['rows']==10?$this->data['filter']['rows']:10;

        if($start !="" && $end != ""){
            $this->data['datas'] = $this->Lecture_money_search_model->getLectureMoneySearchData($teacher, $id, $start, $end, $perpage);
            

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
            $this->data['datas'] = $this->Lecture_money_search_model->getLectureMoneySearchData($teacher, $id, $start, $end, $perpage, $rows, $offset);
        }

        $attrs = array(
            'conditions' => $conditions,
            'rows' => $rows,
            'offset' => $offset,
        );

        $this->load->library('pagination');
        $config['base_url'] = base_url("pay/lecture_money_search?". $this->getQueryString(array(), array('page')));
        $config['total_rows'] = $total;
        $config['per_page'] = $rows;
        $this->pagination->initialize($config);

        $this->data['link_refresh'] = base_url("pay/lecture_money_search/");
        $this->layout->view('pay/lecture_money_search/list',$this->data);
    }

    public function detail()
    {
        $teacher_id = isset($_GET['teacher_id'])?$_GET['teacher_id']:"";
        $this->data['datas'] = $this->Lecture_money_search_model->getTeacherData($teacher_id);
        $this->data['link_refresh'] = base_url("pay/lecture_money_search/detail");
        $this->layout->view('pay/lecture_money_search/detail',$this->data);
    }

}

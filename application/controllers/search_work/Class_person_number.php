<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Class_person_number extends MY_Controller
{
    public function __construct()
	{
		parent::__construct();
		
		$this->load->model('search_work/Class_person_number_model');
        
        //取得頁面與顯示筆數用
        // if (!isset($this->data['filter']['page'])) {
        //     $this->data['filter']['page'] = '1';
        // }
        // if (!isset($this->data['filter']['sort'])) {
        //     $this->data['filter']['sort'] = '';
        // }

	    if (!isset($this->data['filter']['query_type'])) {
            $this->data['filter']['query_type'] = '';
        }
        if (!isset($this->data['filter']['query_second'])) {
            $this->data['filter']['query_second'] = '';
        }
        if (!isset($this->data['filter']['page'])) {
            $this->data['filter']['page'] = '1';
        }
	}    
    



    public function index()
	{

        $this->data['choices']['query_type'] = $this->getSeriesCategory();
        $this->data['choices']['query_type'][''] = '請選擇系列別';
        $this->data['choices']['query_type'] = array_reverse($this->data['choices']['query_type']);

        $conditions = array();
		if ($this->data['filter']['query_type'] !== '' ) {
            $conditions['type'] = $this->data['filter']['query_type'];
            $this->data['choices']['query_second'] = $this->getSecondCategory($this->data['filter']['query_type']);
        }

        if ($this->data['filter']['query_second'] !== '' ) {
            $conditions['beaurau_id'] = $this->data['filter']['query_second'];
        }
		$attrs = array(
            'conditions' => $conditions,
        );

        //取得頁面與顯示筆數用
        //$this->data['filter']['total'] = $total = $this->setclass_model->getListCount($attrs,$this->data['user_bureau']);
        //$this->data['filter']['offset'] = $offset = ($page -1) * $rows;

        // $attrs = array(
        //     'conditions' => $conditions,
        //     'rows' => $rows,
        //     'offset' => $offset,
        // );

        //$this->data['list'] = $this->class_person_number_model->getList($attrs,$this->data['user_bureau']);

        $thisyear = date("Y")-1911;
        $year = isset($_GET['year'])?$_GET['year']:$thisyear;
        $minnumber = isset($_GET['minnumber'])?$_GET['minnumber']:"";
        $maxnumber = isset($_GET['maxnumber'])?$_GET['maxnumber']:"";
        $firstSeries = isset($_GET['firstSeries'])?$_GET['firstSeries']:"";
        $secondSeries = isset($_GET['secondSeries'])?$_GET['secondSeries']:"";
      
        $this->data['sess_year'] = $year;
        $this->data['sess_minnumber'] = $minnumber;
        $this->data['sess_maxnumber'] = $maxnumber;
        $this->data['sess_firstSeries'] = $firstSeries;
        $this->data['sess_secondSeries'] = $secondSeries;
        
        $this->data['link_get_second_category'] = base_url("search_work/class_person_number/getSecondCategory");
        $this->data['link_refresh'] = base_url("search_work/class_person_number/");

        $page = $this->data['filter']['page'];
        $rows = $this->data['filter']['rows'];

        if($year != "") {
            $this->data['datas'] = $this->Class_person_number_model->getClassPersonNumberData($year,$minnumber,$maxnumber,$firstSeries,$secondSeries);
        }
        else {
            $this->data['datas'] = array();
        }

        $this->data['filter']['total'] = $total = count($this->data['datas']);
        $this->data['filter']['offset'] = $offset = ($page -1) * $rows;

        if($total > 0) {
            $this->data['datas'] = $this->Class_person_number_model->getClassPersonNumberData($year,$minnumber,$maxnumber,$firstSeries,$secondSeries, $rows, $offset);
        }

        $attrs = array(
            'conditions' => $conditions,
            'rows' => $rows,
            'offset' => $offset,
        );

        $this->load->library('pagination');
        $config['base_url'] = base_url("search_work/class_person_number?". $this->getQueryString(array(), array('page')));
        $config['total_rows'] = $total;
        $config['per_page'] = $rows;
        $this->pagination->initialize($config);

        if($year != ""){ 
            if(isset($_GET['iscsv'])  && $_GET['iscsv'] == 1  ){
                $this->Class_person_number_model->csvexport(date("Y-m-d"),$year,$minnumber,$maxnumber,$firstSeries,$secondSeries);
            }
            else{
                $this->layout->view('search_work/class_person_number/list',$this->data);
            }
        }
        else{
            $this->layout->view('search_work/class_person_number/list',$this->data);
        }
    }   
}
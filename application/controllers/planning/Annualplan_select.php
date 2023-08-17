<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Annualplan_select extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();

		if ($this->flags->is_login === FALSE) {
			redirect(base_url('welcome'));
		}
		$this->load->model('planning/annualplan_model');
        $this->load->model('planning/set_startdate_model');

        if (!isset($this->data['filter']['page'])) {
            $this->data['filter']['page'] = '1';
        }
        if (!isset($this->data['filter']['sort'])) {
            $this->data['filter']['sort'] = '';
        }
        if (!isset($this->data['filter']['q'])) {
            $this->data['filter']['q'] = '';
        }
        if (!isset($this->data['filter']['query_year'])) {
            $this->data['filter']['query_year'] = date('Y')-1911;
        }
        if (!isset($this->data['filter']['query_class_no'])) {
            $this->data['filter']['query_class_no'] = '';
        }
        if (!isset($this->data['filter']['class_status_search'])) {
            $this->data['filter']['class_status_search'] = '';
        }
        
        if (!isset($this->data['filter']['query_season'])) {
            $this->data['filter']['query_season'] = '';
        }
        if (!isset($this->data['filter']['query_month_start'])) {
            $this->data['filter']['query_month_start'] = '';
        }
        if (!isset($this->data['filter']['query_type'])) {
            $this->data['filter']['query_type'] = '';
        }
        if (!isset($this->data['filter']['query_second'])) {
            $this->data['filter']['query_second'] = '';
        }
	}

	public function index()
	{
		if ($post = $this->input->post()) {
            $chkUpdate = FALSE;
			for($i=0;$i<count($post['rowid']);$i++){
				$parameter = explode(',',$post['rowid'][$i]);
				$chkUpdate = $this->annualplan_model->updateClassStatus($parameter[0],$parameter[1],$post['plan_status']);
			}

            if($chkUpdate){
                $this->setAlert(2, '計畫修改成功');
            } 

			redirect(base_url("planning/annualplan_select/?{$_SERVER['QUERY_STRING']}"));
		}

		$this->data['page_name'] = 'list';
        $this->data['choices']['query_type'] = $this->getSeriesCategory();
        $this->data['choices']['query_type'][''] = '請選擇系列別';
        $this->data['choices']['class_status_search'] = array(''=>'請選擇計畫','1' => '草案','2' => '確定計畫','3' => '新增計畫');

        $conditions = array();
        $page = $this->data['filter']['page'];
        $rows = $this->data['filter']['rows'];
        $this->data['choices']['class_status'] = array(''=>'請選擇計畫','1' => '草案','2' => '確定計畫','3' => '新增計畫');

        if ($this->data['filter']['q'] !== '' ) {
            $attrs['q'] = $this->data['filter']['q'];
        }
        if ($this->data['filter']['query_year'] !== '' ) {
            $conditions['year'] = $this->data['filter']['query_year'];
        }

        if ($this->data['filter']['class_status_search'] !== '' ) {
            $conditions['class_status'] = $this->data['filter']['class_status_search'];
        }

        if ($this->data['filter']['query_class_no'] !== '' ) {
            $conditions['class_no'] = $this->data['filter']['query_class_no'];
        }

        if ($this->data['filter']['query_season'] !== '' ) {
            $conditions['reason'] = $this->data['filter']['query_season'];
        }

        if ($this->data['filter']['query_month_start'] !== '' ) {
            $conditions['start_date1 >='] = ($this->data['filter']['query_year']+1911).'-'.$this->data['filter']['query_month_start'].'-01';
            $first_day = ($this->data['filter']['query_year']+1911).'-'.$this->data['filter']['query_month_start'].'-01';
            $last_day = date('Y-m-d', strtotime("$first_day +1 month -1 day"));
            $conditions['start_date1 <='] = $last_day;
        }
        if ($this->data['filter']['query_type'] !== '' ) {
            $conditions['type'] = $this->data['filter']['query_type'];
            $this->data['choices']['query_second'] = $this->set_startdate_model->getSecondCategory($this->data['filter']['query_type']);
        }

        if ($this->data['filter']['query_second'] !== '' ) {
            $conditions['beaurau_id'] = $this->data['filter']['query_second'];
        }
       
        $attrs = array(
            'conditions' => $conditions,
        );

        $this->data['filter']['total'] = $total = $this->annualplan_model->getListCount($attrs);
        $this->data['filter']['offset'] = $offset = ($page -1) * $rows;

        $attrs = array(
            'conditions' => $conditions,
            'rows' => $rows,
            'offset' => $offset,
        );
        if ($this->data['filter']['q'] !== '' ) {
            $attrs['q'] = $this->data['filter']['q'];
        }
        if ($this->data['filter']['sort'] !== '' ) {
            $attrs['sort'] = $this->data['filter']['sort'];
        }
        if ($this->data['filter']['query_month_start'] !== '' ) {
            $conditions['start_date1 >='] = ($this->data['filter']['query_year']+1911).'-'.$this->data['filter']['query_month_start'].'-01';
            $first_day = ($this->data['filter']['query_year']+1911).'-'.$this->data['filter']['query_month_start'].'-01';
            $last_day = date('Y-m-d', strtotime("$first_day +1 month -1 day"));
            $conditions['start_date1 <='] = $last_day;
        }

        if ($this->data['filter']['class_status_search'] !== '' ) {
            $conditions['class_status'] = $this->data['filter']['class_status_search'];
        }

        if($this->input->get()){
            $this->data['list'] = $this->annualplan_model->getList($attrs);
            for($i=0;$i<count($this->data['list']);$i++){
                    $base_term = $this->annualplan_model->getBaseTerm($this->data['list'][$i]['year'],$this->data['list'][$i]['class_no']);
                    $this->data['list'][$i]['base_term'] = $base_term;
            }
            
            $config['total_rows'] = $total;
            $config['per_page'] = $rows;
        } else {
            $this->data['list'] = array();
            $this->data['filter']['total'] = 0;
            $config['total_rows'] = 0;
            $config['per_page'] = $rows;
        }
		
		$this->load->library('pagination');
        $config['base_url'] = base_url("planning/annualplan_select?". $this->getQueryString(array(), array('page')));
        $this->pagination->initialize($config);
        $this->data['link_get_second_category'] = base_url("planning/set_startdate/getSecondCategory");
        $this->data['link_set_base_term'] = base_url("planning/annualplan_select/setBaseTerm");
		// $this->data['link_confirm'] = '';
		$this->data['link_refresh'] = base_url("planning/annualplan_select/");

		$this->layout->view('planning/annualplan/list', $this->data);
	}

    function setBaseTerm(){
        $year = $this->input->post('year');

        if(!empty($year)){
            $check = $this->annualplan_model->checkBaseTermExist($year);
            if(!$check){
                $status = $this->annualplan_model->setBaseTerm($year);

                if($status){
                    echo 'OK';
                    exit;
                }
            } else {
                echo 'EXIST';
                exit;
            }
        }

        echo 'error';
    }
}
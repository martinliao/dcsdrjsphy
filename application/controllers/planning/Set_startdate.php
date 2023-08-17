<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Set_startdate extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();

		if ($this->flags->is_login === FALSE) {
			redirect(base_url('welcome'));
		}
		$this->load->model('planning/set_startdate_model');
	
        if (!isset($this->data['filter']['page'])) {
            $this->data['filter']['page'] = '1';
        }
        if (!isset($this->data['filter']['sort'])) {
            $this->data['filter']['sort'] = '';
        }
        if (!isset($this->data['filter']['query_year'])) {
            $this->data['filter']['query_year'] = date('Y')-1911;
        }
        if (!isset($this->data['filter']['query_class_no'])) {
            $this->data['filter']['query_class_no'] = '';
        }
        if (!isset($this->data['filter']['query_class_name'])) {
            $this->data['filter']['query_class_name'] = '';
        }
        if (!isset($this->data['filter']['query_season'])) {
            $this->data['filter']['query_season'] = '';
        }
        if (!isset($this->data['filter']['query_month_start'])) {
            $this->data['filter']['query_month_start'] = '';
        }
        if (!isset($this->data['filter']['query_month_end'])) {
            $this->data['filter']['query_month_end'] = '';
        }
        if (!isset($this->data['filter']['query_start_date'])) {
            $this->data['filter']['query_start_date'] = '';
        }
        if (!isset($this->data['filter']['query_end_date'])) {
            $this->data['filter']['query_end_date'] = '';
        }
        if (!isset($this->data['filter']['query_type'])) {
            $this->data['filter']['query_type'] = '';
        }
        if (!isset($this->data['filter']['query_second'])) {
            $this->data['filter']['query_second'] = '';
        }
        if (!isset($this->data['filter']['query_min_term'])) {
            $this->data['filter']['query_min_term'] = '';
        }
        if (!isset($this->data['filter']['query_startdate_setup'])) {
            $this->data['filter']['query_startdate_setup'] = '';
        }
	}

	public function index()
	{
		if ($post = $this->input->post()) {
			for($i=0;$i<count($post['rowid']);$i++){
				if(!empty($post['batch_start_date'])){
                    $this->set_startdate_model->updateStartDate($post['rowid'][$i],$post['batch_start_date']);
                } else {
                    $tmp_start_key = 'start_date_'.$post['rowid'][$i];
                    if(!empty($post[$tmp_start_key])){
                        $this->set_startdate_model->updateStartDate($post['rowid'][$i],$post[$tmp_start_key]);
                    }
                }

                if(!empty($post['batch_end_date'])){
                    $this->set_startdate_model->updateEndDate($post['rowid'][$i],$post['batch_end_date']);
                } else {
                    $tmp_end_key = 'end_date_'.$post['rowid'][$i];
                    if(!empty($post[$tmp_end_key])){
                        $this->set_startdate_model->updateEndDate($post['rowid'][$i],$post[$tmp_end_key]);
                    }
                }
			}
			$this->setAlert(2, '日期修改成功');

			redirect(base_url("planning/set_startdate/?{$_SERVER['QUERY_STRING']}"));
		}

		$this->data['page_name'] = 'list';
        $this->data['choices']['query_type'] = $this->getSeriesCategory();
        $this->data['choices']['query_type'][''] = '請選擇系列別';

        $conditions = array();

        if ($this->data['filter']['query_year'] !== '' ) {
            $conditions['year'] = $this->data['filter']['query_year'];
        }

        if ($this->data['filter']['query_class_no'] !== '' ) {
            $conditions['class_no'] = $this->data['filter']['query_class_no'];
        }

        if ($this->data['filter']['query_season'] !== '' ) {
            $conditions['reason'] = $this->data['filter']['query_season'];
        }

        if ($this->data['filter']['query_month_start'] !== '' ) {
            $conditions['start_date1 >='] = ($this->data['filter']['query_year']+1911).'-'.$this->data['filter']['query_month_start'].'-01';
           
        }

        if ($this->data['filter']['query_month_end'] !== '' ) {
            $first_day = ($this->data['filter']['query_year']+1911).'-'.$this->data['filter']['query_month_end'].'-01';
            $last_day = date('Y-m-d', strtotime("$first_day +1 month -1 day"));

            $conditions['start_date1 <='] = $last_day;
        }

        if ($this->data['filter']['query_start_date'] !== '' ) {
            $conditions['start_date1 >='] = $this->data['filter']['query_start_date'];
        }

        if ($this->data['filter']['query_end_date'] !== '' ) {
            $conditions['start_date1 <='] = $this->data['filter']['query_end_date'];
        }

        if ($this->data['filter']['query_type'] !== '' ) {
            $conditions['type'] = $this->data['filter']['query_type'];
            $this->data['choices']['query_second'] = $this->set_startdate_model->getSecondCategory($this->data['filter']['query_type']);
        }

        if ($this->data['filter']['query_second'] !== '' ) {
            $conditions['beaurau_id'] = $this->data['filter']['query_second'];
        }

        if ($this->data['filter']['query_startdate_setup'] == '1' ) {
            $conditions['start_date1 is null'] = null;
            $conditions['end_date1 is null'] = null;
        }

        $page = $this->data['filter']['page'];
        $rows = $this->data['filter']['rows'];

		$attrs = array(
            'conditions' => $conditions,
        );
        
        if ($this->data['filter']['query_class_name'] !== '' ) {
            $attrs['query_class_name'] = addslashes($this->input->get('query_class_name'));
        }

        if ($this->data['filter']['query_min_term'] !== '' ) {
            $attrs['query_min_term'] = $this->data['filter']['query_min_term'];
        }

        $this->data['filter']['total'] = $total = $this->set_startdate_model->getListCount($attrs);
        $this->data['filter']['offset'] = $offset = ($page -1) * $rows;

        $attrs = array(
            'conditions' => $conditions,
            'rows' => $rows,
            'offset' => $offset,
        );
        
        if ($this->data['filter']['sort'] !== '' ) {
            $attrs['sort'] = $this->data['filter']['sort'];
        }

        if ($this->data['filter']['query_class_name'] !== '' ) {
            $attrs['query_class_name'] = addslashes($this->input->get('query_class_name'));
        }

        if ($this->data['filter']['query_min_term'] !== '' ) {
            $attrs['query_min_term'] = $this->data['filter']['query_min_term'];
        }
        
		$this->data['list'] = $this->set_startdate_model->getList($attrs);
		$this->load->library('pagination');
        $config['base_url'] = base_url("planning/set_startdate?". $this->getQueryString(array(), array('page')));
        $config['total_rows'] = $total;
        $config['per_page'] = $rows;
        $this->pagination->initialize($config);
        $this->data['link_get_second_category'] = base_url("planning/set_startdate/getSecondCategory");
		$this->data['link_confirm'] = '';
		$this->data['link_refresh'] = base_url("planning/set_startdate/");
       
		$this->layout->view('planning/set_startdate/list', $this->data);
	}
}
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Modify_terms extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();

		if ($this->flags->is_login === FALSE) {
			redirect(base_url('welcome'));
		}
		$this->load->model('planning/modify_terms_model');
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
            $this->data['filter']['query_year'] = '';
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
		$this->data['page_name'] = 'list';
        $this->data['user_bureau'] = $this->flags->user['bureau_id'];
        $this->data['choices']['query_type'] = $this->getSeriesCategory();
        $this->data['choices']['query_type'][''] = '請選擇系列別';

        if (empty($this->input->get())){
            $this->data['list'] = [];
        }else{

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

            $page = $this->data['filter']['page'];
            $rows = $this->data['filter']['rows'];
            
    		$attrs = array(
                'conditions' => $conditions,
            );
            if ($this->data['filter']['q'] !== '' ) {
                $attrs['q'] = $this->data['filter']['q'];
            }
            if ($this->data['filter']['query_class_name'] !== '' ) {
                $attrs['query_class_name'] = $this->data['filter']['query_class_name'];
            }

            $this->data['filter']['total'] = $total = $this->modify_terms_model->getListCount($attrs,$this->data['user_bureau']);
            $this->data['filter']['offset'] = $offset = ($page -1) * $rows;

            $attrs = array(
                'conditions' => $conditions,
                'rows' => $rows,
                'offset' => $offset,
            );
            if ($this->data['filter']['query_class_name'] !== '' ) {
                $attrs['query_class_name'] = $this->data['filter']['query_class_name'];
            }
            if ($this->data['filter']['q'] !== '' ) {
                $attrs['q'] = $this->data['filter']['q'];
            }
            if ($this->data['filter']['sort'] !== '' ) {
                $attrs['sort'] = $this->data['filter']['sort'];
            }

            $this->data['list'] = $this->modify_terms_model->getList($attrs,$this->data['user_bureau']);
            foreach ($this->data['list'] as & $row) {
                $row['base_term'] = $this->modify_terms_model->getBaseTerm($row['year'],$row['class_no']);
                $row['link_add'] = base_url("planning/modify_terms/add/{$row['seq_no']}/?{$_SERVER['QUERY_STRING']}");
                $row['link_del'] = base_url("planning/modify_terms/delete/{$row['seq_no']}/?{$_SERVER['QUERY_STRING']}");
                $row['link_cancel_class'] = base_url("planning/modify_terms/cancel_class/{$row['seq_no']}/?{$_SERVER['QUERY_STRING']}");
            }
        }

		
		$this->load->library('pagination');
        $config['base_url'] = base_url("planning/modify_terms?". $this->getQueryString(array(), array('page')));
        $this->data['link_get_second_category'] = base_url("planning/set_startdate/getSecondCategory");
        $config['total_rows'] = $total;
        $config['per_page'] = $rows;
        $this->pagination->initialize($config);

        $this->data['link_refresh'] = base_url("planning/modify_terms/");
        
		$this->layout->view('planning/modifyterms/list', $this->data);
	}

    public function add($id=NULL)
    {
        if ($post = $this->input->post()) {
            $result = FALSE; 
            if (isset($post['add']) && intval($post['add']) > 0) {
                $result = $this->modify_terms_model->addClassTerms($post);
            } else if(isset($post['insert']) && intval($post['insert']) > 0){
                $result = $this->modify_terms_model->insertClassTerms($post);
            }

            if ($result) {
                $this->setAlert(1, '資料新增成功');
            } else {
                $this->setAlert(1, '資料新增失敗');
            }

            redirect(base_url("planning/modify_terms/add/{$id}/?"));
        }

        $this->data['page_name'] = 'add';
        $this->data['form'] = $this->modify_terms_model->getClassData($id);
        $this->data['choices']['class_status'] = array('1' => '草案','2' => '確定計畫','3' => '新增計畫');

        $this->data['link_save'] = base_url("planning/modify_terms/add/{$id}/?{$_SERVER['QUERY_STRING']}");
        $this->data['link_cancel'] = base_url("planning/modify_terms/?{$_SERVER['QUERY_STRING']}");
        $this->layout->view('planning/modifyterms/add', $this->data);
    }

    public function delete($id=NULL)
    {
        if ($post = $this->input->post()) {
            $result = FALSE; 
            $result = $this->modify_terms_model->deleteClassTerms($post);
    
            $this->setAlert(1, $result);
            redirect(base_url("planning/modify_terms/delete/{$id}/?"));
        }

        $this->data['page_name'] = 'delete';
        $this->data['form'] = $this->modify_terms_model->getClassData($id);
        $this->data['link_save_delete'] = base_url("planning/modify_terms/delete/{$id}/?{$_SERVER['QUERY_STRING']}");
        $this->data['link_cancel'] = base_url("planning/modify_terms/?{$_SERVER['QUERY_STRING']}");
        $this->layout->view('planning/modifyterms/delete', $this->data);
    }

    public function cancel_class($id=null)
    {
        if ($post = $this->input->post()) {
            $result = FALSE; 
            $result = $this->modify_terms_model->cancel_class($post);
            
            $this->setAlert(1, $result);
            redirect(base_url("planning/modify_terms/cancel_class/{$id}/?"));
        }

        $this->data['page_name'] = 'cancel_class';
        $this->data['form'] = $this->modify_terms_model->getClassData($id,'cancel_class');
        $this->data['cancel_class_form'] = $this->modify_terms_model->getClassData($id);
        $this->data['link_save_cancel'] = base_url("planning/modify_terms/cancel_class/{$id}/?{$_SERVER['QUERY_STRING']}");
        $this->data['link_cancel'] = base_url("planning/modify_terms/?{$_SERVER['QUERY_STRING']}");
        $this->layout->view('planning/modifyterms/delete', $this->data);
    }

}
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Month_course extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        if ($this->flags->is_login === FALSE) {
			redirect(base_url('welcome'));
		}
		$this->load->model('planning/month_course_model');
        $this->load->model('planning/course_introduct_model');

        if (!isset($this->data['filter']['page'])) {
            $this->data['filter']['page'] = '1';
        }
        
        if (!isset($this->data['filter']['query_year'])) {
            $this->data['filter']['query_year'] = date('Y')-1911;
        }
        if (!isset($this->data['filter']['status'])) {
            $this->data['filter']['status'] = '';
        }
        
        if (!isset($this->data['filter']['query_type'])) {
            $this->data['filter']['query_type'] = '';
        }
        if (!isset($this->data['filter']['query_second'])) {
            $this->data['filter']['query_second'] = '';
        }
        if (!isset($this->data['filter']['query_month_start'])) {
            $this->data['filter']['query_month_start'] = '';
        }
        if (!isset($this->data['filter']['query_month_end'])) {
            $this->data['filter']['query_month_end'] = '';
        }
       
    }

    public function index()
	{
		$this->data['page_name'] = 'list';
        $this->data['choices']['query_type'] = $this->getSeriesCategory();
        $this->data['choices']['query_type'][''] = '請選擇系列別';
        $conditions = array();
		$this->load->library('pagination');
        $config['base_url'] = base_url("planning/season_course_capture?". $this->getQueryString(array(), array('page')));
        $this->data['link_detail']= base_url("planning/month_course/detail");
        $this->pagination->initialize($config);
        $this->data['link_get_second_category'] = base_url("planning/season_schedule/getSecondCategory");
		$this->data['link_confirm'] = '';
        $this->data['link_refresh'] = base_url("planning/month_course/");      
		$this->layout->view('planning/month_course/list', $this->data);
    }
    public function detail()
    {
        $this->data['choices']['query_type'] = $this->getSeriesCategory();
        $this->data['choices']['query_type'][''] = '請選擇系列別';
        $conditions = array();

        
        if ($this->data['filter']['query_year'] !== '' ) {
            $conditions['require.year'] = $this->data['filter']['query_year'];
            $condition['require.year'] = $this->data['filter']['query_year'];
        }
       
        if ($this->data['filter']['query_month_start'] !== '' ) {
            $conditions['start_date1 >='] = ($this->data['filter']['query_year']+1911).'-'.$this->data['filter']['query_month_start'].'-01';
            $condition['start_date1'] =  ($this->data['filter']['query_year']+1911).'-'.$this->data['filter']['query_month_start'].'-01';
        }else{
            echo "<script>
            alert('請選擇起始月份');
            </script>" ;
            redirect('planning/month_course/','refresh');
        }

        if($this->data['filter']['status']!='')
        {   
            $status=$this->data['filter']['status'];
            switch($status) {
                case "1":
                    
                    $condition['statusSql'] = "apply_s_date is not null and apply_e_date is not null";
                    break;
                case "2":
                    
                    $condition['statusSql'] = "apply_s_date2 is not null and apply_e_date2 is not null";
                    break;
                case "1and2":
                    $condition['statusSql'] = "((apply_s_date != 'null') and (apply_e_date != 'null')) or ((apply_s_date2 !='null') and (apply_e_date2 != 'null'))";

                    break;
                case "0":
                    $condition['statusSql'] = "apply_s_date is null and apply_s_date2 is null";
                    break;
                default:
                    break;
            }
        }

        if ($this->data['filter']['query_month_end'] !== '' ) {
            $first_day = ($this->data['filter']['query_year']+1911).'-'.$this->data['filter']['query_month_end'].'-01';
            $last_day = date('Y-m-d', strtotime("$first_day +1 month -1 day"));
            $conditions['start_date1 <='] = $last_day;
            $condition['end_date'] = $last_day;
            }else{
            echo "<script>
            alert('請選擇結束月份');
            </script>" ;
            redirect('planning/month_course/','refresh');
        }

        if ($this->data['filter']['query_type'] !== '' ) {
            $conditions['require.type'] = $this->data['filter']['query_type'];
            $condition['type'] = $this->data['filter']['query_type'];
            $this->data['choices']['query_second'] = $this->course_introduct_model->getSecondCategory($this->data['filter']['query_type']);
        }else{
            echo "<script>
            alert('請選擇系列別');
            </script>" ;
            redirect('planning/month_course/','refresh');
        }

        if ($this->data['filter']['query_second'] !== '' ) {
            $conditions['beaurau_id'] = $this->data['filter']['query_second'];
            $condition['beaurau_id'] = $this->data['filter']['query_second'];
            //$conditions['sc.item_id'] = $this->data['filter']['query_second'];//2019-11-25
            //$condition['sc.item_id'] = $this->data['filter']['query_second'];//2019-11-25

        }
        
        $page = $this->data['filter']['page'];
        $rows = $this->data['filter']['rows'];

		$attrs = array(
            'conditions' => $conditions,
        );
        $query=$this->input->post();
       
        $this->data['filter']['total'] = $total = $this->month_course_model->getListCount($attrs);
        $this->data['filter']['offset'] = $offset = ($page -1) * $rows;

        $attrs = array(
            'conditions' => $conditions,
            'rows' => $rows,
            'offset' => $offset,
            'class'=>$condition,
        );

        $this->data['list'] = $this->month_course_model->getList($attrs);

        $this->data['bureauCount']=$this->month_course_model->getTotalSignUp($condition);
        $this->data['maxsignup']=$this->month_course_model->maxSignUp($condition);
        $this->data['maxterm']=$this->month_course_model->maxTerm($condition);
        $this->data['maxpeople']=$this->month_course_model->maxPeople($condition);

        if ($this->data['filter']['query_month_start'] !== '' ) {
            $this->data['show'][0]['month_start'] = $this->data['filter']['query_month_start'];
        }
        if ($this->data['filter']['query_month_end'] !== '' ) {
            $this->data['show'][0]['month_end'] = $this->data['filter']['query_month_end'];
        }
        if ($this->data['filter']['query_month_end'] !== '' ) {
            $this->data['show'][0]['show_year'] = $this->data['filter']['query_year'];
        }
        
        $this->data['link_refresh'] = base_url("planning/month_course/detail?{$_SERVER['QUERY_STRING']}");      
        $this->layout->view('planning/month_course/export', $this->data);

    }
    

}

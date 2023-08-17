<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Season_course_capture extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        if ($this->flags->is_login === FALSE) {
			redirect(base_url('welcome'));
		}
		$this->load->model('planning/season_course_capture_model');
        $this->load->model('planning/course_introduct_model');

        if (!isset($this->data['filter']['page'])) {
            $this->data['filter']['page'] = '1';
        }
        if (!isset($this->data['filter']['sort'])) {
            $this->data['filter']['sort'] = '';
        }
        if (!isset($this->data['filter']['query_sort'])) {
            $this->data['filter']['query_sort'] = '';
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
        if (!isset($this->data['filter']['query_class_name'])) {
            $this->data['filter']['query_class_name'] = '';
        }
        if (!isset($this->data['filter']['query_season'])) {
            $this->data['filter']['query_season'] = '';
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
        if (!isset($this->data['filter']['show'])) {
            $this->data['filter']['show'] = '';
        }
        
        
    }

    public function index()
	{
		$this->data['page_name'] = 'list';
        $this->data['choices']['query_type'] = $this->getSeriesCategory();
        $this->data['choices']['query_type'][''] = '請選擇系列別';
        $conditions = array();
       
      

        if ($this->data['filter']['query_season'] !== '' ) {
            $conditions['reason'] = $this->data['filter']['query_season'];
        }
        if ($this->data['filter']['query_year'] !== '' ) {
            $conditions['year'] = $this->data['filter']['query_year'];
        }
       
        if ($this->data['filter']['query_month_start'] !== '' ) {
            $conditions['start_date1 >='] = ($this->data['filter']['query_year']+1911).'-'.$this->data['filter']['query_month_start'].'-01';
            $first_day = ($this->data['filter']['query_year']+1911).'-'.$this->data['filter']['query_month_start'].'-01';
            $last_day = date('Y-m-d', strtotime("$first_day +1 month -1 day"));
            $conditions['start_date1 <='] = $last_day;
        }
        if ($this->data['filter']['query_type'] !== '' ) {
            $conditions['require.type'] = $this->data['filter']['query_type'];
            $this->data['choices']['query_second'] = $this->course_introduct_model->getSecondCategory($this->data['filter']['query_type']);
        }

        if ($this->data['filter']['query_second'] !== '' ) {
            $conditions['beaurau_id'] = $this->data['filter']['query_second'];
            //var_dump($conditions['beaurau_id']);
            //die();
        }

        if ($this->data['filter']['show'] == '2' ) {
            $this->export();
        }
       
        
        $page = $this->data['filter']['page'];
        $rows = $this->data['filter']['rows'];

		$attrs = array(
            'conditions' => $conditions,
        );
        
        
        $this->data['filter']['total'] = $total = $this->season_course_capture_model->getListCount($attrs);
        $this->data['filter']['offset'] = $offset = ($page -1) * $rows;

        $attrs = array(
            'conditions' => $conditions,
            'rows' => $rows,
            'offset' => $offset,
        );

        
        $this->data['list'] = $this->season_course_capture_model->getList($attrs);
        
        
		$this->load->library('pagination');
        $config['base_url'] = base_url("planning/season_course_capture?". $this->getQueryString(array(), array('page')));
        $config['total_rows'] = $total;
        $config['per_page'] = $rows;
        $this->data['link_detail']= base_url("planning/season_course_capture/detail");
        $this->pagination->initialize($config);
        $this->data['link_get_second_category'] = base_url("planning/season_schedule/getSecondCategory");
		$this->data['link_confirm'] = '';
        $this->data['link_refresh'] = base_url("planning/season_course_capture/");
        
        //echo "<pre>";
        //var_dump($this->data['list']);
        //die();
		$this->layout->view('planning/season_course_capture/list', $this->data);
    }
    public function detail()
    {
        $this->data['choices']['query_type'] = $this->getSeriesCategory();
        $this->data['choices']['query_type'][''] = '請選擇系列別';

        if ($this->data['filter']['query_season'] !== '' ) {
            $conditions['reason'] = $this->data['filter']['query_season'];
        }
        if ($this->data['filter']['query_year'] !== '' ) {
            $conditions['year'] = $this->data['filter']['query_year'];
        }
       
        if ($this->data['filter']['query_month_start'] !== '' ) {
            $conditions['start_date1 >='] = ($this->data['filter']['query_year']+1911).'-'.$this->data['filter']['query_month_start'].'-01';
            $first_day = ($this->data['filter']['query_year']+1911).'-'.$this->data['filter']['query_month_start'].'-01';
            $last_day = date('Y-m-d', strtotime("$first_day +1 month -1 day"));
            $conditions['start_date1 <='] = $last_day;
        }
        if ($this->data['filter']['query_type'] !== '' ) {
            $conditions['require.type'] = $this->data['filter']['query_type'];
            $this->data['choices']['query_second'] = $this->course_introduct_model->getSecondCategory($this->data['filter']['query_type']);
        }

        if ($this->data['filter']['query_second'] !== '' ) {
            $conditions['beaurau_id'] = $this->data['filter']['query_second'];
        }

        
        $config['base_url'] = base_url("planning/season_course_capture?". $this->getQueryString(array(), array('page')));
    
        $this->data['link_detail']= base_url("planning/season_course_capture/detail");
        $this->pagination->initialize($config);
        $this->data['link_get_second_category'] = base_url("planning/season_schedule/getSecondCategory");
		$this->data['link_confirm'] = '';
        $this->data['link_refresh'] = base_url("planning/season_course_capture/detail");
        $this->layout->view('planning/season_course_capture/detail', $this->data);
    }
    public function export()
    {
        $this->data['choices']['query_type'] = $this->getSeriesCategory();
        $this->data['choices']['query_type'][''] = '請選擇系列別';
        $conditions=array();

        $query=$this->input->post();
        //var_dump($query);

        if ($query['query_season'] !== '' ) {
            $conditions['reason'] = $query['query_season'];
        }
        if ($query['query_year'] !== '' ) {
            $conditions['year'] = $query['query_year'];
        }
       
        if ($query['query_month_start'] !== '' ) {
            $conditions['start_date1 >='] = ($query['query_year']+1911).'-'.$query['query_month_start'].'-01';
            $first_day = ($query['query_year']+1911).'-'.$query['query_month_start'].'-01';
            $last_day = date('Y-m-d', strtotime("$first_day +1 month -1 day"));
            $conditions['start_date1 <='] = $last_day;
        }
        if ($query['query_type'] !== '' ) {
            $conditions['require.type'] = $query['query_type'];
            $this->data['choices']['query_second'] = $this->course_introduct_model->getSecondCategory($query['query_type']);
        }
        $bureau="";
        if ($query['query_second'] !== '' ) {
            $conditions['beaurau_id'] = $query['query_second'];
            $bureau=$this->season_course_capture_model->getBureauName(addslashes($query['query_second']));

        }
        //die();

   

		$attrs = array(
            'conditions' => $conditions,
        );
        
        $info = $this->season_course_capture_model->getList($attrs);
        //var_dump($query);
        //die();
        if($info == null){
            if ($query['query_type']=='A') {
                $info[0]['series_name']='行政系列';
            }
            if ($query['query_type']=='B') {
                $info[0]['series_name']='發展系列';
            }
            $info[0]['class_no']=null;
            $info[0]['term']=null;
            $info[0]['class_name']=null;
            $info[0]['start_date1']=null;
            $info[0]['end_date1']=null;
        }
        


        

        


            header("Content-type: application/csv");
            header("Content-Disposition: attachment; filename=file.csv");
            header("Pragma: no-cache");
            header("Expires: 0");
            $filename = 'test.csv';
            echo iconv("UTF-8", "BIG5", htmlspecialchars($query['query_year']."年度,", ENT_HTML5|ENT_QUOTES));
            echo iconv("UTF-8", "BIG5", $info[0]['series_name'].",");
            echo iconv("UTF-8", "BIG5", $bureau."\r\n");
            

        if ($info[0]['class_no']!=null) {
            for ($i=0;$i<count($info);$i++) {
                echo "\"".iconv("UTF-8", "BIG5", $info[$i]['series_name']."\",");
                echo "\"".iconv("UTF-8", "BIG5", $info[$i]['class_no']."\",");
                echo "\"".iconv("UTF-8", "BIG5", $info[$i]['term']."\",");
                echo "\"".iconv("UTF-8", "BIG5", $info[$i]['class_name']."\",");
                echo "\"".iconv("UTF-8", "BIG5", substr($info[$i]['start_date1'],0,10)."~".substr($info[$i]['end_date1'],0,10)."\"\r\n");
            }
            for ($k=0;$k<5;$k++) {
                echo "\"".iconv("UTF-8", "BIG5", ''."\",");
            }
        }

    }
    
}

<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Practice_order_table extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        if ($this->flags->is_login === FALSE) {
			redirect(base_url('welcome'));
		}
		$this->load->model('planning/practice_order_table_model');
        $this->load->model('planning/set_startdate_model');

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
        
        
    }

    public function index()
	{
		$this->data['page_name'] = 'list';
        $this->data['choices']['query_type'] = $this->getSeriesCategory();
        $this->data['choices']['query_type'][''] = '請選擇系列別';
   
        $bureau_id=$this->flags->user['bureau_id'];
        

        if ($this->data['filter']['query_year'] !== '' ) {
            $data['year'] = $this->data['filter']['query_year'];
        }
        if ($this->data['filter']['query_class_no'] !== '' ) {
            $data['class_no'] = $this->data['filter']['query_class_no'];
        }

        if ($this->data['filter']['query_class_name'] !== '' ) {
            $data['class_name'] = $this->data['filter']['query_class_name'];
        }
       
        
        $page = $this->data['filter']['page'];
        $rows = $this->data['filter']['rows'];
        $query=$this->input->post();
        if (!empty($query['query_sort'])) {
            for ($k=0;$k<count($query['query_sort']);$k++) {
                $this->db->where('year', $query['query_class_year'][$k]);
                $this->db->where('class_no', $query['query_class_no'][$k]);
                $this->db->where('term', $query['query_class_term'][$k]);
                $sort=array('sort'=>$query['query_sort'][$k]);
                $this->db->update('require', $sort);
            }
            //var_dump($query);
            $data['query_sort']=true;
        }

      
        
        $this->data['filter']['total'] = $total = $this->practice_order_table_model->getListCourseCount($data,$bureau_id);
        $this->data['filter']['offset'] = $offset = ($page -1) * $rows;
        $data = array(
            'rows' => $rows,
            'offset' => $offset,
        );
        
        if ($this->data['filter']['query_year'] !== '' ) {
            $data['year'] = $this->data['filter']['query_year'];
        }
        if ($this->data['filter']['query_class_no'] !== '' ) {
            $data['class_no'] = $this->data['filter']['query_class_no'];
        }

        if ($this->data['filter']['query_class_name'] !== '' ) {
            $data['class_name'] = $this->data['filter']['query_class_name'];
        }
        if (!empty($query['query_sort'])) {
            for ($k=0;$k<count($query['query_sort']);$k++) {
                $this->db->where('year', $query['query_class_year'][$k]);
                $this->db->where('class_no', $query['query_class_no'][$k]);
                $this->db->where('term', $query['query_class_term'][$k]);
                $sort=array('sort'=>addslashes($query['query_sort'][$k]));
                $this->db->update('require', $sort);
            }
            $data['query_sort']=true;
        }

      
        if ($this->data['filter']['query_class_name'] !== '' ) {
            $data['class_name'] = $this->data['filter']['query_class_name'];
        }
        
        $this->data['list'] = $this->practice_order_table_model->getCourseList($bureau_id,$data);
        
		$this->load->library('pagination');
        $config['base_url'] = base_url("planning/practice_order_table?". $this->getQueryString(array(), array('page')));
        $config['total_rows'] = $total;
        $config['per_page'] = $rows;
        $this->data['link_index']= base_url("planning/practice_order_table");
        $this->data['link_export']= base_url("planning/practice_order_table/export");
        $this->pagination->initialize($config);
        $this->data['link_get_second_category'] = base_url("planning/season_schedule/getSecondCategory");
        $this->data['link_confirm']='';
        $this->data['link_refresh'] = base_url("planning/practice_order_table/");
        
        
		$this->layout->view('planning/practice_order_table/list', $this->data);
    }
    
    public function export()
    {
        
        $bureau_id=$this->flags->user['bureau_id'];
       

        if ($this->data['filter']['query_year'] !== '' ) {
            $data['year'] = $this->data['filter']['query_year'];
        }
        if ($this->data['filter']['query_class_no'] !== '' ) {
            $data['class_no'] = $this->data['filter']['query_class_no'];
        }

        if ($this->data['filter']['query_class_name'] !== '' ) {
            $data['class_name'] = $this->data['filter']['query_class_name'];
        }

        $query=$this->input->post();
        
        /*按下確定按鈕之後更改資料庫sort column順便排序*/
        if (!empty($query['query_sort'])) {
            for ($k=0;$k<count($query['query_sort']);$k++) {
                $this->db->where('year', $query['query_class_year'][$k]);
                $this->db->where('class_no', $query['query_class_no'][$k]);
                $this->db->where('term', $query['query_class_term'][$k]);
                $sort=array('sort'=>$query['query_sort'][$k]);
                $this->db->update('require', $sort);
            }
            
            $query_sort=true;
        }

        $page = $this->data['filter']['page'];
        $rows = $this->data['filter']['rows'];

        $this->data['filter']['total'] = $total = $this->practice_order_table_model->getListCourseCount($data,$bureau_id);
        $this->data['filter']['offset'] = $offset = ($page -1) * $rows;

        $data = array(
            'rows' => $rows,
            'offset' => $offset,
        );

        if ($this->data['filter']['query_year'] !== '' ) {
            $data['year'] = $this->data['filter']['query_year'];
        }
        if ($this->data['filter']['query_class_no'] !== '' ) {
            $data['class_no'] = $this->data['filter']['query_class_no'];
        }

        if ($this->data['filter']['query_class_name'] !== '' ) {
            $data['class_name'] = $this->data['filter']['query_class_name'];
        }
        /*按下確定按鈕之後更改資料庫sort column順便排序*/
        if (!empty($query['query_sort'])) {
            for ($k=0;$k<count($query['query_sort']);$k++) {
                $this->db->where('year', $query['query_class_year'][$k]);
                $this->db->where('class_no', $query['query_class_no'][$k]);
                $this->db->where('term', $query['query_class_term'][$k]);
                $sort=array('sort'=>addslashes($query['query_sort'][$k]));
                $this->db->update('require', $sort);
            }
            //die();
            $query_sort=true;
        }

        

      
        $data['info']= $this->practice_order_table_model->getCourseList($bureau_id,$data);
        $data['bureau_name']=$data['info'][0]['bureau_name'];
        $data['year']=$data['info'][0]['year'];

        
        if($data['info']==null)
        {
            $data['info'][0]['year']=null;
            $data['info'][0]['sort']=null;
            $data['info'][0]['class_name']=null;
            $data['info'][0]['range']=null;
            $data['info'][0]['map1']=null;
            $data['info'][0]['map2']=null;
            $data['info'][0]['map3']=null;
            $data['info'][0]['map4']=null;
            $data['info'][0]['map5']=null;
            $data['info'][0]['map6']=null;
            $data['info'][0]['map7']=null;
            $data['info'][0]['map8']=null;
            $data['info'][0]['map9']=null;
            $data['info'][0]['map10']=null;
            $data['info'][0]['map11']=null;
        }
        $this->load->view('planning/practice_order_table/export',$data);
    }
}

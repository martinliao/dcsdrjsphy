<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Suggest extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('student/suggest_model');
        if (!isset($this->data['filter']['query_year'])) {
            $this->data['filter']['query_year'] = date('Y')-1911;
        }
        if (!isset($this->data['filter']['query_month_start'])) {
            $this->data['filter']['query_month_start'] = date('m');
        }
        if (!isset($this->data['filter']['query_class_name'])) {
            $this->data['filter']['query_class_name'] = '';
        }
        if (!isset($this->data['filter']['page'])) {
            $this->data['filter']['page'] = '1';
        }
        if (!isset($this->data['choice']['month'])) {
            $this->data['choice']['month'] = array(1=>'1月',2=>'2月',3=>'3月',4=>'4月',5=>'5月',6=>'6月',7=>'7月',8=>'8月'
                                                    ,9=>'9月',10=>'10月',11=>'11月',12=>'12月');
        }
    }

    public function index()
    {
        $conditions = array();
        
        if ($this->data['filter']['query_year'] !== '' ) {
            $conditions['require.year'] = $this->data['filter']['query_year'];
            
        }

        if ($this->data['filter']['query_month_start'] !== '' ) {
            $conditions['start_date1 >='] = ($this->data['filter']['query_year']+1911).'-'.$this->data['filter']['query_month_start'].'-01';
            $last_day = ($this->data['filter']['query_year']+1911).'-'.'0'.$this->data['filter']['query_month_start'].'-31';
            $conditions['start_date1 <='] = $last_day;
        }

        $conditions['os.is_annouce']='Y';
        //var_dump(date('m'));
        //die();
        $page = $this->data['filter']['page'];
        $rows = $this->data['filter']['rows'];
        
		$attrs = array(
            'conditions' => $conditions,
        );
        
        if ($this->data['filter']['query_class_name'] !== '' ) {
            $attrs['query_class_name'] = $this->data['filter']['query_class_name'];
        }

        $this->data['filter']['total'] = $total = $this->suggest_model->getListCount($attrs);
        $this->data['filter']['offset'] = $offset = ($page -1) * $rows;

        $attrs = array(
            'conditions' => $conditions,
            'rows' => $rows,
            'offset' => $offset,
        );
        if ($this->data['filter']['query_class_name'] !== '' ) {
            $attrs['query_class_name'] = $this->data['filter']['query_class_name'];
        }
        

		$this->data['list'] = $this->suggest_model->getList($attrs);
        foreach ($this->data['list'] as & $row) {
            $row['link_detail'] = base_url("student/suggest/detail/{$row['seq_no']}");
        }
		
		$this->load->library('pagination');
        $config['base_url'] = base_url("student/suggest?". $this->getQueryString(array(), array('page')));
        $config['total_rows'] = $total;
        $config['per_page'] = $rows;
        $this->pagination->initialize($config);

        $this->data['link_refresh'] = base_url("student/suggest/");
        
		$this->layout->view('student/suggest/list', $this->data);
    }
    public function detail($seq_no)
    {
        
        $conditions['require.seq_no']=$seq_no;
        $attrs['conditions']=$conditions;
        $this->data['list'] = $this->suggest_model->getSuggest($attrs);
        //var_dump($this->data['list']);
        $this->data['link_refresh'] = base_url("student/suggest/detail/{$seq_no}");
        $this->layout->view('student/suggest/detail',$this->data);
    }

}

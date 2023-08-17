<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Merge extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();

		if ($this->flags->is_login === FALSE) {
			redirect(base_url('welcome'));
		}
		$this->load->model('planning/merge_model');
	
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
	}

	public function index()
	{
		$this->data['page_name'] = 'list';

        $conditions = array();

        if ($this->data['filter']['query_year'] !== '' ) {
            $conditions['year'] = $this->data['filter']['query_year'];
        }

        if ($this->data['filter']['query_class_no'] !== '' ) {
            $conditions['class_no'] = $this->data['filter']['query_class_no'];
        }

        $page = $this->data['filter']['page'];
        $rows = $this->data['filter']['rows'];

		$attrs = array(
            'conditions' => $conditions,
        );

        if ($this->data['filter']['query_class_name'] != '') {
            $attrs['query_class_name'] = $this->data['filter']['query_class_name'];
        }

        $this->data['filter']['total'] = $total = $this->merge_model->getListCount($attrs);
        $this->data['filter']['offset'] = $offset = ($page -1) * $rows;

        $attrs = array(
            'conditions' => $conditions,
            'rows' => $rows,
            'offset' => $offset,
        );
    
        if ($this->data['filter']['sort'] !== '' ) {
            $attrs['sort'] = $this->data['filter']['sort'];
        }

        if ($this->data['filter']['query_class_name'] != '') {
            $attrs['query_class_name'] = $this->data['filter']['query_class_name'];
        }

		$this->data['list'] = $this->merge_model->getList($attrs);
        foreach ($this->data['list'] as & $row) {
            $row['link_detail'] = base_url("planning/merge/detail/{$row['seq_no']}/?{$_SERVER['QUERY_STRING']}");
        }
		
		$this->load->library('pagination');
        $config['base_url'] = base_url("planning/merge?". $this->getQueryString(array(), array('page')));
        $config['total_rows'] = $total;
        $config['per_page'] = $rows;
        $this->pagination->initialize($config);

		$this->data['link_refresh'] = base_url("planning/merge/");

		$this->layout->view('planning/merge/list', $this->data);
	}

    public function detail($id=NULL)
    {
        if ($post = $this->input->post()) {
            $this->require_merge($post);
            redirect(base_url("planning/merge/detail/{$id}/?"));
        }

        $this->data['page_name'] = 'detail';
        $this->data['form'] = $this->merge_model->getClassData($id);
        // dd($this->data['form']);
        $this->data['link_save'] = base_url("planning/merge/detail/{$id}/?{$_SERVER['QUERY_STRING']}");
        $this->data['link_cancel'] = base_url("planning/merge/?{$_SERVER['QUERY_STRING']}");
        $this->layout->view('planning/merge/detail', $this->data);
    }
    private function require_merge($post){

        $result = FALSE; 
        
        $class_info = [
            "year" => $post['year'],
            "class_no" => $post['class_no']
        ];
        
        $merge_terms = $post['term'];

        if (count($merge_terms) > 1){
            $result = $this->merge_model->requireMerge($class_info, $merge_terms);

            if ($result) {
                $this->setAlert(1, '合併成功');
            } else {
                $this->setAlert(1, '合併失敗');
            }
        }else if(count($merge_terms) == 1){
            $this->setAlert(2, "單期不能合併");
        }else{
            $this->setAlert(2, "無勾選要合併的班期");
        }
    }
}
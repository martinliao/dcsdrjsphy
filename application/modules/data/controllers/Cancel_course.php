<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cancel_course extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        if ($this->flags->is_login === FALSE) {
            redirect(base_url('welcome'));
        }
        $this->load->model('data/cancel_course_model');

        if (!isset($this->data['filter']['page'])) {
            $this->data['filter']['page'] = '1';
        }
        if (!isset($this->data['filter']['sort'])) {
            $this->data['filter']['sort'] = '1';
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
        $post = $this->input->post();
        if(isset($post['mode']) && $post['mode'] == 'cancel'){
            $cancel_status = $this->cancel_course_model->cancelClassEnd($post['year'],$post['class_no'],$post['term']);
            if($cancel_status){
                $this->setAlert(2, '取消帶班作業完成!');
            }
        }

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
        $conditions['isend'] = 'Y';

        $attrs = array(
            'conditions' => $conditions,
        );

        if ($this->data['filter']['query_class_name'] != '') {
            $attrs['query_class_name'] = $this->data['filter']['query_class_name'];
        }

        $this->data['filter']['total'] = $total = $this->cancel_course_model->getListCount($attrs);
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

        $this->data['list'] = $this->cancel_course_model->getList($attrs);
        foreach ($this->data['list'] as & $row) {
            $row['link_detail'] = base_url("data/cancel_course/detail/{$row['seq_no']}/?{$_SERVER['QUERY_STRING']}");
        }

        $this->load->library('pagination');
        $config['base_url'] = base_url("data/cancel_course?". $this->getQueryString(array(), array('page')));
        $config['total_rows'] = $total;
        $config['per_page'] = $rows;
        $this->pagination->initialize($config);

        $this->data['link_refresh'] = base_url("data/cancel_course/");
        $this->layout->view('data/cancel_course/list',$this->data);
    }
}

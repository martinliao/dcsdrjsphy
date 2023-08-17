<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MixUrgeLogs extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        if ($this->flags->is_login === FALSE) {
            redirect(base_url('welcome'));
        }

        $this->load->model('system/mixurge_log_model');

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

        if ($this->data['filter']['query_class_name'] !== '' ) {
            $attrs['query_class_name'] = $this->data['filter']['query_class_name'];
        }

        $this->data['filter']['total'] = $total = $this->mixurge_log_model->getListCount($attrs);
        $this->data['filter']['offset'] = $offset = ($page -1) * $rows;

        $attrs = array(
            'conditions' => $conditions,
            'rows' => $rows,
            'offset' => $offset,
        );

        if ($this->data['filter']['query_class_name'] !== '' ) {
            $attrs['query_class_name'] = $this->data['filter']['query_class_name'];
        }

        $this->data['list'] = $this->mixurge_log_model->getList($attrs);

        $this->load->library('pagination');
        $config['base_url'] = base_url("system/MixUrgeLogs?". $this->getQueryString(array(), array('page')));
        $config['total_rows'] = $total;
        $config['per_page'] = $rows;
        $this->pagination->initialize($config);

        $this->data['link_refresh'] = base_url("system/MixUrgeLogs/");
        $this->layout->view('system/mixurgelogs/list',$this->data);
    }

}

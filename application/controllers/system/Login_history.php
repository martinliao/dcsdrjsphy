<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Login_history extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        if ($this->flags->is_login === FALSE) {
			redirect(base_url('welcome'));
		}
        $this->load->model('system/login_history_model');
        if (!isset($this->data['filter']['page'])) {
            $this->data['filter']['page'] = '1';
        }
        if (!isset($this->data['filter']['sort'])) {
            $this->data['filter']['sort'] = '';
        }
        if (!isset($this->data['filter']['username'])) {
            $this->data['filter']['username'] = '';
        }
        if (empty($this->data['filter']['start_date'])) {
            $this->data['filter']['start_date'] = date('Y-m-d', time() - (86400 * 7));
        }

        if (empty($this->data['filter']['end_date'])) {
            $this->data['filter']['end_date'] = date('Y-m-d', time() );
        }
    }

    public function index()
    {
        if (!isset($this->data['filter']['page'])) {
            $this->data['filter']['page'] = '1';
        }
        $page = $this->data['filter']['page'];
        $this->data['filter']['rows'] = $rows = '10';

        $this->data['filter']['offset'] = $offset = ($page -1) * $rows;

		$conditions = array();
		if ($this->data['filter']['start_date'] != '') {
            $conditions['login_time >='] = $this->data['filter']['start_date'].' 00:00:00';
        }
        if ($this->data['filter']['end_date'] != '') {
            $conditions['login_time <='] = date('Y-m-d',  strtotime($this->data['filter']['end_date']) + (86400 * 1)).' 00:00:00';
        }

		$attrs = array(
            'conditions' => $conditions,
        );
		if ($this->data['filter']['username'] !== '' ) {
            $attrs['username'] = $this->data['filter']['username'];
        }

        $this->data['filter']['total'] = $total = $this->login_history_model->getListCount($attrs);

		$attrs = array(
            'conditions' => $conditions,
            'rows' => $rows,
            'offset' => $offset,
        );
        if ($this->data['filter']['username'] !== '' ) {
            $attrs['username'] = $this->data['filter']['username'];
        }
        if ($this->data['filter']['sort'] !== '' ) {
            $attrs['sort'] = $this->data['filter']['sort'];
        }

		$this->data['list'] = $this->login_history_model->getList($attrs);

		$this->load->library('pagination');
        $config['base_url'] = base_url("system/login_history?". $this->getQueryString(array(), array('page')));
        $config['total_rows'] = $total;
        $config['per_page'] = $rows;
        $this->pagination->initialize($config);

		$this->data['link_refresh'] = base_url("system/login_history/");
		$this->layout->view('system/login_history/list', $this->data);
    }

}

<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Course_push extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();

		if ($this->flags->is_login === FALSE) {
			redirect(base_url('welcome'));
		}
		
		$this->load->model('tpcd/course_push_model');

        if (!isset($this->data['filter']['page'])) {
            $this->data['filter']['page'] = '1';
        }
        if (!isset($this->data['filter']['sort'])) {
            $this->data['filter']['sort'] = '';
        }
        if (!isset($this->data['filter']['q'])) {
            $this->data['filter']['q'] = '';
        }
	}

    public function index()
	{
        $this->data['list'] = $this->course_push_model->getSetupData();


        // echo $this->data['list'][0]['message_content'];
        // die();

		$this->data['link_log'] = base_url("tpcd/course_push/log");
		$this->layout->view('tpcd/course_push', $this->data);
	}

    public function setup()
    {
        $post_data = $this->input->post();

        $url = base_url("tpcd/course_push");
        if(!empty($post_data)){ 
            $result = $this->course_push_model->setupData($post_data, $this->flags->user['idno'], $this->flags->user['name']);

            if($result){
                echo '<script>';
                echo 'alert("設定成功");';
                echo 'location.href="'.$url.'";';
                echo '</script>';
            } else {
                echo '<script>';
                echo 'alert("設定失敗");';
                echo 'location.href="'.$url.'";';
                echo '</script>';
            }
        } else {
            echo '<script>';
            echo 'alert("設定資料不能為空");';
            echo 'location.href="'.$url.'";';
            echo '</script>';
        }
    }

    public function log()
	{
		$conditions = array();
        $page = $this->data['filter']['page'];
        $rows = $this->data['filter']['rows'];

        $attrs = array(
            'conditions' => $conditions,
        );
        if ($this->data['filter']['q'] !== '' ) {
            $attrs['q'] = $this->data['filter']['q'];
        }

        $this->data['filter']['total'] = $total = $this->course_push_model->getListCount($attrs);
        $this->data['filter']['offset'] = $offset = ($page -1) * $rows;

        $attrs = array(
            'conditions' => $conditions,
            'rows' => $rows,
            'offset' => $offset,
        );
        if ($this->data['filter']['q'] !== '' ) {
            $attrs['q'] = $this->data['filter']['q'];
        }
        if ($this->data['filter']['sort'] !== '' ) {
            $attrs['sort'] = $this->data['filter']['sort'];
        }

        $this->data['list'] = $this->course_push_model->getList($attrs);

		$this->load->library('pagination');
        $config['base_url'] = base_url("tpcd/course_push/log?". $this->getQueryString(array(), array('page')));
        $config['total_rows'] = $total;
        $config['per_page'] = $rows;
        $this->pagination->initialize($config);

        $this->data['link_index'] = base_url("tpcd/course_push");

        $this->layout->view('tpcd/course_push_log', $this->data);
	}
}

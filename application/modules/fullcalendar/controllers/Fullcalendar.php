<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Fullcalendar extends AdminController //MY_Controller
{
    protected $choices = array();

    public function __construct()
    {
        parent::__construct();
        //$this->logged_in();
        //$this->smarty_acl->authorized('roles'); // Even do authorize check. 最新的檢查
        // 預約場地/教室
        $this->load->model('planning/booking_place_model');
        $this->load->model('planning/createclass_model');
        $this->load->model('data/place_category_model');
        $this->load->model('data/reservation_time_model');
        
        // Prepre choice
        $this->choices['time_list'] = $this->reservation_time_model->getChoices();
        $this->choices['room_type'] = $this->place_category_model->getChoices();

        //$this->load->model('room_model', 'model');

        if (empty($this->data['filter']['start_date'])) {
            $this->data['filter']['start_date'] = date('Y-m-d', time() - (86400 * 7));
        }
        if (empty($this->data['filter']['end_date'])) {
            $this->data['filter']['end_date'] = date('Y-m-d', time());
        }
        if (empty($this->data['filter']['room_type'])) {
            $this->data['filter']['room_type'] = '';
        }
        if (empty($this->data['filter']['room'])) {
            $this->data['filter']['room'] = '';
        }
    }

    public function index()
    {
        $this->data['page_name'] = 'calendar_index';
        $conditions = array();
		if ($this->data['filter']['start_date'] != '') {
            $conditions['start_date'] = $this->data['filter']['start_date'];
        }
        if ($this->data['filter']['end_date'] != '') {
            $conditions['end_date'] = $this->data['filter']['end_date'];
        }
        if ($this->data['filter']['room_type'] != '') {
            $conditions['cat_id'] = $this->data['filter']['room_type'];
            $this->data['choices']['room'] = $this->booking_place_model->get_room($this->data['filter']['room_type'], TRUE);
        }else{
            $this->data['filter']['room'] = '';
            $this->data['choices']['room'] = array();
            if(isset($this->data['filter']['sort']) && $this->data['filter']['room_type']==''){
                $this->setAlert(3, '請選擇場地類別');
                redirect();
            }
        }
        if ($this->data['filter']['room'] != '') {
            $conditions['room_id'] = $this->data['filter']['room'];
        }
        if($this->data['filter']['room_type'] != ''){
            if(isDate($conditions['start_date']) && isDate($conditions['end_date'])){
                $days = ((strtotime($conditions['end_date'])-strtotime($conditions['start_date'])) / 86400) + 1;
                if($days>30){
                    $this->setAlert(3, '搜尋日期請勿超過30天');
                    redirect(base_url("planning/classroom"));
                }
                $this->data['list'] = $this->booking_place_model->select_booking($conditions);
                // jd($this->data['list']);
            }else{
                $this->setAlert(3, '日期錯誤');
                redirect(base_url("planning/classroom"));
            }
        }else{
            $this->data['list'] = array();
        }

        $this->data['choices'] = array_merge($this->data['choices'], $this->choices);

        //$this->layout->view('classroom/list', $this->data);
        //$this->layout->setLayout('common341/layout_main');
        //$this->layout->view('classroom/list', $this->data);

        // Prepare
        /*$this->load_css(array(
            'static/plugin/fullcalendar/dist/fullcalendar.css',
            //'static/plugin/fullcalendar/dist/fullcalendar.print.css',
            'static/css/AdminLTE.min.css'
        ));/** */
        $this->load_js(array(
            'static/plugin/jquery-slimscroll/jquery.slimscroll.min.js',
            'static/plugin/fastclick/lib/fastclick.js',
            'static/js/adminlte.min.js',
        ));
        $this->render_page('classroom_list', $this->data);
    }

    public function showCalendar()
    {
        $data['csrf'] = array(
            'name' => $this->security->get_csrf_token_name(),
            'hash' => $this->security->get_csrf_hash()
        );
        //$this->load->view('_layout/general/head', $data);
        //$this->load->view('core/js', $data);   
        //$this->load->view('calendar', $data);   
        //$this->layout->setLayout('common341/layout_main');
        //$this->layout->view('fullcalendar/calendar', $data);

        /*$this->load_css(array(
            'static/plugin/fullcalendar/dist/fullcalendar.css',
            //'static/plugin/fullcalendar/dist/fullcalendar.print.css',
            'static/css/AdminLTE.min.css'
        ));/** */
        //'static/plugin/fullcalendar/dist/fullcalendar.print.min.js',
        $this->load_js(array(
            //'static/plugin/jStarbox/jstarbox.js',
            //'static/js/my.js',
            //'static/js/common.js',
            //'static/plugin/jquery.blockUI-2.7.0/jquery.blockUI.js',
            //'static/plugin/jquery-ui/jquery-ui.min.js',
            'static/plugin/jquery-slimscroll/jquery.slimscroll.min.js',
            'static/plugin/fastclick/lib/fastclick.js',
            'static/js/adminlte.min.js',
        ));/** */
        $this->render_page('fullcalendar/calendar', $this->data);
    }

    public function smallCalendar()
    {
		$this->load->view('modal/small_calendar', $this->data);
    }
}

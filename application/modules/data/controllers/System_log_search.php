<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class System_log_search extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->load->library('pagination');
        $config['total_rows'] = 200;
        $config['per_page'] = 20;
        $this->pagination->initialize($config);
        $this->data['link_refresh'] = base_url("data/system_log_search/");
        $this->layout->view('data/system_log_search/list',$this->data);
    }
}

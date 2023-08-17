<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Entry_ount extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        
        $this->load->model('entry_count_model');
    }

    public function index()
    {
    	$this->data['list'] = $this->entry_count_model->getList();
        $this->layout->view("other_work/entry_ount/index", $this->data);
    }

}

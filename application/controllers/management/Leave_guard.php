<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Leave_guard extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        if ($this->flags->is_login === FALSE) {
			redirect(base_url('welcome'));
		}
    }

    public function index()
    {
  		redirect(base_url("leave_guard.php"));
    }

}

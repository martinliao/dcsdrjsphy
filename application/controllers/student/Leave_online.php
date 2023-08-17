<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Leave_online extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        redirect(base_url('leave_login.php'));
    }

}

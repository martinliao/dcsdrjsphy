<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Mycalendar extends MY_Controller
{
    public function index()
    {
        $data['csrf'] = array(
            'name' => $this->security->get_csrf_token_name(),
            'hash' => $this->security->get_csrf_hash()
        );
        $this->layout->setLayout('common_calendar/layout_main');
        $this->layout->view('calendar', $data);
    }
}

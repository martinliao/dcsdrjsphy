<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Dashboard extends BackendController //Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        /* Load :: Common */
        $this->load->helper('number');
        #$this->load->model('admin/dashboard_model');
        $this->load->model('dashboard_model');

        //$this->breadcrumbs->unshift(1, lang('menu_dashboard'), 'admin/bashboard');
        /* Breadcrumbs :: Common */
		$this->breadcrumbs->unshift(1, lang('menu_users'), 'admin/users');
    }


    public function index()
    {
        /* Title Page */
        $this->page_title->push(lang('menu_dashboard'));
        $this->data['pagetitle'] = $this->page_title->show();

        /* Breadcrumbs */
        $this->breadcrumbs->unshift(2, lang('menu_users_profile'), 'admin/groups/profile'); // 測試
        $this->data['breadcrumb'] = $this->breadcrumbs->show();

        /* Data */
        $this->data['userid']            = $this->session->userdata["user_id"];

        $this->data['count_bsuser']      = $this->dashboard_model->get_count_record('BS_user');
        $this->data['count_bsgroup']     = $this->dashboard_model->get_count_record('BS_user_group');

        $this->data['count_users']       = $this->dashboard_model->get_count_record('users');   // BS_user
        $this->data['count_groups']      = $this->dashboard_model->get_count_record('acl_roles');  // BS_user_group
        $this->data['disk_totalspace']   = $this->dashboard_model->disk_totalspace(DIRECTORY_SEPARATOR);
        $this->data['disk_freespace']    = $this->dashboard_model->disk_freespace(DIRECTORY_SEPARATOR);
        $this->data['disk_usespace']     = $this->data['disk_totalspace'] - $this->data['disk_freespace'];
        $this->data['disk_usepercent']   = $this->dashboard_model->disk_usepercent(DIRECTORY_SEPARATOR, FALSE);
        $this->data['memory_usage']      = $this->dashboard_model->memory_usage();
        $this->data['memory_peak_usage'] = $this->dashboard_model->memory_peak_usage(TRUE);
        $this->data['memory_usepercent'] = $this->dashboard_model->memory_usepercent(TRUE, FALSE);
        //$this->data['chatrecord']              = $this->dashboard_model->chatrecord();

        //$this->data['url_exist']    = is_url_exist('http://127.0.0.1');
        //$this->data['todos'] = $this->dashboard_model->todolist();
//debugBreak();
        /* Load Template */
        //$this->template->admin_render('admin/dashboard/index', $this->data);
        //$this->render_page('dashboard/index', $this->data);
        $this->render_page('dashboard', $this->data);
        //$this->render_page2('dashboard/index', $this->data);
    }
}

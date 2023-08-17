<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Database extends BackendController //Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->dbutil();
        $this->lang->load('admin/database');

        /* Title Page :: Common */
        $this->page_title->push(lang('menu_database_utility'));
        $this->data['pagetitle'] = $this->page_title->show();

        /* Breadcrumbs :: Common */
        $this->breadcrumbs->unshift(1, lang('menu_database_utility'), 'admin/database');
    }


    public function index()
    {
            /* Breadcrumbs */
            $this->data['breadcrumb'] = $this->breadcrumbs->show();

            /* Data */
            $this->data['list_tables'] = $this->db->list_tables();
            $this->data['platform']    = $this->db->platform();
            $this->data['version']     = $this->db->version();

            /* Load Template */
            //$this->template->admin_render('admin/database/index', $this->data);
            $this->render_page('database', $this->data);
    }
}

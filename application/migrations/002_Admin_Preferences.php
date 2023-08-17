<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Admin_Preferences extends CI_Migration
{
    /**
     * Config settings
     * @var array
     */
    private $settings;

    private function get_settings()
    {
        // Load configs(Example)
        //$this->config->load('smarty_acl', TRUE);
        //Get tables array
        //$tables = $this->config->item('tables', 'smarty_acl');
        //Tables prefix
        //$this->settings['prefix'] = $tables['prefix'] ? $tables['prefix'].'_' : '';
        // Table names
        $this->settings['admin_preferences'] = 'admin_preferences';
    }

    public function up()
    {
        $this->get_settings();
        /**************** Start Create Tables ****************/
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ),
            'user_panel' => array('type' => 'INT', 'constraint' => '1', 'default' => 0, ),
            'sidebar_form' => array('type' => 'INT', 'constraint' => '1', 'default' => 0, ),
            'sidebar_form' => array('type' => 'INT', 'constraint' => '1', 'default' => 0, ),
            'messages_menu' => array('type' => 'INT', 'constraint' => '1', 'default' => 0, ),
            'notifications_menu' => array('type' => 'INT', 'constraint' => '1', 'default' => 0, ),
            'tasks_menu' => array('type' => 'INT', 'constraint' => '1', 'default' => 0, ),
            'user_menu' => array('type' => 'INT', 'constraint' => '1', 'default' => 0, ),
            'ctrl_sidebar' => array('type' => 'INT', 'constraint' => '1', 'default' => 0, ),
            'transition_page' => array('type' => 'INT', 'constraint' => '1', 'default' => 0, ),
            'created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP',
            'updated_at timestamp NOT NULL',
        ));
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table($this->settings['admin_preferences']);
        /**************** End Create Tables ****************/
        /**************** Start Set Foreign Keys ****************/
        /**************** End Set Foreign Keys ****************/
        /**************** Start Insert Data ****************/
        $this->db->insert($this->settings['admin_preferences'],[
             'user_panel' => 1,
             'sidebar_form' => 0,
             'messages_menu' => 0,
             'notifications_menu' => 0,
             'tasks_menu' => 0,
             'user_menu' => 1,
             'ctrl_sidebar' => 0,
             'transition_page' => 0,
             'created_at' => date('Y-m-d H:i:s'),
             'updated_at' => date('Y-m-d H:i:s')
        ]);
        /**************** End Insert Data ****************/
    }

    public function down()
    {
        //Load settings
        $this->get_settings();
        //Drop tables
        $this->dbforge->drop_table($this->settings['admin_preferences']);
    }
}
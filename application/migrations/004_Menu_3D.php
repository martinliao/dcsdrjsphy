<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Menu_3D extends CI_Migration
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
        // 22AA 選單設定
        $this->settings['menu'] = 'BS_menu';
        // 30D 角色權限設定
        $this->settings['group_permission'] = 'BS_user_group_auth';
    }

    public function up()
    {
        $this->get_settings();
        /**************** Start Create Tables ****************/
        /**************** End Create Tables ****************/
        /**************** Start Set Foreign Keys ****************/
        /**************** End Set Foreign Keys ****************/
        /**************** Start Insert Data ****************/
        $new_link = 'defaultclass';
        $this->db->insert($this->settings['menu'],[
             'parent_id' => 586,
             'action_id' => 0,
             'port' => 'admin',
             'name' => '3D 開班需求預設值設定',
             'link' => $new_link,
             'enable' => 1,
             'auth' => 1,
             'sort_order' => 4
        ]);
        $root_id = $this->db->insert_id();
        // 加入選單
        $action_ids = [$root_id];
        $this->db->insert($this->settings['menu'],['parent_id' => 586, 'action_id' => $root_id, 'port' => 'admin', 'name' => 'Add', 'link' => $new_link . '/add', 'enable' => 1, 'auth' => 1, 'sort_order' => 0]);
        array_push($action_ids, $this->db->insert_id());
        $this->db->insert($this->settings['menu'],['parent_id' => 586, 'action_id' => $root_id, 'port' => 'admin', 'name' => 'View', 'link' => $new_link . '/view', 'enable' => 1, 'auth' => 1, 'sort_order' => 0]);
        array_push($action_ids, $this->db->insert_id());
        $this->db->insert($this->settings['menu'],['parent_id' => 586, 'action_id' => $root_id, 'port' => 'admin', 'name' => 'Edit', 'link' => $new_link . '/edit', 'enable' => 1, 'auth' => 1, 'sort_order' => 0]);
        array_push($action_ids, $this->db->insert_id());
        // 全部加給 admin(1)
        foreach($action_ids as & $actionId) {
            $this->db->insert($this->settings['group_permission'],['user_group_id' => 1, 'menu_id' => $actionId]);
        }
        /**************** End Insert Data ****************/
    }

    public function down()
    {
        //Load settings
        $this->get_settings();
        //Drop tables
    }
}
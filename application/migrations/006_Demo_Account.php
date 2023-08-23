<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Demo_Account extends CI_Migration
{
    /**
     * Config settings
     * @var array
     */
    private $settings;

    private function get_settings()
    {
        //Load configs
        $this->config->load('smarty_acl', TRUE);
        //Get tables array
        $tables = $this->config->item('tables', 'smarty_acl');
        //Tables prefix
        $this->settings['prefix'] = $tables['prefix'] ? $tables['prefix'].'_' : '';
        // Table names
        $this->settings['users'] = $tables['users'];
        $this->settings['admins'] = $tables['admins'];
    }

    public function up()
    {
        //Load settings
        $this->get_settings();
        /**************** Start Create Tables ****************/
        // Add demo account: martin
        $this->db->insert($this->settings['users'],[
             'username' => 'martin',
             'password' => '$2y$10$5c0148QZAvFIOVcMlW0j2Oq/cDwfUEQp6KjElBR045dzT3Gcm2bSi', // jxxx5xxx
             'name' => 'Martin',
             'email' => 'martin@click-ap.com',
             'status' => 'active',
             'ip' => '10.10.10.2',
             'email_verified_at' => date('Y-m-d H:i:s'),
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
        $this->db->delete($this->settings['users'], ['username' => 'martin']);
    }
}
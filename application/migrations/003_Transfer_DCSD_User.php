<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Transfer_DCSD_User extends CI_Migration
{
    /**
     * Config settings
     * @var array
     */
    private $settings;

    private $old_db;

    private function get_settings()
    {
        //Load configs
        $this->config->load('smarty_acl', TRUE);

        // Load old database setting
        $olddb = $this->config->item('olddb', 'smarty_acl');
        $old_database = $olddb['database'] ? $olddb['database'] : 'default';
        $this->old_db = $this->load->database($old_database, TRUE);
        $this->settings['old_users'] = "BS_user";
        
        //Get tables array
        $tables = $this->config->item('tables', 'smarty_acl');
        //Tables prefix
        $this->settings['prefix'] = $tables['prefix'] ? $tables['prefix'].'_' : '';
        // Table names
        $this->settings['users'] = $tables['users'];
    }

    public function up()
    {
        //Load settings
        $this->get_settings();
        /**************** Start Set Foreign Keys ****************/
        //Unique keys
        $this->db->query('ALTER TABLE '.$this->settings['users'].' ADD CONSTRAINT username_unique UNIQUE (username)');
        /**************** End Set Foreign Keys ****************/
        /**************** Start Transfer old user ****************/
        //SELECT username, PASSWORD, NAME, co_usrnick, idno, email from BS_user WHERE ENABLE=1 
        if (ENVIRONMENT !== 'production') {
            $this->old_db->limit(10);
        }

        $query = $this->old_db->select(['username', 'password', 'name', 'co_usrnick', 'idno', 'email', 'last_login_time'])
                          ->from($this->settings['old_users'])
                          ->where('enable', 1)
                          ->get()->result_array();
        /**************** Start Insert Data ****************/
        //if ((bool) $query) {
        foreach ($query as & $row) {
            //$tmp = $this->db->insert($this->settings['users'],[
            $tmp = $this->db->replace($this->settings['users'],[
                'username' => $row['username'],
                'password' => $row['password'],
                'name' => $row['name'],
                'email' => $row['email'],
                'last_login' => $row['last_login_time'],
                'status' => 'active',
                'ip' => '172.19.0.1',
                'email_verified_at' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }
        /**************** End Insert Data ****************/
    }

    public function down()
    {
        //Load settings
        $this->get_settings();
    }
}
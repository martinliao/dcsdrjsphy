<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Lux_account_log_model extends MY_Model
{
    public $table = 'lux_account_log';
    public $pk = 'id';

    public function __construct()
    {
        parent::__construct();

        $this->init($this->table, $this->pk);

    }


}
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Online_app_score_model extends MY_Model
{
    public $table = 'online_app_score';
    public $pk = '';

    public function __construct()
    {
        parent::__construct();

        $this->init($this->table, $this->pk);

    }

}
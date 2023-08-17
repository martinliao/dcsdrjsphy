<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Stud_modifylog_model extends MY_Model
{
    public $table = 'stud_modifylog';
    public $pk = '';

    public function __construct()
    {
        parent::__construct();

        $this->init($this->table, $this->pk);

    }


}
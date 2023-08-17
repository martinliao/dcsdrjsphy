<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Courseteacher_model extends MY_Model
{
    public $table = 'courseteacher';
    public $pk = '';

    public function __construct()
    {
        parent::__construct();

        $this->init($this->table, $this->pk);

    }

}
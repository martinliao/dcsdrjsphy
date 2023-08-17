<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Phydisabled_model extends MY_Model
{
    public $table = 'phydisabled';
    public $pk = '';

    public function __construct()
    {
        parent::__construct();

        $this->init($this->table, $this->pk);

    }


}
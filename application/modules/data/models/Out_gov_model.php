<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Out_gov_model extends MY_Model
{
    public $table = 'out_gov';
    public $pk = 'id';

    public function __construct()
    {
        parent::__construct();

        $this->init($this->table, $this->pk);

    }


}
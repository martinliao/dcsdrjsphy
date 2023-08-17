<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Org_detail_model extends MY_Model
{
    public $table = 'org_detail';
    public $pk = 'id';

    public function __construct()
    {
        parent::__construct();

        $this->init($this->table, $this->pk);

    }


}
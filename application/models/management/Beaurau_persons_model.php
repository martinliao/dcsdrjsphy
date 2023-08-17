<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Beaurau_persons_model extends MY_Model
{
    public $table = 'beaurau_persons';
    public $pk = '';

    public function __construct()
    {
        parent::__construct();

        $this->init($this->table, $this->pk);

    }


}
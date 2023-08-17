<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Entry_count_model extends MY_Model
{
    public $table = 'entry_count';
    // public $pk = 'id';
    protected $locale = array();

    public function __construct()
    {
        parent::__construct();

        $this->init($this->table, $this->pk);

    }

    public function getList()
    {
        $query = $this->db->from('entry_count')->order_by('count_year')->get();
        return $query->result();
    }
}

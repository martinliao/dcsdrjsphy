<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Certificate_list_table_model extends MY_Model
{
    public $table = 'certificate_list';
    public $pk = 'id';

    public function __construct()
    {
        parent::__construct();

        $this->init($this->table, $this->pk);

    }

    public function get_list($seq_no)
    {
        $this->db->select("*");
        $this->db->from($this->table);
        $this->db->where("seq_no",$seq_no);

        $query = $this->db->get();
        $all_list = $query->result_array();
        return $all_list;
    }

    public function get_list_new($seq_no)
    {
        $this->db->select("certificate_list.*,certificate_type.category");
        $this->db->from($this->table);
        $this->db->join('certificate_type','certificate_type.id = certificate_list.type_id','left');
        $this->db->where("seq_no",$seq_no);

        $query = $this->db->get();
        $all_list = $query->result_array();
        return $all_list;
    }

}
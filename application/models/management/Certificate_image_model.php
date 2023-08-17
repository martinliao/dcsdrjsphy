<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Certificate_image_model extends MY_Model
{
    public $table = 'certificate_image';
    public $pk = 'id';

    public function __construct()
    {
        parent::__construct();

        $this->init($this->table, $this->pk);

    }

    public function get_image_option($type)
    {
        $this->db->select('*');
        $this->db->where('file_type',$type);  
        $query=$this->db->get($this->table);
        $data=$query->result_array();
        return $data;
    }

}
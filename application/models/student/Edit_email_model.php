<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Edit_email_model extends MY_Model
{
    public $table = 'BS_user';
    public $pk = 'id';

    public function __construct()
    {
        parent::__construct();

        $this->init($this->table, $this->pk);

    }

    public function _update($pk, $fields=array()) 
    {
        return parent::update($pk, $fields);
    }

    public function getEmail($id)
    {
        $this->db->select('email');
        $this->db->where('id',$id);
        $query = $this->db->get($this->table);
        $result = $query->result_array();

        if(!empty($result)){
           return $result[0]['email']; 
        }

        return '';
    }

}
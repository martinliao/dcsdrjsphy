<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Card_rotation_model extends MY_Model
{
    public $table = 'card_rotation';
    public $pk = 'id';

    public function __construct()
    {
        parent::__construct();

        $this->init($this->table, $this->pk);

    }

    public function getList()
    {
        $params = array(
            'select' => '*',
        );

        $data = $this->getData($params);

        return $data;
    }

    public function insertCardRotation($name,$url,$creator){
        $this->db->set('name',$name);
        $this->db->set('url',$url);
        $this->db->set('create_datetime',date('Y-m-d H:i:s'));
        $this->db->set('creator',$creator);

        if($this->db->insert('card_rotation')){
            return true;
        }

        return false;
    }

    public function getInfo($id){
        $this->db->select('*');
        $this->db->where('id',$id);
        $query = $this->db->get('card_rotation');
        $result = $query->result_array();

        return $result;
    }

    public function updateCardRotation($name,$url,$id){
        $this->db->set('name',$name);
        $this->db->set('url',$url);
        $this->db->where('id',$id);
    
        if($this->db->update('card_rotation')){
            return true;
        }

        return false;
    }

    public function deleteCardRotation($id){
        $this->db->where('id',$id);
    
        if($this->db->delete('card_rotation')){
            return true;
        }

        return false;
    }
}
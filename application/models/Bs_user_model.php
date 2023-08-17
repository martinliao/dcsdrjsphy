<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Bs_user_model extends MY_Model
{	
    public $table = 'BS_user';
    public $pk = '';

    public function __construct()
    {
        parent::__construct();
    }	

    public function find($user){
        $select = "user.*, bc.name bc_name";
        $this->db->select($select)
                 ->from('BS_user user')
                 ->join('bureau bc', 'bc.bureau_id = user.bureau_id', 'left');

        if (isset($user['user.id'])) $this->db->where('id', $user['id']);

        if (isset($user['idno'])){
            $user['idno'] = strtoupper($user['idno']);
            $this->db->where('upper(user.idno)', $user['idno']);
        }

        if (isset($user['username'])){
            $this->db->where('user.username', $user['username']);
        }
        
        $query = $this->db->get();
        return $query->row();
    }

    public function getForArriveSelect($queryData)
    {
        $this->db->start_cache();

        $this->db->select("user.id, user.idno, user.name")
                 ->from('BS_user user');

        if (isset($queryData['idno'])){
            $this->db->where('idno LIKE', "%".$queryData['idno']."%");
        }

        if (isset($queryData['member_name'])){
            $this->db->where('name LIKE', "%".$queryData['member_name']."%");
        }

        $this->db->stop_cache(); 

        //$this->paginate();

        $query = $this->db->get();
        $this->db->flush_cache(); 

        return $query->result();
    }

}
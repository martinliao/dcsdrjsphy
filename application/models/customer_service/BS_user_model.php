<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class BS_user_model extends MY_Model
{
    public $table = 'BS_user';
    public $pk = 'id';

    public function __construct()
    {
        parent::__construct();

        $this->init($this->table, $this->pk);

    }

    public function getListCount($attrs=array())
    {
        $params = array(
            'conditions' => $attrs['conditions'],
        );

        if (isset($attrs['query_class_name'])) {
            $params['query_class_name'] = $attrs['query_class_name'];
        }
        $data = $this->getList($params);
        return count($data);
    }

    public function getList($attrs=array())
    {   if(isset($attrs['select'])){
            $params = array(
                'select' => $attrs['select'],
                'order_by' => 'id',
            );
        }else{
            $params = array(
                'select' => 'id,name,idno,gender,birthday,email,telephone,home_address,office_email,office_tel,cellphone,job_title,bureau_id,bureau_name,hid',
                'order_by' => 'id',
            );
        }
        $date_like = array();
        if (isset($attrs['bureau_name'])) {
            $like_bureau = array(
                array('field' => 'bureau_name', 'value'=>$attrs['bureau_name'], 'position'=>'both')
            );
            $date_like = array_merge($date_like, $like_bureau);
        }
        if (isset($attrs['name'])) {
            $like_name = array(
                array('field' => 'name', 'value'=>$attrs['name'], 'position'=>'both')
            );
            $date_like = array_merge($date_like, $like_name);
        }
        if (isset($date_like)) {
            $params['like'] = array(
                'many' => TRUE,
                'data' => $date_like,
            );
        }
        //用於where
        if (count($attrs['conditions'])!=0) {
            $params['conditions'] = $attrs['conditions'];
        }


        $data = $this->getData($params);

        return $data;
    }
    public function getMemberData($id=array(),$select=NULL) //input array(idno1,idno2...)
    {   
        if(count($id)==0){
            return array();
        }
        if(is_null($select)){
            $this->db->select("*");
        }else{
            $this->db->select($select);
        }
        $this->db->from($this->table);
        $this->db->where_in("idno", $id);
        $query = $this->db->get();
        $data = $query->result_array(); 
        return $data;
    }
    public function getMemberDataWithOutGov($id=array(),$select=NULL)
    {
        if(count($id)==0){
            return array();
        }
        $this->db->select($select);
        $this->db->join('out_gov as og','og.id=BS_user.idno','left');
        $this->db->from($this->table);
        $this->db->where_in("idno", $id);
        //var_dump($id);
        $query = $this->db->get();
        $data = $query->result_array(); 
        return $data;
    }
    public function getMemberInfo($id=array(),$select=NULL)
    {
        if(count($id)==0){
            return array();
        }
        $this->db->select($select);
        $this->db->join('bureau as d','d.bureau_id=BS_user.bureau_id','left');
        $this->db->join('view_code_table as f','f.type_id="03" and f.item_id=BS_user.co_position','left');
        $this->db->join('out_gov as og','og.id=BS_user.idno','left');
        $this->db->from($this->table);
        $this->db->where_in("idno", $id);
        $query = $this->db->get();
        $data = $query->result_array(); 

        return $data;
    }
    public function getWorkerID($username){
        $this->db->select('idno');
        $this->db->from($this->table);
        $this->db->where("username", $username);
        $query = $this->db->get();
        $data = $query->row_array(); 
        return $data['idno'];
    }

    public function getBureauIdByEmail($email,$edap=''){
        $this->db->select('bureau_id');
        $this->db->where('email',$email);

        if($edap == 'edap'){
            $this->db->like('username', 'edap', 'after');
        }

        $query = $this->db->get('BS_user');
        $result = $query->result_array();

        if(!empty($result)){
            return $result[0]['bureau_id'];
        }

        return '';
    }

    public function getBureauIdByEmail2($email,$edap=''){
        $this->db->select('bureau_id');
        $this->db->where('email2',$email);

        if($edap == 'edap'){
            $this->db->like('username', 'edap', 'after');
        }

        $query = $this->db->get('BS_user');
        $result = $query->result_array();

        if(!empty($result)){
            return $result[0]['bureau_id'];
        }

        return '';
    }

    public function getBureauIdByUsername($username){
        $this->db->select('bureau_id');
        $this->db->where('username',$username);
        $this->db->like('username', 'edap', 'after');
        
        $query = $this->db->get('BS_user');
        $result = $query->result_array();

        if(!empty($result)){
            return $result[0]['bureau_id'];
        }

        return '';
    }
}
    
    
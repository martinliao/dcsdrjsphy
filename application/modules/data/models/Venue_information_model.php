<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Venue_information_model extends MY_Model
{
    public $table = 'venue_information';
    public $pk = 'id';

    public function __construct()
    {
        parent::__construct();

        $this->init($this->table, $this->pk);

    }

    public function getFormDefault($info=array())
    {
        $data = array_merge(array(
                    'room_id' => '',
                    'room_type' => '',
                    'room_name' => '',
                    'room_sname' => '',
                    'room_bel' => '',
                    'room_countby' => '0',
                    'room_cap' => '0',
                    'room_location' => '',
                    'room_manage' => '',
                    'room_contact' => '',
                    'room_phone' => '',
                    'room_soft' => '',
                    'room_equi' => '',
                ),$info);

        return $data;
    }

    public function getVerifyConfig()
    {
        $config = array(
            'room_id' => array(
                'field' => 'room_id',
                'label' => '場地代碼',
                'rules' => 'trim|required|is_unique[venue_information.room_id]|max_length[5]|callback_valid_exist',
                
            ),
            'room_type' => array(
                'field' => 'room_type',
                'label' => '場地類別',
                'rules' => 'trim|required|max_length[5]',
            ),
            'room_name' => array(
                'field' => 'room_name',
                'label' => '場地名稱',
                'rules' => 'trim|required|max_length[50]',
            ),
            'room_sname' => array(
                'field' => 'room_sname',
                'label' => '場地簡稱',
                'rules' => 'trim|required|max_length[50]',
            ),
            'room_bel' => array(
                'field' => 'room_bel',
                'label' => '所屬單位',
                'rules' => 'trim|required|max_length[5]',
            ),
            'room_countby' => array(
                'field' => 'room_countby',
                'label' => '計價方式',
                'rules' => 'required',
            ),
            'room_cap' => array(
                'field' => 'room_cap',
                'label' => '容納人數',
                'rules' => 'required|integer|max_length[10]',
            ),
            'room_location' => array(
                'field' => 'room_location',
                'label' => '場地教室位置',
                'rules' => 'trim|max_length[50]',
            ),
            'room_manage' => array(
                'field' => 'room_manage',
                'label' => '管理單位',
                'rules' => 'trim|max_length[30]',
            ),
            'room_contact' => array(
                'field' => 'room_contact',
                'label' => '聯絡人',
                'rules' => 'trim|max_length[30]',
            ),
            'room_phone' => array(
                'field' => 'room_phone',
                'label' => '聯絡電話',
                'rules' => 'trim|max_length[30]',
            ),
            'room_soft' => array(
                'field' => 'room_soft',
                'label' => '軟體資源',
                'rules' => 'trim|max_length[30]',
            ),
            'room_equi' => array(
                'field' => 'room_equi',
                'label' => '設備',
                'rules' => 'trim|max_length[30]',
            ),


        );

        return $config;
    }

    public function getDelFlagIsY($room_id)
    {
        $this->db->select("room_id");
        $this->db->where("del_flag","Y");
        $this->db->where("room_id",$room_id);
        $query=$this->db->get("venue_information");
        $query=$query->result_array();
        if(!empty($query)){
            return false;
        }
        return true;
    }


    public function _insert($fields=array())
    {
        return $this->insert($fields);
    }

    public function _update($pk, $fields=array())
    {
        return parent::update($pk, $fields);
    }

    public function getListCount($attrs=array())
    {
        $params = array(
            'conditions' => $attrs['conditions'],
        );

        if (isset($attrs['q'])) {
            $params['q'] = $attrs['q'];
        }
        $data = $this->getList($params);
        return count($data);
    }

    public function getList($attrs=array())
    {
        $params = array(
            'select' => '*',
            'order_by' => 'room_id',
        );
        if (isset($attrs['conditions'])) {
            $params['conditions'] = $attrs['conditions'];
        }
        if (isset($attrs['rows'])) {
            $params['rows'] = $attrs['rows'];
        }
        if (isset($attrs['offset'])) {
            $params['offset'] = $attrs['offset'];
        }
        if (isset($attrs['sort'])) {
            $params['order_by'] = $attrs['sort'];
        }
        if (isset($attrs['q'])) {
            $params['or_like'] = array(
                'many' => TRUE,
                'data' => array(
                    array('field' => 'room_id', 'value'=>$attrs['q'], 'position'=>'both'),
                    array('field' => 'room_name', 'value'=>$attrs['q'], 'position'=>'both'),
                ),
            );
            // unset
        }

        $data = $this->getData($params);
       
        return $data;
    }

}
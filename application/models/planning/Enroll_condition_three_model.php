<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Enroll_condition_three_model extends MY_Model
{
    public $table = 'enroll_condition_3';
    public $pk = 'id';

    public function __construct()
    {
        parent::__construct();

        $this->init($this->table, $this->pk);

    }

    public function getFormDefault($info=array())
    {
        $data = array_merge(array(
                    'id' => '',
                    'limit_name' => '',
                    'condition' => '',
                    'compare_type' => '',
                    'class' => '',
                ),$info);

        return $data;
    }

    public function getVerifyConfig()
    {
        $config = array(
            'limit_name' => array(
                'field' => 'limit_name',
                'label' => '名稱',
                'rules' => 'required|trim',
            ),
        );

        return $config;
    }

    public function _insert($fields=array())
    {
        return $this->insert($fields);
    }

    public function _update($pk, $fields=array()) 
    {
        return parent::update($pk, $fields);
    }

    public function deleteData($group_id){
        $this->db->where('group_id',$group_id);
        
        if($this->db->delete('enroll_condition_3')){
            return true;
        }

        return false;
    }

    public function getListCount($attrs=array())
    {
        $params = array(
            'conditions' => $attrs['conditions'],
        );

        $data = $this->getList($params);
        return count($data);
    }

    public function getList($attrs=array())
    {
        $params = array(
            'select' => 'id, limit_name,condition,compare_type,class_no_2',
            'order_by' => 'class_no',
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
        
        if (isset($attrs['class_no_2'])) {
            $params['where_in'] = array('field' => 'class_no_2', 'value' => $attrs['class_number']);
        }
        
        $data = $this->getData($params);
        return $data;
    }

    public function getDistinctClassName($class_no){
        $this->db->select('class_no,class_name');
        $this->db->where('class_no',$class_no);
        $this->db->group_by('class_no,class_name');
        $query = $this->db->get('require');
        $result = $query->result_array();
       
        return $result;
    }

    public function getDataById($id){
        $this->db->select('limit_name,condition,class_no_2');
        $this->db->where('id',$id);
        $query = $this->db->get('enroll_condition_3');
        $result = $query->result_array();

        return $result;
    }

}
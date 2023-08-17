<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Enroll_condition_two_model extends MY_Model
{
    public $table = 'enroll_condition_2';
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
                    'class' => '',
                    'group_id' => '',
                    'group_name' => '',
                    'limited' => '',
                ),$info);

        return $data;
    }

    public function getVerifyConfig()
    {
        $config = array(
            'group_id' => array(
                'field' => 'group_id',
                'label' => '群組代碼',
                'rules' => 'required',
            ),
            'group_name' => array(
                'field' => 'group_name',
                'label' => '群組名稱',
                'rules' => 'required|trim',
            ),
            'limited' => array(
                'field' => 'limited',
                'label' => '限制參訓數',
                'rules' => 'required',
            ),
        );

        return $config;
    }

    public function insertData($data=array())
    {
        $data['class'] = substr($data['class'],0,-1);
        $class_list = explode(',', $data['class']);
        unset($data['class']);

        $this->db->trans_start();
        for($i=0;$i<count($class_list);$i++){
            $data['class_no'] = $class_list[$i];
            $this->db->insert('enroll_condition_2',$data);
        }
        $this->db->trans_complete();

        if($this->db->trans_status() === TRUE){
            return true;
        }

        return false;
    }

    public function updateData($data=array()){
        $data['class'] = substr($data['class'],0,-1);
        $class_list = explode(',', $data['class']);
        unset($data['class']);
        $class_list = array_values(array_unique($class_list));

        $this->db->trans_start();
        $this->db->where('group_id',$data['group_id']);
        $this->db->delete('enroll_condition_2');
        for($i=0;$i<count($class_list);$i++){
            $data['class_no'] = $class_list[$i];
            $this->db->insert('enroll_condition_2',$data);
        }
        $this->db->trans_complete();

        if($this->db->trans_status() === TRUE){
            return true;
        }

        return false;
    }

    public function deleteData($group_id){
        $this->db->where('group_id',$group_id);
        
        if($this->db->delete('enroll_condition_2')){
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
            'select' => 'id, class_no,limited,group_id,group_name',
            'order_by' => 'group_id',
        );

        $params['group_by'] = 'group_id,group_name';


        if (isset($attrs['class_no'])) {
            $params['where_in'] = array('field' => 'class_no', 'value' => $attrs['class_number']);
        }

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

        $data = $this->getData($params);
        
        return $data;
    }
 

    public function getClassList($group_id){
        $this->db->select('class_no');
        $this->db->where('group_id',$group_id);
        $this->db->order_by('class_no');
        $query = $this->db->get('enroll_condition_2');
        $result = $query->result_array();

        $data = array();
        for($i=0;$i<count($result);$i++){
            $class_name = $this->getDistinctClassName($result[$i]['class_no']);
            for($j=0;$j<count($class_name);$j++){
                array_push($data, $class_name[$j]);
            }
        }

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

    public function getDataByGroupId($group_id){
        $this->db->select('limited,group_id,group_name');
        $this->db->where('group_id',$group_id);
        $this->db->group_by('group_id,group_name');
        $query = $this->db->get('enroll_condition_2');
        $result = $query->result_array();

        return $result;
    }

}
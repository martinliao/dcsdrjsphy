<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Modify_classname_model extends MY_Model
{
    public $table = 'require';
    public $pk = 'seq_no';

    public function __construct()
    {
        parent::__construct();

        $this->init($this->table, $this->pk);

    }

    public function getFormDefault($info=array())
    {
        $data = array_merge(array(
                    'year' => '',
                    'class_no' => '',
                    'class_name' => '',
                    'class_name_shot' => '',
                ),$info);

        return $data;
    }

    public function getVerifyConfig()
    {
        $config = array(
            'new_class_name' => array(
                'field' => 'new_class_name',
                'label' => '新班期名稱',
                'rules' => 'trim|required',
            ),
            'class_name_shot' => array(
                'field' => 'class_name_shot',
                'label' => '備註',
                'rules' => 'trim',
            ),
        );

        return $config;
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
    {
        $params = array(
            'select' => 'seq_no,year, class_no, class_name, class_name_shot',
        );

        $params['group_by'] = 'year,class_no,class_name,class_name_shot';

        if (isset($attrs['query_class_name'])) {
            $params['or_like'] = array(
                'many' => TRUE,
                'data' => array(
                    array('field' => 'class_name', 'value'=>$attrs['query_class_name'], 'position'=>'both'),
                ),
            );
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

    public function updateClassName($data=array()){
        $this->db->set('class_name',$data['new_class_name']);
        $this->db->set('class_name_shot',$data['class_name_shot']);
        $this->db->where('year',$data['year']);
        $this->db->where('class_no',$data['class_no']);

        if($this->db->update('require')){
            return true;
        }

        return false;
    }
}
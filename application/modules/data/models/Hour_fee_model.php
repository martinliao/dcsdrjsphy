<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Hour_fee_model extends MY_Model
{
    public $table = 'hour_fee';
    public $pk = 'id';

    public function __construct()
    {
        parent::__construct();

        $this->init($this->table, $this->pk);

    }

    public function getFormDefault($info=array())
    {

        $data = array_merge(array(
                    'class_type_id' => '',
                    'type' => '',
                    'assistant_type_id' => '',
                    'teacher_type_id' => '',
                    'hour_fee' => '',
                    'traffic_fee' => '',
                ),$info);

        return $data;
    }

    public function getVerifyConfig()
    {
        $config = array(
            'class_type_id' => array(
                'field' => 'class_type_id',
                'label' => '鐘點費類別',
                'rules' => 'trim|required',
            ),
            'type' => array(
                'field' => 'type',
                'label' => '身分別',
                'rules' => 'trim|required',
            ),
            'assistant_type_id' => array(
                'field' => 'assistant_type_id',
                'label' => '助教聘請類別',
                'rules' => 'trim',
            ),
            'teacher_type_id' => array(
                'field' => 'teacher_type_id',
                'label' => '講師聘請類別',
                'rules' => 'trim|required',
            ),
            'hour_fee' => array(
                'field' => 'hour_fee',
                'label' => '鐘點費',
                'rules' => 'required|numeric',
            ),
            'traffic_fee' => array(
                'field' => 'traffic_fee',
                'label' => '交通費',
                'rules' => 'required|numeric',
            ),
        );

        return $config;
    }

    public function getListCount($attrs=array())
    {
        $data = $this->getList($attrs);
        return count($data);
    }

    public function getList($attrs=array())
    {

        $params = array(
            'select' => "*",
            'order_by' => 'type',
        );

        if (isset($attrs['conditions'])) {
            $params['conditions'] = $attrs['conditions'];
        }
        if (isset($attrs['where_special'])) {
            $params['where_special'] = $attrs['where_special'];
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
        // jd($this->db->last_query());

        return $data;
    }


}
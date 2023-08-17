<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Course_code_model extends MY_Model
{
    public $table = 'course_code';
    public $pk = 'id';

    public function __construct()
    {
        parent::__construct();

        $this->init($this->table, $this->pk);

    }

    public function getFormDefault($info=array())
    {
        $data = array_merge(array(
                    'item_id' => '',
                    'name' => '',
                    'remark' => '',
                    'enable' => '',
                ),$info);

        return $data;
    }

    public function getVerifyConfig()
    {
        $config = array(
            'item_id' => array(
                'field' => 'item_id',
                'label' => '代碼',
                'rules' => 'trim|required|is_unique[course_code.item_id]|max_length[6]',
            ),
            'name' => array(
                'field' => 'name',
                'label' => '名稱',
                'rules' => 'trim|required|max_length[100]',
            ),
            'remark' => array(
                'field' => 'remark',
                'label' => '是否為通識課程',
                'rules' => 'trim|in_list[Y,N]',
            ),
            'enable' => array(
                'field' => 'enable',
                'label' => '啟用',
                'rules' => 'required|in_list[0,1]',
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
            'select' => 'id, item_id, name, remark',
            'order_by' => 'item_id',
        );
        if (isset($attrs['conditions'])) {
            $params['conditions'] = $attrs['conditions'];
        }
        if (isset($attrs['where_in'])) {
            $params['where_in'] = $attrs['where_in'];
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
                    array('field' => 'item_id', 'value'=>$attrs['q'], 'position'=>'both'),
                    array('field' => 'name', 'value'=>$attrs['q'], 'position'=>'both'),
                    array('field' => 'remark', 'value'=>$attrs['q'], 'position'=>'both'),
                ),
            );
            // unset
        }

        $data = $this->getData($params);

        return $data;
    }

    public function getChoices()
    {
        $choices = array();
        $attrs['conditions'] = array(
            'enable' => '1',
        );
        $data = $this->getList($attrs);
        foreach ($data as $row) {
            $choices[$row['item_id']] = $row['name'];
        }
        return $choices;
    }

    public function ge_by_item_id($item_id=NULL)
    {
        $course_list = array();
        $item_id = explode(",",$item_id);
        $attrs['where_in'] = array(
            'field' => 'item_id',
            'value' => $item_id,
        );
        $data = $this->getList($attrs);
        foreach ($data as $row) {
            $course_list[$row['item_id']] = $row['name'];
        }
        return $course_list;
    }

    public function ge_course_by_name($value=NULL)
    {
        if ($value) {
            $course_id = array();
            $params['or_like'] = array(
                'many' => TRUE,
                'data' => array(
                    array('field' => 'name', 'value'=>$value, 'position'=>'both'),
                ),
            );
            $data = $this->getData($params);
            foreach ($data as $row) {
                $course_id[] = $row['item_id'];
            }
            return $course_id;
        }

    }

}
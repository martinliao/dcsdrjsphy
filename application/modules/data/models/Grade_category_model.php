<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Grade_category_model extends MY_Model
{
    public $table = 'grade_category';
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
                'rules' => 'trim|required|is_unique[series_category.item_id]',
            ),
            'name' => array(
                'field' => 'name',
                'label' => '名稱',
                'rules' => 'trim|required',
            ),
            'remark' => array(
                'field' => 'remark',
                'label' => '備註',
                'rules' => 'trim',
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
        if (isset($attrs['q'])) {
            $params['or_like'] = array(
                'many' => TRUE,
                'data' => array(
                    array('field' => 'name', 'value'=>$attrs['q'], 'position'=>'both'),
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
        $data = $this->getList();
        foreach ($data as $row) {
            $choices[$row['item_id']] = $row['name'];
        }
        return $choices;
    }

    public function getChoicesAdd($conditions=array())
    {
        $attrs = array();
        $attrs['where_special'] = " item_id NOT IN (select distinct grade_type from require_grade where year='{$conditions['year']}' and class_no='{$conditions['class_no']}' and term='{$conditions['term']}') ";
        $choices = array();
        $data = $this->getList($attrs);
        foreach ($data as $row) {
            $choices[$row['item_id']] = $row['name'];
        }
        return $choices;
    }

    public function getChoicesEdit($conditions=array())
    {
        $attrs = array();
        $attrs['where_special'] = " (item_id NOT IN (select distinct grade_type from require_grade where year='{$conditions['year']}' and class_no='{$conditions['class_no']}' and term='{$conditions['term']}') OR item_id='{$conditions['grade_type']}') ";
        $choices = array();
        $data = $this->getList($attrs);
        foreach ($data as $row) {
            $choices[$row['item_id']] = $row['name'];
        }
        return $choices;
    }

}
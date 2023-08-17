<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Train_price_model extends MY_Model
{
    public $table = 'train_price';
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
                    'add_val1' => '',
                    'add_val2' => '',
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
                'rules' => 'trim|required|is_unique[class_property.item_id]',
            ),
            'name' => array(
                'field' => 'name',
                'label' => '名稱',
                'rules' => 'trim|required',
            ),
            'add_val1' => array(
                'field' => 'add_val1',
                'label' => '單程票價',
                'rules' => 'trim|required',
            ),
            'add_val2' => array(
                'field' => 'add_val2',
                'label' => '交通費',
                'rules' => 'trim|required',
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
            'select' => 'id, item_id, name, add_val1, add_val2',
            'order_by' => 'item_id',
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
                    array('field' => 'name', 'value'=>$attrs['q'], 'position'=>'both'),
                ),
            );
            // unset
        }

        $data = $this->getData($params);

        return $data;
    }

}
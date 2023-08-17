<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Reservation_time_model extends MY_Model
{
    public $table = 'reservation_time';
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
                    'start_time' => '00:00',
                    'end_time' => '00:00',
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
                'rules' => 'trim|required|is_unique[reservation_time.item_id]|max_length[5]',
            ),
            'name' => array(
                'field' => 'name',
                'label' => '名稱',
                'rules' => 'trim|required|max_length[100]',
            ),
            'remark' => array(
                'field' => 'remark',
                'label' => '簡稱',
                'rules' => 'trim|required|max_length[100]',
            ),
            'start_time' => array(
                'field' => 'start_time',
                'label' => '起始時間',
                'rules' => 'trim|required',
            ),
            'end_time' => array(
                'field' => 'end_time',
                'label' => '結束時間',
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
            'select' => '*',
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
                    array('field' => 'item_id', 'value'=>$attrs['q'], 'position'=>'both'),
                    array('field' => 'name', 'value'=>$attrs['q'], 'position'=>'both'),
                    array('field' => 'remark', 'value'=>$attrs['q'], 'position'=>'both'),
                ),
            );
            // unset
        }

        $data = $this->getData($params);

        foreach ($data as & $row) {
            $row['start_time'] = substr($row['start_time'], 0,5);
            $row['end_time'] = substr($row['end_time'], 0,5);
        }

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

}
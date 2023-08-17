<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Agent_set_model extends MY_Model
{
    public $table = 'agent_set';
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
                    'undertaker' => '',
                    'name' => '',
                    'ext1' => '',
                    'ext2' => '',
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
                'rules' => 'trim|required|is_unique[agent_set.item_id]',
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
            'select' => 'id, item_id, name, remark, ext1, ext2',
            'order_by' => 'create_time',
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
        // jd($data);
        foreach($data as & $row){
            $conditions = array(
                'idno' => $row['item_id'],
            );
            $person = $this->user_model->get($conditions);
            $row['item_id'] = $person['name'];
        }

        return $data;
    }

    public function _get($id=NULL)
    {
        $data = $this->get($id);
        $conditions = array(
            'idno' => $data['item_id'],
        );
        $person = $this->user_model->get($conditions);
        $data['undertaker'] = $person['name'];
        return $data;
    }

}
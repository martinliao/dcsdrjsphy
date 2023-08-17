<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Unit_management_model extends MY_Model
{
    public $table = 'applicant';
    public $pk = 'app_id';

    public function __construct()
    {
        parent::__construct();

        $this->init($this->table, $this->pk);

    }

    public function getFormDefault($info=array())
    {
        $data = array_merge(array(
                    'app_name' => '',
                    'is_public' => 'N',
                    'contact_name' => '',
                    'tel' => '',
                    'fax' => '',
                    'zone' => '',
                    'addr' => '',
                    'email' => '',
                    'memo' => '',
                ),$info);

        return $data;
    }

    public function getVerifyConfig()
    {
        $config = array(
            'app_name' => array(
                'field' => 'app_name',
                'label' => '名稱',
                'rules' => 'trim|required|max_length[50]',
            ),
            'is_public' => array(
                'field' => 'is_public',
                'label' => '是否為市府單位',
                'rules' => 'trim|in_list[Y,N]',
            ),
            'contact_name' => array(
                'field' => 'contact_name',
                'label' => '聯絡人姓名',
                'rules' => 'trim|required|max_length[32]',
            ),
            'tel' => array(
                'field' => 'tel',
                'label' => '電話',
                'rules' => 'trim|required|max_length[18]',
            ),
            'fax' => array(
                'field' => 'fax',
                'label' => '電話',
                'rules' => 'trim|max_length[18]',
            ),
            'zone' => array(
                'field' => 'zone',
                'label' => '郵遞區號',
                'rules' => 'trim|max_length[5]',
            ),
            'addr' => array(
                'field' => 'addr',
                'label' => '詳細地址',
                'rules' => 'trim|max_length[100]',
            ),
            'email' => array(
                'field' => 'email',
                'label' => 'EMail',
                'rules' => 'trim|valid_email|max_length[100]',
            ),
            'memo' => array(
                'field' => 'memo',
                'label' => '備註',
                'rules' => 'trim|max_length[100]',
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

        $data = $this->getList($attrs);
        return count($data);
    }

    public function getList($attrs=array())
    {
        $params = array(
            'select' => 'app_id, app_name, is_public, contact_name, tel, fax, zone, addr, email',
            'order_by' => 'cre_date',
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
        if (isset($attrs['where_special'])) {
            $params['where_special'] = $attrs['where_special'];
        }
        if (isset($attrs['app_name'])) {
            $params['or_like'] = array(
                'many' => TRUE,
                'data' => array(
                    array('field' => 'app_name', 'value'=>$attrs['app_name'], 'position'=>'both'),
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

}
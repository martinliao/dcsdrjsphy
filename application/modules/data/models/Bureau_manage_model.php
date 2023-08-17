<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Bureau_manage_model extends MY_Model
{
    public $table = 'bureau';
    public $pk = 'id';

    public function __construct()
    {
        parent::__construct();

        $this->init($this->table, $this->pk);

    }

    public function getFormDefault($info=array())
    {
        $data = array_merge(array(
                    'bureau_id' => '',
                    'name' => '',
                    'bureau_level' => '',
                    'parent_id' => '',
                    'parent_name' => '',
                    'effective_date' => '',
                    'abolish_date' => '',
                    'position' => ''
                ),$info);

        return $data;
    }

    public function getVerifyConfig()
    {
        $config = array(
            'bureau_id' => array(
                'field' => 'bureau_id',
                'label' => '局處代碼',
                'rules' => 'trim|required|is_unique[bureau.bureau_id]',
            ),
            'name' => array(
                'field' => 'name',
                'label' => '局處名稱',
                'rules' => 'trim|required',
            ),
            'bureau_level' => array(
                'field' => 'bureau_level',
                'label' => '機關層級',
                'rules' => 'required',
            ),
            'parent_id' => array(
                'field' => 'parent_id',
                'label' => '主管機關代碼',
                'rules' => 'trim',
            ),
            'parent_name' => array(
                'field' => 'parent_name',
                'label' => '主管機關名稱',
                'rules' => 'trim',
            ),
            'position' => array(
                'field' => 'position',
                'label' => '私人機關',
                'rules' => 'required',
            ),
        );

        return $config;
    }

    public function getNewFormDefault($info=array())
    {
        $data = array_merge(array(
                    'new_bureau_id' => '',
                    'new_name' => '',
                    'new_bureau_level' => '',
                    'new_parent_id' => '',
                    'new_parent_name' => '',
                    'new_effective_date' => '',
                    'abolish_date' => ''
                ),$info);

        return $data;
    }

    public function getNewVerifyConfig()
    {
        $config = array(
            'new_bureau_id' => array(
                'field' => 'new_bureau_id',
                'label' => '新局處代碼',
                'rules' => 'trim|required|is_unique[bureau.bureau_id]',
            ),
            'new_name' => array(
                'field' => 'new_name',
                'label' => '新局處名稱',
                'rules' => 'trim|required',
            ),
            'new_bureau_level' => array(
                'field' => 'new_bureau_level',
                'label' => '新機關層級',
                'rules' => 'required',
            ),
            'new_parent_id' => array(
                'field' => 'parent_id',
                'label' => '主管機關代碼',
                'rules' => 'trim',
            ),
            'new_parent_name' => array(
                'field' => 'new_parent_name',
                'label' => '新主管機關名稱',
                'rules' => 'trim',
            ),
            'abolish_date' => array(
                'field' => 'abolish_date',
                'label' => '機關裁撤日期',
                'rules' => 'required',
            ),
            'new_effective_date' => array(
                'field' => 'new_effective_date',
                'label' => '新機關生效日期',
                'rules' => 'required',
            )
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
            'select' => 'id, bureau_id, name, bureau_level, effective_date, abolish_date, del_flag, position',
            'order_by' => 'bureau_level',
        );

        if (!isset($attrs['del_flag'])) {
            $attrs['conditions']['(del_flag != "C" or del_flag is null)'] = NULL;
            // $params['where_not_in']['field'] = 'del_flag';
            // $params['where_not_in']['value'] = 'C';
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

        $date_like = array();
        if (isset($attrs['bureau_id'])) {
            $like_item_id = array(
                array('field' => 'bureau_id', 'value'=>$attrs['bureau_id'], 'position'=>'both')
            );
            $date_like = array_merge($date_like, $like_item_id);
        }
        if (isset($attrs['name'])) {
            $like_name = array(
                array('field' => 'name', 'value'=>$attrs['name'], 'position'=>'both'),
            );
            $date_like = array_merge($date_like, $like_name);
        }


        if (isset($date_like)) {
            $params['like'] = array(
                'many' => TRUE,
                'data' => $date_like,
            );
        }


        $data = $this->getData($params);

        return $data;
    }

    function getBureauName($bureau_id){
        $params = array(
            'select' => 'name'
        );

        $params = array('conditions' => array('bureau_id' => $bureau_id));

        $data = $this->getData($params);

        return $data[0]['name'];
    }

    function updateUserBureau($old_bureau_id,$new_bureau_id,$new_bureau_name){
        $this->db->set('bureau_id', $new_bureau_id);
        $this->db->set('bureau_name',$new_bureau_name);
        $this->db->where('bureau_id', $old_bureau_id);
        $this->db->update('BS_user');

        return TRUE;
    }

    public function getBureauListCount($attrs=array())
    {

        $data = $this->getList($attrs);
        return count($data);
    }

    public function getBureauList($attrs=array())
    {
        $params = array(
            'select' => 'id, bureau_id, name, bureau_level, effective_date, abolish_date, del_flag, position',
            'order_by' => 'bureau_level',
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

        $date_like = array();
        if (isset($attrs['bureau_id'])) {
            $like_item_id = array(
                array('field' => 'bureau_id', 'value'=>$attrs['bureau_id'], 'position'=>'both')
            );
            $date_like = array_merge($date_like, $like_item_id);
        }
        if (isset($attrs['name'])) {
            $like_name = array(
                array('field' => 'name', 'value'=>$attrs['name'], 'position'=>'both'),
            );
            $date_like = array_merge($date_like, $like_name);
        }


        if (isset($date_like)) {
            $params['like'] = array(
                'many' => TRUE,
                'data' => $date_like,
            );
        }


        $data = $this->getData($params);

        return $data;
    }

}
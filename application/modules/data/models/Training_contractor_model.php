<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Training_contractor_model extends MY_Model
{
    public $table = 'BS_user';
    public $pk = 'id';

    public function __construct()
    {
        parent::__construct();

        $this->init($this->table, $this->pk);

    }

    public function getFormDefault($user=array())
    {
        $data = array_merge(array(
            'name' => '',
            'co_usrnick' => '',
            'gender' => '',
            'idno' => '',
            'birthday' => '',
            'email' => '',
            'office_email' => '',
            'bureau_id' => '',
            'bureau_name' => '',
            'office_tel' => '',
            'cellphone' => '',
            'job_title' => '',
            'job_title_name' => '',
            'office_fax' => '',
            'enable' => 0,
        ), $user);

        return $data;
    }

    public function getVerifyConfig()
    {
        $config = array(
            'name' => array(
                'field' => 'name',
                'label' => '姓名',
                'rules' => 'trim|required',
            ),
            'idno' => array(
                'field' => 'idno',
                'label' => '身分證字號',
                'rules' => 'trim|required|alpha_numeric|is_unique[BS_user.idno]|is_unique[BS_user.username]',
            ),
            'birthday' => array(
                'field' => 'birthday',
                'label' => '生日',
                'rules' => 'trim|required',
            ),
            'email' => array(
                'field' => 'email',
                'label' => 'E-Mail',
                'rules' => 'trim|valid_email',
            ),
            'office_email' => array(
                'field' => 'office_email',
                'label' => '公司E-Mail',
                'rules' => 'trim|required|valid_email',
            ),
            'office_tel' => array(
                'field' => 'office_tel',
                'label' => '公司電話',
                'rules' => 'trim|required',
            ),
            'bureau_name' => array(
                'field' => 'bureau_name',
                'label' => '局處名稱',
                'rules' => 'trim|required',
            ),
        );

        return $config;
    }

    public function getList($attrs=array())
    {
        $params = array(
            'select' => 'BS_user.id, BS_user.username, BS_user.name, BS_user.co_usrnick, BS_user.bureau_id, BS_user.job_title, BS_user.office_tel, BS_user.office_fax, BS_user.email, bureau.name as bureau_name, job_title.name as job_title_name',
            'order_by' => 'BS_user.username, BS_user.update_time',
        );

        $params['join'] = array(array('table' => 'bureau',
                                    'condition' => 'bureau.bureau_id = BS_user.bureau_id',
                                    'join_type' => 'left'),
                                    array('table' => 'job_title',
                                    'condition' => 'job_title.item_id = BS_user.job_title',
                                    'join_type' => 'left'),
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
                    array('field' => 'BS_user.name', 'value'=>$attrs['q'], 'position'=>'both'),
                    array('field' => 'BS_user.username', 'value'=>$attrs['q'], 'position'=>'both'),
                    array('field' => 'BS_user.co_usrnick', 'value'=>$attrs['q'], 'position'=>'both'),
                    array('field' => 'BS_user.job_title', 'value'=>$attrs['q'], 'position'=>'both'),
                    array('field' => 'BS_user.email', 'value'=>$attrs['q'], 'position'=>'both'),
                    array('field' => 'BS_user.office_tel', 'value'=>$attrs['q'], 'position'=>'both'),
                ),
            );
            // unset
        }

        $data = $this->getData($params);

        return $data;
    }

    public function getListCount($attrs=array())
    {
        // $params = array(
        //     'conditions' => $attrs['conditions'],
        // );

        // if (isset($attrs['q'])) {
        //     $params['q'] = $attrs['q'];
        // }
        $data = $this->getList($attrs);
        return count($data);
    }

    public function getBureau($bureau_id){
        $this->db->select('name');
        $this->db->where('bureau_id',$bureau_id);
        $query = $this->db->get('bureau');
        $result = $query->row_array();

        if(!empty($result)){
            return $result['name'];
        } else {
            return '';
        }
    }

    public function getJobTitle($job_title){
        $this->db->select('name');
        $this->db->where('item_id',$job_title);
        $query = $this->db->get('job_title');
        $result = $query->row_array();

        if(!empty($result)){
            return $result['name'];
        } else {
            return '';
        }
    }

    public function _insert($fields=array())
    {
        $fields['idno'] = strtoupper($fields['idno']);

        if (isset($fields['idno']) && $fields['idno'] != '') {
            $fields['password'] = md5($fields['idno']);
        }

        $fields['username'] = $fields['idno'];
        //$fields['user_group_id'] = '5';

        return $this->insert($fields, 'date_added');
    }

    public function _update($pk, $fields=array()) {
        return parent::update($pk, $fields);
    }
}


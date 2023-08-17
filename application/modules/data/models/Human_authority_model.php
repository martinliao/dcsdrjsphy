<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Human_authority_model extends MY_Model
{
    public $table = 'account_role';
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
                'rules' => 'trim|required|alpha_numeric|is_unique[BS_user.idno]',
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
            'select' => 'BS_user.id, BS_user.u_name, BS_user.name, BS_user.co_usrnick, BS_user.bureau_id, BS_user.job_title, BS_user.office_tel, BS_user.office_fax, BS_user.email, bureau.b_name as bureau_name, job_title.t_name as job_title_name',
            'order_by' => 'account_role.username',
            'group_by' => 'BS_user.u_name'
        );

        $params['join'] = array(
                    array(
                        'table' => '(SELECT id, name, idno, co_usrnick, job_title, office_tel, office_fax, email, telephone, username as u_name, bureau_id from BS_user) as BS_user',
                        'condition'=>'BS_user.u_name = account_role.username',
                        'join_type'=>'left',
                    ),
                    array(
                        'table' => '(SELECT bureau_id as b_id, name as b_name from bureau) as bureau',
                        'condition'=>'bureau.b_id = BS_user.bureau_id',
                        'join_type'=>'left',
                    ),
                    array('table' => '(SELECT item_id, name as t_name from job_title) as job_title ',
                        'condition' => 'job_title.item_id = BS_user.job_title',
                        'join_type' => 'left',
                    ),

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
        $date_like = array();
        if (isset($attrs['bureau_name'])) {
            $like_bureau_name = array(
                array('field' => 'b_name', 'value'=>$attrs['bureau_name'], 'position'=>'both')
            );
            $date_like = array_merge($date_like, $like_bureau_name);

        }
        if (isset($attrs['name'])) {
            $like_name = array(
                array('field' => 'name', 'value'=>$attrs['name'], 'position'=>'both')
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

    public function getListCount($attrs=array())
    {
        $params = array(
            'conditions' => $attrs['conditions'],
        );
        if(isset($attrs['bureau_name'])){
            $params['bureau_name']=$attrs['bureau_name'];
        }
        if(isset($attrs['name'])){
            $params['name']=$attrs['name'];
        }
     

        if (isset($attrs['q'])) {
            $params['q'] = $attrs['q'];
        }
        $data = $this->getList($params);
        
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


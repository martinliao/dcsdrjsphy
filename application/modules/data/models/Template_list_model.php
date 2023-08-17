<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Template_list_model extends MY_Model
{
    public $table = 'template';
    public $pk = 'id';
    protected $locale = array();

    public function __construct()
    {
        parent::__construct();

        $this->init($this->table, $this->pk);

        $this->item_id = array(
            '01' => '課表類',
            '02' => 'EMAIL',
            '03' => '線上簽核',  //20210712新增線上簽核使用
        );

    }

    public function getFormDefault($data=array())
    {
        $fields = array(
            'item_id' => '',
            'content' => '',
            'title' => '',
            'is_open' => '1',
        );

        return array_merge($fields, $data);
    }

    public function getVerifyConfig()
    {
        $config = array(
            'item_id' => array(
                'field' => 'item_id',
                'label' => '範本類別',
                'rules' => 'required',
            ),
            'content' => array(
                'field' => 'content',
                'label' => '範本內容',
                'rules' => 'trim|required',
            ),
            'title' => array(
                'field' => 'title',
                'label' => '範本名稱',
                'rules' => 'trim|required',
            ),
            'is_open' => array(
                'field' => 'is_open',
                'label' => '是否開放',
                'rules' => 'required|in_list[0,1]',
            ),
        );

        return $config;
    }

    public function getList($attrs=array())
    {

        $params = array(
            'select' => '',
            'order_by' => 'tmp_seq asc',
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


        $data = $this->getData($params);


        return $data;
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

    public function getChoices()
    {
        $choices = array();
        $data = $this->getList();
        foreach ($data as $row) {
            $choices[$row['id']] = $row['name'];
        }
        return $choices;
    }

    public function getInfo($id=NULL)
    {
        $data = $this->get($id);

        return $data;
    }

    public function getMaxSeq($item_id){
        $this->db->select('max(tmp_seq) max_seq');
        $this->db->where('item_id',$item_id);
        $query = $this->db->get('template');
        $result = $query->result_array();

        if(!empty($result)){
            return $result[0]['max_seq'];
        }

        return '-1';
    }

    public function getMinSeq($item_id){
        $this->db->select('min(tmp_seq) min_seq');
        $this->db->where('item_id',$item_id);
        $query = $this->db->get('template');
        $result = $query->result_array();

        if(!empty($result)){
            return $result[0]['min_seq'];
        }

        return '-1';
    }

    public function getPreSeqData($conditions=array()){
        $this->db->select('max(tmp_seq) pre_seq,id');
        $this->db->where('item_id',$conditions['item_id']);
        $this->db->where('tmp_seq <',$conditions['tmp_seq']);
        $query = $this->db->get('template');
        $result = $query->result_array();

        $this->db->select('tmp_seq as pre_seq,id');
        $this->db->where('item_id',$conditions['item_id']);
        $this->db->where('tmp_seq',$result[0]['pre_seq']);
        $query = $this->db->get('template');
        $result = $query->result_array();

        return $result;
    }

    public function getNextSeqData($conditions=array()){
        $this->db->select('min(tmp_seq) next_seq,id');
        $this->db->where('item_id',$conditions['item_id']);
        $this->db->where('tmp_seq >',$conditions['tmp_seq']);
        $query = $this->db->get('template');
        $result = $query->result_array();

        $this->db->select('tmp_seq as next_seq,id');
        $this->db->where('item_id',$conditions['item_id']);
        $this->db->where('tmp_seq',$result[0]['next_seq']);
        $query = $this->db->get('template');
        $result = $query->result_array();

        return $result;
    }
}

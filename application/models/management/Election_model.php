<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Election_model extends MY_Model
{
    public $table = 'require';
    public $pk = 'seq_no';

    public function __construct()
    {
        parent::__construct();

        $this->init($this->table, $this->pk);

    }

    public function getListCount($attrs=array())
    {

        $data = $this->getList($attrs);
        return count($data);
    }

    public function getList($attrs=array())
    {

        $params = array(
            'select' => "*",
            'order_by' => 'class_no, term',
        );

        $date_like = array();
        if (isset($attrs['class_no'])) {
            $like_idno = array(
                array('field' => 'class_no', 'value'=>$attrs['class_no'], 'position'=>'both')
            );
            $date_like = array_merge($date_like, $like_idno);
        }
        if (isset($attrs['class_name'])) {
            $like_name = array(
                array('field' => 'class_name', 'value'=>$attrs['class_name'], 'position'=>'both')
            );
            $date_like = array_merge($date_like, $like_name);
        }
        if (isset($date_like)) {
            $params['like'] = array(
                'many' => TRUE,
                'data' => $date_like,
            );
        }

        if (isset($attrs['conditions'])) {
            $params['conditions'] = $attrs['conditions'];
        }

        if (isset($attrs['where_special'])) {
            $params['where_special'] = $attrs['where_special'];
        }
        if (isset($attrs['class_status'])) {
            $params['where_in'] = array(
                'field' => 'class_status',
                'value' => $attrs['class_status'],
            );
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

    public function get_regist_list($conditions=array())
    {

        $this->db->select("IFNULL(o.yn_sel,'x') yn_sel, o.year, o.class_no, o.term, o.id, o.memo, o.st_no, o.priority, v.bureau_id, v.name, IFNULL(v.retirement, '1') as retirement, v.idno pid, cd.name as pos_name, if(v.out_gov_name IS NULL or v.out_gov_name='', cd1.name, v.out_gov_name) bea_name, o.insert_date, o.insert_order, o.group_no, o.ori_term, IFNULL(dis.memo,'') as phydis");
        $this->db->from('online_app o');
        $this->db->join('BS_user v', "o.id=v.idno", 'left');
        $this->db->join('phydisabled dis', "dis.gid = o.id", 'left');
        $this->db->join('job_title cd', "v.job_title=cd.item_id", 'left outer');
        $this->db->join('bureau cd1', "v.bureau_id=cd1.bureau_id", 'left');
        // $this->db->join('out_gov og', "v.idno = og.id", 'left outer');
        $this->db->where("o.year",$conditions['year']);
        $this->db->where("o.term",$conditions['term']);
        $this->db->where("o.class_no",$conditions['class_no']);
        $this->db->where("IFNULL(o.yn_sel,'x') not in ('6','7','x')");
        $this->db->order_by('isnull(st_no),st_no');
        if(!empty($conditions['id1'])){
            $this->db->where(" o.id in (" . $conditions['id1'] . ") ");
        }


        $query = $this->db->get();
        $regist_list = $query->result_array();
        return $regist_list;
    }

    public function get_counter($conditions=array())
    {

        $this->db->select("MAX(st_no) as counter");
        $this->db->from('online_app');
        $this->db->where("year",$conditions['year']);
        $this->db->where("term",$conditions['term']);
        $this->db->where("class_no",$conditions['class_no']);
        $query = $this->db->get();
        $data = $query->row_array();
        if(empty($data['counter'])){
            $data['counter'] = 0;
        }
        return $data['counter'];
    }

    public function get_disableCount($conditions=array())
    {
        $this->db->from('online_app');
        $this->db->where("year",$conditions['year']);
        $this->db->where("term",$conditions['term']);
        $this->db->where("class_no",$conditions['class_no']);
        $this->db->where("yn_sel in ('1','4','5')");
        $disableCount = $this->db->count_all_results();
        return $disableCount;
    }

    public function get_max_group($conditions=array())
    {
        $this->db->select("MAX(group_no) as max_g");
        $this->db->from('online_app');
        $this->db->where("year",$conditions['year']);
        $this->db->where("term",$conditions['term']);
        $this->db->where("class_no",$conditions['class_no']);
        $this->db->where("IFNULL(yn_sel,'X') not in ('6','7','X')");
        $query = $this->db->get();
        $data = $query->row_array();
        if(empty($data['max_g'])){
            $data['max_g'] = '';
        }
        return $data['max_g'];
    }

    public function get_person($conditions=array())
    {
        $this->db->select("*");
        $this->db->from('online_app');
        $this->db->where("year",$conditions['year']);
        $this->db->where("term",$conditions['term']);
        $this->db->where("class_no",$conditions['class_no']);
        $this->db->where("id",$conditions['id']);
        $query = $this->db->get();
        $data = $query->row_array();

        $this->db->select("class_name");
        $this->db->from('require');
        $this->db->where("year",$conditions['year']);
        $this->db->where("term",$conditions['term']);
        $this->db->where("class_no",$conditions['class_no']);
        $query = $this->db->get();
        $class_name = $query->row_array();

        $this->db->select("name");
        $this->db->from('bureau');
        $this->db->where("bureau_id",$data['beaurau_id']);
        $query = $this->db->get();
        $bureau_name = $query->row_array();

        $class_name['bureau_name'] = $bureau_name['name'];

        return $class_name;
    }

}
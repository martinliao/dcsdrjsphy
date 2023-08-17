<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Class_record_model extends MY_Model
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
            'select' => " year, class_no, class_name, term, start_date1, end_date1",
            'order_by' => 'class_no, term',
        );

        $date_like = array();

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

        if (isset($attrs['rows'])) {
            $params['rows'] = $attrs['rows'];
        }
        if (isset($attrs['offset'])) {
            $params['offset'] = $attrs['offset'];
        }
        if (isset($attrs['sort'])) {
            $params['order_by'] = $attrs['sort'];
        }
        $this->db->distinct();
        $data = $this->getData($params);

        return $data;
    }

    public function get_regist_list($conditions=array())
    {

        $this->db->select("OA.yn_sel, R.start_date1, R.end_date1, R.class_name, R.term, R.year, IFNULL(og.ou_gov ,BC.name) AS beaurau_name");
        $this->db->from('online_app OA');
        $this->db->join('require R', "R.term=OA.term and R.class_no=OA.class_no and R.year=OA.year", 'left');
        $this->db->join('bureau BC', "BC.bureau_id='{$conditions['bureau_id']}'", 'left');
        $this->db->join('out_gov og', "OA.id = og.id", 'left outer');
        $this->db->where("OA.id",$conditions['idno']);
        $this->db->where("{$conditions['where_special']}");


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
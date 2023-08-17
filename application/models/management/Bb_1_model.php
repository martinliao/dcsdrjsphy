<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Bb_1_model extends MY_Model
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
            'select' => '',
            'order_by' => 'year,class_no, term',
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
        if (isset($attrs['class_no'])) {
            $like_class_no = array(
                array('field' => 'class_no', 'value'=>$attrs['class_no'], 'position'=>'both')
            );
            $date_like = array_merge($date_like, $like_class_no);
        }
        if (isset($attrs['class_name'])) {
            $like_class_name = array(
                array('field' => 'class_name', 'value'=>$attrs['class_name'], 'position'=>'both')
            );
            $date_like = array_merge($date_like, $like_class_name);
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

    public function getPriority($conditions=array()){

        $this->db->from($this->table);
        $this->db->where('year',$conditions['year']);
        $this->db->where('class_no',$conditions['class_no']);
        $this->db->where('term',$conditions['term']);
        $this->db->where('CURDATE() < IFNULL(apply_s_date2 ,CURDATE() + INTERVAL 1 DAY)');
        $priority = $this->db->count_all_results();
        if ($priority > 0) {
            return 1;
        } else {
            return 2;
        }
    }

    public function get_term($conditions=array()){
        $data = array();
        $un_term = $conditions['term'];
        unset($conditions['term']);
        $params = array(
            'conditions' => $conditions,
            'order_by' => 'term desc',
        );
        $term = $this->getData($params);
        foreach ($term as $row) {
            $data[$row['term']] = $row['term'];
        }
        unset($data[$un_term]);
        return $data;
    }


}
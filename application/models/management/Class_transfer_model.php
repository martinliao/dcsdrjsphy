<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Class_transfer_model extends MY_Model
{
    public $table = 'require';
    public $pk = 'seq_no';

    public function __construct()
    {
        parent::__construct();

        $this->init($this->table, $this->pk);

    }

    public function getClassListCount($attrs=array())
    {

        $data = $this->getClassList($attrs);
        return count($data);
    }

    public function getClassList($attrs=array())
    {
    	$params = array(
            'select' => ' class_no, class_name',
            'order_by' => 'class_no',
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

        if (isset($attrs['q'])) {
            $params['or_like'] = array(
                'many' => TRUE,
                'data' => array(
                    array('field' => 'class_no', 'value'=>$attrs['q'], 'position'=>'both'),
                    array('field' => 'class_name', 'value'=>$attrs['q'], 'position'=>'both'),
                ),
            );
            // unset
        }

        $this->db->distinct();
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


}
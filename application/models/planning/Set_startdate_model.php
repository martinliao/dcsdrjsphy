<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Set_startdate_model extends MY_Model
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
        $params = array(
            'conditions' => $attrs['conditions'],
        );

        if (isset($attrs['query_class_name'])) {
            $params['query_class_name'] = $attrs['query_class_name'];
        }
        $data = $this->getList($params);
        return count($data);
    }

    public function getList($attrs=array())
    {
        
        $params = array(
            'select' => 'require.seq_no, require.year, require.class_no, require.class_name, require.term, require.start_date1, require.end_date1',
            'order_by' => 'require.class_no,require.term',
        );

        $params['join'] = array(array('table' => 'series_category', 
                            'condition' => 'series_category.item_id = require.type', 
                            'join_type' => 'left'),
                            array('table' => 'second_category', 
                            'condition' => 'second_category.item_id = require.beaurau_id', 
                            'join_type' => 'left')
                    );

        if (isset($attrs['query_class_name'])) {
            $params['or_like'] = array(
                'many' => TRUE,
                'data' => array(
                    array('field' => 'require.class_name', 'value'=>$attrs['query_class_name'], 'position'=>'both'),
                ),
            );
        }

        if (isset($attrs['query_min_term'])) {

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

        $data = $this->getData($params);

        
        return $data;
    }

    public function getSecondCategory($type){
        $this->db->select('item_id,name');
        $this->db->where('parent_id',$type);
        $query = $this->db->get('second_category');
        $result = $query->result_array();

        return $result;
    }

    public function getSeason($month){
        if($month >= 1 && $month <= 3){
            return 1;
        } else if($month >= 4 && $month <= 6){
            return 2;
        } else if($month >= 7 && $month <= 9){
            return 3;
        } else if($month >= 10 && $month <= 12){
            return 4;
        }
    }

    public function updateStartDate($seq_no,$start_date){
        $tmpArray = explode('-', $start_date);
        $season = $this->getSeason(intval($tmpArray[1]));

        if($season > 0){
            $this->db->set('reason',$season);
        }

    	$this->db->set('start_date1',$start_date);
    	$this->db->where('seq_no',$seq_no);

    	if($this->db->update('require')){
    		return true;
    	}

    	return false;
    }

    public function updateEndDate($seq_no,$end_date){
        $this->db->set('end_date1',$end_date);
        $this->db->where('seq_no',$seq_no);

        if($this->db->update('require')){
            return true;
        }

        return false;
    }
}
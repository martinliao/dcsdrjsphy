<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Annualplan_model extends MY_Model
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

        if (isset($attrs['q'])) {
            $params['q'] = $attrs['q'];
        }
        $data = $this->getList($params);
        return count($data);
    }

    public function getList($attrs=array())
    {
        
        $params = array(
            'select' => 'require.seq_no, require.year,require.class_no,require.class_status,require.start_date1,require.class_name, require.is_assess, require.is_mixed, require.class_status, series_category.name as series_name, second_category.name as second_name',
            'order_by' => 'require.class_no',
        );

        $params['join'] = array(array('table' => 'series_category', 
                            'condition' => 'series_category.item_id = require.type', 
                            'join_type' => 'left'),
                            array('table' => 'second_category', 
                            'condition' => 'second_category.item_id = require.beaurau_id', 
                            'join_type' => 'left')
                    );

        if (isset($attrs['q'])) {
            $params['or_like'] = array(
                'many' => TRUE,
                'data' => array(
                    array('field' => 'series_category.name', 'value'=>$attrs['q'], 'position'=>'both'),
                    array('field' => 'second_category.name', 'value'=>$attrs['q'], 'position'=>'both'),
                    array('field' => 'require.class_name', 'value'=>$attrs['q'], 'position'=>'both'),
                    array('field' => 'require.class_name', 'value'=>$attrs['class_status'], 'position'=>'both'),
                ),
            );
        }

        /*if (isset($attrs['class_status'])) {
            $params['or_like'] = array(
                'many' => TRUE,
                'data' => array(
                    array('field' => 'require.class_name', 'value'=>$attrs['class_status'], 'position'=>'both'),
                ),
            );
        }*/
        $params['group_by'] = 'require.year,require.class_no';

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

    public function updateClassStatus($year,$class_no,$class_status){
    	$this->db->set('class_status',$class_status);
    	$this->db->where('year',$year);
    	$this->db->where('class_no',$class_no);

    	if($this->db->update('require')){
    		return true;
    	}

    	return false;
    }

    public function checkBaseTermExist($year){
        $this->db->select('count(1) cnt');
        $this->db->where('year',$year);
        $query = $this->db->get('base_term');
        $result = $query->result_array();

        if($result[0]['cnt'] > 0){
            return true;
        }

        return false;
    }

    public function setBaseTerm($year){
        $this->db->trans_start();

        $this->db->select('class_no');
        $this->db->where('year',$year);
        $this->db->group_by('year,class_no');
        $query = $this->db->get('require');
        $result = $query->result_array();

        for($i=0;$i<count($result);$i++){
            $this->db->select('count(1) cnt');
            $this->db->where('year',$year);
            $this->db->where('class_no',$result[$i]['class_no']);
            $this->db->where('class_status','2');
            $query = $this->db->get('require');
            $info = $query->result_array();

            $this->db->select('count(1) cnt');
            $this->db->where('year',$year);
            $this->db->where('class_no',$result[$i]['class_no']);
            $query = $this->db->get('base_term');
            $check_exist = $query->result_array();

            if($check_exist[0]['cnt'] > 0){
                $this->db->set('base_term',$info[0]['cnt']);
                $this->db->where('year',$year);
                $this->db->where('class_no',$result[$i]['class_no']);
                $this->db->update('base_term');
            } else {
                $this->db->set('base_term',$info[0]['cnt']);
                $this->db->set('year',$year);
                $this->db->set('class_no',$result[$i]['class_no']);
                $this->db->insert('base_term');
            }
        }

        $this->db->trans_complete();

        if($this->db->trans_status() === TRUE){
            return true;
        } 

        return false;
    }

    function getBaseTerm($year,$class_no){
        $this->db->select('base_term');
        $this->db->where('year',$year);
        $this->db->where('class_no',$class_no);
        $query = $this->db->get('base_term');
        $result = $query->result_array();

        if(!empty($result)){
            return $result[0]['base_term'];
        }

        return '';
    }

}
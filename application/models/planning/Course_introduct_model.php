<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Course_introduct_model extends MY_Model
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
        if (isset($attrs['class_name'])) {
            $params['class_name'] = $attrs['class_name'];
        }
        if (isset($attrs['respondant'])) {
            $params['respondant'] = $attrs['respondant'];
        }

        $data = $this->getList($params);
        return count($data);
    }
    
    
    public function getList($attrs=array())
    {  
        $params = array(
            'select' => 'require.seq_no, require.year,require.respondant,require.term,require.no_persons,require.range_real,require.range_internet,require.env_class,require.policy_class,
                            require.special_status,require.range,require.special_status_other,require.content,require.obj,require.reason,
                            require.class_no,require.class_name,require.worker,require.start_date1,require.end_date1,require.class_status,require.weights,require.room_code,
                            vct.description as type_name ,second_category.name as bureau_name',
            'order_by' => 'require.type,bureau_name,require.class_no,require.term',
        );
        $params['join'] = array(array('table'=>'second_category',
                                'condition' => 'require.beaurau_id=second_category.item_id and second_category.parent_id=require.type',
                                'join_type' => 'left'),
                                array('table'=>'view_code_table as vct',
                                'condition'=>'require.type=vct.item_id and vct.type_id="23"',
                                'join_type'=>'left'),
                        );

        $params['distinct']='require.type,require.range,require.class_name.require.term,require.respondant,require.obj,require.content,require.start_date1,
                             require.end_date1,require.beaurau_id,require.class_no,bureau_name,vct.description';
        $params['group_by']="require.term";
        if (isset($attrs['q'])) {
                $params['or_like'] = array(
                    'many' => TRUE,
                    'data' => array(
                        array('field' => 'series_category.name', 'value'=>$attrs['q'], 'position'=>'both'),
                        array('field' => 'second_category.name', 'value'=>$attrs['q'], 'position'=>'both'),
                    ),
                );
            }
        if (isset($attrs['class_name'])) {
                $params['or_like'] = array(
                    'many' => TRUE,
                    'data' => array(
                        array('field' => 'class_name', 'value'=>$attrs['class_name'], 'position'=>'both'),
                    ),
                );
            }
            if (isset($attrs['respondant'])) {
                $params['or_like'] = array(
                    'many' => TRUE,
                    'data' => array(
                        array('field' => 'respondant', 'value'=>$attrs['respondant'], 'position'=>'both'),
                    ),
                );
            }
        
        if (isset($attrs['conditions'])) {
            $params['conditions'] = $attrs['conditions'];
        }
        $data = $this->getData($params);
        //die();
        for($i=0;$i<count($data);$i++){
            $data[$i]['max_term']=$this->getMaxTerm($data[$i]['year'],$data[$i]['class_no']);
            $data[$i]['total_persons']=$this->getSumPersons($data[$i]['year'],$data[$i]['class_no']);
            $data[$i]['total_range']=$this->getSumRange($data[$i]['year'],$data[$i]['class_no']);
        }
        return $data;
    }
    public function getMaxTerm($year,$class_no)
    {
        $this->db->select('max(require.term) as max_term');
        $this->db->where('year',$year);
        $this->db->where('class_no',$class_no);
        $query=$this->db->get('require');
        $result=$query->result_array();
        return $result[0]['max_term'];

    }
    public function getSumPersons($year,$class_no)
    {
        $this->db->select('sum(no_persons) as total_persons');
        $this->db->where('year',$year);
        $this->db->where('class_no',$class_no);
        $query=$this->db->get('require');
        $result=$query->result_array();
        return $result[0]['total_persons'];

    }
    public function getSumRange($year,$class_no)
    {
        $this->db->select('sum(require.range) as total_range');
        $this->db->where('year',$year);
        $this->db->where('class_no',$class_no);
        $query=$this->db->get('require');
        $result=$query->result_array();
        return $result[0]['total_range'];
    }
    public function getSecondCategory($type)
    {
        $this->db->select('item_id,name');
        $this->db->where('parent_id',$type);
        $query=$this->db->get('second_category');
        $result=$query->result_array();
        
        return $result;
    }



}

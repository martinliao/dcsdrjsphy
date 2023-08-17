<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Season_course_capture_model extends MY_Model
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
            'select' => 'require.seq_no, require.year,require.respondant,require.term,require.no_persons,require.range_real,require.range_internet,
                         require.range,require.contactor,require.content,require.tel,require.reason,require.is_assess,require.is_mixed,require.sort,
                         require.class_no,require.class_name,require.worker,require.start_date1,require.end_date1,require.class_status,require.weights,require.room_code,
                         series_category.name as series_name,second_category.name as bureau_name',
            'order_by' => 'class_no,term,start_date1,end_date1',
        );
        //var_dump($attrs);

        $params['join'] = array(array('table' => 'view_code_table as vct',
                                    'condition' => 'vct.item_id = require.type and vct.type_id="23"',
                                    'join_type' => 'left'),
                                array('table' => 'series_category',
                                    'condition' => 'series_category.item_id = require.type',
                                    'join_type' => 'left'),
                                array('table'=>'second_category',
                                    'condition' => 'require.beaurau_id=second_category.item_id and second_category.parent_id=require.type',
                                    'join_type' => 'left'),
                            );
                    
        if (isset($attrs['q'])) {
                $params['or_like'] = array(
                    'many' => TRUE,
                    'data' => array(
                        array('field' => 'series_category.name', 'value'=>$attrs['q'], 'position'=>'both'),
                        array('field' => 'second_category.name', 'value'=>$attrs['q'], 'position'=>'both'),
                    ),
                );
            }
      
         
    
        //$params['group_by'] = 'require.year,require.class_no';

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

    public function getBureauName($bureau_id)
    {
        $this->db->select('name as bname');
        $this->db->where('item_id',$bureau_id);
        $query=$this->db->get('second_category');
        $query=$query->result_array();
        //var_dump($query);
        return $query[0]['bname'];
    }



}

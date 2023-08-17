<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Suggest_model extends MY_Model
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
            'select' => 'require.seq_no, require.year, require.class_no,require.class_name,require.start_date1,require.class_status,require.term',
            'order_by' => 'require.year desc,require.class_no,require.term',
        );
        
        $params['join'] = array(array('table' => 'open_suggest as os',
                    'condition' => 'require.year = os.year and require.term = os.term and require.class_no = os.class_no',
                    'join_type' => 'inner'),
                    array('table' => 'BS_user as bu',
                    'condition' => 'bu.username = os.annouce_by',
                    'join_type' => 'left')                       
        );
        $class_status=[2,3];
        $params['where_in']=array('field'=>'require.class_status','value'=>$class_status);

        if (isset($attrs['query_class_name'])) {
            $params['or_like'] = array(
                'many' => TRUE,
                'data' => array(
                    array('field' => 'require.class_name', 'value'=>$attrs['query_class_name'], 'position'=>'both'),
                ),
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
       
        $data = $this->getData($params);
        return $data;
    }
    public function getSuggest($attrs=array())
    {
        $params = array(
            'select' => 'require.seq_no, require.year, require.class_no,require.class_name,require.start_date1,require.class_status,require.term,
                        os.s1, os.s2, os.s3, os.s4, os.s5, os.s6, os.s7, os.s8,os.a1, os.a2, os.a3, os.a4, os.a5, os.a6, os.a7, os.a8,
                        v1.name as a1_by, v2.name as a2_by,v3.name as a3_by,v4.name as a4_by, v5.name as a5_by, v6.name as a6_by, v7.name as a7_by,v8.name as a8_by,
                        os.is_a1_visible, os.is_a2_visible, os.is_a3_visible, os.is_a4_visible, os.is_a5_visible, os.is_a6_visible,os.is_a7_visible, os.is_a8_visible,
                        os.is_annouce,os.annouce_date,v.name as annouce_by, w.name as worker',
            'order_by' => 'require.year desc,require.class_no,require.term',
        );
        $params['join'] = array(array('table' => 'open_suggest as os',
                    'condition' => 'require.year = os.year and require.term = os.term and require.class_no = os.class_no',
                    'join_type' => 'inner'),
                    array('table' => 'BS_user as v',
                    'condition' => 'v.username = os.annouce_by',
                    'join_type' => 'left'),
                    array('table' => 'BS_user as w',
                    'condition' => 'w.idno = require.worker',
                    'join_type' => 'left'),  
                    array('table' => 'BS_user as v1',
                    'condition' => 'v1.username = os.a1_by',
                    'join_type' => 'left'),  
                    array('table' => 'BS_user as v2',
                    'condition' => 'v2.username = os.a2_by',
                    'join_type' => 'left'),  
                    array('table' => 'BS_user as v3',
                    'condition' => 'v3.username = os.a3_by',
                    'join_type' => 'left'),  
                    array('table' => 'BS_user as v4',
                    'condition' => 'v4.username = os.a4_by',
                    'join_type' => 'left'),  
                    array('table' => 'BS_user as v5',
                    'condition' => 'v5.username = os.a5_by',
                    'join_type' => 'left'),  
                    array('table' => 'BS_user as v6',
                    'condition' => 'v6.username = os.a6_by',
                    'join_type' => 'left'),  
                    array('table' => 'BS_user as v7',
                    'condition' => 'v7.username = os.a7_by',
                    'join_type' => 'left'),  
                    array('table' => 'BS_user as v8',
                    'condition' => 'v8.username = os.a8_by',
                    'join_type' => 'left'),                      
        );

        if (isset($attrs['conditions'])) {
            $params['conditions'] = $attrs['conditions'];
        }
        //var_dump($attrs);
        $data = $this->getData($params);
        return $data;
    }


    
}
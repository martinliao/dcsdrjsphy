<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Season_schedule_model extends MY_Model
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
                         require.range,require.contactor,require.content,require.tel,require.reason,require.is_assess,require.is_mixed,
                         require.class_no,require.class_name,require.worker,require.start_date1,require.end_date1,require.class_status,require.weights,require.room_code,bu.name as bu_name,
                         sc.name as bureau_name,series_category.name as type_name',
            'order_by' => 'require.type,require.beaurau_id,require.class_no,require.term'
        );


        /*$params['join'] = array(array('table' => 'series_category',
                                'condition' => 'series_category.item_id = require.type',
                                'join_type' => 'left'),
                                array('table' => 'second_category',
                                'condition' => 'second_category.item_id = require.beaurau_id',
                                'join_type' => 'left'),                      
                            );*/
        
        $params['join'] = array(array('table'=>'second_category as sc',
                                    'condition'=>'require.beaurau_id = sc.item_id and require.type=sc.parent_id',
                                    'join_type'=>'left'),
                                array('table'=>'BS_user as bu',
                                    'condition'=>'require.worker=bu.idno',
                                    'join_type'=>'left'),
                                    array('table' => 'series_category',
                                    'condition' => 'series_category.item_id = require.type',
                                    'join_type' => 'left'),
                                );
                    
        //$params['group_by'] = 'require.year,require.class_no';
        $class_status=[2,3];
        $params['where_in']=array('field'=>'class_status','value'=>$class_status);

        if (isset($attrs['conditions'])) {
            $params['conditions'] = $attrs['conditions'];
            
        }
       
        if (isset($attrs['sort'])) {
            $params['order_by'] = $attrs['sort'];
        }

        $data = $this->getData($params);

        return $data;
    }



}

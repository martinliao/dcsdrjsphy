<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Practice_order_table_model extends MY_Model
{
    public $table = 'require';
    public $pk = 'seq_no';

    public function __construct()
    {
        parent::__construct();

        $this->init($this->table, $this->pk);
    }
    public function getListCourseCount($attrs,$bureau_id)
    {
        /*$params = array(
            'conditions' => $attrs['conditions'],
        );*/
    
        $data = $this->getCourseList($bureau_id,$attrs);
        return count($data);
    }
    
    

    public function getList($attrs=array(),$bureau_id)
    {
        
        $params = array(
            'select' => 'require.seq_no, require.year,require.respondant,require.term,require.no_persons,require.range_real,require.range_internet,require.map1,require.map2,require.map3,require.map4,require.map5,require.map6,require.map7,require.map8,
                         require.map9,require.map10,require.map11,
                         require.range,require.contactor,require.content,require.tel,require.reason,require.is_assess,require.is_mixed,require.sort,
                         require.class_no,require.class_name,require.worker,require.start_date1,require.end_date1,require.class_status,require.weights,require.room_code,
                         series_category.name as series_name,bureau.name as bureau_name,BS_user.name as bs_name,second_category.name as second_name,count(1) as total_terms',
            'order_by' => 'require.sort asc,series_name,second_name,require.class_no',
        );


        $params['join'] = array(array('table' => 'series_category',
                                'condition' => 'series_category.item_id = require.type',
                                'join_type' => 'left'),
                                array('table' => 'second_category',
                                'condition' => 'second_category.item_id = require.beaurau_id',
                                'join_type' => 'left'),
                                array('table' => 'BS_user',
                                'condition' => 'require.worker = BS_user.idno',
                                'join_type' => 'left'),
                                array('table' => 'bureau',
                                'condition' => 'require.beaurau_id = bureau.bureau_id',
                                'join_type' => 'left')                          
                            );
        
        //$params['where_in']=array('field'=>)
                    
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

            if (isset($attrs['query_sort'])) {
                $params['order_by']='sort asc';
            }
    
        $params['group_by'] = 'require.year,require.class_no,require.class_name';

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

    public function getCourseList($bureau_id,$data=array())
    {
        $this->db->select('temp.bureau_id');
        $this->db->where('temp.bureau_id',$bureau_id);
        $this->db->where('temp.del_flag !=','C');
        $this->db->or_where('temp.del_flag',null);
        $query1=$this->db->get('bureau as temp');
        $query1=$query1->result_array();
        if(empty($query1)){
            $query1[0]['bureau_id']=null;
        }

        $this->db->select('temp2.bureau_id');
        $this->db->where('temp2.parent_id',$bureau_id);
        $this->db->where('temp2.del_flag !=','C');
        $this->db->or_where('temp2.del_flag',null);
        $query2=$this->db->get('bureau as temp2');
        $query2=$query2->result_array();
        if(empty($query2)){
            $query2[0]['bureau_id']=null;
        }

        $condition=[$query1[0]['bureau_id'],$query2[0]['bureau_id']];


        $this->db->select('a.seq_no,a.year,a.class_no,a.class_name,count(*) as total_term,b.description as type_name,a.sort,b1.name as bureau_name,a.term,a.map1,a.map2,a.map3,a.map4,a.map5,a.map6,a.map7,a.map8,a.map9,a.map10,a.map11');
        $this->db->join('view_code_table as b','a.type=b.item_id and b.type_id="23"','left')
                 ->join('bureau as b1','a.dev_type = b1.bureau_id');
        
        if(isset($data['year'])){
            $this->db->where('a.year',$data['year']);
        }
        if(isset($data['class_no'])){
            $this->db->where('a.class_no',$data['class_no']);
        }
        if(isset($data['class_name'])){
            $this->db->like('class_name',$data['class_name'],'both');
        }
        if(isset($data['query_sort'])){
            $this->db->order_by('a.sort asc');
        }
        if(isset($data['term'])){
            $this->db->where('a.term','1');
        }
        /*if (isset($data['rows']) && isset($data['offset'])) {
            $this->db->limit($data['rows'], $data['offset']);
        } elseif (isset($data['rows'])) {
            $this->db->limit($data['rows']);
        }*/

        $this->db->where_in('a.dev_type',$condition);
        $this->db->or_where_in('bureau_id',$bureau_id);
        
        if(isset($data['year'])){
            $this->db->where('a.year',$data['year']);
     
        }
        if(isset($data['class_no'])){
            $this->db->where('a.class_no',$data['class_no']);
        }
        if(isset($data['class_name'])){
            $this->db->like('class_name',$data['class_name'],'both');
        }
        if(isset($data['query_sort'])){
            $this->db->order_by('a.sort asc');
        }
        /*if(isset($data['term'])){
            $this->db->where('a.term','1');
        }*/

        if (isset($data['rows']) && isset($data['offset'])) {
            $this->db->limit($data['rows'], $data['offset']);
        } elseif (isset($data['rows'])) {
            $this->db->limit($data['rows']);
        }

        $this->db->group_by('a.year,a.class_no,a.class_name,b.description,a.type,b1.name');

        $this->db->order_by('type_name desc,a.sort asc');
        $query=$this->db->get('require as a');
        $result=$query->result_array();
        
        return $result;

    }

}

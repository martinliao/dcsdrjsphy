<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Month_course_model extends MY_Model
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
            'select' => 'require.seq_no,require.no_persons,require.year,require.contactor,require.content,require.apply_s_date,require.apply_e_date,
                         require.apply_s_date2,require.apply_e_date2,count(term)as max_term,
                         (select count(*) from online_app where class_no = require.class_no and year = require.year and term=require.term and yn_sel !="6") as a_count,
                         require.class_no,require.class_name,require.worker,require.start_date1,require.end_date1,max(no_persons) as sum_people,
                         series_category.name as series_name,bu.name as name,sc.name as sc_name'
                         ,
            'order_by' => 'beaurau_id asc',
        );


        $params['join'] = array(array('table' => 'series_category',
                                'condition' => 'series_category.item_id = require.type',
                                'join_type' => 'left'),
                                array('table'=>'view_code_table as vct',
                                'condition'=>'vct.type_id="06" and vct.item_id = require.dev_type',
                                'join_type'=>'left'),
                                array('table'=>'BS_user as bu',
                                'condition'=>'bu.idno=require.worker',
                                'join_type'=>'left'),
                                array('table'=>'second_category as sc',
                                'condition'=>'sc.item_id=require.beaurau_id and sc.parent_id=require.type',
                                'join_type'=>'left'),
                            );
        if(!empty($attrs['class']['statusSql'])){
            //echo"heell";
            //var_dump($attrs['type']);
            $params['escape_query']=$attrs['class'];
        }

        

        $params['group_by'] = 'require.class_name,name';

        if (isset($attrs['conditions'])) {
            $params['conditions'] = $attrs['conditions'];
        }
        
        $data = $this->getData($params);
        for($i=0;$i<count($data);$i++){
            $data[$i]['final_a_count']=$this->getClassSignUp($attrs,$data[$i]['class_no']); 
        }

            
        
        return $data;        
    }
    public function getClassSignUp($attrs=array(),$class_no)
    {
       

        
        $this->db->select("class_name,(select count(*) from online_app where class_no = a.class_no and year = a.year and term = a.term and yn_sel not in ('6')) as a_count,v.name as name");
        $this->db->from('require as a');
        $this->db->join('view_code_table as vct','vct.type_id=06 and vct.item_id=a.dev_type','left');
        $this->db->join('BS_user as v','v.idno=a.worker','left');
        $this->db->join('second_category as sc','sc.item_id=a.beaurau_id and sc.parent_id=a.type','left');
        $this->db->where('a.year',$attrs['conditions']['require.year']);
        $this->db->where('a.type',$attrs['conditions']['require.type']);
        if(isset($attrs['conditions']['beaurau_id'])){
            $this->db->where('a.beaurau_id',$attrs['conditions']['beaurau_id']);
        }
        $this->db->where('a.class_no',$class_no);
        $this->db->where('a.start_date1>=',$attrs['conditions']['start_date1 >=']);
        $this->db->where('a.start_date1<=',$attrs['conditions']['start_date1 <=']);
        if(isset($attrs['class']['statusSql'])){
            $this->db->where($attrs['class']['statusSql']);
        }
        $query1=$this->db->get_compiled_select();

        $this->db->select('temp.class_name,sum(temp.a_count) as a_count');
        $this->db->from('('.$query1.') as temp');
        
        $this->db->group_by('class_name,name');
        $test=$this->db->get();
        $final=$test->result_array();
        //var_dump($attrs);
        return $final[0]['a_count'];  

    }

    public function getTotalSignUp($attrs=array())
    {
        $this->db->select('(select count(*) from online_app where class_no = a.class_no and year = a.year and term = a.term and yn_sel!="6") as a_count,
                            c.item_id,c.remark,c.name as description
                            ',false);
       // $this->db->join('view_code_table as vct','vct.type_id="06" and vct.item_id=a.dev_type','left');
        //$this->db->join('BS_user as bu','bu.idno=a.worker','left');
        $this->db->join('second_category as c','a.beaurau_id=c.item_id and c.parent_id=a.type','left');
        $this->db->where('a.year',$attrs['require.year']);
        $this->db->where('a.type',$attrs['type']);
        $this->db->where('a.start_date1 >=',$attrs['start_date1']);
        $this->db->where('a.start_date1 <=',$attrs['end_date']);

        if(isset($attrs['beaurau_id'])){
            $this->db->where('beaurau_id',$attrs['beaurau_id']);
            
        }
        if(isset($attrs['statusSql'])){
            $this->db->where($attrs['statusSql']);
        }

        
        $this->db->from('require as a');
        $query=$this->db->get_compiled_select();
        $this->db->select('temp.item_id,temp.remark,temp.description,sum(temp.a_count) as a_count');
        $this->db->group_by('item_id,remark,description,');
        $this->db->order_by('temp.item_id asc');
        $this->db->from('('.$query.') as temp');
        $test=$this->db->get();
        $test=$test->result_array();

        for($i=0;$i<count($test);$i++){
            $test[$i]['expect_total']=$this->getTotalExpect($test[$i]['item_id'],$attrs);
            $test[$i]['term_total']=$this->getTotalTerm($test[$i]['item_id'],$attrs);
            $test[$i]['people']=$this->test($test[$i]['item_id'],$attrs);
        }
        //var_dump($test);
        return $test;
    }

    public function test($item_id,$attrs)
    {
        $this->db->select('(select count(*) from online_app where class_no = a.class_no and year = a.year and term = a.term ) as a_count,
                            c.item_id,c.remark,c.name as description
                            ',false);
        $this->db->join('second_category as c','a.beaurau_id=c.item_id and c.parent_id=a.type','left');
        $this->db->where('a.year',$attrs['require.year']);
        $this->db->where('a.type',$attrs['type']);
        $this->db->where('a.start_date1 >=',$attrs['start_date1']);
        $this->db->where('a.start_date1 <=',$attrs['end_date']);

        if(isset($attrs['beaurau_id'])){
            $this->db->where('beaurau_id',$attrs['beaurau_id']);
        }
        if(isset($attrs['statusSql'])){
            $this->db->where($attrs['statusSql']);
        }
        $this->db->where('a.beaurau_id',$item_id);

        $this->db->from('require as a');
        $query=$this->db->get_compiled_select();
        $this->db->select('temp.item_id,temp.remark,temp.description,sum(temp.a_count) as a_count');

        $this->db->group_by('item_id,remark,description,');
        $this->db->order_by('remark');
        $this->db->from('('.$query.') as temp');
        $test=$this->db->get();
        $test=$test->result_array();
        $total=0;
        for($i=0;$i<count($test);$i++){
            $total+=$test[$i]['a_count'];
        }
        //var_dump($total);
        
        return $total;
    }

    public function getTotalExpect($item_id,$attrs=array())
    {
        $this->db->select('max(no_persons) as max_persons');
        $this->db->where('a.year',$attrs['require.year']);
        $this->db->where('a.type',$attrs['type']);
        $this->db->where('a.start_date1 >=',$attrs['start_date1']);
        $this->db->where('a.start_date1 <=',$attrs['end_date']);

        if(isset($attrs['beaurau_id'])){
            $this->db->where('beaurau_id',$attrs['beaurau_id']);
        }


        $this->db->where('a.beaurau_id',$item_id);
        $this->db->group_by('class_name');
        $temp=$this->db->get('require as a');
        $temp=$temp->result_array();
        $total=0;
        for($i=0;$i<count($temp);$i++){
            $total+=$temp[$i]['max_persons'];
        }
        return $total;        
    }

    public function getTotalTerm($item_id,$attrs=array())
    {
        $this->db->select('count(term) as max_term');
        $this->db->where('a.year',$attrs['require.year']);
        $this->db->where('a.type',$attrs['type']);
        $this->db->where('a.start_date1 >=',$attrs['start_date1']);
        $this->db->where('a.start_date1 <=',$attrs['end_date']);

        if(isset($attrs['beaurau_id'])){
            $this->db->where('beaurau_id',$attrs['beaurau_id']);
        }
        $this->db->where('a.beaurau_id',$item_id);

        $this->db->group_by('class_name');
        $temp=$this->db->get('require as a');
        $temp=$temp->result_array();
        $total=0;
        for($i=0;$i<count($temp);$i++){
            $total+=$temp[$i]['max_term'];
        }
        
        return $total;  
    }

    public function maxSignUp($attrs=array())
    {
        $this->db->select('(select count(*) from online_app where class_no=a.class_no and year=a.year and term=a.term and yn_sel!="6") as total');
        $this->db->join('second_category as c','a.beaurau_id=c.item_id and c.parent_id=a.type','left');
        $this->db->where('a.year',$attrs['require.year']);
        $this->db->where('a.type',$attrs['type']);
        $this->db->where('a.start_date1 >=',$attrs['start_date1']);
        $this->db->where('a.start_date1 <=',$attrs['end_date']);

        if(isset($attrs['beaurau_id'])){
            $this->db->where('beaurau_id',$attrs['beaurau_id']);
        }
        $query=$this->db->get('require as a');
        $query=$query->result_array();

        $total=0;
        for($i=0;$i<count($query);$i++){
            $total+=$query[$i]['total'];
        }
        return $total;
    }

    public function maxTerm($attrs=array())
    {
        $this->db->select('count(term) as mt');
        $this->db->join('view_code_table as c','c.type_id="06" and c.item_id=a.dev_type','left');
        $this->db->join('second_category as sc','sc.item_id=a.beaurau_id and sc.parent_id=a.type','left');
        $this->db->where('a.year',$attrs['require.year']);
        $this->db->where('a.type',$attrs['type']);
        $this->db->where('a.start_date1 >=',$attrs['start_date1']);
        $this->db->where('a.start_date1 <=',$attrs['end_date']);

        if(isset($attrs['beaurau_id'])){
            $this->db->where('beaurau_id',$attrs['beaurau_id']);
        }
        $this->db->group_by('year,class_no');

        $query=$this->db->get('require as a');
        $query=$query->result_array();
        $total=0;
        for($i=0;$i<count($query);$i++){
            $total+=$query[$i]['mt'];
        }
        return $total;
    }
    public function maxPeople($attrs=array())
    {
        $this->db->select('max(no_persons) as mt');
        $this->db->join('view_code_table as c','c.type_id="06" and c.item_id=a.dev_type','left');
        $this->db->join('second_category as sc','sc.item_id=a.beaurau_id and sc.parent_id=a.type','left');
        $this->db->where('a.year',$attrs['require.year']);
        $this->db->where('a.type',$attrs['type']);
        $this->db->where('a.start_date1 >=',$attrs['start_date1']);
        $this->db->where('a.start_date1 <=',$attrs['end_date']);

        if(isset($attrs['beaurau_id'])){
            $this->db->where('beaurau_id',$attrs['beaurau_id']);
        }
        if(isset($attrs['statusSql'])){
            $this->db->where($attrs['statusSql']);
        }
        $this->db->group_by('year,class_no');

        $query=$this->db->get('require as a');
        $query=$query->result_array();
        $total=0;
        for($i=0;$i<count($query);$i++){
            $total+=$query[$i]['mt'];
        }
        return $total;
    }

  

}

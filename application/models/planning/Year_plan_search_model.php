<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Year_plan_search_model extends MY_Model
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
    public function getPreviousYearWokrer($data=array())
    {   
        $this->db->select('BS_user.name');
        $this->db->join('BS_user','BS_user.idno=require.worker','left');
        $this->db->where('year',$data['year']);
        $this->db->where('class_no',$data['class_no']);
        $this->db->group_by('worker');
        $query=$this->db->get('require');
        $result=$query->result_array();
      
        if(empty($result)){
            return null;
        }
        return $result[0]['name'];
            
    }
    

    public function getList($attrs=array())
    {
        
        $params = array(
            'select' => 'require.seq_no, require.year,require.respondant,require.env_class,require.policy_class,require.map1,require.map2,require.map3,require.map4,require.no_persons,require.contactor,count(require.term) as term,
                         require.map5,require.map6,require.map7,require.map8,require.map9,require.map10,require.map11,require.special_status,require.range,require.special_status_other,require.contactor,require.class_name,require.class_status,BS_user.name as BS_name,
                         require.class_no,require.class_name,require.worker,require.start_date1,require.end_date1,require.class_status,require.weights,require.room_code,require.open_retirement,require.range,require.range_real,require.range_internet,bt.base_term as base_term,
                         series_category.name as series_name,sc.name as second_name,bureau.name as bureau_name,bureau.name as dev_type_name,not_hourfee,not_location',
            'order_by' => 'sc.name,require.class_no,require.term',
        );


        $params['join'] = array(array('table' => 'series_category',
                                'condition' => 'series_category.item_id = require.type',
                                'join_type' => 'left'),
                                array('table'=>'second_category as sc',
                                'condition'=>'sc.item_id=require.beaurau_id',
                                'join_type'=>'left'),  
                                array('table' => 'bureau',
                                'condition' => 'bureau.bureau_id = require.dev_type',
                                'join_type' => 'left'),  
                                array('table' => 'BS_user',
                                'condition' => 'BS_user.idno = require.worker',
                                'join_type' => 'left'),
                                array('table'=>'base_term as bt',
                                'condition'=>'bt.year=require.year and bt.class_no=require.class_no',
                                'join_type'=>'left'),                     
                            );
                    
  
            
            if (isset($attrs['query_class_name'])) {
                $params['or_like'] = array(
                    'many' => TRUE,
                    'data' => array(
                        array('field' => 'require.class_name', 'value'=>$attrs['query_class_name'], 'position'=>'both'),
                    ),
                );
            }
        
        $params['group_by'] = 'require.year,require.class_no';

        if (isset($attrs['conditions'])) {
            $params['conditions'] = $attrs['conditions'];
        }

        $data = $this->getData($params);
        //var_dump($data);
        for($i=0;$i<count($data);$i++){
            $data[$i]['pre_worker']=$this->getPreviousYearWokrer($data[$i]);
            $data[$i]['online_course']=$this->getOnlineCourse($data[$i]);
            
                $data[$i]['online_total_hours']=$this->getFirstTermOnlineTotalHours($data[$i]);
            // }else{
            //     $data[$i]['online_total_hours']=$this->getOnlineCourseTotalHours($data[$i]); 
            // }
            
            $data[$i]['each_term_date']=$this->getEachTermDate($data[$i]);
            $data[$i]['cancel_count']=$this->getCancelTotal($data[$i]);
            $data[$i]['5a_num']=$this->getClassData($data[$i]['seq_no'],'cancel_class');
            $data[$i]['5a_num']=count($data[$i]['5a_num']);
            //var_dump($data[$i]['online_course']);
            //die();
        }
        //$data_new = $this->getPreviousYear($data);
        //var_dump($data);
        return $data;
    }

    public function getClassData($id,$mode=null)
    {
        $this->db->select('year,class_no');
        $this->db->where('seq_no',$id);
        //$this->db->where('5a_is_cancel!=','Y');
        $query = $this->db->get('require');
        $result = $query->row_array();

        $this->db->select('seq_no,year,class_no,term,class_name,start_date1,end_date1,contactor');
        $this->db->where('year',$result['year']);
        $this->db->where('class_no',$result['class_no']);
        if($mode=='cancel_class'){
            $where="(5a_is_cancel!='Y' or 5a_is_cancel is null)";
            $this->db->where($where);
        }
        //$this->db->where('5a_is_cancel!=','Y');
        //$where="{$result['5a_is_cancel']}!='Y'";
        //$this->db->where($where);
        $this->db->order_by('term');
        $query = $this->db->get('require');
        $data = $query->result_array();
        
        return $data;
    }

    public function getFirstTermOnlineTotalHours($data){
        $sql = "
                SELECT r.`year`, r.class_no, sum(hours) as total_hours
                FROM `require_online` r 
                JOIN (
                    SELECT `year`, class_no, min(term) term 
                    FROM `require` 
                    GROUP BY `year`, class_no   
                ) min_term ON min_term.`year` = r.`year` AND min_term.class_no = r.class_no AND min_term.term = r.term
                 WHERE r.`year` = ? AND r.class_no = ? AND elearn_id <> -1
                ";
        $query = $this->db->query($sql, [$this->db->escape(addslashes($data['year'])), $this->db->escape(addslashes($data['class_no']))]);
        $result = $query->row();
        if (empty($result)) return 0;
        return $result->total_hours;  
    }

    public function getOnlineCourseTotalHours($data=array())
    {
        $this->db->select('SUM(hours) as total_hours');
        $this->db->where('year',$data['year']);
        $this->db->where('class_no',$data['class_no']);
        $this->db->where('elearn_id!=',-1);
        $this->db->distinct('online_course');
        $query=$this->db->get('require_online');
        $result=$query->result_array();

        if(empty($result)){
            return 0;
        }
       
        return $result[0]['total_hours'];
        
    }

    public function getOnlineCourse($data=array())
    {
        $this->db->select('class_name as online_course');
        $this->db->where('year',$data['year']);
        $this->db->where('class_no',$data['class_no']);
        $this->db->where('elearn_id!=',-1);
        $this->db->order_by('online_course asc');
        $this->db->distinct('online_course');
        $query=$this->db->get('require_online');
        $result=$query->result_array();

        if(empty($result)){
            $result=[];
            return $result;
        }
        //var_dump($result);
        $final=array();
        for($i=0;$i<count($result);$i++){
            $final[$i]=$result[$i]['online_course'];
        }
        //var_dump($final[0]);
        //die();
        return $final;
        
    }
    public function getEachTermDate($data=array())
    {
        $this->db->select('start_date1,end_date1');
        $this->db->where('year',$data['year']);
        $this->db->where('class_no',$data['class_no']);
        $this->db->order_by('term asc');
        $query=$this->db->get('require');
        $query=$query->result_array();
        $time=[];
        for($i=0;$i<count($query);$i++){
            $time[$i]=substr($query[$i]['start_date1'],0,10).'~'.substr($query[$i]['end_date1'],0,10);
        }
        $time=implode(", ",$time);
        
        return $time;
    }
    public function getCancelTotal($data=array())
    {
        $this->db->select('count(1) as cnt');
        $this->db->where('is_cancel','1');
        $this->db->where('year',$data['year']);
        $this->db->where('year',$data['year']);
        $this->db->where('class_no',$data['class_no']);
        $count=$this->db->get('require');
        $count=$count->result_array();
        if(empty($count)){
            return 0;
        }
        //var_dump($count[0]['cnt']);
        //die();
        return $count[0]['cnt'];
    }
 


}

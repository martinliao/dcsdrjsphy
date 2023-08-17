<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Opinion_response_model extends MY_Model
{
    public $table = 'require as r';
    public $pk = 'seq_no';

    public function __construct()
    {
        parent::__construct();

        $this->init($this->table, $this->pk);
    }
    public function getListCount($attrs=array())
    {
        $params = array(
            'conditions' => $attrs,
        );
        


        $data = $this->getList($params);
        //var_dump(count($data));
        //die();
        return count($data);
    }


    public function getList($attrs=array())
    {

        $this->db->select('r.year, r.class_no, r.term, r.class_name,r.worker, os.s1, os.s2, os.s3, os.s4, os.is_annouce,r.seq_no,os.annouce_by,r.end_date1 as end_date,os.annouce_date,v.username, os.A1_BY, os.A2_BY, os.A3_BY, os.A4_BY, os.A5_BY, os.A6_BY, os.A7_BY, os.A8_BY');

        /*if($attrs['conditions']['year']>=105){
            $this->db->join('feedback_course_collocation as qm',' r.year=qm.class_year and r.class_no=qm.class_id and r.term=qm.class_term','inner');
            $this->db->where('qm.isready','1');
        }else{
            $this->db->join('question_management as qm',' r.year=qm.class_year and r.class_no=qm.class_id and r.term=qm.class_term','inner');
            $this->db->where('qm.isready','Y');
        }*/


        $this->db->join('open_suggest as os','r.year=os.year and r.class_no=os.class_no and r.term=os.term','left');
        $this->db->join('BS_user as v','v.username=os.annouce_by','left');
        $this->db->join('courseteacher as ct','ct.year=r.year and ct.class_no=r.class_no and ct.term=r.term','left');
        $this->db->group_by('year,class_no,term');
        $this->db->from('require as r');

        if (isset($attrs['rows']) && isset($attrs['offset'])) {
            $this->db->limit($attrs['rows'], $attrs['offset']);
        }else if (isset($attrs['rows'])) {
            $this->db->limit($attrs['rows']);
        }
        if(isset($attrs['conditions']['class_no'])){
            $this->db->where('r.class_no',$attrs['conditions']['class_no']);
        }

        
        if(isset($attrs['conditions']['class_name'])){
            $this->db->like('r.class_name',$attrs['conditions']['class_name'],'both');
        }

        if(isset($attrs['conditions']['worker'])){
            $this->db->where('r.worker',$attrs['conditions']['worker']);
        }
        $this->db->where('ct.isevaluate','Y');
        $this->db->where('r.start_date1>=',$attrs['conditions']['start_date']);
        $this->db->where('r.start_date1<=',$attrs['conditions']['end_date']);
        $this->db->where_in('r.class_status',$attrs['conditions']['class_status']);
        $this->db->where('r.type',$attrs['conditions']['type']);
        //var_dump($attrs['conditions']);
        
        
        $this->db->where('r.year',$attrs['conditions']['year']);
        $this->db->order_by('end_date,year desc,class_no,term');


        $data=$this->db->get();
        $data=$data->result_array();
        
        for($i=0;$i<count($data);$i++){
            $data[$i]['worker_name']=$this->getWorkerName($data[$i]);
            $data[$i]['suggest_status']=$this->getSuggestStatus($data[$i]);
        }
        //var_dump($data[0]);
        
        return $data;        
    }
    public function getWorkerName($attrs=array())
    {

        $this->db->distinct('name');
        $this->db->select('name');
        $this->db->where('idno',$attrs['worker']);
        $query=$this->db->get('BS_user');
        $query=$query->result_array();
        if(!empty($query)){
           return $query[0]['name']; 
        }
        return null;
    }
    public function getSuggestStatus($attrs=array())
    {
        $this->db->select('status');
        $this->db->where('year',$attrs['year']);
        $this->db->where('class_no',$attrs['class_no']);
        $this->db->where('term',$attrs['term']);
        $query=$this->db->get('course_suggest');
        $query=$query->result_array();
        if(!empty($query)){
            return $query[0]['status'];
        }
        return null;
    }
    public function getDetail($seq_no)
    {
        $this->db->select(' 
                r.year, r.class_no, r.term,r.class_name, os.s1, os.s2, os.s3, os.s4, os.s5, os.s6, os.s7, os.s8,os.a1, os.a2,os.a3, os.a4, os.a5, os.a6, os.a7, os.a8, v1.name as a1_by, v2.name as a2_by, v3.name as a3_by,v4.name as a4_by, v5.name as a5_by, v6.name as a6_by, v7.name as a7_by, v8.name as a8_by,r.seq_no,
                os.is_a1_visible, os.is_a2_visible, os.is_a3_visible, os.is_a4_visible, os.is_a5_visible, os.is_a6_visible,os.is_a7_visible, os.is_a8_visible,os.is_annouce, os.annouce_date , v.name as annouce_by, w.name as worker');
        $this->db->join('open_suggest as os','r.year=os.year and r.term=os.term and r.class_no=os.class_no','left');
        
        $this->db->join('BS_user as v1','v1.username=os.a1_by','left');
        $this->db->join('BS_user as v','v.username=os.annouce_by','left');
        $this->db->join('BS_user as w','w.idno=r.worker','left');
        $this->db->join('BS_user as v2','v2.username=os.a2_by','left');
        $this->db->join('BS_user as v3','v3.username=os.a3_by','left');
        $this->db->join('BS_user as v4','v4.username=os.a4_by','left');
        $this->db->join('BS_user as v5','v5.username=os.a5_by','left');
        $this->db->join('BS_user as v6','v6.username=os.a6_by','left');
        $this->db->join('BS_user as v7','v7.username=os.a7_by','left');
        $this->db->join('BS_user as v8','v8.username=os.a8_by','left');
        $this->db->where('seq_no',$seq_no);
        $query=$this->db->get('require as r');
        $query=$query->result_array();

        return $query;
    }
    public function getDetailItem($seq_no)
    {
        $this->db->select(' 
                r.year, r.class_no, r.term, r.class_name, os.s1, os.s2, os.s3, os.s4, os.s5, os.s6, os.s7, os.s8, 
                os.a1, os.a2, os.a3, os.a4, os.a5, os.a6, os.a7, os.a8,r.seq_no,
                os.is_a1_visible, os.is_a2_visible, os.is_a3_visible, os.is_a4_visible, os.is_a5_visible, os.is_a6_visible,os.is_a7_visible, os.is_a8_visible,
                v.name as annouce_by, v1.name as worker');
        $this->db->join('open_suggest as os','r.year=os.year and r.term=os.term and r.class_no=os.class_no','left');
        $this->db->join('BS_user as v','v.username=os.annouce_by','left');
        $this->db->join('BS_user as v1','v1.idno=r.worker','left');
        $this->db->where('seq_no',$seq_no);
        $query=$this->db->get('require as r');
        $query=$query->result_array();

        return $query;

    }
    public function saveSuggest($attrs=array())
    {
        $username=$this->flags->user['username'];

        $this->db->where('os.year',$attrs['year']);
        $this->db->where('os.term',$attrs['term']);
        $this->db->where('os.class_no',$attrs['class_no']);
        $exist=$this->db->get('open_suggest as os');
        $exist=$exist->result_array();
        

        $this->db->where('year',$attrs['year']);
        $this->db->where('term',$attrs['term']);
        $this->db->where('class_no',$attrs['class_no']);

        $ans = $attrs['A'];
        $sug = $attrs['S'];
        $is_visible = $attrs['is_visible'];
        
        if ($attrs['item']=="s1") {
            $update=['s1'=>$sug,
                    'a1'=>$ans,
                    'is_a1_visible'=>$is_visible,
                    'a1_by'=>$username];
        }   
        if ($attrs['item']=="s2") {
            $update=['s2'=>$sug,
                    'a2'=>$ans,
                    'is_a2_visible'=>$is_visible,
                    'a2_by'=>$username];
        }   
        if ($attrs['item']=="s3") {
            $update=['s3'=>$sug,
                    'a3'=>$ans,
                    'is_a3_visible'=>$is_visible,
                    'a3_by'=>$username];
        }   
        if ($attrs['item']=="s4") {
            $update=['s4'=>$sug,
                    'a4'=>$ans,
                    'is_a4_visible'=>$is_visible,
                    'a4_by'=>$username];
        }   
        if ($attrs['item']=="s5") {
            $update=['s5'=>$sug,
                    'a5'=>$ans,
                    'is_a5_visible'=>$is_visible,
                    'a5_by'=>$username];
        }   
        if ($attrs['item']=="s6") {
            $update=['s6'=>$sug,
                    'a6'=>$ans,
                    'is_a6_visible'=>$is_visible,
                    'a6_by'=>$username];
        }   
        if ($attrs['item']=="s7") {
            $update=['s7'=>$sug,
                    'a7'=>$ans,
                    'is_a7_visible'=>$is_visible,
                    'a7_by'=>$username];
        }   
        if ($attrs['item']=="s8") {
            $update=['s8'=>$sug,
                    'a8'=>$ans,
                    'is_a8_visible'=>$is_visible,
                    'a8_by'=>$username];
        }

        if(!empty($exist)){
            $this->db->update('open_suggest',$update);
            return true;  
        }else{
            $insert=[];
            foreach ($update as $key => $value) {
                $insert[$key]=$value;
            }
            $insert['year']=$attrs['year'];
            $insert['term']=$attrs['term'];
            $insert['class_no']=$attrs['class_no'];
            $insert = array_map('addslashes', $insert);
            $this->db->insert('open_suggest',$insert);
            //var_dump($insert);
            return true;
        }
        
    }
    public function courseSuggest($attrs=array())
    {
        $this->db->select('count(1) as cnt');
        $this->db->where('year',$attrs['year']);
        $this->db->where('class_no',$attrs['class_no']);
        $this->db->where('term',$attrs['term']);
        $query=$this->db->get('course_suggest');
        $query=$query->result_array();

        $this->db->where('year',$attrs['year']);
        $this->db->where('class_no',$attrs['class_no']);
        $this->db->where('term',$attrs['term']);

        if($query[0]['cnt']>0){
            $update=['status'=>$attrs['status'],
                     'upduser'=>$attrs['username']];
            $update = array_map('addslashes', $update);
            $this->db->update('course_suggest',$update);
            return true;
        }else{
            $insert=['year'=>$attrs['year'],
                     'class_no'=>$attrs['class_no'],
                     'term'=>$attrs['term'],
                     'status'=>$attrs['status'],
                     'upduser'=>$attrs['username']];
            $insert = array_map('addslashes', $insert);
            $this->db->insert('course_suggest',$insert);
            return true;
        }
        
        return false;

    }
    public function controlAnnouce($attrs=array())
    {
        $username=$this->flags->user['username'];
        $date=date('Y-m-d H:i:s');
        $this->db->select('count(1) as cnt');
        $this->db->where('year',$attrs['year']);
        $this->db->where('term',$attrs['term']);
        $this->db->where('class_no',$attrs['class_no']);
        $query=$this->db->get('open_suggest');
        $query=$query->result_array();
        if($query[0]['cnt']==0){
            return false;
        }

        $this->db->where('year',$attrs['year']);
        $this->db->where('term',$attrs['term']);
        $this->db->where('class_no',$attrs['class_no']);

      

        if($attrs['mode']=='annouce'){
            $update=['is_annouce'=>'Y',
                     'annouce_by'=>$username,
                     'annouce_date'=>$date];
        }else{
            $update=['is_annouce'=>'N',
                     'annouce_by'=>$username,
                     'annouce_date'=>$date];
        }
        $this->db->update('open_suggest',$update);
        return true;

    }

  

  

}

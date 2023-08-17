<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Print_schedule_model extends MY_Model
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
            'select' => 'require.seq_no, require.year, require.class_no, require.class_name, require.term,require.note,
                         require.worker, require.range,require.range_real,require.range_internet,require.weights, require.room_code, require.room_remark, require.reason ,require.start_date1,',
            'order_by' => 'require.year,require.class_no,require.term',
        );

        $params['join'] = array(array('table' => 'BS_user',
                                    'condition' => "BS_user.idno = require.worker",
                                    'join_type' => 'left'),
                                array('table' => 'view_code_table',
                                    'condition' => "BS_user.idno = require.worker and view_code_table.type_id='26' and BS_user.idno = view_code_table.item_id",
                                    'join_type' => 'left'),
                    );

        if (isset($attrs['query_class_name'])) {
            $params['or_like'] = array(
                'many' => true,
                'data' => array(
                    array('field' => 'require.class_name', 'value'=>$attrs['query_class_name'], 'position'=>'both'),
                ),
            );
        }
        $status=[2,3];
        $params['where_in']=array('field'=>'class_status','value'=>$status);

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
    public function getOnlineCourse($data=array())
    {   
        
        $this->db->select('require_online.class_name,require_online.teacher_name,require_online.place,require_online.start_date,require_online.end_date');
        $this->db->where('year',$data[0]['year']);
        $this->db->where('class_no',$data[0]['class_no']);
        $this->db->where('term',$data[0]['term']);
        $this->db->order_by('id');
        $query=$this->db->get('require_online');
        $result=$query->result_array();
        
        return $result;
    }

    public function getRealCourseTest($data=array())
    {
        $this->db->select('ru.use_date,ru.use_period,ru.teacher_id');
        $this->db->group_by('ru.use_period,ru.teacher_id,ru.use_date');
        $this->db->where('ru.year',$data[0]['year']);
        $this->db->where('ru.class_id',$data[0]['class_no']);
        $this->db->where('ru.term',$data[0]['term']);
        $query=$this->db->get('room_use as ru');
        $result=$query->result_array();
        //var_dump($result);
        //die();
    }

    public function getRealCourse($data=array())
    {
        $this->db->distinct('a.use_period,a.teacher_id,a.use_date');
        $this->db->select('a.use_period,a.teacher_id,a.use_date,a.year,a.class_id,a.term,a.room_id,a.use_id,a.sort');
        $this->db->order_by("a.use_date","a.use_period","a.teacher_id");
        $this->db->where('a.year',$data[0]['year']);
        $this->db->where('a.class_id',$data[0]['class_no']);
        $this->db->where('a.term',$data[0]['term']);
        $this->db->where('a.use_date !=','0000-00-00 00:00:00');
        $this->db->where('a.use_date !=',null);

        //$this->db->group_by(array("temp1.use_period","temp1.teacher_id","temp1.use_date"));
        //$this->db->group_by("room_use.use_period,room_use.teacher_id,room_use.use_date");
        $this->db->from('room_use as a');

        $subquery4 = $this->db->get_compiled_select();
        //$this->db->distinct("ru.use_period,ru.use_date");
        $this->db->select('ru.use_period,ru.use_date,GROUP_CONCAT(teacher_id order by ru.sort asc)as teacher_id,ru.year,ru.class_id,ru.term,ru.room_id,ru.use_id');
        $this->db->group_by(array("ru.use_period","ru.use_date"));
        $this->db->from('('.$subquery4.') as ru');
        $subquery3 = $this->db->get_compiled_select();

        //$this->db->distinct('temp.teacher_id,temp.use_period,temp.use_date,temp.class_id,temp.year,temp.class_id,r.contactor,r.tel,vct.description');
        $this->db->select('temp.use_date,temp.use_period,temp.teacher_id,temp.year,temp.class_id,temp.term,temp.room_id,temp.use_id,r.contactor,r.tel,vct.description as class_name,CONCAT(min(p.from_time), '.', max(p.to_time)) AS ltime,temp.room_id,ifnull(cr.room_sname,cr.room_name) as classroom_name');
        $this->db->order_by("temp.year","temp.class_id","temp.term","temp.use_date","temp.use_period");
        $this->db->group_by(array("temp.teacher_id","temp.use_period","temp.use_date","temp.class_id","temp.year","temp.class_id","r.contactor","r.tel","vct.description"));
        //$this->db->distinct('temp.teacher_id,temp.use_period,temp.use_date,temp.class_id,temp.year,temp.class_id,r.contactor,r.tel,vct.description');
        $this->db->from('('.$subquery3.') as temp');

        $this->db->join('periodtime as p','temp.use_period = p.id and temp.year = p.year and temp.term = p.term and temp.class_id = p.class_no and temp.use_date = p.course_date and p.course_code is not null','left');
        $this->db->join('view_code_table as vct','temp.use_id = vct.item_id and vct.type_id="17" ','left');
        $this->db->join('venue_information as cr','cr.room_id = temp.room_id','left');
        $this->db->join('require as r','r.class_no = temp.class_id and r.term = temp.term and r.year = temp.year','left');
        $this->db->order_by('use_date,ltime,sort asc');
        $this->db->where('temp.year',$data[0]['year']);
        $this->db->where('temp.class_id',$data[0]['class_no']);
        $this->db->where('temp.term',$data[0]['term']);
        $this->db->where('temp.use_date !=','0000-00-00 00:00:00');
        $this->db->where('temp.use_date !=',null);


        $result = $this->db->get()->result_array();

        //var_dump($result);
        //die();

        for($z=0;$z<count($result);$z++){
            $day=$this->getDay($result[$z]['use_date']);
            $result[$z]['cday'] = $day;
            $result[$z]['ltime']=substr($result[$z]["ltime"],0,2).":".substr($result[$z]["ltime"],2,2)."~".substr($result[$z]["ltime"],4,2).":".substr($result[$z]["ltime"],6,2);
        }

        $teacher_id = array();
        for($i=0;$i<count($result);$i++){
            $teacher_id[$i]=explode(',',$result[$i]['teacher_id']);
            $result[$i]['teacher_id_spilt'] = $teacher_id[$i];
        }
        //var_dump($result);
        //die();
 
       /*2019/10/13*/
        for($k=0;$k<count($result);$k++){
            $result[$k]['teacher_info']=$this->getTeacher($teacher_id[$k],$result[$k]['year'],$result[$k]['class_id'],$result[$k]['term'],$result[$k]['use_date'],$result[$k]['use_id'],$result[$k]['use_period']);
            unset($result[$k]['teacher_id_spilt']);
       
            if(empty($result[$k]['teacher_info'])){
                if($result[$k]['use_id']=='O00001'||$result[$k]['use_id']=='O00002'||$result[$k]['use_id']=='O00003'||$result[$k]['use_id']=='O00004'||$result[$k]['use_id']=='O00005'){
                    $result[$k]['teacher_info'][0]['name']='教務組';
                    $result[$k]['teacher_info'][0]['title']="";
                }         
            }
        }

        for($j=count($result)-1;$j>=1;$j--){
            if($result[$j]['use_date']==$result[$j-1]['use_date']&&$result[$j]['teacher_info']==$result[$j-1]['teacher_info']&&$result[$j]['class_name']==$result[$j-1]['class_name']){
                //$result[$j-1]['ltime']=substr($result[$j-1]['ltime'],0,5).substr($result[$j]['ltime'], 5,6); //2021-06-30 取消課程節數合併
            }
        }
       
        $delete_index=[];

        for($t=count($result)-1;$t>=1;$t--){
            if($result[$t]['use_date']==$result[$t-1]['use_date']&&$result[$t]['teacher_info']==$result[$t-1]['teacher_info']&&$result[$t]['class_name']==$result[$t-1]['class_name']){
                //array_push($delete_index,$t); //2021-06-30 取消課程節數合併
            }
        }
  
        for($m=0;$m<count($delete_index);$m++){
            unset($result[$delete_index[$m]]);
        }
        
        $result=array_values($result);

        return $result;
    }
    public function getDay($date)
    {
        $weekday = date('w', strtotime($date));
        return ['日', '一', '二', '三', '四', '五', '六'][$weekday];
    }
    public function getTeacher($teacher_id = array(),$year,$class_id,$term,$use_date,$use_id,$use_period)
    {
        //var_dump($use_id);
        $this->db->select('t.name,t.teacher_type,ru.title,ru.sort,t.idno');
        $this->db->join('teacher as t','t.idno = ru.teacher_id and ru.isteacher=t.teacher','left');
        $this->db->where_in('t.idno',$teacher_id);
        $this->db->where('ru.year',$year);
        $this->db->where('ru.class_id',$class_id);
        $this->db->where('ru.term',$term);
        $this->db->where('ru.use_date',$use_date);
        $this->db->where('ru.use_id',$use_id);
        $this->db->where('ru.use_period',$use_period);
        $this->db->order_by('t.teacher_type asc,ru.sort');
        $this->db->group_by(array("t.name","t.teacher_type"));
        $query=$this->db->get('room_use as ru');
        $result=$query->result_array();
        //var_dump($result);
        return $result;
    }
    public function getResearcher($data=array())
    {
        $this->db->select('require.class_content as class_content2,require.*,bu.name,bu.office_tel,vct.description,vct.add_val1,vct.add_val2');
        $this->db->join('BS_user as bu','bu.idno=require.worker','left');
        $this->db->join('view_code_table as vct','bu.idno=vct.item_id and vct.type_id="26"','left');
        $this->db->where('require.year',$data[0]['year']);
        $this->db->where('require.class_no',$data[0]['class_no']);
        $this->db->where('require.term',$data[0]['term']);

        $query=$this->db->get('require');
        $result=$query->result_array();
        for($i=0;$i<count($result);$i++){
            $result[$i]['sel_number']=$this->getSelNumber($result[$i]);
            $result[$i]['worker_mail']=$this->getWorkerMail($result[$i]);
        }
    
        return $result;
    }
    /*$content有問題*/ 
    public function getMailLog($data=array())
    {
        $this->db->select('1');
        $this->db->where('require.year',$data[0]['year']);
        $this->db->where('require.class_no',$data[0]['class_no']);
        $this->db->where('require.term',$data[0]['term']);
        $this->db->where('require.is_cancel','1');
        $query=$this->db->get('require');
        $cancelCnt=count($query->result_array());
        $content="";

        if($cancelCnt>0){
            $this->db->select('ml.*');
            $this->db->from('mail_log as ml');
            $this->db->where('ml.year',$data[0]['year']);
            $this->db->where('ml.class_no',$data[0]['class_no']);
            $this->db->where('ml.term',$data[0]['term']);
            $this->db->where('ml.mail_type','8');
            $this->db->order_by('ml.seq desc');
            $query1=$this->db->get_compiled_select();

            $this->db->select('temp.body as content');
            $this->db->limit(1);
            $this->db->from('('.$query1.') as temp');
            $this->db->order_by('temp.seq desc');
            $result = $this->db->get()->result_array();
        }else{
            $this->db->select('ml.*');
            $this->db->from('mail_log as ml');
            $this->db->where('ml.year',$data[0]['year']);
            $this->db->where('ml.class_no',$data[0]['class_no']);
            $this->db->where('ml.term',$data[0]['term']);
            $this->db->where('ml.mail_type','1');
            $this->db->order_by('ml.seq desc');
            $query1=$this->db->get_compiled_select();

            $this->db->select('temp.body as content');
            $this->db->limit(1);
            $this->db->from('('.$query1.') as temp');
            $this->db->order_by('temp.seq desc');
            $result = $this->db->get()->result_array();
        }

        return $result;       
    }
    public function getRoomCount($data=array())
    {
        $this->db->distinct();
        $this->db->select('ru.room_id');
        $this->db->from('room_use as ru');
        $this->db->where('ru.year',$data[0]['year']);
        $this->db->where('ru.class_id',$data[0]['class_no']);
        $this->db->where('ru.term',$data[0]['term']);
        $this->db->where('ru.use_date !=',null);
        $query1=$this->db->get_compiled_select();

        $this->db->select('1');
        $this->db->from('('.$query1.') as temp');
        $result=count($this->db->get()->result_array());
        //var_dump($result);
        //die();
        return $result;
    }
    public function getRoomName($data=array())
    {
        $this->db->distinct('ru.room_id,c.room_name');
        $this->db->select('ru.room_id,c.room_name');
        $this->db->join('periodtime','ru.use_period=periodtime.id','left');
        $this->db->join('venue_information as c','ru.room_id=c.room_id','left');
        $this->db->where('ru.year',$data[0]['year']);
        $this->db->where('ru.class_id',$data[0]['class_no']);
        $this->db->where('ru.term',$data[0]['term']);
        //$this->db->where('ru.use_date !=',null);
        $query=$this->db->get('room_use as ru');
        $result=$query->result_array();
        //var_dump($result);
        return $result;

    }
    public function getSelNumber($data=array())
    {
        $this->db->select('*');
        $this->db->where('year',$data['year']);
        $this->db->where('class_no',$data['class_no']);
        $this->db->where('term',$data['term']);
        $ynsel=[1,3,8];
        $this->db->where_in('yn_sel',$ynsel);
        $query=$this->db->get('online_app');
        $result=count($query->result_array());
        return $result;
    }
    public function getWorkerMail($data=array())
    {
        $this->db->select('NVL(BS_user.co_empdb_email,BS_user.email) as mail');
        $this->db->join('BS_user','BS_user.idno=require.worker','inner');
        $this->db->where('require.year',$data['year']);
        $this->db->where('require.class_no',$data['class_no']);
        $this->db->where('require.term',$data['term']);
        $query=$this->db->get('require');
        $result=$query->result_array();
        return $result;
    }
}
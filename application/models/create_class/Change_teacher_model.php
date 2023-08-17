<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Change_teacher_model extends MY_Model
{
    public $table = 'require';
    public $pk = 'seq_no';

    public function __construct()
    {
        parent::__construct();

        $this->init($this->table, $this->pk);

    }

    public function getFormDefault($info=array())
    {
        $data = array_merge(array(
                        'teacher' => ''
                    ),$info);

        return $data;
    }

    public function getVerifyConfig()
    {
        $config = array(
            'teacher' => array(
                'field' => 'teacher',
                'label' => '原來講師',
                'rules' => 'trim|required',
            ),
            'new_teacher_id' => array(
                'field' => 'new_teacher_id',
                'label' => '新講師',
                'rules' => 'trim|required',
            ),
        );

        return $config;
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
            'select' => 'seq_no,year,class_no,term,class_name',
            'order_by' => 'year desc,class_no,term',
        );

        $params['where_special'] = '(year, class_no, term) in (select distinct year, class_id, term from room_use)';

        if (isset($attrs['query_class_name'])) {
            $params['or_like'] = array(
                'many' => TRUE,
                'data' => array(
                    array('field' => 'class_name', 'value'=>$attrs['query_class_name'], 'position'=>'both'),
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
        if (isset($attrs['sort'])) {
            $params['order_by'] = $attrs['sort'];
        }

        $data = $this->getData($params);
        
        return $data;
    }

    public function getTeacher($year,$class_no,$term){
        $today = date('Y-m-d');
        $sql = sprintf("SELECT
                            *
                        FROM
                            (
                                SELECT
                                    A.year,
                                    A.class_id,
                                    A.term,
                                    A.teacher_id,
                                    A.isteacher,
                                    NVL (B1. name, B2. name) AS teacher_name,
                                    C.name as course_name,
                                    A.use_id
                                FROM
                                    room_use A
                                LEFT JOIN teacher B1 ON A.teacher_id = B1.idno
                                AND B1.teacher_type = '1'
                                LEFT JOIN teacher B2 ON A.teacher_id = B2.idno
                                AND B2.teacher_type = '2'
                                LEFT JOIN course_code C ON A.use_id = C.item_id
                                WHERE
                                    A.year = '%s'
                                AND A.class_id = '%s'
                                AND A.term = '%s'
                                AND A.use_date >= '%s'
                                ORDER BY
                                    A.teacher_id
                            ) D
                        WHERE
                            teacher_id IS NOT NULL",$year,$class_no,$term,$today);

        $query = $this->db->query($sql);
        $result = $query->result_array();

        $data = array(''=>'請選擇');
        for($i=0;$i<count($result);$i++){
            $key = $result[$i]['teacher_id'].'::'.$result[$i]['isteacher'].'::'.$result[$i]['use_id'];
            if($result[$i]['isteacher'] == 'Y'){
                $data[$key] = $result[$i]['teacher_name'].'(講師)-'.$result[$i]['course_name'];
            } else {
                $data[$key] = $result[$i]['teacher_name'].'(助教)-'.$result[$i]['course_name'];
            }
        }

        return $data;
    }

    public function updateRooomUse($year,$class_no,$term,$old_teacher_id,$old_isteacher,$old_course_code,$new_teacher_id,$new_teacher_title){
        $this->db->set('teacher_id',$new_teacher_id);
        $this->db->set('title',$new_teacher_title);
        $this->db->where('teacher_id',$old_teacher_id);
        $this->db->where('isteacher',$old_isteacher);
        $this->db->where('use_id',$old_course_code);
        $this->db->where('year',$year);
        $this->db->where('class_id',$class_no);
        $this->db->where('term',$term);

        if($this->db->update('room_use')){
            return true;
        }

        return false;
    }

    public function updateCourseTeacher($year,$class_no,$term,$old_teacher_id,$old_course_code,$new_teacher_id){
        $this->db->set('teacher_id',$new_teacher_id);
        $this->db->where('course_code',$old_course_code);
        $this->db->where('teacher_id',$old_teacher_id);
        $this->db->where('year',$year);
        $this->db->where('class_no',$class_no);
        $this->db->where('term',$term);

        if($this->db->update('courseteacher')){
            return true;
        }

        return false;
    }

    public function chkCanteach($new_teacher_id,$old_course_code,$teacher_type){
        $this->db->select('count(1) cnt');
        $this->db->where('id',$new_teacher_id);
        $this->db->where('course_code',$old_course_code);
        $this->db->where('type',$teacher_type);
        $query = $this->db->get('canteach');
        $result = $query->result_array();

        return $result[0]['cnt'];
    }

    public function insertCanteach($new_teacher_id,$old_course_code,$teacher_type,$cre_user){
        $this->db->set('id',$new_teacher_id);
        $this->db->set('course_code',$old_course_code);
        $this->db->set('type',$teacher_type);
        $this->db->set('cre_user',$cre_user);
        $this->db->set('cre_date',date('Y-m-d H:i:s'));
        $this->db->set('upd_user',$cre_user);
        $this->db->set('upd_date',date('Y-m-d H:i:s'));
        if($this->db->insert('canteach')){
            return true;
        } 

        return false;
    }

    public function getTeacherData($new_teacher_id,$teacher_type){
        $this->db->select('*');
        $this->db->where('idno',$new_teacher_id);
        $this->db->where('teacher_type',$teacher_type);
        $query = $this->db->get('teacher');
        $result = $query->result_array();

        return $result;
    }

    public function getHourTrafficTax($year,$class_no,$term,$new_teacher_id,$old_isteacher){
        $this->db->select('*');
        $this->db->where('year',$year);
        $this->db->where('class_no',$class_no);
        $this->db->where('term',$term);
        $this->db->where('teacher_id',$new_teacher_id);
        $this->db->where('isteacher',$old_isteacher);
        $this->db->where('(status is null or status = "待確認")',NULL,false);
        $query = $this->db->get('hour_traffic_tax');
        $result = $query->result_array();

        return $result;
    }

    public function getBankType($bank_code){
        $this->db->select('remark');
        $this->db->where('item_id',$bank_code);
        $query = $this->db->get('bank_code');
        $result = $query->result_array();

        return $result[0]['remark'];
    }

    public function getAssistantSource($year,$class_no,$term,$use_date){
        $this->db->select('teacher.hire_type');
        $this->db->join('teacher','room_use.teacher_id = teacher.idno and teacher.teacher_type = "1"','left');
        $this->db->where('room_use.year',$year);
        $this->db->where('room_use.class_id',$class_no);
        $this->db->where('room_use.term',$term);
        $this->db->where('room_use.use_date',$use_date);
        $this->db->where('room_use.isteacher','Y');
        $this->db->limit(1);
        $query = $this->db->get('room_use');
        $result = $query->result_array();

        return $result[0]['hire_type'];
    }

    public function updateHourTrafficTax($teacher_id,$teacher_name,$teacher_bank_type,$bank_code,$bank_account,$account_name,$address,$t_source,$hire_type,$old_isteacher,$seq){
        $this->db->set('teacher_id',$teacher_id);
        $this->db->set('teacher_name',$teacher_name);
        $this->db->set('teacher_bank_type',$teacher_bank_type);
        $this->db->set('teacher_bank_id',$bank_code);
        $this->db->set('teacher_account',$bank_account);
        $this->db->set('teacher_acct_name',$account_name);
        $this->db->set('teacher_acct_name',$address);
        $this->db->set('t_source',$t_source);
        $this->db->set('a_source',$hire_type);
        $this->db->set('isteacher',$old_isteacher);
        $this->db->where('seq',$seq);

        if($this->db->update('hour_traffic_tax')){
            return true;
        }

        return false;
    }
}
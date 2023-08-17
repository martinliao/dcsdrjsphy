<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Class_record_model extends MY_Model
{
    public $table = 'BS_user';
    public $pk = 'id';

    public function __construct()
    {
        parent::__construct();

        $this->init($this->table, $this->pk);
    }	

  
    public function getList($attrs=array())
    {
        $params = array(
            'select' =>'ar.role_id',
            'order_by' => 'class_no',
        );
        $params['join'] = array(array('table' => 'account_role as ar',
                                'condition' => 'ar.id = BS_user.username',
                                'join_type' => 'left'),
                                array('table' => 'role_right as rr',
                                'condition' => 'rr.fun_id = "students_transaction_bureaus and rr.role_id=ar_role_id"',
                                'join_type' => 'left')
                        );

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

    public function getStudentCourseInformation_byAns($idno,$student_name,$year,$class_no,$class_name)
    {
        if($idno!=''){
            $this->db->where('online_app.id',$idno);
        }
        if($student_name!=''){
            $this->db->like('bu.name',$student_name,'both');
        }
        if($class_name!=''){
            $this->db->like('r.class_name',$class_name,'both');
        }
        if($class_no!=''){
            $this->db->where('class_id',$class_no);
        }
        if($year!=''){
            $this->db->where('online_app.year',$year);
        }

        $this->db->distinct();
        $this->db->select('online_app.id,online_app.st_no,r.year,r.class_name,r.term,r.start_date1,r.class_no as class_id,r.room_code,ru.room_id,
                           t.description as title,bu.name as pname,t.description as name,
                           CASE WHEN (TRIM(bu.out_gov_name) = \'\' or bu.out_gov_name is null) THEN b1.name ELSE bu.out_gov_name END as bname,
                           b2.name as unit_name,r.is_assess,r.is_mixed,r.seq_no,modify_table_upload.filename,modify_table_upload.path');
        $this->db->join('require as r','online_app.year=r.year and online_app.class_no=r.class_no and online_app.term=r.term','left')
                ->join('BS_user as bu','online_app.id=bu.idno','left')
                ->join('view_code_table as t','t.item_id = bu.job_title and t.type_id="02"','left')
                ->join('bureau as b1','bu.bureau_id=b1.bureau_id','left')
                ->join('bureau as b2','online_app.beaurau_id=b2.bureau_id','left')
                ->join('room_use as ru','ru.year=online_app.year and ru.term=online_app.term and ru.class_id=online_app.class_no and ru.use_period="01"','left')
                ->join('out_gov as og','bu.idno=og.id','left')
                ->join('modify_table_upload','r.seq_no=modify_table_upload.seq and modify_table_upload.uid = bu.id','left');
        $yn_sel=[1,3,8];
        $this->db->where_in('online_app.yn_sel',$yn_sel);
        $this->db->where('r.is_cancel !=',1);
        $this->db->where('r.is_cancel is NOT NULL', NULL, FALSE);
        $this->db->order_by('r.year desc,r.start_date1 desc');
        $query=$this->db->get('online_app');
        $result=$query->result_array();
        
        return $result;
                
    }
    
    public function getStudentCourseInformation($idno,$name)
    {
        if($idno!=''){
            $this->db->where('online_app.id',$idno);
        }
        if($name!=''){
            $this->db->like('bu.name',$name,'both');
        }

        $this->db->distinct();
        $this->db->select('online_app.id,online_app.st_no,r.year,r.class_name,r.term,r.start_date1,r.class_no as class_id,r.room_code,ru.room_id,
                           t.description as title,bu.name as pname,t.description as name,
                           CASE WHEN (TRIM(bu.out_gov_name) = \'\' or bu.out_gov_name is null) THEN b1.name ELSE bu.out_gov_name END as bname,
                           b2.name as unit_name,r.is_assess,r.is_mixed,r.seq_no,,modify_table_upload.filename,modify_table_upload.path');
        $this->db->join('require as r','online_app.year=r.year and online_app.class_no=r.class_no and online_app.term=r.term','left')
                ->join('BS_user as bu','online_app.id=bu.idno','left')
                ->join('view_code_table as t','t.item_id = bu.job_title and t.type_id="02"','left')
                ->join('bureau as b1','bu.bureau_id=b1.bureau_id','left')
                ->join('bureau as b2','online_app.beaurau_id=b2.bureau_id','left')
                ->join('room_use as ru','ru.year=online_app.year and ru.term=online_app.term and ru.class_id=online_app.class_no and ru.use_period="01"','left')
                ->join('out_gov as og','bu.idno=og.id','left')
                ->join('modify_table_upload','r.seq_no=modify_table_upload.seq and modify_table_upload.uid = bu.id','left');
        $yn_sel=[1,3,8];
        $this->db->where_in('online_app.yn_sel',$yn_sel);
        $this->db->where('r.is_cancel !=',1);
        $this->db->where('r.is_cancel is NOT NULL', NULL, FALSE);
        $this->db->group_by('r.year,r.class_no,r.term');

        $this->db->order_by('r.year desc,r.start_date1 desc');
        $query=$this->db->get('online_app');
        $result=$query->result_array();
        return $result;
    }

    public function getStudentCourseInfo1($idno,$name)
    {
        if($idno!=''){
            $this->db->where('online_app.id',$idno);
        }
        if($name!=''){
            $this->db->like('bu.name',$name,'both');
        }
        $this->db->select('online_app.id,online_app.st_no,r.year,r.class_name,r.term,r.start_date1,r.class_no as class_id,r.room_code,t.description as title,
                           t.description as name,bu.name as pname,nvl(og.ou_gov,b1.name) as bname,b2.name as unit_name');
        $this->db->join('require as r','online_app.year=r.year and online_app.class_no=r.class_no and online_app.term=r.term','left')
                           ->join('BS_user as bu','online_app.id=bu.idno','left')
                           ->join('view_code_table as t','t.item_id = bu.job_title and t.type_id="02"','left')
                           ->join('bureau as b1','bu.bureau_id=b1.bureau_id','left')
                           ->join('bureau as b2','online_app.beaurau_id=b2.bureau_id','left')
                           ->join('room_use as ru','ru.year=online_app.year and ru.term=online_app.term and ru.class_id=online_app.class_no and ru.use_period="01"','left')
                           ->join('out_gov as og','bu.idno=og.id','left');
        $yn_sel=[1,3,8];
        $this->db->where_in('online_app.yn_sel',$yn_sel);
        $this->db->where('r.is_cancel !=',1);
        $this->db->where('r.is_cancel is NOT NULL', NULL, FALSE);
        $this->db->group_by('`ru`.`year`,`ru`.`class_id`,`ru`.`term`');
        $this->db->order_by('r.year desc,r.start_date1 desc');
        $query=$this->db->get('online_app');
        $result=$query->result_array();
        
        return $result;

    }
    
    public function getRequireInfo($seq, $uid)
    {
        $this->db->select('require.year, require.class_name, require.term, require.worker, online_app.st_no, BS_user.name');
        $this->db->from('require');
        $this->db->join('online_app','online_app.year=require.year and online_app.class_no=require.class_no and online_app.term=require.term');
        $this->db->join('BS_user','online_app.id = BS_user.idno');
        $this->db->where('require.seq_no', intval($seq));
        $this->db->where('BS_user.id', intval($uid));

        $query=$this->db->get();
        $result=$query->result_array();
        
        return $result;
    }

    public function checkModifyTableUploadExist($seq, $uid)
    {
        $this->db->select('count(1) cnt');
        $this->db->from('modify_table_upload');
        $this->db->where('seq', intval($seq));
        $this->db->where('uid', intval($uid));

        $query=$this->db->get();
        $result=$query->result_array();

        if($result[0]['cnt'] > 0){
            return true;
        }

        return false;
    }

    public function uploadModifyTableUploadExist($seq, $uid, $filename, $file_path)
    {
        $this->db->set('filename', addslashes($filename));
        $this->db->set('path', addslashes($file_path));
        $this->db->set('modify_time', date('Y-m-d H:i:s'));

        $this->db->where('seq', intval($seq));
        $this->db->where('uid', intval($uid));

        if($this->db->update('modify_table_upload')){
            return true;
        }

        return false;
    }

    public function insertModifyTableUploadExist($seq, $uid, $filename, $file_path)
    {
        $this->db->set('seq', intval($seq));
        $this->db->set('uid', intval($uid));
        $this->db->set('filename', addslashes($filename));
        $this->db->set('path', addslashes($file_path));
        $this->db->set('create_time', date('Y-m-d H:i:s'));
        $this->db->set('modify_time', date('Y-m-d H:i:s'));
        
        if($this->db->insert('modify_table_upload')){
            return true;
        }

        return false;
    }

    public function getWorkerEmail($worker)
    {
        $this->db->select('email,email2');
        $this->db->from('BS_user');
        $this->db->where('idno', addslashes($worker));

        $query=$this->db->get();
        $result=$query->result_array();

        if(!empty($result[0]['email'])){
            return $result[0]['email'];
        } else if($result[0]['email2']){
            return $result[0]['email2'];
        }

        return '';
    }

    public function getModifyTableUploadInfo($seq, $uid)
    {
        $this->db->select('*');
        $this->db->from('modify_table_upload');
        $this->db->where('seq', intval($seq));
        $this->db->where('uid', intval($uid));

        $query=$this->db->get();
        $result=$query->result_array();
        
        return $result;
    }
}
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Course_sch_model extends MY_Model
{
    public $table = 'course_sch_app';
    public $pk = 'course_code';

    public function __construct()
    {
        parent::__construct();

        $this->init($this->table, $this->pk);

    }

    public function getsignuser()
    {
        $group_id = array(25, 26);
        $this->db->select('acc.group_id, user.username, user.idno, user.name, user.office_email');
        $this->db->from('account_role acc');
        $this->db->join('BS_user user','user.username=acc.username','inner');
        $this->db->where_in('acc.group_id',$group_id);
                
        $result = $this->db->get()->result_array();
        return $result;
    }

    

    public function insertcoursesch($course_code,$year,$class_no,$term,$pot1,$pot2,$pot3,$fix1,$fix2,$fix3,$bef1,$bef2,$bef3,$aft1,$aft2,$aft3,$rem1,$rem2,$rem3,$opinion,$worker,$boss,$leader,$status,$to_leader,$cre_date,$training_text){
        $this->db->set('course_code',$course_code);
        $this->db->set('year',$year);
        $this->db->set('class_no',$class_no);
        $this->db->set('term',$term);
        $this->db->set('pot1',$pot1);
        $this->db->set('pot2',$pot2);
        $this->db->set('pot3',$pot3);
        $this->db->set('fix1',$fix1);
        $this->db->set('fix2',$fix2);
        $this->db->set('fix3',$fix3);
        $this->db->set('bef1',$bef1);
        $this->db->set('bef2',$bef2);
        $this->db->set('bef3',$bef3);
        $this->db->set('aft1',$aft1);
        $this->db->set('aft2',$aft2);
        $this->db->set('aft3',$aft3);
        $this->db->set('rem1',$rem1);
        $this->db->set('rem2',$rem2);
        $this->db->set('rem3',$rem3);
        $this->db->set('opinion',$opinion);
        $this->db->set('worker',$worker);
        $this->db->set('boss',$boss);
        $this->db->set('leader',$leader);
        $this->db->set('status',$status);
        $this->db->set('to_leader',$to_leader);
        $this->db->set('training_text',$training_text);
        $this->db->set('cre_date',date('Y-m-d H:i:s'));

        if($this->db->insert('course_sch_app')){
            return true;
        } else {
            return false;
        }
    }

    public function get_course_sch_Count($seq_no){
        $this->db->select('*');
        $this->db->from('course_sch_app');
        $this->db->where('course_code',$seq_no);
        
        //$result = $this->db->count_all_results();
        $result = $this->db->get()->result_array();
        return $result;
    }

    public function user_idno($username){
        $this->db->select('idno');
        $this->db->from('BS_user');
        $this->db->where('username',$username);
                
        $result = $this->db->get()->result_array();
        return $result;
    }

    public function get_boss_Count($boss_no){
        $this->db->select('*');
        $this->db->from('course_sch_app');
        $this->db->where('boss',$boss_no);
        $this->db->where('status',2);
        $result = $this->db->count_all_results();
        return $result;
    }

    public function get_leader_Count($leader_no){
        
        $this->db->select('*');
        $this->db->from('course_sch_app');
        $this->db->where('leader',$leader_no);
        $this->db->where('status',3);
        $result = $this->db->count_all_results();
        return $result;
    }

    public function get_mail_template($id){
        
        $this->db->select('*');
        $this->db->from('template');
        $this->db->where('id',$id);
        $result = $this->db->get()->result_array();
        return $result;
    }

    public function get_usermail($id){
        
        $this->db->select('office_email,name');
        $this->db->from('BS_user');
        $this->db->where('idno',$id);
        $result = $this->db->get()->result_array();
        return $result;
    }



    public function _update($pk, $fields=array()){
        return parent::update($pk, $fields);
    }

    public function get_class_name($seq_no){
        
        $this->db->select('*');
        $this->db->from('require');
        $this->db->where('seq_no',$seq_no);
        
        $result = $this->db->get()->row();
        return $result;
    }

}


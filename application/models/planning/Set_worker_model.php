<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Set_worker_model extends MY_Model
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

        // $this->db->cache_on();
        $params = array(
            'select' => 'require.seq_no, require.year, require.class_no, require.class_name, require.term, require.worker, require.range, require.weights, require.room_code, require.room_remark, require.reason ,require.start_date1, require.end_date1, require.apply_s_date, require.apply_e_date, require.apply_s_date2, require.apply_e_date2 ,series_category.name as series_name, second_category.name as second_name, venue_information.room_name, BS_user.name as worker_name',
            'order_by' => 'require.class_no,require.term',
        );

        $params['join'] = array(array('table' => 'series_category',
                            'condition' => 'series_category.item_id = require.type',
                            'join_type' => 'left'),
                            array('table' => 'second_category',
                            'condition' => 'second_category.item_id = require.beaurau_id',
                            'join_type' => 'left'),
                            array('table' => 'venue_information',
                                    'condition' => 'venue_information.room_id = require.room_code',
                                    'join_type' => 'left'),
                            array('table' => 'BS_user',
                                    'condition' => "BS_user.idno = require.worker",
                                    'join_type' => 'left'),
                    );

        if (isset($attrs['query_class_name'])) {
            $params['or_like'] = array(
                'many' => TRUE,
                'data' => array(
                    array('field' => 'require.class_name', 'value'=>$attrs['query_class_name'], 'position'=>'both'),
                ),
            );
        }
        if(isset($attrs['class_status'])){
           $params['where_in']=array('field'=>'class_status','value'=>$attrs['class_status']); 
        }
        

        if (isset($attrs['query_min_term'])) {

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

    public function getSecondCategory($type){
        $this->db->select('item_id,name');
        $this->db->where('parent_id',$type);
        $query = $this->db->get('second_category');
        $result = $query->result_array();

        return $result;
    }

    public function updateApplyStartDate($seq_no,$apply_s_date){
    	$this->db->set('apply_s_date',$apply_s_date);
    	$this->db->where('seq_no',$seq_no);

    	if($this->db->update('require')){
    		return true;
    	}

    	return false;
    }

    public function updateApplyEndDate($seq_no,$apply_e_date){
        $this->db->set('apply_e_date',$apply_e_date);
        $this->db->where('seq_no',$seq_no);

        if($this->db->update('require')){
            return true;
        }

        return false;
    }

    public function updateApplyStartDate2($seq_no,$apply_s_date2){
        $this->db->set('apply_s_date2',$apply_s_date2);
        $this->db->where('seq_no',$seq_no);

        if($this->db->update('require')){
            return true;
        }

        return false;
    }

    public function updateApplyEndDate2($seq_no,$apply_e_date2){
        $this->db->set('apply_e_date2',$apply_e_date2);
        $this->db->where('seq_no',$seq_no);

        if($this->db->update('require')){
            return true;
        }

        return false;
    }

    public function updateWorker($seq_no,$worker){
        $this->db->set('worker',$worker);
        $this->db->where('seq_no',$seq_no);

        if($this->db->update('require')){
            return true;
        }

        return false;
    }

    public function updateWorkerByImport($data=array()){
        $this->db->set('worker',$data['worker']);
        if(isset($data['weights'])){
            $this->db->set('weights',$data['weights']);
        }
        if(isset($data['apply_s_date'])){
            $this->db->set('apply_s_date',$data['apply_s_date']);
        }
        if(isset($data['apply_e_date'])){
            $this->db->set('apply_e_date',$data['apply_e_date']);
        }
        if(isset($data['apply_s_date2'])){
            $this->db->set('apply_s_date2',$data['apply_s_date2']);
        }
        if(isset($data['apply_e_date2'])){
            $this->db->set('apply_e_date2',$data['apply_e_date2']);
        }

        $this->db->where('year',$data['year']);
        $this->db->where('class_no',$data['class_no']);
        $this->db->where('term',$data['term']);


        if($this->db->update('require')){
            return true;
        }

        return false;
    }

    public function updateRoom($seq_no,$room){
        $this->db->set('room_code',$room);
        $this->db->where('seq_no',$seq_no);

        if($this->db->update('require')){
            return true;
        }

        return false;
    }

    public function checkApplyDate($seq_no){
        $this->db->select('apply_s_date2,apply_e_date2,year,class_name,term');
        $this->db->where('seq_no',$seq_no);
        $query = $this->db->get('require');

        $result = $query->row_array();
        
        //die();
        //die(); &&substr($result['apply_s_date2'],0,10)!=$test1&&substr($result['apply_e_date2'],0,10)!=$test2)
        if(!empty($result['apply_s_date2']) || !empty($result['apply_e_date2'])){
            
            $message = $result['year'].'年'.$result['class_name'].'第'.$result['term'].'期已有二次報名時段，不能修改<br>';
            return $message;
        } else {
            return '';
        }
    }

    public function checkApplyStartDateIsChange($seq_no,$apply_s_date){
        $this->db->select('count(1) cnt');
        $this->db->where('seq_no',$seq_no);
        $this->db->where('apply_s_date',$apply_s_date);
        $query = $this->db->get('require');
        $result = $query->row_array();

        if($result['cnt'] == '0'){
            return true;
        } else {
            return false;
        }
    }

    public function checkApplyEndDateIsChange($seq_no,$apply_e_date){
        $this->db->select('count(1) cnt');
        $this->db->where('seq_no',$seq_no);
        $this->db->where('apply_e_date',$apply_e_date);
        $query = $this->db->get('require');
        $result = $query->row_array();

        if($result['cnt'] == '0'){
            return true;
        } else {
            return false;
        }
    }

    public function get_worker_idno($worker_name){
        $this->db->select('idno');
        $this->db->where('name',$worker_name);
        $this->db->where("username in (select username from account_role where group_id = '8')");
        $this->db->order_by('id','asc');
        $query = $this->db->get('BS_user');
        $result = $query->row_array();

        if(!empty($result)){
            return $result['idno'];
        } else {
            return '';
        }
    }

    public function getClassInfo($seq_no){
        $this->db->select('year,class_no,term');
        $this->db->where('seq_no',$seq_no);
        $query = $this->db->get('require');
        $result = $query->result_array();

        return $result;
    }

    public function updateWorkerForHourTrafficTax($class_info,$worker){
        $this->db->set('worker_id',$worker);
        $this->db->where('year',$class_info[0]['year']);
        $this->db->where('class_no',$class_info[0]['class_no']);
        $this->db->where('term',$class_info[0]['term']);

        if($this->db->update('hour_traffic_tax')){
            return true;
        }

        return false;
    }

    public function updateWorkerForDiningStudent($class_info,$worker){
        $this->db->set('worker',$worker);
        $this->db->where('year',$class_info[0]['year']);
        $this->db->where('class_no',$class_info[0]['class_no']);
        $this->db->where('term',$class_info[0]['term']);

        if($this->db->update('dining_student')){
            return true;
        }

        return false;
    }
}
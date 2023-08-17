<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Modify_terms_model extends MY_Model
{
    public $table = 'require';
    public $pk = 'seq_no';

    public function __construct()
    {
        parent::__construct();

        $this->init($this->table, $this->pk);

    }

    public function getListCount($attrs=array(),$bureau_id)
    {
        $params = array(
            'conditions' => $attrs['conditions'],
        );

        if (isset($attrs['query_class_name'])) {
            $params['query_class_name'] = $attrs['query_class_name'];
        }
        $data = $this->getList($params,$bureau_id);
        return count($data);
    }

    public function getList($attrs=array(),$bureau_id)
    {
        if($bureau_id == '379680000A'){
            $params = array(
                'select' => 'require.seq_no, require.year, require.class_no,require.class_name,require.start_date1,require.class_status, series_category.name as series_name, second_category.name as second_name, count(1) as total_terms',
                'order_by' => 'require.year,require.class_no',
            );

            $params['join'] = array(array('table' => 'series_category',
                                'condition' => 'series_category.item_id = require.type',
                                'join_type' => 'left'),
                                array('table' => 'second_category',
                                'condition' => 'second_category.item_id = require.beaurau_id',
                                'join_type' => 'left')
                        );

            if (isset($attrs['query_class_name'])) {
                $params['or_like'] = array(
                    'many' => TRUE,
                    'data' => array(
                        array('field' => 'require.class_name', 'value'=>$attrs['query_class_name'], 'position'=>'both'),
                    ),
                );
            }
        } else {
            $params = array(
                'select' => 'require.seq_no, require.year, require.class_no, require.class_name, require.class_status, count(1) as total_terms',
                'order_by' => 'require.year,require.class_no',
            );

            $level = $this->getBureauLevel($bureau_id);
            $under_bureau_id = $this->getEachUnderBureauId($bureau_id,abs($level['bureau_level']-5));
            $params['where_in'] = array('field' => 'require.dev_type', 'value' => $under_bureau_id);

            if (isset($attrs['query_class_name'])) {
                $params['or_like'] = array(
                    'many' => TRUE,
                    'data' => array(
                        array('field' => 'require.class_name', 'value'=>$attrs['query_class_name'], 'position'=>'both'),
                    ),
                );
            }
        }

        $params['group_by'] = 'require.year,require.class_no';

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
        
        for($i=0;$i<count($data);$i++){
            $data[$i]['5a_num']=$this->getClassData($data[$i]['seq_no'],'cancel_class');
            $data[$i]['5a_num']=count($data[$i]['5a_num']);
        }
        

        return $data;
    }

    public function _insert($fields=array())
    {
        return $this->insert($fields);
    }

    public function getBaseTerm($year,$class_no){
        $this->db->select('base_term');
        $this->db->where('year',$year);
        $this->db->where('class_no',$class_no);
        $query = $this->db->get('base_term');
        $result = $query->result_array();

        if(isset($result[0]['base_term']) && !empty($result[0]['base_term'])){
            return $result[0]['base_term'];
        }

        return '';
    }

    public function getClassData($id,$mode=null){
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

    public function getAllClassData($year,$class_no,$term){
        $this->db->select('*');
        $this->db->where('year',$year);
        $this->db->where('class_no',$class_no);
        $this->db->where('term',$term);
        $query = $this->db->get('require');
        $result = $query->result_array();

        return array_pop($result);
    }

    public function getRequireContent($year,$class_no,$term){
        $this->db->select('year,class_no,term,course_name,material');
        $this->db->where('year',$year);
        $this->db->where('class_no',$class_no);
        $this->db->where('term',$term);
        $query = $this->db->get('require_content');
        $result = $query->result_array();

        return $result;
    }

    public function addClassTerms($data = array()){
        $this->db->select('max(term) as max_term');
        $this->db->where('year',$data['year']);
        $this->db->where('class_no',$data['class_no']);
        $query = $this->db->get('require');
        $result = $query->row_array();

        $new_term = $result['max_term']+1;
        $class_info = $this->getAllClassData($data['year'],$data['class_no'],$result['max_term']);
        $require_content = $this->getRequireContent($data['year'],$data['class_no'],$result['max_term']);
        $require_online = $this->getRequireOnline($data['year'],$data['class_no'],'1');

        unset($class_info['seq_no']);
        // unset($class_info['start_date1']);
        // unset($class_info['end_date1']);
        unset($class_info['start_date2']);
        unset($class_info['end_date2']);
        unset($class_info['start_date3']);
        unset($class_info['end_date3']);
        unset($class_info['note']);
        unset($class_info['room_code']);
        unset($class_info['isevaluate']);
        unset($class_info['apply_s_date']);
        unset($class_info['apply_e_date']);
        unset($class_info['apply_s_date2']);
        unset($class_info['apply_e_date2']);
        unset($class_info['isend']);
        unset($class_info['is_volunteer']);
        unset($class_info['learn_send']);

        $this->db->trans_start();
        for ($i=0;$i<$data['add'];$i++) {
            $class_info['class_status'] = $data['class_status'];
            $class_info['term'] = $new_term;
            if($this->_insert($class_info)){
                for($j=0;$j<count($require_content);$j++){
                    $require_content[$j]['term'] = $new_term;
                    $this->db->insert('require_content',$require_content[$j]);
                }

                for($k=0;$k<count($require_online);$k++){
                    unset($require_online[$k]['id']);
                    $require_online[$k]['term'] = $new_term;
                    $this->db->insert('require_online',$require_online[$k]);
                }
            }
            $new_term++;
        }
        $this->db->trans_complete();

        if($this->db->trans_status() === TRUE){
            return true;
        }

        return false;
    }

    public function getRequireOnline($year,$class_no,$term){
        $this->db->select('*');
        $this->db->where('year',$year);
        $this->db->where('class_no',$class_no);
        $this->db->where('term','1');
        $query = $this->db->get('require_online');
        $result = $query->result_array();

        return $result;
    }

    public function insertClassTerms($data = array()){
        $this->db->select('count(1) cnt');
        $this->db->where('year',$data['year']);
        $this->db->where('class_no',$data['class_no']);
        $this->db->where('term >=',$data['insert']);
        $this->db->where_in('class_status',array(2,3));
        $query = $this->db->get('require');
        $check = $query->row_array();

        if($check['cnt'] == '0'){
            $class_info = $this->getAllClassData($data['year'],$data['class_no'],$data['insert']);
            $class_info['class_status'] = $data['class_status'];
            $class_info['term'] = $data['insert'];
            $require_content = $this->getRequireContent($data['year'],$data['class_no'],$data['insert']);

            unset($class_info['seq_no']);
            unset($class_info['start_date1']);
            unset($class_info['end_date1']);
            unset($class_info['start_date2']);
            unset($class_info['end_date2']);
            unset($class_info['start_date3']);
            unset($class_info['end_date3']);
            unset($class_info['room_code']);
            unset($class_info['note']);
            unset($class_info['isevaluate']);
            unset($class_info['learn_send']);

            $this->db->trans_start();
            $this->db->set('term','term+1',false);
            $this->db->where('term >=',$data['insert']);
            $this->db->where('year',$data['year']);
            $this->db->where('class_no',$data['class_no']);
            $this->db->order_by('term','desc');
            $this->db->update('require');

            $this->db->set('term','term+1',false);
            $this->db->where('term >=',$data['insert']);
            $this->db->where('year',$data['year']);
            $this->db->where('class_no',$data['class_no']);
            $this->db->order_by('term','desc');
            $this->db->update('require_content');
            $class_info = array_map('addslashes', $class_info);
            $this->db->insert('require',$class_info);

            for($j=0;$j<count($require_content);$j++){
                $require_content[$j]['term'] = $data['insert'];
                $this->db->insert('require_content',$require_content[$j]);
            }

            $this->db->trans_complete();

            if($this->db->trans_status() === TRUE){
                return true;
            }
        }

        return false;
    }

    public function deleteClassTerms($data = array())
    {
        $message = '';
        $this->db->trans_start();
        for($i=count($data['term'])-1;$i>=0;$i--){
            // $this->db->select('count(1) cnt');
            // $this->db->where('year',$data['year']);
            // $this->db->where('class_no',$data['class_no']);
            // $this->db->where('term >=',$data['term'][$i]);
            // $this->db->where_in('class_status',array(2,3));
            // $query = $this->db->get('require');
            // $check = $query->row_array();

            // if($check['cnt'] == '0'){
                $this->db->where('year',$data['year']);
                $this->db->where('class_no',$data['class_no']);
                $this->db->where('term',$data['term'][$i]);
                $this->db->delete('require');

                $this->db->where('year',$data['year']);
                $this->db->where('class_no',$data['class_no']);
                $this->db->where('term',$data['term'][$i]);
                $this->db->delete('stud_modify');

                $this->db->where('year',$data['year']);
                $this->db->where('class_no',$data['class_no']);
                $this->db->where('term',$data['term'][$i]);
                $this->db->delete('online_app');

                $this->db->where('year',$data['year']);
                $this->db->where('class_no',$data['class_no']);
                $this->db->where('term',$data['term'][$i]);
                $this->db->delete('course');

                $this->db->where('year',$data['year']);
                $this->db->where('class_no',$data['class_no']);
                $this->db->where('term',$data['term'][$i]);
                $this->db->delete('courseteacher');

                $this->db->where('year',$data['year']);
                $this->db->where('class_id',$data['class_no']);
                $this->db->where('term',$data['term'][$i]);
                $this->db->delete('room_use');

                $this->db->where('year',$data['year']);
                $this->db->where('class_no',$data['class_no']);
                $this->db->where('term',$data['term'][$i]);
                $this->db->delete('booking_place');

                $this->db->where('year',$data['year']);
                $this->db->where('class_no',$data['class_no']);
                $this->db->where('term',$data['term'][$i]);
                $this->db->delete('upload_file');

                $this->db->where('year',$data['year']);
                $this->db->where('class_no',$data['class_no']);
                $this->db->where('term',$data['term'][$i]);
                $this->db->delete('s_vacation');

                $this->db->where('year',$data['year']);
                $this->db->where('class_no',$data['class_no']);
                $this->db->where('term',$data['term'][$i]);
                $this->db->delete('require_grade');

                $this->db->where('year',$data['year']);
                $this->db->where('class_no',$data['class_no']);
                $this->db->where('term',$data['term'][$i]);
                $this->db->delete('hour_traffic_tax');

                $this->db->where('year',$data['year']);
                $this->db->where('class_no',$data['class_no']);
                $this->db->where('term',$data['term'][$i]);
                $this->db->delete('require_content');

                $this->db->where('year',$data['year']);
                $this->db->where('class',$data['class_no']);
                $this->db->where('ladder',$data['term'][$i]);
                $this->db->delete('SV_ClassManagement');

                $this->db->where('year',$data['year']);
                $this->db->where('class_no',$data['class_no']);
                $this->db->where('term',$data['term'][$i]);
                $this->db->delete('periodtime');

                $this->db->where('year',$data['year']);
                $this->db->where('class_no',$data['class_no']);
                $this->db->where('term',$data['term'][$i]);
                $this->db->delete('dining_teacher');

                $this->db->where('year',$data['year']);
                $this->db->where('class_no',$data['class_no']);
                $this->db->where('term',$data['term'][$i]);
                $this->db->delete('dining_student');

                $this->db->where('year',$data['year']);
                $this->db->where('class_no',$data['class_no']);
                $this->db->where('term',$data['term'][$i]);
                $this->db->delete('dining');

                $this->db->where('year',$data['year']);
                $this->db->where('class_no',$data['class_no']);
                $this->db->where('term',$data['term'][$i]);
                $this->db->delete('beaurau_persons');
                
                $this->db->set('term','term-1',false);
                $this->db->where('year',$data['year']);
                $this->db->where('class_no',$data['class_no']);
                $this->db->where('term >',$data['term'][$i]);
                $this->db->order_by('term','asc');
                $this->db->update('require');

                $this->db->set('term','term-1',false);
                $this->db->where('year',$data['year']);
                $this->db->where('class_no',$data['class_no']);
                $this->db->where('term >',$data['term'][$i]);
                $this->db->order_by('term','asc');
                $this->db->update('stud_modifylog');

                $this->db->set('term','term-1',false);
                $this->db->where('year',$data['year']);
                $this->db->where('class_no',$data['class_no']);
                $this->db->where('term >',$data['term'][$i]);
                $this->db->order_by('term','asc');
                $this->db->update('periodtime');

                $this->db->set('term','term-1',false);
                $this->db->where('year',$data['year']);
                $this->db->where('class_no',$data['class_no']);
                $this->db->where('term >',$data['term'][$i]);
                $this->db->order_by('term','asc');
                $this->db->update('online_app');

                $this->db->set('term','term-1',false);
                $this->db->where('year',$data['year']);
                $this->db->where('class_no',$data['class_no']);
                $this->db->where('term >',$data['term'][$i]);
                $this->db->order_by('term','asc');
                $this->db->update('course');

                $this->db->set('term','term-1',false);
                $this->db->where('year',$data['year']);
                $this->db->where('class_no',$data['class_no']);
                $this->db->where('term >',$data['term'][$i]);
                $this->db->order_by('term','asc');
                $this->db->update('courseteacher');

                $this->db->set('term','term-1',false);
                $this->db->where('year',$data['year']);
                $this->db->where('class_id',$data['class_no']);
                $this->db->where('term >',$data['term'][$i]);
                $this->db->order_by('term','asc');
                $this->db->update('room_use');

                $this->db->set('term','term-1',false);
                $this->db->where('year',$data['year']);
                $this->db->where('class_no',$data['class_no']);
                $this->db->where('term >',$data['term'][$i]);
                $this->db->order_by('term','asc');
                $this->db->update('booking_place');

                $this->db->set('term','term-1',false);
                $this->db->where('year',$data['year']);
                $this->db->where('class_no',$data['class_no']);
                $this->db->where('term >',$data['term'][$i]);
                $this->db->order_by('term','asc');
                $this->db->update('upload_file');

                $this->db->set('term','term-1',false);
                $this->db->where('year',$data['year']);
                $this->db->where('class_no',$data['class_no']);
                $this->db->where('term >',$data['term'][$i]);
                $this->db->order_by('term','asc');
                $this->db->update('s_vacation');

                $this->db->set('term','term-1',false);
                $this->db->where('year',$data['year']);
                $this->db->where('class_no',$data['class_no']);
                $this->db->where('term >',$data['term'][$i]);
                $this->db->order_by('term','asc');
                $this->db->update('require_grade');

                $this->db->set('term','term-1',false);
                $this->db->where('year',$data['year']);
                $this->db->where('class_no',$data['class_no']);
                $this->db->where('term >',$data['term'][$i]);
                $this->db->order_by('term','asc');
                $this->db->update('mail_log');

                $this->db->set('term','term-1',false);
                $this->db->where('year',$data['year']);
                $this->db->where('class_no',$data['class_no']);
                $this->db->where('term >',$data['term'][$i]);
                $this->db->order_by('term','asc');
                $this->db->update('hour_traffic_tax');

                $this->db->set('term','term-1',false);
                $this->db->where('year',$data['year']);
                $this->db->where('class_no',$data['class_no']);
                $this->db->where('term >',$data['term'][$i]);
                $this->db->order_by('term','asc');
                $this->db->update('require_content');

                $this->db->set('ladder','ladder-1',false);
                $this->db->where('year',$data['year']);
                $this->db->where('class',$data['class_no']);
                $this->db->where('ladder >',$data['term'][$i]);
                $this->db->order_by('ladder','asc');
                $this->db->update('SV_ClassManagement');

                $this->db->set('term','term-1',false);
                $this->db->where('year',$data['year']);
                $this->db->where('class_no',$data['class_no']);
                $this->db->where('term >',$data['term'][$i]);
                $this->db->order_by('term','asc');
                $this->db->update('dining_teacher');

                $this->db->set('term','term-1',false);
                $this->db->where('year',$data['year']);
                $this->db->where('class_no',$data['class_no']);
                $this->db->where('term >',$data['term'][$i]);
                $this->db->order_by('term','asc');
                $this->db->update('dining_student');

                $this->db->set('term','term-1',false);
                $this->db->where('year',$data['year']);
                $this->db->where('class_no',$data['class_no']);
                $this->db->where('term >',$data['term'][$i]);
                $this->db->order_by('term','asc');
                $this->db->update('dining');

                $this->db->set('term','term-1',false);
                $this->db->where('year',$data['year']);
                $this->db->where('class_no',$data['class_no']);
                $this->db->where('term >',$data['term'][$i]);
                $this->db->order_by('term','asc');
                $this->db->update('beaurau_persons');

                $message .= "第".$data['term'][$i]."期，刪除成功<br>";
            // } else {
            //     $message .= '因第'.$data['term'][$i].'期或此期之後有非草案的期別存在，故您所勾選在本期(含本期)之前的期別，刪除失敗';

            //     return $message;
            // }
        }
        $this->db->trans_complete();
        if($this->db->trans_status() === TRUE){
            return $message;
        } else {
            return '刪除過程發生錯誤';
        }

    }

    public function cancel_class($data = array())
    {
        $message = '';
        $this->db->trans_start();
        for($i=count($data['term'])-1;$i>=0;$i--){

                $update=['5a_is_cancel'=>'Y','class_status'=>'1'];
                $this->db->where('year',$data['year']);
                $this->db->where('class_no',$data['class_no']);
                $this->db->where('term',$data['term'][$i]);
                $this->db->update('require',$update);

                /*$this->db->where('year',$data['year']);
                $this->db->where('class_no',$data['class_no']);
                $this->db->where('term',$data['term'][$i]);
                $this->db->delete('stud_modify');

                $this->db->where('year',$data['year']);
                $this->db->where('class_no',$data['class_no']);
                $this->db->where('term',$data['term'][$i]);
                $this->db->delete('online_app');

                $this->db->where('year',$data['year']);
                $this->db->where('class_no',$data['class_no']);
                $this->db->where('term',$data['term'][$i]);
                $this->db->delete('course');

                $this->db->where('year',$data['year']);
                $this->db->where('class_no',$data['class_no']);
                $this->db->where('term',$data['term'][$i]);
                $this->db->delete('courseteacher');

                $this->db->where('year',$data['year']);
                $this->db->where('class_id',$data['class_no']);
                $this->db->where('term',$data['term'][$i]);
                $this->db->delete('room_use');*/


                $this->db->where('year',$data['year']);
                $this->db->where('class_no',$data['class_no']);
                $this->db->where('term',$data['term'][$i]);
                $this->db->delete('booking_place');
                /*
                $this->db->where('year',$data['year']);
                $this->db->where('class_no',$data['class_no']);
                $this->db->where('term',$data['term'][$i]);
                $this->db->delete('upload_file');

                $this->db->where('year',$data['year']);
                $this->db->where('class_no',$data['class_no']);
                $this->db->where('term',$data['term'][$i]);
                $this->db->delete('s_vacation');

                $this->db->where('year',$data['year']);
                $this->db->where('class_no',$data['class_no']);
                $this->db->where('term',$data['term'][$i]);
                $this->db->delete('require_grade');

                $this->db->where('year',$data['year']);
                $this->db->where('class_no',$data['class_no']);
                $this->db->where('term',$data['term'][$i]);
                $this->db->delete('hour_traffic_tax');

                $this->db->where('year',$data['year']);
                $this->db->where('class_no',$data['class_no']);
                $this->db->where('term',$data['term'][$i]);
                $this->db->delete('require_content');

                $this->db->where('year',$data['year']);
                $this->db->where('class',$data['class_no']);
                $this->db->where('ladder',$data['term'][$i]);
                $this->db->delete('SV_ClassManagement');

                $this->db->where('year',$data['year']);
                $this->db->where('class_no',$data['class_no']);
                $this->db->where('term',$data['term'][$i]);
                $this->db->delete('periodtime');

                $this->db->where('year',$data['year']);
                $this->db->where('class_no',$data['class_no']);
                $this->db->where('term',$data['term'][$i]);
                $this->db->delete('dining_teacher');

                $this->db->where('year',$data['year']);
                $this->db->where('class_no',$data['class_no']);
                $this->db->where('term',$data['term'][$i]);
                $this->db->delete('dining_student');

                $this->db->where('year',$data['year']);
                $this->db->where('class_no',$data['class_no']);
                $this->db->where('term',$data['term'][$i]);
                $this->db->delete('dining');

                $this->db->where('year',$data['year']);
                $this->db->where('class_no',$data['class_no']);
                $this->db->where('term',$data['term'][$i]);
                $this->db->delete('beaurau_persons');
                /*
                $this->db->set('term','term-1',false);
                $this->db->where('year',$data['year']);
                $this->db->where('class_no',$data['class_no']);
                $this->db->where('term >',$data['term'][$i]);
                $this->db->order_by('term','asc');
                $this->db->update('require');

                $this->db->set('term','term-1',false);
                $this->db->where('year',$data['year']);
                $this->db->where('class_no',$data['class_no']);
                $this->db->where('term >',$data['term'][$i]);
                $this->db->order_by('term','asc');
                $this->db->update('stud_modifylog');

                $this->db->set('term','term-1',false);
                $this->db->where('year',$data['year']);
                $this->db->where('class_no',$data['class_no']);
                $this->db->where('term >',$data['term'][$i]);
                $this->db->order_by('term','asc');
                $this->db->update('periodtime');

                $this->db->set('term','term-1',false);
                $this->db->where('year',$data['year']);
                $this->db->where('class_no',$data['class_no']);
                $this->db->where('term >',$data['term'][$i]);
                $this->db->order_by('term','asc');
                $this->db->update('online_app');

                $this->db->set('term','term-1',false);
                $this->db->where('year',$data['year']);
                $this->db->where('class_no',$data['class_no']);
                $this->db->where('term >',$data['term'][$i]);
                $this->db->order_by('term','asc');
                $this->db->update('course');

                $this->db->set('term','term-1',false);
                $this->db->where('year',$data['year']);
                $this->db->where('class_no',$data['class_no']);
                $this->db->where('term >',$data['term'][$i]);
                $this->db->order_by('term','asc');
                $this->db->update('courseteacher');

                $this->db->set('term','term-1',false);
                $this->db->where('year',$data['year']);
                $this->db->where('class_id',$data['class_no']);
                $this->db->where('term >',$data['term'][$i]);
                $this->db->order_by('term','asc');
                $this->db->update('room_use');

                $this->db->set('term','term-1',false);
                $this->db->where('year',$data['year']);
                $this->db->where('class_no',$data['class_no']);
                $this->db->where('term >',$data['term'][$i]);
                $this->db->order_by('term','asc');
                $this->db->update('booking_place');

                $this->db->set('term','term-1',false);
                $this->db->where('year',$data['year']);
                $this->db->where('class_no',$data['class_no']);
                $this->db->where('term >',$data['term'][$i]);
                $this->db->order_by('term','asc');
                $this->db->update('upload_file');

                $this->db->set('term','term-1',false);
                $this->db->where('year',$data['year']);
                $this->db->where('class_no',$data['class_no']);
                $this->db->where('term >',$data['term'][$i]);
                $this->db->order_by('term','asc');
                $this->db->update('s_vacation');

                $this->db->set('term','term-1',false);
                $this->db->where('year',$data['year']);
                $this->db->where('class_no',$data['class_no']);
                $this->db->where('term >',$data['term'][$i]);
                $this->db->order_by('term','asc');
                $this->db->update('require_grade');

                $this->db->set('term','term-1',false);
                $this->db->where('year',$data['year']);
                $this->db->where('class_no',$data['class_no']);
                $this->db->where('term >',$data['term'][$i]);
                $this->db->order_by('term','asc');
                $this->db->update('mail_log');

                $this->db->set('term','term-1',false);
                $this->db->where('year',$data['year']);
                $this->db->where('class_no',$data['class_no']);
                $this->db->where('term >',$data['term'][$i]);
                $this->db->order_by('term','asc');
                $this->db->update('hour_traffic_tax');

                $this->db->set('term','term-1',false);
                $this->db->where('year',$data['year']);
                $this->db->where('class_no',$data['class_no']);
                $this->db->where('term >',$data['term'][$i]);
                $this->db->order_by('term','asc');
                $this->db->update('require_content');

                $this->db->set('ladder','ladder-1',false);
                $this->db->where('year',$data['year']);
                $this->db->where('class',$data['class_no']);
                $this->db->where('ladder >',$data['term'][$i]);
                $this->db->order_by('ladder','asc');
                $this->db->update('SV_ClassManagement');

                $this->db->set('term','term-1',false);
                $this->db->where('year',$data['year']);
                $this->db->where('class_no',$data['class_no']);
                $this->db->where('term >',$data['term'][$i]);
                $this->db->order_by('term','asc');
                $this->db->update('dining_teacher');

                $this->db->set('term','term-1',false);
                $this->db->where('year',$data['year']);
                $this->db->where('class_no',$data['class_no']);
                $this->db->where('term >',$data['term'][$i]);
                $this->db->order_by('term','asc');
                $this->db->update('dining_student');

                $this->db->set('term','term-1',false);
                $this->db->where('year',$data['year']);
                $this->db->where('class_no',$data['class_no']);
                $this->db->where('term >',$data['term'][$i]);
                $this->db->order_by('term','asc');
                $this->db->update('dining');

                $this->db->set('term','term-1',false);
                $this->db->where('year',$data['year']);
                $this->db->where('class_no',$data['class_no']);
                $this->db->where('term >',$data['term'][$i]);
                $this->db->order_by('term','asc');
                $this->db->update('beaurau_persons');*/

                $message .= "第".$data['term'][$i]."期，取消成功<br>";
            // } else {
            //     $message .= '因第'.$data['term'][$i].'期或此期之後有非草案的期別存在，故您所勾選在本期(含本期)之前的期別，刪除失敗';

            //     return $message;
            // }
        }
        $this->db->trans_complete();
        if($this->db->trans_status() === TRUE){
            return $message;
        } else {
            return '取消過程發生錯誤';
        }
    }

    public function getEachUnderBureauId($bureau_id,$times){
        $bureau_id_list = array($bureau_id);
        $total_bureau_id = array($bureau_id);
      
        for($i=0;$i<$times;$i++){
            if(isset($bureau_id_list) && !empty($bureau_id_list)){
                $this->db->select('bureau_id');
                $this->db->where_in('parent_id',$bureau_id_list);
                $this->db->where('del_flag','N');
                $query = $this->db->get('bureau');
                $bureau_id_list = array();
                $result = $query->result_array();
               
                foreach ($result as $row){
                    array_push($bureau_id_list, $row['bureau_id']);
                    array_push($total_bureau_id, $row['bureau_id']);
                } 
            } 
        }

        return  $total_bureau_id;
    }


    public function getBureauLevel($bureau_id){
        $this->db->select('bureau_level');
        $this->db->where('bureau_id',$bureau_id);
        $query = $this->db->get('bureau');
        $result = $query->row_array();

        return $result;
    }
}
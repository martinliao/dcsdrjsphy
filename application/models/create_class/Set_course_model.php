<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Set_course_model extends MY_Model
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
                    'room_name' => '',
                    'worker_name' => '',
                    'f1' => '',
                    'f2' => '',
                    'f3' => '',
                    'f4' => '',
                    'f5' => '',
                ),$info);

        return $data;
    }

    public function getVerifyConfig($class_attribute)
    {
        $config = array(
            'env_r1' => array(
                'field' => 'env_r1',
                'label' => '全教課程類別代碼',
                'rules' => 'required|max_length[3]',
            ),
            'env_r2' => array(
                'field' => 'env_r2',
                'label' => '全教課程類別細項代碼',
                'rules' => 'required|max_length[3]',
            ),
            'env_r3' => array(
                'field' => 'env_r3',
                'label' => '全教課程類別科目代碼',
                'rules' => 'required|max_length[3]',
            ),
            'env_r4' => array(
                'field' => 'env_r4',
                'label' => '全教研習進修範疇細項',
                'rules' => 'required|exact_length[13]',
            ),
            'ecpa_class_id' => array(
                'field' => 'ecpa_class_id',
                'label' => '終身學習類別代碼',
                'rules' => 'required',
            ),
            'class_attribute' => array(
                'field' => 'class_attribute',
                'label' => '班期性質',
                'rules' => 'required|valid_gather_zero',
            ),
        );

        if($class_attribute == '2'){
            $config['range_internet'] = array('field'=>'range_internet','label'=>'線上時數','rules'=>'required');
        }

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
            'select' => 'seq_no, year, class_no, class_name, term',
            'order_by' => 'year desc,class_no,term',
        );

        $params['where_in']['field'] = 'class_status';
        $params['where_in']['value'] = array(2,3);

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



    public function getListCount2($attrs=array())
    {
        $params = array(
            'conditions' => $attrs['conditions'],
        );

        if (isset($attrs['query_class_name'])) {
            $params['query_class_name'] = $attrs['query_class_name'];
        }
        
        $data = $this->getsignList($params);
        return count($data);
    }

    public function getsignList($attrs=array())
    {
        
        $params = array(
            'select' => 'require.seq_no, require.year, require.class_no, require.class_name, require.term, course_sch_app.status, course_sch_app.boss, course_sch_app.leader',
            'order_by' => 'require.year desc, require.class_no, require.term',
        );
        $params['join'] = array(array('table' => 'course_sch_app',
        'condition' => "course_sch_app.course_code = require.seq_no",
        'join_type' => 'left'),
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

    public function _update($pk, $fields=array()){
        return parent::update($pk, $fields);
    }

    public function chechAdmin($username){
        $this->db->select('count(1) cnt');
        $this->db->where('username',$username);
        $this->db->where_in('group_id',array('1','9'));
        $query = $this->db->get('account_role');
        $result = $query->result_array();

        if($result[0]['cnt'] > 0){
            return true;
        }

        return false;
    }

    public function getMixPublishInfo($year,$class_no,$term){
        $this->db->select('notice_elearn,notice_start,notice_end');
        $this->db->where('year',$year);
        $this->db->where('class_no',$class_no);
        $this->db->where('term',$term);
        $query = $this->db->get('lux_announcement_logs');
        $result = $query->result_array();

        return $result;
    }

    public function getWorkerName($worker_idno){
        $this->db->select('name');
        // $this->db->where('user_group_id',8);
        $this->db->where('idno',$worker_idno);
        $query = $this->db->get('BS_user');
        $result = $query->row_array();

        if(!empty($result)){
            return $result['name'];
        } else {
            return '';
        }
    }

    public function getSectionTime(){
        $this->db->select('remark');
        $this->db->where('enable',1);
        $this->db->order_by('id');
        $query = $this->db->get('section_time');
        $result = $query->result_array();

        return $result;
    }

    public function getEduCode($year,$class_no,$term){
        $this->db->select('env_r1,env_r2,env_r3,env_r4');
        $this->db->where('year',$year);
        $this->db->where('class_no',$class_no);
        $this->db->where('term',$term);
        $query = $this->db->get('require');
        $result = $query->result_array();

        return $result;
    }

    public function insertRequireFile($year,$class_no,$term,$path,$create_user){
        $this->db->set('year',$year);
        $this->db->set('class_no',$class_no);
        $this->db->set('term',$term);
        $this->db->set('file_path',$path);
        $this->db->set('cre_user',$create_user);
        $this->db->set('cre_date',date('Y-m-d H:i:s'));

        if($this->db->insert('require_file')){
            return true;
        } else {
            return false;
        }
    }

    public function checkAnnouncementLog($year,$class_no,$term){
        $this->db->select('count(1) cnt');
        $this->db->where('year',$year);
        $this->db->where('class_no',$class_no);
        $this->db->where('term',$term);
        $query = $this->db->get('lux_announcement_logs');
        $result = $query->row_array();

        if($result['cnt'] > 0){
            return true;
        } else {
            return false;
        }
    }

    public function updateAnnouncementLog($year,$class_no,$term,$notice_elearn,$notice_start,$notice_end){
        $this->db->set('notice_elearn',$notice_elearn);
        $this->db->set('notice_start',$notice_start);
        $this->db->set('notice_end',$notice_end);
        $this->db->where('year',$year);
        $this->db->where('class_no',$class_no);
        $this->db->where('term',$term);
        
        if($this->db->update('lux_announcement_logs')){
            return true;
        } else {
            return false;
        }
    }

    public function insertAnnouncementLog($year,$class_no,$term,$notice_elearn,$notice_start,$notice_end){
        $this->db->set('notice_elearn',$notice_elearn);
        $this->db->set('notice_start',$notice_start);
        $this->db->set('notice_end',$notice_end);
        $this->db->set('year',$year);
        $this->db->set('class_no',$class_no);
        $this->db->set('term',$term);
        
        if($this->db->insert('lux_announcement_logs')){
            return true;
        } else {
            return false;
        }
    }

    public function insertCourse($year,$class_no,$term,$course_list,$create_user){
        $this->db->trans_start();
        $this->deleteCourse($year,$class_no,$term);

        for($i=0;$i<count($course_list);$i++){
            $this->db->set('year',$year);
            $this->db->set('class_no',$class_no);
            $this->db->set('term',$term);
            $this->db->set('course_code',$course_list[$i]);
            $this->db->set('cre_user',$create_user);
            $this->db->set('cre_date',date('Y-m-d H:i:s'));
            $this->db->set('upd_user',$create_user);
            $this->db->set('upd_date',date('Y-m-d H:i:s'));
            $this->db->insert('course');
        }

        $this->db->trans_complete();

        if($this->db->trans_status() === TRUE){
            return true;
        } 

        return false;
    }

    public function deleteCourse($year,$class_no,$term){
        $this->db->where('year',$year);
        $this->db->where('class_no',$class_no);
        $this->db->where('term',$term);
        
        if($this->db->delete('course')){
            return true;
        } 

        return false;
    }

    public function getCourse($year,$class_no,$term){
        $this->db->select('course.course_code,course_code.name,room_use.use_id');
        $this->db->join('course_code','course_code.item_id = course.course_code');
        $this->db->join('room_use','room_use.year = course.year and room_use.class_id = course.class_no and room_use.term = course.term and room_use.use_id = course.course_code','left');
        $this->db->where('course.year',$year);
        $this->db->where('course.class_no',$class_no);
        $this->db->where('course.term',$term);
        $this->db->group_by('course.course_code');
        $query = $this->db->get('course');
        $result = $query->result_array();

        return $result;
    }

    public function copyCourse($year,$class_no,$term){
        $this->db->select('course_code');
        $this->db->where('course.year',$year);
        $this->db->where('course.class_no',$class_no);
        $this->db->where('course.term',$term);
        $query = $this->db->get('course');
        $result = $query->result_array();

        return $result;
    }

    public function getUploadFile($year,$class_no,$term){
        $this->db->select('file_path');
        $this->db->where('year',$year);
        $this->db->where('class_no',$class_no);
        $this->db->where('term',$term);
        $query = $this->db->get('require_file');
        $result = $query->result_array();

        return $result;
    }

    public function deleteRequireFile($year,$class_no,$term,$file_name){
        $this->db->where('year',$year);
        $this->db->where('class_no',$class_no);
        $this->db->where('term',$term);
        $this->db->where('file_path',$file_name);
        
        if($this->db->delete('require_file')){
            return true;
        }

        return false;
    }

    public function getBookingDate($year,$class_no,$term){
        $this->db->select('booking_date');
        $this->db->where('year',$year);
        $this->db->where('class_no',$class_no);
        $this->db->where('term',$term);
        $this->db->group_by('booking_date');
        $this->db->order_by('booking_date');
        $query = $this->db->get('booking_place');
        $result = $query->result_array();

        $data = array();
        for($i=0;$i<count($result);$i++){
            $result[$i]['booking_date'] = date('Y-m-d',strtotime($result[$i]['booking_date']));
            $data[$result[$i]['booking_date']] = $result[$i]['booking_date'];
        }
        return $data;
    }

    public function getBookingRoom($year,$class_no,$term,$booking_date){
        $this->db->select('booking_place.booking_date,booking_place.room_id,venue_information.room_name');
        $this->db->join('venue_information','venue_information.room_id = booking_place.room_id');
        $this->db->where('booking_place.year',$year);
        $this->db->where('booking_place.class_no',$class_no);
        $this->db->where('booking_place.term',$term);
        $this->db->where('booking_place.booking_date',$booking_date);
        $this->db->order_by('booking_place.booking_date, booking_place.room_id');
        $query = $this->db->get('booking_place');
        $result = $query->result_array();

        return $result;
    }

    public function delBookingData($year,$class_no,$term){
        $this->db->where('year',$year);
        $this->db->where('class_no',$class_no);
        $this->db->where('term',$term);
        
        if($this->db->delete('booking_place')){
            return true;
        }

        return false;
    }

    public function getRoomUseDate($year,$class_no,$term){
        $this->db->select('use_date');
        $this->db->where('year',$year);
        $this->db->where('class_id',$class_no);
        $this->db->where('term',$term);
        $this->db->group_by('use_date');
        $this->db->order_by('use_date');
        $query = $this->db->get('room_use');
        $result = $query->result_array();

        $data = array();
        for($i=0;$i<count($result);$i++){
            $result[$i]['use_date'] = date('Y-m-d',strtotime($result[$i]['use_date']));
            $data[$result[$i]['use_date']] = $result[$i]['use_date'];
        }
        return $data;
    }

    public function getOldBookingRoom($year,$class_no,$term,$use_date){
        $this->db->select('room_use.use_date,room_use.room_id,venue_information.room_name');
        $this->db->join('venue_information','venue_information.room_id = room_use.room_id');
        $this->db->where('room_use.year',$year);
        $this->db->where('room_use.class_id',$class_no);
        $this->db->where('room_use.term',$term);
        $this->db->where('room_use.use_date',$use_date);
        $this->db->group_by('room_use.room_id');
        $this->db->order_by('room_use.use_date, room_use.room_id');
        $query = $this->db->get('room_use');
        $result = $query->result_array();

        return $result;
    }

    public function getRoomName($room_id){
        $this->db->select('room_name');
        $this->db->where('room_id',$room_id);
        $query = $this->db->get('venue_information');
        $result = $query->result_array();

        if(isset($result[0]['room_name']) && !empty($result[0]['room_name'])){
            return $result[0]['room_name'];
        } 

        return '';
    }

    public function getRoomInfo($room_id){
        $this->db->select('room_name,room_sname,room_bel');
        $this->db->where('room_id',$room_id);
        $query = $this->db->get('venue_information');
        $result = $query->result_array();

        return $result;
    }

    public function getCourseTime($year,$class_no,$term,$course_date,$class_description){
        $this->db->select('concat(substring(periodtime.from_time, 1, 2),":",substring(periodtime.from_time, 3, 2)) as from_time,concat(substring(periodtime.to_time, 1, 2),":",substring(periodtime.to_time, 3, 2)) as to_time,periodtime.id');
        $this->db->join('periodtime','room_use.year = periodtime.year and room_use.class_id = periodtime.class_no and room_use.term = periodtime.term and room_use.use_date = periodtime.course_date and room_use.use_id = periodtime.course_code and room_use.room_id = periodtime.room_id and room_use.use_period = periodtime.id');
        $this->db->where('room_use.year',$year);
        $this->db->where('room_use.class_id',$class_no);
        $this->db->where('room_use.term',$term);
        $this->db->where('room_use.use_date',$course_date);
        $this->db->where_not_in('room_use.use_period',array('00','AA01'));
        $this->db->where('periodtime.to_time is not null',NULL, FALSE);
        $this->db->where('LENGTH(periodtime.to_time)',4);
        $this->db->order_by('room_use.use_date,room_use.use_period desc');
        $this->db->limit(1);
        $query = $this->db->get('room_use');
        $result = $query->result_array();

        return $result;
    }

    public function checkCanteachExist($teacher_idno,$course_code,$type){
        $this->db->select('count(1) as cnt');
        $this->db->where('id',$teacher_idno);
        $this->db->where('course_code',$course_code);
        $this->db->where('type',$type);
        $query = $this->db->get('canteach');
        $result = $query->result_array();

        if($result[0]['cnt'] > 0){
            return true;
        } 

        return false;
    }

    public function insertCanteach($teacher_idno,$course_code,$type,$cre_user){
        $this->db->set('id',$teacher_idno);
        $this->db->set('course_code',$course_code);
        $this->db->set('type',$type);
        $this->db->set('cre_user',$cre_user);
        $this->db->set('cre_date',date('Y-m-d H:i:s'));
        $this->db->set('upd_user',$cre_user);
        $this->db->set('upd_date',date('Y-m-d H:i:s'));
        if($this->db->insert('canteach')){
            return true;
        } 

        return false;
    }

    public function checkClassFirstExist($year,$class_no,$term,$course_code){
        $this->db->select('count(1) cnt');
        $this->db->where('year',$year);
        $this->db->where('class_no',$class_no);
        $this->db->where('term',$term);
        $this->db->where('course_code',$course_code);
        $query = $this->db->get('course');
        $result = $query->result_array();

        return $result[0]['cnt'];
    }

    public function insertSingleCourse($year,$class_no,$term,$course_code,$cre_user){
        $this->db->set('year',$year);
        $this->db->set('class_no',$class_no);
        $this->db->set('term',$term);
        $this->db->set('course_code',$course_code);
        $this->db->set('cre_user',$cre_user);
        $this->db->set('cre_date',date('Y-m-d H:i:s'));
        $this->db->set('upd_user',$cre_user);
        $this->db->set('upd_date',date('Y-m-d H:i:s'));

        if($this->db->insert('course')){
            return true;
        }

        return false;
    }

    public function checkRoomUse($booking_date,$room_id,$from_time,$to_time,$year=null,$class_no=null,$term=null){
        $this->db->select('count(1) cnt');
        $this->db->join('periodtime','room_use.use_period = periodtime.id and room_use.year = periodtime.year and room_use.class_id = periodtime.class_no and room_use.term = periodtime.term and room_use.room_id = periodtime.room_id and room_use.use_id = periodtime.course_code and room_use.use_date = periodtime.course_date');
        $this->db->join('require','require.year = room_use.year and require.class_no = room_use.class_id and require.term = room_use.term');
        $this->db->where('room_use.room_id',$room_id);
        $this->db->where('room_use.use_date',$booking_date);
        $this->db->where('require.is_cancel','0');
        $this->db->where('concat(substring(periodtime.to_time, 1, 2),":",substring(periodtime.to_time, 3, 2)) > ',$from_time);
        $this->db->where('concat(substring(periodtime.from_time, 1, 2),":",substring(periodtime.from_time, 3, 2)) < ',$to_time);

        if(!empty($year) && !empty($class_no) && !empty($term)){
            $this->db->where('room_use.year !=',$year);
            $this->db->where('room_use.class_id !=',$class_no);
            $this->db->where('room_use.term !=',$term);
        }

        $query = $this->db->get('room_use');
        $result = $query->result_array();

        if($result[0]['cnt'] > 0){
            return true;
        }

        return false;
    }

    //新增課表用來檢查教室的預約是否跟此課程相同
    public function checkRoom_use($booking_date,$room_id,$from_time,$to_time,$year=null,$class_no=null,$term=null)
    {
        $this->db->select('count(1) cnt');
        $this->db->join('periodtime','room_use.use_period = periodtime.id and room_use.year = periodtime.year and room_use.class_id = periodtime.class_no and room_use.term = periodtime.term and room_use.room_id = periodtime.room_id and room_use.use_id = periodtime.course_code and room_use.use_date = periodtime.course_date');
        $this->db->join('require','require.year = room_use.year and require.class_no = room_use.class_id and require.term = room_use.term');
        $this->db->where('room_use.room_id',$room_id);
        $this->db->where('room_use.use_date',$booking_date);
        $this->db->where('require.is_cancel','0');
        $this->db->where('concat(substring(periodtime.to_time, 1, 2),":",substring(periodtime.to_time, 3, 2)) > ',$from_time);
        $this->db->where('concat(substring(periodtime.from_time, 1, 2),":",substring(periodtime.from_time, 3, 2)) < ',$to_time);

        if(!empty($year) && !empty($class_no) && !empty($term)){
            $this->db->where('room_use.year !=',$year);
            $this->db->where('room_use.class_id !=',$class_no);
            $this->db->where('room_use.term !=',$term);
        }

        $query = $this->db->get('room_use');
        $result = $query->result_array();

        if($result[0]['cnt'] > 0 &&!empty($year) && !empty($class_no) && !empty($term)){
            return FALSE;
        }

        if($result[0]['cnt'] > 0){
            return true;
        }

        return false;
    }

    public function checkRoomUseForAppinfo($booking_date,$room_id,$from_time,$to_time){
        $this->db->select('count(1) cnt');
        $this->db->join('reservation_time','room_use.use_period = reservation_time.item_id');
        $this->db->where('room_use.room_id',$room_id);
        $this->db->where('room_use.use_date',$booking_date);
        $this->db->where('year is null', NULL, FALSE);
        $this->db->where('class_id is null', NULL, FALSE);
        $this->db->where('term is null', NULL, FALSE);
        $this->db->where('appi_id is not null', NULL, FALSE);
        $this->db->where('concat(substring(reservation_time.end_time, 1, 2),":",substring(reservation_time.end_time, 3, 2)) > ',$from_time);
        $this->db->where('concat(substring(reservation_time.start_time, 1, 2),":",substring(reservation_time.start_time, 3, 2)) < ',$to_time);
        $query = $this->db->get('room_use');
        $result = $query->result_array();

        if($result[0]['cnt'] > 0){
            return true;
        }

        return false;
    }

    public function checkBookingExist($booking_date,$room_id,$from_time,$to_time,$year=null,$class_no=null,$term=null){
        $this->db->select('count(1) cnt');
        $this->db->join('reservation_time','booking_place.booking_period = reservation_time.item_id');
        $this->db->join('require','require.year = booking_place.year and require.class_no = booking_place.class_no and require.term = booking_place.term');
        $this->db->where('booking_place.booking_date',$booking_date);
        $this->db->where('booking_place.room_id',$room_id);
        $this->db->group_start();
        $this->db->group_start();
        $this->db->where('reservation_time.start_time <=',$from_time);
        $this->db->where('reservation_time.end_time >=',$from_time);
        $this->db->group_end();
        $this->db->or_group_start();
        $this->db->where('reservation_time.start_time <=',$to_time);
        $this->db->where('reservation_time.end_time >=',$to_time);
        $this->db->group_end();
        $this->db->group_end();
        $this->db->where('require.is_cancel','0');

        if(!empty($year) && !empty($class_no) && !empty($term)){
            $this->db->where('booking_place.year !=',$year);
            $this->db->where('booking_place.class_no !=',$class_no);
            $this->db->where('booking_place.term !=',$term);
        }

        $query = $this->db->get('booking_place');
        $result = $query->result_array();

        if($result[0]['cnt'] > 0){
            return true;
        }

        return false;
    }

    public function checkClassDescription($year,$class_no,$term,$booking_date){
        $this->db->select('count(1) cnt');
        $this->db->where('year',$year);
        $this->db->where('class_id',$class_no);
        $this->db->where('term',$term);
        $this->db->where('use_date',$booking_date);
        $this->db->where_in('use_period',array('00','AA01'));
        $query = $this->db->get('room_use');
        $result = $query->result_array();

        return $result[0]['cnt'];
    }

    public function checkPeriodtime($year,$class_no,$term,$booking_date,$room_id,$course_code,$from_time,$to_time,$pno,$pid){
        $this->db->select('count(1) cnt');
        $this->db->where('id',$pno);
        $this->db->where('name',$pid);
        $this->db->where('from_time',preg_replace('/:/','',$from_time));
        $this->db->where('to_time',preg_replace('/:/','',$to_time));
        $this->db->where('course_code',$course_code);
        $this->db->where('room_id',$room_id);
        $this->db->where('year',$year);
        $this->db->where('class_no',$class_no);
        $this->db->where('term',$term);
        $this->db->where('course_date',$booking_date);
        
        $query = $this->db->get('periodtime');
        $result = $query->result_array();

        if($result[0]['cnt'] > 0 ){
            return true;
        }

        return false;
    }

    public function checkPeriodtimeNew($year,$class_no,$term,$booking_date,$room_id,$from_time,$to_time){
        $this->db->select('count(1) cnt');
        $this->db->where('from_time',preg_replace('/:/','',$from_time));
        $this->db->where('to_time',preg_replace('/:/','',$to_time));
        $this->db->where('room_id',$room_id);
        $this->db->where('year',$year);
        $this->db->where('class_no',$class_no);
        $this->db->where('term',$term);
        $this->db->where('course_date',$booking_date);
        
        $query = $this->db->get('periodtime');
        $result = $query->result_array();

        if($result[0]['cnt'] > 0 ){
            return true;
        }

        return false;
    }

    public function insertPeriodtime($year,$class_no,$term,$booking_date,$room_id,$course_code,$from_time,$to_time,$pno,$pid){
        $this->db->set('id',$pno);
        $this->db->set('name',$pid);
        $this->db->set('from_time',preg_replace('/:/','',$from_time));
        $this->db->set('to_time',preg_replace('/:/','',$to_time));
        $this->db->set('course_code',$course_code);
        $this->db->set('room_id',$room_id);
        $this->db->set('year',$year);
        $this->db->set('class_no',$class_no);
        $this->db->set('term',$term);
        $this->db->set('course_date',$booking_date);
        
        if($this->db->insert('periodtime')){
            return true;
        }

        return false;
    }

    public function updatePeriodtime($year,$class_no,$term,$upd_course_code,$upd_room_id,$upd_from_time,$upd_to_time,$old_room_id,$old_course_code,$old_period,$old_course_date) {
        $this->db->set('course_code',$upd_course_code);
        $this->db->set('room_id',$upd_room_id);
        $this->db->set('from_time',preg_replace('/:/','',$upd_from_time));
        $this->db->set('to_time',preg_replace('/:/','',$upd_to_time));
        $this->db->where('year',$year);
        $this->db->where('class_no',$class_no);
        $this->db->where('term',$term);
        $this->db->where('room_id',$old_room_id);
        $this->db->where('course_code',$old_course_code);
        $this->db->where('id',$old_period);
        $this->db->where('course_date',$old_course_date);

        if($this->db->update('periodtime')){
            return true;
        }

        return false;
    }

    public function deleteRoomUse($year,$class_no,$term,$old_course_date,$old_period,$old_room_id,$old_course_code=NULL){
        $this->db->where('year',$year);
        $this->db->where('class_id',$class_no);
        $this->db->where('term',$term);
        $this->db->where('use_date',$old_course_date);
        $this->db->where('use_period',$old_period);
        $this->db->where('room_id',$old_room_id);
        if(!empty($old_course_code)){
            $this->db->where('use_id',$old_course_code);
        }
        
        if($this->db->delete('room_use')){
            return true;
        }

        return false;
    }

    public function delPeriodtime($year,$class_no,$term,$del_course_date,$del_period,$del_room_id){
        $this->db->where('year',$year);
        $this->db->where('class_no',$class_no);
        $this->db->where('term',$term);
        $this->db->where('course_date',$del_course_date);
        $this->db->where('id',$del_period);
        $this->db->where('room_id',$del_room_id);
        if(!empty($old_course_code)){
            $this->db->where('use_id',$old_course_code);
        }
        
        if($this->db->delete('periodtime')){
            return true;
        }

        return false;
    }

    public function insertRoomuse($year,$class_no,$term,$booking_date,$room_id,$course_code,$pno,$hours,$teacher_idno=NULL,$teacher_title=NULL,$teacher_sort=NULL,$is_teacher=NULL){
        $this->db->set('room_id',$room_id);
        $this->db->set('num',0);
        $this->db->set('expense',0);
        $this->db->set('use_date',$booking_date);
        $this->db->set('use_period',$pno);
        $this->db->set('use_type',0);
        $this->db->set('year',$year);
        $this->db->set('class_id',$class_no);
        $this->db->set('term',$term);
        $this->db->set('use_id',$course_code);
        $this->db->set('teacher_id',$teacher_idno);
        $this->db->set('isteacher',$is_teacher);
        $this->db->set('hrs',$hours);
        $this->db->set('title',$teacher_title);

        if(!empty($teacher_sort)){
            $this->db->set('sort',$teacher_sort);
        }
        
        if($this->db->insert('room_use')){
            return true;
        }

        return false;
    }

    public function chkHours($year,$class_no,$term){
        $sql = sprintf("SELECT
                            CASE
                        WHEN range_real = hrs THEN
                            'Y'
                        ELSE
                            'N'
                        END as status
                        FROM
                            (
                                SELECT
                                    a.range_real,
                                    nvl (b.hrs, 0) AS hrs
                                FROM
                                    `require` a 
                                LEFT JOIN (
                                    SELECT
                                        sum(hrs) AS hrs
                                    FROM
                                        (
                                            SELECT
                                                max(hrs) AS hrs
                                            FROM
                                                room_use
                                            WHERE
                                                year = '%s'
                                            AND class_id = '%s'
                                            AND term = '%s'
                                            GROUP BY
                                                year,
                                                class_id,
                                                term,
                                                use_period,
                                                use_id,
                                                use_date
                                        ) c
                                ) b ON 1 = 1 
                                WHERE
                                    a.year = '%s'
                                AND a.class_no = '%s'
                                AND a.term = '%s'
                            ) d",$year,$class_no,$term,$year,$class_no,$term);

        $query = $this->db->query($sql);
        $result = $query->result_array();

        return $result[0]['status'];
    }

    public function chkHoursNew($year,$class_no,$term){
        $sql = sprintf("SELECT
                            range_real,
                            hrs
                        FROM
                            (
                                SELECT
                                    a.range_real,
                                    nvl (b.hrs, 0) AS hrs
                                FROM
                                    `require` a 
                                LEFT JOIN (
                                    SELECT
                                        sum(hrs) AS hrs
                                    FROM
                                        (
                                            SELECT
                                                max(hrs) AS hrs
                                            FROM
                                                room_use
                                            WHERE
                                                year = %s
                                            AND class_id = %s
                                            AND term = %s
                                            GROUP BY
                                                year,
                                                class_id,
                                                term,
                                                use_period,
                                                use_id,
                                                use_date
                                        ) c
                                ) b ON 1 = 1 
                                WHERE
                                    a.year = %s
                                AND a.class_no = %s
                                AND a.term = %s
                            ) d",$this->db->escape(addslashes($year)),$this->db->escape(addslashes($class_no)),$this->db->escape(addslashes($term)),$this->db->escape(addslashes($year)),$this->db->escape(addslashes($class_no)),$this->db->escape(addslashes($term)));

        $query = $this->db->query($sql);
        $result = $query->result_array();

        return $result;
    }

    public function chkHourTrafficTax($year,$class_no,$term,$booking_date){
        $this->db->select('count(1) cnt');
        $this->db->where('year',$year);
        $this->db->where('class_no',$class_no);
        $this->db->where('term',$term);
        $this->db->where('use_date',$booking_date);
        $this->db->where('(status is not null and status != "")', NULL, FALSE);
        $query = $this->db->get('hour_traffic_tax');
        $result = $query->result_array();

        if($result[0]['cnt'] > 0){
            return true;
        }

        return false;
    }

    public function delHourTrafficTax($year,$class_no,$term,$booking_date){
        $sql = sprintf("DELETE
                        FROM
                            hour_traffic_tax
                        WHERE
                            (
                                YEAR,
                                class_no,
                                term,
                                use_date
                            ) IN (SELECT
                                YEAR,
                                class_id AS class_no,
                                term,
                                use_date
                            FROM
                                room_use
                            WHERE
                                year = %s
                            AND class_id = %s
                            AND term = %s
                            AND use_date = %s
                            GROUP BY
                                YEAR,
                                class_id,
                                term,
                                use_date)",$this->db->escape(addslashes($year)),$this->db->escape(addslashes($class_no)),$this->db->escape(addslashes($term)),$this->db->escape(addslashes($booking_date)));

        if($this->db->query($sql)){
            return true;
        }

        return false;
    }   

    public function delDiningTeacher($year,$class_no,$term,$booking_date){
        $this->db->where('year',$year);
        $this->db->where('class_no',$class_no);
        $this->db->where('term',$term);
        $this->db->where('use_date',$booking_date);

        if($this->db->delete('dining_teacher')){
            return true;
        }

        return false;
    }

    public function delDiningStudent($year,$class_no,$term,$booking_date){
        $this->db->where('year',$year);
        $this->db->where('class_no',$class_no);
        $this->db->where('term',$term);
        $this->db->where('use_date',$booking_date);

        if($this->db->delete('dining_student')){
            return true;
        }

        return false;
    }

    public function insertDiningTeacher($year,$class_no,$term,$booking_date,$cre_user){
        $this->db->trans_start();
        $this->delDiningTeacher($year,$class_no,$term,$booking_date);

        $sql_M = sprintf("INSERT INTO dining_teacher (
                            year,
                            class_no,
                            term,
                            class_name,
                            use_date,
                            dining_type,
                            ID,
                            NAME,
                            TYPE,
                            cre_user,
                            cre_date,
                            upd_user,
                            upd_date
                        ) SELECT DISTINCT
                            year,
                            class_id,
                            term,
                            class_name,
                            use_date,
                            'A',
                            teacher_id,
                            NAME,
                            (
                                CASE
                                WHEN isteacher = 'Y' THEN
                                    '1'
                                ELSE
                                    '2'
                                END
                            ) AS TYPE,
                            %s,
                            now(),
                            %s,
                            now()
                        FROM
                            (
                                SELECT
                                    A .*,
                                    (
                                        CASE
                                        WHEN class_cate = '2' THEN
                                            'Y'
                                        END
                                    ) AS M,
                                    (
                                        CASE
                                        WHEN '1130' BETWEEN from_time
                                        AND TO_TIME THEN
                                            'Y'
                                        END
                                    ) AS L,
                                    (
                                        CASE
                                        WHEN '1800' BETWEEN from_time
                                        AND TO_TIME THEN
                                            'Y'
                                        END
                                    ) AS D,
                                    B.Breakfast_Type AS M1,
                                    (
                                        A .no_persons * NVL (
                                            B.Breakfast_Money,
                                            C1.price
                                        )
                                    ) AS M2,
                                    (
                                        ROUND (A .no_persons / 10) * B.Breakfast_Money
                                    ) AS M3,
                                    B.Lunch_type AS L1,
                                    (
                                        A .no_persons * NVL (B.lunch_money, C2.price)
                                    ) AS L2,
                                    (
                                        ROUND (A .no_persons / 10) * B.lunch_money
                                    ) AS L3,
                                    B.Dinner_Type AS D1,
                                    (
                                        A .no_persons * NVL (B.dinner_money, C3.price)
                                    ) AS D2,
                                    (
                                        ROUND (A .no_persons / 10) * B.dinner_money
                                    ) AS D3
                                FROM
                                    (
                                        SELECT
                                            year,
                                            class_id,
                                            term,
                                            class_name,
                                            worker,
                                            no_persons,
                                            class_cate,
                                            use_date,
                                            teacher_id,
                                            isteacher,
                                            name,
                                            min(from_time) AS from_time,
                                            max(TO_TIME) AS TO_TIME
                                        FROM
                                            (
                                                SELECT
                                                    A . year,
                                                    A .class_id,
                                                    A .term,
                                                    A .use_date,
                                                    A .room_id,
                                                    A .teacher_id,
                                                    A .isteacher,
                                                    c.class_name,
                                                    c.worker,
                                                    c.no_persons,
                                                    c.class_cate,
                                                    NVL (d1. NAME, d2. NAME) AS NAME,
                                                    NVL (b1.FROM_TIME, b2.FROM_TIME) AS FROM_TIME,
                                                    NVL (b1. TO_TIME, b2. TO_TIME) AS TO_TIME
                                                FROM
                                                    (
                                                        SELECT DISTINCT
                                                            year,
                                                            class_id,
                                                            term,
                                                            use_date,
                                                            use_period,
                                                            room_id,
                                                            use_id,
                                                            teacher_id,
                                                            isteacher
                                                        FROM
                                                            room_use
                                                        WHERE
                                                            year = %s
                                                        AND class_id = %s
                                                        AND term = %s
                                                        AND use_date = %s
                                                        AND teacher_id IS NOT NULL
                                                    ) A
                                                LEFT JOIN periodtime b1 ON A .use_period = b1. ID
                                                AND A . year = b1. year
                                                AND A .class_id = b1.class_no
                                                AND A .term = b1.term
                                                AND A .room_id = b1.room_id
                                                AND A .use_id = b1.course_code
                                                AND A .use_date = b1.course_date
                                                LEFT JOIN periodtime b2 ON A .use_period = b2. ID
                                                AND b2. year IS NULL
                                                AND b2.class_no IS NULL
                                                AND b2.term IS NULL
                                                AND b2.room_id IS NULL
                                                AND b2.course_code IS NULL
                                                LEFT JOIN `require` c ON A . year = c. year
                                                AND A .class_id = c.class_no
                                                AND A .term = c.term
                                                LEFT JOIN teacher d1 ON d1.Teacher_type = '1'
                                                AND d1. idno = A .teacher_id
                                                AND A .isteacher = 'Y'
                                                LEFT JOIN teacher d2 ON d2.Teacher_type = '2'
                                                AND d2. idno = A .teacher_id
                                                AND A .isteacher = 'N'
                                            ) T
                                        GROUP BY
                                            year,
                                            class_id,
                                            term,
                                            class_name,
                                            worker,
                                            no_persons,
                                            use_date,
                                            class_cate,
                                            teacher_id,
                                            isteacher,
                                            name
                                    ) A
                                LEFT JOIN dining B ON A . YEAR = B. YEAR
                                AND A .class_id = B.class_no
                                AND A .term = B.term
                                LEFT JOIN food_code C1 ON C1.item_id = 'A'
                                LEFT JOIN food_code C2 ON C2.item_id = 'B'
                                LEFT JOIN food_code C3 ON C3.item_id = 'C'
                            ) X
                        WHERE
                            M IS NOT NULL",$this->db->escape(addslashes($cre_user)),$this->db->escape(addslashes($cre_user)),$this->db->escape(addslashes($year)),$this->db->escape(addslashes($class_no)),$this->db->escape(addslashes($term)),$this->db->escape(addslashes($booking_date)));

        $this->db->query($sql_M);

        $sql_L = sprintf("INSERT INTO dining_teacher (
                                year,
                                class_no,
                                term,
                                class_name,
                                use_date,
                                dining_type,
                                ID,
                                NAME,
                                TYPE,
                                cre_user,
                                cre_date,
                                upd_user,
                                upd_date
                            ) SELECT DISTINCT
                                YEAR,
                                class_id,
                                term,
                                class_name,
                                use_date,
                                'B',
                                teacher_id,
                                NAME,
                                (
                                    CASE
                                    WHEN isteacher = 'Y' THEN
                                        '1'
                                    ELSE
                                        '2'
                                    END
                                ) AS TYPE,
                                %s,
                                now(),
                                %s,
                                now()
                            FROM
                                (
                                    SELECT
                                        A .*,
                                        (
                                            CASE
                                            WHEN class_cate = '2' THEN
                                                'Y'
                                            END
                                        ) AS M,
                                        (
                                            CASE
                                            WHEN '1130' BETWEEN from_time
                                            AND TO_TIME THEN
                                                'Y'
                                            END
                                        ) AS L,
                                        (
                                            CASE
                                            WHEN '1800' BETWEEN from_time
                                            AND TO_TIME THEN
                                                'Y'
                                            END
                                        ) AS D,
                                        B.Breakfast_Type AS M1,
                                        (
                                            A .no_persons * NVL (
                                                B.Breakfast_Money,
                                                C1.price
                                            )
                                        ) AS M2,
                                        (
                                            ROUND (A .no_persons / 10) * B.Breakfast_Money
                                        ) AS M3,
                                        B.Lunch_type AS L1,
                                        (
                                            A .no_persons * NVL (B.lunch_money, C2.price)
                                        ) AS L2,
                                        (
                                            ROUND (A .no_persons / 10) * B.lunch_money
                                        ) AS L3,
                                        B.Dinner_Type AS D1,
                                        (
                                            A .no_persons * NVL (B.dinner_money, C3.price)
                                        ) AS D2,
                                        (
                                            ROUND (A .no_persons / 10) * B.dinner_money
                                        ) AS D3
                                    FROM
                                        (
                                            SELECT
                                                YEAR,
                                                class_id,
                                                term,
                                                class_name,
                                                worker,
                                                no_persons,
                                                class_cate,
                                                use_date,
                                                teacher_id,
                                                isteacher,
                                                NAME,
                                                min(from_time) AS from_time,
                                                max(TO_TIME) AS TO_TIME
                                            FROM
                                                (
                                                    SELECT
                                                        A . YEAR,
                                                        A .class_id,
                                                        A .term,
                                                        A .use_date,
                                                        A .room_id,
                                                        A .use_period,
                                                        A .teacher_id,
                                                        A .isteacher,
                                                        c.class_name,
                                                        c.worker,
                                                        c.no_persons,
                                                        c.class_cate,
                                                        NVL (d1. NAME, d2. NAME) AS NAME,
                                                        NVL (b1.FROM_TIME, b2.FROM_TIME) AS FROM_TIME,
                                                        NVL (b1. TO_TIME, b2. TO_TIME) AS TO_TIME
                                                    FROM
                                                        (
                                                            SELECT DISTINCT
                                                                room_use. YEAR,
                                                                room_use.class_id,
                                                                room_use.term,
                                                                room_use.use_date,
                                                                room_use.use_period,
                                                                room_use.room_id,
                                                                room_use.use_id,
                                                                room_use.teacher_id,
                                                                room_use.isteacher
                                                            FROM
                                                                room_use
                                                            JOIN venue_information ON room_use.ROOM_ID = venue_information.ROOM_ID
                                                            WHERE
                                                                room_use. YEAR = %s
                                                            AND room_use.class_id = %s
                                                            AND room_use.term = %s
                                                            AND room_use.use_date = %s
                                                            AND room_use.teacher_id IS NOT NULL
                                                            AND venue_information.room_bel = '68000'
                                                        ) A
                                                    LEFT JOIN periodtime b1 ON A .use_period = b1. ID
                                                    AND A . YEAR = b1. YEAR
                                                    AND A .class_id = b1.class_no
                                                    AND A .term = b1.term
                                                    AND A .room_id = b1.room_id
                                                    AND A .use_id = b1.course_code
                                                    AND A .use_date = b1.course_date
                                                    LEFT JOIN periodtime b2 ON A .use_period = b2. ID
                                                    AND b2. YEAR IS NULL
                                                    AND b2.class_no IS NULL
                                                    AND b2.term IS NULL
                                                    AND b2.room_id IS NULL
                                                    AND b2.course_code IS NULL
                                                    LEFT JOIN `require` c ON A . YEAR = c. YEAR
                                                    AND A .class_id = c.class_no
                                                    AND A .term = c.term
                                                    LEFT JOIN teacher d1 ON d1.teacher_type = '1'
                                                    AND d1. idno = A .teacher_id
                                                    AND A .isteacher = 'Y'
                                                    LEFT JOIN teacher d2 ON d2.teacher_type = '2'
                                                    AND d2. idno = A .teacher_id
                                                    AND A .isteacher = 'N'
                                                ) T
                                            GROUP BY
                                                YEAR,
                                                class_id,
                                                term,
                                                class_name,
                                                use_period,
                                                worker,
                                                no_persons,
                                                use_date,
                                                class_cate,
                                                teacher_id,
                                                isteacher,
                                                NAME
                                        ) A
                                    LEFT JOIN dining B ON A . YEAR = B. YEAR
                                    AND A .class_id = B.class_no
                                    AND A .term = B.term
                                    LEFT JOIN food_code C1 ON C1.item_id = 'A'
                                    LEFT JOIN food_code C2 ON C2.item_id = 'B'
                                    LEFT JOIN food_code C3 ON C3.item_id = 'C'
                                ) X
                            WHERE
                                L IS NOT NULL",$this->db->escape(addslashes($cre_user)),$this->db->escape(addslashes($cre_user)),$this->db->escape(addslashes($year)),$this->db->escape(addslashes($class_no)),$this->db->escape(addslashes($term)),$this->db->escape(addslashes($booking_date)));
        $this->db->query($sql_L);

        $sql_D = sprintf("INSERT INTO dining_teacher (
                                YEAR,
                                class_no,
                                term,
                                class_name,
                                use_date,
                                dining_type,
                                ID,
                                NAME,
                                TYPE,
                                cre_user,
                                cre_date,
                                upd_user,
                                upd_date
                            ) SELECT DISTINCT
                                YEAR,
                                class_id,
                                term,
                                class_name,
                                use_date,
                                'C',
                                teacher_id,
                                NAME,
                                (
                                    CASE
                                    WHEN isteacher = 'Y' THEN
                                        '1'
                                    ELSE
                                        '2'
                                    END
                                ) AS TYPE,
                                %s,
                                now(),
                                %s,
                                now()
                            FROM
                                (
                                    SELECT
                                        A .*,
                                        (
                                            CASE
                                            WHEN class_cate = '2' THEN
                                                'Y'
                                            END
                                        ) AS M,
                                        (
                                            CASE
                                            WHEN '1130' BETWEEN from_time
                                            AND TO_TIME THEN
                                                'Y'
                                            END
                                        ) AS L,
                                        (
                                            CASE
                                            WHEN '1800' BETWEEN from_time
                                            AND TO_TIME THEN
                                                'Y'
                                            END
                                        ) AS D,
                                        B.Breakfast_Type AS M1,
                                        (
                                            A .no_persons * NVL (
                                                B.Breakfast_Money,
                                                C1.price
                                            )
                                        ) AS M2,
                                        (
                                            ROUND (A .no_persons / 10) * B.Breakfast_Money
                                        ) AS M3,
                                        B.Lunch_type AS L1,
                                        (
                                            A .no_persons * NVL (B.lunch_money, C2.price)
                                        ) AS L2,
                                        (
                                            ROUND (A .no_persons / 10) * B.lunch_money
                                        ) AS L3,
                                        B.Dinner_Type AS D1,
                                        (
                                            A .no_persons * NVL (B.dinner_money, C3.price)
                                        ) AS D2,
                                        (
                                            ROUND (A .no_persons / 10) * B.dinner_money
                                        ) AS D3
                                    FROM
                                        (
                                            SELECT
                                                YEAR,
                                                class_id,
                                                term,
                                                class_name,
                                                worker,
                                                no_persons,
                                                class_cate,
                                                use_date,
                                                teacher_id,
                                                isteacher,
                                                NAME,
                                                min(from_time) AS from_time,
                                                max(TO_TIME) AS TO_TIME
                                            FROM
                                                (
                                                    SELECT
                                                        A . YEAR,
                                                        A .class_id,
                                                        A .term,
                                                        A .use_date,
                                                        A .room_id,
                                                        A .teacher_id,
                                                        A .isteacher,
                                                        c.class_name,
                                                        c.worker,
                                                        c.no_persons,
                                                        c.class_cate,
                                                        NVL (d1. NAME, d2. NAME) AS NAME,
                                                        NVL (b1.FROM_TIME, b2.FROM_TIME) AS FROM_TIME,
                                                        NVL (b1. TO_TIME, b2. TO_TIME) AS TO_TIME
                                                    FROM
                                                        (
                                                            SELECT DISTINCT
                                                                room_use. YEAR,
                                                                room_use.class_id,
                                                                room_use.term,
                                                                room_use.use_date,
                                                                room_use.use_period,
                                                                room_use.room_id,
                                                                room_use.use_id,
                                                                room_use.teacher_id,
                                                                room_use.isteacher
                                                            FROM
                                                                room_use
                                                            JOIN venue_information ON room_use.ROOM_ID = venue_information.ROOM_ID
                                                            WHERE
                                                                room_use. YEAR = %s
                                                            AND room_use.class_id = %s
                                                            AND room_use.term = %s
                                                            AND room_use.use_date = %s
                                                            AND room_use.teacher_id IS NOT NULL
                                                            AND venue_information.room_bel = '68000'
                                                        ) A
                                                    LEFT JOIN periodtime b1 ON A .use_period = b1. ID
                                                    AND A . YEAR = b1. YEAR
                                                    AND A .class_id = b1.class_no
                                                    AND A .term = b1.term
                                                    AND A .room_id = b1.room_id
                                                    AND A .use_id = b1.course_code
                                                    AND A .use_date = b1.course_date
                                                    LEFT JOIN periodtime b2 ON A .use_period = b2. ID
                                                    AND b2. YEAR IS NULL
                                                    AND b2.class_no IS NULL
                                                    AND b2.term IS NULL
                                                    AND b2.room_id IS NULL
                                                    AND b2.course_code IS NULL
                                                    LEFT JOIN `require` c ON A . YEAR = c. YEAR
                                                    AND A .class_id = c.class_no
                                                    AND A .term = c.term
                                                    LEFT JOIN teacher d1 ON d1.teacher_type = '1'
                                                    AND d1. idno = A .teacher_id
                                                    AND A .isteacher = 'Y'
                                                    LEFT JOIN teacher d2 ON d2.teacher_type = '2'
                                                    AND d2. idno = A .teacher_id
                                                    AND A .isteacher = 'N'
                                                ) T
                                            GROUP BY
                                                YEAR,
                                                class_id,
                                                term,
                                                class_name,
                                                worker,
                                                no_persons,
                                                use_date,
                                                class_cate,
                                                teacher_id,
                                                isteacher,
                                                NAME
                                        ) A
                                    LEFT JOIN dining B ON A . YEAR = B. YEAR
                                    AND A .class_id = B.class_no
                                    AND A .term = B.term
                                    LEFT JOIN food_code C1 ON C1.item_id = 'A'
                                    LEFT JOIN food_code C2 ON C2.item_id = 'B'
                                    LEFT JOIN food_code C3 ON C3.item_id = 'C'
                                ) X
                            WHERE
                                D IS NOT NULL",$this->db->escape(addslashes($cre_user)),$this->db->escape(addslashes($cre_user)),$this->db->escape(addslashes($year)),$this->db->escape(addslashes($class_no)),$this->db->escape(addslashes($term)),$this->db->escape(addslashes($booking_date)));  
        $this->db->query($sql_D);

        $this->db->trans_complete();

        if($this->db->trans_status() === TRUE){
            return true;
        } 

        return false;
    }

    public function insertDiningStudent($year,$class_no,$term,$booking_date,$cre_user){
        $this->db->trans_start();

        $this->delDiningStudent($year,$class_no,$term,$booking_date);

        $sql = sprintf("INSERT INTO dining_student (
                                YEAR,
                                class_no,
                                term,
                                class_name,
                                worker,
                                use_date,
                                persons_1,
                                amount_1,
                                persons_2,
                                amount_2,
                                persons_3,
                                amount_3,
                                total_amount,
                                cre_user,
                                cre_date,
                                upd_user,
                                upd_date
                            ) SELECT
                                YEAR,
                                class_id,
                                term,
                                class_name,
                                worker,
                                use_date,
                                (
                                    CASE
                                    WHEN M IS NOT NULL THEN
                                        no_persons
                                    END
                                ) AS p1,
                                (
                                    CASE
                                    WHEN M IS NOT NULL THEN
                                        CASE
                                    WHEN M1 = '2' THEN
                                        M3
                                    ELSE
                                        M2
                                    END
                                    END
                                ) AS p1Amt,
                                (
                                    CASE
                                    WHEN L IS NOT NULL THEN
                                        no_persons
                                    END
                                ) AS p2,
                                (
                                    CASE
                                    WHEN L IS NOT NULL THEN
                                        CASE
                                    WHEN L1 = '2' THEN
                                        L3
                                    ELSE
                                        L2
                                    END
                                    END
                                ) AS p2Amt,
                                (
                                    CASE
                                    WHEN D IS NOT NULL THEN
                                        no_persons
                                    END
                                ) AS p3,
                                (
                                    CASE
                                    WHEN D IS NOT NULL THEN
                                        CASE
                                    WHEN D1 = '2' THEN
                                        D3
                                    ELSE
                                        D2
                                    END
                                    END
                                ) AS p3Amt,
                                NVL (
                                    (
                                        CASE
                                        WHEN M IS NOT NULL THEN
                                            CASE
                                        WHEN M1 = '2' THEN
                                            M3
                                        ELSE
                                            M2
                                        END
                                        END
                                    ),
                                    0
                                ) + NVL (
                                    (
                                        CASE
                                        WHEN L IS NOT NULL THEN
                                            CASE
                                        WHEN L1 = '2' THEN
                                            L3
                                        ELSE
                                            L2
                                        END
                                        END
                                    ),
                                    0
                                ) + NVL (
                                    (
                                        CASE
                                        WHEN D IS NOT NULL THEN
                                            CASE
                                        WHEN D1 = '2' THEN
                                            D3
                                        ELSE
                                            D2
                                        END
                                        END
                                    ),
                                    0
                                ) AS totAmt,
                                %s,
                                now(),
                                %s,
                                now()
                            FROM
                                (
                                    SELECT
                                        A .*,
                                        (
                                            CASE
                                            WHEN class_cate = '2' THEN
                                                'Y'
                                            END
                                        ) AS M,
                                        (
                                            CASE
                                            WHEN '1130' BETWEEN from_time
                                            AND TO_TIME THEN
                                                'Y'
                                            END
                                        ) AS L,
                                        (
                                            CASE
                                            WHEN '1800' BETWEEN from_time
                                            AND TO_TIME THEN
                                                'Y'
                                            END
                                        ) AS D,
                                        B.Breakfast_Type AS M1,
                                        (
                                            (A .no_persons + NVL(E1.cnt, 0)) * NVL (
                                                B.Breakfast_Money,
                                                C1.price
                                            )
                                        ) AS M2,
                                        (
                                            ROUND (
                                                (A .no_persons + NVL(E1.cnt, 0)) / 10
                                            ) * B.Breakfast_Money
                                        ) AS M3,
                                        B.Lunch_type AS L1,
                                        (
                                            (A .no_persons + NVL(E2.cnt, 0)) * NVL (B.lunch_money, C2.price)
                                        ) AS L2,
                                        (
                                            ROUND (
                                                (A .no_persons + NVL(E2.cnt, 0)) / 10
                                            ) * B.lunch_money
                                        ) AS L3,
                                        B.Dinner_Type AS D1,
                                        (
                                            (A .no_persons + NVL(E3.cnt, 0)) * NVL (B.dinner_money, C3.price)
                                        ) AS D2,
                                        (
                                            ROUND (
                                                (A .no_persons + NVL(E3.cnt, 0)) / 10
                                            ) * B.dinner_money
                                        ) AS D3
                                    FROM
                                        (
                                            SELECT
                                                YEAR,
                                                class_id,
                                                term,
                                                class_name,
                                                worker,
                                                no_persons,
                                                class_cate,
                                                use_date,
                                                min(from_time) AS from_time,
                                                max(TO_TIME) AS TO_TIME
                                            FROM
                                                (
                                                    SELECT
                                                        A . YEAR,
                                                        A .class_id,
                                                        A .term,
                                                        A .use_date,
                                                        A .room_id,
                                                        c.class_name,
                                                        c.worker,
                                                        NVL (c.no_persons, 0) AS no_persons,
                                                        c.class_cate,
                                                        NVL (b1.FROM_TIME, b2.FROM_TIME) AS FROM_TIME,
                                                        NVL (b1. TO_TIME, b2. TO_TIME) AS TO_TIME
                                                    FROM
                                                        (
                                                            SELECT DISTINCT
                                                                room_use. YEAR,
                                                                room_use.class_id,
                                                                room_use.term,
                                                                room_use.use_date,
                                                                room_use.use_period,
                                                                room_use.room_id,
                                                                room_use.use_id
                                                            FROM
                                                                room_use
                                                            JOIN venue_information ON room_use.ROOM_ID = venue_information.ROOM_ID
                                                            WHERE
                                                                room_use. YEAR = %s
                                                            AND room_use.class_id = %s
                                                            AND room_use.term = %s
                                                            AND room_use.use_date = %s
                                                            AND room_use.appi_id IS NULL
                                                            AND venue_information.room_bel = '68000'
                                                        ) A
                                                    LEFT JOIN periodtime b1 ON A .use_period = b1. ID
                                                    AND A . YEAR = b1. YEAR
                                                    AND A .class_id = b1.class_no
                                                    AND A .term = b1.term
                                                    AND A .room_id = b1.room_id
                                                    AND A .use_id = b1.course_code
                                                    AND A .use_date = b1.course_date
                                                    LEFT JOIN periodtime b2 ON A .use_period = b2. ID
                                                    AND b2. YEAR IS NULL
                                                    AND b2.class_no IS NULL
                                                    AND b2.term IS NULL
                                                    AND b2.room_id IS NULL
                                                    AND b2.course_code IS NULL
                                                    LEFT JOIN `require` c ON A . YEAR = c. YEAR
                                                    AND A .class_id = c.class_no
                                                    AND A .term = c.term
                                                ) T
                                            GROUP BY
                                                YEAR,
                                                class_id,
                                                term,
                                                class_name,
                                                worker,
                                                no_persons,
                                                use_date,
                                                class_cate
                                        ) A
                                    LEFT JOIN dining B ON A . YEAR = B. YEAR
                                    AND A .class_id = B.class_no
                                    AND A .term = B.term
                                    LEFT JOIN food_code C1 ON C1.item_id = 'A'
                                    LEFT JOIN food_code C2 ON C2.item_id = 'B'
                                    LEFT JOIN food_code C3 ON C3.item_id = 'C'
                                    LEFT JOIN (
                                        SELECT
                                            YEAR,
                                            CLASS_NO,
                                            TERM,
                                            USE_DATE,
                                            count(1) AS CNT
                                        FROM
                                            dining_teacher
                                        WHERE
                                            DINING_TYPE = 'A'
                                        GROUP BY
                                            YEAR,
                                            CLASS_NO,
                                            TERM,
                                            USE_DATE
                                    ) E1 ON A . YEAR = E1. YEAR
                                    AND A .CLASS_ID = E1.CLASS_NO
                                    AND A .TERM = E1.TERM
                                    AND A .USE_DATE = E1.USE_DATE
                                    LEFT JOIN (
                                        SELECT
                                            YEAR,
                                            CLASS_NO,
                                            TERM,
                                            USE_DATE,
                                            count(1) AS CNT
                                        FROM
                                            dining_teacher
                                        WHERE
                                            DINING_TYPE = 'B'
                                        GROUP BY
                                            YEAR,
                                            CLASS_NO,
                                            TERM,
                                            USE_DATE
                                    ) E2 ON A . YEAR = E2. YEAR
                                    AND A .CLASS_ID = E2.CLASS_NO
                                    AND A .TERM = E2.TERM
                                    AND A .USE_DATE = E2.USE_DATE
                                    LEFT JOIN (
                                        SELECT
                                            YEAR,
                                            CLASS_NO,
                                            TERM,
                                            USE_DATE,
                                            count(1) AS CNT
                                        FROM
                                            dining_teacher
                                        WHERE
                                            DINING_TYPE = 'C'
                                        GROUP BY
                                            YEAR,
                                            CLASS_NO,
                                            TERM,
                                            USE_DATE
                                    ) E3 ON A . YEAR = E3. YEAR
                                    AND A .CLASS_ID = E3.CLASS_NO
                                    AND A .TERM = E3.TERM
                                    AND A .USE_DATE = E3.USE_DATE
                                ) X
                            WHERE
                                M IS NOT NULL
                            OR L IS NOT NULL
                            OR D IS NOT NULL",$this->db->escape(addslashes($cre_user)),$this->db->escape(addslashes($cre_user)),$this->db->escape(addslashes($year)),$this->db->escape(addslashes($class_no)),$this->db->escape(addslashes($term)),$this->db->escape(addslashes($booking_date)));
        
        $this->db->query($sql);

        $this->db->trans_complete();

        if($this->db->trans_status() === TRUE){
            return true;
        } 

        return false;
    }

    public function updateRequireStartEndDate($year,$class_no,$term){
        $sql = sprintf("UPDATE `require`
                        SET START_DATE1 = NVL (
                            (
                                SELECT
                                    min(use_date)
                                FROM
                                    room_use
                                WHERE
                                    YEAR = %s
                                AND CLASS_ID = %s
                                AND TERM = %s
                            ),
                            START_DATE1
                        ),
                         END_DATE1 = NVL (
                            (
                                SELECT
                                    max(use_date)
                                FROM
                                    room_use
                                WHERE
                                    YEAR = %s
                                AND CLASS_ID = %s
                                AND TERM = %s
                            ),
                            END_DATE1
                        )
                        WHERE
                            YEAR = %s
                        AND CLASS_NO = %s
                        AND TERM = %s",$this->db->escape(addslashes($year)),$this->db->escape(addslashes($class_no)),$this->db->escape(addslashes($term)),$this->db->escape(addslashes($year)),$this->db->escape(addslashes($class_no)),$this->db->escape(addslashes($term)),$this->db->escape(addslashes($year)),$this->db->escape(addslashes($class_no)),$this->db->escape(addslashes($term)));

        if($this->db->query($sql)){
            return true;
        }

        return false;
    }

    public function getRequireStartMonth($year,$class_no,$term){
        $this->db->select('start_date1');
        $this->db->where('year',$year);
        $this->db->where('class_no',$class_no);
        $this->db->where('term',$term);
        $query = $this->db->get('require');
        $result = $query->result_array();

        if(!empty($result)){
            $month = date('m',strtotime($result[0]['start_date1']));

            return $month;
        }

        return 0;
    }

    public function updateRequireSeason($year,$class_no,$term){
        $month = $this->getRequireStartMonth($year,$class_no,$term);
        $month = intval($month);

        $season = '';

        if ($month>=1 && $month<=3){
          $season = "1";
        } else if ($month>=4 && $month<=6){
          $season = "2";
        } else if ($month>=7 && $month<=9){
          $season = "3";
        } else if ($month>=10 && $month<=12){
          $season = "4";
        }

        $this->db->set('reason',$season);
        $this->db->where('year',$year);
        $this->db->where('class_no',$class_no);
        $this->db->where('term',$term);

        if($this->db->update('require')){
            return true;
        }

        return false;
    }

    public function updateRequireRoom($year,$class_no,$term){
        $sql = sprintf("UPDATE `require`
                        SET room_code = (
                            SELECT
                                room_id
                            FROM
                                room_use
                            WHERE
                                YEAR = %s
                            AND CLASS_ID = %s
                            AND TERM = %s
                            ORDER BY
                                USE_DATE,
                                USE_PERIOD
                            LIMIT 1
                        )
                        WHERE
                            YEAR = %s
                        AND CLASS_NO = %s
                        AND TERM = %s",$this->db->escape(addslashes($year)),$this->db->escape(addslashes($class_no)),$this->db->escape(addslashes($term)),$this->db->escape(addslashes($year)),$this->db->escape(addslashes($class_no)),$this->db->escape(addslashes($term)));

        if($this->db->query($sql)){
            return true;
        }  

        return false;
    }

    public function delCourseTeacher($year,$class_no,$term){
        $this->db->where('year',$year);
        $this->db->where('class_no',$class_no);
        $this->db->where('term',$term);

        if($this->db->delete('courseteacher')){
            return true;
        }

        return false;
    }

    public function insertCourseTeacher($year,$class_no,$term,$cre_user){
        $this->db->trans_start();
        $this->delCourseTeacher($year,$class_no,$term);

        $sql = sprintf("INSERT INTO courseteacher (
                            YEAR,
                            CLASS_NO,
                            TERM,
                            COURSE_CODE,
                            TEACHER_ID,
                            ISEVALUATE,
                            CRE_USER,
                            CRE_DTE,
                            UPD_USER,
                            UPD_DATE,
                            ASSESS_DATE,
                            USE_DATE,
                            CO_SYNC2EPA
                        ) SELECT DISTINCT
                            YEAR,
                            class_id,
                            term,
                            use_id,
                            teacher_id,
                            'N',
                            %s,
                            now(),
                            %s,
                            now(),
                            NULL,
                            use_date,
                            'N'
                        FROM
                            room_use
                        WHERE
                            isteacher = 'Y'
                        AND YEAR = %s
                        AND CLASS_ID = %s
                        AND TERM = %s",$this->db->escape(addslashes($cre_user)),$this->db->escape(addslashes($cre_user)),$this->db->escape(addslashes($year)),$this->db->escape(addslashes($class_no)),$this->db->escape(addslashes($term)));

        $this->db->query($sql);

        $this->db->trans_complete();

        if($this->db->trans_status() === TRUE){
            return true;
        } 

        return false;
    }

    public function getCopyScheduleDate($year,$class_no,$term){
        $sql = sprintf("SELECT date_format(use_date, '%%Y-%%m-%%d') as use_date FROM room_use WHERE year = %s AND class_id = %s AND term = %s GROUP BY use_date", $this->db->escape(addslashes($year)), $this->db->escape(addslashes($class_no)), $this->db->escape(addslashes($term)));

        $query = $this->db->query($sql); 
        $result = $query->result_array();

        return $result;
    }

    public function getCopyScheduleRoom(){
        $sql = "SELECT room_id, room_name FROM venue_information WHERE room_type = '01' AND IFNULL(del_flag,'N') <> 'Y'";

        $query = $this->db->query($sql); 
        $result = $query->result_array();

        return $result;
    }

    public function getCourseSchedule($year,$class_no,$term){
        $sql = sprintf("SELECT
                            date_format(room_use.use_date, '%%Y-%%m-%%d') as use_date,
                            periodtime.from_time,
                            periodtime.to_time,
                            periodtime.id as pno,
                            periodtime.name as pidnm,
                            course_code.`name` AS course_name,
                            venue_information.room_name,
                            teacher.`name` AS teacher_name,
                            room_use.hrs,
                            room_use.isteacher,
                            room_use.use_id,
                            room_use.room_id,
                            room_use.use_period,
                            periodtime.name as period_name,
                            room_use.title,
                            room_use.teacher_id,
                            room_use.sort
                        FROM
                            room_use
                        JOIN periodtime ON room_use.`year` = periodtime.`year`
                        AND room_use.class_id = periodtime.class_no
                        AND room_use.term = periodtime.term
                        AND room_use.use_date = periodtime.course_date
                        AND room_use.use_id = periodtime.course_code
                        AND room_use.room_id = periodtime.room_id
                        AND room_use.use_period = periodtime.id
                        LEFT JOIN course_code ON room_use.use_id = course_code.item_id
                        LEFT JOIN venue_information ON room_use.room_id = venue_information.room_id
                        LEFT JOIN teacher ON room_use.teacher_id = teacher.idno and room_use.isteacher = teacher.teacher
                        WHERE
                            room_use.year = %s
                        AND room_use.class_id = %s
                        AND room_use.term = %s
                        GROUP BY room_use.use_date,periodtime.from_time,periodtime.to_time,room_use.teacher_id,room_use.room_id 
                        order by room_use.use_date,periodtime.from_time,periodtime.to_time,teacher.teacher_type,room_use.sort,room_use.use_period",$this->db->escape(addslashes($year)),$this->db->escape(addslashes($class_no)),$this->db->escape(addslashes($term)));

        // die($sql);
        $query = $this->db->query($sql); 
        $result = $query->result_array();
        for($s=0;$s<count($result);$s++){
           $result[$s]['trafic_status']=$this->getHourTrafficTax($result[$s],$year,$class_no,$term);
        }
        //var_dump($result);

        return $result;
    }
    public function getHourTrafficTax($data=array(),$year,$class_no,$term){
        //var_dump($data);
        $this->db->select('status,teacher_id');
        $this->db->where('year',$year);
        $this->db->where('class_no',$class_no);
        $this->db->where('term',$term);
        $this->db->where('teacher_id',$data['teacher_id']);
        $this->db->where('use_date',$data['use_date']);
        $query=$this->db->get('hour_traffic_tax');
        $query=$query->result_array();
        //var_dump($query);
        if(empty($query)){
            return null;
        }
        return $query[0]['status'];

    }

    public function getClassIsready($year,$class_no,$term){
        $this->db->select('isready');
        $this->db->where('class_year',$year);
        $this->db->where('class_id',$class_no);
        $this->db->where('class_term',$term);
        $query = $this->db->get('feedback_course_collocation');
        $result = $query->result_array();

        return $result;
    }

    public function insertHourTrafficTax($year,$class_no,$term,$booking_date){
        $this->db->trans_start();
        $this->delHourTrafficTax($year,$class_no,$term,$booking_date);

        $sql = sprintf("INSERT INTO hour_traffic_tax (
                            year,
                            class_no,
                            term,
                            class_name,
                            start_date,
                            end_date,
                            teacher_id,
                            teacher_name,
                            teacher_bank_type,
                            teacher_bank_id,
                            teacher_account,
                            teacher_acct_name,
                            teacher_addr,
                            hrs,
                            unit_hour_fee,
                            traffic_fee,
                            T_source,
                            A_source,
                            HT_class_type,
                            use_date,
                            IsTeacher,
                            worker_id,
                            hour_fee,
                            subtotal,
                            tax_rate,
                            tax,
                            aftertax,
                            hour_fee_is_changed,
                            unit_hour_fee_is_changed
                        ) SELECT
                            a.year,
                            a.class_no,
                            a.term,
                            b.class_name,
                            b.start_date1 AS start_date,
                            b.end_date1 AS end_date,
                            a.teacher_id,
                            nvl (c1.name, c2.name),
                            d.remark,
                            nvl (c1.bank_code, c2.bank_code),
                            nvl (c1.bank_account, c2.bank_account),
                            nvl (c1.account_name, c2.account_name),
                            nvl (c1.route, c2.route),
                            a.hrs,
                            nvl (
                                nvl (e1.hour_fee, e2.hour_fee),
                                0
                            ) AS unit_hour_fee,
                            CASE
                        WHEN nvl (
                            nvl (
                                e1.traffic_fee,
                                e2.traffic_fee
                            ),
                            0
                        ) < 0 THEN
                            0
                        ELSE
                            nvl (
                                nvl (
                                    e1.traffic_fee,
                                    e2.traffic_fee
                                ),
                                0
                            )
                        END AS traffic_fee,
                         (
                            CASE
                            WHEN a.IsTeacher = 'N' THEN
                                c2.hire_type
                            ELSE
                                c1.hire_type
                            END
                        ) AS T_source,
                         c2.hire_type AS A_sourse,
                         b.HT_class_type,
                         a.use_date,
                         a.IsTeacher,
                         b.worker,
                         0,
                         0,
                         0,
                         0,
                         0,
                         'N',
                         'N'
                        FROM
                            (
                                SELECT
                                    year,
                                    class_id AS class_no,
                                    term,
                                    use_date,
                                    teacher_id,
                                    nvl (IsTeacher, 'N') AS IsTeacher,
                                    sum(nvl(hrs, 0)) AS hrs
                                FROM
                                    room_use
                                WHERE
                                    year = %s
                                AND class_id = %s
                                AND term = %s
                                AND use_date = %s
                                AND teacher_id IS NOT NULL
                                GROUP BY
                                    year,
                                    class_id,
                                    term,
                                    use_date,
                                    teacher_id,
                                    nvl (IsTeacher, 'N')
                                ORDER BY
                                    use_date
                            ) a
                        LEFT JOIN `require` b ON a.year = b.year
                        AND a.class_no = b.class_no
                        AND a.term = b.term
                        LEFT JOIN teacher c1 ON a.teacher_id = c1.idno
                        AND c1.teacher_type = '1'
                        LEFT JOIN teacher c2 ON a.teacher_id = c2.idno
                        AND c2.teacher_type = '2'
                        LEFT JOIN bank_code d ON nvl (c1.bank_code, c2.bank_code) = d.item_id
                        LEFT JOIN hour_fee e1 ON b.ht_class_type = e1.class_type_id
                        AND c1.hire_type = e1.teacher_type_id
                        AND e1.type = '1'
                        AND a.IsTeacher = 'Y'
                        LEFT JOIN hour_fee e2 ON b.ht_class_type = e2.class_type_id
                        AND c2.hire_type = e2.teacher_type_id
                        AND c2.hire_type = e2.assistant_type_id
                        AND e2.type = '2'
                        AND a.IsTeacher = 'N'
                        WHERE
                            d.remark IS NOT NULL",$this->db->escape(addslashes($year)),$this->db->escape(addslashes($class_no)),$this->db->escape(addslashes($term)),$this->db->escape(addslashes($booking_date)));

        $this->db->query($sql);

        $this->db->trans_complete();

        if($this->db->trans_status() === TRUE){
            return true;
        } 

        return false;
    }

    public function updateHourTrafficTax($year,$class_no,$term,$booking_date){
        $this->db->trans_start();

        $sql_1 = sprintf("UPDATE hour_traffic_tax
                        SET hour_fee = (unit_hour_fee * hrs),
                         subtotal = (unit_hour_fee * hrs) + traffic_fee,
                         tax_rate = CASE
                        WHEN (unit_hour_fee * hrs) + traffic_fee > 40000 THEN
                            5
                        ELSE
                            0
                        END
                        WHERE
                            year = %s
                        AND class_no = %s
                        AND term = %s
                        AND use_date = %s",$this->db->escape(addslashes($year)),$this->db->escape(addslashes($class_no)),$this->db->escape(addslashes($term)),$this->db->escape(addslashes($booking_date)));

        $this->db->query($sql_1);

        $sql_2 = sprintf("UPDATE hour_traffic_tax
                        SET tax = CASE
                        WHEN (hour_fee * tax_rate / 100) < 2000 THEN
                            0
                        ELSE
                            (hour_fee * tax_rate / 100)
                        END,
                         aftertax = subtotal - (
                            CASE
                            WHEN (hour_fee * tax_rate / 100) < 2000 THEN
                                0
                            ELSE
                                (hour_fee * tax_rate / 100)
                            END
                        )
                        WHERE
                            year = %s
                        AND class_no = %s
                        AND term = %s
                        AND use_date = %s",$this->db->escape(addslashes($year)),$this->db->escape(addslashes($class_no)),$this->db->escape(addslashes($term)),$this->db->escape(addslashes($booking_date)));

        $this->db->query($sql_2);

        $this->db->trans_complete();

        if($this->db->trans_status() === TRUE){
            return true;
        } 

        return false;
    }

    public function getPreqMainData($year,$class_no,$term){
        $this->db->select('preq_id,start_date,end_date');
        $this->db->where('year',$year);
        $this->db->where('class_no',$class_no);
        $this->db->where('term',$term);
        $query = $this->db->get('preq_main');
        $result = $query->result_array();

        return $result;
    }

    public function getPreqItem($preq_id){
        $this->db->select('item_title');
        $this->db->where('preq_id',$preq_id);
        $this->db->order_by('item_id');
        $query = $this->db->get('preq_item');
        $result = $query->result_array();

        return $result;
    }

    public function getPreqCount($preq_id){
        $this->db->select('count(1) cnt');
        $this->db->where('preq_id',$preq_id);
        $query = $this->db->get('preq_result');
        $result = $query->result_array();

        return $result[0]['cnt'];
    }

    public function getPreqResult($qid){
        $sql = sprintf("SELECT
                            pm.year,
                            pm.class_no,
                            r.class_name,
                            pm.term,
                            pi.item_title,
                            pr.sno,
                            pr.short_id,
                            v.idno,
                            v.name as first_name,
                            pr.content,
                            pr.create_date AS create_date,
                            pr.upd_date AS update_date
                        FROM
                            preq_result pr
                        JOIN preq_main pm ON pr.preq_id = pm.preq_id
                        JOIN preq_item pi ON pr.preq_id = pi.preq_id
                        AND pr.item_id = pi.item_id
                        JOIN `require` r ON pm. year = r. year
                        AND pm.class_no = r.class_no
                        AND pm.term = r.term
                        LEFT JOIN online_app oa ON pm. year = oa. year
                        AND pm.class_no = oa.class_no
                        AND oa.term = r.term
                        AND oa.st_no = pr.sno
                        AND substr(oa.id ,- 3) = pr.short_id
                        LEFT JOIN BS_user v ON v.idno = oa.id
                        WHERE
                            pr.preq_id = '%s'
                        ORDER BY
                            pr.sno,
                            pi.item_id",$qid);   

        $query = $this->db->query($sql);
        $result = $query->result_array();

        return $result;
    }

    public function insertRequireOnline($year,$class_no,$term,$is_assess,$is_mixed,$r_start_date,$r_end_date,$online_course_name,$hours,$teacher_name,$place,$elrid){
        $this->db->trans_start();

        $this->db->where('year',$year);
        $this->db->where('class_no',$class_no);
        $this->db->where('term',$term);
        $this->db->delete('require_online');

        if($is_assess == '1' && $is_mixed == '1'){
            for($i=0;$i<count($r_start_date);$i++){
                $this->db->set('year',$year);
                $this->db->set('class_no',$class_no);
                $this->db->set('term',$term);
                $this->db->set('class_name',$online_course_name[$i]);
                $this->db->set('elearn_id',$elrid[$i]);
                $this->db->set('hours',$hours[$i]);
                $this->db->set('createdate',date('Y-m-d H:i:s'));

                if(isset($r_start_date[$i]) && !empty($r_start_date[$i])){
                    $this->db->set('start_date',$r_start_date[$i]);
                } else {
                    $this->db->set('start_date',null);
                }

                if(isset($r_end_date[$i]) && !empty($r_end_date[$i])){
                    $this->db->set('end_date',$r_end_date[$i]);
                } else {
                    $this->db->set('end_date',null);
                }
                
                $this->db->set('teacher_name',$teacher_name[$i]);
                $this->db->set('place',$place[$i]);
                $this->db->insert('require_online');
            }
        }

        $this->db->where('year',$year);
        $this->db->where('class_no',$class_no);
        $this->db->where('term',$term);
        $this->db->delete('require_mix');

        if($is_assess == '1' && $is_mixed == '1'){
            for($i=0;$i<count($r_start_date);$i++){
                $this->db->set('year',$year);
                $this->db->set('class_no',$class_no);
                $this->db->set('term',$term);
                $this->db->set('sort',$i);
                $this->db->set('class_name',$online_course_name[$i]);

                if(isset($r_start_date[$i]) && !empty($r_start_date[$i])){
                    $this->db->set('start_date',$r_start_date[$i]);
                } else {
                    $this->db->set('start_date',null);
                }

                if(isset($r_end_date[$i]) && !empty($r_end_date[$i])){
                    $this->db->set('end_date',$r_end_date[$i]);
                } else {
                    $this->db->set('end_date',null);
                }
                
                $this->db->set('teacher_name',$teacher_name[$i]);
                $this->db->set('place',$place[$i]);
                $this->db->insert('require_mix');
            }
        }

        $this->db->trans_complete();

        if($this->db->trans_status() === TRUE){
            return true;
        } 
    }

    public function getRequireOnline($year,$class_no,$term){
        $this->db->select('*');
        $this->db->where('year',$year);
        $this->db->where('class_no',$class_no);
        $this->db->where('term',$term);
        $this->db->order_by('id');
        $query = $this->db->get('require_online');
        $result = $query->result_array();

        return $result;
    }

    public function checkIsVolunteer($year,$class_no,$term){
        $this->db->select('is_volunteer');
        $this->db->where('year',$year);
        $this->db->where('class_no',$class_no);
        $this->db->where('term',$term);
        $query = $this->db->get('require');
        $result = $query->result_array();

        if(isset($result[0]['is_volunteer']) && $result[0]['is_volunteer'] == '1'){
            return true;
        }

        return false;
    }

    public function checkCourseScheduleExist($year, $class_no, $term, $use_date, $room_id, $use_id, $teacher_id, $use_period){
        $this->db->select('count(1) cnt');
        $this->db->where('year',intval($year));
        $this->db->where('class_id',addslashes($class_no));
        $this->db->where('term',intval($term));
        $this->db->where('use_date',addslashes($use_date));
        $this->db->where('room_id',addslashes($room_id));
        $this->db->where('use_id',addslashes($use_id));
        $this->db->where('teacher_id',addslashes($teacher_id));
        $this->db->where('use_period',addslashes($use_period));

        $query = $this->db->get('room_use');
        $result = $query->result_array();

        if($result[0]['cnt'] > 0 ){
            return true;
        }

        return false;
    }
}


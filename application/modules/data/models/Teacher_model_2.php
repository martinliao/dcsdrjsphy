<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Teacher_model_2 extends MY_Model
{
    public $table = 'teacher';
    public $pk = 'id';
    public $idno = 'idno';
    public $rpno = 'rpno';

    public function __construct()
    {
        parent::__construct();

        $this->init($this->table, $this->pk);

        $this->load->model('data/course_code_model');

        $this->teacher_type = array(
            '1' => '講師',
            '2' => '助教',
        );

        $this->identity_type = array(
            '' => '',
            '1' => '個人',
            '2' => '公司行號',
            '3' => '外國人',
            '4' => '無身分證',
        );

    }

    public function getFormDefault($user=array())
    {

        $data = array_merge(array(
        	'image' => '',
            'idno' => '',
            'old_idno'=>'',
            'rpno' => '',
            'identity_type' => '3',
            'birthday' => '',
            'name' => '',
            'another_name' => '',
            'institution' => '',
            'job_title' => '',
            'education' => '',
            'major' => '',
            'zipcode' => '',
            'county' => '',
            'district' => '',
            'route' => '',
            'address' => '',
            'h_tel' => '',
            'h_tel2' => '',
            'c_tel' => '',
            'c_tel2' => '',
            'mobile' => '',
            'fax' => '',
            'email' => '',
            'email2' => '',
            'bank_code' => '',
            'bank_account' => '',
            'account_name' => '',
            'contact_person' => '',
            'contact_tel' => '',
            'experience' => '',
            'demand' => '',
            'introduction' => '',
            'teacher_type' => '',
            'hire_type' => '',
            'course' => '',
        ), $user);

        return $data;
    }

    public function getVerifyConfig()
    {
        $config = array(

            'image' => array(
                'field' => 'image',
                'label' => '圖片',
                // 'rules' => 'required',
            ),
            'idno' => array(
                'field' => 'idno',
                'label' => '身分證字號',
                'rules' => 'trim|required|exact_length[10]|alpha_numeric',
            ),
            'rpno' => array(
                'field' => 'rpno',
                'label' => '居留證號',
                'rules' => 'trim|exact_length[10]|alpha_numeric|is_unique[teacher.rpno]',
            ),
            'identity_type' => array(
                'field' => 'identity_type',
                'label' => '身分別',
                'rules' => 'trim|required',
            ),
            'birthday' => array(
                'field' => 'birthday',
                'label' => '生日',
                'rules' => 'trim|required|valid_date',
            ),
            'name' => array(
                'field' => 'name',
                'label' => '姓名',
                'rules' => 'trim|required',
            ),
            'institution' => array(
                'field' => 'institution',
                'label' => '任職機關',
                'rules' => 'trim|required',
            ),
            'job_title' => array(
                'field' => 'job_title',
                'label' => '職稱',
                'rules' => 'trim|required',
            ),
            'education' => array(
                'field' => 'education',
                'label' => '學歷',
                'rules' => 'trim|required',
            ),
            'zipcode' => array(
                'field' => 'zipcode',
                'label' => '縣市 / 區域 / 郵遞區號',
                'rules' => 'trim|required|validate_zipcode',
            ),
            'route' => array(
                'field' => 'route',
                'label' => '詳細地址',
                'rules' => 'trim|required',
            ),
            'email' => array(
                'field' => 'email',
                'label' => 'EMail',
                'rules' => 'trim|valid_email',
            ),
            'email2' => array(
                'field' => 'email2',
                'label' => 'EMail2',
                'rules' => 'trim|valid_email',
            ),
            'bank_code' => array(
                'field' => 'bank_code',
                'label' => '銀行代碼',
                'rules' => 'trim|required',
            ),
            'bank_account' => array(
                'field' => 'bank_account',
                'label' => '帳號',
                'rules' => 'trim|required|validate_bank',
            ),
            'account_name' => array(
                'field' => 'account_name',
                'label' => '帳戶名稱',
                'rules' => 'trim|required',
            ),
            'teacher_type' => array(
                'field' => 'teacher_type',
                'label' => '講師或助教',
                'rules' => 'trim|required',
            ),
            'hire_type' => array(
                'field' => 'hire_type',
                'label' => '聘請類別',
                'rules' => 'trim|required',
            ),

        );

        return $config;
    }

    public function getList($attrs=array())
    {

        $params = array(
            'select' => '',
            'order_by' => 'id asc',
        );
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
        //like name idno old_idno identity_type
        $date_like = array();
        if (isset($attrs['identity_type'])) {
            $like_id_type = array(
                array('field' => 'identity_type', 'value'=>$attrs['identity_type'], 'position'=>'both')
            );
            $date_like = array_merge($date_like, $like_id_type);
        }
       //like name idno
        if (isset($attrs['idno'])) {
            $like_idno = array(
                array('field' => 'idno', 'value'=>$attrs['idno'], 'position'=>'both')
            );
            $date_like = array_merge($date_like, $like_idno);
        }
        if (isset($attrs['old_idno'])) {
            $like_old_idno = array(
                array('field' => 'old_idno', 'value'=>$attrs['old_idno'], 'position'=>'both')
            );
            $date_like = array_merge($date_like, $like_old_idno);
        }
        if (isset($attrs['rpno'])) {
            $like_rpno = array(
                array('field' => 'rpno', 'value'=>$attrs['rpno'], 'position'=>'both')
            );
            $date_like = array_merge($date_like, $like_rpno);
        }
        if (isset($attrs['name'])) {
            $like_name = array(
                array('field' => 'name', 'value'=>$attrs['name'], 'position'=>'both')
            );
            $date_like = array_merge($date_like, $like_name);
        }
        if (isset($date_like)) {
            $params['like'] = array(
                'many' => TRUE,
                'data' => $date_like,
            );
        }

        if (isset($attrs['queryFile'])){
            $search_file_sql = $this->db->select("id teacher_id")
                                        ->distinct()
                                        ->from("upload_file")
                                        ->where("title LIKE", "%{$attrs['queryFile']}%")
                                        ->or_where("SUBSTRING_INDEX(file_path, '/', -1) LIKE", "%{$attrs['queryFile']}%")
                                        ->get_compiled_select();
            $params['join'] = [
                [
                    "join_type" => "",
                    "table" => "({$search_file_sql}) f",
                    "condition" => "f.teacher_id = teacher.idno"
                ]
            ];

        }
        //like_course_name
        if (isset($attrs['course_name'])) {
            $course_id = $this->course_code_model->ge_course_by_name($attrs['course_name']);
            //jd($course_id);
            if(!empty($course_id)){
                $course_list = array();
                foreach($course_id as $c_id){
                    $course_list[] = $c_id;
                }
                $teacher_id = $this->get_teacher_id($course_list);
            }
            if(isset($teacher_id)){
	        	if($teacher_id != 'nothing'){
	        		$params['where_in'] = array(
			            'field' => 'idno',
			            'value' => $teacher_id,
			        );
	        	}
	        }
        }
        $data = $this->getData($params);

        // jd($this->db->last_query());
        foreach($data as & $course){
            $course['course'] = $this->getCanteach($course['idno'], $course['teacher_type']);
        }

        foreach($data as & $row){
            $row['course_lis'] = $this->course_code_model->ge_by_item_id($row['course']);
        }

        return $data;
    }
    public function getCanteach($id=NULL, $teacher_type)
    {
        $attrs['where_in'] = array(
            'field' => 'idno',
            'value' => $id,
        );
        $data = $this->getData($attrs);
        $course=array();
        $string = '';
        $this->db->where('id', $id);
        $this->db->where('type', $teacher_type);
        $this->db->select('course_code');
        $query = $this->db->get('canteach');
        $result = $query->result_array();
        foreach($result as $key)
        {
            $temp=$key['course_code'];
            array_push($course,$temp);
            
        }
        if(count($course) == 1){
            $string=$course[0].',';
        } else {
            $string=implode(",",$course);
        }
        
        return $data[0]['course']=$string;
    }

    /*

    */

    public function _get($id, $key = 'id')
    {
        $data = $this->get([$key => $id]);
        $this->db->select('idno');
        $this->db->where($key,$id);
        $query = $this->db->get('teacher');
        $temp_idno = $query->result_array();
        $idno = $temp_idno[0]['idno'];

        $course = $this->getCanteach($idno, $data['teacher_type']);
        $data['course'] = $course;
        $data['image_src'] = HTTP_MEDIA . $data['image'];
        $data['image_thumb_src'] = str_replace('teacher/', 'teacher/thumb/', $data['image_src']);

        $data['course_lis'] = $this->course_code_model->ge_by_item_id($data['course']);
        return $data;
    }





    public function getListCount($attrs=array())
    {
        $params = array(
            'conditions' => $attrs['conditions'],
        );

        if (isset($attrs['idno'])) {
            $params['idno'] = $attrs['idno'];
        }
        if (isset($attrs['identity_type'])) {
            $params['identity_type'] = $attrs['identity_type'];
        }
        if (isset($attrs['rpno'])) {
            $params['rpno'] = $attrs['rpno'];
        }
        if (isset($attrs['name'])) {
            $params['name'] = $attrs['name'];
        }
         if (isset($attrs['course_name'])) {
            $params['course_name'] = $attrs['course_name'];
        }
        if (isset($attrs['queryFile'])) {
            $params['queryFile'] = $attrs['queryFile'];
        }

        if(empty($attrs['course_name']) && empty($attrs['name']) && empty($attrs['idno']) && empty($attrs['conditions'])){
            $data = $this->getCount();
        }else{
            $data = $this->getList($params);
            $data = count($data);
        }
        return $data;
    }

    public function getChoices($conditions=array())
    {
        $data = array();
        $users = $this->getList();
        foreach ($users as $user) {
            $data[$user['id']] = $user['name'];
        }

        return $data;
    }

    public function getBureau(){
        $data = array();
        $this->db->select('id,name');
        $this->db->from('bureau');
        $query = $this->db->get();
        $bureau = $query->result_array();

        foreach ($bureau as $key) {
            $data[$key['id']] = $key['name'];
        }

        return $data;
    }

    public function get_teacher_id($course_list=array()){
        $data = array();
        
        $params = array(
            'select' => 'id',
            'order_by' => 'id asc',
        );
        $params['table'] = 'canteach';
        // $params['or_like'] = array(
        //         'many' => TRUE,
        //         'data' => $course_list,
        //     );
        $params['where_in'] = array(
                        'field' => 'course_code',
                        'value' => $course_list,
                    );
        $teacher_id = $this->getData($params);

        if(!empty($teacher_id)){
        	foreach ($teacher_id as $row) {
	            $data[] = $row['id'];
	        }
	        return $data;
        }else{
        	return $data = 'nothing';
        }

    }

    public function getEducation(){
        $data = array();
        $this->db->select('item_id,name');
        $this->db->from('education');
        $query = $this->db->get();
        $education = $query->result_array();

        foreach ($education as $key) {
            $data[$key['item_id']] = $key['name'];
        }

        return $data;
    }

    public function get_city($zipcode){
        if(in_array(strlen($zipcode), [5, 6])){
            $zipcode = substr($zipcode, 0,3);
        }

        $data = array();
        $this->db->select('co_subcity.city, co_city.city_name, co_subcity.subcity, co_subcity.subcity_name');
        $this->db->from('co_subcity');
        $this->db->join('co_city', 'co_subcity.city = co_city.city');
        $this->db->where('co_subcity.subcity',$zipcode);
        $query = $this->db->get();
        $data = $query->row_array();

        return $data;
    }

    public function _insert($fields=array())
    {
        unset($fields['course']);/*don't write course colum into teacher table*/
        $this->insert($fields, 'date_added');
        return true;
    }

    public function _update($pk, $fields=array())
    {
        unset($fields['course']);
        return parent::update($pk, $fields);
    }
    public function updateAll($idno,$fields)
    {
        // Alex Chiou 2021-06-25
        // 更新所有 teacher 關聯資料表的外國人老師身分證字號
        $this->db->trans_start();
        //更新 canteach 資料表
        $canteach_sql = "INSERT INTO `canteach` (`id`, `course_code`, `type`, `cre_user`,`cre_date`,`upd_user`,`upd_date`,`del_flag`) SELECT '{$idno}', `course_code`, `type`, `cre_user`,`cre_date`,`upd_user`,`upd_date`,`del_flag` FROM `canteach` AS Table_B WHERE Table_B.id = '{$fields['old_idno']}'";
        //var_dump($canteach_sql);die();
        $this->db->query($canteach_sql); 

        //更新 courseteacher 資料表
        $courseteacher_sql = "INSERT INTO `courseteacher` (`year`, `class_no`, `term`, `course_code`,`teacher_id`,`isevaluate`,`cre_user`,`cre_dte`,`upd_user`,`upd_date`,`assess_date`,`use_date`,`co_sync2epa`,`inside`,`assess_date_end`) SELECT `year`, `class_no`, `term`, `course_code`,'{$idno}',`isevaluate`,`cre_user`,`cre_dte`,`upd_user`,`upd_date`,`assess_date`,`use_date`,`co_sync2epa`,`inside`,`assess_date_end` FROM `courseteacher` AS Table_B WHERE Table_B.teacher_id = '{$fields['old_idno']}'";
        //var_dump($courseteacher_sql);die();
        $this->db->query($courseteacher_sql); 

         //更新 room_use 資料表
         $room_use_sql = "INSERT INTO `room_use` (`appi_id`, `room_id`, `num`, `expense`,`use_date`,`use_period`,`use_type`,`year`,`class_id`,`term`,`use_id`,`teacher_id`,`obj`,`bgcolor`,`isteacher`,`course_code`,`status`,`cat_id`,`unit`,`title`,`hrs`,`discount`,`groupnum`,`groupnote`,`sort`)";
         $room_use_sql .= " SELECT `appi_id`, `room_id`, `num`, `expense`,`use_date`,`use_period`,`use_type`,`year`,`class_id`,`term`,`use_id`,'{$idno}',`obj`,`bgcolor`,`isteacher`,`course_code`,`status`,`cat_id`,`unit`,`title`,`hrs`,`discount`,`groupnum`,`groupnote`,`sort` ";
         $room_use_sql .= " FROM `room_use` AS Table_B WHERE Table_B.teacher_id = '{$fields['old_idno']}'";
         //var_dump($room_use_sql);die();
         $this->db->query($room_use_sql); 
 

       
        //更新老師資料表
        unset($fields["course"]);
        unset($fields["teacher_type"]);
        unset($fields["hire_type"]);
        unset($fields["teacher"]);
        unset($fields["identity_type"]);
        $this->db->where("idno",$idno);
        $this->db->update("teacher",$fields);
        


        $this->db->trans_complete();
    }

    public function _update_or_create($conditions, $fields=array())
    {
        unset($fields['course']);
        return $this->update_or_create($conditions, $fields);
    }

    /*update canteach table value */
    public function canteach_update($post)
    {
        $this->load->helper('date');
        $this->db->select('id');
        $this->db->get('canteach');
        $this->db->trans_start();
        $this->db->delete('canteach',array('id'=>$post['idno'],'type'=>$post['teacher_type']));
        $course=explode(",", $post['course']);
        for ($i=0;$i<count($course);$i++) {
            if(!empty($course[$i])){
                $this->db->set('id', $post['idno']);
                $this->db->set('type', $post['teacher_type']);
                $this->db->set('upd_date', 'NOW()', false);
                $this->db->set('course_code', $course[$i]);
                $this->db->set('upd_user', $post['name']);
                $this->db->insert('canteach');
            }
        }
        $this->db->trans_complete();
    }
    public function canteach_delete($post)
    {
        $this->db->trans_start();
        foreach ($post['rowid'] as $id) {
            $this->db->select('idno');
            $this->db->where('id',$id);
            $query = $this->db->get('teacher');
            $temp = $query->result_array();
        }
        foreach($temp as $key){
            $this->db->where('id', $key['idno']);
            $this->db->delete('canteach');
        }
       return;
       $this->db->trans_complete();
    }

    public function getTeacherByType($idno, $teacher_type){
        $this->db->select("*")
                 ->from("teacher")
                 ->where("idno", $idno)
                 ->where('teacher_type', $teacher_type)
                 ->where('del_flag', 'N');

        $query = $this->db->get();
        return $query->result();
    }

    public function getTeacherIdByIdno($idno){
        $this->db->select("id")
                 ->from("teacher")
                 ->where("idno", $idno)
                 ->order_by('teacher_type');

        $query = $this->db->get();
        $result = $query->result_array();
        
        if(count($result) > 0){
            return $result[0]['id'];
        }

        return -1; 
    }


    public function getAutoID($id_type){

        switch($id_type){
            case "2":
                $like = 'COM';
                $min = '0000001';
                $left_num = 7;
                $ptn = '/^[C][O][M][0-9]{7}+$/';
                break;
            case "3":
                $like = 'FOR';
                $min = '0000001';
                $left_num = 7;
                $ptn = '/^[F][O][R][0-9]{7}$/';
                break;
            case "4":
                $like = 'NO';
                $min = '00000001';
                $left_num = 8;
                $ptn = '/^[N][O][0-9]{8}$/';
                break;
        }

        $this->db->select('idno');
        $this->db->from($this->table);
        $this->db->where('identity_type',$id_type);
        $this->db->like('idno',$like, 'right');
        $this->db->order_by('idno');
        $query = $this->db->get();
        $data = $query->result_array();

        foreach($data as $row){
            if(preg_match($ptn, $row['idno'])){
                $maxID = $row['idno'];
            }
        }
        if($maxID == ''){
            $autoID = $like . $min;
        }else{
            // 解析目前DB中最大的流水號
            $num = intval(str_replace($like, '', $maxID));
            $max_num = $num + 1;
            $auto_num = str_pad($max_num, $left_num, "0", STR_PAD_LEFT);
            $autoID = $like . $auto_num;

        }

        // jd($autoID);
        return $autoID;
    }
    // 外國老師身分證更新使用 Alex 2021-06-25
    public function check_hour_traffic_tax($oldId)
    {
        $this->db->select('seq, TEACHER_ID, teacher_name, year, class_no, term, class_name, status, bill_date, entry_date');
        $this->db->from('hour_traffic_tax');
        $this->db->where("TEACHER_ID = '".$oldId."' AND (status != '已設定為不請款' or status is null) AND (bill_date is null OR entry_date is null)");
        //$this->db->where("TEACHER_ID = '".$oldId."' AND (status is not null OR entry_date is null)");
        $query = $this->db->get();
        //$query = $this->db->get();
        $result = $query->result();
        //return sizeof($result) > 0 ? false : true;
        return $result;
    }
    
    public function update_identification_related_table($table, $conditions, $data)
    {
        foreach ($conditions as $key => $item) {
            $this->db->where($key, $item);
        }
        $this->db->update($table, $data);
    }

    public function update_identification_execution($table, $column, $oldId, $newId)
    {
        $targetTable = $table;
        $conditions = [
            $column => $oldId
        ];
        $data = [$column => $newId];
        $this->update_identification_related_table($targetTable, $conditions, $data);
    }

    public function update_teacher_auth($oldId, $newId)
    {
        $this->update_identification_execution('teacher_auth', 'teacher_id', $oldId, $newId);
    }

    public function update_canteach($oldId, $newId)
    {
        $this->update_identification_execution('canteach', 'id', $oldId, $newId);
    }

    public function update_room_use($oldId, $newId)
    {
        $this->update_identification_execution('room_use', 'teacher_id', $oldId, $newId);
    }

    public function update_hour_traffic_tax($oldId, $newId)
    {
        $this->update_identification_execution('hour_traffic_tax', 'TEACHER_ID', $oldId, $newId);
    }

    public function update_courseteacher($oldId, $newId)
    {
        $this->update_identification_execution('courseteacher', 'teacher_id', $oldId, $newId);
    }

    public function update_SV_ClassManagementForm($oldId, $newId)
    {
        $this->update_identification_execution('SV_classManagementForm', 'teacher', $oldId, $newId);
    }

    public function update_teacher_dining($oldId, $newId)
    {
        $this->update_identification_execution('teacher_dining', 'idno', $oldId, $newId);
    }

    public function update_user_modify_log($oldId, $newId)
    {
        $this->update_identification_execution('user_modify_log', 'idno', $oldId, $newId);
    }

    public function update_hour_bill($oldId, $newId)
    {
        $this->update_identification_execution('hour_bill', 'teacher_id', $oldId, $newId);
    }

    public function update_handouts_status($oldId, $newId)
    {
        $this->update_identification_execution('handouts_status', 'teacher_id', $oldId, $newId);
    }
}



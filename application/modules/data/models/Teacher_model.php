<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Teacher_model extends MY_Model
{
    public $table = 'teacher';
    public $pk = 'id';

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
            'rpno' => '',
            'identity_type' => '',
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
        //like name idno
        $date_like = array();
        if (isset($attrs['idno'])) {
            $like_idno = array(
                array('field' => 'idno', 'value'=>$attrs['idno'], 'position'=>'both')
            );
            $date_like = array_merge($date_like, $like_idno);
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
        unset($fields["course"]);
        unset($fields["teacher_type"]);
        unset($fields["hire_type"]);
        unset($fields["teacher"]);
        unset($fields["identity_type"]);
        $this->db->where("idno",$idno);
        $this->db->update("teacher",$fields);
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
        $this->db->delete('canteach',
            array(
                'id'=>addslashes($post['idno']),
                'type'=>addslashes($post['teacher_type'])
            )
        );
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

}



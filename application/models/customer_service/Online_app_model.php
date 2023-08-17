<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Online_app_model extends MY_Model
{
    public $table = 'online_app';
    public $pk = '';

    public function __construct()
    {
        parent::__construct();

        $this->init($this->table, $this->pk);

    }
    public function getListCount($attrs=array())
    {

        $data = $this->getList($attrs);
        return count($data);
    }
    public function getList($conditions=array())
    {

        $params = array(
            'select' => '',
            'order_by' => 'insert_order asc',
        );
        if (isset($conditions['order_by'])) {
            $params['order_by'] = $conditions['order_by'];
            unset($conditions['order_by']);
        }
        if (isset($conditions['where_special'])) {
            $params['where_special'] = $conditions['where_special'];
            unset($conditions['where_special']);
        }
        if (isset($conditions)) {
            $params['conditions'] = $conditions;
        }
        $data = $this->getData($params);
        
        foreach ($data as & $row) {
            $conditions = array(
                'idno' => $row['id'],
            );
            $person = $this->user_model->_get($conditions);

            $this->db->select("ou_gov");
            $this->db->from("out_gov");
            $this->db->where("id", $row['id']);
            $query = $this->db->get();
            $ou_gov_data = $query->row_array();
            
            $row['beaurau_name'] = $person['bureau'];
            $row['name'] = $person['name'];
            $row['birthday'] = $person['birthday'];
            $row['title'] = $person['job_title_name'];
            $row['phydisabled'] = $person['phydisabled'];
            $row['sex'] = $person['gender'];

        }
        //var_dump($data);

        return $data;
    }
    public function getMemberCount($attrs=array())
    {

        $data = $this->getMemberClassData($attrs);
        return count($data);
    }
    public function getMemberClassData($attrs=array())  //取得上課資料array
    {
        $this->db->select("year,class_no,term,yn_sel");
        $this->db->from($this->table);
        if($attrs['conditions']['year'])  $this->db->where("year", $attrs['conditions']['year']);

        if(!is_null($attrs['term']) && $attrs['term']!=='')  $this->db->where("term", $attrs['term']);

        if(!is_null($attrs['class_no']) && $attrs['class_no']!=='')  $this->db->like("class_no", $attrs['class_no']);

        if(count($attrs['id'])>0)  $this->db->where_in("id", $attrs['id']);
        $this->db->order_by('year','class_no','term');
        if (isset($attrs['rows']) && isset($attrs['offset']) ){
            $this->db->limit($attrs['rows'], $attrs['offset']);
        } elseif (isset($attrs['rows']) ) {
            $this->db->limit($attrs['rows']);
        }
        $query = $this->db->get();
        $data = $query->result_array();

        return $data;
    }
    public function getMemberId($conditions=array(),$select=NULL)  //取得身分證字號array
    {   if(is_null($select)){
            $this->db->select("id");
        }else{
            $this->db->select($select);
        }
        $this->db->from($this->table);
        $this->db->where("year", $conditions['year']);
        $this->db->where("class_no", $conditions['class_no']);
        $this->db->where("term", $conditions['term']);
        //$this->db->order_by('st_no','asc');
        if(isset($conditions['yn_sel']) ){
            $this->db->where_in("yn_sel", $conditions['yn_sel']);
        }
        $query = $this->db->get();
        $data = $query->result_array(); //get id
        $this->db->order_by('id', 'DESC');
        
        return $data;
    }

    public function getBureau($bureau_id)
    {
        $this->db->select("name");
        $this->db->from('bureau');
        $this->db->where("bureau_id", $bureau_id);
        $query = $this->db->get();
        $data = $query->row_array();
        return $data['name'];
    }
    public function getMaxOrder($conditions=array())
    {
    	$this->db->select("IFNULL(max(insert_order)+1,1) max_order");
        $this->db->from($this->table);
        $this->db->where("year", $conditions['year']);
        $this->db->where("class_no", $conditions['class_no']);
        $this->db->where("term", $conditions['term']);
        $this->db->where_not_in("yn_sel", array('6'));
        $query = $this->db->get();
        $data = $query->row_array();

        return $data['max_order'];
    }

    public function getInsertOrder($conditions=array())
    {
        $this->db->select("IFNULL(max(insert_order)+1,1) max_order");
        $this->db->from($this->table);
        $this->db->where("year", $conditions['year']);
        $this->db->where("class_no", $conditions['class_no']);
        $this->db->where("term", $conditions['term']);
        $this->db->where("beaurau_id", $conditions['bureau_id']);
        $this->db->where_not_in("yn_sel", array('6'));
        $query = $this->db->get();
        $data = $query->row_array();

        return $data['max_order'];
    }

    public function get_st_no($conditions=array())
    {
        $this->db->select("IFNULL(max(st_no)+1,1) mx");
        $this->db->from($this->table);
        $this->db->where("year", $conditions['year']);
        $this->db->where("class_no", $conditions['class_no']);
        $this->db->where("term", $conditions['term']);
        $query = $this->db->get();
        $data = $query->row_array();

        return $data['mx'];
    }

    public function getDel($conditions=array()) {
        $this->db->from($this->table);
        $this->db->where("id", $conditions['id']);
        $this->db->where("year", $conditions['year']);
        $this->db->where("class_no", $conditions['class_no']);
        $this->db->where("term", $conditions['term']);
        $this->db->where("(yn_sel = '6' or yn_sel = '7')");
        $regist_del = $this->db->count_all_results();
        return $regist_del;
    }

    public function getRegist($conditions=array()) {
        $this->db->from($this->table);
        $this->db->where("id", $conditions['id']);
        $this->db->where("year", $conditions['year']);
        $this->db->where("class_no", $conditions['class_no']);
        $this->db->where("term", $conditions['term']);
        $this->db->where("yn_sel != '6' and yn_sel != '7'");
        $regist = $this->db->count_all_results();
        return $regist;
    }

    public function update_order($conditions=array()) {

        $params = array(
            'select' => '',
            'order_by' => 'insert_order asc',
        );
        if (isset($conditions)) {
            $params['conditions'] = $conditions;
        }

        $data = $this->getData($params);
        unset($conditions['insert_order >']);
        foreach($data as $row){
            $fields = array(
                'insert_order' => $row['insert_order'] - 1,
            );
            $this->update($conditions, $fields);
        }

    }

    public function ckeckFactor_1($idno, $classNo, $year) {

        $this->db->select('COUNT(1) CNT');
        $this->db->from($this->table);
        $this->db->where("id", $idno);
        $this->db->where("class_no", $classNo);
        $this->db->where("yn_sel", '1');
        $this->db->where("year >=", $year);
        $query = $this->db->get();
        $data = $query->row_array();

        if($data) {
            if($data["CNT"]>0) { //N年內有修過紀錄then deny
                return false;
            }
        }
        return true;
    }

    public function ckeckFactor_2($idno, $gid, $year) {

        $this->db->select('limited, class_no');
        $this->db->from('enroll_condition_2');
        $this->db->where("group_id", $gid);
        $query = $this->db->get();
        $enroll_condition_2_data = $query->result_array();
        $counter = 0;
        $classList = array();
        foreach($enroll_condition_2_data as $row){
            array_push($classList, $row["class_no"]);
            $counter = $row["limited"];
        }
        if(count($classList)==0 || $counter==0) { //條件寬鬆pass
            return 0;
        }

        //檢查學員修課紀錄
        $this->db->select('COUNT(1) CNT');
        $this->db->from($this->table);
        $this->db->where("id", $idno);
        $this->db->where("yn_sel", '1');
        $this->db->where_in("class_no", $classList);
        $this->db->where("year", $year);
        $query = $this->db->get();
        $data = $query->row_array();

        if($data) {
            if($data["CNT"]>0) { //同年度有修過群組內課程紀錄
                return $counter;
            }
            // if(1) { //同年度有修過群組內課程紀錄
            //  return $counter;
            // }
        }
        return 0;
    }

    public function ckeckFactor_3($idno, $gid, $year) {

        $this->db->select('class_no_2, condition');
        $this->db->from('enroll_condition_3');
        $this->db->where("limit_name", $gid);
        $query = $this->db->get();
        $enroll_condition_3_data = $query->row_array();

        if($enroll_condition_3_data) {
            $classStr = "";
            $classList = explode(",", $enroll_condition_3_data["class_no_2"]);
            $sqlin = count($classList); //預設必修筆數相同
            if(strtoupper($enroll_condition_3_data["condition"])!=="IN") { //擋修筆數0
                $sqlin = 0;
            }
            //檢查學員修課紀錄
            $this->db->select('COUNT(1) CNT');
            $this->db->from($this->table);
            $this->db->where("id", $idno);
            $this->db->where("yn_sel", '1');
            $this->db->where_in("class_no", $classList);
            $this->db->where("year", $year);
            $query = $this->db->get();
            $data = $query->row_array();

            if($data) {
                if($data["CNT"]!=$sqlin) { //同年修課門檻不符
                    return $sqlin>0?3:4;
                }
            }
        }
        return 1;
    }

    public function repeat_sign($conditions=array()) {

        $this->db->from($this->table);
        $this->db->where("id", $idno);
        $this->db->where("class_no", $classNo);
        $this->db->where("year", $year);
        $this->db->where_in("yn_sel", array('1','2','3'));
        $repeat_sign = $this->db->count_all_results();

        return $repeat_sign;
    }

    public function get_regist($conditions=array()) {

        $this->db->from($this->table);
        $this->db->where("term", $conditions['term']);
        $this->db->where("class_no", $conditions['class_no']);
        $this->db->where("year", $conditions['year']);
        $this->db->where("beaurau_id", $conditions['beaurau']);
        $this->db->where_in("yn_sel", array('1','2','3','8'));
        $regist_count = $this->db->count_all_results();

        return $regist_count;
    }

    public function get_group_number($conditions=array()) {

        $this->db->select("count(distinct group_no) as count_group_no");
        $this->db->from($this->table);
        $this->db->where("term", $conditions['term']);
        $this->db->where("class_no", $conditions['class_no']);
        $this->db->where("year", $conditions['year']);
        $this->db->where_in("yn_sel", array('1','3','8'));
        $query = $this->db->get();
        $data = $query->row_array();
        return $data['count_group_no'];
    }

    public function getCurrentBureauPersonNo($conditions=array()) {
        $this->db->from($this->table);
        $this->db->join('BS_user', "{$this->table}.id=BS_user.idno", 'left');
        $this->db->where("online_app.year", $conditions['year']);
        $this->db->where("online_app.class_no", $conditions['class_no']);
        $this->db->where("online_app.term", $conditions['term']);
        $this->db->where("online_app.yn_sel in ('1', '2', '3', '8')");
        $this->db->where("BS_user.bureau_id", $conditions['beaurauId']);
        $BureauCount = $this->db->count_all_results();
        return $BureauCount;
    }

    public function checkErollmentLimit1($conditions=array()) {

        $errorMsg = '';

        $this->db->select('limit_id');
        $this->db->from('require');
        $this->db->where("year", $conditions['year']);
        $this->db->where("class_no", $conditions['class_no']);
        $this->db->where("term", $conditions['term']);
        $this->db->where("limit_id is not null");
        $query = $this->db->get();
        $data = $query->row_array();

        $this->db->select('limit_year');
        $this->db->from('enroll_condition');
        $this->db->where("id", $data['limit_id']);
        $query = $this->db->get();
        $data = $query->row_array();
        $limit_1_year = $data['limit_year'];
        if(empty($limit_1_year)){
            return $errorMsg;
        }
        if($limit_1_year == 0){
            $limit_1_year = 99;
        }

        $date_now = new DateTime('now');
        $year_now = $date_now->format('Y');
        $year = $year_now - 1911 - $limit_1_year;
        $this->db->from($this->table);
        $this->db->where("id", $conditions['id']);
        $this->db->where("class_no", $conditions['class_no']);
        $this->db->where("year >=", ($year_now - 1911 - $limit_1_year));
        $this->db->where("yn_sel not in ('2', '6', '7')");
        $this->db->where("id", $conditions['id']);
        $count_all = $this->db->count_all_results();

        if($count_all > 0){
            if($limit_1_year >= 99){
                $errorMsg = '違反參訓限制條件1-已修過則永久不得報名';
            }else{
                $errorMsg = '違反參訓限制條件1-'.$limit_1_year.'年內不得報名';
            }
            return $errorMsg;
        }
    }

    public function checkErollmentLimit2($conditions=array()) {
        $errorMsg = '';
        $this->db->select('limit_id1');
        $this->db->from('require');
        $this->db->where("year", $conditions['year']);
        $this->db->where("class_no", $conditions['class_no']);
        $this->db->where("term", $conditions['term']);
        $query = $this->db->get();
        $data = $query->row_array();

        $this->db->select('limited');
        $this->db->from('enroll_condition_2');
        $this->db->where("group_id", $data['limit_id1']);
        $query = $this->db->get();
        $enroll_condition_2 = $query->result_array();
        $classList = array();
        $counter = 0;
        foreach($enroll_condition_2 as $row){
            array_push($classList, $row["class_no"]);
            $counter = $row["limited"];
        }

        if(count($classList)>0){
            $this->db->from($this->table);
            $this->db->where("year", $conditions['year']);
            $this->db->where("yn_sel not in ('2', '6', '7')");
            $this->db->where("id", $conditions['id']);
            $this->db->where_in("class_no", $classList);
            $count_all = $this->db->count_all_results();
        }else{
            $count_all = 0;
        }
        if( $counter > 0 && $count_all > $counter){
            $errorMsg = '違反參訓限制條件2-參訓數'.$counter;
        }
        return $errorMsg;
    }

    public function checkErollmentLimit3($conditions=array()) {
        $errorMsg = '';
        $this->db->select('*');
        $this->db->from('require_limit3');
        $this->db->join('enroll_condition_3', 'require_limit3.limit_id2 = enroll_condition_3.id', 'left');
        $this->db->where("require_limit3.year", $conditions['year']);
        $this->db->where("require_limit3.class_no", $conditions['class_no']);
        $this->db->where("require_limit3.term", $conditions['term']);
        $this->db->where(" (require_limit3.limit2_start='Y' or require_limit3.limit2_start is null) ");
        $query = $this->db->get();
        $require_limit3 = $query->result_array();
        $msg = '';
        foreach($require_limit3 as $row){
            $check_class_no_list = explode(',', $fields['class_no_2']);
            if(strtoupper($row["condition"])!=="IN") {
                if($row['compare_type'] == 0){

                    $this->db->from($this->table);
                    $this->db->where("yn_sel not in ('2', '6', '7')");
                    $this->db->where("id", $conditions['id']);
                    $this->db->where_not_in("class_no",$check_class_no_list);
                    $query = $this->db->get();
                    $count_all = $this->db->count_all_results();

                    if($count_all == 0){
                        $this->db->select('distinct class_name');
                        $this->db->from('require');
                        $this->db->where_in("class_no",$check_class_no_list);
                        $query = $this->db->get();
                        $class_data = $query->result_array();
                        $class_name = array();
                        foreach($class_data as $class_row){
                            $class_name[] = $class_row['class_name'];
                        }
                        $class_name_list = implode(',', $class_name);
                        $msg = '違反參訓限制條件3_1-需修過'.$class_name_list;
                    }
                }else{

                    $this->db->from($this->table);
                    $this->db->where("yn_sel not in ('2', '6', '7')");
                    $this->db->where("id", $conditions['id']);
                    $this->db->where_in("class_no",$check_class_no_list);
                    $query = $this->db->get();
                    $count_all = $this->db->count_all_results();

                    if($count_all == 0){
                        $this->db->select('distinct class_name');
                        $this->db->from('require');
                        $this->db->where_in("class_no",$check_class_no_list);
                        $query = $this->db->get();
                        $class_data = $query->result_array();
                        $class_name = array();
                        foreach($class_data as $class_row){
                            $class_name[] = $class_row['class_name'];
                        }
                        $class_name_list = implode(',', $class_name);
                        $msg = '違反參訓限制條件3_2-需修過'.$class_name_list;
                    }

                }
            }else{
                if($row['compare_type'] == 0){
                    $this->db->from($this->table);
                    $this->db->where("yn_sel not in ('2', '6', '7')");
                    $this->db->where("id", $conditions['id']);
                    $this->db->where_in("class_no",$check_class_no_list);
                    $query = $this->db->get();
                    $count_all = $this->db->count_all_results();

                    if($count_all > 0){
                        $this->db->select('distinct class_name');
                        $this->db->from('require');
                        $this->db->where_in("class_no",$check_class_no_list);
                        $query = $this->db->get();
                        $class_data = $query->result_array();
                        $class_name = array();
                        foreach($class_data as $class_row){
                            $class_name[] = $class_row['class_name'];
                        }
                        $class_name_list = implode(',', $class_name);
                        $msg = '違反參訓限制條件3_3-已修過'.$class_name_list;
                    }

                }else{
                    $this->db->from($this->table);
                    $this->db->where("yn_sel not in ('2', '6', '7')");
                    $this->db->where("id", $conditions['id']);
                    $this->db->where_in("class_no",$check_class_no_list);
                    $query = $this->db->get();
                    $count_all = $this->db->count_all_results();

                    if($count_all > 0){
                        $this->db->select('distinct class_name');
                        $this->db->from('require');
                        $this->db->where_in("class_no",$check_class_no_list);
                        $query = $this->db->get();
                        $class_data = $query->result_array();
                        $class_name = array();
                        foreach($class_data as $class_row){
                            $class_name[] = $class_row['class_name'];
                        }
                        $class_name_list = implode(',', $class_name);
                        $msg = '違反參訓限制條件3_4-已修過'.$class_name_list;
                    }
                }
            }
            if(!empty($msg)){
                $errorMsg .= $msg .'<br>';
            }
        }
        return $errorMsg;
    }

}
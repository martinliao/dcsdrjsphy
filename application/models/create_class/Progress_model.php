<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Progress_model extends MY_Model
{
    public $table = 'require_list';
    public $pk = '';

    public function __construct()
    {
        parent::__construct();

        $this->init($this->table, $this->pk);

    }
    public function resetCancel($year,$class_no,$term)
    {
        $this->db->where('year',$year);
        $this->db->where('class_no',$class_no);
        $this->db->where('term',$term);
        $is_cancel=array('is_cancel'=>0);
        return $this->db->update('require',$is_cancel);
    }  //2021-0604, 9A原"已取消開班"恢復開班時'is_cancel'=>NULL, 造成9L無法正常判斷該課程已經不取消。故改為0

    public function getListCount($attrs=array())
    {

        $data = $this->getList($attrs);
        return count($data);
    }

    public function getList($attrs=array())
    {
        // $this->db->cache_on();
    	$params = array(
            'select' => '*, date_format(start_date1, "%Y-%m-%d") start_date1_format, date_format(end_date1, "%Y-%m-%d") end_date1_format',
            'order_by'=>'type asc,year,start_date1 asc',
        );
    	if($attrs['sort'] == '1'){
    		$params['order_by'] = 'type, start_date1, class_no, term';
    	}else{
    		$params['order_by'] = 'type, class_no, start_date1, term';
    	}

        $params['join'] = array(
                array(
                        'table' => '(SELECT seq_no, type, is_cancel ,contactor, is_assess, is_mixed from `require` ) as require_b',
                        'condition'=>'require_b.seq_no = require_list.seq_no',
                        'join_type'=>'left',
                    ),
               
                array(
                    'table' => 'BS_user as user',
                    'condition'=>'user.idno = require_list.worker',
                    'join_type'=>'left',
                ),                    
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

        // if (isset($params['conditions']['worker'])) {
        //     $params['worker'] = $attrs['worker'];
        // }        

        // if (isset($attrs['q'])) {
        //     $params['like'] = array(
        //         'many' => TRUE,
        //         'data' => array(
        //             array('field' => 'second_category.name', 'value'=>$attrs['q'], 'position'=>'both')
        //         )
        //     );
        // }

        // if (isset($attrs['item_id'])) {
        //     $params['like'] = array(
        //         'many' => TRUE,
        //         'data' => array(
        //             array('field' => 'second_category.item_id', 'value'=>$attrs['item_id'], 'position'=>'both')
        //         )
        //     );
        // }

        $date_like = array();
        if (isset($attrs['class_no'])) {
            $like_class_no = array(
                array('field' => 'require_list.class_no', 'value'=>$attrs['class_no'], 'position'=>'both')
            );
            $date_like = array_merge($date_like, $like_class_no);
        }
        if (isset($attrs['class_name'])) {
            $like_class_name = array(
                array('field' => 'require_list.class_name', 'value'=>$attrs['class_name'], 'position'=>'both')
            );
            $date_like = array_merge($date_like, $like_class_name);
        }


        if (isset($date_like)) {
            $params['like'] = array(
                'many' => TRUE,
                'data' => $date_like,
            );
        }

        $data = $this->getData($params);
    

        // jd($this->db->last_query());
        return $data;
    }

    /*
        取得課程資訊
    */

    public function getRequire($params){
        $this->db->select("r.*, 
                           CASE WHEN user.office_email IS NULL OR user.office_email = '' 
                               THEN user.email 
                               ELSE user.office_email 
                           END worker_email,
                           code_table.description")
                 ->from("require r")
                 ->join("BS_user user", "user.idno = r.worker")
                 ->join('code_table', 'TYPE_ID = "07" and ITEM_ID = r.ht_class_type', 'left')
                 ->where("r.class_no", $params['class_no'])
                 ->where("r.year", $params['year'])
                 ->where("r.term", $params['term']);
        $query = $this->db->get();
        return $query->row();
    }

    public function checkWaitConfirm($year,$class_no,$term){
        $this->db->select("count(1) cnt");
        $this->db->from("hour_traffic_tax");
        $this->db->where("class_no", $class_no);
        $this->db->where("year", $year);
        $this->db->where("term", $term);
        $this->db->where("status", '待確認');

        $query = $this->db->get();
        return $query->result_array();
    }

    public function checkAppSeq($year,$class_no,$term){
        $this->db->select("count(1) cnt");
        $this->db->from("hour_traffic_tax hour_traffic_tax");
        $this->db->join("hour_app hour_app", "hour_traffic_tax.seq = hour_app.seq");
        $this->db->where("hour_traffic_tax.class_no", $class_no);
        $this->db->where("hour_traffic_tax.year", $year);
        $this->db->where("hour_traffic_tax.term", $term);
        $this->db->where("hour_app.del_flag is null");

        $query = $this->db->get();
        return $query->result_array();
    }

    public function delBookingRoom($year,$class_no,$term){
        $this->db->where("class_no", $class_no);
        $this->db->where("year", $year);
        $this->db->where("term", $term);

        if($this->db->delete("booking_place")){
            return true;
        }
   
        return false;
    }

    public function updateRoomToBlank($year,$class_no,$term){
        $this->db->set('room_id', 4449);
        $this->db->where("class_no", $class_no);
        $this->db->where("year", $year);
        $this->db->where("term", $term);
        $result = $this->db->update("periodtime");

        $this->db->set('room_id', 4449);
        $this->db->where("class_id", $class_no);
        $this->db->where("year", $year);
        $this->db->where("term", $term);
        $result2 = $this->db->update("room_use");

        if($result && $result2){
            return true;
        }

        return false;
    }

    /*
        取得報名學員資料
    */
    public function getStudent($params, $bureau_id = null){
        $select = "user.idno, user.name, user.bureau_id, trim(user.email) email, trim(user.office_email) office_email,user.co_empdb_poftel, user.office_tel, oa.class_no, oa.st_no, r.year, r.term, r.class_name, r.reason, bureau.name bureau_name, CASE oa.yn_sel WHEN '4' THEN '退訓' WHEN '5' THEN '未報到' ELSE '' END yn_sel";

        $this->db->select($select)
                 ->from("online_app oa")
                 ->join("BS_user user", "user.idno = oa.id")
                 ->join("require r", "r.year = oa.year AND r.term = oa.term AND r.class_no = oa.class_no", 'left')
                 ->join("bureau", "bureau.bureau_id = oa.beaurau_id", 'left')
                 //->where("oa.yn_sel not in ('2', '6', '7')")
                 ->where("oa.yn_sel not in ('2','6','7')")
                 ->where("oa.year", $params['year'])
                 ->where("oa.term", $params['term'])
                 ->where("oa.class_no", $params['class_no'])
                 ->order_by("oa.st_no");

        if ($bureau_id != null){
            $this->db->where("oa.beaurau_id", $bureau_id);
        }

        $query = $this->db->get();

         
        
        return $query->result();
    }

    public function getTeacher($class_no, $year, $term){
        $select = "r.class_name, r.year, r.term, r.class_no, trim(t.email) email1, trim(t.email2) email2, t.name teacher_name, t.id t_id, r.reason";
        $this->db->select($select)
                 ->distinct()
                 ->from("require r")
                 ->join("room_use ru", "ru.year = r.year AND ru.term = r.term AND ru.class_id = r.class_no", 'left')
                 ->join("teacher t", "t.idno = ru.teacher_id")
                 ->where("r.class_no", $class_no)
                 ->where("r.year", $year)
                 ->where("r.term", $term)
                 //->where("isteacher = 'Y'");
                 ->group_by("r.year,r.class_no,r.term,teacher_name");
                 
                 
        $query = $this->db->get();
        // dd($this->db->last_query());
        
        return $query->result();
    }

    public function getPersonnel($class_no, $year, $term){
        $select = "";
        $sub = $this->db->select("user.bureau_id")
                        ->distinct()
                        ->from("require r")
                        ->join("online_app oa", "oa.year = r.year AND oa.class_no = r.class_no AND oa.term = r.term", "left")
                        ->join("BS_user user", "user.idno = oa.id", "left")
                        ->where("oa.yn_sel not in ('2', '6', '7')")
                        ->where("r.class_no", $class_no)
                        ->where("r.year", $year)
                        ->where("r.term", $term)
                        ->get_compiled_select();

        $this->db->select("user.name, user.office_tel, user.email, user.co_empdb_email office_email, bureau.name bureau_name")
                 ->from("BS_user user")
                 ->join("bureau bureau", "bureau.bureau_id = user.bureau_id", "left")
                 ->where("user.bureau_id IN ($sub)")
                 ->where("user.enable",1)    //20210616 去除不在使用的帳號
                 ->where("user.username LIKE", 'edap%'); //因為沒資料所以暫時註解掉測試其他功能中
        $query = $this->db->get();
        return $query->result();
    }

    public function getPersonnelByLeave($class_no, $year, $term){
        $select = "";
        $sub = $this->db->select("user.bureau_id")
                        ->distinct()
                        ->from("require r")
                        ->join("online_app oa", "oa.year = r.year AND oa.class_no = r.class_no AND oa.term = r.term", "left")
                        ->join("BS_user user", "user.idno = oa.id", "left")
                        ->join('s_vacation sv', 'sv.year = oa.year AND sv.class_no = oa.class_no AND sv.term = oa.term AND sv.va_code is not null AND sv.id = oa.id', 'left')
                        ->where("r.class_no", $class_no)
                        ->where("r.year", $year)
                        ->where("r.term", $term)
                        ->where("(oa.yn_sel in ('4', '5') OR (oa.yn_sel in ('3','8','1') AND sv.va_code in ('01', '02', '03')))")
                        ->get_compiled_select();

        $this->db->select("user.username, user.name, user.office_tel, user.email2, user.co_empdb_email office_email, bureau.name bureau_name")
                 ->from("BS_user user")
                 ->join("bureau bureau", "bureau.bureau_id = user.bureau_id", "left")
                 ->where("user.bureau_id IN ($sub)")
                 ->where("user.enable",1)
                 ->where("user.username LIKE", 'edap%'); //因為沒資料所以暫時註解掉測試其他功能中
        $query = $this->db->get();
        return $query->result();
    }

    public function getNoPass($class_no, $year, $term){

        $select = "user.name, ifnull(user.co_empdb_poftel,office_tel) as telephone, trim(user.email) email, trim(user.office_email) office_email, bureau.name bureau_name";

        $stundent_query = $this->db->select($select)
                 ->from("online_app oa")
                 ->join("BS_user user", "user.idno = oa.id")
                 ->join("bureau", "bureau.bureau_id = oa.beaurau_id")
                 ->where("oa.yn_sel", 2)
                 ->where("oa.class_no", $class_no)
                 ->where("oa.year", $year)
                 ->where("oa.term", $term)
                 ->get_compiled_select();

        $this->db->reset_query();

        $sub = $this->db->select("user.bureau_id")
                        ->distinct()
                        ->from("require r")
                        ->join("online_app oa", "oa.year = r.year AND oa.class_no = r.class_no AND oa.term = r.term", "left")
                        ->join("BS_user user", "user.idno = oa.id", "left")
                        ->where("oa.yn_sel in (2)")
                        ->where("r.class_no", $class_no)
                        ->where("r.year", $year)
                        ->where("r.term", $term)
                        ->get_compiled_select();

        $this->db->reset_query();

        $personnel_query = $this->db->select("user.name, ifnull(user.co_empdb_poftel,office_tel) as telephone, trim(user.email) email, trim(user.office_email) office_email, bureau.name bureau_name")
                                    ->from("BS_user user")
                                    ->join("bureau bureau", "bureau.bureau_id = user.bureau_id", "left")
                                    ->where("user.bureau_id IN ($sub)")
                                    ->where("user.username LIKE", 'edap%') //因為沒資料所以暫時註解掉測試其他功能中
                                    ->get_compiled_select();
        $this->db->reset_query();

        $query = $this->db->query($stundent_query." UNION ".$personnel_query);

        

        return $query->result();
    }

    public function getAllStudent($class_no,$year,$term){
        $select = "user.name, user.telephone, trim(user.email) email, trim(user.office_email) office_email, bureau.name bureau_name,ifnull(user.co_empdb_poftel,office_tel) as office_tel";

        $stundent_query = $this->db->select($select)
                 ->from("online_app oa")
                 ->join("BS_user user", "user.idno = oa.id")
                 ->join("bureau", "bureau.bureau_id = oa.beaurau_id",'left')
                 //->where("oa.yn_sel", 2) //2021-0519 因應疫情需求,註解只針對尚在報名狀態的學員寄信通知
                 ->where("oa.yn_sel in (2, 3, 8)") //2021-0519 因應疫情需求,增加已選員學員寄信通知
                 ->where("oa.class_no", $class_no)
                 ->where("oa.year", $year)
                 ->where("oa.term", $term)
                 ->get_compiled_select();

        $this->db->reset_query();

        $sub = $this->db->select("user.bureau_id")
                        ->distinct()
                        ->from("require r")
                        ->join("online_app oa", "oa.year = r.year AND oa.class_no = r.class_no AND oa.term = r.term", "left")
                        ->join("BS_user user", "user.idno = oa.id", "left")
                        //->where("oa.yn_sel in (2)") //2021-0519 因應疫情需求,註解只針對尚在報名狀態的學員寄信通知
                        ->where("oa.yn_sel in (2, 3, 8)") //2021-0519 因應疫情需求,增加已選員學員寄信通知
                        ->where("r.class_no", $class_no)
                        ->where("r.year", $year)
                        ->where("r.term", $term)
                        ->get_compiled_select();

        $this->db->reset_query();

        $personnel_query = $this->db->select("user.name, user.telephone, trim(user.email) email, trim(user.office_email) office_email, bureau.name bureau_name,ifnull(user.co_empdb_poftel,office_tel) as office_tel")
                                    ->from("BS_user user")
                                    ->join("bureau bureau", "bureau.bureau_id = user.bureau_id", "left")
                                    ->where("user.bureau_id IN ($sub)")
                                    ->where("user.username LIKE", 'edap%') //因為沒資料所以暫時註解掉測試其他功能中
                                    ->get_compiled_select();
        $this->db->reset_query();

        $query = $this->db->query($stundent_query." UNION ".$personnel_query);

        return $query->result();
    }

    public function getBureau($type){
        $this->db->select("user.name, user.email, user.office_tel, bureau.name bureau_name")
                 ->from("BS_user user")
                 ->join("bureau bureau", "bureau.bureau_id = user.bureau_id", "left")
                 ->join("account_role", "account_role.username = user.username AND group_id = 6")
                 ->where("user.username LIKE", "edap%")
                 ->where("user.enable = 1")
                 ->group_start()
                 ->where("bureau.del_flag", null)
                 ->or_where("bureau.del_flag",'N')
                 //->or_where("bureau.del_flag",'')
                 ->group_end();

        switch ($type) {
            case 0:
                $this->db->where("bureau.bureau_level <=", 3)
                         ->where("bureau.bureau_id !=", "379000000A");
                break;
            case 1:
                $this->db->group_start()
                             ->group_start()
                                 ->where("bureau.bureau_level =", 4)
                                 ->where("bureau.parent_id !=", "379040000E")
                             ->group_end()
                             ->or_group_start()
                                ->where("bureau.bureau_id <=", "379043200E")
                                ->where("bureau.parent_id =", "379040000E")
                             ->group_end()
                         ->group_end();
                break;
            case 2:
                $this->db->where("bureau.bureau_id >", "379043200E")
                         ->where("bureau.parent_id =", "379040000E");
                break;

            default:
                # code...
                break;
        }
        $query = $this->db->get();
        return $query->result();
    }

    public function getBureauName($bureau_id){
        $this->db->select('name');
        $this->db->where('bureau_id',$bureau_id);
        $query = $this->db->get('bureau');
        $result = $query->result_array();

        if(!empty($result)){
            return $result[0]['name'];
        }

        return '';
    }

    public function checkOnlineLeave($params,$idno,$vacation_date){
        $this->db->select('count(1) cnt');
        $this->db->where('year',$params['year']);
        $this->db->where('class_no',$params['class_no']);
        $this->db->where('term',$params['term']);
        $this->db->where('idno',$idno);
        $this->db->where('vacation_date',$vacation_date);
        $query = $this->db->get('leave_online');
        $result = $query->result_array();

        if($result[0]['cnt'] > 0){
            return true;
        }

        return false;
    }

    public function getCourseUserList($who, $params){
        switch ($who) {
            case '9':
                $yn_status = "'1','3','4','5','6','7','8'";
                break;
            default:
                $yn_status = "'2','6','7'";
                break;
        }
        //var_dump($who);

        //20211111 Roger 加入不顯示退休的設定
        $this->db->select("user.bureau_id, oa.st_no, CASE oa.yn_sel WHEN '4' THEN '(退訓)' WHEN '5' THEN '(未報到)' ELSE '' END yn_sel, user.out_gov_name, bureau.name as bureau_name, ct.description title, user.name, oa.group_no, CASE user.showretirement WHEN '0' THEN '' ELSE (CASE user.retirement WHEN '0' THEN '退休' ELSE '' END) END retirement")
                 ->from("online_app oa")
                 ->join("BS_user user", "user.idno = oa.id", "left")
                 ->join("bureau bureau", "bureau.bureau_id = oa.beaurau_id", "left")
                 ->join("out_gov og", "og.id = user.idno", "left")
                 ->join("code_table ct", "ct.type_id = '02' AND ct.item_id = user.job_title", "left")
                 ->where("oa.year", $params['year'])
                 ->where("oa.term", $params['term'])
                 ->where("oa.class_no", $params['class_no'])
                 ->where("oa.yn_sel not in (".$yn_status.")");
        if ($who == 3){
            $this->db->order_by("user.bureau_id, oa.st_no asc");
        }else{
            $this->db->order_by("oa.group_no asc , oa.st_no asc");
        }
        
        $query = $this->db->get();
        // dd($this->db->last_query());
        return $query->result();
    }

    /*
        取得實體課程課表
    */
    public function getPhySchedule($params){
        $select_name = "
            CASE WHEN t.name = '教務組' OR ru.title = '無' THEN t.name
                      WHEN ru.title <> '' THEN CONCAT(t.name,ru.title)
                      ELSE
                           CASE WHEN t.teacher_type = 1 THEN CONCAT(t.name, ' 老師')
                                WHEN t.teacher_type = 2 THEN CONCAT(t.name, ' (助)')
                                WHEN ru.use_id in ('O00001', 'O00002', 'O00003', 'O00004', 'O00005') THEN '教務組'
                                ELSE ''
                           END
            END AS name";
            /*$select_name = "
            CASE WHEN t.name = '教務組' OR ru.title = '無' THEN t.name
                      WHEN ru.title <> '' THEN ru.title|| t.name
                      ELSE
                           CASE WHEN t.teacher_type = 1 THEN CONCAT(t.name, ' 老師')
                                WHEN t.teacher_type = 2 THEN CONCAT(t.name, ' (助)')
                                WHEN ru.use_id in ('O00001', 'O00002', 'O00003', 'O00004', 'O00005') THEN '教務組'
                                ELSE ''
                           END
            END AS name";*/

        $this->db->select([
            "ru.teacher_id", 
            "ru.use_period", 
            "DATE_FORMAT(ru.use_date, '%Y-%m-%d') use_date", 
            "ru.use_id", 
            "ru.year", 
            "ru.class_id", 
            "ru.term", 
            "pt.from_time", 
            "pt.to_time", 
            "ct.description",
            "ifnull(cr.room_name, cr.room_sname) room_name", 
            "cr.room_sname as room_sname", 
            "r.contactor", 
            "r.tel", 
            $select_name,
            "r.class_name",
            "t.name teacher_name",
            "t.teacher_type"
            ])
             ->from("room_use ru")
             ->join("periodtime pt", "
                pt.year = ru.year AND 
                pt.class_no = ru.class_id AND 
                pt.term = ru.term AND 
                pt.id = ru.use_period AND
                pt.course_date = ru.use_date AND
                pt.course_code = ru.use_id AND
                pt.room_id = ru.room_id
             ", 'left')
             ->join("code_table ct", "ct.item_id = ru.use_id AND ct.type_id = '17'", 'left')
             ->join("venue_information cr", "cr.room_id = ru.room_id", 'left')
             ->join("require r", "r.year = ru.year AND r.term = ru.term AND r.class_no = ru.class_id")
             ->join("teacher t", "t.idno = ru.teacher_id AND t.teacher_type = (CASE WHEN ru.isteacher = 'Y' THEN '1' ELSE '2' END) ", 'left');

             if (is_array($params)){
                $this->db->where("ru.year", $params['year'])
                         ->where("ru.term", $params['term'])
                         ->where("ru.class_id", $params['class_no']);
             }else{
                $this->db->where("r.seq_no", $params);
             }

             $this->db->where("ru.use_date is not null")
                      ->group_by("ru.use_date, ru.teacher_id, pt.from_time")
                      ->order_by("ru.use_date,pt.from_time asc,use_period asc,t.teacher_type asc,ru.sort asc");
        $query = $this->db->get();
        //var_dump($query->result_array());
        //die();
        //20211027 Roger 增加group by teacher_id 與 from_time 修正老師名字的重覆筆數
        //20211029 Chris 增加group_by ru.use_date 修正合併課程列印不同天無法出現

        return $query->result();
    }

    /*
        取得線上課程課表
    */

    public function getOnlineSchedule($params){
        $this->db->select("rm.*, date_format(rm.start_date, '%m/%d') start_date_format, date_format(rm.end_date, '%m/%d') end_date_format")
                 ->from('require_online rm');
        if (is_array($params)){
            $this->db->where("year", $params['year'])
                     ->where("term", $params['term'])
                     ->where("class_no", $params['class_no']);
        }else{
            $this->db->join('require r', 'r.year = rm.year AND r.class_no = rm.class_no AND r.term = rm.term')
                     ->where("r.seq_no", $params);
        }

        //$this->db->order_by('sort');
        $query = $this->db->get();
        return $query->result();

    }

    public function setOnlineApp($params, $field = array()){
        $this->db->where($params)
                 ->update('online_app', $field);

        return $this->db->affected_rows();
    }

    public function insertStudLog($params)
    {
        /*insert into stud_modifylog (YEAR,CLASS_NO,TERM,BEAURAU_ID,ID,ST_NO,MODIFY_ITEM,MODIFY_DATE,MODIFY_LOG,O_ID,N_TERM,UPD_USER,S_BEAURAU_ID)
                                        select o.year,o.class_no,o.term,'{$beaurauId}',o.id,o.st_no,'調訓',sysdate,'','','','{$_SESSION['Login']['username']}' ,v.BEAURAU_ID
                                        from online_app o 
                                        left join vm_all_account v on v.personal_id = o.id
                                        where o.year='{$year}' and o.term='{$term}' and o.class_no='{$class_no}' and o.yn_sel='3' */
        $user=$this->flags->user['idno'];
        $user_beaurau=$this->flags->user['bureau_id'];
        $now_time=date('Y-m-d H:i:s');
        $this->db->select('o.year,o.class_no,o.term,o.id,o.st_no,o.beaurau_id');
        $this->db->where('o.year',$params['year']);
        $this->db->where('o.term',$params['term']);
        $this->db->where('o.class_no',$params['class_no']);
        $this->db->where('o.yn_sel','3');
        $student=$this->db->get('online_app as o');
        $student=$student->result_array();
        //var_dump($student);
        for($i=0;$i<count($student);$i++){
            $insert=['year'=>$params['year'],'class_no'=>$params['class_no'],'term'=>$params['term'],'beaurau_id'=>$student[$i]['beaurau_id'],'st_no'=>$student[$i]['st_no'],'id'=>$student[$i]['id'],'modify_item'=>'調訓','n_term'=>$params['term'],'o_id'=>$user,'s_beaurau_id'=>$user_beaurau ,'upd_user'=>$user,'modify_date'=>$now_time];
            //var_dump($insert);
            $ok=$this->db->insert('stud_modifylog',$insert);
            //var_dump($ok);
        }
       // die();
        //$insert=['year'=>$params['year'],'term'=>$params['term'],'beaurau_id','st_no','modify_item'=>'調訓','upd_user'=>$user,'modify_date'=>$now_time];
    }

    public function getReplaceData($params){

        $course = $this->getCourseInfo($params);
        $student_count = $this->getNumberOfStudent($params);
        $online_course = $this->getOnlineCourseURL($params);
       
        $replace_data['student_count'] = (empty($student_count)) ? 0 : $student_count;
        $replace_data['course'] = $course;
        $replace_data['online_course'] = $online_course;
        return $replace_data;

    }

    /*
        取得承辦人資訊,課前問卷,報到時間
    */

    public function getCourseInfo($params){

        $this->db->select('
                    user.name worker_name,
                    as.ext1 as worker_sub_phone,
                    pm.preq_id,
                    date_format(pm.start_date,"%Y-%m-%d") preq_start_date,
                    date_format(pm.end_date,"%Y-%m-%d") preq_end_date,
                    p.*,
                    vi.room_name,
                    date_format(rm.start_date,"%Y-%m-%d") mix_start_date,
                    date_format(rm.end_date,"%Y-%m-%d") mix_end_date,
                    r.quit_class,r.quit_class2,
                    r.range,
                    ifnull(user.office_email, user.email) worker_email,
                    r.class_name,
                    r.year,
                    r.term'
                 )
                 ->from('require r')
                 ->join('BS_user user', 'user.idno = r.worker', 'left')
                 ->join('agent_set as', 'as.item_id = r.worker', 'left')
                 ->join('preq_main pm', 'pm.year = r.year AND pm.term = r.term AND pm.class_no = r.class_no', 'left')
                 ->join('periodtime p', "p.year = r.year AND p.term = r.term AND p.class_no = r.class_no AND p.course_code in ('O00001','O00003','O00004','O00005','004643','006430','017826','017835')", 'left')
                 ->join('venue_information vi', 'vi.room_id = p.room_id', 'left')
                 ->join('require_mix rm', 'rm.year = r.year AND rm.term = r.term AND rm.class_no = r.class_no', 'left')
                 ->where("r.year", $params['year'])
                 ->where("r.term", $params['term'])
                 ->where("r.class_no", $params['class_no']);
        $query = $this->db->get();
        return $query->row();
    }

    public function getNumberOfStudent($params){
        $ynsel=[2,6,7];
        $this->db->select('count(*) student_count')
                 ->from('online_app')
                 ->where("year", $params['year'])
                 ->where("term", $params['term'])
                 ->where("class_no", $params['class_no'])
                 ->where_not_in("yn_sel",$ynsel)
                 ->group_by("year,term,class_no");
        $query = $this->db->get();

        return $query->row();
    }

    public function getOnlineCourseURL($params){
        $this->db->select("*")
                 ->from('require_online')
                 ->where("year", $params['year'])
                 ->where("term", $params['term'])
                 ->where("class_no", $params['class_no']);
        $query = $this->db->get();
        return $query->result();
    }

    public function getSpecialEvaluationDate($cmid){
        $this->db->select('beginDatetime,endDatetime');
        $this->db->where('cmid',$cmid);
        $query = $this->db->get('SV_ClassManagementForm');
        $result = $query->result_array();

        return $result;
    }

    public function getEvaluationTeacher($id){
        $this->db->select('year,class_no,term');
        $this->db->where('seq_no',$id);
        $query = $this->db->get('require');
        $info = $query->result_array();

        $result = array();
        if(!empty($info)){
            $sql = sprintf("SELECT
                            ct.*, t. name AS teacher_name,
                            t.job_title,
                            t.major,
                            c.name AS course_name,
                            d.class_name,
                            d.isevaluate_no_teacher 
                        FROM
                            courseteacher ct
                        LEFT JOIN (
                            SELECT DISTINCT
                                name,
                                job_title,
                                major,
                                idno
                            FROM
                                teacher
                            WHERE
                                teacher_type = '1'
                        ) t ON t.idno = ct.teacher_id
                        LEFT JOIN course_code c ON c.item_id = ct.course_code
                        LEFT JOIN `require` d on d.year = ct.year 
                        and d.class_no = ct.class_no
                        and d.term = ct.term
                        WHERE
                            ct.use_date IS NOT NULL
                        AND c.name <> '報到暨班務說明'
                        AND ct.year='%s' and ct.class_no ='%s' AND ct.term='%s'
                        ORDER BY
                            ct.use_date,
                            ct.teacher_id,
                            ct.course_code",$info[0]['year'],$info[0]['class_no'],$info[0]['term']);
            $query = $this->db->query($sql);
            $result = $query->result_array();
        }

        return $result;
    }

    //20211112 Roger 其它問卷查詢
    public function getEvaluationOther($id){
        $this->db->select('year,class_no,term');
        $this->db->where('seq_no',$id);
        $query = $this->db->get('require');
        $info = $query->result_array();
        
        $result = array();

        if(!empty($info)){
            
                $sql =sprintf(
                    "SELECT cmf.id, cmf.cmid,cmf.fid, cmf.formName, cmf.courseDate, cmf.beginDatetime,cmf.endDatetime
                    FROM SV_ClassManagementForm cmf
                    left join SV_ClassManagement cm on cm.id = cmf.cmid
                    WHERE cmf.fid != '91'
                         AND cm.year='%s' 
                         AND cm.class ='%s'
                         AND cm.ladder='%s'",$info[0]['year'],$info[0]['class_no'],$info[0]['term']);
                

            $query = $this->db->query($sql);
            $result = $query->result_array();
        }
//var_dump($result);die();
        return $result;
    }

    //20211115 Roger 刪除其它問卷
    public function deltEvaluationOther($cmid, $fid){
        $this->db->where('cmid',$cmid);
        
        $this->db->where('fid ',$fid);
        $this->db->delete('SV_ClassManagementForm');
    }
    
    //20211115 Roger 新增特殊問卷
    public function instEvaluationOther($ins_info=array()){
        $cmid = $ins_info['cmid'];
        //var_dump($cmid);die();
        if ($cmid ==null){
            $this->db->select('id');
            $this->db->where('year',$ins_info['year']);
            $this->db->where('class',$ins_info['class_no']);
            $this->db->where('ladder',$ins_info['term']);
            $query = $this->db->get('SV_ClassManagement');
            $result = $query->result_array();  //查詢問卷裡有沒有已經生成的問卷

            if(empty($result)){  //如果查詢出來沒有資料的話，就新增一筆問卷 ，並取得cmid
                if(isset($info['anonymous'])){
                    $this->db->set('anonymous',$info['anonymous']);
                }

                $this->db->set('year',$ins_info['year']);
                $this->db->set('class',$ins_info['class_no']);
                $this->db->set('ladder',$ins_info['term']);
                $this->db->set('name',$ins_info['class_name']);
                $this->db->set('creater',$ins_info['cre_user']);
                $this->db->set('createDate',date('Y-m-d'));
                $this->db->set('createTime',date('H:i:s'));
                $this->db->set('updater',$ins_info['cre_user']);
                $this->db->set('updateDate',date('Y-m-d'));
                $this->db->set('updateTime',date('H:i:s'));
                $this->db->insert('SV_ClassManagement');
                $cmid = $this->db->insert_id();
            } else { // 如果已有資料，就更新問卷的時間
                if(isset($info['anonymous'])){
                    $this->db->set('anonymous',$info['anonymous']);
                }
                $this->db->set('name',$ins_info['class_name']);
                $this->db->set('updater',$ins_info['cre_user']);
                $this->db->set('updateDate',date('Y-m-d'));
                $this->db->set('updateTime',date('H:i:s'));
                $this->db->where('id',$result[0]['id']);
                $this->db->update('SV_ClassManagement');
                $cmid = $result[0]['id'];
            }
        }
//var_dump($cmid);die();

        if(!empty($ins_info['question_id'])){
            //$cmid = $ins_info['cmid'];   //20220119 造成第一筆資料會出現錯誤
            $max_order = $this->getSvClassManagementFormMaxOrder($cmid);  //取得最大的order
            $form_name = $this->getSvFormName($ins_info['question_id']);  //取得問卷名稱
            //$begin_time = $this->getSvClassManagementFormMaxBeginTime($cmid); //取得問卷的開始時間
            //$end_time = $this->getSvClassManagementFormMaxEndDateTime($cmid); //取得問卷的結束時間

            if(empty($begin_time)){
                $begin_time = $ins_info['standard_date'];
            }

            if(empty($end_time)){
                $end_time = $ins_info['standard_date_end'].' 23:59:59';
            }
            
            $this->db->select('count(1) cnt');
            $this->db->where('cmid',$cmid);
            $this->db->where('fid',$ins_info['question_id']);
            $query = $this->db->get('SV_ClassManagementForm');
            $check_is_exist2 = $query->result_array();  //查詢這課程的cmid裡的問卷有幾筆

            if($check_is_exist2[0]['cnt'] > 0){  //如果大於0個，則更新所有問卷的資料
                
                $this->db->set('beginDatetime',$begin_time);
                $this->db->set('endDatetime',$end_time);
                $this->db->set('updater',$ins_info['cre_user']);
                $this->db->set('updateDate',date('Y-m-d'));
                $this->db->set('updateTime',date('H:i:s'));
                $this->db->where('cmid',$cmid);
                $this->db->where('fid',$ins_info['question_id']);
                $this->db->update('SV_ClassManagementForm');
            } else {  //如果沒有資料，則再查詢一次不是91(滿意度)之外的問卷
                
                $this->db->select('fid');
                $this->db->where('cmid',$cmid);
                $this->db->where('fid !=','91');
                $query = $this->db->get('SV_ClassManagementForm');
                $tmp_fid = $query->result_array();

                
                    //var_dump($ins_info);var_dump($max_order);die();
                    $this->db->set('cmid',$cmid);
                    $this->db->set('fid',$ins_info['question_id']);
                    $this->db->set('`order`',$max_order);
                    $this->db->set('formName',$form_name);
                    $this->db->set('beginDatetime',$begin_time);
                    $this->db->set('endDatetime',$end_time);
                    $this->db->set('creater',$ins_info['cre_user']);
                    $this->db->set('createDate',date('Y-m-d'));
                    $this->db->set('createTime',date('H:i:s'));
                    $this->db->set('updater',$ins_info['cre_user']);
                    $this->db->set('updateDate',date('Y-m-d'));
                    $this->db->set('updateTime',date('H:i:s'));
                    $this->db->insert('SV_ClassManagementForm');
                
            }
        }
    }
        //20220122 Roger 特殊問卷評估日期更新
        public function update_other($info=array()){
        
            
            for($i=0;$i<count($info['rowid_other']);$i++){
    
                $this->db->set('beginDatetime',$info['standard_date2']);
                $this->db->set('endDatetime',$info['standard_date_end2'].' 23:59:59');
                $this->db->set('updater',$info['cre_user']);
                $this->db->set('updateDate',date('Y-m-d'));
                $this->db->set('updateTime',date('H:i:s'));
                $this->db->where('id',$info['rowid_other'][$i]);
                $this->db->update('SV_ClassManagementForm');
            }
           
        }
        //20220126 Roger 新增判斷已作答的課程有哪些
    public function replyedcourse($year,$class,$term){
        $this->db->select('cmf.course,cmf.teacher');
        $this->db->from('SV_ClassManagementForm cmf');
        $this->db->join('SV_ClassManagement cm', 'cm.id = cmf.cmid');
        $this->db->join('SV_Reply r','r.cmfid = cmf.id');
        $this->db->where('cm.year',$year);
        $this->db->where('cm.class',$class);
        $this->db->where('cm.ladder',$term);
        $this->db->where('r.fid',91);
        $query = $this->db->get();
        $result = $query->result_array();
        return $result;
    }
        
    public function setEvaluationTeacher($info=array()){
        //20211209 Roger 若「評估儲存」按了，沒有選起訖日期的話則不儲存
        $new_rowid = [];
        if(isset($info['rowid'])){                 
            for($i=0;$i<count($info['rowid']);$i++){
                
                $ass_start = 'assess_start_date@'.$info['rowid'][$i];
                $ass_end = 'assess_end_date@'.$info['rowid'][$i];
                if (($info[$ass_start] <> "")&&($info[$ass_end] <> "")){
                     array_push($new_rowid,$info['rowid'][$i]);
                }
            }
        }
        $info['rowid'] = $new_rowid; //20211209 Roger 把舊的array換掉


        if(isset($info['anonymous'])){
            $anonymous = $info['anonymous'];
        }else{
            $anonymous = "";
        }

        $this->db->trans_start();
        //20220126 Roger 修改全不選時，已作答的問卷資訊也不會被清除
        $course2 = $this->replyedcourse($info['year'],$info['class_no'],$info['term']);
        
        $this->db->where('year',$info['year']);
        $this->db->where('class_no',$info['class_no']);
        $this->db->where('term',$info['term']);
        $query = $this->db->get('courseteacher');
        $need_fix_all = $query->result_array();
                
        foreach ($need_fix_all as $key => $need_fix) {
            $lkk = 0;
            foreach ($course2 as $key2 => $course23) {
                
                if (($need_fix['course_code']==$course23['course'])&&($need_fix['teacher_id']==$course23['teacher'])){                    
                    $lkk = 1;
                }              
            }

            if ($lkk == 1){
            }else{
                $this->db->set('isevaluate','N');
                $this->db->set('assess_date',null);
                $this->db->set('assess_date_end',null);
                $this->db->set('inside',$info['inside']);
                $this->db->where('year',$info['year']);
                $this->db->where('class_no',$info['class_no']);
                $this->db->where('term',$info['term']);
                $this->db->where('course_code',$need_fix['course_code']);
                $this->db->where('teacher_id',$need_fix['teacher_id']);
                $this->db->update('courseteacher');
            }

        }



        
            $this->db->select('id');
            $this->db->where('year',$info['year']);
            $this->db->where('class',$info['class_no']);
            $this->db->where('ladder',$info['term']);
            $query = $this->db->get('SV_ClassManagement');
            $result = $query->result_array();

            if(empty($result)){
                /* if(isset($info['anonymous'])){
                    $this->db->set('anonymous',$info['anonymous']);
                } */
                $this->db->set('anonymous',$anonymous);
                $this->db->set('year',$info['year']);
                $this->db->set('class',$info['class_no']);
                $this->db->set('ladder',$info['term']);
                $this->db->set('name',$info['class_name']);
                $this->db->set('creater',$info['cre_user']);
                $this->db->set('createDate',date('Y-m-d'));
                $this->db->set('createTime',date('H:i:s'));
                $this->db->set('updater',$info['cre_user']);
                $this->db->set('updateDate',date('Y-m-d'));
                $this->db->set('updateTime',date('H:i:s'));
                $this->db->insert('SV_ClassManagement');
                $cmid = $this->db->insert_id();
            } else {
                /* if(isset($info['anonymous'])){
                    $this->db->set('anonymous',$info['anonymous']);
                } */
                $this->db->set('anonymous',$anonymous);
                $this->db->set('name',$info['class_name']);
                $this->db->set('updater',$info['cre_user']);
                $this->db->set('updateDate',date('Y-m-d'));
                $this->db->set('updateTime',date('H:i:s'));
                $this->db->where('id',$result[0]['id']);
                $this->db->update('SV_ClassManagement');
                $cmid = $result[0]['id'];
            }
            if(!empty($info['rowid']) || !empty($info['question_id']) || !empty($info['isevaluate_no_teacher'])){

            $this->db->select('id,courseDate,course,teacher,beginDatetime,endDatetime');
            $this->db->where('cmid',$cmid);
            $this->db->where('fid','91');
            $query = $this->db->get('SV_ClassManagementForm');
            $form_list = $query->result_array();

            for($i=0;$i<count($form_list);$i++){
                $this->db->select('count(1) cnt');
                $this->db->where('cmid',$cmid);
                $this->db->where('cmfid',$form_list[$i]['id']);
                $this->db->where('fid','91');
                $query = $this->db->get('SV_Reply');
                $reply_count = $query->result_array();

                if($reply_count[0]['cnt'] > 0){
                    $this->db->set('isevaluate','Y');
                    $this->db->set('assess_date',$form_list[$i]['beginDatetime']);
                    $this->db->set('assess_date_end',$form_list[$i]['endDatetime']);
                    $this->db->set('inside',$info['inside']);
                    $this->db->where('year',$info['year']);
                    $this->db->where('class_no',$info['class_no']);
                    $this->db->where('term',$info['term']);
                    $this->db->where('course_code',$form_list[$i]['course']);
                    $this->db->where('teacher_id',$form_list[$i]['teacher']);
                    $this->db->where('use_date',$form_list[$i]['courseDate']);
                    $this->db->update('courseteacher');
                } else {
                    $this->db->where('id',$form_list[$i]['id']);
                    $this->db->where('fid','91');
                    $this->db->delete('SV_ClassManagementForm');
                }
            }
           

            if(isset($info['rowid'])){
                for($i=0;$i<count($info['rowid']);$i++){
                    $data = explode('@', $info['rowid'][$i]);
                    $teacher_id = $data[0];
                    $use_date = $data[1];
                    $course_code = $data[2];

                    $assess_start_date_key = 'assess_start_date@'.$teacher_id.'@'.$use_date.'@'.$course_code;
                    $assess_end_date_key = 'assess_end_date@'.$teacher_id.'@'.$use_date.'@'.$course_code;

                    if(isset($info[$assess_start_date_key]) && !empty($assess_start_date_key)){
                        $this->db->set('assess_date',$info[$assess_start_date_key]);
                    }

                    if(isset($info[$assess_end_date_key]) && !empty($assess_end_date_key)){
                        $this->db->set('assess_date_end',$info[$assess_end_date_key].' 23:59:59');
                    }

                    $this->db->set('isevaluate','Y');
                    $this->db->set('inside',$info['inside']);
                    $this->db->where('year',$info['year']);
                    $this->db->where('class_no',$info['class_no']);
                    $this->db->where('term',$info['term']);
                    $this->db->where('course_code',$course_code);
                    $this->db->where('teacher_id',$teacher_id);
                    if(!empty($use_date)){
                        $this->db->where('use_date',$use_date);
                    }
                    $this->db->update('courseteacher');


                    $this->db->select('count(1) cnt');
                    $this->db->where('cmid',$cmid);
                    $this->db->where('fid','91');
                    $this->db->where('course',$course_code);
                    $this->db->where('teacher',$teacher_id);
                    if(!empty($use_date)){
                        $this->db->where('courseDate',$use_date);
                    }
                    $query = $this->db->get('SV_ClassManagementForm');
                    $check_is_exist = $query->result_array();

                    if($check_is_exist[0]['cnt'] > 0){
                        $this->db->set('beginDatetime',$info[$assess_start_date_key]);
                        $this->db->set('endDatetime',$info[$assess_end_date_key].' 23:59:59');
                        $this->db->set('updater',$info['cre_user']);
                        $this->db->set('updateDate',date('Y-m-d'));
                        $this->db->set('updateTime',date('H:i:s'));
                        $this->db->where('cmid',$cmid);
                        $this->db->where('fid','91');
                        $this->db->where('course',$course_code);
                        $this->db->where('teacher',$teacher_id);
                        $this->db->update('SV_ClassManagementForm');
                    } else {
                        $max_order = $this->getSvClassManagementFormMaxOrder($cmid);

                        $this->db->select('name');
                        $this->db->where('item_id',$course_code);
                        $query = $this->db->get('course_code');
                        $course_name = $query->result_array();

                        $this->db->select('name');
                        $this->db->where('idno',$teacher_id);
                        $query = $this->db->get('teacher');
                        $teacher_name = $query->result_array();

                        $formName = '講座評估-'.$course_name[0]['name'].'-'.$teacher_name[0]['name'];

                        $this->db->set('cmid',$cmid);
                        $this->db->set('fid','91');
                        $this->db->set('`order`',$max_order);
                        $this->db->set('formName',$formName);
                        $this->db->set('courseDate',$use_date);
                        $this->db->set('course',$course_code);
                        $this->db->set('teacher',$teacher_id);
                        $this->db->set('beginDatetime',$info[$assess_start_date_key]);
                        $this->db->set('endDatetime',$info[$assess_end_date_key].' 23:59:59');
                        $this->db->set('creater',$info['cre_user']);
                        $this->db->set('createDate',date('Y-m-d'));
                        $this->db->set('createTime',date('H:i:s'));
                        $this->db->set('updater',$info['cre_user']);
                        $this->db->set('updateDate',date('Y-m-d'));
                        $this->db->set('updateTime',date('H:i:s'));
                        $this->db->insert('SV_ClassManagementForm');
                    }
                }
            }
            if(!empty($info['question_id'])){
                $max_order = $this->getSvClassManagementFormMaxOrder($cmid);
                $form_name = $this->getSvFormName($info['question_id']);
                $begin_time = $this->getSvClassManagementFormMaxBeginTime($cmid);
                $end_time = $this->getSvClassManagementFormMaxEndDateTime($cmid);

                if(empty($begin_time)){
                    $begin_time = $info['standard_date'];
                }

                if(empty($end_time)){
                    $end_time = $info['standard_date_end'].' 23:59:59';
                }

                $this->db->select('count(1) cnt');
                $this->db->where('cmid',$cmid);
                $this->db->where('fid',$info['question_id']);
                $query = $this->db->get('SV_ClassManagementForm');
                $check_is_exist2 = $query->result_array();

                if($check_is_exist2[0]['cnt'] > 0){
                    $this->db->set('beginDatetime',$begin_time);
                    $this->db->set('endDatetime',$end_time);
                    $this->db->set('updater',$info['cre_user']);
                    $this->db->set('updateDate',date('Y-m-d'));
                    $this->db->set('updateTime',date('H:i:s'));
                    $this->db->where('cmid',$cmid);
                    $this->db->where('fid',$info['question_id']);
                    $this->db->update('SV_ClassManagementForm');
                } else {
                    $this->db->select('fid');
                    $this->db->where('cmid',$cmid);
                    $this->db->where('fid !=','91');
                    $query = $this->db->get('SV_ClassManagementForm');
                    $tmp_fid = $query->result_array();

                    $check_has_answer = 0;
                    if(isset($tmp_fid[0]['fid']) && !empty($tmp_fid)){
                        $check_has_answer = $this->checkHasAnswer($cmid,$tmp_fid[0]['fid']);
                    }

                    if($check_has_answer == '0'){
                        $this->db->where('cmid',$cmid);
                        $this->db->where('fid !=','91');
                        $this->db->delete('SV_ClassManagementForm');

                        $this->db->set('cmid',$cmid);
                        $this->db->set('fid',$info['question_id']);
                        $this->db->set('`order`',$max_order);
                        $this->db->set('formName',$form_name);
                        $this->db->set('beginDatetime',$begin_time);
                        $this->db->set('endDatetime',$end_time);
                        $this->db->set('creater',$info['cre_user']);
                        $this->db->set('createDate',date('Y-m-d'));
                        $this->db->set('createTime',date('H:i:s'));
                        $this->db->set('updater',$info['cre_user']);
                        $this->db->set('updateDate',date('Y-m-d'));
                        $this->db->set('updateTime',date('H:i:s'));
                        $this->db->insert('SV_ClassManagementForm');
                    } else {
                        $this->db->set('beginDatetime',$begin_time);
                        $this->db->set('endDatetime',$end_time);
                        $this->db->set('updater',$info['cre_user']);
                        $this->db->set('updateDate',date('Y-m-d'));
                        $this->db->set('updateTime',date('H:i:s'));
                        $this->db->where('cmid',$cmid);
                        $this->db->where('fid',$tmp_fid[0]['fid']);
                        $this->db->update('SV_ClassManagementForm');
                    }
                }
            }

            
            $this->db->select('course_code');
            $this->db->where('year',$info['year']);
            $this->db->where('class_no',$info['class_no']);
            $this->db->where('term',$info['term']);
            $this->db->group_by('course_code');
            $this->db->order_by('course_date,from_time');
            $query = $this->db->get('periodtime');
            $orderList = $query->result_array();

            for($i=0;$i<count($orderList);$i++){
                $this->db->select('count(1) cnt');
                $this->db->where('cmid',$cmid);
                $this->db->where('course',$orderList[$i]['course_code']);
                $this->db->where('fid',91);

                $query = $this->db->get('SV_ClassManagementForm');
                $checkExist = $query->result_array();

                if($checkExist[0]['cnt'] == '1'){
                    $this->db->set('order',$i);
                    $this->db->where('cmid',$cmid);
                    $this->db->where('course',$orderList[$i]['course_code']);
                    $this->db->where('fid',91);
                    $this->db->update('SV_ClassManagementForm');
                }
            }
           

        }

        $isevaluate_no_teacher = (!empty($info['isevaluate_no_teacher'])) ? 'Y' : null;

        $this->db->set('isevaluate_no_teacher', $isevaluate_no_teacher)
                 ->where('class_no', $info['class_no'])
                 ->where('term', $info['term'])
                 ->where('year', $info['year'])
                 ->update('require');

        $this->db->trans_complete();

        if($this->db->trans_status() === TRUE){
            return true;
        } 

        return false;
    }

    public function updateSVOrder(){
        // 
        //     $this->db->select('id,year,class,ladder');
        //     $this->db->where('year',109);
        //     $query = $this->db->get('SV_ClassManagement');
        //     $info = $query->result_array();

        //     for($j=0;$j<count($info);$j++){
        //         $this->db->select('course_code');
        //         $this->db->where('year',$info[$j]['year']);
        //         $this->db->where('class_no',$info[$j]['class']);
        //         $this->db->where('term',$info[$j]['ladder']);
        //         $this->db->group_by('course_code');
        //         $this->db->order_by('course_date,from_time');
        //         $query = $this->db->get('periodtime');
        //         $orderList = $query->result_array();

        //         for($i=0;$i<count($orderList);$i++){
        //             $this->db->select('count(1) cnt');
        //             $this->db->where('cmid',$info[$j]['id']);
        //             $this->db->where('course',$orderList[$i]['course_code']);
        //             $this->db->where('fid',91);

        //             $query = $this->db->get('SV_ClassManagementForm');
        //             $checkExist = $query->result_array();

        //             if($checkExist[0]['cnt'] == '1'){
        //                 $this->db->set('order',$i);
        //                 $this->db->where('cmid',$info[$j]['id']);
        //                 $this->db->where('course',$orderList[$i]['course_code']);
        //                 $this->db->where('fid',91);
        //                 $this->db->update('SV_ClassManagementForm');
        //             }
        //         }
        //     }
        // }
    }

    public function checkHasAnswer($cmid,$qid){
        $this->db->select('count(1) cnt');
        $this->db->where('cmid',$cmid);
        $this->db->where('fid',$qid);
        $query = $this->db->get('SV_Reply');
        $result = $query->result_array();

        return $result[0]['cnt'];
    }

    public function getSvClassManagementFormFid($id){
        $this->db->select('year,class_no,term');
        $this->db->where('seq_no',$id);
        $query = $this->db->get('require');
        $info = $query->result_array();

        if(isset($info[0]['year']) && !empty($info[0]['year'])){
            $this->db->select('id');
            $this->db->where('year',$info[0]['year']);
            $this->db->where('class',$info[0]['class_no']);
            $this->db->where('ladder',$info[0]['term']);
            $query = $this->db->get('SV_ClassManagement');
            $cmid = $query->result_array();

            if(isset($cmid[0]['id']) && !empty($cmid[0]['id'])){
                $this->db->select('fid');
                $this->db->where('cmid',$cmid[0]['id']);
                $this->db->where('fid != ','91');
                $query = $this->db->get('SV_ClassManagementForm');
                $fid = $query->result_array();

                if(isset($fid[0]['fid']) && !empty($fid[0]['fid'])){
                    return $fid[0]['fid'];
                } else {
                    return '0';
                }
            } else {
                return '0';
            }
        } else {
            return '0';
        }
    }

    public function getSvClassManagementFormMaxOrder($cmid){
        $this->db->select('max(`order`) as max_order');
        $this->db->where('cmid',$cmid);
        $query = $this->db->get('SV_ClassManagementForm');
        $max_order = $query->result_array();
        
        if(isset($max_order[0]['max_order']) && !empty($max_order[0]['max_order'])){
            $max_order = $max_order[0]['max_order']+1;
        } else {
            $max_order = 1;
        }
        
        return $max_order;
    }

    public function getSvFormName($fid){
        $this->db->select('name');
        $this->db->where('id',$fid);
        $query = $this->db->get('SV_Form');
        $result = $query->result_array();

        if(!empty($result)){
            return $result[0]['name'];
        }

        return '';
    }

    public function getCmid($year, $class_no, $term){
        $this->db->select('id');
        $this->db->where('year',$year);
        $this->db->where('class',$class_no);
        $this->db->where('ladder',$term);
        $query = $this->db->get('SV_ClassManagement');
        $cmid = $query->result_array();

        return $cmid;
    }

    public function getRid($cmid,$idno){
        $this->db->select('id');
        $this->db->where('cmid',$cmid);
        $this->db->where('sid',$idno);
        $query = $this->db->get('SV_Reply');
        $rid = $query->result_array();

        return $rid;
    }

    public function getRqid($rid){
        $this->db->select('id');
        $this->db->where('rid',$rid);
        $query = $this->db->get('SV_ReplyQuestion');
        $rqid = $query->result_array();

        return $rqid;
    }

    public function delReply($cmid,$idno){
        $this->db->where('cmid',$cmid);
        $this->db->where('sid',$idno);

        if($this->db->delete('SV_Reply')){
            return true;
        }

        return false;
    }

    public function delReplyQuestion($rid,$idno){
        $this->db->where('rid',$rid);
        $this->db->where('creater',$idno);

        if($this->db->delete('SV_ReplyQuestion')){
            return true;
        }

        return false;
    }

    public function delReplyAnswer($rqid,$idno){
        $this->db->where('rqid',$rqid);
        $this->db->where('creater',$idno);

        if($this->db->delete('SV_ReplyAnswer')){
            return true;
        }

        return false;
    }

    public function getSvClassManagementFormMaxBeginTime($cmid){
        $this->db->select('max(beginDatetime) as beginDatetime');
        $this->db->where('cmid',$cmid);
        $this->db->where('fid','91');
        $query = $this->db->get('SV_ClassManagementForm');
        $result = $query->result_array();

        if(!empty($result)){
            return $result[0]['beginDatetime'];
        }

        return null;
    }

    public function getSvClassManagementFormMaxEndDateTime($cmid){
        $this->db->select('max(endDatetime) as endDatetime');
        $this->db->where('cmid',$cmid);
        $this->db->where('fid','91');
        $query = $this->db->get('SV_ClassManagementForm');
        $result = $query->result_array();

        if(!empty($result)){
            return $result[0]['endDatetime'];
        }

        return null;
    }

    public function getOnlineAppCount($year,$class_no,$term){
        $this->db->select('count(1) cnt');
        $this->db->where('year',$year);
        $this->db->where('class_no',$class_no);
        $this->db->where('term',$term);
        $this->db->where('is_assess','1');
        $query = $this->db->get('online_app');
        $result = $query->result_array();

        return $result[0]['cnt'];
    }

    public function getAssessMix($year,$class_no,$term){
        $this->db->select('is_assess,is_mixed');
        $this->db->where('year',$year);
        $this->db->where('class_no',$class_no);
        $this->db->where('term',$term);
        $query = $this->db->get('require');
        $result = $query->result_array();

        return $result;
    }

    public function getOnlineAppID($year,$class_no,$term){
        $this->db->select('id');
        $this->db->where('year',$year);
        $this->db->where('class_no',$class_no);
        $this->db->where('term',$term);
        $this->db->where('is_assess','1');
        $this->db->where('online_ready','1');
        $query = $this->db->get('online_app');
        $result = $query->result_array();

        return $result;
    }

    public function getOnlineAppID2($year,$class_no,$term){
        $this->db->select('id');
        $this->db->where('year',$year);
        $this->db->where('class_no',$class_no);
        $this->db->where('term',$term);
        $this->db->where('is_assess','1');
        $query = $this->db->get('online_app');
        $result = $query->result_array();

        return $result;
    }

    public function getAge($idno){
        $this->db->select('birthday');
        $this->db->where('idno',$idno);
        $query = $this->db->get('BS_user');
        $result = $query->result_array();

        list($y1,$m1,$d1) = explode("-",date("Y-m-d",strtotime($result[0]['birthday']))); 
        $now = strtotime("now"); 
        list($y2,$m2,$d2) = explode("-",date("Y-m-d",$now)); 
        $age = $y2 - $y1; 
        if((int)($m2.$d2) < (int)($m1.$d1)) {
            $age -= 1; 
        }
        
        return $age;
    }

    public function updateOnlineApp($year,$class_no,$term,$idno){
        $age = $this->getAge($idno);

        $sql = sprintf("UPDATE online_app SET yn_sel='1'
                        ,age = %s 
                        ,ori_beaurau_id=(SELECT bureau_id FROM BS_user WHERE online_app.id=BS_user.idno)
                        ,ori_title=(SELECT job_title FROM BS_user WHERE online_app.id=BS_user.idno)
                        ,ori_gender=(SELECT gender FROM BS_user WHERE online_app.id=BS_user.idno)
                        ,co_position=(SELECT job_distinguish FROM BS_user WHERE online_app.id=BS_user.idno)
                        ,co_education=(SELECT education FROM BS_user WHERE online_app.id=BS_user.idno)
                        WHERE year=%s AND class_no=%s AND term=%s AND yn_sel IN ('3','8') AND id=%s",$this->db->escape(addslashes($age)),$this->db->escape(addslashes($year)),$this->db->escape(addslashes($class_no)),$this->db->escape(addslashes($term)),$this->db->escape(addslashes($idno)));

        if($this->db->query($sql)){
            return true;
        }

        return false;
    }

    public function updateOnlineAppLoop($year,$class_no,$term){
        $this->db->trans_start();

        $this->db->select('id');
        $this->db->where('year',$year);
        $this->db->where('class_no',$class_no);
        $this->db->where('term',$term);
        $query = $this->db->get('online_app');
        $result = $query->result_array();

        for($i=0;$i<count($result);$i++) { 
            $this->updateOnlineApp($year,$class_no,$term,$result[$i]['id']);
        }

        $this->db->trans_complete();

        if($this->db->trans_status() === TRUE){
            return true;
        } 

        return false;
    }

    public function updateRequire($year,$class_no,$term,$is_end){
        $this->db->set('isend',$is_end);
        $this->db->set('classenddate',date('Y-m-d H:i:s'));
        $this->db->where('year',$year);
        $this->db->where('class_no',$class_no);
        $this->db->where('term',$term);

        if($this->db->update('require')){
            return true;
        }

        return false;
    }

    public function getCancelStudent($year,$class_no,$term){
        $this->db->select('id');
        $this->db->where('year',$year);
        $this->db->where('class_no',$class_no);
        $this->db->where('term',$term);
        $this->db->where_in('yn_sel',array('4','5','6','7'));
        $query = $this->db->get('online_app');
        $result = $query->result_array();

        return $result;
    }

    public function getTrainingPeople($class_info){
        $this->db->from("online_app")
                 ->where("year", $class_info['year'])
                 ->where("class_no", $class_info['class_no'])
                 ->where("term", $class_info['term'])
                 ->where("yn_sel", '8');
        return $this->db->count_all_results();
    }
    /*
        取得r該承辦人未評估班期
    */
    public function getUnAssess($idno){
        $year = date("Y")-1911;
        $sql = "SELECT
                    a.year,
                    a.class_no,
                    a.term,
                    a.class_name,
                    b.idno
                FROM
                    `require` a
                JOIN BS_user b ON
                    a.worker = b.idno
                LEFT JOIN (
                    SELECT year,
                        class_no,
                        term,
                        count(1) cnt
                    FROM
                        courseteacher
                    WHERE
                        assess_date is not null
                    GROUP BY
                        year,
                        class_no,
                        term ) cnt ON cnt.year = a.year AND cnt.class_no = a.class_no AND cnt.term = a.term 
                WHERE
                    a.isevaluate = 'Y' AND a.year >= ? AND (cnt.year is null AND (a.isevaluate_no_teacher <> 'Y' OR a.isevaluate_no_teacher is null)) AND b.idno = ? ";

        $query = $this->db->query($sql, [$year,$idno]);
       

        return $query->result();
    }

    public function getClassName($data=array()){
        $this->db->select('class_name');
        $this->db->where('year',$data['year']);
        $this->db->where('class_no',$data['class_no']);
        $this->db->where('term',$data['term']);
        $query = $this->db->get('require');
        $result = $query->result_array();

        if(!empty($result)){
            return $result[0]['class_name'];
        }

        return ''; 
    }

    public function getClassSchedule($data=array()){
        $this->db->select('count(1) cnt');
        $this->db->where('year',$data['year']);
        $this->db->where('class_no',$data['class_no']);
        $this->db->where('term',$data['term']);
        $this->db->where('status is not null',null,false);
        $query2 = $this->db->get('hour_traffic_tax');
        $result2 = $query2->result_array();

        if($result2[0]['cnt'] > 0){
            return '請款階段';
        }

        $this->db->select('count(1) cnt');
        $this->db->where('year',$data['year']);
        $this->db->where('class_no',$data['class_no']);
        $this->db->where('term',$data['term']);
        $this->db->where('isevaluate','Y');
        $query = $this->db->get('require');
        $result = $query->result_array();

        if($result[0]['cnt'] > 0){
            return '評估階段';
        }

        $this->db->select('count(1) cnt');
        $this->db->where('year',$data['year']);
        $this->db->where('class_no',$data['class_no']);
        $this->db->where('term',$data['term']);
        $query3 = $this->db->get('online_app');
        $result3 = $query3->result_array();

        if($result3[0]['cnt'] > 0){
            return '帶班階段';
        }

        $this->db->select('count(1) cnt');
        $this->db->where('year',$data['year']);
        $this->db->where('class_id',$data['class_no']);
        $this->db->where('term',$data['term']);
        $query4 = $this->db->get('room_use');
        $result4 = $query4->result_array();

        if($result4[0]['cnt'] > 0){
            return '建班階段';
        }

        $this->db->select('count(1) cnt');
        $this->db->where('year',$data['year']);
        $this->db->where('class_no',$data['class_no']);
        $this->db->where('term',$data['term']);
        $query5 = $this->db->get('require');
        $result5 = $query5->result_array();

        if($result5[0]['cnt'] > 0){
            return '規劃階段';
        }
        return '';
    }

    public function getSignatureLinks($class_info, $signatures, $email_list)
    {
        $links = [];
        $teachers = $this->getTeachersEmail($class_info, $signatures);
        if ($teachers === false) return [];

        foreach($teachers as $teacher){
            /*
                查詢 teahcer id email有沒有在寄送名單 
                如果有就加上 電子簽章連結                
            */
            $check = array_search($teacher->email, $email_list);
            $check2 = array_search($teacher->email2, $email_list);
            $token = "{$class_info['year']}-{$class_info['class_no']}-{$class_info['term']}-{$teacher->id}-{$teacher->class_name}";
            $token = DES::encode($token, 'DE4LKM');

            if ($check !== false){
                $links[$check] = "<a href='https://dcsdcourse.taipei.gov.tw/base/api/signature.php?token={$token}'><button>講義授權電子簽名</button></a>";
                // $links[$check] = "<a  style='background-color: #ffbb73;  font-weight:bold; border: none; color: white; padding: 15px 32px; text-align: center; text-decoration: none; display: inline-block; font-size: 16px; margin: 4px 2px; cursor: pointer;' href='http://{$_SERVER['HTTP_HOST']}/base/api/signature.php?token={$token}'>講義授權電子簽名</a>";
                // $links[$check] = "<input type='button' value='講義授權電子簽名' onclick=\"location.href='http://{$_SERVER['HTTP_HOST']}/base/api/signature.php?token={$token}\">";
                unset($email_list[$check]); //可能會有相同 mail 所以加過的先移除避免下次又找到
            }
            if ($check2 !== false){
                $links[$check2] = "<a href='https://dcsdcourse.taipei.gov.tw/base/api/signature.php?token={$token}'><button>講義授權電子簽名</button></a>";
                // $links[$check2] = "<a  style='background-color: #ffbb73;  font-weight:bold; border: none; color: white; padding: 15px 32px; text-align: center; text-decoration: none; display: inline-block; font-size: 16px; margin: 4px 2px; cursor: pointer;'  href='http://{$_SERVER['HTTP_HOST']}/base/api/signature.php?token={$token}'>講義授權電子簽名</a>";
                // $links[$check2] = "<input type='button' value='講義授權電子簽名' onclick=\"location.href='http://{$_SERVER['HTTP_HOST']}/base/api/signature.php?token={$token}\">";
                unset($email_list[$check2]); //可能會有相同 mail 所以加過的先移除避免下次又找到
            } 
                      
        }
        // dd($links);
        return $links;
    }
    /*
        取得需要附加電子簽章的講座email
        return boolean 
    */
    public function getTeachersEmail($class_info, $teacher_id)
    {
        if (empty($teacher_id)) return false;

        if (is_array($teacher_id)){
            for($i=0;$i<count($teacher_id);$i++){
                $teacher_id[$i] = $this->db->escape(addslashes($teacher_id[$i]));
            }
            $teacher_id = implode(",", $teacher_id);
        }

        $sql = "SELECT id, idno, email, email2, r.class_name
                FROM teacher
                JOIN (SELECT DISTINCT teacher_id, `year`, class_id, term FROM room_use WHERE `year` = ".$this->db->escape(addslashes($class_info['year']))." AND class_id = ".$this->db->escape(addslashes($class_info['class_no']))." AND term = ".$this->db->escape(addslashes($class_info['term'])).") t ON 
                    t.teacher_id = teacher.idno
                JOIN `require` r ON r.`year` = t.year AND r.class_no = t.class_id  AND r.term = t.term
                WHERE id IN (".$teacher_id.")";

        $query = $this->db->query($sql);
        // dd($this->db->last_query());
        return $query->result();
    }

    public function getFormList(){
        $this->db->select('id,name');
        $this->db->where('status',1);
        $this->db->where('type','0');
        $this->db->where('id !=','91');
        $query = $this->db->get('SV_Form');
        $result = $query->result_array();

        return $result;
    }

    public function getSvClassManagementFormCmid($id){
        $this->db->select('year,class_no,term');
        $this->db->where('seq_no',$id);
        $query = $this->db->get('require');
        $info = $query->result_array();

        $cmid = array();
        if(isset($info[0]['year']) && !empty($info[0]['year'])){
            $this->db->select('id,anonymous');
            $this->db->where('year',$info[0]['year']);
            $this->db->where('class',$info[0]['class_no']);
            $this->db->where('ladder',$info[0]['term']);
            $query = $this->db->get('SV_ClassManagement');
            $cmid = $query->result_array();
        }

        return $cmid;
    }

    public function getSvClassManagementFormid($cmid){
        $this->db->select('id,formName');
        $this->db->where('cmid',$cmid);
        $this->db->order_by('order');
        $query = $this->db->get('SV_ClassManagementForm');
        $result = $query->result_array();

        return $result;
    }

    //20211203 Roger 讓9a評估老師的預覽列印有班期資訊
    public function getcourseinfo9a($cmid){
        $this->db->select('cm.year,cm.class,cm.ladder,cm.name');
        $this->db->join('SV_ClassManagementForm cmf', 'cm.id = cmf.cmid');
        $this->db->where('cmf.cmid',$cmid);
        $this->db->group_by('cm.class');
        $query = $this->db->get('SV_ClassManagement cm');
        $result = $query->row();
        return $result;
    }

    public function getDefaultDate($id){
        $this->db->select('year,class_no,term');
        $this->db->where('seq_no',$id);
        $query = $this->db->get('require');
        $info = $query->result_array();

        if(isset($info[0]['year']) && !empty($info[0]['year'])){
            $this->db->select('use_date');
            $this->db->where('year',$info[0]['year']);
            $this->db->where('class_no',$info[0]['class_no']);
            $this->db->where('term',$info[0]['term']);
            $this->db->where('use_date is not null',null,false);
            $this->db->order_by('use_date desc');
            $query = $this->db->get('courseteacher');
            $result = $query->result_array();

            if(!empty($result)){
                return $result[0]['use_date'];
            } else {
                return '';
            }
            
        }

        return '';
    }
}

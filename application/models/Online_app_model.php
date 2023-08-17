<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Online_app_model extends MY_Model
{   
    public $table = 'online_app';
    public $pk = '';

    public function __construct()
    {
        parent::__construct();
    }   

    /*
    yn_sel 
    1:結訓 
    2:報名 
    3:選員 
    4:退訓 
    5:未報到
    6:取消報名 
    7:取消參訓 
    8:調訓    
    */

    public function getList($condition, $row = false){
        $this->db->select("oa.year, oa.term, oa.class_no, oa.st_no, oa.group_no, user.name, user.idno, r.class_name, bc.name bc_name")
                 ->from("online_app oa")
                 ->join('require r', 'r.year=oa.year AND r.class_no = oa.class_no AND r.term = oa.term')
                 ->join("bureau bc", "bc.bureau_id = oa.beaurau_id", 'left')
                 ->join("BS_user user", "user.idno = oa.id")
                 ->where("oa.year", $condition['year'])
                 ->where("oa.class_no", $condition['class_no']);

        if (isset($condition['term'])){
            $this->db->where("oa.term", $condition['term']);
        }   

        if (isset($condition['id'])){
            $this->db->where("upper(oa.id)", $condition['id']);
        }            

        if (isset($condition['yn_sel_not_in'])){
            $this->db->where_not_in("oa.yn_sel", $condition['yn_sel_not_in']);
        }   
             
        if (isset($condition['yn_sel_in'])){
            $this->db->where_in("oa.yn_sel", $condition['yn_sel_in']);
        }

        if (isset($condition['yn_sel'])){
            $this->db->where("oa.yn_sel", $condition['yn_sel']);
        }            

        $this->db->order_by('st_no');
        $query = $this->db->get();
        // dd($this->   db->last_query());
        if ($row) return $query->row();
        return $query->result();
    }

    public function getRetreat($class_info, $paginate = true){
        $vacation = $this->db->select("id, sum(hours) hours")
                             ->from('s_vacation')
                             ->where_in('va_code', ['01', '02', '03'])
                             ->where('class_no', $class_info['class_no'])
                             ->where('year', $class_info['year'])
                             ->where('term', $class_info['term'])
                             ->where('id is not null')
                             ->group_by('class_no, year, term, id')
                             ->get_compiled_select();
        $this->db->start_cache();       

        $this->db->select("oa.yn_sel, oa.st_no, oa.id, user.name, oa.memo, bc.name be_name, sv.hours, jt.name job_title")
                 ->from('online_app oa')
                 ->join('BS_user user', 'user.idno = oa.id', 'left')
                 ->join('bureau bc', 'bc.bureau_id = user.bureau_id', 'left')
                 ->join('job_title jt', 'jt.item_id = user.job_title', 'left')
                 ->join("require r", "r.year = '{$class_info['year']}' AND r.class_no='{$class_info['class_no']}' AND r.term = '{$class_info['term']}'", 'left')
                 ->join("({$vacation}) sv", "sv.id = oa.id AND sv.hours > {$class_info['retreat_standard']}", "left")
                 ->where('oa.class_no', $class_info['class_no'])
                 ->where('oa.year', $class_info['year'])
                 ->where('oa.term', $class_info['term'])
                 ->where_in("oa.yn_sel", [3,4,5,8,1])
                 ->where("r.quit_class is not null")
                 ->where("(ifnull(r.quit_class2, CEILING(r.range_real/r.quit_class)) < sv.hours OR oa.yn_sel = 4)");
        
        $this->db->stop_cache(); 
        //if ($paginate) $this->paginate();                
        $this->db->order_by("oa.st_no");
        
        $query = $this->db->get();
        $this->db->flush_cache(); 
        //$test=$query->result_array();
        //var_dump($test);
        

        return $query->result(); 
    }

    public function getLearnList($condition){
        // $this->db->cache_on();
        $this->db->select("
                    oa.year, oa.class_no, oa.term, oa.yn_sel, oa.st_no st_no, oa.id, oa.beaurau_id,
                    r.class_name, bc.name description, user.out_gov_name, ifnull(user.name,'') name, oa.memo,
                    vacation_date, CONCAT(
                                        substr( sv.from_time, 1, 2 ),
                                        ':',
                                        substr( sv.from_time, 3, 2 ),
                                        '-',
                                        substr( sv.to_time, 1, 2 ),
                                        ':',
                                    substr( sv.to_time, 3, 2 )) as time,sv.hours, sv.va_code,
                    v_count, CEILING(ifnull(r.range_real, r.range) / r.quit_class) AS standard, user.retirement,
                    row_number() over( partition by oa.st_no Order by vacation_date, from_time ) as va_sn,
                    CASE va_code WHEN '01' THEN '請假' WHEN '02' THEN '未請假' WHEN '03' THEN '未留宿' ELSE '' END va_code_text
                    ")
                 ->from('online_app oa')
                 ->join('require r', 'r.year = oa.year AND r.class_no = oa.class_no AND r.term = oa.term', 'left')
                 ->join('BS_user user', 'user.idno = oa.id', 'left')
                 ->join('bureau bc', 'bc.bureau_id = user.bureau_id', 'left')
                 ->join('s_vacation sv', 'sv.year = oa.year AND sv.class_no = oa.class_no AND sv.term = oa.term AND sv.va_code is not null AND sv.id = oa.id', 'left')
                 ->join('(SELECT year, class_no, term, id,COUNT(1) AS v_count FROM s_vacation GROUP BY id, term, year, class_no) vc',
                   'oa.year = vc.year and oa.class_no = vc.class_no and oa.term = vc.term and oa.id = vc.id', 'left')
                 ->join('out_gov og', 'og.id = user.idno', 'left')
                 ->where('oa.year', $condition['year'])
                 ->where('oa.class_no', $condition['class_no'])
                 ->where('oa.term', $condition['term'])
                 ->where("(oa.yn_sel in ('4', '5') OR (oa.yn_sel in ('3','8','1') AND sv.va_code in ('01', '02', '03')))");    

        $this->db->order_by('oa.st_no, vacation_date, from_time asc');           

        $query = $this->db->get();  

        

        return $query->result();
    }

    /* 異動互調 */
    public function exchange($class_info, $exchange_user){

        if (!empty($exchange_user[0]) && !empty($exchange_user[1])){
            $this->exchangeClear($class_info, $exchange_user);
            $data[0] = [
                "id" => $exchange_user[1]->idno, 
                "beaurau_id" => $exchange_user[1]->bureau_id
            ];
            $condition = $class_info;
            $condition['term'] = $exchange_user[0]->term;
            $condition['id'] = $exchange_user[0]->idno;

            $this->update($condition, $data[0]);
            $data[0]['term'] =  $exchange_user[1]->term;
            $data[1] = [
                "id" => $exchange_user[0]->idno, 
                "beaurau_id" => $exchange_user[0]->bureau_id
            ];
            
            $condition['term'] = $exchange_user[1]->term;
            $condition['id'] = $exchange_user[1]->idno;
            // dd($condition);
            $this->update($condition, $data[1]);
            $data[1]['term'] =  $exchange_user[0]->term;
            return $data;
        }else{
            return false;
        }
    }
    /* 互調時清除另一個班期報名紀錄 */
    public function exchangeClear($class_info, $exchange_user){
        $condition = $class_info;
        $condition['id'] = $exchange_user[0]->idno;
        $condition['term'] = $exchange_user[1]->term;

        $this->delete($condition);

        $condition['id'] = $exchange_user[1]->idno;
        $condition['term'] = $exchange_user[0]->term;

        $this->delete($condition);      
    }
    /* 取得這個身分證號在這門課的優先順序 */
    public function getInsertOrder($class_info, $bureau_id){
        $this->db->select("max(insert_order) insert_order")
                 ->from("online_app")
                 ->where($class_info)
                 ->where_not_in('yn_sel', ['6'])
                 ->where("beaurau_id",  $bureau_id);
        $query = $this->db->get();
        $row = $query->row();
        return empty($row->insert_order) ? 1 : $row->insert_order+1;
    }

    public function getCurrentClassPersonNo($class_info){
        $this->db->from("online_app")
                 ->where("year", $class_info['year'])
                 ->where("class_no", $class_info['class_no'])
                 ->where("term", $class_info['term'])
                 ->where_in(["1", "3", "8"]);
        return $this->db->count_all_results();

    }
    /**
     * 取得最新學號
     *
     * @param array $class_info 班期資訊 包含 year, class_no, term
     * @return array 最新學號
     */
    public function getNewStNo($class_info){
        $this->db->select("max(st_no) st_no")
                 ->from("online_app")
                 ->where("year", $class_info['year'])
                 ->where("class_no", $class_info['class_no'])
                 ->where("term", $class_info['term']);
        $query = $this->db->get();
        $row = $query->row();
        // 最新學號 = 目前最後學號+1
        return $row->st_no + 1;
    }

    /**
     * 取得某身分證號某班期的報名資訊
     *
     * @param array $class_info 班期資訊 包含 year, class_no, term
     * @param string $idno 身份證
     * @return array 報名資訊
     */
    public function getEnrollData($class_info, $idno){
        $condition = $class_info;
        $this->db->select("*")
                 ->from("online_app")
                 ->where("year", $class_info['year'])
                 ->where("class_no", $class_info['class_no'])
                 ->where("term", $class_info['term'])
                 ->where("id", $idno);        
        $query = $this->db->get();
        return $query->row();
    }

    /*

    */
    public function getVmTransaction($class_info, $bureau_id = null){
        $card_log = $this->db->select("gid id, count(*) count")
                             ->from("card_log")
                             ->where($class_info)
                             ->group_by("gid")
                             ->get_compiled_select();
        $this->db->start_cache();
        $this->db->select("oa.*, user.name user_name, cl.count count")
                 ->from("online_app oa")
                 ->join("require r", "r.year = oa.year AND r.term = oa.term AND r.class_no = oa.class_no", 'left')
                 ->join("BS_user user", "user.idno = oa.id", 'left')
                 ->join("bureau bc", "bc.bureau_id = user.bureau_id", 'left')
                 ->join("({$card_log}) cl", "cl.id = oa.id", 'left')                 
                 ->where_in("oa.yn_sel", ['3' ,'8'])
                 ->where("oa.year", $class_info['year'])
                 ->where("oa.class_no", $class_info['class_no'])
                 ->where("oa.term", $class_info['term'])
                 ->order_by("oa.st_no");

        if (isset($bureau_id)){
            $this->db->where("user.bureau_id", $bureau_id);
        }
        
        $this->db->stop_cache();
        // $this->paginate();
        $query = $this->db->get();
        $query=$query->result();
        $this->db->flush_cache();
        
        foreach($query as $row){
            $row->card_log_num=$this->getCardLog($row);
        }
            //var_dump($query);
        
        
        return $query;
    }
    public function getCardLog($attrs)
    {
        $this->db->select('count(1) as cnt');
        $this->db->where('year',$attrs->year);
        $this->db->where('class_no',$attrs->class_no);
        $this->db->where('term',$attrs->term);
        $this->db->where('gid',$attrs->id);
        $query=$this->db->get('card_log');
        $query=$query->result();

        return $query[0]->cnt;
    }


    /**
     * 取得某班期錄取名單
     *
     * @param array $class_info 班期資訊 包含 year, class_no, term
     * @return array 錄取名單
     */
    public function getEnroll($class_info){
        $this->db->select("oa.*, user.name user_name, bc.name bc_name")
                 ->from("online_app oa")
                 ->join("BS_user user", "user.idno = oa.id", 'left')
                 ->join("bureau bc", "bc.bureau_id = oa.beaurau_id", 'left')
                 ->join("out_gov og", "og.id = user.idno", 'left')
                 ->where("oa.year", $class_info['year'])
                 ->where("oa.class_no", $class_info['class_no'])
                 ->where("oa.term", $class_info['term'])
                 ->where_in("oa.yn_sel", [3,8,1])
                 ->order_by("oa.st_no");
        $query = $this->db->get();
        return $query->result();
    }

    /*
        取得某班報名成功學員的單位人事
    */
    public function getHA($class_info){
        $online_app = $this->db->select("oa.beaurau_id")
                               ->distinct()
                               ->from("online_app oa")
                               ->join("BS_user user", "user.idno = oa.id")
                               ->where("year", $class_info['year'])
                               ->where("class_no", $class_info['class_no'])
                               ->where("term", $class_info['term'])
                               ->where("yn_sel not in ('2', '6', '7')")
                               ->get_compiled_select();
        $this->db->select("user.name, user.co_empdb_poftel as office_tel, user.email, bc.name bc_name, user.co_usrnick")
                 ->from("BS_user user")
                 ->join("bureau bc", "user.bureau_id = bc.bureau_id", "left")
                 ->join("({$online_app}) oa", "oa.beaurau_id = user.bureau_id")
                 ->where("user.username LIKE", "%edap%");
        $query = $this->db->get();
        return $query->result();       
    }


    public function getStudent($class_info, $yn_sel = null, $nyn_sel = null){
        $this->db->select("oa.*, user.bureau_id stu_bureau_id,bureau_name,user.name,user.office_tel,user.office_email")
                 ->from("online_app oa")
                 ->join("BS_user user", "user.idno = oa.id")
                 ->join("require r", "r.year = oa.year AND r.term = oa.term AND r.class_no = oa.class_no", 'left')
                 ->join("bureau", "bureau.bureau_id = oa.beaurau_id", 'left')
                 ->where("oa.year", $class_info['year'])
                 ->where("oa.term", $class_info['term'])
                 ->where("oa.class_no", $class_info['class_no'])
                 ->order_by("oa.st_no");

        if ($yn_sel !== null){
            $this->db->where_in("yn_sel", $yn_sel);
        }
                 
        if ($nyn_sel !== null){
            $this->db->where_not_in("yn_sel", $nyn_sel);
        }

        $query = $this->db->get();
        //var_dump($class_info);
        //die();
        return $query->result();
    }

  


    public function getCancelList($class_info, $bureau_id){

        $this->db->select("*")
                 ->from("bureau")
                 ->where("bureau_id",$bureau_id);
        $query = $this->db->get();
        $bureau = $query->row();

        $this->db->select("
                    oa.st_no,
                    bureau.name bureau_name,
                    user.name user_name,
                    CASE 
                        WHEN oa.id LIKE '_1%' THEN '男'
                        WHEN oa.id LIKE '_2%' THEN '女'
                    END sex,
                    user.office_tel cell_phone,
                    t.name title
                 ")
                 ->from("online_app oa")
                 ->join("BS_user user", "user.idno = oa.id")
                 ->join("require r", "r.year = oa.year AND r.term = oa.term AND r.class_no = oa.class_no", 'left')
                 ->join("bureau", "bureau.bureau_id = oa.beaurau_id", 'left')
                 ->join("title t", "t.id = user.job_title")
                 ->where("yn_sel", 7)
                 ->where("r.year", $class_info['year'])
                 ->where("r.class_no", $class_info['class_no'])
                 ->where("r.term", $class_info['term'])
                 ->where("user.bureau_id", $bureau->bureau_id)
                 ->order_by("st_no");
        $query = $this->db->get();
        return $query->result();   

    }
    /*
        取得未選員名單
    */
    public function getNoRecord($class_info, $bureau_id){

        $this->db->select("*")
                 ->from("bureau")
                 ->where("bureau_id",$bureau_id);
        $query = $this->db->get();
        $bureau = $query->row();

        $this->db->select("
                    oa.st_no,
                    bureau.name bureau_name,
                    user.name user_name,
                    CASE 
                        WHEN oa.id LIKE '_1%' THEN '男'
                        WHEN oa.id LIKE '_2%' THEN '女'
                    END sex,
                    user.office_tel cell_phone,
                    t.name title
                 ")
                 ->from("online_app oa")
                 ->join("BS_user user", "user.idno = oa.id")
                 ->join("require r", "r.year = oa.year AND r.term = oa.term AND r.class_no = oa.class_no", 'left')
                 ->join("bureau", "bureau.bureau_id = oa.beaurau_id", 'left')
                 ->join("title t", "t.id = user.job_title")
                 ->where("yn_sel not in  ('1','3','4','5','6','7','8')")
                 ->where("r.year", $class_info['year'])
                 ->where("r.class_no", $class_info['class_no'])
                 ->where("r.term", $class_info['term'])
                 ->where("user.bureau_id", $bureau->bureau_id)
                 ->order_by("st_no");
        $query = $this->db->get();
        return $query->result();   

    } 
    /*
        錄取名冊
        $user 使用者權限判斷
    */
    public function getPassList($condition, $user){
        $now = new DateTime();
        $search_all_group = [1, 6, 15];
        $search_all = false;

        foreach($search_all_group as $gid){
            if(in_array($gid, $user['group_id'])){
                $search_all = true;
                break;
            }
        }
        $this->db->start_cache();
        $this->db->select("oa.*, r.class_name, DATE_FORMAT(r.start_date1, '%Y-%m-%d') start_date1, DATE_FORMAT(r.end_date1, '%Y-%m-%d') end_date1")
                 ->from("online_app oa")
                 ->join("BS_user user", "user.idno = oa.id")
                 ->join("require r", "r.year = oa.year AND r.term = oa.term AND r.class_no = oa.class_no", 'left')
                 ->where("oa.yn_sel in (3, 8)")
                 ->where("r.class_status in ('2', '3')")
                 ->where("r.co_open_member_sheet", 'Y')
                 ->where("co_sheet_open_sdate <= '{$now->format("Y-m-d")}'")
                 ->where("co_sheet_open_edate >= '{$now->format("Y-m-d")}'")
                 ->order_by("r.year, r.class_name, oa.term, oa.st_no");

        // 查詢是否限定個人
        if ($search_all == false){
            $this->db->where("user.username", $user['username']);
        }

        // 是否開班
        if (isset($condition['start'])){
            if ($condition['start'] == 1){
                $this->db->where("r.start_date1 <= '{$now->format("Y-m-d")}'");
            }else{
                $this->db->where("r.start_date1 > '{$now->format("Y-m-d")}'");
            }
        }

        // 單一條件區 開始
        if (isset($condition['class_no'])){
            $class_no = mb_strtolower($condition['class_no']);
            $this->db->where("upper(r.class_no) LIKE", "%{$class_no}%");
        }

        if (isset($condition['class_name'])){
            $class_name = mb_strtolower($condition['class_name']);
            $this->db->where("upper(r.class_name) LIKE", "%{$condition['class_name']}%");
        }

        if (isset($condition['year'])){
            $this->db->where("r.year LIKE", "%{$condition['year']}%");
        }
        $this->db->stop_cache();
        // 單一條件區 結束
        //$this->paginate();  
        $query = $this->db->get();

        $this->db->flush_cache(); 
        return $query->result();
    }

    public function getCourseUserList($class_info){
        $this->db->select("
                    user.bureau_id, oa.st_no, 
                    CASE oa.yn_sel 
                        WHEN '4' THEN '(退訓)' 
                        WHEN '5' THEN '(未報到)' 
                        ELSE '' 
                    END yn_sel, 
                    IFNULL(og.ou_gov, bureau.name) bureau_name, 
                    ct.description title, 
                    user.name, 
                    oa.group_no, 
                    CASE user.retirement 
                        WHEN '0' THEN '退休' 
                        ELSE '' 
                    END retirement,
                    CASE 
                        WHEN substr(user.idno,2,1) = '1' THEN '男'
                        WHEN substr(user.idno,2,1) = '2' THEN '女'
                    END sex
                    ")
                 ->from("online_app oa")
                 ->join("BS_user user", "user.idno = oa.id", "left")
                 ->join("bureau bureau", "bureau.bureau_id = oa.beaurau_id", "left")
                 ->join("out_gov og", "og.id = user.idno", "left")
                 ->join("code_table_his ct", "ct.type_id = '02' AND ct.item_id = user.job_title", "left")
                 ->where("oa.year", $class_info['year'])
                 ->where("oa.term", $class_info['term'])
                 ->where("oa.class_no", $class_info['class_no'])
                 ->where("oa.yn_sel not in ('2','6','7')")
                 ->order_by('oa.st_no');
        $query = $this->db->get();
        return $query->result();
    }

    /*
        取得尚未加入e大會員的學員
    */
    public function getUnRegister($condition)
    {
        $this->db->select("oap.id,
                           vaa.office_email as EMAIL,
                           vaa.co_empdb_poftel as office_tel,
                           vaa.name,
                           vaa.bureau_name")
                 ->from("`require` rr")
                 ->join("online_app oap", "rr.year = oap.year AND rr.term = oap.term AND rr.class_no = oap.class_no")
                 ->join("BS_user vaa", "oap.id = vaa.idno")
                 ->where("rr.is_mixed", 1)
                 ->where("oap.yn_sel", 8)
                 ->where("rr.class_no", $condition['class_no'])
                 ->where("rr.year", $condition['year'])
                 ->where("rr.term", $condition['term'])
                 ->group_by("oap.id, vaa.office_email, vaa.co_empdb_poftel, vaa.first_name, vaa.bureau_name")
                 ->order_by("oap.id");
        $query = $this->db->get();
        return $query->result();
    }

    /*
        取得尚未完成課程的學員
    */
    public function getUnFinish($condition)
    {
        $this->db->select("oap.id,
                           vaa.office_email as EMAIL,
                           vaa.co_empdb_poftel as office_tel,
                           vaa.name,
                           vaa.bureau_name,
                           rr.start_date1")
                 ->from("`require` rr")
                 ->join("online_app oap", "rr.year = oap.year AND rr.term = oap.term AND rr.class_no = oap.class_no")
                 ->join("BS_user vaa", "oap.id = vaa.idno")
                 ->where("rr.is_mixed", 1)
                 ->where("oap.yn_sel", 8)
                 ->where("rr.class_no", $condition['class_no'])
                 ->where("rr.year", $condition['year'])
                 ->where("rr.term", $condition['term'])
                 ->group_by("oap.id, vaa.office_email, vaa.co_empdb_poftel, vaa.first_name, vaa.bureau_name")
                 ->order_by("oap.id");
        $query = $this->db->get();
        return $query->result();
    }    

    public function getStuMixs($condition){
        $this->db->select("r.year, r.class_no, r.term, DATE_FORMAT(start_date1, '%Y-%m-%d') start_date1, DATE_FORMAT(end_date1, '%Y-%m-%d') end_date1, ro.class_name, ro.hours, teacher_name, ro.elearn_id, r.class_name require_name")
                 ->from("online_app")
                 ->join('require r', 'r.year = online_app.year AND r.class_no = online_app.class_no AND r.term = online_app.term')
                 ->join('require_online ro', 'ro.year = r.year AND ro.class_no = r.class_no AND ro.term = r.term');

        if (!empty($condition['year'])){
            $this->db->where('online_app.year', $condition['year']);
        }

        if (!empty($condition['month'])){
            $this->db->where('MONTH(r.start_date1)', $condition['month']);
        }

        if (!empty($condition['student_id'])){
            $this->db->where('online_app.id', $condition['student_id']);
        }

        $query = $this->db->get();
        // dd($this->db->last_query());
        return $query->result();
    }

    public function getStuMixsForGroup($condition){
        $this->db->select("r.year, r.class_no, r.term, DATE_FORMAT(ro.start_date, '%Y-%m-%d') start_date1, DATE_FORMAT(ro.end_date, '%Y-%m-%d') end_date1, ro.class_name, ro.hours, ro.elearn_id, r.class_name require_name, GROUP_CONCAT(CONCAT(`ro`.`class_name`, '&$2',ro.elearn_id) SEPARATOR ',') groupname, GROUP_CONCAT(DATE_FORMAT(ro.start_date, '%Y-%m-%d') SEPARATOR '<br>') groupstartdate, GROUP_CONCAT(DATE_FORMAT(ro.end_date, '%Y-%m-%d') SEPARATOR '<br>') groupenddate, GROUP_CONCAT(teacher_name SEPARATOR '<br>') teacher_name")
                 ->from("online_app")
                 ->join('require r', 'r.year = online_app.year AND r.class_no = online_app.class_no AND r.term = online_app.term')
                 ->join('require_online ro', 'ro.year = r.year AND ro.class_no = r.class_no AND ro.term = r.term');

        if (!empty($condition['year'])){
            $this->db->where('online_app.year', $condition['year']);
        }

        if (!empty($condition['month'])){
            $this->db->where('MONTH(r.start_date1)', $condition['month']);
        }

        if (!empty($condition['student_id'])){
            $this->db->where('online_app.id', $condition['student_id']);
        }

        $this->db->where('ro.elearn_id <>', -1);
        $this->db->group_by('ro.year, ro.class_no, ro.term, online_app.id');

        $query = $this->db->get();
        // dd($this->db->last_query());
        return $query->result();
    }

    public function getStudentArriveRemind($classinfo, $remindMemberEmails)
    {
        $now = new DateTime();
        $this->db->select('r.year, r.class_no, r.term, r.class_name, BS_user.`name`,  arrive_reminds.email, worker.name worker_name')
                 ->from('online_app')
                 ->join('require r', 'r.year = online_app.year AND r.class_no=online_app.class_no AND r.term=online_app.term')
                 ->join('BS_user', 'BS_user.idno = online_app.id')
                 ->join('arrive_reminds', 'arrive_reminds.member_id = BS_user.id')
                 ->join("BS_user worker", "worker.idno = r.worker", 'left')
                 ->where('r.year', $classinfo['year'])
                 ->where('r.class_no', $classinfo['class_no'])
                 ->where('r.term', $classinfo['term'])
                 ->where('arrive_reminds.remind_sdate <=', $now->format('Y-m-d'))
                 ->where('arrive_reminds.remind_edate >=', $now->format('Y-m-d'))
                 ->group_start()
                 ->where_in('BS_user.office_email', $remindMemberEmails)
                 ->or_where_in('BS_user.email', $remindMemberEmails)
                 ->group_end();
        $query = $this->db->get();
        return $query->result();
    }
}
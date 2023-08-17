<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Require_model extends MY_Model
{	
    public $table = 'require';
    public $pk = '';

    public function __construct()
    {
        parent::__construct();
    }	

    public function getList($condition = array(), $paginate = true, $order_by = null){
        $enroll_count = "SELECT year, class_no, term, count(*) count 
                         FROM online_app 
                         WHERE yn_sel 
                         IN ('3', '8', '1') 
                         GROUP BY year, class_no, term";
        
        if (isset($condition['student_idno']) || isset($condition['student_name'])){
            // 如果有設定學員身份證或學員姓名 才篩選錄取資訊是否包含
            $this->db->select("oa.year, oa.class_no, oa.term") 
                     ->distinct()
                     ->from("online_app oa")
                     ->where("oa.yn_sel IN ('3', '8', '1')")
                     ->join("BS_user user", "user.idno = oa.id");
            
            if (isset($condition['student_idno'])) $this->db->where("oa.id LIKE", "%{$condition['student_idno']}%" );
            if (isset($condition['student_name'])) $this->db->where("user.name LIKE", "%{$condition['student_name']}%" );

            $online_app = $this->db->get_compiled_select();
        }

        $this->db->start_cache();
        $sql = (isset($condition['select']))? $condition['select'] :'r.*, as.ext1 phone, enroll.count enroll';
        $this->db->select($sql)
                 ->from("require r")
                 ->join("({$enroll_count}) enroll", "enroll.year = r.year AND enroll.class_no = r.class_no AND enroll.term = r.term", 'left')
                 ->join("BS_user user", "user.idno = r.worker", 'left')
                 ->join("agent_set as", "as.item_id = user.idno", 'left')
                 ->where("r.is_cancel", 0)
                 ->where("(r.5a_is_cancel != 'Y' or r.5a_is_cancel is null)");

        if (isset($online_app)) {
            $this->db->join("({$online_app}) oa", "oa.year = r.year AND oa.class_no = r.class_no AND oa.term = r.term");
        }

        if (isset($condition['year'])) $this->db->where("r.year", $condition['year']);    
        if (isset($condition['idno'])) $this->db->where('r.worker', $condition['idno']);

        if (isset($condition['class_no'])){
            $condition['class_no'] = strtoupper($condition['class_no']);
            $this->db->where("UPPER(r.class_no) LIKE", "%{$condition['class_no']}%" );
        }

        if (isset($condition['class_name'])){
            $this->db->where("r.class_name LIKE", "%{$condition['class_name']}%");
        }

        $this->db->stop_cache();

        //if ($paginate) $this->paginate();
        $this->db->order_by('r.year, r.class_no, r.term');
        $query = $this->db->get();
        // dd($this->db->last_query());
        $this->db->flush_cache(); 
        return $query->result();
    }

    public function find($class_info, $select = null){
        $this->db->select("
                    r.*,
                    if (ifnull(r.quit_class2, 0) = 0,CEILING(r.range_real/r.quit_class),r.quit_class2 ) retreat_standard,
                    trim(ifnull(user.office_email, user.email)) worker_email,
                    user.name worker_name,
                    as.ext1 worker_sub_phone,
                    as.name agent_name,
                    as.ext2 agent_sub_phone,
                    s.sd_edate,
                    s.sd_edate_h_m,
                    s.sd_chgterm,
                    s.sd_cancel,
                    s.sd_another,
                    s.sd_change")
                 ->from("require r")
                 ->join("BS_user user", "user.idno=r.worker", "left")
                 ->join("agent_set as", "as.item_id = user.idno", "left")
                 ->join("stud_modify s", "s.year = r.year AND s.class_no = r.class_no AND s.term = r.term",'left');
        
        if (is_array($class_info)){
            if (isset($class_info['year'])) $this->db->where("r.year", $class_info['year']);
            if (isset($class_info['class_no'])) $this->db->where("r.class_no", $class_info['class_no']);
            if (isset($class_info['term'])) $this->db->where("r.term", $class_info['term']);
        }else{
            $this->db->where("r.seq_no", $class_info);
        }

        if ($select !== null){
            $this->db->select($select);
        }

        $query = $this->db->get();
        $require = $query->row();
        
        // 研習人數
        if (!empty($require)){
            $require->search_count =  $this->db->from("online_app")
                                               //->where($class_info)
                                               ->where('year',$require->year)
                                               ->where('class_no',$require->class_no)
                                               ->where('term',$require->term)
                                               ->where_in("yn_sel", [1, 3, 8])
                                               ->count_all_results();
                //var_dump($require->class_no);
                //var_dump($require->term);
                //var_dump($require->year);
                //var_dump($require->search_count);
                //die();
            //var_dump($class_info);
            

        }
        //var_dump($query->row());
        return $query->row();
    }

    public function getBureau($seq_no){

        $this->db->select("distinct ifnull(og.ou_gov, bc.name) name")
                 ->from("require r")
                 ->join("online_app oa", "oa.year = r.year AND oa.term = r.term AND oa.class_no = r.class_no", 'left')
                 ->join("BS_user user", "user.idno = oa.id", 'left')
                 ->join("bureau bc", "bc.bureau_id = user.bureau_id", 'left')
                 ->join("s_vacation sv", "sv.year = r.year AND sv.class_no = r.class_no AND sv.term = r.term and sv.id = oa.id", 'left')
                 ->join("out_gov og", "og.id = user.idno", 'left')
                 ->where("(oa.yn_sel in ('4', '5') or (oa.yn_sel IN ('1','3','8') AND sv.va_code in ('01','02','03')))");

        if (is_array($seq_no)){
            $this->db->where_in("r.seq_no", $seq_no);
        }else{
            $this->db->where("r.seq_no", $seq_no);
        }

        $query = $this->db->get();
        return $query->result();
    }

    public function isExchangeTerm($year, $class_no, $term){
        $this->db->select("*")
                 ->from("require r")
                 ->join("stud_modify sm", "sm.year = r.year AND sm.class_no = r.class_no AND sm.term = r.term")
                 ->join("require_list rl", "rl.year = r.year AND rl.class_no = r.class_no AND rl.term = r.term")
                 ->where("r.year", $year)
                 ->where("r.class_no", $class_no)
                 ->where("r.term", $term)
                 ->where_in("r.class_status",['2', '3'])
                 ->where("sm.sd_modify", 1)
                 ->where("sm.sd_change", 1)
                 ->where("rl.mail_mag_count >", 0)
                 ->where("ifnull(rl.isend), 'N' <> 'Y'");
        return $this->db->count_all_results() > 0;                 
    }

    public function checkErollmentCondition($idno, $class_info){
        $this->db->select("
                    require.is_start,
                    ifnull(require.limit_start, 'Y') as limit_start,
                    ifnull(require.limit1_start, 'Y') as limit1_start,
                    require.limit_id,
                    require.limit_id1
                  ")
                 ->from('require')
                 ->where($class_info);
        $query = $this->db->get();
        $require = $query->row();

        if (empty($require)){
            return false;
        }else{
            $result = [];
            if ($require->limit_start == "Y"){
                $result['limit1'] = $this->checkErollmentLimit1($idno, $class_info);
            }
            
            if ($require->limit1_start == "Y"){
                $result['limit2'] = $this->checkErollmentLimit2($idno, $class_info);
                
            }
            $result['limit3'] = $this->checkErollmentLimit3($idno, $class_info);


        }

        return $result;
        
    }

    public function checkErollmentLimit1($idno, $class_info){
        $this->db->select("ec.limit_year")
                 ->from("require r")
                 ->join("enroll_condition ec", "ec.id = r.limit_id")
                 ->where("r.year", $class_info['year'])
                 ->where("r.class_no", $class_info['class_no'])
                 ->where("r.term", $class_info['term'])
                 ->where("r.limit_id is not null");
        $query = $this->db->get();
        $require = $query->row();

        //有找到的話則為違反限制
        if (empty($require)){
            return [
                "status" => true
            ];
        }else{
            $limit_year = ($require->limit_year == 0) ? 99 : $require->limit_year;
            $year = date("Y") - 1911 - $limit_year;
            $this->db->select("count(id) count")
                     ->from("online_app")
                     ->where_in("yn_sel", ['2', '6', '7'])
                     ->where('id', $idno)
                     ->where('class_no', $class_info['class_no'])
                     ->where('year >= ', $year)
                     ->group_by("id");
            $query = $this->db->get();
            $counter = $query->row();
            if (empty($counter)){
                return [
                    "status" => true,
                ];
            }else{
                if ($limit_year >= 99){
                    return [
                        "status" => false,
                        "msg" => "違反參訓限制條件1-已修過則永久不得報名"
                    ];
                }else{
                    return [
                        "status" => false,
                        "msg" => "違反參訓限制條件1-{$limit_year}年內不得報名"
                    ];
                }
            }
        }
    }

    public function checkErollmentLimit2($idno, $class_info){
        
        $this->db->select("limited")
                 ->from("require r")
                 ->join("enroll_condition_2 ec2", 'ec2.group_id = r.limit_id1', 'left')
                 ->where("r.year", $class_info['year'])
                 ->where("r.class_no", $class_info['class_no'])
                 ->where('r.term', $class_info['term']);
        $query = $this->db->get();
        $limited = $query->row();
        $limit_counter = $limited->limited;

        if (!empty($limit_counter) && $limit_counter > 0){
            
        } else {
            return [
                "status" => true
            ];
        }

        $class_no_sql = $this->db->select("r.class_no")
                                 ->from("require r")
                                 ->join("enroll_condition_2 ec2", 'ec2.group_id = r.limit_id1', 'left')
                                 ->where("r.year", $class_info['year'])
                                 ->where("r.class_no", $class_info['class_no'])
                                 ->where('r.term', $class_info['term'])
                                 ->get_compiled_select();

        $this->db->select("count(*) count")
                 ->from("online_app oa")
                 ->where("oa.year", $class_info['year'])
                 ->where_not_in("oa.yn_sel", ['2', '6', '7'])
                 ->where("oa.id", $idno)
                 ->where("oa.class_no in ({$class_no_sql})")
                 ->group_by("id")
                 ->having("count(*) >= '{$limit_counter}'");
        $query = $this->db->get();
        $counter = $query->row();
        if (empty($counter)){
            return [
                "status" => true
            ];
        }else{
            return [
                "status" => false,
                "msg" => "違反參訓限制條件2-參訓數{$limit_counter}"
            ];                
        }
    }

    public function checkErollmentLimit3($idno, $class_info){
        $this->db->select("*")
                 ->from("enroll_condition_3 ec3")
                 ->join("require_limit3 rl3", "rl3.limit_id2 = ec3.id", "left")
                 ->where("rl3.year", $class_info['year'])
                 ->where("rl3.class_no", $class_info['class_no'])
                 ->where("rl3.term", $class_info['term'])
                 ->where("(limit2_start = 'Y' OR limit2_start is null)");
        $query = $this->db->get();
        $result = $query->result();
        if (empty($result)){
            return [
                "status" => true,
            ];                
        }else{
            foreach ($result as $row){
                $check_class_no_list = explode(',', $row->class_no_2);
                if ($row->condition == "in"){
                    if ($row->compare_type){
                        return $this->checkErollmentLimit3_1($idno, $check_class_no_list);
                    }else{
                        return $this->checkErollmentLimit3_2($idno, $check_class_no_list);
                    }
                }else{
                    if ($row->compare_type){
                        return $this->checkErollmentLimit3_3($idno, $check_class_no_list);
                    }else{
                        return $this->checkErollmentLimit3_4($idno, $check_class_no_list);
                    }
                }
            }
        }


    }

    public function checkErollmentLimit3_1($idno, $check_class_no_list){
        $this->db->select("count(*) count")
                 ->from("online_app")
                 ->where_in("yn_sel", ['2', '6', '7'])
                 ->where_in("class_no", $check_class_no_list)
                 ->where('id', $idno);
        $query = $this->db->get();
        $counter = $query->row();

        if ($counter->count == 0){
            $this->db->select("class_name")
                     ->distinct()
                     ->from("require")
                     ->where_in("class_no", $check_class_no_list);
            $query = $this->db->get();
            $requires = $query->result();
            $class_names = [];
            foreach($requires as $require){
                $class_names[] = $require->class_name; 
            }
            $class_name_list = implode(',', $class_names);
            return [
                "status" => false, 
                "msg" => "違反參訓限制條件3_1-需修過{$class_name_list}"
            ];                         
        }else{
            return [
                "status" => true
            ];    
        }
    }
    
    public function checkErollmentLimit3_2($id, $check_class_no_list){
        $this->db->select("class_name")
                 ->distinct()
                 ->from("require")
                 ->where_in("class_no", $check_class_no_list);
        $query = $this->db->get();
        $requires = $query->result();
        foreach($requires as $require){
            $class_names[] = $require->class_name; 
        }

        $this->db->select("count(*) count")
                 ->from("online_app oa")
                 ->join("require r", "oa.class_no = r.class_no")
                 ->where_in("yn_sel", ['2', '6', '7'])
                 ->where("id", $id)
                 ->where_in("r.class_name", $class_names);
        $query = $this->db->get();
        $counter = $query->row();

        if ($counter->count > 0){
            return [
                "status" => true, 
            ]; 
        }else{
            $class_name_list = implode(',', $class_names);
            return [
                "status" => false, 
                "msg" => "違反參訓限制條件3_2-需修過{$class_name_list}"
            ];
        }

    }

    public function checkErollmentLimit3_3($id, $check_class_no_list){
        $this->db->select("count(id)")
                 ->from("online_app oa")
                 ->join("require r", "oa.class_no = r.class_no")
                 ->where_in("yn_sel", ['2', '6', '7'])
                 ->where("id", $id)
                 ->where_in("class_no", $check_class_no_list);
        $query = $this->db->get();
        $counter = $query->row();
        if ($counter->count == 0){
            return [
                "status" => true
            ]; 
        }else{
            $this->db->select("distinct r.class_name")
                     ->from("require")
                     ->where_in("class_no", $check_class_no_list);            
            $query = $this->db->get();
            $result = $query->result();
            foreach($requires as $require){
                $class_names[] = $require->class_name; 
            }
            $class_name_list = implode(',', $class_names);
            return [
                "status" => false, 
                "msg" => "違反參訓限制條件3_3-已修過{$class_name_list}"
            ];
        }                 

    }

    public function checkErollmentLimit3_4($id, $check_class_no_list){
        $this->db->select("distinct r.class_name")
                 ->from("require")
                 ->where_in("class_no", $check_class_no_list);            
        $query = $this->db->get();
        $result = $query->result();
        foreach($requires as $require){
            $class_names[] = $require->class_name; 
        }

        $this->db->select("count(id) count")
                 ->from("online_app oa")
                 ->join("require r", "oa.class_no = r.class_no")
                 ->where_in("yn_sel", ['2', '6', '7'])
                 ->where("id", $id)
                 ->where_in("r.class_name", $class_names);
        $query = $this->db->get();
        $counter = $query->row();        
        if ($counter->count == 0){
            return [
                "status" => true, 
            ];             
        }else{
            $this->db->select("distinct r.class_name")
                        ->from("online_app oa")
                        ->join("require r", "r.class_no = oa.class_no =  AND oa.id='{$id}'")                         
                        ->where_in("class_no", $check_class_no_list);            
            $query = $this->db->get();
            $result = $query->result();
            foreach($requires as $require){
                $class_names[] = $require->class_name; 
            }
            $class_name_list = implode(',', $class_names);                
            return [
                "status" => false, 
                "msg" => "違反參訓限制條件3_4-已修過{$class_name_list}"
            ];
        }

    }
    /* 取得現在是第幾次報名 */
    public function getPriority($class_info){
        $this->db->select("count(*) count")
                 ->from("require")
                 ->where("class_no", $class_info['class_no'])
                 ->where("year", $class_info['year'])
                 ->where("term", $class_info['term'])
                 ->where("sysdate() < ifnull(apply_s_date2, sysdate() + INTERVAL 1 DAY )");
        $query = $this->db->get();
        return $query->row()->count;

    }

    public function getMaxClassPersonNo($class_info){
        $this->db->from("require r")
                 ->join("stud_modify sm", "sm.year = r.year AND sm.class_no = r.class_no AND sm.term = r.term")
                 ->where("r.year", $class_info['year'])
                 ->where("r.class_no", $class_info['class_no'])
                 ->where("r.term", $class_info['term']);
        return $this->db->count_all_results();
    }

    /*
        取得某班的所有期別資訊
    */
    public function getClassAllTermInfo($class_info){
        $this->db->select("*")
                 ->from("require")
                 ->where("year", $class_info['year'])
                 ->where("class_no", $class_info['class_no']);
        $query = $this->db->get();
        
        return $query->result();
    }

    

    public function getModifyInfo($class_info){

        $online_app_tcount = $this->db->select("year, class_no, term, count(*) count")
                               ->from("online_app")
                               ->where("year", $class_info['year'])
                               ->where("class_no", $class_info['class_no'])
                               ->where("term", $class_info['term'])
                               ->group_by("year, class_no, term")
                               ->get_compiled_select();
        $online_app_pcount = $this->db->select("year, class_no, term, count(*) count")
                               ->from("online_app")
                               ->where("year", $class_info['year'])
                               ->where("class_no", $class_info['class_no'])
                               ->where("term", $class_info['term'])
                               ->where_not_in("yn_sel", ['6'])
                               ->group_by("year, class_no, term")
                               ->get_compiled_select();                               

        $this->db->select("
                 r.year, r.class_no, r. term,r.worker,user.name as user_name,
                 r.class_name, 
                 r.start_date1, 
                 r.end_date1, 
                 user.name, 
                 as.ext1 phone,
                 user.co_empdb_poftel as office_tel,
                 sm.sd_cnt,
                 sm.sd_cancel,
                 sm.sd_change,
                 sm.sd_chgterm,
                 sm.sd_another,
                 CONCAT(IFNULL(sm.sd_edate, '2012-01-01'),' ',IFNULL(sm.sd_edate_h_m, '23:59')) dupdate,
                 oat.count tcount,
                 oap.count pcount
                 ")
                 ->from("require r")
                 ->join("BS_user user", "user.idno = r.worker", "left")
                 ->join("agent_set as", "as.item_id = user.idno", "left")
                 ->join("stud_modify sm", "sm.year = r.year AND sm.class_no = r.class_no AND sm.term = r. term", 'left')
                 ->join("({$online_app_tcount}) oat", "oat.year = r.year AND oat.class_no = r.class_no AND oat.term = r. term", 'left')
                 ->join("({$online_app_pcount}) oap", "oap.year = r.year AND oap.class_no = r.class_no AND oap.term = r. term", 'left')
                 ->where("r.year", $class_info['year'])
                 ->where("r.class_no", $class_info['class_no'])
                 ->where("r.term", $class_info['term']);
        $query = $this->db->get();      
        return $query->row();
    }
    /*
        取得調訓函發文單位
    */
    public function getPractice($condition){

        $this->db->select("r.year, r.class_no, r.term, r.class_name, ifnull(og.ou_gov ,bc.name) bc_name ")
                 ->distinct()
                 ->from("require r")
                 ->join("online_app oa", "oa.year = r.year AND oa.term = r.term AND oa.class_no = r.class_no", 'left')
                 ->join("BS_user user", "user.idno = oa.id", 'left')
                 ->join("bureau bc", "bc.bureau_id = user.bureau_id", 'left')
                 ->join("out_gov og", "og.id = user.idno", 'left');

        if (is_array($condition['seq_no'])){
            $this->db->where_in("seq_no", $condition['seq_no']);
        }else{
            $this->db->where("seq_no", $condition['seq_no']);
        }

        $query = $this->db->get();
        return $query->result();
    }

    /*
        10H 班期訊息及異動作業專用
    */
    public function getRequireInfoByStatus($condition, $bureau_id){

        $this->db->select("*")
                 ->from("bureau")
                 ->where("bureau_id",$bureau_id);
        $query = $this->db->get();
        $bureau = $query->row();

        // 各班級錄取人數 
        $gcount = $this->db->select("year, class_no, term, count(*) count")
                              ->from("online_app oa")
                              ->join("BS_user user", "oa.id = user.idno")
                              ->where("yn_sel in ('1', '3', '4', '5', '8')")
                              ->where("user.bureau_id", $bureau->bureau_id)
                              ->group_by("year, class_no, term")
                              ->get_compiled_select();
        //  
        $ccount = $this->db->select("year, class_no, term, count(*) count")
                              ->from("online_app oa")
                              ->join("BS_user user", "oa.id = user.idno")
                              ->where("yn_sel", 7)                    
                              ->where("user.bureau_id", $bureau->bureau_id)          
                              ->group_by("year, class_no, term")
                              ->get_compiled_select();
        //  
        $tcount = $this->db->select("year, class_no, term, count(*) count")
                              ->from("online_app oa")
                              ->join("BS_user user", "oa.id = user.idno") 
                              ->where("user.bureau_id", $bureau->bureau_id)                         
                              ->group_by("year, class_no, term")
                              ->get_compiled_select();
        //  未選員人數
        $ncount = $this->db->select("year, class_no, term, count(*) count")
                              ->from("online_app oa")
                              ->join("BS_user user", "oa.id = user.idno")
                              ->where("yn_sel in ('2', '3', '8')")    
                              ->where("user.bureau_id", $bureau->bureau_id)                          
                              ->group_by("year, class_no, term")
                              ->get_compiled_select();                              

        $nocount = $this->db->select("year, class_no, term, count(*) count")
                            ->from("online_app oa")
                            ->join("BS_user user", "oa.id = user.idno")
                            ->where("yn_sel not in ('1','3','4','5','6','7','8')")    
                            ->where("user.bureau_id", $bureau->bureau_id)                          
                            ->group_by("year, class_no, term")
                            ->get_compiled_select();    

        $rcount = $this->db->select("year, class_no, term, count(*) count")
                            ->from("online_app oa")
                            ->join("BS_user user", "oa.id = user.idno")
                            ->where("yn_sel in ('2','3')")    
                            ->where("user.bureau_id", $bureau->bureau_id)                          
                            ->group_by("year, class_no, term")
                            ->get_compiled_select();    
        
        $mail_log = $this->db->select("year, class_no, term, count(*) count")
                             ->from("mail_log")
                             ->where("mail_type", 3)
                             ->group_by("year, class_no, term")
                             ->get_compiled_select();                             
        $this->db->start_cache();
        $this->db->select("
                    r.*, 
                    ifnull(gcount.count, 0) gcount,
                    ifnull(ccount.count, 0) ccount,
                    ifnull(tcount.count, 0) tcount,
                    ifnull(ncount.count, 0) ncount,
                    ifnull(nocount.count, 0) nocount,
                    ifnull(rcount.count, 0) rcount,
                    s.sd_modify,
                    s.sd_edate,
                    s.sd_edate updateday, 
                    CASE WHEN sd_edate_h_m = '2400' THEN '23:59'
                        WHEN length(sd_edate_h_m) = 4 THEN concat(substr(s.sd_edate_h_m,1,2), ':', substr(s.sd_edate_h_m,3,2))
                        WHEN s.sd_edate_h_m is null OR s.sd_edate_h_m = '' THEN '23:59'
                        ELSE s.sd_edate_h_m
                    END updateday2
                    ")
                 ->from("require r")
                 ->join("(${gcount}) gcount", "gcount.year = r.year AND gcount.class_no= r.class_no AND gcount.term = r.term", "left")
                 ->join("(${ccount}) ccount", "ccount.year = r.year AND ccount.class_no= r.class_no AND ccount.term = r.term", "left")
                 ->join("(${tcount}) tcount", "tcount.year = r.year AND tcount.class_no= r.class_no AND tcount.term = r.term", "left")
                 ->join("(${ncount}) ncount", "ncount.year = r.year AND ncount.class_no= r.class_no AND ncount.term = r.term", "left")
                 ->join("(${nocount}) nocount", "nocount.year = r.year AND nocount.class_no= r.class_no AND nocount.term = r.term", "left")
                 ->join("(${rcount}) rcount", "rcount.year = r.year AND rcount.class_no= r.class_no AND rcount.term = r.term", "left")
                 ->join("stud_modify s", "s.year = r.year AND s.class_no= r.class_no AND s.term = r.term", "left")
                 ->where("class_status in ('2', '3')");

        if (isset($condition['query_year'])){
            $this->db->where("r.year LIKE", "%{$condition['query_year']}%");
        }

        if (isset($condition['class_no'])){
            $this->db->where("r.class_no LIKE", "%{$condition['class_no']}%");
        }

        if (isset($condition['class_name'])){
            $this->db->where("r.class_name LIKE", "%{$condition['class_name']}%");
        }

        if (isset($condition['query_month'])){
            $query_date = ((int)$condition['query_year']+1911)."-".$condition['query_month']."-01";
            $this->db->where("r.start_date1 >=", $query_date);
        }

        switch ($condition['query_type']) {
            case '1': // 查詢(已發調訓通知且有錄取人員之班期)
                $this->db->join("({$mail_log}) mail_log", "mail_log.year = r.year AND mail_log.class_no= r.class_no AND mail_log.term = r.term");
                $this->db->where("gcount.count > 0");
                break;      
            case '2': // 查詢(已發調訓通知之所有班期)          
                $this->db->join("({$mail_log}) mail_log", "mail_log.year = r.year AND mail_log.class_no= r.class_no AND mail_log.term = r.term");

                break;            
            case '3': // 查詢(已報名尚未開辦之班期)
                $this->db->join("({$mail_log}) mail_log", "mail_log.year = r.year AND mail_log.class_no= r.class_no AND mail_log.term = r.term", 'left');
                $this->db->where("mail_log.year is null");
                $this->db->where("rcount.count > 0");

                break;  
            case '4': // 查詢(已報名但取消開班之班期)
                $this->db->where("r.is_cancel = '1'");
                $this->db->where("ncount.count > 0");
                break; 
            default:
                # code...
                break;
        }  

        $this->db->stop_cache();
        //$this->paginate();
        $query = $this->db->get();

        $this->db->flush_cache(); 
        return $query->result();      
    }

    public function getRequireFile($class_info){
        $this->db->select("file_path")
                 ->from("require_file")
                 ->where("year", $class_info['year'])
                 ->where("class_no", $class_info['class_no'])
                 ->where("term", $class_info['term']);
        $query = $this->db->get();            
        return $query->result();
    }

    /*
        取得某班期的線上課程
    */
    public function getOnlineRequire($condition){
        $this->db->select("*")
                 ->from("require_online")
                 ->where("year", $condition['year'])
                 ->where("class_no", $condition['class_no'])
                 ->where("term", $condition['term']);
        $query = $this->db->get();
        return $query->result();
    }

    public function getTeacherFirstSchedule($requires){
        if (count($requires) == 0) return [];

        $requires = array_map(function($require){
            return "(ru.`year` = {$require['year']} AND ru.class_id = '{$require['class_no']}' AND ru.term = {$require['term']})";
        }, $requires);

        $requires = join(" OR ", $requires);

        $sql = "SELECT first_course.`year`, first_course.class_id as class_no, first_course.term, first_course.teacher_id, first_course.use_date, course_code.name course_name, first_course.from_time, first_course.to_time, cr.room_name
                FROM (
                    SELECT ru.`year`, ru.class_id, ru.term, ru.teacher_id, ru.use_date, min(from_time) from_time, max(to_time) to_time, pt.course_code , ru.room_id
                    FROM room_use ru
                    JOIN (
                        SELECT `year`, class_id, term, teacher_id, min(use_date) use_date
                        FROM room_use
                        GROUP BY `year`, class_id, term, teacher_id
                    ) ru2 ON ru2.`year` = ru.`year` AND 
                             ru2.class_id = ru.class_id AND 
                             ru2.term = ru.term AND 
                             ru2.teacher_id = ru.teacher_id AND 
                             ru2.use_date = ru.use_date
                    JOIN periodtime pt ON pt.`year` = ru.`year` AND 
                                      pt.class_no = ru.class_id AND 
                                      pt.term = ru.term AND 
                                      pt.id = ru.use_period AND
                                      pt.course_date = ru.use_date AND
                                      pt.course_code = ru.use_id AND
                                      pt.room_id = ru.room_id
                    WHERE {$requires}
                    GROUP BY ru.`year`, ru.class_id, ru.term, ru.teacher_id, ru.use_date
                ) first_course
                JOIN course_code ON course_code.item_id = first_course.course_code
                LEFT JOIN venue_information cr ON cr.room_id = first_course.room_id
                ";
        
        $query = $this->db->query($sql);
        return $query->result();
    }

    public function updateLearnSend($params){
        $this->db->set('learn_send','1');
        $this->db->where('year',$params['year']);
        $this->db->where('class_no',$params['class_no']);
        $this->db->where('term',$params['term']);

        if($this->db->update('require')){
            return true;
        }

        return false;
    }
    
    public function updateSelectNumber($conditions,$select_number=0){
        $this->db->set('select_number',$select_number);
        $this->db->where('year',$conditions['year']);
        $this->db->where('class_no',$conditions['class_no']);
        $this->db->where('term',$conditions['term']);

        if($this->db->update('require')){
            return true;
        }

        return false;
    }
}
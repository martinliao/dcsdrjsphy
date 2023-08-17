<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class stud_modifylog_model extends MY_Model
{	
    public $table = 'stud_modifylog';
    public $pk = '';

    public function __construct()
    {
        parent::__construct();
    }	

    public function getList($class_info){
        // $this->db->start_cache();
        // $this->db->select("sml.id, sml.modify_item, sml.modify_log, max(modify_date)")
        //          ->from("stud_modifylog sml")
        //          ->join("BS_user user", "user.idno = sml.id", 'left')
        //          ->join("BS_user o_user", "user.idno = sml.o_id", 'left')
        //          ->where("sml.year", $class_info['year'])
        //          ->where("sml.class_no", $class_info['class_no'])
        //          ->where("sml.term", $class_info['term'])
        //          ->group_by("sml.id, sml.modify_item, sml.modify_log");

        // $this->db->stop_cache();
        // $this->paginate(); 
        // $query = $this->db->get();
        // $this->db->flush_cache();
        // return $query->result();
    }

    public function getModifyLogByRequire($class_info)
    {   

        $query_condition1 = "modifylog_ids.year = '{$class_info['year']}' AND modifylog_ids.class_no = '{$class_info['class_no']}' AND modifylog_ids.term = '{$class_info['term']}'";
        $query_condition2 = "smlog.year = '{$class_info['year']}' AND smlog.class_no = '{$class_info['class_no']}' AND smlog.term = '{$class_info['term']}'";
        $query_condition3 = "s1.year = '{$class_info['year']}' AND s1.class_no = '{$class_info['class_no']}' AND s1.term = '{$class_info['term']}'";
        $query_condition4 = "sm1.year = '{$class_info['year']}' AND sm1.class_no = '{$class_info['class_no']}' AND sm1.term = '{$class_info['term']}'";

        $query_condition_cancel_class = "SELECT max(modify_date) from stud_modifylog smlog WHERE {$query_condition2} AND modify_item = '取消開班' GROUP BY modify_item";

        $query_condition_id = "SELECT id, `year`, class_no, term FROM stud_modifylog smlog WHERE {$query_condition2} GROUP BY id, year, class_no, term";

        $query_condition_select = "SELECT max(modify_date) as modify_date, modify_log, n_term, st_no, id, modify_item, beaurau_id FROM stud_modifylog smlog WHERE {$query_condition2} AND modify_item='選員' GROUP BY modify_log, n_term, st_no, id, modify_item, beaurau_id";

        $query_condition_training = " select max(modify_date) as modify_date, modify_log, n_term, st_no, id, modify_item, beaurau_id from stud_modifylog smlog where {$query_condition2} and modify_item='調訓' group by modify_log, n_term, st_no, id, modify_item, beaurau_id ";

        //找取消開班後的那幾筆
        $query_condition_enrollment = "SELECT 
            tt.enrollment_date, sm1.beaurau_id AS ENROLLMENT_BUREAU_ID, sm1.MODIFY_LOG, sm1.N_TERM, sm1.st_no, tt.id, tt.cancel_enrollment_date, SM2.beaurau_id as CANCEL_ENROLLMENT_BUREAU_ID 
            FROM
            (select 
                s1.modify_date AS enrollment_date, s1.id, min(S2.modify_date) AS cancel_enrollment_date, s1.modify_item  
            from 
                stud_modifylog s1
                LEFT JOIN stud_modifylog S2 on s1.year=S2.year AND s1.class_no=S2.class_no AND s1.term=S2.term AND s1.id = S2.id AND S2.modify_item = '取消報名' and s1.modify_date < S2.modify_date
                WHERE 
                {$query_condition3} AND 
                s1.modify_item='報名' AND 
                s1.modify_date > IFNULL(( {$query_condition_cancel_class} ), str_to_date('2001-01-01', '%Y-%m-%d %H:%i:%s'))
            group by s1.modify_date, s1.id, s1.modify_item ) tt 
            left join stud_modifylog sm1 on tt.id = sm1.id AND tt.enrollment_date = sm1.modify_date AND sm1.modify_item = '報名'
            left join stud_modifylog SM2 on tt.id = SM2.id AND tt.cancel_enrollment_date = SM2.modify_date AND SM2.modify_item = '取消報名'
        WHERE 
            {$query_condition4}
        ";  

        $sql = "SELECT * FROM ( SELECT a.*, @rownum := @rownum + 1 AS num FROM
        (SELECT 
            modifylog_ids.id,
            IF (online_app.st_no IS NULL, 999, online_app.st_no) as st_no,
            online_app.yn_sel,
            IFNULL(BS_user.name, modifylog_ids.id) as name,
            STUDENT_bureau_code. NAME AS student_beaurau_name,
            T.name AS title,
            DATE_FORMAT(LOG1.enrollment_date,'%Y-%m-%d %H:%i:%s') AS enrollment_date,
            ENROLLMENT_bureau_code.name AS enrollment_bureau_name,
            DATE_FORMAT(LOG1.cancel_enrollment_date,'%Y-%m-%d %H:%i:%s') AS cancel_enrollment_date,
            CANCEL_ENROLLMENT_bureau_code.name AS cancel_enrollment_bureau_name,
            DATE_FORMAT(LOG3.modify_date,'%Y-%m-%d %H:%i:%s') AS select_date,
            LOG3.MODIFY_LOG AS select_type,
            LOG3.N_TERM AS select_from_term,
            LOG3.st_no AS select_stno,
            DATE_FORMAT(LOG4.modify_date,'%Y-%m-%d %H:%i:%s') AS modify_term_from_date,
            LOG4.n_term AS modify_term_from_term,
            MODIFY_TERM_FROM_bureau_code.name AS modify_term_from_bureau_name,
            DATE_FORMAT(LOG5.modify_date,'%Y-%m-%d %H:%i:%s') AS modify_term_to_date,
            LOG5.n_term AS modify_term_to_term,
            MODIFY_TERM_TO_bureau_code.name AS modify_term_to_bureau_name,
            DATE_FORMAT(LOG6.modify_date,'%Y-%m-%d %H:%i:%s') AS exchange_from_date,
            LOG6.o_id AS exchange_from_id,
            exchange_from_account.name AS exchange_from_name,
            EXCHANGE_FROM_bureau_code.name AS exchange_from_bureau_name,
            DATE_FORMAT(LOG7.modify_date,'%Y-%m-%d %H:%i:%s') AS exchange_to_date,
            LOG7.id AS exchange_to_id,
            exchange_to_account.name AS exchange_to_name,
            LOG7.n_term AS exchange_to_term,
            exchange_to_bureau_code.name AS exchange_to_bureau_name,
            DATE_FORMAT(LOG8.modify_date,'%Y-%m-%d %H:%i:%s') AS modify_student_from_date,
            LOG8.o_id AS modify_student_from_id,
            MODIFY_STUDENT_FROM_BUREAU.name AS modify_student_from_bureau,
            MODIFY_STUDENT_FROM_ACCOUNT.name AS modify_student_from_name,
            DATE_FORMAT(LOG9.modify_date,'%Y-%m-%d %H:%i:%s') AS modify_student_to_date,
            LOG9.o_id AS modify_student_to_id,
            MODIFY_STUDENT_TO_ACCOUNT.name AS modify_student_to_name,
            modify_student_to_bureau_code.name AS modify_student_to_bureau,
            DATE_FORMAT(LOG10.modify_date,'%Y-%m-%d %H:%i:%s') AS delete_student_date,
            DELETE_STUDENT_bureau_code.name AS delete_student_bureau_name,
            DATE_FORMAT(LOG11.modify_date,'%Y-%m-%d %H:%i:%s') AS training_date, 
            LOG10.modify_log AS delete_modify_log
        FROM ({$query_condition_id}) modifylog_ids
        left join ({$query_condition_enrollment}) LOG1 on LOG1.id = modifylog_ids.id 
        left join ({$query_condition_select}) LOG3 on LOG3.id = modifylog_ids.id 
        left join stud_modifylog LOG4 on modifylog_ids.year=LOG4.year and modifylog_ids.class_no=LOG4.class_no and modifylog_ids.term=LOG4.term and LOG4.id = modifylog_ids.id and LOG4.modify_item='換期' and LOG4.modify_log='from'
        left join stud_modifylog LOG5 on modifylog_ids.year=LOG5.year and modifylog_ids.class_no=LOG5.class_no and modifylog_ids.term=LOG5.term and LOG5.id = modifylog_ids.id and LOG5.modify_item='換期' and LOG5.modify_log='to'
        left join stud_modifylog LOG6 on modifylog_ids.year=LOG6.year and modifylog_ids.class_no=LOG6.class_no and modifylog_ids.term=LOG6.term and LOG6.id = modifylog_ids.id and LOG6.modify_item='互調'
        left join stud_modifylog LOG7 on modifylog_ids.year=LOG7.year and modifylog_ids.class_no=LOG7.class_no and modifylog_ids.term=LOG7.term and LOG7.o_id = modifylog_ids.id and LOG7.modify_item='互調'
        left join stud_modifylog LOG8 on modifylog_ids.year=LOG8.year and modifylog_ids.class_no=LOG8.class_no and modifylog_ids.term=LOG8.term and LOG8.id = modifylog_ids.id and LOG8.modify_item='換員' and LOG8.modify_log='from'
        left join stud_modifylog LOG9 on modifylog_ids.year=LOG9.year and modifylog_ids.class_no=LOG9.class_no and modifylog_ids.term=LOG9.term and LOG9.id = modifylog_ids.id and LOG9.modify_item='換員' and LOG9.modify_log='to'
        left join stud_modifylog LOG10 on modifylog_ids.year=LOG10.year and modifylog_ids.class_no=LOG10.class_no and modifylog_ids.term=LOG10.term and LOG10.id = modifylog_ids.id and LOG10.modify_item='取消'
        left join ({$query_condition_training}) LOG11 on LOG11.id = modifylog_ids.id 
        left join online_app on modifylog_ids.id = online_app.id and modifylog_ids.year = online_app.year and modifylog_ids.class_no = online_app.class_no and modifylog_ids.term = online_app.term
        left join BS_user on modifylog_ids.id = BS_user.idno
        left join BS_user exchange_from_account on LOG6.o_id = exchange_from_account.idno
        left join BS_user exchange_to_account on LOG7.id = exchange_to_account.idno
        left join BS_user MODIFY_STUDENT_FROM_ACCOUNT on LOG8.o_id = MODIFY_STUDENT_FROM_ACCOUNT.idno
        left join BS_user MODIFY_STUDENT_TO_ACCOUNT on LOG9.o_id = MODIFY_STUDENT_TO_ACCOUNT.idno
        left join bureau STUDENT_bureau_code on STUDENT_bureau_code.bureau_id = BS_user.bureau_id
        left join bureau ENROLLMENT_bureau_code on ENROLLMENT_bureau_code.bureau_id = LOG1.ENROLLMENT_BUREAU_ID
        left join bureau CANCEL_ENROLLMENT_bureau_code on CANCEL_ENROLLMENT_bureau_code.bureau_id = LOG1.CANCEL_ENROLLMENT_BUREAU_ID
        left join bureau EXCHANGE_FROM_bureau_code on EXCHANGE_FROM_bureau_code.bureau_id = LOG6.beaurau_id
        left join bureau exchange_to_bureau_code on exchange_to_bureau_code.bureau_id = LOG7.beaurau_id
        left join bureau MODIFY_TERM_FROM_bureau_code on MODIFY_TERM_FROM_bureau_code.bureau_id = LOG4.beaurau_id
        left join bureau MODIFY_TERM_TO_bureau_code on MODIFY_TERM_TO_bureau_code.bureau_id = LOG5.beaurau_id
        left join bureau DELETE_STUDENT_bureau_code on DELETE_STUDENT_bureau_code.bureau_id = LOG10.beaurau_id
        left join bureau MODIFY_STUDENT_FROM_BUREAU on MODIFY_STUDENT_FROM_BUREAU.bureau_id = LOG8.beaurau_id
        left join bureau modify_student_to_bureau_code on modify_student_to_bureau_code.bureau_id = LOG9.beaurau_id
        left join job_title T on T.item_id=BS_user.job_title) a , (SELECT @rownum := 0) b
        order by st_no,id

        ) b
        ORDER by st_no";

    $query = $this->db->query($sql);
    return $query->result();
    }

    public function getModify($class_info)
    {
        $this->db->select("*");
        $this->db->where('year',$class_info['year']);
        $this->db->where('class_no',$class_info['class_no']);
        $this->db->where('term',$class_info['term']);
        $query=$this->db->get('stud_modifylog');
        $query=$query->result_array();
        //var_dump($query);
        return $query;
    }

    public function getStudModify($class_info)
    {
        $this->db->select("sd_cnt");
        $this->db->where('year',$class_info['year']);
        $this->db->where('class_no',$class_info['class_no']);
        $this->db->where('term',$class_info['term']);
        $query=$this->db->get('stud_modify'); 
        $query=$query->result_array();
        return $query;
    }
    

}

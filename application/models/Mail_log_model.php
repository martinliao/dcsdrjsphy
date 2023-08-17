<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mail_log_model extends MY_Model
{	
    public $table = 'mail_log';
    public $pk = '';

    public function __construct()
    {
        parent::__construct();
    }	

    public function getList($condition, $paginate=true){
        $this->db->start_cache();
        $this->db->select("
                    log.*,
                    worker.name worker_name, 
                    creater.name creater_name, 
                    by_user.name by_user_name,
                    rh_worker.name rh_worker_name,
                    rh.class_name cancel_class_name,
                    r.class_name,
                    CASE log.mail_type 
                        WHEN '1' THEN '學生' 
                        WHEN '2' THEN '老師'
                        WHEN '3' THEN '人事'
                        WHEN '4' THEN '單位承辦人'
                        WHEN '5' THEN '調訓通知(補發)'
                        WHEN '6' THEN '學員(補發)'
                        WHEN '7' THEN '學員成績'
                        WHEN '8' THEN '取消開班'
                        WHEN '9' THEN '未錄取'
                        WHEN '11' THEN '人事(研習紀錄)'
                    END mail_to
                 ")
                 ->from("mail_log log")
                 ->join("require r", "r.year = log.year AND r.class_no = log.class_no AND r.term = log.term", "left")
                 ->join("BS_user worker", "worker.idno = r.worker", "left")
                 ->join("BS_user creater", "creater.username = log.cre_user", "left")
                 ->join("BS_user by_user", "by_user.idno = log.by_user", "left")
                 ->join("require_his rh", "rh.year = log.year AND rh.class_no = log.class_no AND rh.term = log.term", "left")
                 ->join("BS_user rh_worker", "rh_worker.idno = rh.worker", "left")
                 ->order_by("cre_date desc");
        
        if (isset($condition['year'])) $this->db->where("log.year", $condition['year']);
        if (isset($condition['class_name'])) $this->db->like("r.class_name", $condition['class_name'],'both');

        if (isset($condition['class_no'])) {
            $condition['class_no'] = strtoupper($condition['class_no']);
            $this->db->where("log.class_no", $condition['class_no']);
        }
        $this->db->stop_cache();
        
        //if ($paginate) $this->paginate();
        $query = $this->db->get();
        $this->db->flush_cache(); 
        return $query->result();

    }

    public function find($class_info, $mail_type = null){
        $this->db->select("body, body2, subject, UNIX_TIMESTAMP(cre_date) as chk_cre_date")
                 ->from("mail_log")
                 ->where("year", $class_info['year'])
                 ->where("class_no", $class_info['class_no'])
                 ->where("term", $class_info['term']);
        if (!empty($mail_type)){
            $this->db->where("mail_type", $mail_type);
        }
        $this->db->order_by("seq desc");
        $query = $this->db->get();
        return $query->row();
    }
    /*
        取得最新流水號
    */
    public function getSeqNo(){
        $this->db->select("max(seq) seq")
                 ->from("mail_log");
        $query = $this->db->get();
        return $query->row();
    }
}

<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Leave_model extends MY_Model
{
    public $table = 's_vacation';
    public $pk = 'seq_no';

    public function __construct()
    {
        parent::__construct();

        // $this->init($this->table, $this->pk);

    }
    public function getListInfo($class_info, $paginate = true, $other_where = false){
        $this->db->start_cache();       

        $this->db->select("sv.*, user.name, st_no, r.isend, oa.beaurau_id,oa.yn_sel")
                 ->from('s_vacation sv')
                 ->join('BS_user user', 'user.idno = sv.id', 'left')
                 ->join('require r', 'r.class_no = sv.class_no AND r.year = sv.year AND r.term = sv.term', 'left')
                 ->join('online_app oa', 'oa.class_no = sv.class_no AND oa.year = sv.year AND oa.term = sv.term AND oa.id = user.idno')
                 ->where('sv.class_no', $class_info['class_no'])
                 ->where('sv.year', $class_info['year'])
                 ->where('sv.term', $class_info['term'])
                 ->where_in('oa.yn_sel', ['1', '3', '4', '5', '8']);

        if (!empty($other_where)){
            $this->db->where($other_where);
        }

        $this->db->stop_cache();
        
        //if ($paginate) $this->paginate();
        $this->db->order_by('st_no, vacation_date, sv.from_time asc');

        $query = $this->db->get();
        $this->db->flush_cache(); 
        return $query->result(); 
        
    }

    public function getListInfoNew($class_info, $paginate = true, $other_where = false){
        $this->db->start_cache();       

        $this->db->select("sv.*, user.name, st_no, r.isend, oa.beaurau_id,oa.yn_sel")
                 ->from('s_vacation sv')
                 ->join('BS_user user', 'user.idno = sv.id', 'left')
                 ->join('require r', 'r.class_no = sv.class_no AND r.year = sv.year AND r.term = sv.term', 'left')
                 ->join('online_app oa', 'oa.class_no = sv.class_no AND oa.year = sv.year AND oa.term = sv.term AND oa.id = user.idno')
                //  ->join('leave_online lo', 'lo.idno = sv.id and lo.vacation_date = sv.vacation_date', 'left')
                 ->where('sv.class_no', $class_info['class_no'])
                 ->where('sv.year', $class_info['year'])
                 ->where('sv.term', $class_info['term'])
                 ->where_in('oa.yn_sel', ['1', '3', '4', '8']);

        if (!empty($other_where)){
            $this->db->where($other_where);
        }

        $this->db->stop_cache();
        
        //if ($paginate) $this->paginate();
        $this->db->order_by('st_no, sv.vacation_date, sv.from_time asc');

        $query = $this->db->get();
        $this->db->flush_cache(); 
        return $query->result(); 
        
    }

    public function getRequires($condition, $paginate = true, $idno){
        $this->db->start_cache();

        $this->db->select("r.*, date_format(r.start_date1, '%Y-%m-%d') start_date1_format")
                 ->from("require r")
                 // ->join('online_app oa', 'oa.class_no = r.class_no AND oa.year = r.year AND oa.term = r.term' ,'left') 不知道要幹麻先mark掉
                 ->where("r.year", $condition['year'])
                 ->where("r.is_cancel", 0)
                 ->where("(r.5a_is_cancel !='Y' or r.5a_is_cancel is null)");
        if ($idno){
            $this->db->where('r.worker', $idno);
        }


        if (!empty($condition['class_no'])){
            $condition['class_no'] = strtoupper($condition['class_no']);
            $this->db->where("UPPER(r.class_no) LIKE", "%{$condition['class_no']}%" );
        }

        if (!empty($condition['class_name'])){
            $condition['class_name'] = strtoupper($condition['class_name']);
            $this->db->where("UPPER(r.class_name) LIKE", "%{$condition['class_name']}%");
        }

        $this->db->stop_cache();


        //if ($paginate) $this->paginate();
        $this->db->order_by('r.year, r.class_no, r.term');
        $query = $this->db->get();

        $this->db->flush_cache(); 
        return $query->result();
    }

    public function getRoomUse($condition){
        $this->db->select("date_format(use_date, '%Y-%m-%d') use_date")
                 ->distinct()
                 ->from("room_use")
                 ->where($condition)
                 ->order_by('use_date');
        $query = $this->db->get();
        return $query->result();
    }

    public function getRequire($condition){
        $this->db->select("*")
                 ->from("require")
                 ->where($condition)
                 ->where("is_cancel", 0);  
        $query = $this->db->get();
        
        return $query->row();   
    }
    
    public function findStudent($student_info)
    {
        $this->db->select("online_app.year, online_app.class_no, online_app.term, online_app.st_no, BS_user.name, online_app.yn_sel, online_app.id")
                 ->from("online_app")
                 ->join("BS_user", "BS_user.idno = online_app.id")
                 ->where("year", $student_info['year'])
                 ->where("class_no", $student_info['class_no'])
                 ->where("term", $student_info['term'])
                 ->where("st_no", $student_info['st_no'])
                 ->where("yn_sel not in ('7')");
        $query = $this->db->get();
        return $query->row_array();
    }

    public function add_s_vacation($data)
    {
        $this->db->select("max(seq_no) seq_no")
                 ->from("s_vacation");
        $query = $this->db->get();
        $row = $query->row();
        if (empty($row->seq_no)){
            $data['seq_no'] = 1;
        }else{
            $data['seq_no'] = (int)$row->seq_no + 1 ;
        }
        $data = array_map('addslashes', $data);
        return $this->db->insert("s_vacation", $data);
    }

    public function delete_vacation($class_info ,$seq_no)
    {
        if (empty($class_info['year'])) return false;
        if (empty($class_info['class_no'])) return false;
        if (empty($class_info['term'])) return false;
        if (empty($seq_no)) return false;

        return $this->db->where($class_info)
                        ->where_in("seq_no", $seq_no)
                        ->delete("s_vacation");
        
    }

    public function getLearnRecord($params,$bureau_id = ''){
        $where = '';
        if(isset($params['class_name']) && !empty($params['class_name'])){
            $where .= sprintf(" and `require`.class_name LIKE %s",$this->db->escape("%%".addslashes($params['class_name'])."%%"));
        }

        if(isset($params['student_name']) && !empty($params['student_name'])){
            $where .= sprintf(" and BS_user.`name` = %s",$this->db->escape(addslashes($params['student_name'])));
        }

        if(isset($params['idno']) && !empty($params['idno'])){
            $where .= sprintf(" and online_app.id = %s",$this->db->escape(addslashes($params['idno'])));
        }

        if(isset($params['start_date']) && isset($params['end_date']) && !empty($params['start_date']) && !empty($params['end_date'])){
            $where .= sprintf(" and ((`require`.start_date1 BETWEEN %s 
                            AND %s) or (`require`.end_date1 BETWEEN %s 
                            AND %s))",$this->db->escape(addslashes($params['start_date'])),$this->db->escape(addslashes($params['end_date'])),$this->db->escape(addslashes($params['start_date'])),$this->db->escape(addslashes($params['end_date'])));
        } else if(isset($params['year']) && !empty($params['year'])){
            $where .= sprintf(" and online_app.year = %s",$this->db->escape(addslashes($params['year'])));
        }

        if(!empty($bureau_id)){
            $where .= sprintf(" and BS_user.bureau_id = %s",$this->db->escape(addslashes($bureau_id)));
        }

        $sql = sprintf("SELECT
                            online_app.st_no,
                            BS_user.`name`,
                            online_app.`year`,
                            online_app.`class_no`,
                            `require`.class_name,
                            online_app.term,
                            online_app.yn_sel,
                            online_app.mark as online_app_mark,
                            job_title.`name` AS job_name,
                            s_vacation.vacation_date,
                            s_vacation.seq_no, 
                            s_vacation.mark as vacation_mark
                        FROM
                            online_app
                            JOIN `require` ON online_app.`year` = `require`.`year` 
                            AND online_app.class_no = `require`.class_no 
                            AND online_app.term = `require`.term
                            LEFT JOIN s_vacation ON online_app.id = s_vacation.id  
                            AND online_app.`year` = s_vacation.`year` 
                            AND online_app.class_no = s_vacation.class_no 
                            AND online_app.term = s_vacation.term
                            JOIN BS_user ON online_app.id = BS_user.idno
                            JOIN job_title ON BS_user.job_title = job_title.item_id 
                        WHERE
                            `require`.learn_send = 1 
                        AND online_app.yn_sel in (1,3,4,5,8)
                            %s
                        GROUP BY
                            online_app.`year`,
                            online_app.class_no,
                            online_app.term,
                            online_app.id,
                            s_vacation.vacation_date",$where); 
        
        $query = $this->db->query($sql);
        $result = $query->result_array();

        return $result;
    }

    public function getLearnList($condition){
        // $this->db->cache_on();
        $this->db->select("
                    oa.year, oa.class_no, oa.term, oa.yn_sel, oa.st_no st_no,
                    r.class_name, ifnull(og.ou_gov, bc.name) description, ifnull(user.name,'') name, oa.memo,
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
                 ->where('oa.term', $condition['term']);
                 
        if(!empty($condition['vacation_date'])){
            $this->db->where("sv.vacation_date",$condition['vacation_date']);
        }

        if(!empty($condition['id'])){
            $this->db->where("sv.seq_no",$condition['id']);
        }

        if(!empty($condition['no'])){
            $this->db->where("oa.st_no",$condition['no']);
        }
                 
        $this->db->order_by('oa.st_no, vacation_date, from_time asc');           

        $query = $this->db->get();  

        

        return $query->result();
    }

    public function updateMark($markList,$status){
        $this->db->set('mark',$status);

        $check = explode('_', $markList);
        $cnt = count($check);
        if($cnt == 4){
            $this->db->where('year',$check[0]);
            $this->db->where('class_no',$check[1]);
            $this->db->where('term',$check[2]);
            $this->db->where('st_no',$check[3]);
            $table = 'online_app';
        } else if($cnt == 1){
            $this->db->where('seq_no',$check[0]);
            $table = 's_vacation';
        } else {
            return false;
        }

        if($this->db->update($table)){
            return true;
        }

        return false;
    }
}
<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

require_once APPPATH . "models/Common_model.php";
class Print_pay_list_model extends Common_model
{
    public function getPrintPayListData($start_date,$send_date,$count,$idno, $rightType,$rows="", $offset="")
    {
        $d1 = $start_date;
        $d2 = $send_date;

       

        $todayaddseven=$tomorrow = date("Y-m-d", strtotime("+ 7 day"));

        if(''==$d1)
        {
        
            $d1 = date("Y-m-d",mktime(0, 0, 0,date("m"),date("d")-date("w")+1));
        }
        if($d2=='')
        {
            $d2= $todayaddseven;
        }else{
            //$d2=$d2<$todayaddseven?$d2:$todayaddseven;
            if(strtotime($d2)>strtotime($todayaddseven)){
                $d2=$todayaddseven;
            }
        }
        
        
        $where = "and T.use_date between date(".$this->db->escape(addslashes($d1)).") and date(".$this->db->escape(addslashes($d2)).") ";

        $countIndex = 0;
        if($idno != 'admin') {
            for($i=0; $i<sizeof($rightType); $i++) {
                if($rightType[$i] != 9) {
                    $countIndex++;
                }
            }

            if($countIndex == sizeof($rightType)) {
                $where .= "AND T.worker_id=".$this->db->escape(addslashes($idno))."";
            }
        }

        $sql = "SELECT
            A .app_seq,
            T . YEAR,
            T .class_no,
            T .term,
            T .class_name,
            R.IS_CANCEL
        FROM
            hour_traffic_tax T
        LEFT JOIN hour_app A ON T .seq = A .seq 
        LEFT JOIN `require` R on R.SEQ_NO = T.seq
        WHERE
            (
                T .status = '待確認'
                OR (
                    A .app_seq IS NOT NULL
                    AND status IN (
                        '請款確認',
                        '市庫支票'
                    )
                )
            )
        {$where}
        GROUP BY
            A .app_seq,
            T . YEAR,
            T .class_no,
            T .term,
            T .class_name,
            R.IS_CANCEL
        ORDER BY
            A .app_seq desc";

         $limit = "";
         if($rows != "" && $offset != "") {
         $limit = " limit " . intVal($rows) . " offset " . intVal($offset);
         }
         else if($rows != "") {
         $limit = " limit " . intVal($rows);
         }
 
         $sql = $sql . " " . $limit;
        $rs = $this->db->query($sql);
        $rs = $this->QueryToArray($rs);
        for ($i=0; $i < sizeof($rs); $i++) { 
            $getStatus = $this->chkStatus($rs[$i]['app_seq']);
            $isStatusOK = ($getStatus == '待確認' ? 'N' : 'Y');
            $rs[$i]['getStatus']=$getStatus;
            $rs[$i]['isStatusOK']=$isStatusOK;
        }

        return $rs;

    }

    public function insertPrintPayListData($start_date,$send_date,$count,$chklist,$account)
    {
        $d1 = $start_date;
        $d2 = $send_date;
        if(''==$d1)
        {
        
            $d1 = date("Y-m-d",mktime(0, 0, 0,date("m"),date("d")-date("w")+1));
        }
        if(''==$d2)
        {
            $d2= date("Y-m-d",mktime(0, 0, 0,date("m"),date("d")-date("w")+7));
        }

       



        $where_date = " and use_date between date(".$this->db->escape(addslashes($d1)).") and date(".$this->db->escape(addslashes($d2)).")";
        //$where_date = " and use_date between {$d1} and {$d2}";
        $where2 = "";
        $arry = explode(",,",$chklist);
        for ($x=0;$x<count($arry);$x++){
            if ($arry[$x]!="")
            {
                $arryValue = explode("::",$arry[$x]);
                    if ($where2==""){
                        $where2 .= "select ".$this->db->escape(addslashes($arryValue[0])).",".$this->db->escape(addslashes($arryValue[1])).",".$this->db->escape(addslashes($arryValue[2]))." from dual ";
                    }
                    else{
                        $where2 .= "union all select ".$this->db->escape(addslashes($arryValue[0])).",".$this->db->escape(addslashes($arryValue[1])).",".$this->db->escape(addslashes($arryValue[2]))." from dual ";
                    }
            }
        }        
        $where2 = " (year, class_no, term) in ({$where2})";
        // 當天的第一筆
        $Dailyfirst1 = date('Ymd') . '0001';
        //儲存
        //#47552 實體系統-13A及13B已產生流水號的請款清冊、憑證資料沒有by流水號：增加請款流水號的判斷
        $sql = "insert into hour_app(APP_SEQ, SEQ, UPD_USER, UPD_DATE) " .
             "select ( SELECT CASE WHEN max(APP_SEQ)+1 is null THEN {$Dailyfirst1} ELSE max(APP_SEQ)+1 END FROM hour_app WHERE date_format(UPD_DATE,'%Y-%m-%d') = date_format( NOW(),'%Y-%m-%d') ), seq, '{$account}', NOW() from hour_traffic_tax " .
             "where {$where2} {$where_date} and status = '待確認' and seq not in (select seq from hour_app)";
        $this->db->query($sql);
        //var_dump($test);
        //die();

        // 紀錄第一次產生流水號的seq有哪些
        $this->db->query("insert into co_hourapp_log (APP_SEQ, SEQ, CRE_DATE, YEAR, CLASS_NO
        , TERM, CLASS_NAME, USE_DATE, TEACHER_ID, STATUS, BILL_DATE, WORKER_ID, ENTRY_DATE)
         select A.APP_SEQ, A.SEQ, now(), B.YEAR, B.CLASS_NO, B.TERM, B.CLASS_NAME, B.USE_DATE,
          B.TEACHER_ID, B.STATUS, B.BILL_DATE, B.WORKER_ID, B.ENTRY_DATE from hour_app A 
          left join hour_traffic_tax B on A.seq = B.seq 
          where A.seq in ( select seq from hour_traffic_tax 
          where {$where2} {$where_date} and status = '待確認' )");
        
          return "產生成功";
                
    }
    public function deletePrintPayListData($start_date,$send_date,$count,$chklist)
    {
        $d1 = $start_date;
        $d2 = $send_date;
        if(''==$d1)
        {
        
            $d1 = date("Y-m-d",mktime(0, 0, 0,date("m"),date("d")-date("w")+1));
        }
        if(''==$d2)
        {
            $d2= date("Y-m-d",mktime(0, 0, 0,date("m"),date("d")-date("w")+7));
        }
        $where_date = " and use_date between date(".$this->db->escape(addslashes($d1)).") and date(".$this->db->escape(addslashes($d2)).")";
        $arry = explode(",,",$chklist);
        for($i=0; $i<count($arry); $i++){
            if(trim($arry[$i]) != ''){
                $data = explode('::', $arry[$i]); // 201204030001::100::A00082::1 流水號::年度::班級代碼::期別
                //#47711 北e大 - 實體系統-13B刪除流水號，13A似未連動：增加刪除狀態是空值者
                $this->db->query("delete from hour_app where APP_SEQ = ".$this->db->escape(addslashes($data[0]))." and SEQ in (select SEQ from hour_traffic_tax where YEAR = ".$this->db->escape(addslashes($data[1]))." and CLASS_NO  = ".$this->db->escape(addslashes($data[2]))." and TERM = ".$this->db->escape(addslashes($data[3]))." and (status = '待確認' or status is null))");

            }
        }
        return("刪除成功");        
    }

    function getSEQNO($year, $class_no, $term, $d1, $d2){
        global $db;
        $sql = "select A.APP_SEQ from HOUR_TRAFFIC_TAX T left join hour_app A on T.SEQ = A.SEQ where T.YEAR='{$year}' and T.CLASS_NO='{$class_no}' and T.TERM='{$term}' and (A.DEL_FLAG != 'Y' or A.DEL_FLAG is null) and T.USE_DATE between to_date('{$d1}','yyyy-mm-dd') and to_date('{$d2}','yyyy-mm-dd') group by APP_SEQ";
        $rtn_seq = $db->GetOne($sql);
        return trim($rtn_seq);
    }
    
    function chkStatus($app_seq) {
        $rs = $this->db->query("select status from hour_app A left join hour_traffic_tax T on A.seq = T.seq where A.app_seq = '{$app_seq}' group by T.status");
        $rs = $this->QueryToArray($rs);
        if($rs){
            for ($i=0; $i < sizeof($rs); $i++) { 
                $row=$rs[$i];
                if($row['status'] != '待確認' && $row['status'] != '已設定為不請款'){
                    return $row['status'];
                }
            }
        }
        return '待確認';
    }
    
    function getTaxByAppSeqs($seqs)
    {
        $this->db->select('hour_traffic_tax.*, DATE_FORMAT(hour_traffic_tax.use_date, "%Y-%m-%d") as u_date, hour_app.app_seq, DATE_FORMAT(hour_traffic_tax.start_date, "%Y-%m-%d") as sdate, DATE_FORMAT(hour_traffic_tax.end_date, "%Y-%m-%d") as edate, bank_code.name as bank_name, teacher.email, code_table.description');
        $this->db->join('hour_traffic_tax', 'hour_traffic_tax.seq = hour_app.seq');
        $this->db->join('bank_code', 'bank_code.item_id = hour_traffic_tax.teacher_bank_id');
        $this->db->join('teacher', 'teacher.idno = hour_traffic_tax.teacher_id and teacher.teacher = hour_traffic_tax.isteacher');
        $this->db->join('code_table', 'code_table.item_id = hour_traffic_tax.t_source');        
        $this->db->where_in('hour_app.app_seq', $seqs);
        $this->db->order_by('app_seq');
        $query = $this->db->get('hour_app');
        $data = [];

        foreach ($query->result() as $hour_traffic_tax){
            if (!isset($data[$hour_traffic_tax->app_seq])){
                $data[$hour_traffic_tax->app_seq] = [];
            }
            $data[$hour_traffic_tax->app_seq][] = $hour_traffic_tax;
        }

        return $data;
    }

    function getBYKey($key)
    {
        $this->db->select('hour_traffic_tax.*, DATE_FORMAT(hour_traffic_tax.use_date, "%Y-%m-%d") as u_date, bank_code.name as bank_name, teacher.email, code_table.description, hour_app.app_seq, type_code.description as class_type, DATE_FORMAT(hour_traffic_tax.start_date, "%Y-%m-%d") as sdate, DATE_FORMAT(hour_traffic_tax.end_date, "%Y-%m-%d") as edate');
        $this->db->join('hour_app', 'hour_app.seq = hour_traffic_tax.seq');
        $this->db->join('bank_code', 'bank_code.item_id = hour_traffic_tax.teacher_bank_id');
        $this->db->join('teacher', 'teacher.idno = hour_traffic_tax.teacher_id and teacher.teacher = hour_traffic_tax.isteacher');
        $this->db->join('code_table', 'code_table.item_id = hour_traffic_tax.t_source');
        // $this->db->join('require r', 'r.class_no = hour_traffic_tax.class_no AND r.term = hour_traffic_tax.term');
        $this->db->join('code_table type_code', 'type_code.TYPE_ID = "07" and type_code.ITEM_ID = hour_traffic_tax.ht_class_type', 'left');
        // $this->db->where($key);
        $this->db->where('hour_traffic_tax.seq', $key['seq']);
        $query = $this->db->get('hour_traffic_tax');
        return $query->row();
    }

    function updateCheck($seq)
    {
        return $this->db->where('seq', $seq)
                        ->update('hour_traffic_tax', ['ischeck' => 'Y']);
    }
}

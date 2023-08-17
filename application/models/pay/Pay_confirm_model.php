<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

require_once APPPATH . "models/Common_model.php";
class Pay_confirm_model extends Common_model
{
    public function getPayConfirmData($start_date,$send_date,$count,$idno, $rightType, $rows="", $offset="")
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
        $where = "WHERE NOT EXISTS (
            SELECT *
            FROM hour_app 
            JOIN hour_traffic_tax tax ON tax.seq = hour_app.seq
            WHERE hour_app.app_seq = a.app_seq AND tax.ischeck = 'N'
        )";

        $sql="SELECT b.* 
        FROM 
        (
            SELECT A .app_seq 
            FROM hour_app A 
            LEFT JOIN hour_traffic_tax T ON A .seq = T .seq 
            LEFT JOIN `require` R on R.SEQ_NO = A.seq and ifnull(R.IS_CANCEL,'0') = '0'
            WHERE T .STATUS = '待確認' 
            AND T .use_date BETWEEN DATE (".$this->db->escape(addslashes($d1)).") 
            AND DATE (".$this->db->escape(addslashes($d2)).") 
            GROUP BY A .app_seq 
            ORDER BY A .app_seq DESC
        ) a 
        LEFT OUTER JOIN 
        (
            SELECT A.app_seq, T.YEAR, T.class_no, T.term, T.class_name, R.IS_CANCEL, T.worker_id, COUNT(*) AS cnt 
            FROM hour_app A 
            LEFT JOIN hour_traffic_tax T ON A .seq = T .seq 
            LEFT JOIN `require` R ON R.SEQ_NO = A.seq and ifnull(R.IS_CANCEL,'0') = '0'
            WHERE T .STATUS = '待確認' 
            AND T .use_date BETWEEN DATE (".$this->db->escape(addslashes($d1)).") 
            AND DATE (".$this->db->escape(addslashes($d2)).") 
            GROUP BY A .app_seq, T.YEAR, T.class_no, T.term, T.class_name, R.IS_CANCEL
        ) b 
        ON a.app_seq = b.app_seq ";

        $countIndex = 0;
        if($idno != 'admin') {
            for($i=0; $i<sizeof($rightType); $i++) {
                if($rightType[$i] != 9) {
                    $countIndex++;
                }
            }

            if($countIndex == sizeof($rightType)) {
                $where .= " AND b.worker_id =".$this->db->escape(addslashes($idno))."";
            }
        }

        $limit = "";
        if($rows != "" && $offset != "") {
          $limit = " limit " . intVal($rows) . " offset " . intVal($offset);
        }
        else if($rows != "") {
          $limit = " limit " . intVal($rows);
        }

        $sql = $sql . " " . $where . " " . $limit;

        $rs = $this->db->query($sql);

        return $this->QueryToArray($rs);

    }

    public function savePayConfirmData($start_date,$send_date,$count,$chklist)
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

        $mtList = explode(",", $chklist);
        for($i=0; $i<count($mtList); $i++){
            $mtList[$i] = $this->db->escape(addslashes($mtList[$i]));
        }

        $tmp = "請款確認";
        $sql = "update hour_traffic_tax set status = '{$tmp}' " .  
            "where status = '待確認' "."and use_date between date(".$this->db->escape(addslashes($d1)).") and date(".$this->db->escape(addslashes($d2)).") "." "."and seq in ( select seq from hour_app where app_seq in (".implode(",", $mtList).") )";
        $this->db->query($sql);
        
        $sql = " update hour_app  set del_flag = null  where app_seq in (".implode(",", $mtList).")  ";
        $this->db->query($sql);
        
        return("確認成功");


    }

    public function checkPayConfirmDate($app_seq){
        $today = date('Y-m-d');
        
        $this->db->select('count(1) cnt');
        $this->db->join('hour_traffic_tax','hour_traffic_tax.seq = hour_app.seq');
        $this->db->where('hour_app.app_seq',$app_seq);
        $this->db->where('hour_traffic_tax.use_date <= ',$today);
        $query = $this->db->get('hour_app');
        $result = $query->result_array();

        if($result[0]['cnt'] > 0){
            return true;
        }

        return false;
    }

    public function insertPayConfirmData($start_date,$send_date,$count,$chklist,$username)
    {
        $where_date = " and use_date between date(".$this->db->escape(addslashes($start_date)).") and date(".$this->db->escape(addslashes($send_date)).")";
            $where2 = "";
            $arry = explode(",",$chklist);
            for ($x=0;$x<sizeof($arry);$x++){
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
                "select ( SELECT CASE WHEN max(APP_SEQ)+1 is null THEN {$Dailyfirst1} ELSE max(APP_SEQ)+1 END FROM hour_app 
                WHERE date_format(UPD_DATE,'%Y-%m-%d') = date_format(now(),'%Y-%m-%d') ), seq, ".$this->db->escape(addslashes($username)).", now() from hour_traffic_tax " .
                "where {$where2} {$where_date} and status = '待確認' and seq not in (select seq from hour_app)";
            //		echo $sql;
            $this->db->query($sql);

            // 紀錄第一次產生流水號的seq有哪些
            $this->db->query("insert into co_hourapp_log (APP_SEQ, SEQ, CRE_DATE, YEAR, CLASS_NO, TERM, CLASS_NAME, USE_DATE, TEACHER_ID, STATUS,
            BILL_DATE, WORKER_ID, ENTRY_DATE) select A.APP_SEQ, A.SEQ, now(), B.YEAR, B.CLASS_NO, B.TERM, B.CLASS_NAME, B.USE_DATE,
             B.TEACHER_ID, B.STATUS, B.BILL_DATE, B.WORKER_ID, B.ENTRY_DATE from hour_app A left join hour_traffic_tax B on A.seq = B.seq 
             where A.seq in ( select seq from hour_traffic_tax where {$where2} {$where_date} and status = '待確認' )");
            return("新增成功");    
    }

    public function deletePayConfirmData($start_date,$send_date,$count,$chklist)
    {
        $arry = explode(",,",$chklist);
        for($i=0; $i<sizeof($arry); $i++){
            if(trim($arry[$i]) != ''){
                $data = explode('::', $arry[$i]); // 201204030001::100::A00082::1 流水號::年度::班級代碼::期別
                //#47711 北e大 - 實體系統-13B刪除流水號，13A似未連動：增加刪除狀態是空值者
                $this->db->query("delete from hour_app where APP_SEQ = ".$this->db->escape(addslashes($data[0]))." and SEQ in (select SEQ from hour_traffic_tax 
                where YEAR = ".$this->db->escape(addslashes($data[1]))." and CLASS_NO  = ".$this->db->escape(addslashes($data[2]))." and TERM = ".$this->db->escape(addslashes($data[3]))." and (status = '待確認' or status is null))");

            }
        }
        return("刪除成功");

    }

}

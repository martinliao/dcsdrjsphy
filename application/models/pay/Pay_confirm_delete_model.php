<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

require_once APPPATH . "models/Common_model.php";
class Pay_confirm_delete_model extends Common_model
{
    public function getPayConfirmDeleteData($taker,$applyid,$start,$end,$count, $rows="", $offset="")
    {
        $workname = $taker;
        $appno    = $applyid;
        $d1 = $start;
        $d2 = $end;
        
        $where = "use_date between date(".$this->db->escape(addslashes($d1)).") and date(".$this->db->escape(addslashes($d2)).")";
        $where .= ($workname != "" ? " and NAME like ".$this->db->escape("%".addslashes($workname)."%")." " : "");
        $where .= ($appno != "" ? " and APP_SEQ = ".$this->db->escape(addslashes($appno))." " : "");

        $sql = "SELECT * FROM ( " . 
            "SELECT A1.APP_SEQ, COUNT(*) AS CNT, date(A1.UPD_DATE) AS USE_DATE, C.NAME FROM hour_app A1 LEFT JOIN view_all_account C ON A1.UPD_USER = C.USERNAME WHERE A1.DEL_FLAG IS NULL GROUP BY A1.APP_SEQ, date(A1.UPD_DATE), C.NAME " .
            ")a WHERE ".$where;

        $limit = "";
        if($rows != "" && $offset != "") {
          $limit = " limit " . intVal($rows) . " offset " . intVal($offset);
        }
        else if($rows != "") {
          $limit = " limit " . intVal($rows);
        }

        $sql = $sql . " " . $limit;

        $rs = $this->db->query($sql);
        return $this->QueryToArray($rs);           
        
    }

    public function getPayConfirmDeleteDataBySeq($appseq)
    {
        $sql = "SELECT a.*,b.*,c.DESCRIPTION AS cDESCRIPTION ,d.DESCRIPTION AS dDESCRIPTION 
        FROM hour_traffic_tax a
        LEFT OUTER JOIN teacher b ON a.teacher_id=b.idno
        left join code_table c on b.hire_type = c.ITEM_ID and c.TYPE_ID = '08'
        left join code_table d ON a.teacher_bank_id = d.ITEM_ID AND d.TYPE_ID = '14'
        WHERE a.seq IN (
        select seq from hour_app 
        where app_seq = ".$this->db->escape(addslashes($appseq))."
        ) 
        order by year, class_no, term";
        $rs = $this->db->query($sql);
       

        $resultData = $this->QueryToArray($rs); 
        
        for ($i=0; $i < sizeof($resultData) ; $i++) { 
               $resultData[$i]['traffic_fee'] = ($resultData[$i]['traffic_fee'] == "-1" ? 0 : $resultData[$i]['traffic_fee']);
                $resultData[$i]['subtotal'] = $resultData[$i]['hour_fee'] + $resultData[$i]['traffic_fee'];
                $resultData[$i]['banbank_namek_type'] = ($resultData[$i]['teacher_bank_type'] == 'bank' ? '銀行' : ($resultData[$i]['teacher_bank_type'] == 'post' ? '郵局' : ''));
        }
        // echo json_encode($resultData);
        // return;
        return $resultData;     
        
    }

    public function deletePayConfirmDeleteData($taker,$applyid,$start,$end,$count,$chklist){

        $workname = $taker;
        $appno    = $applyid;
        $d1 = $start;
        $d2 = $end;

        $mtList = $chklist;
        $where = "";
        $arry = explode(",,",$mtList);
        for ($x=0;$x<sizeof($arry);$x++){
            if ($arry[$x]!="")
            {
            if ($where==""){
                $where .= $this->db->escape(addslashes($arry[$x]));
            }
            else{
                $where .= ",".$this->db->escape(addslashes($arry[$x]))."";
            } 
            }    
        }
        $where = "app_seq in (".$where.")";
            
        $sql = "update hour_traffic_tax set status = '待確認' " . // custom by chiahua 刪除請款後，狀態由原本的null改成待確認  
                "where seq in (select seq from hour_app where ".$where .")";

        $rs = $this->db->query($sql);

        $sql = "update hour_app set del_flag = 'Y'" .  
                "where ".$where;
        $rs = $this->db->query($sql);

        return('刪除成功');                        
    }
}

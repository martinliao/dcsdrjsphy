<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once(APPPATH."models/Common_model.php");
class Course_person_count_model extends Common_model
{
     public function getFunction($year,$start_date,$end_date,$type){
         
        
        $queryCancel = "and (a.is_cancel not in ('1') or a.is_cancel is null)";
        //#47449 實體系統-20M、20N、21A教室有與20H不一致的狀況：教室代碼改取自room_use
      //#47568 不開班的資料不帶出
    //   (select IFNULL(cr.room_sname, cr.room_name) from venue_information cr where cr.room_id = c.room_id) room_id
        $sql =  "SELECT DISTINCT
                    date_format(c.use_date,'%Y-%m-%d') as d
                    ,DAYOFWEEK(c.use_date)as dayofweek,
                    ct.description AS series, start_date1, class_no, a.room_code, IFNULL(cr.room_sname, cr.room_name) AS room_id,
                    b.name as description, a.class_name, a.term,
                    (select count('x') from online_app oa where yn_sel NOT IN ('2','6','7') and oa.year=a.year and oa.class_no=a.class_no and oa.term=a.term) as pcount,
                    (select IFNULL(CT.ADD_VAL1, CT.ADD_VAL2) tel FROM code_table CT
                    where CT.type_id = '26' and CT.item_id = a.worker) tel,
                (select vm.name AS FIRST_NAME from BS_user vm where vm.idno = a.worker) FIRST_NAME
            FROM `require` a
            LEFT outer JOIN second_category b
                ON a.beaurau_id=b.item_id and b.parent_id=a.type
            LEFT outer JOIN code_table ct
                ON a.type=ct.item_id AND type_id='23'
            LEFT JOIN room_use c
                ON c.year = a.year and c.class_id = a.class_no and c.term = a.term
            right JOIN venue_information cr ON cr.room_id = c.room_id
            WHERE
                ((".$this->db->escape(addslashes($start_date)).")<= c.use_date and c.use_date <= (".$this->db->escape(addslashes($end_date)).") )
                AND a.year=".$this->db->escape(addslashes($year))."
                AND a.class_status IN (2, 3)
                AND	EXISTS
                (SELECT * FROM mail_log WHERE mail_log.year = a.year and mail_log.class_no = a.class_no and mail_log.term = a.term and mail_log.MAIL_TYPE='3')
                ".$queryCancel."
            ORDER BY d,class_no";

        //測試有資料sql
        // $sql = "SELECT DISTINCT date_format(c.use_date,'%Y-%m-%d') as d ,DAYOFWEEK(c.use_date)as dayofweek, ct.description AS series, start_date1, class_no, a.room_code, (select IFNULL(cr.sname, cr.name) from classroom cr where cr.room_id = c.room_id) room_id, b.name as description, a.class_name, a.term, (select count('x') from online_app oa where yn_sel NOT IN ('2','6','7') and oa.year=a.year and oa.class_no=a.class_no and oa.term=a.term) as pcount, (select IFNULL(CT.ADD_VAL1, CT.ADD_VAL2) tel FROM code_table CT where CT.type_id = '26' and CT.item_id = a.worker) tel, (select vm.FIRST_NAME from vm_all_account vm where vm.personal_id = a.worker) FIRST_NAME FROM `require` a LEFT outer JOIN sub_category b ON a.beaurau_id=b.cate_id and b.type=a.type LEFT outer JOIN code_table ct ON a.type=ct.item_id AND type_id='23' LEFT JOIN room_use c ON c.year = a.year and c.class_id = a.class_no and c.term = a.term WHERE c.use_date >= '2017-05-31' ";
         $query = $this->db->query($sql);

        return $this->QueryToArray($query);

     }

     public function csvexport($filename,$query_start_date,$query_end_date,$data,$dayOfWeek){
        $filename = date("Ymd").'.csv';

        header("Content-type: application/csv");  
        header("Content-Disposition: attachment; filename=$filename");
            
        echo iconv("UTF-8","BIG5","臺北市政府公務人員訓練處,");
        echo iconv("UTF-8","BIG5","當週每日班期實調人數一覽表");
        echo "\r\n";
        echo iconv("UTF-8","BIG5","$query_start_date"."至"."$query_end_date");
        echo "\r\n";
        // echo iconv("UTF-8","BIG5","合計：".$data['TOT_COUNT']);
        // echo "\r\n";
        echo iconv("UTF-8","BIG5","日期,");
        echo iconv("UTF-8","BIG5","星期,");
        echo iconv("UTF-8","BIG5","系列,");
        echo iconv("UTF-8","BIG5","單位/類別,");
        echo iconv("UTF-8","BIG5","班期名稱,");
        echo iconv("UTF-8","BIG5","期別,");
        echo iconv("UTF-8","BIG5","教室,");
        echo iconv("UTF-8","BIG5","實調人數 \r\n");  
    

        
        foreach ($data as $val) {
            echo iconv("UTF-8","BIG5",$val['d']).',';
            echo iconv("UTF-8","BIG5",$val["dayofweek"] ==null?"" :$dayOfWeek[$val["dayofweek"]]).',';
            echo iconv("UTF-8","BIG5",$val['series']).',';
            echo iconv("UTF-8","BIG5",$val['description']).',';
            echo iconv("UTF-8","BIG5",$val['class_name']).',';
            echo iconv("UTF-8","BIG5",$val['term']).',';
            echo iconv("UTF-8","BIG5",$val['room_id']).',';
            echo iconv("UTF-8","BIG5",$val['pcount']).',';
            echo "\r\n";   
        }
     }

}
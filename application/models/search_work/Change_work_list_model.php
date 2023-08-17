<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

require_once APPPATH . "models/Common_model.php";
class Change_work_list_model extends Common_model
{
    public function getChangeWorkListData($year, $start_date, $end_date,$schedule,$sort)
    {
        $sql = "SELECT T.class_name, DATE_FORMAT(T.start_date1, '%Y-%m-%d') as start_date1, DATE_FORMAT(T.end_date1, '%Y-%m-%d') as end_date1, 
        T.term, T.year, T.CLASS_NO, DATE_FORMAT(T.outtraydate, '%Y-%m-%d') as outtraydate,DATE_FORMAT(m.cre_date, '%Y-%m-%d') as classenddate
        , V.name as FIRST_NAME, '' as LAST_NAME , 
        ( select nvl(CT.ADD_VAL1, CT.ADD_VAL2) FROM `require` A 
        LEFT JOIN code_table CT ON CT.item_id = A.worker where CT.type_id = '26' and T.WORKER = A.WORKER 
        GROUP BY CT.item_id, CT.ADD_VAL1, CT.ADD_VAL2 ) tel 
        , DATE_FORMAT(T.MAIL_DATE, '%Y-%m-%d') as MAIL_DATE, 
        (SELECT COUNT(*) FROM online_app WHERE YEAR = T . YEAR AND class_no = T .class_no AND term = T .term AND yn_sel IN ('2')) 
        AS tapply_count 
        from `require` T 
        left join (select * from (select year,class_no,term,max(cre_date) as cre_date from mail_log where mail_type='1' 
        group by year,class_no,term ) as zz) m on m.year=T.year and m.class_no=T.class_no and m.term=T.term 
        LEFT JOIN BS_user V ON T.WORKER=V.idno 
        WHERE T.class_status IN ('2', '3') AND T.type!='C' AND START_DATE1 >= Date(". $this->db->escape(addslashes($start_date)) .") 
        AND START_DATE1 < DATE(" .$this->db->escape(addslashes($end_date)). ") and IFNULL(T.is_cancel, '0') = '0'";

        if($schedule != "") {
            $sql .= " AND T.class_name LIKE " . $this->db->escape(addslashes($schedule) . "%");
        }

        if($sort == '') {
            $order = " order by T.year";
        }
        else {
            $orderArr = explode("+", $sort);
            $order = " order by ".addslashes($orderArr[0])." ".addslashes($orderArr[1]);
        }
        
        $sql .= $order;

        $query = $this->db->query($sql);

        return $this->QueryToArray($query);

    }

    public function exportChangeWorkListData($year, $start_date, $end_date,$schedule,$sort)
    {      
        $data = array();
        // $sql = "SELECT T.class_name, DATE_FORMAT(T.start_date1, '%Y-%m-%d') as start_date1, 
        // DATE_FORMAT(T.end_date1, '%Y-%m-%d') as end_date1, T.term, 
        // DATE_FORMAT(T.outtraydate, '%Y-%m-%d') as outtraydate, DATE_FORMAT(m.cre_date, '%Y-%m-%d') as classenddate
        // , V.name as FIRST_NAME, '' as LAST_NAME from `require` T 
        
        // left join (select * from (select year,class_no,term,max(cre_date) as cre_date from mail_log where mail_type='1' 
        // group by year,class_no,term ) as zz) m on m.year=T.year and m.class_no=T.class_no and m.term=T.term 
        // LEFT JOIN BS_user V ON T.WORKER=V.idno 
        // where END_DATE1 >= date('". $start_date ."') AND END_DATE1 < date('" .$end_date. "') 
        // AND T.class_status IN ('2', '3') AND T.type!='C' 
        // order by T.year" ;
        $sql = "SELECT T.class_name, DATE_FORMAT(T.start_date1, '%Y-%m-%d') as start_date1, DATE_FORMAT(T.end_date1, '%Y-%m-%d') as end_date1, 
        T.term, T.year, T.CLASS_NO, DATE_FORMAT(T.outtraydate, '%Y-%m-%d') as outtraydate,DATE_FORMAT(m.cre_date, '%Y-%m-%d') as classenddate
        , V.name as FIRST_NAME, '' as LAST_NAME , 
        ( select nvl(CT.ADD_VAL1, CT.ADD_VAL2) FROM `require` A 
        LEFT JOIN code_table CT ON CT.item_id = A.worker where CT.type_id = '26' and T.WORKER = A.WORKER 
        GROUP BY CT.item_id, CT.ADD_VAL1, CT.ADD_VAL2 ) tel 
        , DATE_FORMAT(T.MAIL_DATE, '%Y-%m-%d') as MAIL_DATE, 
        (SELECT COUNT(*) FROM online_app WHERE YEAR = T . YEAR AND class_no = T .class_no AND term = T .term AND yn_sel IN ('2')) 
        AS tapply_count 
        from `require` T 
        left join (select * from (select year,class_no,term,max(cre_date) as cre_date from mail_log where mail_type='1' 
        group by year,class_no,term ) as zz) m on m.year=T.year and m.class_no=T.class_no and m.term=T.term 
        LEFT JOIN BS_user V ON T.WORKER=V.idno 
        WHERE T.class_status IN ('2', '3') AND T.type!='C' AND START_DATE1 >= Date(". $this->db->escape(addslashes($start_date)) .") 
        AND START_DATE1 < DATE(" .$this->db->escape(addslashes($end_date)). ") and IFNULL(T.is_cancel, '0') = '0'";

        if($schedule != "") {
            $sql .= " AND T.class_name LIKE " . $this->db->escape(addslashes($schedule) . "%");
        }

        if($sort == '') {
            $order = " order by T.year";
        }
        else {
            $orderArr = explode("+", $sort);
            $order = " order by ".addslashes($orderArr[0])." ".addslashes($orderArr[1]);
        }
        
        $sql .= $order;
        

        $rs = $this->db->query($sql);
        $rs = $this->QueryToArray($rs);

        // 寫檔
        $filename = 'Change_work_list.csv';

        header("Content-type: application/csv");    //header抬頭設定
        header("Content-Disposition: attachment; filename=Change_work_list.csv");
            
        echo iconv("UTF-8","BIG5","班期名稱, \t");
        echo iconv("UTF-8","BIG5","期別, \t");
        echo iconv("UTF-8","BIG5","承辦人(分機), \t");
        echo iconv("UTF-8","BIG5","開課起迄日, \t");
        echo iconv("UTF-8","BIG5","應完成日, \t");
        echo iconv("UTF-8","BIG5","實際完成日(mail人事), \t");
        echo iconv("UTF-8","BIG5","實際完成日(mail學員), \n");
            
        for ($i=0; $i < sizeof($rs); $i++) {
            $arr=$rs[$i]; 
            $class_name = iconv("UTF-8","BIG5",$arr["class_name"]);		
            $term = iconv("UTF-8","BIG5",$arr["term"]);
            $first_name = iconv("UTF-8","BIG5",$arr["FIRST_NAME"]);	
            $last_name =  iconv("UTF-8","BIG5",$arr["LAST_NAME"]);
            $tel = iconv("UTF-8","BIG5",($arr["tel"]!=''?'(':'').$arr["tel"].($arr["tel"]!=''?')':''));
            $start_date1 = iconv("UTF-8","BIG5",$arr["start_date1"]);
            $end_date1 = iconv("UTF-8","BIG5",$arr["end_date1"]);
            $outtraydate = iconv("UTF-8","BIG5",$arr["outtraydate"]);
            $classenddate = iconv("UTF-8","BIG5",$arr["classenddate"]);

            // 計算應完成日
            $Completiondate = explode('-',$start_date1);
            $fmtDate = date('Y-m-d', mktime(0,0,0,$Completiondate[1],$Completiondate[2] - 10,$Completiondate[0]));
            // END 計算應完成日

            echo   "$class_name ,\t"; 
            echo   "$term,\t"; 
            echo   "$first_name$last_name$tel,\t";  
            echo   iconv("UTF-8","BIG5",$start_date1.'~'.$end_date1.",\t"); 
            echo   "$fmtDate ,\t";
            echo   "$outtraydate,\t";
            echo   "$classenddate,\n";
        }
    }

    public function getDetailChangeWorkListData($year,$class,$term,$type)
    {
        //查詢
        $year       = $year;
        $class_no = $class;
        $term       = $term;
        $type = $type;

        if($type=="") $type ='3';
        if($type=='3') {
            $type_name = "錄取名冊";
            $type_filed = "錄取人數";
            $type_condi = " yn_sel NOT IN ('2', '6', '7') ";
        }
        else	
        {	
            $type_name = "未錄取名冊";
            $type_filed = "未錄取人數";
            $type_condi = " yn_sel IN ('2') ";
        }

        $sql = "select o.*,v.name,nvl(og.ou_gov,BC.NAME) AS description
                from online_app o 
                left join BS_user v on o.id=v.idno 
                LEFT JOIN bureau BC ON BC.bureau_id=v.bureau_id 
                LEFT outer JOIN out_gov og on v.idno = og.ID
                where o.year=".$this->db->escape(addslashes($year))." and o.class_no=".$this->db->escape(addslashes($class_no))." and o.term=".$this->db->escape(addslashes($term))."  and {$type_condi}";
            // $rsAll = $this->db->query($sql);
            // $page_size = 30;
            // $total_query_records = $rsAll->RecordCount();
            // $total_page = ceil($total_query_records / $page_size);
            // $cur_page   = ($_POST['p'] == '') ? 1 : intval($_POST['p']);
            // $rdsBegIdx = 1;
            // $rdsEndIdx = $page_size;
            // if ($cur_page <= 0)
            // {
            //     $cur_page = 0;
            //     $rdsEndIdx = $total_query_records;
            // }
            // else if ($cur_page > $total_page)
            // {
            //     $cur_page = 1;
            //     $rdsBegIdx = (($cur_page-1)*$page_size) ;
            //     $rdsEndIdx = $page_size;
            // }
            // else if ($cur_page <= $total_page)
            // {
            //     $rdsBegIdx = (($cur_page-1)*$page_size ) +1 ;
            //     $rdsEndIdx = $cur_page*$page_size;		
            // }
            // if ($total_query_records==0){
            // $cur_page = 0;
            // }
            
            // $p1 = (($cur_page==1) || ($cur_page==0)) ? "disabled" : 'onclick="page(-1)"';
            // $p2 = (($cur_page==1) || ($cur_page==0)) ? "disabled" : 'onclick="page(-2)"';
            // $p3 = (($cur_page==$total_page) || ($cur_page==0)) ? "disabled" : 'onclick="page(-3)"';
            // $p4 = (($cur_page==$total_page) || ($cur_page==0)) ? "disabled" : 'onclick="page(-4)"';
            
            $sql = "SELECT * FROM (SELECT Z.* FROM (" . $sql . ") Z ) as TEST ";  
            
            $rs = $this->db->query($sql);

        return $this->QueryToArray($rs);

    }

}

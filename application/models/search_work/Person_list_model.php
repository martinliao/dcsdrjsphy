<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

require_once APPPATH . "models/Common_model.php";
class Person_list_model extends Common_model
{

    public function getPersonListData($year, $schedule, $class_no, $contactor, $firstSeries, $secondSeries , $ssd, $sed, $rows="", $offset="")
    {

        // if($year=='')
        //     $year = date('Y')-1911;
 
        //$query_month = "1";


        //班期代碼,班期名稱
        // $query_class_name=$_REQUEST['query_class_name'];
        // $query_class_no=$_REQUEST['query_class_no'];
        // $queryType = $_REQUEST['queryType'];
        // $BEAURAU = $_REQUEST['BEAURAU'];

        // if (!empty($_REQUEST['query_year'])) {
        //     $query_year = sanitize($_REQUEST['query_year']);
        // }
        
        // if (isset($_REQUEST['query_month'])) {
        //     $query_month = sanitize($_REQUEST['query_month']);
        // }
        
        $queryConditionSql = '';

        if (!empty($contactor)) {
            $queryConditionSql .=" AND R.worker like ".$this->db->escape("%".addslashes($contactor)."%");
        }


        if((!empty($schedule)) && $schedule!=""){
            $queryConditionSql.= " and upper(class_name) like upper(".$this->db->escape("%".addslashes($schedule)."%").") ";
        }

        if((!empty($class_no)) && $class_no!=""){
            $queryConditionSql.="  AND UPPER(CLASS_NO) LIKE UPPER(".$this->db->escape("%".addslashes($class_no)."%").") ";
        }

        if(!empty($firstSeries) && $firstSeries!=""){
            $queryConditionSql.= "  AND R.TYPE = ".$this->db->escape(addslashes($firstSeries))." ";
        }

        if(!empty($secondSeries) && $secondSeries!=""){
            $queryConditionSql.= "  AND R.BEAURAU_ID = ".$this->db->escape(addslashes($secondSeries))." ";
        }

        //$time_sql = '';
        //$pagenum = $_SESSION['Study_Class_Record']['pagenum'];
        //$Enter_Teacher_Name=$_REQUEST['Enter_Teacher_Name'];
        //$Enter_Course_Name=$_REQUEST['Enter_Course_Name'];
        //$Enter_Schedule_Name=$_REQUEST['Enter_Schedule_Name'];

        //公用查詢
        //$search_start_date=$_REQUEST['search_start_date'];
        //$search_end_date=$_REQUEST['search_end_date'];

        //組成 sql partial where constraint
        //$No_Date = true;
        //撈資料
        //$data = array();
        
        if (!empty($ssd)){
           // $year = date('Y',strtotime(".$ssd."))-1911;
            $sql = $this->QueryCondSql($year, $ssd, $sed, $queryConditionSql, $contactor);
        }
        else{
            $ssd = date('Y-m-01');
            $sed = date('Y-m-01');

            if ($year !='')
                {
                    $cyear = $year + 1911;
                    $ssd = date($cyear."-m-01");
                    $sed = date($cyear."-m-t");
                }
            $sql = $this->QueryCondSql($year, $ssd, $sed, $queryConditionSql, $contactor);
        }

        $limit = "";
        if($rows != "" && $offset != "") {
          $limit = " limit " . intVal($rows) . " offset " . intVal($offset);
        }
        else if($rows != "") {
          $limit = " limit " . intVal($rows);
        }

        $sql = $sql . " " . $limit;

        $query = $this->db->query($sql);

        return $this->QueryToArray($query);

    }

    public function getContactor()
    {
        $sql="select v.idno as PERSONAL_ID,v.NAME
        from account_role a
         join BS_user v on a.username = v.username
        where a.group_id = '8' and v.idno IS NOT NULL";
        $query = $this->db->query($sql);

        return $this->QueryToArray($query);

    }
    

    public function QueryCondSql($year, $ssd, $sed, $queryConditionSql, $contactor)
    {
        $idnotemp="";
        if($contactor == "") {
            $sqltemp = "(select v.name from BS_user v where v.idno=R.worker) as UNAME";
        }
        else {
            $sqltemp = "(select  name from BS_user where idno=".$this->db->escape(addslashes($contactor)).") as UNAME";
        }

        $sql = "SELECT ".$sqltemp.",
        (select CT.add_val1 from code_table CT where CT.type_id='26' and R.worker = CT.item_id) as SUBTEL,
        (
            SELECT
                CT.DESCRIPTION
            FROM
                code_table CT
            WHERE
                CT.type_id = '23'
            AND R.TYPE = CT.item_id
        ) AS TYPE_NAME,
        (
            SELECT
                SC.NAME
            FROM
                second_category SC
            WHERE
                R.BEAURAU_ID = SC.item_id
            AND R.TYPE = SC.parent_id
        ) AS BU_NAME,
        R.class_name, R.seq_no, R.year, R.start_date1, R.end_date1, R.Range_real, R.Range, R.YEAR, R.term,R.class_no,
        (select IFNULL(room_sname, room_name) from venue_information where  R.room_code = room_id limit 1) room_name,
        (SELECT COUNT(*)
        FROM online_app
        WHERE R.term = online_app.term
        AND R.year = online_app.YEAR
        AND R.class_no = online_app.class_no
        AND online_app.yn_sel NOT IN ('2', '6')) student_count,
            R.IS_ASSESS,
            R.IS_MIXED
        FROM `require` R
        WHERE R.start_date1 >= date(".$this->db->escape(addslashes($ssd)).") and R.start_date1<=date(".$this->db->escape(addslashes($sed)).")
             $queryConditionSql and  IFNULL(R.is_cancel, '0') = '0' and R.CLASS_STATUS in ('2', '3') order by start_date1, TYPE, class_no, term";
    
    return $sql;
                
    }
    


    public function csvexport($filename, $query_start_date, $query_end_date,$year, $schedule, $class_no, $contactor, $firstSeries, $secondSeries)
    {
        $data = $this->getPersonListData($year, $schedule, $class_no, $contactor, $firstSeries, $secondSeries, $query_start_date, $query_end_date);

        $filename = iconv("UTF-8", "BIG5", '查詢作業-承辦人帶班一覽表.csv');

        header("Content-type: application/csv");
        header("Content-Disposition: attachment; filename=$filename");

        echo iconv("UTF-8", "BIG5", "臺北市政府公務人員訓練處,");
        echo iconv("UTF-8", "BIG5", "承辦人帶班一覽表\r\n");
        //echo iconv("UTF-8","BIG5",$query_start_date."至".$query_end_date."\r\n");

        echo iconv("UTF-8", "BIG5", "承辦人,");
        echo iconv("UTF-8", "BIG5", "班期代碼,");
        echo iconv("UTF-8", "BIG5", "班期性質,");
        echo iconv("UTF-8", "BIG5", "系列別,");
        echo iconv("UTF-8", "BIG5", "次類別,");

        echo iconv("UTF-8", "BIG5", "班期名稱,");
        echo iconv("UTF-8", "BIG5", "期別,");
        echo iconv("UTF-8", "BIG5", "教室,");
        echo iconv("UTF-8", "BIG5", "實招人數,");
        echo iconv("UTF-8", "BIG5", "開班起日,");

        echo iconv("UTF-8", "BIG5", "開班迄日,");
        echo iconv("UTF-8", "BIG5", "實際期程(小時),");
        echo iconv("UTF-8", "BIG5", "計畫期程(小時)\r\n");
        

        foreach ($data as $val) {
            echo iconv("UTF-8", "BIG5", $val['UNAME'].$val['SUBTEL'].',');
            echo iconv("UTF-8", "BIG5", $val['class_no']) . ',';
            
            $asses_name = "";
		        if('1'==$val['IS_ASSESS'] && '1'==$val['IS_MIXED'])
		        {
			$asses_name = "混成";
		        }else if('1'==$val['IS_ASSESS'] )
		    {
			$asses_name = "	考核";
		    }

            echo iconv("UTF-8", "BIG5", "$asses_name") . ',';
            echo iconv("UTF-8", "BIG5", $val['TYPE_NAME']) . ',';
            echo iconv("UTF-8", "BIG5", $val['BU_NAME']) . ',';

            echo iconv("UTF-8", "BIG5", $val['class_name']) . ',';
            echo iconv("UTF-8", "BIG5", $val['term']) . ',';
            echo iconv("UTF-8", "BIG5", $val['room_name']) . ',';
            echo iconv("UTF-8", "BIG5", $val['student_count']) . ',';
            echo iconv("UTF-8", "BIG5", date('Y/m/d',strtotime($val['start_date1']))) . ',';
            echo iconv("UTF-8", "BIG5", date('Y/m/d',strtotime($val['end_date1']))) . ',';
            
            echo iconv("UTF-8", "BIG5", $val['Range_real']) . ',';
            echo iconv("UTF-8", "BIG5", $val['Range']) . ',';
                       
            echo "\r\n";
        }
    }

}
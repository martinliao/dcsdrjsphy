<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

require_once APPPATH . "models/Common_model.php";
class Class_person_number_model extends Common_model
{
    public function getClassPersonNumberData($year, $minnumber, $maxnumber, $firstSeries, $secondSeries, $rows="", $offset="")
    {
        
        if($year=='') {
            $year = date('Y')-1910;
        }
        
        //頁數
        // $pagenum = 1;
        // if($_REQUEST['pagenum']!="")
        //     $pagenum =  sanitize($_REQUEST['pagenum']);
        
        //組成 以 require 為主檔的 sql partial where constraint
        $query_cond_string = 'year='.$this->db->escape(addslashes($year));

        //$series   = $firstSeries      系列別代碼 
        //$BEAURAU  = $secondSeries  次類別代碼
        if($firstSeries!="")
            $query_cond_string .=" and type = ".$this->db->escape(addslashes($firstSeries))."";
        if($secondSeries!="" && "A"==$firstSeries)
        {
                $query_cond_string .=" and beaurau_id = ".$this->db->escape(addslashes($secondSeries))."";
        }else if($secondSeries!="" && "B"==$firstSeries){
                $query_cond_string .=" and beaurau_id = ".$this->db->escape(addslashes($secondSeries))."";
        }

        if($minnumber!="")
            $query_cond_string .=' AND NO_PERSONS>= '.$this->db->escape(addslashes($minnumber));
            
        if($maxnumber!="")
            $query_cond_string .=' AND NO_PERSONS<= '.$this->db->escape(addslashes($maxnumber));



        //撈資料 part1: 以 beaurau 為主檔
        //$data = array();
        $sql = $this->QueryCondString($query_cond_string);

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



    public function QueryCondString($query_cond_string)
    {
  //       $sql = "
  //       SELECT distinct class_name,term, no_persons ,MONTH(start_date1) AS MON1,MONTH(end_date1) AS MON2
		// ,CASE WHEN 1>=MONTH(start_date1) AND 1<=MONTH(end_date1)  THEN '<p align=left style=color:red>■■■■</p>' END AS M1
		// ,CASE WHEN 2>=MONTH(start_date1) AND 2<=MONTH(end_date1)  THEN '<p align=left style=color:red>■■■■</p>' END AS M2
		// ,CASE WHEN 3>=MONTH(start_date1) AND 3<=MONTH(end_date1)  THEN '<p align=left style=color:red>■■■■</p>' END AS M3
		// ,CASE WHEN 4>=MONTH(start_date1) AND 4<=MONTH(end_date1)  THEN '<p align=left style=color:red>■■■■</p>' END AS M4
		// ,CASE WHEN 5>=MONTH(start_date1) AND 5<=MONTH(end_date1)  THEN '<p align=left style=color:red>■■■■</p>' END AS M5
		// ,CASE WHEN 6>=MONTH(start_date1) AND 6<=MONTH(end_date1)  THEN '<p align=left style=color:red>■■■■</p>' END AS M6
		// ,CASE WHEN 7>=MONTH(start_date1) AND 7<=MONTH(end_date1)  THEN '<p align=left style=color:red>■■■■</p>' END AS M7
		// ,CASE WHEN 8>=MONTH(start_date1) AND 8<=MONTH(end_date1)  THEN '<p align=left style=color:red>■■■■</p>' END AS M8
		// ,CASE WHEN 9>=MONTH(start_date1) AND 9<=MONTH(end_date1)  THEN '<p align=left style=color:red>■■■■</p>' END AS M9
		// ,CASE WHEN 10>=MONTH(start_date1) AND 10<=MONTH(end_date1)  THEN '<p align=left style=color:red>■■■■</p>' END AS M10
		// ,CASE WHEN 11>=MONTH(start_date1) AND 11<=MONTH(end_date1)  THEN '<p align=left style=color:red>■■■■</p>' END AS M11
		// ,CASE WHEN 12>=MONTH(start_date1) AND 12<=MONTH(end_date1)  THEN '<p align=left style=color:red>■■■■</p> ' END AS M12
		// FROM `require` where 
		//  ".$query_cond_string." and IS_CANCEL = 0 order by no_persons ";
        $sql = "
        SELECT distinct class_name,term, no_persons ,MONTH(start_date1) AS MON1,MONTH(end_date1) AS MON2
        ,CASE WHEN 1>=MONTH(start_date1) AND 1<=MONTH(end_date1)  THEN '■■■' END AS M1
        ,CASE WHEN 2>=MONTH(start_date1) AND 2<=MONTH(end_date1)  THEN '■■■' END AS M2
        ,CASE WHEN 3>=MONTH(start_date1) AND 3<=MONTH(end_date1)  THEN '■■■' END AS M3
        ,CASE WHEN 4>=MONTH(start_date1) AND 4<=MONTH(end_date1)  THEN '■■■' END AS M4
        ,CASE WHEN 5>=MONTH(start_date1) AND 5<=MONTH(end_date1)  THEN '■■■' END AS M5
        ,CASE WHEN 6>=MONTH(start_date1) AND 6<=MONTH(end_date1)  THEN '■■■' END AS M6
        ,CASE WHEN 7>=MONTH(start_date1) AND 7<=MONTH(end_date1)  THEN '■■■' END AS M7
        ,CASE WHEN 8>=MONTH(start_date1) AND 8<=MONTH(end_date1)  THEN '■■■' END AS M8
        ,CASE WHEN 9>=MONTH(start_date1) AND 9<=MONTH(end_date1)  THEN '■■■' END AS M9
        ,CASE WHEN 10>=MONTH(start_date1) AND 10<=MONTH(end_date1)  THEN '■■■' END AS M10
        ,CASE WHEN 11>=MONTH(start_date1) AND 11<=MONTH(end_date1)  THEN '■■■' END AS M11
        ,CASE WHEN 12>=MONTH(start_date1) AND 12<=MONTH(end_date1)  THEN '■■■ ' END AS M12
        FROM `require` where 
         ".$query_cond_string." and IS_CANCEL = 0 order by no_persons ";

    return $sql;

    } 
    
    

    public function csvexport($filename,$year,$minnumber,$maxnumber,$firstSeries,$secondSeries)
    {
        $data = $this->getClassPersonNumberData($year,$minnumber,$maxnumber,$firstSeries,$secondSeries);

        $filename = iconv("UTF-8", "BIG5", '查詢作業-年度班期人數與時間一覽.csv');

        header("Content-type: application/csv");
        header("Content-Disposition: attachment; filename=$filename");

        echo iconv("UTF-8", "BIG5", "臺北市政府公務人員訓練處,");
        echo iconv("UTF-8", "BIG5", "年度班期人數與時間一覽\r\n");
        //echo iconv("UTF-8","BIG5",$query_start_date."至".$query_end_date."\r\n");

        echo iconv("UTF-8", "BIG5", "班期名稱,");
        echo iconv("UTF-8", "BIG5", "期別,");
        echo iconv("UTF-8", "BIG5", "人數,");
        echo iconv("UTF-8", "BIG5", "一月,");
        echo iconv("UTF-8", "BIG5", "二月,");
        echo iconv("UTF-8", "BIG5", "三月,");
        echo iconv("UTF-8", "BIG5", "四月,");
        echo iconv("UTF-8", "BIG5", "五月,");
        echo iconv("UTF-8", "BIG5", "六月,");
        echo iconv("UTF-8", "BIG5", "七月,");
        echo iconv("UTF-8", "BIG5", "八月,");
        echo iconv("UTF-8", "BIG5", "九月,");
        echo iconv("UTF-8", "BIG5", "十月,");
        echo iconv("UTF-8", "BIG5", "十一月,");
        echo iconv("UTF-8", "BIG5", "十二月\r\n");
        

        foreach ($data as $val) {
            echo iconv("UTF-8", "BIG5", $val['class_name']) . ',';
            echo iconv("UTF-8", "BIG5", $val['term']) . ',';
            echo iconv("UTF-8", "BIG5", $val['no_persons']) . ',';
            echo iconv("UTF-8", "BIG5", $val['M1']) . ',';
            echo iconv("UTF-8", "BIG5", $val['M2']) . ',';
            echo iconv("UTF-8", "BIG5", $val['M3']) . ',';
            echo iconv("UTF-8", "BIG5", $val['M4']) . ',';
            echo iconv("UTF-8", "BIG5", $val['M5']) . ',';
            echo iconv("UTF-8", "BIG5", $val['M6']) . ',';
            echo iconv("UTF-8", "BIG5", $val['M7']) . ',';
            echo iconv("UTF-8", "BIG5", $val['M8']) . ',';
            echo iconv("UTF-8", "BIG5", $val['M9']) . ',';
            echo iconv("UTF-8", "BIG5", $val['M10']) . ',';
            echo iconv("UTF-8", "BIG5", $val['M11']) . ',';
            echo iconv("UTF-8", "BIG5", $val['M12']) . ',';
           
            echo "\r\n";
        }
    }


}


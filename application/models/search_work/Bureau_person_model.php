<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

require_once APPPATH . "models/Common_model.php";
class Bureau_person_model extends Common_model
{
    public function getBureauPersonData($year, $schedule, $class_no, $contactor, $ssd, $sed, $rows="", $offset="")
    {
        if($year==''){
             $year = date('Y')-1911;}

        $queryCondString = '1=1';

        if((!empty($contactor)) && $contactor!=''){
            $queryCondString.= " AND `require`.Contactor like ".$this->db->escape("%".addslashes($contactor)."%")." ";
        }

        if((!empty($schedule)) && $schedule!=''){
            $queryCondString.= " AND `require`.class_name like ".$this->db->escape("%".addslashes($schedule)."%")." ";
        }

        if((!empty($class_no)) && $class_no!=''){
            $queryCondString.= " AND upper(`require`.CLASS_NO) LIKE UPPER(".$this->db->escape("%".addslashes($class_no)."%").") ";
        }


        //開班日期條件
        // 下面4種情形應該被列出 1. 搜尋起日介於開班起迄日之間 2. 搜尋迄日介於開班起迄日之間 3. 開班起日介於搜尋起迄日之間 4. 開班迄日介於搜尋起迄日之間
        if((!empty($ssd)) && (!empty($sed)) ) {
            $queryCondString.= " and `require`.year=".$this->db->escape(addslashes($year))."";
            $queryCondString.= " and  (
                                (date(".$this->db->escape(addslashes($ssd)).") between start_date1 and end_date1) or 
                                (date(".$this->db->escape(addslashes($sed)).") between start_date1 and end_date1) or 
                                (start_date1 between date(".$this->db->escape(addslashes($ssd)).") and date(".$this->db->escape(addslashes($sed)).")) or 
                                (end_date1 between date(".$this->db->escape(addslashes($ssd)).") and date(".$this->db->escape(addslashes($sed)).")) )
                                 ";
        } elseif((!empty($ssd)) && (empty($sed))) {
            $queryCondString.= " and `require`.year=".$this->db->escape(addslashes($year))."";
            $queryCondString.= " and  date(start_date1)>=".$this->db->escape(addslashes($ssd))."";
        } elseif((empty($ssd)) && (!empty($sed))) {
            $queryCondString.= " and `require`.year=".$this->db->escape(addslashes($year))." ";
            $queryCondString.= " and  date(end_date1)<=".$this->db->escape(addslashes($sed))." ";
        }

        if((!empty($year)) && (empty($ssd))) {
            $queryCondString.= " and `require`.year=".$this->db->escape(addslashes($year))." ";
        }


        //撈資料
        
        if(!empty($queryCondString) && strcmp($queryCondString, ' 1=1 ')!=0)
        {
            $sql = $this->QueryCondSql($year, $queryCondString);
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

        public function QueryCondSql($year, $queryCondString)
        {
            $sql  = "SELECT * 
    		FROM (
    			SELECT (
    				select code_table.DESCRIPTION
                	FROM code_table
                	WHERE code_table.TYPE_ID = '23'
                    AND code_table.ITEM_ID = `require`.TYPE
                ) as TYPE, 
                `require`.class_name, 
                `require`.class_no, 
                term,
                bc.name as DESCRIPTION, 
                Contactor as UNAME, 
                TEL, 
                `require`.start_date1,
                `require`.end_date1, 
                `require`.Range, 
                `require`.Room_Code
				FROM `require`
				LEFT JOIN bureau bc 
				    ON bc.BUREAU_ID=`require`.REQ_BEAURAU
                
             WHERE ".$queryCondString."
               
            )a ORDER BY start_date1";

   
        return $sql;
                    
        }



    public function csvexport($filename, $query_start_date, $query_end_date, $year, $schedule, $class_no, $contactor)
    {
        $data = $this->getBureauPersonData($year, $schedule, $class_no, $contactor, $query_start_date, $query_end_date);

        $filename = iconv("UTF-8", "BIG5", '查詢作業-局處承辦人一覽.csv');

        header("Content-type: application/csv");
        header("Content-Disposition: attachment; filename=$filename");

        echo iconv("UTF-8", "BIG5", "臺北市政府公務人員訓練處,");
        echo iconv("UTF-8", "BIG5", "局處承辦人一覽\r\n");
        //echo iconv("UTF-8","BIG5",$query_start_date."至".$query_end_date."\r\n");

        echo iconv("UTF-8", "BIG5", "系列,");        
        echo iconv("UTF-8", "BIG5", "班期名稱,");
        echo iconv("UTF-8", "BIG5", "期別,");
        echo iconv("UTF-8", "BIG5", "局處名稱,");
        echo iconv("UTF-8", "BIG5", "承辦人,");
        echo iconv("UTF-8", "BIG5", "局處電話,");
        echo iconv("UTF-8", "BIG5", "開班起日,");
        echo iconv("UTF-8", "BIG5", "開班迄日,");
        echo iconv("UTF-8", "BIG5", "期程(小時),");
        echo iconv("UTF-8", "BIG5", "教室代碼\r\n");
        

        foreach ($data as $val) {
            
            echo iconv("UTF-8", "BIG5", $val['TYPE']) . ',';
            echo iconv("UTF-8", "BIG5", $val['class_name']) . ',';
            echo iconv("UTF-8", "BIG5", $val['term']) . ',';
            echo iconv("UTF-8", "BIG5", $val['DESCRIPTION']) . ',';
            echo iconv("UTF-8", "BIG5//IGNORE", $val['UNAME']) . ',';
            echo iconv("UTF-8", "BIG5", $val['TEL']) . ',';
            echo iconv("UTF-8", "BIG5", $val['start_date1']) . ',';
            echo iconv("UTF-8", "BIG5", $val['end_date1']) . ',';
            echo iconv("UTF-8", "BIG5", $val['Range']) . ',';
            echo iconv("UTF-8", "BIG5", $val['Room_Code']) . ',';
           
            echo "\r\n";
        }
    }

    
}
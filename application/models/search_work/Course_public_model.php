<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

require_once APPPATH . "models/Common_model.php";
class Course_public_model extends Common_model
{
    public function getCoursePublicData($query_class_name, $query_type, $use_s_date, $use_e_date
        , $t_name, $t_source, $cre_s_date, $cre_e_date, $edu, $job,$rows="", $offset="") {
        $No_Date = true;
        $where = "";

        if ((!empty($query_class_name))) //班期名稱
        {
            $where .= " and class_name like ".$this->db->escape("%".addslashes($query_class_name)."%")." ";
        }

        if ((!empty($query_type))) //班期類別
        {
            $where .= " and type = ".$this->db->escape(addslashes($query_type))." ";
        }
        if ((!empty($use_s_date)) && (!empty($use_e_date))) //上課日期
        {
            $where .= " and   use_date  between  ".$this->db->escape(addslashes($use_s_date))." and  ".$this->db->escape(addslashes($use_e_date))."  ";
        }

        if ((!empty($t_name))) //姓名
        {
            $where .= " and name like ".$this->db->escape("%".addslashes($t_name)."%")." ";
        }
        if ((!empty($t_source))) //聘請類別
        {
            $where .= " and hire_type = ".$this->db->escape(addslashes($t_source))." ";
        }

        if ((!empty($cre_s_date)) && (!empty($cre_e_date))) //建檔日期
        {
            $where .= " and t.date_added between ".$this->db->escape(addslashes($cre_s_date))." and ".$this->db->escape(addslashes($cre_e_date))." ";
        }

        //學歷
        if (!empty($edu)) {
            $where .= " and education=".$this->db->escape(addslashes($edu))." ";
        }

        // //排序
        // if(($query["sort_filed"]!="")&&($query["sort_type"]!="")){
        //     $where .= " order by ".$query["sort_filed"]." ".$query["sort_type"];
        // }else {
        $where .= "ORDER BY t.name, t.IDno, t.major, DESCRIPTION ,ru.USE_DATE, r.CLASS_NAME ";
        // }

        //撈資料
        $data = array();
        $cn = array();
        $tn = array();
        $sql = "";

        if ($job == 'all') {
            $sql = $this->getJobAllSql($where);
        } else {
            $sql = $this->getJobNoAllSql($job, $where);
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

        $jobSQlData = $this->QueryToArray($query);

        $data = array();

        for ($i = 0; $i < sizeof($jobSQlData); $i++) {

            // 分別抓可受課程
            $jobSQlData[$i]['CAN_TEACH'] = '';
            $techId = $jobSQlData[$i]['idno'];
            $class_no = $jobSQlData[$i]['class_no'];
            $year = $jobSQlData[$i]['year'];
            $term = $jobSQlData[$i]['term'];
            $teachType = $jobSQlData[$i]['teacher'];

            
            if (!empty($use_s_date) && !empty($use_e_date)){
                $jobSQlData[$i]['total_hrs'] = $this->getTeachHours($year, $class_no, $term, $jobSQlData[$i]['use_date'], $techId, $teachType);
            } else {
                $jobSQlData[$i]['total_hrs'] = '';
            }
            

            // $getCanteachData = $this->getCANTEACHData($techId);
            // if(preg_match("/^10.254.250.169$/", $_SERVER["REMOTE_ADDR"])) {
            if ((!empty($use_s_date)) && (!empty($use_e_date))) //上課日期
            {
                $getCanteachData = $this->getCANTEACHData_NEW($techId,$class_no,$year,$term);
            }else{
                $getCanteachData = $this->getCANTEACHData($techId);
            } 
            // }

            $c = 1;
            for ($ct = 0; $ct < sizeof($getCanteachData); $ct++) {
                if ($c % 3 == 0) {
                    $jobSQlData[$i]['CAN_TEACH'] .= $getCanteachData[$ct]['COURSE_NAME'] . '<br>';
                } else {
                    $jobSQlData[$i]['CAN_TEACH'] .= $getCanteachData[$ct]['COURSE_NAME'] . ',';
                }
                $c++;
            }
            $data[] = $jobSQlData[$i];
            //$data['rows']['body']['name'] = 'abc';
        }
        return $data;

    }

    public function getTeachHours($year, $class_no, $term, $use_date, $idno, $teacher_type)
    {
        $sql = sprintf("SELECT
                            SUM(hrs) total_hrs
                        FROM
                            room_use 
                        WHERE
                            `year` = %s 
                            AND class_id = %s 
                            AND term = %s 
                            AND use_date = %s 
                            AND teacher_id = %s 
                            AND isteacher = %s", $this->db->escape(addslashes($year)), $this->db->escape(addslashes($class_no)), $this->db->escape(addslashes($term)), $this->db->escape(addslashes($use_date)), $this->db->escape(addslashes($idno)), $this->db->escape(addslashes($teacher_type)));

        $query = $this->db->query($sql);
        $total_hrs = $query->result_array();

        if(!empty($total_hrs)){
            return intval($total_hrs[0]['total_hrs']);
        } else {
            return 0;
        }
    }

    public function get_source_list()
    {
        $sql = sprintf("
			select ITEM_ID,DESCRIPTION
			from code_table
			where TYPE_ID='08'
			order by ITEM_ID
			"
        );

        $query = $this->db->query($sql);

        return $this->QueryToArray($query);
    }

    public function get_Stype_list()
    {
        $sql = sprintf("
   			select ITEM_ID,DESCRIPTION
    			from code_table
  			where
  				TYPE_ID='23'
  				order by ITEM_ID
				"
        );

        $query = $this->db->query($sql);

        return $this->QueryToArray($query);
    }

    public function get_StudentData_list()
    {
        $sql = "select ITEM_ID, DESCRIPTION from code_table where type_id = '04' order by item_id";

        $query = $this->db->query($sql);

        return $this->QueryToArray($query);
    }

    public function getJobAllSql($where)
    {
        $sql = "SELECT DISTINCT
				t.name, t.another_name AS alias,t.idno, t.id, t.major AS SCHOOL, t.birthday AS birth, t.career, t.email, t.institution AS corp, t.zipcode AS zone, t.job_title AS position, t.h_tel AS telo, t.h_tel2 AS telh, t.mobile AS mobil,
                t.teacher as teacher,t.teacher as assistant,r.seq_no,
				concat(cc.city_name,csc.subcity_name,t.route) AS addr,
	            (
	            	SELECT DESCRIPTION
	                FROM code_table ct
	                WHERE
	                	TYPE_ID = '08'
	                	and ct.item_id = t.hire_type
	            ) as DESCRIPTION,
	            ct.DESCRIPTION AS EDU_NAME,
	            r.class_name, r.type, r.year, r.class_no, r.term,r.range_real,
	            ru.use_date
	    	FROM teacher t
	  		INNER JOIN co_city cc
	  		    ON cc.city=t.county
	  		INNER JOIN co_subcity csc
	  		    ON csc.city=t.county AND csc.subcity=t.district
	  		LEFT JOIN code_table ct
	  			ON t.education=ct.item_id AND ct.type_id='04'
	  		INNER JOIN room_use ru
	  			ON ru.teacher_id = t.idno and t.teacher=ru.isteacher
	  		INNER JOIN `require` r
	  			ON ru.year = r.year and ru.term = r.term and ru.class_id = r.class_no
	        WHERE t.name NOT IN ('教務組', '教務組1', '總務組' ,'綜企組')
            $where";

        return $sql;
    }

    public function getJobNoAllSql($job, $where)
    {
        $sql = "SELECT DISTINCT
          t.name, t.another_name AS alias,t.idno, t.id, t.major AS SCHOOL, t.birthday AS birth, t.email, t.institution AS corp, t.zipcode AS zone, t.job_title AS position, t.h_tel AS telo, t.h_tel2 AS telh, t.mobile AS mobil, t.career,
          t.teacher as teacher,t.teacher as assistant,r.seq_no,
          concat(cc.city_name,csc.subcity_name,t.route) AS addr,
        (
            SELECT DESCRIPTION
            FROM code_table ct
            WHERE
                TYPE_ID = '08'
                and ct.item_id = t.hire_type
        ) as DESCRIPTION,
        ct.DESCRIPTION AS EDU_NAME,
        r.class_name, r.type, r.year, r.class_no, r.term,r.range_real,
        ru.use_date
        FROM teacher t
        INNER JOIN co_city cc
            ON cc.city=t.county
        INNER JOIN co_subcity csc
            ON csc.city=t.county AND csc.subcity=t.district
        LEFT JOIN code_table ct
            ON t.education=ct.item_id AND ct.type_id='04'
        INNER JOIN room_use ru
            ON ru.teacher_id = t.idno and t.teacher=ru.isteacher
        INNER JOIN `require` r
            ON ru.year = r.year and ru.term = r.term and ru.class_id = r.class_no
        WHERE t.name NOT IN ('教務組', '教務組1', '總務組' ,'綜企組')  and t.teacher_type = ".$this->db->escape(addslashes($job))."
        $where";

        return $sql;
    }

    public function getCANTEACHData($techId)
    {
        $sql = "SELECT b.DESCRIPTION AS COURSE_NAME " .
            "FROM canteach a " .
            "LEFT JOIN code_table b ON a.COURSE_CODE = b.ITEM_ID AND b.TYPE_ID = '17' " .
            "WHERE a.ID='$techId' " .
            "ORDER BY b.DESCRIPTION";
        $query = $this->db->query($sql);

        return $this->QueryToArray($query);
    }
    public function getCANTEACHData_NEW($techId,$class_no,$year,$term)
    {
        $sql = "SELECT b.description AS COURSE_NAME FROM room_use a
                LEFT JOIN canteach c
                ON a.use_id = c.course_code
                LEFT JOIN code_table b 
                ON c.course_code = b.ITEM_ID AND b.TYPE_ID = '17' 
                WHERE c.ID=".$this->db->escape(addslashes($techId))." AND a.`year` = ".$this->db->escape(addslashes($year))." AND a.class_id = ".$this->db->escape(addslashes($class_no))." AND a.term = ".$this->db->escape(addslashes($term))."
                GROUP BY description";
        $query = $this->db->query($sql);
        return $this->QueryToArray($query);
    }
    public function csvexport($filename, $query_start_date, $query_end_date, $dayOfWeek, $query_class_name, $query_type, $use_s_date, $use_e_date, $t_name, $t_source, $cre_s_date, $cre_e_date, $edu, $job)
    {
        $data = $this->getCoursePublicData($query_class_name, $query_type, $use_s_date, $use_e_date, $t_name, $t_source, $cre_s_date, $cre_e_date, $edu, $job);
       
        $filename = iconv("UTF-8", "BIG5", date('Y-m-d') . '_detail.csv');

        header("Content-type: application/csv");
        header("Content-Disposition: attachment; filename=$filename");

        echo iconv("UTF-8", "BIG5", "臺北市政府公務人員訓練處,");
        echo iconv("UTF-8", "BIG5", "講座基本資料查詢\r\n");
        echo iconv("UTF-8", "BIG5", '"姓名","');
        echo iconv("UTF-8", "BIG5", '別名","');
        echo iconv("UTF-8", "BIG5", '身分證","');
        echo iconv("UTF-8", "BIG5", '聘請類別","');
        echo iconv("UTF-8", "BIG5", '學歷","');
        echo iconv("UTF-8", "BIG5", '生日","');
        echo iconv("UTF-8", "BIG5", '公司電話","');
        echo iconv("UTF-8", "BIG5", '家用電話","');
        echo iconv("UTF-8", "BIG5", '手機","');
        echo iconv("UTF-8", "BIG5", '班期名稱","');
        echo iconv("UTF-8", "BIG5", '期別","');
        echo iconv("UTF-8", "BIG5", '上課日期","');
        echo iconv("UTF-8", "BIG5", '郵遞區號","');
        echo iconv("UTF-8", "BIG5", '地址","');
        echo iconv("UTF-8", "BIG5", '任職機關","');
        echo iconv("UTF-8", "BIG5", '職稱","');
        echo iconv("UTF-8", "BIG5", 'Email","');
        echo iconv("UTF-8", "BIG5", '經歷","');
        echo iconv("UTF-8", "BIG5", '可授課程"');
        echo "\r\n";

        foreach ($data as $val) {
            $val['birth'] = date('Y-m-d',strtotime($val['birth']));
            $val['use_date'] = date('Y-m-d',strtotime($val['use_date']));
            echo (mb_convert_encoding('"'.$this->convertCsv($val['name']) .'","',"BIG5","UTF-8"));
            echo mb_convert_encoding($this->convertCsv($val['alias']) .'","',"BIG5","UTF-8");
            echo mb_convert_encoding($this->convertCsv($val['idno']) .'","',"BIG5","UTF-8");
            echo mb_convert_encoding($this->convertCsv($val['DESCRIPTION']) .'","',"BIG5","UTF-8");
            echo mb_convert_encoding($this->convertCsv($val['EDU_NAME']) .'","',"BIG5","UTF-8");
            echo mb_convert_encoding($this->convertCsv($val['birth']) .'","',"BIG5","UTF-8");
            echo mb_convert_encoding($this->convertCsv($val['telo']) .'","',"BIG5","UTF-8");
            echo mb_convert_encoding($this->convertCsv($val['telh']) .'","',"BIG5","UTF-8");
            echo mb_convert_encoding($this->convertCsv($val['mobil']) .'","',"BIG5","UTF-8");
            echo mb_convert_encoding($this->convertCsv($val['class_name']) .'","',"BIG5","UTF-8");
            echo mb_convert_encoding($this->convertCsv($val['term']) .'","',"BIG5","UTF-8");
            echo mb_convert_encoding($this->convertCsv($val['use_date']) .'","',"BIG5","UTF-8");
            echo mb_convert_encoding($this->convertCsv($val['zone']) .'","',"BIG5","UTF-8");
            echo mb_convert_encoding($this->convertCsv($val['addr']) .'","',"BIG5","UTF-8");
            echo mb_convert_encoding($this->convertCsv($val['corp']) .'","',"BIG5","UTF-8");
            echo mb_convert_encoding($this->convertCsv($val['position']) .'","',"BIG5","UTF-8");
            echo mb_convert_encoding($this->convertCsv($val['email']) .'","',"BIG5","UTF-8");
            echo mb_convert_encoding($this->convertCsv($val['career']) .'","',"BIG5","UTF-8");
            echo mb_convert_encoding($this->convertCsv($val['CAN_TEACH']) .'"',"BIG5","UTF-8");
            echo "\r\n";
        }
    }

    private function convertCsv($str){
        return str_replace(',', '，', $str);
    }

    public function getDataFunction($YEAR, $CLASS_NO, $TERM)
    {
        $query_cond_string = sprintf("`require`.YEAR=%s AND `require`.CLASS_NO=%s and `require`.TERM=%s ", $this->db->escape(addslashes($YEAR)), $this->db->escape(addslashes($CLASS_NO)), $this->db->escape(addslashes($TERM)));
        $sql = sprintf("
            select `require`.CLASS_CONTENT AS CLASS_CONTENT2 ,`require`.*, BS_user.name as FIRST_NAME,'' as LAST_NAME,BS_user.OFFICE_TEL,code_table.DESCRIPTION,code_table.ADD_VAL1,code_table.ADD_VAL2
            from `require`
            left join BS_user on `require`.WORKER=BS_user.idno
            left join code_table on code_table.TYPE_ID='26' and BS_user.idno =  code_table.ITEM_ID
            where %s
            ",
            $query_cond_string
        );

        $query = $this->db->query($sql);

        return $this->QueryToArray($query);

    }

    public function get_room_count($query_detail_string)
    {
        $sql = "select count(*) as count from (select a.room_id  from room_use a where {$query_detail_string} group by room_id ) as zz";
        $query = $this->db->query($sql);

        return $this->QueryToArray($query);

    }

    public function get_list($query_detail_string)
    {
        $weekarray = array("日", "一", "二", "三", "四", "五", "六");

        $sql ="select rank() over(partition by use_date1, cday order by ltime, classroom_name) as `key`, 
        z.* 
        from ( 
        select me.tt,a.use_period,a.use_date as use_date2,a.use_date as use_date1, a.use_id,a.year,a.class_id
        ,a.term,date_format(a.use_date,'%m-%d') as use_date,dayofweek(a.use_date) as cday,min(b.from_time) || max(b.to_time) as ltime 
        ,c.description as class_name,NVL(cr.room_sname, cr.room_name) as classroom_name ,r.contactor,r.tel 
        from room_use a 
        left outer join ( 
        select concat(xxx.use_period) as dd ,xxx.tt,xxx.use_date 
        from ( 
        select yyy.use_period,yyy.use_date,max(yyy.tt) as tt 
        from ( 
        select rank() over(partition by zz.use_date, zz.use_period order by zz.`key`) as `key`,zz.use_period
        ,concat(zz.teacher_id)  as tt ,zz.use_date 
        from ( 
        select rank() over(partition by use_date, use_period order by teacher_id) as `key`,a.use_period,a.teacher_id,use_date 
        from room_use a  where 1=1 and ".$query_detail_string." 
         group by a.use_period,a.teacher_id,a.use_date order by a.use_date,a.use_period,a.teacher_id ,`key` 
         ) zz order by zz.use_date,zz.use_period
         ) yyy 
         group by use_period,use_date ) xxx
          group by tt,use_date order by use_date,dd ) me
         
         
          on a.use_period in (select regexp_substr(me.dd,'[^,]+') from dual) 
         and a.use_date=me.use_date left outer join periodtime b on a.use_period=b.id and a.year=b.year and a.term=b.term and a.class_id=b.class_no 
         and me.use_date = b.course_date left outer join code_table c on a.use_id=c.item_id and c.type_id='17' 
         left outer join venue_information cr on cr.room_id=a.room_id left outer join `require` r on a.year=r.year and a.term=r.term and a.class_id=r.class_no 
         where 1=1 and ".$query_detail_string." 
         group by me.tt,a.use_period,a.use_date,a.use_date,a.use_id,a.year,a.class_id,a.term,a.use_date,c.description,NVL(cr.room_sname, cr.room_name)
         ,r.contactor,r.tel Order by a.year,a.class_id,a.term,a.use_date,min(b.from_time), a.use_period ) z
        ";

        $query = $this->db->query($sql);

        $sqlData = $this->QueryToArray($query);

        $list = array();

        // return $sqlData;

        for ($i = 0; $i < sizeof($sqlData); $i++) {
            $sqlData[$i]["cday"] = $weekarray[$sqlData[$i]["cday"] - 1];
            if ($sqlData[$i]["ltime"] != "") {
                $sqlData[$i]["ltime"] = substr($sqlData[$i]["ltime"], 0, 2) . ":" . substr($sqlData[$i]["ltime"], 2, 2) . "~" . substr($sqlData[$i]["ltime"], 4, 2) . ":" . substr($sqlData[$i]["ltime"], 6, 2);
            }

            $sql = "select nvl(d.ALIAS,d.name) as name,d.teacher as TEACHER,d.teacher as ASSISTANT,a.title,a.sort 
            from room_use a 
            left join teacher d on a.teacher_id=d.idno and a.isteacher=d.teacher 
            where a.year=".$this->db->escape(addslashes($sqlData[$i]["year"]))." AND a.class_id=".$this->db->escape(addslashes($sqlData[$i]["class_id"]))." and a.term=".$this->db->escape(addslashes($sqlData[$i]["term"]))." and a.use_id=".$this->db->escape(addslashes($sqlData[$i]["use_id"]))." 
            and a.use_date=date_format(".$this->db->escape(addslashes($sqlData[$i]["use_date2"])).",'%Y-%m-%d')
            and a.teacher_id in (select regexp_substr(".$this->db->escape(addslashes($sqlData[$i]["tt"])).",'[^,]+') from dual 
            ) 
            group by d.another_name,d.name,d.teacher,d.ASSISTANT,a.title,a.sort order by d.teacher desc ,a.sort asc,d.teacher
            ";

           
            $query = $this->db->query($sql);

            $sqlSubData = $this->QueryToArray($query);

            for ($s = 0; $s < sizeof($sqlSubData); $s++) {
                if ($sqlSubData[$s]["name"] == "教務組" || $sqlSubData[$s]["title"] == "無") {
                    $sqlData[$i]["name"] .= $sqlSubData[$s]["name"] . "<br>";
                } elseif ($sqlSubData[$s]["title"] != "") {
                    $sqlData[$i]["name"] .= $sqlSubData[$s]["name"] . " " . $sqlSubData[$s]["TITLE"] . "<br>";
                } else {
                    if ($sqlSubData[$s]["TEACHER"] == 'Y') {
                        if(isset($sqlData[$i]["NAME"]))
                            $sqlData[$i]["NAME"] .= $sqlSubData[$s]["name"] . " " . "老師" . "<br>";
                        else
                        $sqlData[$i]["name"] = "";
                    }
                    if ($sqlSubData[$s]["ASSISTANT"] == 'Y') {
                        if(isset($sqlData[$i]["NAME"]))
                            $sqlData[$i]["name"] .= $sqlSubData[$s]["name"] . " " . "(助)" . "<br>";
                        else
                        $sqlData[$i]["name"] = ""; 
                    }
                }
            }
            if ($sqlData[$i]["name"] == '') {
                $sql = "select count(*) from code_table where TYPE_ID = '17' and add_val2 = 'Y'  and item_id = ".$this->db->escape(addslashes($sqlData[$i]["use_id"]))."";
                $query = $this->db->query($sql);
                $t_count = $this->QueryToArray($query);
                if ($t_count > 0) {
                    $sqlData[$i]["name"] = '教務組';
                }
            }
            

            // if (($list['rows'][count($list['rows']) - 1]["USE_DATE"] == $sqlData[$i]["USE_DATE"]) && ($list['rows'][count($list['rows']) - 1]["NAME"] == $sqlData[$i]["NAME"]) && ($list['rows'][count($list['rows']) - 1]["CLASS_NAME"] == $sqlData[$i]["CLASS_NAME"])) {
            //     $list['rows'][count($list['rows']) - 1]["LTIME"] = substr($list['rows'][count($list['rows']) - 1]["LTIME"], 0, 6) . substr($sqlData[$i]["LTIME"], 6, 5);
            // } else {
            //     $list['rows'][] = $sqlData[$i];
            // }
            $list[] = $sqlData[$i];
        }

        return $list;
    }

    public function get_Classroom_Name_List($query_detail_string)
    {
        $sql = sprintf("
            select distinct a.room_id,c.name
            from room_use a
            left outer join periodtime b on a.use_period=b.id
            left outer join venue_information c on a.room_id=c.room_id
            Where 1=1 and  %s
            ",

            $query_detail_string
        );
        $query = $this->db->query($sql);

        $sqlSubData = $this->QueryToArray($query);

        return $sqlSubData;
    }

    public function get_mixlist($YEAR, $CLASS_NO, $TERM)
    {
        $sql = "select CLASS_NAME,TEACHER_NAME,PLACE,date_format(start_date,'mm/dd') as start_date,date_format(end_date,'mm/dd') as end_date from require_online where year=".$this->db->escape(addslashes($YEAR))." and class_no=".$this->db->escape(addslashes($CLASS_NO))." and term=".$this->db->escape(addslashes($TERM))." ORDER BY ID";

        $query = $this->db->query($sql);

        $sqlSubData = $this->QueryToArray($query);

        return $sqlSubData;
    }
}

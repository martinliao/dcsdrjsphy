<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

require_once APPPATH . "models/Common_model.php";
class Daily_lecture_data_model extends Common_model
{
    public function getDailyLectureData($queryYear, $queryStartDate, $queryEndDate, $queryInnerRoom, $queryNoTeacher = "",$sites = "")
    {
        if ($sites !="") {
            $sites = explode(",", $sites);
            for($i=0; $i<count($sites); $i++){
                $sites[$i] = $this->db->escape(addslashes($sites[$i]));
            }
            $sites = implode(",", $sites);

            $innerWhere = " AND SUBSTR(cr.room_sname, 1, 1) IN (".$sites.")";
        } elseif($queryInnerRoom == "Y" && $sites ==""){//只限公訓教室
            $innerWhere = " AND SUBSTR(cr.room_sname, 1, 1) IN ('B', 'C', 'E')";
        }else {
            $innerWhere = "";
        }

        $queryArray = array();
        $queryArray_mesh = array();
        $queryArray_end = array();
        if ($queryNoTeacher == "T_N") //不顯示老師資料
        {
            $queryArray = $this->sql1($innerWhere, $queryStartDate, $queryEndDate, $queryYear);
        } else if ($queryNoTeacher == "T_M"){ //合併同班期、同講座
            
   
            $queryArray = $this->sql2($innerWhere, $queryStartDate, $queryEndDate, $queryYear);

                // $queryArray_mesh['sss'] = array(0 => 100, "color" => "red");

            //    die(var_dump(array_keys($queryArray_mesh,'sss')));
             //合併同班期、同講座

            

                foreach($queryArray as $item)
                {     
                    $key_v  = ($item['year'].'-');
                    $key_v .= ($item['class_id'].'-');
                    $key_v .= ($item['term'].'-');
                    $key_v .= ($item['teacher_id'].'-');
                    $key_v .= ($item['use_date'].'-');

                    if(array_key_exists($key_v, $queryArray_mesh)){
                        // isset($queryArray_mesh[$key_v])){
                        $arr_tmp  =$queryArray_mesh[$key_v];

                        if(intval($item['from_time']) < intval($arr_tmp['from_time'])){
                            $arr_tmp['from_time'] = $item['from_time'];
                            $arr_tmp['description'] = $item['description'];
                        }

                        if(intval($item['to_time']) > intval($arr_tmp['to_time'])){
                            $arr_tmp['to_time'] = $item['to_time'];
                        }
                        
                        $queryArray_mesh[$key_v] = $arr_tmp;
                    }else{
                        $queryArray_mesh[$key_v] = $item;
                    }                    
                }
                $arr_i = 0;
                foreach($queryArray_mesh as $item2)
                {   
                    $queryArray_end[$arr_i] = $item2;
                    $arr_i++;
                }               

                $queryArray = $queryArray_end;            
     

        }else{
            
            $queryArray = $this->sql2($innerWhere, $queryStartDate, $queryEndDate, $queryYear);
      
        }

        $data = array();
        $opened = false;
        for ($i = 0; $i < sizeof($queryArray); $i++) {
            // "報到暨班務說明"不顯示，但是後一堂課判斷為今日開班
            if ($queryNoTeacher != "Y") {
                /*
                代碼    說明
                O00001    報到(含班務說明表
                O00003    報到暨班務說明
                O00004    報到
                O00005    班務介紹
                004643    報到暨開訓
                006430    報到暨開訓儀式
                017826    迎賓暨報到
                017835    報到及課程說明
                 */
                //if ($arr['item_id']==='報到暨班務說明') {
                $firstday_item_ary = array("O00001", "O00003", "O00004", "O00005", "004643", "006430", "017826", "017835"); //第一節課是教務班務說明->第一天上課

                if (in_array($queryArray[$i]['item_id'], $firstday_item_ary)) {
                    $opened = true;
                    continue;
                }
                // 設定為今日開班
                if ($opened) {
                    $queryArray[$i]['OPENED'] = true;
                    $opened = false;
                } else {
                    $queryArray[$i]['OPENED'] = false;
                }
                //修正顯示時間
                $queryArray[$i]['from_time'] = substr($queryArray[$i]['from_time'], 0, 2) . ':' . substr($queryArray[$i]['from_time'], 2, 2);
                $queryArray[$i]['to_time'] = substr($queryArray[$i]['to_time'], 0, 2) . ':' . substr($queryArray[$i]['to_time'], 2, 2);
            }
            $data[] = $queryArray[$i];
        }

        for ($i = 0; $i < count($data); $i++) {
            $data[$i]['DEV_NAME'] = $this->getDevName($data[$i]['year'], $data[$i]['class_id'], $data[$i]['term']);
        }

        return $data;

    }

    public function sql1($innerWhere, $queryStartDate, $queryEndDate, $queryYear)
    {
        $sql = "SELECT seq_no,belongto, room_id, use_date, year, class_id, term, pcount, class_name, workername
                FROM ( SELECT DISTINCT cr.room_bel as belongto, cr.room_id as rid, IFNULL(cr.room_sname, cr.room_name) as room_id, a.use_date, a.year, a.class_id, a.term,
                (select count('x') from online_app p where yn_sel NOT IN ('2','6','7') and p.year=a.year and p.class_no=a.class_id and p.term=a.term) as pcount,
                    c.class_name,v.name as workername,c.seq_no
                    FROM room_use a LEFT JOIN code_table b ON a.use_id=b.item_id and b.type_id=17
                LEFT JOIN `require` c ON a.year=c.year and a.class_id=trim(c.class_no) and a.term=c.term AND IFNULL(c.is_cancel, '0') = '0' 
                LEFT JOIN require_list rl ON rl.class_no=c.class_no AND rl.year=c.year AND rl.term=c.term
                LEFT JOIN BS_user v ON v.idno=c.worker
                LEFT JOIN venue_information cr ON a.room_id=cr.room_id
            WHERE
                rl.mail_mag_count > 0 AND cr.room_type = '01' " . $innerWhere . "
                AND a.use_date >=" . $this->db->escape(addslashes($queryStartDate)) . " and a.use_date<=" . $this->db->escape(addslashes($queryEndDate)) . "
                AND c.year=" . $this->db->escape(addslashes($queryYear)) . " AND c.class_status IN (2, 3) ORDER BY a.use_date, cr.room_name) as zz ORDER BY use_date, belongto, room_id";

        $query = $this->db->query($sql);

        return $this->QueryToArray($query);
    }

    public function sql2($innerWhere, $queryStartDate, $queryEndDate, $queryYear)
    {
        $sql = "SELECT
        seq_no,belongto, item_id, room_id, use_date, year, class_id, term, teacher_id, isteacher,
        pcount, dining_count,
        `description`,
        class_name,
        `name`, corp, position,
        workername,
        CASE
            WHEN CAST((min(from_time)) AS DECIMAL(10,2))>1200 THEN 1
            ELSE 0
        END AS after_count,
        min(from_time) as from_time, max(to_time) as to_time
        FROM (
            SELECT DISTINCT
                c.seq_no,cr.room_bel as belongto, b.item_id, cr.room_id as rid, IFNULL(cr.room_sname, cr.room_name) as room_id,
                a.use_date, a.year, a.class_id, a.term, a.teacher_id, a.use_period, a.isteacher,
                (select count('x') from online_app p where yn_sel NOT IN ('2','6','7') and p.year=a.year and p.class_no=a.class_id and p.term=a.term) as pcount,
                (select count(1) from dining_teacher dt where a.year=dt.year and a.class_id=dt.class_no and a.term=dt.term and a.use_date=dt.use_date and a.teacher_id=dt.id ) as dining_count,
                b.description,
                c.class_name,
                e.to_time, e.from_time,
                IFNULL(f.ALIAS,f.name) as name, f.corp, f.position,
                (v.name) workername
            FROM room_use a
            LEFT JOIN code_table b
                ON a.use_id=b.item_id and b.type_id=17
            LEFT JOIN `require` c
                ON a.year=c.year and a.class_id=trim(c.class_no) and a.term=c.term
                AND IFNULL(c.is_cancel, '0') = '0' 
            LEFT JOIN require_list rl
                ON  rl.class_no=c.class_no AND rl.year=c.year AND rl.term=c.term
            LEFT JOIN periodtime e
                ON a.use_period=e.id and a.year=e.year and a.class_id=e.class_no and a.term=e.term
            LEFT JOIN (SELECT DISTINCT another_name as alias, name, institution as corp,job_title as position,teacher, idno FROM teacher) as f
                ON a.teacher_id=f.idno and a.isteacher = f.teacher
            LEFT JOIN BS_user v
                ON v.idno=c.worker
            LEFT JOIN venue_information cr
                ON a.room_id=cr.room_id
            WHERE
                rl.mail_mag_count > 0 AND 
                cr.room_type = '01' " . $innerWhere . "
                AND a.use_date >=" . $this->db->escape(addslashes($queryStartDate)) . " and a.use_date<=" . $this->db->escape(addslashes($queryEndDate)) . "
                AND a.use_id=IFNULL(e.course_code,a.use_id)
                AND a.use_date=IFNULL(e.course_date,a.use_date)
                AND e.course_date is not null
                AND c.year=" . $this->db->escape(addslashes($queryYear)) . " AND c.class_status IN (2, 3)
                AND (from_time REGEXP '^[0-9]+$')
                AND LENGTH(from_time)>0
            ORDER BY a.use_date, cr.room_name) as zz
        GROUP BY
            belongto, item_id, room_id, use_date, year, class_id, term, teacher_id, isteacher,
            pcount, dining_count,
            description,
            class_name,
            name, corp, position,
            workername
        ORDER BY
            use_date, belongto, room_id, from_time, year, class_id, term, isteacher desc";

        $query = $this->db->query($sql);

        return $this->QueryToArray($query);

    }

    public function getDevName($year, $class_no, $term)
    {
        $sql = sprintf("select b.name from `require` a join
        bureau b on a.dev_type = b.bureau_id where a.class_no =
        ".$this->db->escape(addslashes($class_no))." and year = ".$this->db->escape(addslashes($year))." and term = ".$this->db->escape(addslashes($term))."");

        $query = $this->db->query($sql);

        $datas = $this->QueryToArray($query);

        $data = array();
        for ($i = 0; $i < sizeof($datas); $i++) {
            $data = $datas[$i];
        }

        if (!empty($data)) {
            return $data['name'];
        } else {
            return '';
        }
    }

    public function csvexport($filename, $query_start_date, $query_end_date, $dayOfWeek, $noTeacher, $year, $detailcheck,$sites="")
    {
        $data = $this->getDailyLectureData($year, $query_start_date, $query_end_date, $detailcheck, $noTeacher,$sites);
        

        $filename = date("Ymd") . '.csv';

        header("Content-type: application/csv");
        header("Content-Disposition: attachment; filename=$filename");

        if ($noTeacher == "Y") // 給職工每日查上課班級用，2015/11/04
        {
            echo iconv('UTF-8', 'BIG5', "臺北市政府公務人員訓練處,");
            echo iconv('UTF-8', 'BIG5', "當週每日研習班次講座資料\r\n");
            echo iconv('UTF-8', 'BIG5', "{$query_start_date}至{$query_end_date}\r\n");
            echo iconv('UTF-8', 'BIG5', "上課時間,");
            echo iconv('UTF-8', 'BIG5', "班期名稱,");
            echo iconv('UTF-8', 'BIG5', "局處,");
            echo iconv('UTF-8', 'BIG5', "期別,");
            echo iconv('UTF-8', 'BIG5', "調訓人數,");
            echo iconv('UTF-8', 'BIG5', "承辦人,");
            echo iconv('UTF-8', 'BIG5', "教室代碼 \r\n");
        } else {
            echo iconv('UTF-8', 'BIG5', "臺北市政府公務人員訓練處,");
            echo iconv('UTF-8', 'BIG5', "當週每日研習班次講座資料\r\n");
            echo iconv('UTF-8', 'BIG5', "{$query_start_date}至{$query_end_date}\r\n");
            echo iconv('UTF-8', 'BIG5', "今日開班,");
            echo iconv('UTF-8', 'BIG5', "班期名稱,");
            echo iconv('UTF-8', 'BIG5', "局處,");
            echo iconv('UTF-8', 'BIG5', "期別,");
            echo iconv('UTF-8', 'BIG5', "調訓人數,");
            echo iconv('UTF-8', 'BIG5', "承辦人,");
            echo iconv('UTF-8', 'BIG5', "教室代碼,");
            echo iconv('UTF-8', 'BIG5', "課程名稱,");
            echo iconv('UTF-8', 'BIG5', "時間,");
            echo iconv('UTF-8', 'BIG5', "講師,");
            echo iconv('UTF-8', 'BIG5', "講師背景,");
            echo iconv('UTF-8', 'BIG5', "上課日期 ,");
            echo iconv('UTF-8', 'BIG5', "用餐否 ,");
            echo iconv('UTF-8', 'BIG5', "下午上課 \r\n");
        }

        if ($noTeacher == "Y") {
            foreach ($data as $val) {
                $COL[1] = iconv("UTF-8", "BIG5", substr($val['use_date'],0,10));
                $COL[2] = iconv("UTF-8", "BIG5", $val['class_name']);
                $COL[3] = iconv("UTF-8", "BIG5", $val['DEV_NAME']);
                $COL[4] = iconv("UTF-8", "BIG5", $val['term']);
                $COL[5] = iconv("UTF-8", "BIG5", $val['pcount']);
                $COL[6] = iconv("UTF-8", "BIG5", $val['workername']);
                $COL[7] = iconv("UTF-8", "BIG5", $val['room_id']);
                $COL[8] = iconv("UTF-8", "BIG5", '');
                for ($i = 1; $i < 8; $i++) {
                    echo $COL[$i] . ",";
                }
                echo $COL[$i] . "\r\n";
            }
        } else {
            foreach ($data as $val) {
                if ($val['OPENED']) {
                    $COL[1] = 'Y';
                } else {
                    $COL[1] = '';
                }
                $COL[2] = iconv("UTF-8", "BIG5", $val['class_name']);
                $COL[3] = iconv("UTF-8", "BIG5", $val['DEV_NAME']);
                $COL[4] = iconv("UTF-8", "BIG5", $val['term']);
                $COL[5] = iconv("UTF-8", "BIG5", $val['pcount']);
                $COL[6] = iconv("UTF-8", "BIG5", $val['workername']);
                $COL[7] = iconv("UTF-8", "BIG5", $val['room_id']);
                $COL[8] = iconv("UTF-8", "BIG5//IGNORE", $val['description']);
                $COL[9] = iconv("UTF-8", "BIG5", $val['from_time'] . "-" . $val['to_time']);
                $COL[10] = iconv("UTF-8", "BIG5//IGNORE", $val['name']);
                if ($val['isteacher'] == '') {
                    $COL[11] = iconv('UTF-8', 'BIG5//IGNORE', "(助)" . $val['corp']) . "-" . iconv("UTF-8", "BIG5", $val['position']);
                } else {
                    $COL[11] = iconv("UTF-8", "BIG5//IGNORE", $val['corp']) . "-" . iconv("UTF-8", "BIG5", $val['position']);
                }
                if ($val['from_time'] == "") {$COL[9] = "";}
                if ($val['corp'] == "") {$COL[11] = "";}
                $COL[12] = iconv("UTF-8", "BIG5", substr($val['use_date'],0,10));
                if ($val['dining_count'] > 0) {
                    $COL[13] = "Y";
                } else {
                    $COL[13] = '';
                }
                if ($val['after_count'] > 0) {
                    $COL[14] = "Y";
                } else {
                    $COL[14] = '';
                }

                for ($i = 1; $i < 14; $i++) {
                    echo $COL[$i] . ",";
                }
                echo $COL[$i] . "\r\n";
            }
        }

    }

}

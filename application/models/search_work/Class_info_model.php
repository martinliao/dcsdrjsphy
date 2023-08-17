<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

require_once APPPATH . "models/Common_model.php";
class Class_info_model extends Common_model
{
    public function getclass_info($year, $start_date, $end_date, $schedule)
    {

        $query_cond_string = "";

        if (($start_date != "") && ($end_date != "")) {
            $query_cond_string = " a.use_date >= ".$this->db->escape(addslashes($start_date))." and a.use_date <= ".$this->db->escape(addslashes($end_date))."";
        } elseif (($start_date == "") && ($end_date != "")) {
            $query_cond_string = "  a.use_date <= ".$this->db->escape(addslashes($end_date))." ";
        } elseif (($start_date !== "") && ($end_date == "")) {
            $query_cond_string = "  a.use_date >= ".$this->db->escape(addslashes($start_date))." ";
        } else {
            $query_cond_string = '1=1';
        }

        $where = "";
        $orderby = "";
        $sql = "SELECT class_name,term,START_DATE1,END_DATE1,OBJ,RESPONDANT,CONTENT,group_concat(name) as name
        from(
        SELECT distinct class_name,term,DATE_FORMAT(START_DATE1,'%Y/%m/%d') as START_DATE1,
        DATE_FORMAT(END_DATE1,'%Y/%m/%d') as END_DATE1
        ,OBJ,RESPONDANT,CONTENT,name
        from(SELECT
        room_id, use_date, year, class_id, term, teacher_id, isteacher,
        pcount, dining_count,
           description,
        class_name,
        name, corp, position,
        workername,
        START_DATE1,
        END_DATE1,
        CLASS_NO,
        OBJ,
        RESPONDANT,
        CONTENT,
        CASE
                    WHEN CAST(min(from_time) AS DECIMAL(10,2))>1200 THEN 1
                    ELSE 0
            END AS after_count,
        min(from_time) as from_time, max(to_time) as to_time
        FROM (

                SELECT DISTINCT
                IFNULL(cr.room_sname, cr.room_name) as room_id,
                        a.use_date, a.year, a.class_id, a.term, a.teacher_id, a.use_period, a.isteacher,
                        (select count('x') from online_app p where yn_sel NOT IN ('2','6','7') and p.year=a.year and p.class_no=a.class_id and p.term=a.term) as pcount,
                        (select count(1) from dining_teacher dt where a.year=dt.year and a.class_id=dt.class_no and a.term=dt.term and a.use_date=dt.use_date and a.teacher_id=dt.id ) as dining_count,
                        b.description,
                    c.class_name,
                    c.START_DATE1,
                    c.END_DATE1,
                    c.CLASS_NO,
                    c.OBJ,
                    c.RESPONDANT,
                    c.CONTENT,
                    e.to_time, e.from_time,
                f.name, f.corp, f.position,
                (v.name) AS workername
                    FROM room_use a
            LEFT JOIN code_table b
                ON a.use_id=b.item_id and b.type_id=17
            LEFT JOIN `require` c
                ON a.year=c.year and a.class_id=trim(c.class_no) and a.term=c.term
                and ifnull(c.is_cancel is null,'0')='0' AND c.class_name LIKE ".$this->db->escape("%".addslashes($schedule)."%")."  
            LEFT JOIN require_list rl
                ON  rl.class_no=c.class_no AND rl.year=c.year AND rl.term=c.term
            LEFT JOIN periodtime e
                ON a.use_period=e.id and a.year=e.year and a.class_id=e.class_no and a.term=e.term
            LEFT JOIN (SELECT DISTINCT name, institution AS corp, job_title AS position, idno, teacher FROM teacher) f
                ON a.teacher_id=f.idno and a.isteacher = f.teacher
            LEFT JOIN BS_user v
                ON v.idno=c.worker
            LEFT JOIN venue_information cr
                ON a.room_id=cr.room_id
            WHERE
                rl.mail_mag_count > 0 AND cr.room_type = '01' AND left(cr.room_sname, 1) IN ('B', 'C', 'E')
                    AND a.use_id=IFNULL(e.course_code,a.use_id)
                    AND a.use_date=IFNULL(e.course_date,a.use_date)
                    AND c.year=" . $this->db->escape(addslashes($year)) . "  AND c.class_status IN (2, 3) AND

                    " . $query_cond_string . "

            ORDER BY a.use_date, cr.room_name
            ) AS K
            GROUP BY
            room_id, use_date, year, class_id, term, teacher_id, isteacher,
            pcount, dining_count,
                    description,
            class_name,
            name, corp, position,
            workername,
            START_DATE1,
            END_DATE1,
            CLASS_NO,
            OBJ,
            RESPONDANT,
            CONTENT
            ";

        $orderby = " ORDER BY
        use_date, room_id, from_time, year, class_id, term ";

        $sql = $sql . " " . $where . " " . $orderby . ")z)y
        group by class_name,term,START_DATE1,END_DATE1,OBJ,RESPONDANT,CONTENT ";

        $query = $this->db->query($sql);

        return $this->QueryToArray($query);

        // $data1 = array();
        // $opened = false;
        // if ($rows) {
        //     while ($arr = $rows->FetchRow()) {
        //         // "報到暨班務說明"不顯示，但是後一堂課判斷為今日開班
        //         if ($arr['DESCRIPTION'] === '報到暨班務說明') {
        //             $opened = true;
        //             continue;
        //         }
        //         // 設定為今日開班
        //         if ($opened) {
        //             $arr['OPENED'] = true;
        //             $opened = false;
        //         } else {
        //             $arr['OPENED'] = false;
        //         }
        //         //修正顯示時間
        //         $arr['FROM_TIME'] = substr($arr['FROM_TIME'], 0, 2) . ':' . substr($arr['FROM_TIME'], 2, 2);
        //         $arr['TO_TIME'] = substr($arr['TO_TIME'], 0, 2) . ':' . substr($arr['TO_TIME'], 2, 2);

        //         $year = $arr["YEAR"];
        //         $term = $arr["TERM"];
        //         $class_no = $arr["CLASS_NO"];
        //         $sql1 = "SELECT get_room_teacher (r.year,r.term,r.class_no) AS TEACHER_NAME
		// 			FROM REQUIRE r
		// 			WHERE
		// 			r.year = '" . $year . "'
		// 			AND r.CLASS_NO = '" . $class_no . "'
		// 			AND r.TERM = '" . $term . "'
		// 			ORDER BY start_date1,
		// 			         r.year,
		// 			         r.class_no,
		// 			         r.term";
        //         $rs1 = db_excute($sql1);
        //         $arr1 = $rs1->FetchRow();
        //         $arr["TEACHER_NAME"] = $arr1["TEACHER_NAME"];

        //         if ($data1[$class_no . "_" . $term] != 1) {
        //             $data['rows'][] = $arr;
        //             $data1[$class_no . "_" . $term] = 1;
        //         }

        //     }
        // }

    }

    public function csvexport($filename, $query_start_date, $query_end_date, $dayOfWeek,$year, $start_date, $end_date, $schedule)
    {
        $data = $this->getclass_info($year, $start_date, $end_date, $schedule);

        if ($filename == "") {
            $filename = date("Ymd") . '.csv';
        } else {
            $filename = $filename . '.csv';
        }

        header("Content-type: application/csv");
        header("Content-Disposition: attachment; filename=$filename");

        echo iconv('UTF-8', 'BIG5', "班期名稱,");
        echo iconv('UTF-8', 'BIG5', "研習日期,");
        echo iconv('UTF-8', 'BIG5', "研習目標,");
        echo iconv('UTF-8', 'BIG5', "研習對象,");
        echo iconv('UTF-8', 'BIG5', "課程內容,");
        echo iconv('UTF-8', 'BIG5', "講師 \r\n");
        
        foreach ($data as $val) {
            echo iconv('UTF-8', 'BIG5', $val["class_name"]." 第" . $val["term"]) . ',';
            echo iconv('UTF-8', 'BIG5', $val["START_DATE1"] ." ~ " .$val["END_DATE1"] ) . ',';
            echo iconv('UTF-8', 'BIG5', $val["OBJ"]) . ',';
            echo iconv('UTF-8', 'BIG5', $val["RESPONDANT"]) . ',';
            echo iconv('UTF-8', 'BIG5', $val["CONTENT"]) . ',';
            echo iconv('UTF-8', 'BIG5', "") . ',';
            echo "\r\n";
        }

    }

}

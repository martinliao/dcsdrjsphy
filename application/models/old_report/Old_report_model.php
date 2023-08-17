<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

require_once APPPATH . "models/Common_model.php";
class Old_report_model extends Common_model
{
    public function __construct()
    {
        parent::__construct();

        $this->odb = $this->load->database('old_oracle',TRUE);

    }

    public function getScoreList($queryYear, $searchStartDate, $searchEndDate, $search_class_no, $search_class_name,$rows="", $offset="")
    {
        $sql = "SELECT r.start_date1, r.end_date1, r.class_name, r.class_no, r.term, r.year, r.map1, r.map2, r.map3, r.map4, r.map5, r.map6, r.map7, r.map8,
                            ct_mc.description AS master_cate,
                            sc.name AS sub_cate,
                            bc.name AS bname,
                            fcc.ID question_id,
                            fcc.QID qid,
                            vaa.first_name AS worker_name
                        FROM `REQUIRE` r
                        LEFT JOIN VM_ALL_ACCOUNT vaa
                            ON r.worker=vaa.personal_id
                        LEFT JOIN CODE_TABLE ct_mc
                            ON r.type=item_id AND ct_mc.type_id='23'
                        LEFT JOIN SUB_CATEGORY sc
                            ON r.BEAURAU_ID=sc.cate_id AND r.type=sc.type
                        LEFT JOIN BUREAU_CODE bc
                            ON r.REQ_BEAURAU=bc.bureau_id or r.DEV_TYPE = bc.bureau_id
                        JOIN FEEDBACK_COURSE_COLLOCATION fcc
                            ON r.year=fcc.CLASS_YEAR AND r.term=fcc.CLASS_TERM AND r.class_no=fcc.CLASS_ID
                        WHERE r.class_status IN ('2', '3') ";

        if(''!=$queryYear)
        {
            $sql .= " AND r.year='{$queryYear}' ";
        }
        if(''!=$search_class_no)
        {
            $sql .= " AND UPPER(r.class_no) LIKE UPPER('%{$search_class_no}%')  ";
        }		 
        if(''!=$search_class_name)
        {
            $sql .= " AND UPPER(r.class_name) LIKE UPPER('%{$search_class_name}%')  ";
        }	
        if(''!=$searchStartDate)
        {
            $sql .= " AND r.start_date1 >= '{$searchStartDate}' ";
        }	 
        if(''!=$searchEndDate)
        {
            $sql .= " AND r.start_date1 <= '{$searchEndDate}' ";
        }		 
        $sql .= " ORDER BY r.type, r.dev_type";

        $limit = "";
        if($rows != "" && $offset != "") {
            $limit = " limit " . intVal($rows) . " offset " . intVal($offset);
        } else if($rows != "") {
            $limit = " limit " . intVal($rows);
        }

        $sql = $sql . " " . $limit;
        
        $query = $this->odb->query($sql);

        return $query->result_array();
    }

    public function get_cnt_gender_avg($classId, $year, $trem)
    {
        $sql = sprintf("SELECT round(avg(case when tmp.ANSWER = 5 then 5
                            when tmp.ANSWER = 4 then 4
                            when tmp.ANSWER = 3 then 3
                            when tmp.ANSWER = 2 then 2
                            when tmp.ANSWER = 1 then 1 end), 2) agg, b.GENDER
						FROM( SELECT A.SEQUENCE, A .ANSWER, A.ITEM
							FROM FEEDBACK_ANSWER A
							WHERE A .CLASS_ID = '%s'
							AND A .`YEAR` = %u
							AND A .CLASS_ITEM = %u
							and A.ITEM in (select t.ID from FEEDBACK_OPTION t where t.PID = 2)
							and A .ANSWER is not null
						) tmp left join (
							SELECT bb.SEQUENCE, bb.ANSWER gender
							FROM FEEDBACK_ANSWER bb
							WHERE bb.CLASS_ID = '%s'
							AND bb.`YEAR` = %u
							AND bb.CLASS_ITEM = %u
							and bb.ITEM = 0
						) b
						on tmp.SEQUENCE = b.SEQUENCE
						group by b.GENDER
						order by b.GENDER ", addslashes($classId), intval($year), intval($trem), addslashes($classId), intval($year), intval($trem));
        
        $query = $this->odb->query($sql);
        $result = $query->result_array();
        
        $data = array();
        if(empty($result)) {
			$data['m_agg'] = 0;
			$data['f_agg'] = 0;
		} else {
            $data['f_agg'] = empty($result[0]['agg'])?0:$result[0]['agg'];
			$data['m_agg'] = empty($result[1]['agg'])?0:$result[1]['agg'];
        }

        return $data;
    }

    public function getRateList($queryYear, $searchStartDate, $searchEndDate, $search_class_no, $search_class_name,$rows="", $offset="")
    {
        $sql = "SELECT r.start_date1, r.end_date1, r.class_name, r.class_no, r.term, r.year, r.map1, r.map2, r.map3, r.map4, r.map5, r.map6, r.map7, r.map8,
                            ct_mc.description AS master_cate,
                            sc.name AS sub_cate,
                            bc.name AS bname,
                            fcc.ID question_id,
                            fcc.QID qid,
                            vaa.first_name AS worker_name
                        FROM `REQUIRE` r
                        LEFT JOIN VM_ALL_ACCOUNT vaa
                            ON r.worker=vaa.personal_id
                        LEFT JOIN CODE_TABLE ct_mc
                            ON r.type=item_id AND ct_mc.type_id='23'
                        LEFT JOIN SUB_CATEGORY sc
                            ON r.BEAURAU_ID=sc.cate_id AND r.type=sc.type
                        LEFT JOIN BUREAU_CODE bc
                            ON r.REQ_BEAURAU=bc.bureau_id or r.DEV_TYPE = bc.bureau_id
                        JOIN FEEDBACK_COURSE_COLLOCATION fcc
                            ON r.year=fcc.CLASS_YEAR AND r.term=fcc.CLASS_TERM AND r.class_no=fcc.CLASS_ID
                        WHERE r.class_status IN ('2', '3') ";

        if(''!=$queryYear)
        {
            $sql .= " AND r.year='{$queryYear}' ";
        }
        if(''!=$search_class_no)
        {
            $sql .= " AND UPPER(r.class_no) LIKE UPPER('%{$search_class_no}%')  ";
        }		 
        if(''!=$search_class_name)
        {
            $sql .= " AND UPPER(r.class_name) LIKE UPPER('%{$search_class_name}%')  ";
        }	
        if(''!=$searchStartDate)
        {
            $sql .= " AND r.start_date1 >= '{$searchStartDate}' ";
        }	 
        if(''!=$searchEndDate)
        {
            $sql .= " AND r.start_date1 <= '{$searchEndDate}' ";
        }		 
        $sql .= " ORDER BY r.type, r.dev_type";

        $limit = "";
        if($rows != "" && $offset != "") {
            $limit = " limit " . intVal($rows) . " offset " . intVal($offset);
        } else if($rows != "") {
            $limit = " limit " . intVal($rows);
        }

        $sql = $sql . " " . $limit;
        
        $query = $this->odb->query($sql);

        return $query->result_array();
    }

    public function get_cnt_gender_avg_rate($classId, $year, $trem)
    {
        $sql = sprintf("SELECT avg(case when tmp.ANSWER = 5 then 5
                            when tmp.ANSWER = 4 then 4
                            when tmp.ANSWER = 3 then 3
                            when tmp.ANSWER = 2 then 2
                            when tmp.ANSWER = 1 then 1 end)/1 agg, b.GENDER
						FROM( SELECT A.SEQUENCE, A .ANSWER, A.ITEM
							FROM FEEDBACK_ANSWER A
							WHERE A .CLASS_ID = '%s'
							AND A .`YEAR` = %u
							AND A .CLASS_ITEM = %u
							and A.ITEM in (select t.ID from FEEDBACK_OPTION t where t.PID = 2)
							and A .ANSWER is not null
						) tmp left join (
							SELECT bb.SEQUENCE, bb.ANSWER gender
							FROM FEEDBACK_ANSWER bb
							WHERE bb.CLASS_ID = '%s'
							AND bb.`YEAR` = %u
							AND bb.CLASS_ITEM = %u
							and bb.ITEM = 0
						) b
						on tmp.SEQUENCE = b.SEQUENCE
						group by b.GENDER
						order by b.GENDER ", addslashes($classId), intval($year), intval($trem), addslashes($classId), intval($year), intval($trem));
        
        $query = $this->odb->query($sql);
        $result = $query->result_array();
        
        $data = array();
        if(empty($result)) {
			$data['m_agg'] = 0;
			$data['f_agg'] = 0;
		} else {
            $data['f_agg'] = empty($result[0]['agg'])?0:$result[0]['agg'];
			$data['m_agg'] = empty($result[1]['agg'])?0:$result[1]['agg'];
        }

        return $data;
    }

    public function getTeacherScoreList($teacher_name, $teacher_idno, $course_name, $class_name, $start_date, $end_date, $rows="", $offset="")
    {
        $query_cond_string = '';
        if(!empty($teacher_name)) {
            $query_cond_string .= sprintf("AND t.name= '%s'", addslashes($teacher_name));
        }
        if(!empty($course_name)) {
            $query_cond_string .= sprintf(" and cte.description LIKE '%%%s%%'", addslashes($course_name));
        }
        if(!empty($class_name)){
            $query_cond_string .= sprintf(" and r.class_name LIKE '%%%s%%'", addslashes($class_name));
        }
        if(!empty($teacher_idno)) {
            $query_cond_string .= sprintf(" AND tc.teacher_id LIKE '%s%%'", addslashes($teacher_idno));
        }
        if(!empty($end_date)) {
            $query_cond_string .= sprintf(" AND r.start_date1 >= '%s'", addslashes($start_date));
        }
        if(!empty($end_date)) {
            $query_cond_string .= sprintf(" AND r.end_date1 <= '%s'", addslashes($end_date));
        }

        $sql = sprintf("SELECT
                        tc.course_code,
                        cte.DESCRIPTION course_name,
                        fcc.class_id,
                        r.class_name,
                        fcc.CLASS_YEAR year,
                        fcc.CLASS_TERM term,
                        r.start_date1,
                        t.NAME teacher_name,
                        tc.TEACHER_ID id
                    FROM
                        FEEDBACK_COURSE_COLLOCATION fcc
                        JOIN `REQUIRE` r ON r.YEAR = fcc.CLASS_YEAR 
                        AND r.class_no = fcc.CLASS_ID 
                        AND r.term = fcc.CLASS_TERM
                        JOIN COURSETEACHER tc ON tc.YEAR = fcc.CLASS_YEAR 
                        AND tc.CLASS_NO = fcc.CLASS_ID 
                        AND tc.TERM = fcc.CLASS_TERM
                        JOIN CODE_TABLE cte ON tc.COURSE_CODE = cte.ITEM_ID 
                        AND cte.TYPE_ID = '17'
                        JOIN ( SELECT DISTINCT NAME, ID FROM TEACHER ) t ON t.ID = tc.TEACHER_ID 
                    WHERE
                        fcc.ISREADY = 1 
                        %s
                    GROUP BY
                        fcc.CLASS_ID,
                        fcc.CLASS_YEAR,
                        fcc.CLASS_TERM,
                        t.ID,
                        t.NAME,
                        tc.COURSE_CODE,
                        r.CLASS_NAME,
                        cte.DESCRIPTION,
                        r.start_date1,
                        tc.TEACHER_ID 
                    ORDER BY
                        tc.COURSE_CODE,
                        fcc.CLASS_ID,
                        fcc.CLASS_YEAR,
                        fcc.CLASS_TERM", $query_cond_string);
        
        $limit = "";
        if($rows != "" && $offset != "") {
            $limit = " limit " . intVal($rows) . " offset " . intVal($offset);
        } else if($rows != "") {
            $limit = " limit " . intVal($rows);
        }

        $sql = $sql . " " . $limit;

        $query = $this->odb->query($sql);
        $result = $query->result_array();

        return $result;
    }

    public function get_teacher_cnt_gender_score($class_no,$year,$term,$course_code)
    {
        $sql = sprintf("SELECT  sum(case when B.gender = 1 then tmp.ANSWER else 0 end) as male,
							sum(case when B.gender = 0 then tmp.ANSWER else 0 end) as female,
							sum(case when B.gender = 1 then 1 else 0 end) m_cnt,
							sum(case when B.gender = 0 then 1 else 0 end) f_cnt
						FROM( SELECT A.SEQUENCE, A .ANSWER
							FROM FEEDBACK_ANSWER A
							WHERE A .CLASS_ID = '%s'
							AND A . YEAR = %u
							AND A .CLASS_ITEM = %u
							and A.ITEM in (select t.ID from FEEDBACK_OPTION t where t.PID = 2)
							and A .ANSWER is not null
							and A.class_number = '%s'
						) tmp left join (
							SELECT bb.SEQUENCE, bb.ANSWER gender
							FROM FEEDBACK_ANSWER bb
							WHERE bb.CLASS_ID = '%s'
							AND bb. YEAR = %u
							AND bb.CLASS_ITEM = %u
							and bb.ITEM = 0
						) B
						on tmp.SEQUENCE = B.SEQUENCE", addslashes($class_no), intval($year), intval($term), addslashes($course_code), addslashes($class_no), intval($year), intval($term));

       
        $query = $this->odb->query($sql);
        $result = $query->row_array();

        return $result;
    }

    public function getTeacherRateList($teacher_name, $teacher_idno, $course_name, $class_name, $start_date, $end_date, $rows="", $offset="")
    {
        $query_cond_string = '';
        if(!empty($teacher_name)) {
            $query_cond_string .= sprintf("AND t.name= '%s'", addslashes($teacher_name));
        }
        if(!empty($course_name)) {
            $query_cond_string .= sprintf(" and cte.description LIKE '%%%s%%'", addslashes($course_name));
        }
        if(!empty($class_name)){
            $query_cond_string .= sprintf(" and r.class_name LIKE '%%%s%%'", addslashes($class_name));
        }
        if(!empty($teacher_idno)) {
            $query_cond_string .= sprintf(" AND tc.teacher_id LIKE '%s%%'", addslashes($teacher_idno));
        }
        if(!empty($end_date)) {
            $query_cond_string .= sprintf(" AND r.start_date1 >= '%s'", addslashes($start_date));
        }
        if(!empty($end_date)) {
            $query_cond_string .= sprintf(" AND r.end_date1 <= '%s'", addslashes($end_date));
        }

        $sql = sprintf("SELECT
                        tc.course_code,
                        cte.DESCRIPTION course_name,
                        fcc.class_id,
                        r.class_name,
                        fcc.CLASS_YEAR year,
                        fcc.CLASS_TERM term,
                        r.start_date1,
                        t.NAME teacher_name,
                        tc.TEACHER_ID id
                    FROM
                        FEEDBACK_COURSE_COLLOCATION fcc
                        JOIN `REQUIRE` r ON r.YEAR = fcc.CLASS_YEAR 
                        AND r.class_no = fcc.CLASS_ID 
                        AND r.term = fcc.CLASS_TERM
                        JOIN COURSETEACHER tc ON tc.YEAR = fcc.CLASS_YEAR 
                        AND tc.CLASS_NO = fcc.CLASS_ID 
                        AND tc.TERM = fcc.CLASS_TERM
                        JOIN CODE_TABLE cte ON tc.COURSE_CODE = cte.ITEM_ID 
                        AND cte.TYPE_ID = '17'
                        JOIN ( SELECT DISTINCT NAME, ID FROM TEACHER ) t ON t.ID = tc.TEACHER_ID 
                    WHERE
                        fcc.ISREADY = 1 
                        %s
                    GROUP BY
                        fcc.CLASS_ID,
                        fcc.CLASS_YEAR,
                        fcc.CLASS_TERM,
                        t.ID,
                        t.NAME,
                        tc.COURSE_CODE,
                        r.CLASS_NAME,
                        cte.DESCRIPTION,
                        r.start_date1,
                        tc.TEACHER_ID 
                    ORDER BY
                        tc.COURSE_CODE,
                        fcc.CLASS_ID,
                        fcc.CLASS_YEAR,
                        fcc.CLASS_TERM", $query_cond_string);

        $limit = "";
        if($rows != "" && $offset != "") {
            $limit = " limit " . intVal($rows) . " offset " . intVal($offset);
        } else if($rows != "") {
            $limit = " limit " . intVal($rows);
        }

        $sql = $sql . " " . $limit;

        $query = $this->odb->query($sql);
        $result = $query->result_array();

        return $result;
    }

    public function get_teacher_cnt_gender_rate($class_no,$year,$term,$course_code)
    {
        $sql = sprintf("SELECT  sum(case when B.gender = 1 then case when tmp.ANSWER = 5 then 100 
                                    when tmp.ANSWER = 4 then 80
                                    when tmp.ANSWER = 3 then 60
                                    when tmp.ANSWER = 2 then 40
                                    when tmp.ANSWER = 1 then 20 end else 0 end) as male,
                            sum(case when B.gender = 0 then case when tmp.ANSWER = 5 then 100
                                    when tmp.ANSWER = 4 then 80
                                    when tmp.ANSWER = 3 then 60
                                    when tmp.ANSWER = 2 then 40
                                    when tmp.ANSWER = 1 then 20 end else 0 end) as female,
							sum(case when B.gender = 1 then 1 else 0 end) m_cnt,
							sum(case when B.gender = 0 then 1 else 0 end) f_cnt
						FROM( SELECT A.SEQUENCE, A .ANSWER
							FROM FEEDBACK_ANSWER A
							WHERE A .CLASS_ID = '%s'
							AND A . YEAR = %u
							AND A .CLASS_ITEM = %u
							and A.ITEM in (select t.ID from FEEDBACK_OPTION t where t.PID = 2)
							and A .ANSWER is not null
							and A.class_number = '%s'
						) tmp left join (
							SELECT bb.SEQUENCE, bb.ANSWER gender
							FROM FEEDBACK_ANSWER bb
							WHERE bb.CLASS_ID = '%s'
							AND bb. YEAR = %u
							AND bb.CLASS_ITEM = %u
							and bb.ITEM = 0
						) B
						on tmp.SEQUENCE = B.SEQUENCE", addslashes($class_no), intval($year), intval($term), addslashes($course_code), addslashes($class_no), intval($year), intval($term));

       
        $query = $this->odb->query($sql);
        $result = $query->row_array();

        return $result;
    }

    public function getOldTeacherScoreList($teacher_name, $teacher_idno, $course_name, $class_name, $start_date, $end_date, $rows="", $offset="")
    {
        $query_cond_string = '';
        if(!empty($teacher_name)) {
            $query_cond_string .= sprintf("AND T.name= '%s'", addslashes($teacher_name));
        }
        if(!empty($course_name)) {
            $query_cond_string .= sprintf(" and cc.description LIKE '%%%s%%'", addslashes($course_name));
        }
        if(!empty($class_name)){
            $query_cond_string .= sprintf(" and R.class_name LIKE '%%%s%%'", addslashes($class_name));
        }
        if(!empty($teacher_idno)) {
            $query_cond_string .= sprintf(" AND C.teacher_id LIKE '%s%%'", addslashes($teacher_idno));
        }
        if(!empty($end_date)) {
            $query_cond_string .= sprintf(" AND R.start_date1 >= '%s'", addslashes($start_date));
        }
        if(!empty($end_date)) {
            $query_cond_string .= sprintf(" AND R.end_date1 <= '%s'", addslashes($end_date));
        }

        $sql=sprintf(
            "
            SELECT
                qcr.use_date AS start_date1,
                R.class_name,
                C.class_no,
                C.year,
                C.term,
                cc.DESCRIPTION AS course_name,
                T . NAME AS teacher_name,
                T . id,
                ROUND (qcr.score1, 2) AS e_score1,
                ROUND (qcr.score2, 2) AS e_score2,
                ROUND (qcr.score3, 2) AS e_score3,
                ROUND (
                    (
                        qcr.score1 + qcr.score2 + qcr.score3
                    ) / 3,
                    2
                ) AS avg
            FROM
                COURSETEACHER C
            JOIN `REQUIRE` R ON R. YEAR = C. YEAR
            AND R.class_no = C.class_no
            AND R.term = C.term
            JOIN TEACHER T ON C.TEACHER_ID = T . ID
            JOIN CODE_TABLE cc ON cc.TYPE_ID = '17'
            AND C.course_code = cc.ITEM_ID
            LEFT JOIN (
                SELECT
                    RU.USE_DATE,
                    TE.teacher_id,
                    TE.course_code,
                    TE. YEAR,
                    TE.term,
                    TE.class_no,
                    (AVG(5 - TE.score1) * 25) AS score1,
                    (AVG(5 - TE.score2) * 25) AS score2,
                    (AVG(5 - TE.score3) * 25) AS score3
                FROM
                    T_EVALUATE TE
                JOIN (
                    SELECT
                        TEACHER_ID,
                        YEAR,
                        CLASS_ID,
                        TERM,
                        USE_ID,
                        USE_DATE
                    FROM
                        ROOM_USE TE
                    GROUP BY
                        TEACHER_ID,
                        YEAR,
                        CLASS_ID,
                        TERM,
                        USE_ID,
                        USE_DATE
                ) RU ON TE.TEACHER_ID = RU.TEACHER_ID
                AND TE. YEAR = RU. YEAR
                AND TE.CLASS_NO = RU.CLASS_ID
                AND TE.TERM = RU.TERM
                AND TE.COURSE_CODE = RU.USE_ID
                GROUP BY
                RU.USE_DATE,
                TE.teacher_id,
                TE.course_code,
                TE. YEAR,
                TE.term,
                TE.class_no
            ) qcr ON qcr.teacher_id = C.teacher_id
            AND qcr.course_code = C.course_code
            AND qcr. YEAR = R. YEAR
            AND qcr.term = R.term
            AND qcr.class_no = R.class_no
            WHERE
                1 = 1
            AND C.ISEVALUATE = 'Y'
            %s
            GROUP BY
                qcr.use_date,
                class_name,
                C.CLASS_NO,
                C. YEAR,
                C.TERM,
                cc.DESCRIPTION,
                T . NAME,
                T . ID,
                qcr.score1,
                qcr.score2,
                qcr.score3
            ORDER BY
                C. YEAR,
                qcr.use_date,
                C.CLASS_NO,
                C.TERM
            "	,
                $query_cond_string);
        
        
        $limit = "";
        if($rows != "" && $offset != "") {
            $limit = " limit " . intVal($rows) . " offset " . intVal($offset);
        } else if($rows != "") {
            $limit = " limit " . intVal($rows);
        }

        $sql = $sql . " " . $limit;

        $query = $this->odb->query($sql);
        $result = $query->result_array();

        return $result;
    }

    public function getOlderTeacherScoreList($teacher_name, $teacher_idno, $course_name, $class_name, $start_date, $end_date, $rows="", $offset="")
    {
        $query_cond_string = '';
        if(!empty($teacher_name)) {
            $query_cond_string .= sprintf("AND T.name= '%s'", addslashes($teacher_name));
        }
        if(!empty($course_name)) {
            $query_cond_string .= sprintf(" and cc.description LIKE '%%%s%%'", addslashes($course_name));
        }
        if(!empty($class_name)){
            $query_cond_string .= sprintf(" and r.class_name LIKE '%%%s%%'", addslashes($class_name));
        }
        if(!empty($teacher_idno)) {
            $query_cond_string .= sprintf(" AND c.teacher_id LIKE '%s%%'", addslashes($teacher_idno));
        }
        if(!empty($end_date)) {
            $query_cond_string .= sprintf(" AND r.start_date1 >= '%s'", addslashes($start_date));
        }
        if(!empty($end_date)) {
            $query_cond_string .= sprintf(" AND r.end_date1 <= '%s'", addslashes($end_date));
        }

        $sql = sprintf("
                        SELECT DISTINCT
                            start_date1,
                            class_name,
                            c.class_no,
                            c.year,
                            c.term,
                            cc.DESCRIPTION AS course_name,
                            T.NAME AS teacher_name,
                            T.id,
                            qcr.question_id,
                            ROUND ( qcr.score1, 2 ) AS e_score1,
                            ROUND ( qcr1.score2, 2 ) AS e_score2,
                            ROUND ( qcr2.score3, 2 ) AS e_score3,
                            ROUND ( ( qcr.score1 + qcr1.score2 + qcr2.score3 ) / 3, 2 ) AS avg 
                        FROM
                            COURSETEACHER c
                            JOIN `REQUIRE` r ON r.YEAR = c.YEAR 
                            AND r.class_no = c.class_no 
                            AND r.term = c.term
                            JOIN ROOM_USE ru ON ru.YEAR = c.YEAR 
                            AND ru.class_id = c.class_no 
                            AND ru.term = c.term 
                            AND c.use_date = ru.use_date 
                            AND ru.use_id = c.course_code
                            JOIN TEACHER T ON c.TEACHER_ID = T.ID 
                            AND T.TEACHER = ru.isteacher 
                            AND ru.teacher_id = T.ID
                            JOIN CODE_TABLE cc ON cc.TYPE_ID = '17' 
                            AND c.course_code = cc.ITEM_ID
                            LEFT JOIN (
                            SELECT
                                teacher_id,
                                course_code,
                                qm.YEAR,
                                qm.term,
                                qm.class_no,
                                qm.question_id,
                                SUM(
                                    25 *(
                                        5 - YOU_ANS 
                                    )) / COUNT(*) AS score1 
                            FROM
                                QUESTION_COURSE_RESULT qcr
                                LEFT JOIN QUESTION_MANAGEMENT qm ON qcr.question_id = qm.question_id 
                            WHERE
                                qcr.YOU_ANS BETWEEN 1 
                                AND 4 
                            GROUP BY
                                teacher_id,
                                course_code,
                                qm.YEAR,
                                qm.term,
                                qm.class_no 
                            ) qcr ON qcr.teacher_id = c.teacher_id 
                            AND qcr.course_code = c.course_code 
                            AND qcr.YEAR = r.YEAR 
                            AND qcr.term = r.term 
                            AND qcr.class_no = r.class_no
                            LEFT JOIN (
                            SELECT
                                teacher_id,
                                course_code,
                                qm.YEAR,
                                qm.term,
                                qm.class_no,
                                SUM(
                                    25 *(
                                        5 - CONTENT_ANS 
                                    )) / COUNT(*) AS score2 
                            FROM
                                QUESTION_COURSE_RESULT qcr1
                                LEFT JOIN QUESTION_MANAGEMENT qm ON qcr1.question_id = qm.question_id 
                            WHERE
                                qcr1.CONTENT_ANS BETWEEN 1 
                                AND 4 
                            GROUP BY
                                teacher_id,
                                course_code,
                                qm.YEAR,
                                qm.term,
                                qm.class_no 
                            ) qcr1 ON qcr1.teacher_id = c.teacher_id 
                            AND qcr1.course_code = c.course_code 
                            AND qcr1.YEAR = r.YEAR 
                            AND qcr1.term = r.term 
                            AND qcr1.class_no = r.class_no
                            LEFT JOIN (
                            SELECT
                                teacher_id,
                                course_code,
                                qm.YEAR,
                                qm.term,
                                qm.class_no,
                                SUM(
                                    25 *(
                                        5 - TEACH_ANS 
                                    )) / COUNT(*) AS score3 
                            FROM
                                QUESTION_COURSE_RESULT qcr2
                                LEFT JOIN QUESTION_MANAGEMENT qm ON qcr2.question_id = qm.question_id 
                            WHERE
                                qcr2.TEACH_ANS BETWEEN 1 
                                AND 4 
                            GROUP BY
                                teacher_id,
                                course_code,
                                qm.YEAR,
                                qm.term,
                                qm.class_no 
                            ) qcr2 ON qcr2.teacher_id = c.teacher_id 
                            AND qcr2.course_code = c.course_code 
                            AND qcr2.YEAR = r.YEAR 
                            AND qcr2.term = r.term 
                            AND qcr2.class_no = r.class_no 
                        WHERE
                            1 = 1 
                            AND c.ISEVALUATE = 'Y' 
                            %s
                        ORDER BY
                            T.ID,
                            c.YEAR,
                            c.term,
                            c.CLASS_NO,
                            start_date1
                            ",
                            $query_cond_string
                    );
        
        $limit = "";
        if($rows != "" && $offset != "") {
            $limit = " limit " . intVal($rows) . " offset " . intVal($offset);
        } else if($rows != "") {
            $limit = " limit " . intVal($rows);
        }

        $sql = $sql . " " . $limit;

        $query = $this->odb->query($sql);
        $result = $query->result_array();

        return $result;
    }

    public function getScore($classId, $year, $term){
        $sql = sprintf("SELECT
                            answer 
                        FROM
                            view_17L 
                        WHERE
                            year = '%s' 
                            AND class_id = '%s' 
                            AND class_item = '%s'", intval($year), addslashes($classId), intval($term));
        
        $query = $this->odb->query($sql);
        $result = $query->result_array();

        if(!empty($result)){
            return $result[0]['answer'];
        } else {
            return 0;
        }
    }

    public function getRate($classId, $year, $term){
        $sql = sprintf("SELECT
                            answer 
                        FROM
                            view_17L_rate 
                        WHERE
                            year = '%s' 
                            AND class_id = '%s' 
                            AND class_item = '%s'", intval($year), addslashes($classId), intval($term));
        
        $query = $this->odb->query($sql);
        $result = $query->result_array();

        if(!empty($result)){
            return $result[0]['answer'];
        } else {
            return 0;
        }
    }

    public function get_teacher_score($class_id,$year,$term,$course_code,$id){
        $sql = sprintf("SELECT
                            v.answer 
                        FROM
                            view_17J v 
                        WHERE
                            v.CLASS_ID = '%s' 
                        AND v.YEAR = '%s'
                        AND v.CLASS_ITEM = '%s' 
                        AND v.CLASS_NUMBER = '%s' 
                        AND v.TEACHER_ID = '%s'
                        ORDER BY v.item", addslashes($class_id), intval($year), intval($term), addslashes($course_code), addslashes($id));

        $query = $this->odb->query($sql);
        $result = $query->result_array();

        return $result;
    }

    public function get_teacher_rate($class_id,$year,$term,$course_code,$id){
        $sql = sprintf("SELECT
                            v.answer 
                        FROM
                            view_17J_rate v 
                        WHERE
                            v.CLASS_ID = '%s' 
                        AND v.YEAR = '%s'
                        AND v.CLASS_ITEM = '%s' 
                        AND v.CLASS_NUMBER = '%s' 
                        AND v.TEACHER_ID = '%s'
                        ORDER BY v.item", addslashes($class_id), intval($year), intval($term), addslashes($course_code), addslashes($id));

        $query = $this->odb->query($sql);
        $result = $query->result_array();

        return $result;
    }

    public function getContent($id)
    {
        $sql = sprintf("SELECT
                        CASE
                            WHEN
                                aid = 2 THEN
                                    1 
                                    WHEN aid = 3 THEN
                                    2 ELSE aid 
                                END aid,
                            bid,
                            cid,
                            `name`,
                            name2,
                            name3 
                        FROM
                            (
                            SELECT
                                nvl ( aid, 999999 ) aid,
                                bid,
                                cid,
                                y.NAME,
                                y.NAME2,
                                y.NAME3 
                            FROM
                                FEEDBACK_ITEM x
                                JOIN (
                                SELECT
                                    a.ID aid,
                                    b.ID bid,
                                    c.ID cid,
                                    a.NAME as `name`,
                                    b.NAME AS name2,
                                    c.NAME AS name3 
                                FROM
                                    FEEDBACK_PARENT a
                                    JOIN FEEDBACK_SUB b ON a.ID = b.PARENT
                                    RIGHT OUTER JOIN FEEDBACK_OPTION c ON b.ID = c.SID 
                                ) y ON x.OID = y.cid 
                            WHERE
                                x.QID = '%s' 
                            ORDER BY
                                aid,
                            bid,
                            cid) z", intval($id));

        $query = $this->odb->query($sql);
        $result = $query->result_array();

        return $result;
    }

    public function getContent_17I_excel($c_no, $year, $term, $where)
    {
        if($where!="") {
    		$where = " and f.ITEM in ($where) ";
    	}
    	$sql = sprintf("select f.ANSWER, f.ITEM, count(1) cnt
                from FEEDBACK_ANSWER f
                where f.CLASS_ID = '%s'
                and f.CLASS_ITEM = '%s'
                and f.YEAR = '%s'
                AND f.ANSWER is not null
                %s
                group by f.ANSWER, f.ITEM", addslashes($c_no), intval($term), intval($year), $where);

        $query = $this->odb->query($sql);
        $result = $query->result_array();

        return $result;
    }

    public function searchSpecialDetailGd($questionId)
    {
        $sql = sprintf(
            "SELECT op.DETAIL_ID,op.ITEM_ID,im.TITLE AS ITEM_TITLE,op.ANSWER,op.ANSWER_TIMES
             FROM OPEN_DETAIL op
             LEFT JOIN ITEM_MANAGEMENT im ON im.ITEM_ID = op.ITEM_ID
             WHERE op.QUESTION_ID = %u
             ORDER BY op.ITEM_ID, op.DETAIL_ID",
            intval($questionId)
        );

        $query = $this->odb->query($sql);
        $result = $query->result_array();
        
        return $result;
    }

    public function getQuestionData($year, $class_no, $term)
    {
        $sql = sprintf("SELECT
                            * 
                        FROM
                            QUESTION_DATA 
                        WHERE
                            QD_YEAR = '%s' 
                            AND QD_CLASS_NO = '%s' 
                            AND QD_TERM = '%s'", addslashes($year), addslashes($class_no), addslashes($term));

        $query = $this->odb->query($sql);
        $result = $query->result_array();

        return $result;
    }

    public function getDefaultQuestionData($id)
    {
        $sql = sprintf("SELECT
                            id,
                            question 
                        FROM
                            DEFAULT_QUESTION_DATA 
                        WHERE
                            kind != 'basic' 
                            AND ( ans_num = '1' AND ans_text1 = '1' ) 
                            AND father_id = '%s' 
                        ORDER BY
                            q_order", addslashes($id));

        $query = $this->odb->query($sql);
        $result = $query->result_array();

        return $result;
    }

    public function getOpenQuestion($year, $class_no, $term, $question)
    {
        $sql = sprintf("SELECT
                            * 
                        FROM
                            OPEN_QUESTION 
                        WHERE
                            od_year = '%s' 
                            AND od_class_no = '%s' 
                            AND od_term = '%s' 
                            AND od_question = '%s' 
                            AND od_count > 0", addslashes($year), addslashes($class_no), addslashes($term), addslashes($question));
        
        $query = $this->odb->query($sql);
        $result = $query->result_array();

        return $result;
    }

    public function getOnlineAppCount($year, $class_no, $term)
    {
        $sql = sprintf("SELECT
                            count(*) AS cnt 
                        FROM
                            ONLINE_APP 
                        WHERE
                            ( yn_sel = '3' OR yn_sel = '1' ) 
                            AND year = '%s' 
                            AND class_no = '%s' 
                            AND term = '%s'",addslashes($year), addslashes($class_no), addslashes($term));

        $query = $this->odb->query($sql);
        $result = $query->result_array();

        return $result[0]['cnt'];
    }

    public function getTEvaluateCount($year, $class_no, $term)
    {
        $sql = sprintf("SELECT DISTINCT
                            autokey 
                        FROM
                        T_EVALUATE 
                        WHERE
                            year = '%s' 
                            AND class_no = '%s' 
                            AND term = '%s'",addslashes($year), addslashes($class_no), addslashes($term));

        $query = $this->odb->query($sql);
        $result = $query->result_array();
        
        return count($result);
    }

    public function getDefaultQuestionDataForRestaurant($id)
    {
        $sql = sprintf("SELECT
                            id,
                            question 
                        FROM
                            DEFAULT_QUESTION_DATA 
                        WHERE
                            kind = 'basic' 
                            AND lay = '3'
                            AND father_id = '%s' 
                        ORDER BY
                            question", addslashes($id));

        $query = $this->odb->query($sql);
        $result = $query->result_array();

        return $result;
    }

    public function getAnswerData($qd_id, $id, $type){
        if($type == 1){
            $sql = sprintf("SELECT
                            count( ad_def_id ) AS cnt 
                        FROM
                            ANSWER_DATA 
                        WHERE
                            ad_qd_id = '%s' 
                            AND ad_def_id = '%s' 
                            AND (
                            ad_answer >= '1' 
                            AND ad_answer <= '4')", addslashes($qd_id), addslashes($id));
        } else if($type == 2){
            $sql = sprintf("SELECT
                            count( ad_def_id ) AS cnt 
                        FROM
                            ANSWER_DATA 
                        WHERE
                            ad_qd_id = '%s' 
                            AND ad_def_id = '%s' 
                            AND (
                            ad_answer = '1' 
                            or ad_answer = '2')", addslashes($qd_id), addslashes($id));
        } else if($type == 3){
            $sql = sprintf("SELECT
                            count( ad_def_id ) AS cnt 
                        FROM
                            ANSWER_DATA 
                        WHERE
                            ad_qd_id = '%s' 
                            AND ad_def_id = '%s' 
                            AND (
                            ad_answer = '3' 
                            or ad_answer = '4')", addslashes($qd_id), addslashes($id));
        }
        
        $query = $this->odb->query($sql);
        $result = $query->result_array();

        return $result[0]['cnt'];
    }

    public function getOpenQuestionDetail($year, $class_no, $term)
    {
        $sql = sprintf("SELECT
                            Q.qd_class_name,
                            Q.qd_sdate,
                            Q.qd_edate,
                            Q.qd_room_name,
                            O.od_content,
                            O.od_count 
                        FROM
                            QUESTION_DATA Q
                            LEFT JOIN OPEN_QUESTION O ON 
                            O.od_year = Q.qd_year 
                            AND O.od_class_no = Q.qd_class_no 
                            AND O.od_term = Q.qd_term 
                        WHERE
                            O.od_kind = 'customer' 
                            AND O.od_question LIKE '%%其他開放性意見%%'
                            and Q.QD_YEAR = '%s'
                            and Q.QD_CLASS_NO = '%s'
                            and Q.QD_TERM = '%s'
                        ORDER BY
                            Q.qd_class_no,
                            Q.qd_sdate",addslashes($year), addslashes($class_no), addslashes($term));
       
        $query = $this->odb->query($sql);
        $result = $query->result_array();

        return $result;
    } 
}
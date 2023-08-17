<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

require_once APPPATH . "models/Common_model.php";
class Eat_money_count_model extends Common_model
{
    public function made($d1, $d2)
    {
        $sql = "delete from dining_student_his  where use_date between ".$this->db->escape(addslashes($d1))." and ".$this->db->escape(addslashes($d2))."";

        $query = $this->db->query($sql);

        $sql = "insert into dining_student_his select  A.*, now() as insert_date from dining_student A where A.use_date between ".$this->db->escape(addslashes($d1))." and ".$this->db->escape(addslashes($d2))."";

        $query = $this->db->query($sql);

        return [true, 1];

    }

    public function checked($d1, $d2, $account)
    {
        $d1 = $this->db->escape(addslashes($d1));
        $d2 = $this->db->escape(addslashes($d2));
        
        $where = "A.use_date between ".$this->db->escape(addslashes($d1))." and ".$this->db->escape(addslashes($d2))."";
        $this->update_dining_student($where, $account);

        $reSql1 = $this->sql1($where);

        $MSG = "";
        for ($i = 0; $i < sizeof($reSql1); $i++) {
            $c1 = abs($reSql1[$i]["AP1"] - $reSql1[$i]["BP1"]);
            $c2 = abs($reSql1[$i]["AP2"] - $reSql1[$i]["BP2"]);
            $c3 = abs($reSql1[$i]["AP3"] - $reSql1[$i]["BP3"]);
            $d1 = abs($reSql1[$i]["AP1"] * 0.1);
            $d2 = abs($reSql1[$i]["AP2"] * 0.1);
            $d3 = abs($reSql1[$i]["AP3"] * 0.1);
            if ($c1 > $d1) {
                $MSG .= $reSql1[$i]["USE_DATE"] . " 早餐 預估{$reSql1[$i]["BP1"]}人 實際{$reSql1[$i]["AP1"]}人\\n";
            }
            if ($c2 > $d2) {
                $MSG .= $reSql1[$i]["USE_DATE"] . " 午餐 預估{$reSql1[$i]["BP2"]}人 實際{$reSql1[$i]["AP2"]}人\\n";
            }
            if ($c3 > $d3) {
                $MSG .= $reSql1[$i]["USE_DATE"] . " 晚餐 預估{$reSql1[$i]["BP3"]}人 實際{$reSql1[$i]["AP3"]}人\\n";
            }
        }

        if ($MSG != "") {

            echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
            echo "<script>";
            echo "var msg = '{$MSG}';";
            echo "alert(msg);";
            echo "</script>";
        } else {
            echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
            echo "<script>";
            echo "alert('檢核完成');";
            echo "</script>";
        }
        return [true, 2];
    }

    public function print1($d1, $d2)
    {
        $where = "between ".$this->db->escape(addslashes($d1))." and ".$this->db->escape(addslashes($d2))." ";
        $result = array();
        $cweeklist = array("星期日", "星期一", "星期二", "星期三", "星期四", "星期五", "星期六");
        $result['sql1'] = $this->printSql1($where);
        $result['sql2'] = $this->printSql2($where);
        // $result['sql3'] = $this->printSql2($where);
        $result['sql3'] = $this->printSql3($where, $d1, $cweeklist);
        $result['total'] = $this->printSql5($where);
        return $result;
    }

    public function print4($d1, $d2)
    {            
        $where = "between ".$this->db->escape(addslashes($d1))." and ".$this->db->escape(addslashes($d2))." ";
        $result = array();
        $cweeklist = array("星期日", "星期一", "星期二", "星期三", "星期四", "星期五", "星期六");
        $result['sql1'] = $this->print4Sql1($where);
        $result['sql2'] = $this->print4Sql2($where);
        // $result['sql3'] = $this->printSql2($where);
        $result['sql3'] = $this->print4Sql3($where, $d1, $cweeklist);
        $result['total'] = $this->printSql5($where);
        return $result;

    }

    public function sql1($where)
    {
        $sql = "SELECT * FROM (
        select nvl(sum(A.persons_1),0) as BP1,nvl(sum(A.persons_2),0) as BP2 ,nvl(sum(A.persons_3),0) as BP3,A.use_date from dining_student_his A where {$where}
        group by A.Use_Date order by A.use_date
        ) aa
        LEFT JOIN (
        select nvl(sum(A.persons_1),0) as AP1,nvl(sum(A.persons_2),0) as AP2 ,nvl(sum(A.persons_3),0) as AP3,A.use_date as use_date1 from dining_student A where {$where}
        group by A.Use_Date order by A.use_date ) bb ON aa.use_date = bb.use_date1";

        $query = $this->db->query($sql);

        return $this->QueryToArray($query);
    }

    public function update_dining_student($where, $account)
    {
        $user = $account;

        $sql = "select id,year,class_no,term from dining_student A WHERE {$where}  ORDER BY A.USE_DATE DESC, A.YEAR DESC, A.CLASS_NO, A.TERM ";
        $query = $this->db->query($sql);

        $reData1 = $this->QueryToArray($query);
        for ($i = 0; $i < sizeof($reData1); $i++) {
            $uSql1 = $this->update_sql1($reData1[$i]["year"], $reData1[$i]["class_no"], $reData1[$i]["term"]);
            $sql = "update dining_student set PERSONS_1 = nvl2(PERSONS_1,{$uSql1[0]['count']},null),PERSONS_2 = nvl2(PERSONS_2,{$uSql1[0]['count']},null),PERSONS_3 = nvl2(PERSONS_3,{$uSql1[0]['count']},null), UPD_USER=".$this->db->escape(addslashes($user)).", UPD_DATE=now()
            where id = '{$reData1[$i]['id']}'";
            $query = $this->db->query($sql);

            $sql = "update dining_student
          INNER JOIN

          (

          SELECT CASE WHEN NEWAMT1 = 0 THEN NULL ELSE NEWAMT1 END AS NEWAMT1, CASE WHEN NEWAMT2 = 0 THEN NULL ELSE NEWAMT2 END AS NEWAMT2,
          CASE WHEN NEWAMT3 = 0 THEN NULL ELSE NEWAMT3 END AS NEWAMT3, (NEWAMT1 + NEWAMT2 + NEWAMT3) AS NEWTOTAMT, ".$this->db->escape(addslashes($user))." as user, now() as nowdate
          FROM (
                   SELECT CASE WHEN UNIT1 = '1' THEN NEWP1 * AMT1 ELSE (NEWP1/10) * AMT1 END AS NEWAMT1,
               CASE WHEN UNIT2 = '1' THEN NEWP2 * AMT2 ELSE (NEWP2/10) * AMT2 END AS NEWAMT2,
                   CASE WHEN UNIT3 = '1' THEN NEWP3 * AMT3 ELSE (NEWP3/10) * AMT3 END AS NEWAMT3
                   FROM (
                          SELECT NVL(A.PERSONS_1,0) + NVL(ADD_PERSONS_1,0) + NVL(E1.CNT,0) AS NEWP1,
                          NVL(A.PERSONS_2,0) + NVL(ADD_PERSONS_2,0) + NVL(E2.CNT,0) AS NEWP2,
                          NVL(A.PERSONS_3,0) + NVL(ADD_PERSONS_3,0) + NVL(E3.CNT,0) AS NEWP3, NVL(D.BREAKFAST_TYPE,'1') AS UNIT1,
                          NVL(D.BREAKFAST_MONEY,C1.ADD_VAL1) AS AMT1, NVL(D.LUNCH_TYPE,'1') AS UNIT2, NVL(D.LUNCH_MONEY,C2.ADD_VAL1) AS AMT2,
                          NVL(D.DINNER_TYPE,'1') AS UNIT3, NVL(D.DINNER_MONEY,C3.ADD_VAL1) AS AMT3
                          FROM dining_student A LEFT JOIN code_table C1 ON C1.TYPE_ID = '25' AND C1.ITEM_ID = 'A'
                          LEFT JOIN code_table C2 ON C2.TYPE_ID = '25' AND C2.ITEM_ID = 'B'
                          LEFT JOIN code_table C3 ON C3.TYPE_ID = '25' AND C3.ITEM_ID = 'C'
                          LEFT JOIN dining D ON A.YEAR = D.YEAR AND A.CLASS_NO = D.CLASS_NO AND A.TERM = D.TERM
                          LEFT JOIN (SELECT YEAR, CLASS_NO, TERM, USE_DATE, COUNT(*) AS CNT FROM dining_teacher WHERE DINING_TYPE = 'A'
                                    GROUP BY YEAR, CLASS_NO, TERM, USE_DATE) E1
                  ON A.YEAR = E1.YEAR AND A.CLASS_NO = E1.CLASS_NO AND A.TERM = E1.TERM AND A.USE_DATE = E1.USE_DATE
                          LEFT JOIN (SELECT YEAR, CLASS_NO, TERM, USE_DATE, COUNT(*) AS CNT FROM dining_teacher WHERE DINING_TYPE = 'B'
                          GROUP BY YEAR, CLASS_NO, TERM, USE_DATE)
                          E2 ON A.YEAR = E2.YEAR AND A.CLASS_NO = E2.CLASS_NO AND A.TERM = E2.TERM AND A.USE_DATE = E2.USE_DATE
                          LEFT JOIN (SELECT YEAR, CLASS_NO, TERM, USE_DATE, COUNT(*) AS CNT FROM dining_teacher WHERE DINING_TYPE = 'C'
                          GROUP BY YEAR, CLASS_NO, TERM, USE_DATE)
                          E3 ON A.YEAR = E3.YEAR AND A.CLASS_NO = E3.CLASS_NO AND A.TERM = E3.TERM AND A.USE_DATE = E3.USE_DATE WHERE A.id = ".$this->db->escape(addslashes($reData1[$i]['id']))." ) AS ZZ ) AS YY ) AS XX


          set
          amount_1=XX.NEWAMT1, amount_2=XX.NEWAMT2, amount_3=XX.NEWAMT3, total_amount=XX.NEWTOTAMT, upd_user=XX.user, upd_date=XX.nowdate

          WHERE id =  ".$this->db->escape(addslashes($reData1[$i]['id']))."		";

            // $sql="
            // update dining_student set (amount_1, amount_2, amount_3, total_amount, upd_user, upd_date) =
            // (
            //   SELECT
            //   CASE WHEN NEWAMT1 = 0 THEN NULL ELSE NEWAMT1 END AS NEWAMT1,
            //   CASE WHEN NEWAMT2 = 0 THEN NULL ELSE NEWAMT2 END AS NEWAMT2,
            //   CASE WHEN NEWAMT3 = 0 THEN NULL ELSE NEWAMT3 END AS NEWAMT3,
            //   (NEWAMT1 + NEWAMT2 + NEWAMT3) AS NEWTOTAMT, '{$user}', now()
            //   FROM
            //   (
            //     SELECT
            //     CASE WHEN UNIT1 = '1' THEN NEWP1 * AMT1 ELSE (NEWP1/10) * AMT1 END AS NEWAMT1,
            //     CASE WHEN UNIT2 = '1' THEN NEWP2 * AMT2 ELSE (NEWP2/10) * AMT2 END AS NEWAMT2,
            //     CASE WHEN UNIT3 = '1' THEN NEWP3 * AMT3 ELSE (NEWP3/10) * AMT3 END AS NEWAMT3
            //     FROM
            //     (
            //       SELECT
            //       NVL(A.PERSONS_1,0) + NVL(ADD_PERSONS_1,0) + NVL(E1.CNT,0) AS NEWP1,
            //       NVL(A.PERSONS_2,0) + NVL(ADD_PERSONS_2,0) + NVL(E2.CNT,0) AS NEWP2,
            //       NVL(A.PERSONS_3,0) + NVL(ADD_PERSONS_3,0) + NVL(E3.CNT,0) AS NEWP3,
            //       NVL(D.BREAKFAST_TYPE,'1') AS UNIT1, NVL(D.BREAKFAST_MONEY,C1.ADD_VAL1) AS AMT1,
            //       NVL(D.LUNCH_TYPE,'1')     AS UNIT2, NVL(D.LUNCH_MONEY,C2.ADD_VAL1)     AS AMT2,
            //       NVL(D.DINNER_TYPE,'1')    AS UNIT3, NVL(D.DINNER_MONEY,C3.ADD_VAL1)    AS AMT3
            //       FROM DINING_STUDENT A
            //       LEFT JOIN CODE_TABLE C1 ON C1.TYPE_ID = '25' AND C1.ITEM_ID = 'A'
            //       LEFT JOIN CODE_TABLE C2 ON C2.TYPE_ID = '25' AND C2.ITEM_ID = 'B'
            //       LEFT JOIN CODE_TABLE C3 ON C3.TYPE_ID = '25' AND C3.ITEM_ID = 'C'
            //       LEFT JOIN DINING D ON A.YEAR = D.YEAR AND A.CLASS_NO = D.CLASS_NO AND A.TERM = D.TERM
            //       LEFT JOIN (SELECT YEAR, CLASS_NO, TERM, USE_DATE, COUNT(*) AS CNT FROM DINING_TEACHER WHERE DINING_TYPE = 'A' GROUP BY YEAR, CLASS_NO, TERM, USE_DATE) E1 ON A.YEAR = E1.YEAR AND A.CLASS_NO = E1.CLASS_NO AND A.TERM = E1.TERM AND A.USE_DATE = E1.USE_DATE
            //       LEFT JOIN (SELECT YEAR, CLASS_NO, TERM, USE_DATE, COUNT(*) AS CNT FROM DINING_TEACHER WHERE DINING_TYPE = 'B' GROUP BY YEAR, CLASS_NO, TERM, USE_DATE) E2 ON A.YEAR = E2.YEAR AND A.CLASS_NO = E2.CLASS_NO AND A.TERM = E2.TERM AND A.USE_DATE = E2.USE_DATE
            //       LEFT JOIN (SELECT YEAR, CLASS_NO, TERM, USE_DATE, COUNT(*) AS CNT FROM DINING_TEACHER WHERE DINING_TYPE = 'C' GROUP BY YEAR, CLASS_NO, TERM, USE_DATE) E3 ON A.YEAR = E3.YEAR AND A.CLASS_NO = E3.CLASS_NO AND A.TERM = E3.TERM AND A.USE_DATE = E3.USE_DATE
            //       WHERE A.id = '{$reData1[$i]['id']}'
            //     )
            //   )
            // )
            // WHERE id = '{$reData1[$i]['id']}'        ";

            $query = $this->db->query($sql);
        }
    }

    public function update_sql1($year, $classno, $term)
    {
        $sql = "select count(*) as count from online_app where year='{$year}' and class_no='{$classno}' and term='{$term}' and yn_sel in ('8','3','1') ";
        $query = $this->db->query($sql);

        return $this->QueryToArray($query);
    }

    public function printSql1($where)
    {
        $sql = "select a.*, b.no_persons, b.ROOM_CODE, nvl(c.NAME,a.worker) as worker_name " .
            "from ( " .
            " select distinct year, class_no, term, class_name, worker from dining_student " .
            " where use_date {$where} " .
            ") a " .
            "left join `require` b on a.year = b.year and a.class_no = b.class_no and a.term = b.term " .
            "left join view_all_account c on a.worker = c.personal_id " .
            "where b.type not in ('O') " .
            "order by a.year, a.class_no, a.term";
        $query = $this->db->query($sql);

        return $this->QueryToArray($query);

    }

    public function printSql2($where)
    {

        $sql = "select b.name as worker_name, a.* " .
            "from ( " .
            " select * from appinfo where appi_id in (select distinct appi_id from room_use where use_period in ('11','12','13','14') and appi_id is not null and use_date {$where} ) " .
            ") a " .
            "left join view_all_account b on a.cre_user = b.personal_id " .
            "order by a.appi_id";

        $query = $this->db->query($sql);

        return $this->QueryToArray($query);

    }

    public function printSql3($where, $d1, $cweeks)
    {
        $sql = "select A.*, date_format(data_dt,'%m-%d') as dt_nm, dayofweek(data_dt) - 1 as week_nm from
      ( select date('$d1') as data_dt from dual
      union select date_add('$d1',interval 1 day)  from dual
      union select date_add('$d1',interval 2 day)  from dual
      union select date_add('$d1',interval 3 day)  from dual
      union select date_add('$d1',interval 4 day)  from dual
      union select date_add('$d1',interval 5 day)  from dual
      union select date_add('$d1',interval 6 day)  from dual
      ) A order by A.data_dt";
        $query = $this->db->query($sql);

        $datas = $this->QueryToArray($query);

        for ($i = 0; $i < sizeof($datas); $i++) {
            $datas[$i]["sub"] = $this->printSql4($where, $datas[$i]['data_dt']);
            $datas[$i]["cWeek"] = $cweeks[$datas[$i]['week_nm']];
        }

        return $datas;
    }

    public function printSql4($where, $query_dt)
    {

        $sql = "select a.*, b.no_persons, b.room_code
      ,a1.m_cnt, a1.l_cnt, a1.d_cnt
      ,replace(c1.m_name,',','<br>') as m_name, c1.m_teach_cnt
      ,replace(c2.l_name,',','<br>') as l_name, c2.l_teach_cnt
      ,replace(c3.d_name,',','<br>') as d_name, c3.d_teach_cnt
      from
      (
       select distinct year, class_no, term, class_name, worker from dining_student
       where use_date {$where}
      ) a
      left join
      (
       select year, class_no, term, class_name
       ,(nvl(persons_1,0) + nvl(add_persons_1,0)) as m_cnt
       ,(nvl(persons_2,0) + nvl(add_persons_2,0)) as l_cnt
       ,(nvl(persons_3,0) + nvl(add_persons_3,0)) as d_cnt
       from dining_student
       where use_date = date('{$query_dt}')
      ) a1 on a.year = a1.year and a.class_no = a1.class_no and a.term = a1.term
      left join `require` b on a.year = b.year and a.class_no = b.class_no and a.term = b.term
      left join
      (
       select year, class_no, term, use_date,name as m_NAME, count(*) as m_teach_cnt
       from dining_teacher
       where use_date = date('{$query_dt}') and dining_type = 'A' and type != '2'
       group by year, class_no, term, use_date
      ) c1 on a.year = c1.year and a.class_no = c1.class_no and a.term = c1.term
      left join
      (
       select year, class_no, term, use_date, name as l_NAME, count(*) as l_teach_cnt
       from dining_teacher
       where use_date = date('{$query_dt}') and dining_type = 'B' and type != '2'
       group by year, class_no, term, use_date
      ) c2 on a.year = c2.year and a.class_no = c2.class_no and a.term = c2.term
      left join
      (
       select year, class_no, term, use_date, name as d_NAME, count(*) as d_teach_cnt
       from dining_teacher
       where use_date = date('{$query_dt}') and dining_type = 'C' and type != '2'
       group by year, class_no, term, use_date
      ) c3 on a.year = c3.year and a.class_no = c3.class_no and a.term = c3.term
      where b.type not in ('O')
      order by a.year, a.class_no, a.term";

        $query = $this->db->query($sql);

        return $this->QueryToArray($query);

    }

    public function printSql5($where)
    {
        $sql = "select nvl(sum(tot),0) as totalmon from ( " .
            "select nvl(amount_1,0) + nvl(amount_2,0) + nvl(amount_3,0) as tot from dining_student_his  " .
            "where use_date " . $where . ") as zz";
        $query = $this->db->query($sql);

        return $this->QueryToArray($query);
    }

    public function print4Sql1($where)
    {
        $sql = "select a.*, b.no_persons, b.ROOM_CODE, nvl(c.NAME,a.worker) as worker_name " .
            "from ( " .
            " select distinct year, class_no, term, class_name, worker from dining_student_his " .
            " where use_date {$where} " .
            ") a " .
            "left join `require` b on a.year = b.year and a.class_no = b.class_no and a.term = b.term " .
            "left join view_all_account c on a.worker = c.personal_id " .
            "order by a.year, a.class_no, a.term";

        $query = $this->db->query($sql);

        return $this->QueryToArray($query);
    }

    public function print4Sql2($where)
    {
        $sql = "select b.name as worker_name, a.* " .
            "from ( " .
            " select * from appinfo where appi_id in (select distinct appi_id from room_use where use_period in ('11','12','13','14') and appi_id is not null and use_date {$where} ) " .
            ") a " .
            "left join view_all_account b on a.cre_user = b.username " .
            "order by a.appi_id";

        $query = $this->db->query($sql);

        return $this->QueryToArray($query);
    }

    public function print4Sql3($where, $d1, $cweeks)
    {
        $sql = "select A.*, date_format(data_dt,'%m-%d') as dt_nm, dayofweek(data_dt) - 1 as week_nm from
      ( select date(".$this->db->escape(addslashes($d1))." as data_dt from dual
      union select date_add(".$this->db->escape(addslashes($d1)).",interval 1 day)  from dual
      union select date_add(".$this->db->escape(addslashes($d1)).",interval 2 day)  from dual
      union select date_add(".$this->db->escape(addslashes($d1)).",interval 3 day)  from dual
      union select date_add(".$this->db->escape(addslashes($d1)).",interval 4 day)  from dual
      union select date_add(".$this->db->escape(addslashes($d1)).",interval 5 day)  from dual
      union select date_add(".$this->db->escape(addslashes($d1)).",interval 6 day)  from dual
      ) A order by A.data_dt";
        $query = $this->db->query($sql);

        $datas = $this->QueryToArray($query);

        for ($i = 0; $i < sizeof($datas); $i++) {
            $datas[$i]["sub"] = $this->print4Sql4($where, $datas[$i]['data_dt']);
            $datas[$i]["cWeek"] = $cweeks[$datas[$i]['week_nm']];
        }

        return $datas;
    }

    public function print4Sql4($where, $query_dt)
    {
        $sql = "select a.*, b.no_persons, b.room_code
      ,a1.m_cnt, a1.l_cnt, a1.d_cnt
      ,replace(c1.m_name,',','<br>') as m_name, c1.m_teach_cnt
      ,replace(c2.l_name,',','<br>') as l_name, c2.l_teach_cnt
      ,replace(c3.d_name,',','<br>') as d_name, c3.d_teach_cnt
      from
      (
       select distinct year, class_no, term, class_name, worker from dining_student_his
          where use_date {$where}
      ) a
      left join
      (
       select year, class_no, term, class_name
       ,(nvl(persons_1,0) + nvl(add_persons_1,0)) as m_cnt
       ,(nvl(persons_2,0) + nvl(add_persons_2,0)) as l_cnt
       ,(nvl(persons_3,0) + nvl(add_persons_3,0)) as d_cnt
       from dining_student_his
       where use_date = date(".$this->db->escape(addslashes($query_dt)).")
      ) a1 on a.year = a1.year and a.class_no = a1.class_no and a.term = a1.term
      left join `require` b on a.year = b.year and a.class_no = b.class_no and a.term = b.term
      left join
      (
       select year, class_no, term, use_date, name as m_NAME, count(*) as m_teach_cnt
       from dining_teacher
       where use_date = date(".$this->db->escape(addslashes($query_dt)).") and dining_type = 'A'
       group by year, class_no, term, use_date
      ) c1 on a.year = c1.year and a.class_no = c1.class_no and a.term = c1.term
      left join
      (
       select year, class_no, term, use_date, name as l_NAME, count(*) as l_teach_cnt
       from dining_teacher
       where use_date = date(".$this->db->escape(addslashes($query_dt)).")and  dining_type = 'B'
       group by year, class_no, term, use_date
      ) c2 on a.year = c2.year and a.class_no = c2.class_no and a.term = c2.term
      left join
      (
       select year, class_no, term, use_date, name as d_NAME, count(*) as d_teach_cnt
       from dining_teacher
       where use_date = date(".$this->db->escape(addslashes($query_dt)).") and dining_type = 'C'
       group by year, class_no, term, use_date
      ) c3 on a.year = c3.year and a.class_no = c3.class_no and a.term = c3.term
      order by a.year, a.class_no, a.term
      ";

        $query = $this->db->query($sql);

        return $this->QueryToArray($query);

    }

    public function print2($d1, $d2)
    {
        $where = "between '{$d1}' and '{$d2}' ";
        $result = array();
        $cweeklist = array("星期日", "星期一", "星期二", "星期三", "星期四", "星期五", "星期六");
        $result['sql1'] = $this->print4Sql1($where);
        $result['sql2'] = $this->Fprint4Sql2($where);
        // $result['sql3'] = $this->printSql2($where);
        $result['sql3'] = $this->print4Sql3($where, $d1, $cweeklist);
        $result['total'] = $this->printSql5($where);
        return $result;
    }

    

}

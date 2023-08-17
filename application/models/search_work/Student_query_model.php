<?php if (!defined('BASEPATH')) {
      exit('No direct script access allowed');
}

require_once APPPATH . "models/Common_model.php";
class Student_query_model extends Common_model
{
      public function getStudentQuery(
            $query_year,
            $query_class_no,
            $query_class_name,
            $queryContactor,
            $queryMix,
            $queryPreq,
            $apply_s_date,
            $apply_e_date,
            $apply_s_date1,
            $apply_e_date1,
            $apply_s_date2,
            $apply_e_date2,
            $sort
      ) {

            //組成 sql partial where constraint
            //$No_Date = true;
            $where = "";

            if ((!empty($query_year))) {
                  $where .= " AND a.year = '$query_year' ";
            }
            if ((!empty($query_class_no))) {
                  $where .= " AND UPPER(a.class_no) LIKE UPPER('%" . $query_class_no . "%')  ";
            }
            if ((!empty($query_class_name))) {
                  $where .= " AND upper(a.class_name) like upper('%" . $query_class_name . "%') ";
            }

            // 開班日期起迄
            if ((!empty($apply_s_date)) && (!empty($apply_e_date))) {
                  $where .= " and ( (  '{$apply_s_date}' between  start_date1 and end_date1  or  '{$apply_e_date}'  between start_date1 and end_date1 or
                   ( start_date1 >='" . $apply_s_date . "' and  end_date1<='" . $apply_e_date . "') ))  ";
            } elseif ((!empty($apply_s_date)) && (empty($apply_e_date))) {

                  $where .= " and  start_date1 >= '" . $apply_s_date . "' ";
            } elseif ((empty($apply_s_date)) && (!empty($apply_e_date))) {

                  $where .= " and end_date1 <= '" . $apply_e_date . "') ";
            }

            // 報名日期起迄
            if (!empty($apply_s_date1)) {
                  $where .= " AND  apply_s_date >='" . $apply_s_date1 . "' ";
            }
            if (!empty($apply_e_date1)) {
                  $where .= " AND apply_e_date <='" . $apply_e_date1 . "' ";
            }

            //上課日期起訖
            if (!empty($apply_s_date2) && !empty($apply_e_date2)) {
                  $where .= "    and (a.year, a.class_no, a.term) in
                  (select  year, class_id, term
                  from room_use
                  where use_date between '" . $apply_s_date2 . "' and '" . $apply_e_date2 . "') ";
            } elseif (!empty($apply_s_date2)) {
                  $where .= "    and (a.year, a.class_no, a.term) in
                  (select  year, class_id, term
                  from room_use
                  where  use_date >= '" . $apply_s_date2 . "') ";
            } elseif (!empty($apply_e_date2)) {
                  $where .= "    and (a.year, a.class_no, a.term) in
                  (select  year, class_id, term
                  from room_use
                  where  use_date  <= '" . $apply_e_date2 . "') ";
            }

            // 承辦人
            if ((!empty($queryContactor))) {
                  $where .= " AND a.worker like '%" . $queryContactor . "%' ";
            }


            // if (!empty($apply_s_date2) && !empty($apply_e_date2) && ($apply_s_date2 == $apply_e_date2))
            //       $where .= " order by belongto, date_format(a.start_date1, '%m-%d'), a.term";
            // else
            //       $where .= " order by date_format(a.start_date1, '%m-%d'), a.term";


            //撈資料 
            $data = $this->getSdata($queryMix, $queryPreq, $apply_s_date, $apply_e_date, $apply_s_date1, $apply_e_date1, $apply_s_date2, $apply_e_date2, $where,$sort);

            
                //var_dump($apply_s_date2);
                //var_dump($data);
                //if($apply_s_date2==$apply_e_date2){
                    for($i=0;$i<count($data);$i++){
                        $data[$i]['min_from_time']=$this->getClassTime($data[$i],$apply_s_date2,$apply_e_date2);
                    }
                //}
            //}

            return $data;
      }
      public function getClassTime($data,$start_date,$end_date)
      {
          $this->db->select_min('from_time');
          $this->db->where('ru.year',$data['year']);
          $this->db->where('ru.class_id',$data['class_no']);
          $this->db->where('ru.term',$data['term']);
          $this->db->where('ru.use_date >=',$start_date);
          $this->db->where('ru.use_date <=',$end_date);
          $this->db->join('periodtime as p','p.class_no=ru.class_id and p.term=ru.term and p.year=ru.year and p.course_date=ru.use_date and ru.use_period=p.id','inner');
          $course_code=['O00001','O00002','O00003','O00004','O00005'];
          $this->db->where_not_in('p.course_code',$course_code);
          $query=$this->db->get('room_use as ru');
          $query=$query->result_array();
          if(empty($query)){
              return $query[0]=null;
          }
          return $query[0];
          //var_dump($query->result_array());
          //die();
      }


      public function QuerySqlMyPy($apply_s_date2, $where)
      {
            // , ru.room_id AS rid

            $sql = "SELECT distinct
              a.seq_no, a.year, a.class_no, a.class_name, a.term, a.room_code, CR.room_bel as belongto, a.is_cancel, (IFNULL (e.persons_2, 0) + IFNULL (e.add_persons_2, 0)) as ecount, e.upd_date, e.upd_user, 
              (select ru.room_id
					   from room_use ru
					  where ru.year = a.year
						and ru.class_id = a.class_no
						and ru.term = a.term
						and date_format(use_date, '%Y-%m-%d') = '{$apply_s_date2}' limit 1) rid,
              a.no_persons, a.`range`, 
              date_format(a.apply_s_date,'%m-%d') AS apply_s_date, date_format(a.apply_e_date,'%m-%d') AS apply_e_date,
              date_format(a.apply_s_date2,'%m-%d') AS apply_s_date2, date_format(a.apply_e_date2,'%m-%d') AS apply_e_date2,
              date_format(a.start_date1,'%m-%d') AS start_date1, date_format(a.end_date1,'%m-%d') AS end_date1,
              v.name as contactor, CT.add_val1 AS contactor_tel,
              (SELECT count(*) FROM online_app WHERE yn_sel NOT IN ('6') and year=a.year and class_no=a.class_no and term=a.term) as scount,
              (SELECT count(*) FROM online_app WHERE yn_sel IN ('1', '3', '8') and year=a.year and class_no=a.class_no and term=a.term) as gcount,
              (SELECT count(*) FROM online_app WHERE yn_sel IN ('3') and year=a.year and class_no=a.class_no and term=a.term) as acount,
              (SELECT count(*) FROM online_app WHERE yn_sel IN ('1', '8') and year=a.year and class_no=a.class_no and term=a.term) as bcount,
              (select min(date_format(cre_date,'%Y-%m-%d'))  from mail_log where year=a.year and term=a.term and class_no=a.class_no and mail_type='3') as mail_date,
              (SELECT count(*) FROM room_use WHERE room_use.year=a.year and room_use.class_id=a.class_no and room_use.term=a.term) as classRoomCount

                  FROM `require` a
                  JOIN require_mix m
                        on a.year=m.year and a.term=m.term and a.class_no=m.class_no
                  join preq_main pm
                        on a.year=pm.year and a.term=pm.term and a.class_no=pm.class_no 
                        LEFT JOIN room_use ru ON ru.year = a.year and ru.class_id = a.class_no and ru.term = a.term and date_format(use_date,'%Y-%m-%d')= '$apply_s_date2' 
                  LEFT JOIN code_table CT
                        ON CT.item_id=a.worker AND CT.type_id='26'
                  LEFT JOIN venue_information CR
                        ON a.room_code = CR.room_id
                  LEFT JOIN BS_user v on CT.item_id=v.idno
                  LEFT JOIN dining_student e on e.year=a.year and e.class_no=a.class_no and e.term=a.term and e.use_date = '" . $apply_s_date2 . "'
                  WHERE 1=1 AND a.class_status != 1
                        " . $where . "
              
            ";

            return $sql;
      }



      public function QuerySqlMy($apply_s_date2, $where)
      {

            $sql = "SELECT distinct
              a.seq_no, a.year, a.class_no, a.class_name, a.term, a.room_code, CR.room_bel as belongto, a.is_cancel, (IFNULL (e.persons_2, 0) + IFNULL (e.add_persons_2, 0)) as ecount, e.upd_date, e.upd_user, 
              (select ru.room_id
					   from room_use ru
					  where ru.year = a.year
						and ru.class_id = a.class_no
                                    and ru.term = a.term";
            if($apply_s_date2 != '') {
                  $sql .= " and date_format(use_date, '%Y-%m-%d') = '{$apply_s_date2}'";
            }
            
		$sql .= " limit 1) rid,
              a.no_persons, a.`range`, 
              date_format(a.apply_s_date,'%m-%d') AS apply_s_date, date_format(a.apply_e_date,'%m-%d') AS apply_e_date,
              date_format(a.apply_s_date2,'%m-%d') AS apply_s_date2, date_format(a.apply_e_date2,'%m-%d') AS apply_e_date2,
              date_format(a.start_date1,'%m-%d') AS start_date1, date_format(a.end_date1,'%m-%d') AS end_date1,
              v.name as contactor, CT.add_val1 AS contactor_tel,
              (SELECT count(*) FROM online_app WHERE yn_sel NOT IN ('6') and year=a.year and class_no=a.class_no and term=a.term) as scount,
              (SELECT count(*) FROM online_app WHERE yn_sel IN ('1', '3', '8') and year=a.year and class_no=a.class_no and term=a.term) as gcount,
              (SELECT count(*) FROM online_app WHERE yn_sel IN ('3') and year=a.year and class_no=a.class_no and term=a.term) as acount,
              (SELECT count(*) FROM online_app WHERE yn_sel IN ('1', '8') and year=a.year and class_no=a.class_no and term=a.term) as bcount,
              (select min(date_format(cre_date,'%Y-%m-%d'))  from mail_log where year=a.year and term=a.term and class_no=a.class_no and mail_type='3') as mail_date,
              (SELECT count(*) FROM room_use WHERE room_use.year=a.year and room_use.class_id=a.class_no and room_use.term=a.term) as classRoomCount

                  FROM `require` a
                  JOIN require_mix m
                        on a.year=m.year and a.term=m.term and a.class_no=m.class_no
                        LEFT JOIN room_use ru ON ru.year = a.year and ru.class_id = a.class_no and ru.term = a.term"; 
                  
                  if($apply_s_date2 != '') {
                        $sql .= " and date_format(use_date,'%Y-%m-%d')= '$apply_s_date2'";
                  }
                  
            $sql .= " LEFT JOIN code_table CT
                        ON CT.item_id=a.worker AND CT.type_id='26'
                  LEFT JOIN venue_information CR
                        ON a.room_code = CR.room_id
                  LEFT JOIN BS_user v on CT.item_id=v.idno
                  LEFT JOIN dining_student e on e.year=a.year and e.class_no=a.class_no and e.term=a.term";
                  
                  if($apply_s_date2 != '') {
                        $sql .= " and e.use_date = '" . $apply_s_date2 . "'";
                  }

            $sql .= " WHERE 1=1 AND a.class_status != 1
                        " . $where . "
              
            ";

            return $sql;
      }


      public function QuerySqlPy($apply_s_date2, $where)
      {

            $sql = "SELECT distinct
              a.seq_no, a.year, a.class_no, a.class_name, a.term, a.room_code, CR.room_bel as belongto, a.is_cancel, (IFNULL (e.persons_2, 0) + IFNULL (e.add_persons_2, 0)) as ecount, e.upd_date, e.upd_user,  
              (select ru.room_id
					   from room_use ru
					  where ru.year = a.year
						and ru.class_id = a.class_no
						and ru.term = a.term
						and date_format(use_date, '%Y-%m-%d') = '{$apply_s_date2}' limit 1) rid,
              a.no_persons, a.range, 
              date_format(a.apply_s_date,'%m-%d') AS apply_s_date, date_format(a.apply_e_date,'%m-%d') AS apply_e_date,
              date_format(a.apply_s_date2,'%m-%d') AS apply_s_date2, date_format(a.apply_e_date2,'%m-%d') AS apply_e_date2,
              date_format(a.start_date1,'%m-%d') AS start_date1, date_format(a.end_date1,'%m-%d') AS end_date1,
              v.name as contactor, CT.add_val1 AS contactor_tel,
              (SELECT count(*) FROM online_app WHERE yn_sel NOT IN ('6') and year=a.year and class_no=a.class_no and term=a.term) as scount,
              (SELECT count(*) FROM online_app WHERE yn_sel IN ('1', '3', '8') and year=a.year and class_no=a.class_no and term=a.term) as gcount,
              (SELECT count(*) FROM online_app WHERE yn_sel IN ('3') and year=a.year and class_no=a.class_no and term=a.term) as acount,
              (SELECT count(*) FROM online_app WHERE yn_sel IN ('1', '8') and year=a.year and class_no=a.class_no and term=a.term) as bcount,
              (select min(date_format(cre_date,'%Y-%m-%d'))  from mail_log where year=a.year and term=a.term and class_no=a.class_no and mail_type='3') as mail_date,
              (SELECT count(*) FROM room_use WHERE room_use.year=a.year and room_use.class_id=a.class_no and room_use.term=a.term) as classRoomCount
                  FROM `require` a
                  join preq_main pm
                        on a.year=pm.year and a.term=pm.term and a.class_no=pm.class_no 
                        LEFT JOIN room_use ru ON ru.year = a.year and ru.class_id = a.class_no and ru.term = a.term and date_format(use_date,'%Y-%m-%d')= '$apply_s_date2' 
                  LEFT JOIN code_table CT
                        ON CT.item_id=a.worker AND CT.type_id='26'
                  LEFT JOIN venue_information CR
                        ON a.room_code = CR.room_id
                  LEFT JOIN BS_user v on CT.item_id=v.idno
                  LEFT JOIN dining_student e on e.year=a.year and e.class_no=a.class_no and e.term=a.term and e.use_date = '" . $apply_s_date2 . "'
                  WHERE 1=1 AND a.class_status != 1
                        " . $where . "
                        
            ";

            return $sql;
      }


      public function QuerySql($apply_s_date2, $where)
      {

            $sql = "SELECT distinct
            a.seq_no,a.year, a.class_no, a.class_name, a.term, a.room_code, CR.room_bel as belongto, a.is_cancel, (IFNULL (e.persons_2, 0) + IFNULL (e.add_persons_2, 0)) as ecount, e.upd_date, e.upd_user, 
            (select ru.room_id
					   from room_use ru
					  where ru.year = a.year
						and ru.class_id = a.class_no
						and ru.term = a.term
						and date_format(use_date, '%Y-%m-%d') = '{$apply_s_date2}' limit 1) rid,
            a.no_persons, a.range, 
            date_format(a.apply_s_date,'%m-%d') AS apply_s_date, date_format(a.apply_e_date,'%m-%d') AS apply_e_date,
            date_format(a.apply_s_date2,'%m-%d') AS apply_s_date2, date_format(a.apply_e_date2,'%m-%d') AS apply_e_date2,
            date_format(a.start_date1,'%m-%d') AS start_date1, date_format(a.end_date1,'%m-%d') AS end_date1,
            v.name as contactor, CT.add_val1 AS contactor_tel,
            (SELECT count(*) FROM online_app WHERE yn_sel NOT IN ('6') and year=a.year and class_no=a.class_no and term=a.term) as scount,
            (SELECT count(*) FROM online_app WHERE yn_sel IN ('1', '3', '8') and year=a.year and class_no=a.class_no and term=a.term) as gcount,
            (SELECT count(*) FROM online_app WHERE yn_sel IN ('3') and year=a.year and class_no=a.class_no and term=a.term) as acount,
            (SELECT count(*) FROM online_app WHERE yn_sel IN ('1', '8') and year=a.year and class_no=a.class_no and term=a.term) as bcount,
            (select min(date_format(cre_date,'%Y-%m-%d'))  from mail_log where year=a.year and term=a.term and class_no=a.class_no and mail_type='3') as mail_date,
            (SELECT count(*) FROM room_use WHERE room_use.year=a.year and room_use.class_id=a.class_no and room_use.term=a.term) as classRoomCount
                  FROM `require` a
                  LEFT JOIN room_use ru ON ru.year = a.year and ru.class_id = a.class_no and ru.term = a.term and date_format(use_date,'%Y-%m-%d')= '$apply_s_date2' 
                        LEFT JOIN code_table CT
                                    ON CT.item_id=a.worker AND CT.type_id='26'
                        LEFT JOIN venue_information CR
                                    ON a.room_code = CR.room_id
                        LEFT JOIN BS_user v on CT.item_id=v.idno
                        LEFT JOIN dining_student e on e.year=a.year and e.class_no=a.class_no and e.term=a.term and date_format(e.use_date,'%Y-%m-%d') = '{$apply_s_date2}'
                              WHERE 1=1 AND a.class_status != 1 " . $where . "
              
            ";

            return $sql;
      }

      public function getSdata($queryMix, $queryPreq, $apply_s_date, $apply_e_date, $apply_s_date1, $apply_e_date1, $apply_s_date2, $apply_e_date2, $where, $sort)
      {

            /*調訓人數 設為0 無欄位*/
            //#47449 實體系統-20M、20N、21A教室有與20H不一致的狀況：教室代碼改取自room_use
            // 20130525 教室代碼改回 room_code
            if ($queryMix == "y" && $queryPreq == "y") {

                  $sql = $this->QuerySqlMyPy($apply_s_date2, $where);
            } else if ($queryMix == "y") {

                  $sql = $this->QuerySqlMy($apply_s_date2, $where);
            } else if ($queryPreq == "y") {

                  $sql = $this->QuerySqlPy($apply_s_date2, $where);
            } else {

                  $sql = $this->QuerySql($apply_s_date2, $where);
            }

            if($sort != '') {
                  $orderArr = explode("+", $sort);
                  $order = " order by ".$orderArr[0]." ".$orderArr[1];

                  if($orderArr[0] == 'apply_s_date') {
                        $order .= ', apply_e_date'." ".$orderArr[1];
                  }
                  else if($orderArr[0] == 'start_date1') {
                        $order .= ', end_date1'." ".$orderArr[1];
                  }

                  $where .= $order;
            }
            else if (!empty($apply_s_date2) && !empty($apply_e_date2) && ($apply_s_date2 == $apply_e_date2)) {
                  $where .= " order by belongto, date_format(a.start_date1, '%m-%d'), a.term";
            }
            else {
                  $where .= " order by date_format(a.start_date1, '%m-%d'), a.term";
            }
                  
            $sql .= $where;

            $rssql = $this->db->query($sql);

            $rs = $this->QueryToArray($rssql);

            //填資料      
            if ($rs)

                  for ($i = 0; $i < sizeof($rs); $i++) {

                        if (!empty($apply_s_date2) && !empty($apply_e_date2) && ($apply_s_date2 == $apply_e_date2)) {
                              // if query date is one then show the room_id for that day
                              $sql = "select room_id from periodtime where year = '{$rs[$i]['year']}' and term = '{$rs[$i]['term']}' and class_no = '{$rs[$i]['class_no']}' and course_date = '{$apply_s_date2}' and course_code != '021360' order by from_time";
                              //echo "sql:".$sql."<BR>";
                              if ($rs_rid = $this->db->query($sql)) {
                                    $r_id =  $this->QueryToArray($rs_rid);
                                    if(!empty($r_id)){
                                      $rs[$i]['rid'] = $r_id[0]['room_id'];
                                    } else {
                                      $rs[$i]['rid'] = '';
                                    }     
                              }
                        }

                        if (!empty($rs[$i]['year']) &&  !empty($rs[$i]['year']) && !empty($rs[$i]['class_no']) && !empty($rs[$i]['term']) && !empty($rs[$i]['term']) && !empty($rs[$i]['upd_date'])) {
                              $sql_check = sprintf("SELECT dining_count FROM dining_info WHERE year = " . $rs[$i]['year'] . " AND class_no = '" . $rs[$i]['class_no'] . "' AND term = " . $rs[$i]['term'] . " AND use_date = '" . $rs[$i]['upd_date'] . "' ");
                              $rs_check = $this->db->query($sql_check);
                              $lcnt_info = $this->QueryToArray($rs_check);
                        } else {
                              $lcnt_info = '';
                        }

                        if (!empty($lcnt_info)) {
                              $rs['ecount'] = $lcnt_info['dining_count'];
                        }

                        $sql = sprintf("SELECT
                                                  MIN(from_time) from_time
                                            FROM
                                                  periodtime
                                            WHERE
                                                COURSE_DATE = (
                                                      SELECT
                                                              MIN(COURSE_DATE)
                                                      FROM
                                                            periodtime
                                                      WHERE
                                                      year = " . $rs[$i]['year'] . "
                                                      AND class_no = '" . $rs[$i]['class_no'] . "'
                                                      AND term = " . $rs[$i]['term'] . "
                                                      AND course_code NOT IN ('O00003', '021360')
                                                )
                                            AND year = " . $rs[$i]['year'] . "
                                            AND class_no = '" . $rs[$i]['class_no'] . "'
                                            AND term = " . $rs[$i]['term'] . "
                                            AND course_code NOT IN ('O00003', '021360')");

                        if ($rs_ftime = $this->db->query($sql)) {
                              $ftime_data = $this->QueryToArray($rs_ftime);

                              if (!empty($ftime_data[0]['from_time'])) {
                                    $rs[$i]['FROM_TIME'] = $ftime_data[0]['from_time'];
                              } else {
                                    $rs[$i]['FROM_TIME'] = '';
                              }
                        }


                        // get 當日教室代碼 名稱
                        $sql = "select room_id,room_bel as belongto,room_name as name,room_sname as sname from venue_information where room_type = '01' and room_id = '{$rs[$i]['rid']}'"; //
                        if ($rs_rname = $this->db->query($sql)) {
                              $rname_data = $this->QueryToArray($rs_rname);
                              if (sizeof($rname_data) != 0) {
                                    // 名稱:若為公訓處且簡稱非空->簡稱
                                    if ($rname_data[0]['belongto'] == "68000" && ($rname_data[0]['sname'] != ""))
                                          $rs[$i]['rid_name'] = $rname_data[0]['sname'];
                                    // 否則	名稱:完整名稱
                                    else
                                          $rs[$i]['rid_name'] = $rname_data[0]['name'];
                              } else {
                                    $rs[$i]['rid_name'] = "";
                              }
                        }

                        // get 教室代碼 名稱
                        $sql = "select room_id,room_bel as belongto,room_name as name,room_sname as sname from venue_information where room_type = '01' and room_id = '{$rs[$i]['room_code']}'";
                        if ($rs_rname = $this->db->query($sql)) {
                              $rname_data = $this->QueryToArray($rs_rname);
                              // 若為公訓處且簡稱非空->簡稱
                              if (sizeof($rname_data) != 0) {
                                    if ($rname_data[0]['belongto'] == "68000" && ($rname_data[0]['sname'] != ""))
                                          $rs[$i]['room_code_name'] = $rname_data[0]['sname'];
                                    else
                                          $rs[$i]['room_code_name'] = $rname_data[0]['name'];
                              } else {
                                    $rs[$i]['room_code_name'] = "";
                              }
                        }
                        if ($queryPreq == "y") {
                              $sql = "select IFNULL(pr.RESULT_COUNT,0) RESULT_COUNT from preq_main pm left join 
                                      (select preq_id,count(*)as RESULT_COUNT from (SELECT distinct preq_id, short_id FROM preq_result) z group by preq_id) pr  
                                       on pm.preq_id=pr.preq_id     
                                       where pm.year = '{$rs[$i]['year']}' and pm.term = {$rs[$i]['term']} and pm.class_no = '{$rs[$i]['class_no']}'";
                              //echo "sql:".$sql;
                              $preq_ans_count = $this->QueryToArray($this->db->query($sql))[0]['RESULT_COUNT'];
                              $rs[$i]['preq_anscount'] = $preq_ans_count;

                              $sql = "select preq_id from preq_main where year = '{$rs[$i]['year']}' and term = '{$rs[$i]['term']}' and class_no = '{$rs[$i]['class_no']}'";
                              $preq_id = $this->QueryToArray($this->db->query($sql))[0]['preq_id'];
                              $rs[$i]['preq_id'] = $preq_id;
                        }
                        if (!empty($rs[$i]['contactor_tel'])) {
                              $rs[$i]['contactor'] .= "({$rs[$i]['contactor_tel']})";
                        }
                  }
            return $rs;
      }



      public function getSdata2($year, $apply_s_date2, $apply_e_date2)
      {
            
      //       //  抓承辦人
	// $sql = "SELECT
      //             DESCRIPTION  AS NAME
      //             -- vaa.first_name||vaa.last_name AS NAME
      //             FROM code_table ct
      //             LEFT JOIN vm_all_account vaa
      //             ON ct.item_id=vaa.personal_id
      //             WHERE ct.type_id='26'";
      // $rssql = $this->db->query($sql);

      // $rs = $this->QueryToArray($rssql);

      $data_5c = array();

      // for ($i = 0; $i < sizeof($rs); $i++) {
      //       $data_5c['workers'][] = $rs[$i];
      //       }

      if(!empty($apply_s_date2) && !empty($apply_e_date2) ) {
            $where_5c = sprintf("a.appi_id IN (SELECT DISTINCT appi_id FROM room_use WHERE use_date BETWEEN '%s' AND '%s' AND appi_id IS NOT NULL AND year(use_date)-1911=$year)",$apply_s_date2,$apply_e_date2);

            $sql = "SELECT b.app_name, b.contact_name, b.tel, b.fax, b.zone, b.addr, b.email, a.* FROM appinfo a left join applicant b on a.app_id = b.app_id " .
            "where " . $where_5c . " order by a.app_date desc";

            $rssql = $this->db->query($sql);
            $rs = $this->QueryToArray($rssql);
            $data_5c=$rs;
      
            // for ($i = 0; $i < sizeof($rs); $i++) {
            //       $data_5c[] = $rs[$i] ;
            // }

            
            for($i=0;$i<sizeof($data_5c);$i++){

                  // $app_start='';
                  // $app_end='';
                  // $room_name='';
                  $data_5c[$i]['room_name'] = '';
                  $sql_new = sprintf("SELECT
                        MIN(z.app_date_s) as app_start,
                        MAX(z.app_date_e) as app_end,
                        z. NAME AS room_name
                        FROM
                        (
                              SELECT
                              MIN(use_date) AS app_date_s,
                              MAX(use_date) AS app_date_e,
                              cat_id,
                              room_id,
                              NAME
                              FROM
                              (
                              SELECT
                                    a .*, b.room_type, b.room_name as NAME,
                                    CASE
                              WHEN date_format(use_date, 'D') IN ('1', '7') THEN
                                    1
                              END AS weekend
                              FROM
                                    room_use a
                              JOIN venue_information b ON a .room_id = b.room_id
                              WHERE
                                    appi_id = '".$data_5c[$i]['appi_id']."'
                              ) zz
                              GROUP BY
                              appi_id,
                              room_type,
                              room_id,
                              NAME,
                              use_period,
                              unit,
                              num,
                              discount,
                              groupnum,
                              groupnote
                              ORDER BY
                              room_type,
                              room_id,
                              app_date_s,
                              use_period
                        ) z 
                        
                        GROUP BY
                        z. NAME,
                        z.room_id");

                        $sql = $this->db->query($sql_new);
                        $rs_new = $this->QueryToArray($sql);
                       

                        if(count($rs_new)==0){
                            $data_5c[$i]['app_start'] = " ";
                            $data_5c[$i]['app_end'] = " ";
                            $data_5c[$i]['room_name'] .= " "."<br/>";
                        }
                              for ($j=0; $j < sizeof($rs_new); $j++) { 
                                    $result=$rs_new[$j];
                                    $data_5c[$i]['app_start'] = $result['app_start'];
                                    $data_5c[$i]['app_end'] = $result['app_end'];
                                    $data_5c[$i]['room_name'] .= $result['room_name'].'<br/>'; 
                              }

                              // $app_start = array_column($rs_new, 'app_start');
                              // $app_end = array_column($rs_new, 'app_end');
                              // $room_name = array_column($rs_new, 'room_name');

                              // $data_5c[$i]['app_start'] =$app_start;
                              // $data_5c[$i]['app_end'] =$app_end;
                              // $data_5c[$i]['room_name'] =$room_name;
                                          
                        
                        }

                        

                        for($i=0;$i<sizeof($data_5c);$i++){
                              $app_period='';

                              $sql_5c = sprintf("SELECT A.*, B2.IS_PUBLIC, C.room_countby, C.room_name, D.DESCRIPTION AS CAT_NAME, E.DESCRIPTION AS period FROM " .
                                    "( " .
                                    "SELECT MIN(use_date) AS app_date_s, MAX(use_date) AS app_date_e, appi_id, cat_id, room_id, use_period, unit, num, " .
                                    "SUM(EXPENSE) AS EXPENSE, discount, groupnum, groupnote, SUM(weekend) AS weekend " . 
                                    "FROM (SELECT a.*, CASE WHEN date_format(use_date,'D') IN ('1','7') THEN 1 END AS weekend FROM room_use a) zz " . 
                                    "WHERE appi_id = '".$data_5c[$i]['appi_id']."' AND use_date GROUP BY " .
                                    "appi_id, cat_id, room_id, use_period, unit, num, discount, groupnum, groupnote " .
                                    ") A " .
                                    "LEFT JOIN appinfo B1 ON A.appi_id = B1.appi_id " .
                                    "LEFT JOIN applicant B2 ON B1.app_id = B2.app_id " .
                                    "LEFT JOIN venue_information C ON A.room_id = C.room_id " .
                                    "LEFT JOIN code_table D ON C.room_id = D.ITEM_ID AND D.TYPE_ID = '20' " .
                                    "LEFT JOIN code_table E ON A.use_period = E.ITEM_ID AND E.TYPE_ID = '31' " . 
                                    //"LEFT JOIN classroom_timeprice F ON A.room_id = F.room_id AND A.use_period = F.USETIME " . 
                                    //"WHERE app_date_s BETWEEN '".$apply_s_date2."' AND '".$apply_e_date2."' 
                                    "ORDER BY room_type, room_id, app_date_s, use_period");
            
                        $sql_5c = $this->db->query($sql_5c);
                        $rs_5c = $this->QueryToArray($sql_5c);

                        if(count($rs_5c)==0){
                            $data_5c[$i]['period'] = " ";
                        }

                        for ($j=0; $j < sizeof($rs_5c); $j++) { 
                              $fields_5c=$rs_5c[$j];
                              $data_5c[$i]['period'] = $fields_5c['period'];
                        }
                  //       while($fields_5c = $rs_5c->FetchRow()){
                  //             $data_5c[$i]['PERIOD'] = $fields_5c['PERIOD'];
                  //     }
                                          
                  //       $app_period = array_column($rs_5c, 'period');
                  //       $data_5c[$i]['period'] = $app_period;
                        
                        }

       }
      return $data_5c;
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


      public function csvexport($filename, $query_start_date, $query_end_date, $data)
      {
            $filename = iconv("UTF-8", "BIG5", '查詢作業-各班期課表及研習人員名冊.csv');

            header("Content-type: application/csv");
            header("Content-Disposition: attachment; filename=$filename");

            echo iconv("UTF-8", "BIG5", "台北市政府公務人員訓練處,");
            echo iconv("UTF-8", "BIG5", "各班期課表及研習人員名冊\r\n");
            //echo iconv("UTF-8","BIG5",$query_start_date."至".$query_end_date."\r\n");

            echo iconv("UTF-8", "BIG5", "班期代碼,");
            echo iconv("UTF-8", "BIG5", "班期名稱,");
            echo iconv("UTF-8", "BIG5", "期別,");
            echo iconv("UTF-8", "BIG5", "承辦人(分機),");
            echo iconv("UTF-8", "BIG5", "教室,");

            echo iconv("UTF-8", "BIG5", "報名起迄日,");
            echo iconv("UTF-8", "BIG5", "期程(小時),");
            echo iconv("UTF-8", "BIG5", "開班日期,");
            echo iconv("UTF-8", "BIG5", "每期人數,");
            echo iconv("UTF-8", "BIG5", "報名人數,");

            echo iconv("UTF-8", "BIG5", "調訓日期,");
            echo iconv("UTF-8", "BIG5", "選+調結=研習人數,");
            echo iconv("UTF-8", "BIG5", "用餐人數\r\n");


            foreach ($data as $val) {
                  $totalcount = $val['acount']+$val['bcount'];
                  echo iconv("UTF-8", "BIG5", $val['class_no']) . ',';
                  echo iconv("UTF-8", "BIG5", $val['class_name']) . ',';
                  echo iconv("UTF-8", "BIG5", $val['term']) . ',';
                  echo iconv("UTF-8", "BIG5", $val['contactor'] . ($val['contactor_tel']!=''?'(':'') . $val['contactor_tel']) . ($val['contactor_tel']!=''?'(':'') . ',';
                  echo iconv("UTF-8", "BIG5", $val['room_code_name']) . ',';

                  echo iconv("UTF-8", "BIG5", $val['apply_s_date'] . "~" . $val['apply_e_date']) . ',';
                  echo iconv("UTF-8", "BIG5", $val['range']) . ',';
                  echo iconv("UTF-8", "BIG5", $val['start_date1']  . "~" . $val['end_date1']) . ',';
                  echo iconv("UTF-8", "BIG5", $val['no_persons']) . ',';
                  echo iconv("UTF-8", "BIG5", $val['scount']) . ',';

                  echo iconv("UTF-8", "BIG5", $val['mail_date']) . ',';
                  echo iconv("UTF-8", "BIG5", $val['acount'] . "+" . $val['bcount'] . "=" . $totalcount) . ',';
                  echo iconv("UTF-8", "BIG5", $val['ecount']) . ',';


                  echo "\r\n";
            }


      }
}
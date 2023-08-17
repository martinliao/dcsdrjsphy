<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

require_once APPPATH . "models/Common_model.php";
class Vegetarian_management_model extends Common_model
{
    public function getVegetarianSearch($appDateS)
    { 

      $firstPartData = $this->SqlP1($appDateS);
      
      $data = array();
      
      for($i=0;$i<sizeof($firstPartData);$i++){
        $temp=$firstPartData[$i];
        $tmploopData =  $this->getLoopSql($firstPartData[$i]["YEAR"],$firstPartData[$i]["CLASS_NO"]
        ,$firstPartData[$i]["TERM"],$firstPartData[$i]["COURSE_DATE"]);
        
        // if(empty($tmploopData)) {
        //   continue;
        // }
        $temp['class_name']=$firstPartData[$i]["class_name"];
        $temp['room_id']=$firstPartData[$i]["room_id"];
        $tmploopDatap2 = $this->getLoopSqlP2($firstPartData[$i]["YEAR"],$firstPartData[$i]["CLASS_NO"]
        ,$firstPartData[$i]["TERM"],$firstPartData[$i]["COURSE_DATE"]);


        $temp['DINING_COUNT'] = $tmploopDatap2[0]['cnt'];

        $tmploopDatap3 = $this->getLoopSqlP3($firstPartData[$i]["YEAR"],$firstPartData[$i]["CLASS_NO"]
        ,$firstPartData[$i]["TERM"],$firstPartData[$i]["COURSE_DATE"]);

        if(!empty($tmploopDatap3[0]['to_time'])){
          $temp['TO_TIME'] = $tmploopDatap3[0]['to_time'];
        } else {
          $temp['TO_TIME'] = $tmploopDatap2[0]['use_date'];
        }

        $tmploopDatap4 = $this->getLoopSqlP4($firstPartData[$i]["YEAR"],$firstPartData[$i]["CLASS_NO"]
        ,$firstPartData[$i]["TERM"],$firstPartData[$i]["COURSE_DATE"]);

        if(!empty($tmploopDatap4[0]['arrival_time'])){
          $temp['ARRIVAL_TIME'] = $tmploopDatap4[0]['arrival_time'];
        }else {
          $temp['ARRIVAL_TIME'] = "";
        }

        $tmploopDatap5 = $this->getLoopSqlP5($firstPartData[$i]["YEAR"],$firstPartData[$i]["CLASS_NO"]
        ,$firstPartData[$i]["TERM"],$firstPartData[$i]["COURSE_DATE"]);

        $temp['totalCount'] = $tmploopDatap5[0]['cnt'];
        
        $tmploopDatap6 = $this->getLoopSqlP6($firstPartData[$i]["YEAR"],$firstPartData[$i]["CLASS_NO"]
        ,$firstPartData[$i]["TERM"],$firstPartData[$i]["COURSE_DATE"]);
        
        
        if(!empty($tmploopDatap6[0]['remark'])){
          $temp['REMARK'] = $tmploopDatap6[0]['remark'];
        }else {
          $temp['REMARK'] = "";
        }

        $tmploopDatap7 = $this->getLoopSqlP7($firstPartData[$i]["YEAR"],$firstPartData[$i]["CLASS_NO"]
        ,$firstPartData[$i]["TERM"],$firstPartData[$i]["COURSE_DATE"]);

        if(isset($tmploopDatap7[0]['teacher_vegt'])){
          $temp['teacher_vegt'] = $tmploopDatap7[0]['teacher_vegt'];
          $temp['teacher_vegt_changed'] = 1;
        } else {
          $temp['teacher_vegt'] = 0;
          $temp['teacher_vegt_changed'] = 0; 
        }

        array_push($data, $temp);

      }

        return $data;
        

    }

    public function getVegetarianSearch2($appDateS){

        $where ="where oa.yn_sel in (3,8)";
          if ($appDateS != ""){
            $where .= " AND v.course_date = date(".$this->db->escape(addslashes($appDateS)).") ";  
          }
        // $sql_total = sprintf("select count(1) cnt from vegetarian where course_date = '%s'",$appDateS);
        // $query = $this->db->query($sql_total);

        // return $this->QueryToArray($query);

        // $sql_get = sprintf("select count(1) cnt from vegetarian where course_date = '%s' and isget = 1",$appDateS);
        
        // $query = $this->db->query($sql_get);

        // return $this->QueryToArray($query);

        // $rs_get = db_excute($sql_get);
        // $fields_get = $rs_get->FetchRow();

        // if(empty($data)){
        //   echo '<script>alert("當日無人申請");</script>';
        // }
        $data2 = array();
        $sql = "select v.id,v.year,v.class_no,v.term,v.class_name,v.name,oa.st_no,date_format(v.get_time,'%Y-%m-%d %H:%i:%s') get_time 
                FROM vegetarian v
                JOIN online_app oa ON oa.class_no = v.class_no AND oa.year = v.year AND oa.term = v.term AND oa.id = v.idno
                {$where} 
                order by class_name,term";
        
        $query = $this->db->query($sql);

        $sDate = $this->QueryToArray($query);

        for($i = 0 ; $i < sizeof($sDate);$i++){
          array_push($data2, $sDate[$i]);        
        }

        return $data2;
    }

    public function getVegetarianSearch3($appDateS){

      $data = array();

      $sql_total = sprintf("select count(1) cnt from vegetarian where course_date = %s",$this->db->escape(addslashes($appDateS)));
      $rs_total = $this->db->query($sql_total);
      $fields_total = $this->QueryToArray($rs_total)[0]['cnt'];
      $data['fields_total']=$fields_total;

      $sql_get = sprintf("select count(1) cnt from vegetarian where course_date = %s and isget = 1",$this->db->escape(addslashes($appDateS)));
      $rs_get = $this->db->query($sql_get);
      $fields_get = $this->QueryToArray($rs_get)[0]['cnt'];
      $data['fields_get']=$fields_get;

      return $data;
    }


    public function SqlP1($appDateS){
        

      $where ="";
        if ($appDateS != ""){
          $where .= " b.course_date = date(".$this->db->escape(addslashes($appDateS)).") AND";  
        }

/* $where .= " e.MAIL_MAG_COUNT > 0
          AND e.MAIL_STUDENT_COUNT > 0
          AND f.belongto = '68000'
          AND '1130' BETWEEN b.from_time
          AND b. TO_TIME"; */

          //20211004 Roger 將只要早上有課的都列出來
          $where .= " e.MAIL_MAG_COUNT > 0
          AND e.MAIL_STUDENT_COUNT > 0
          AND f.belongto = '68000'
          AND b.from_time  BETWEEN '0800' and '1200'
          AND b. TO_TIME";

        $sql = "
          SELECT distinct 
            b.YEAR,
            b.CLASS_NO,
            b.TERM,
            b.from_time,
            b.TO_TIME,
            b.COURSE_DATE,
            b.room_id,
            c.class_name,
            c.worker,
            bs.`name`,
            g.ext1 as office_tel
          FROM periodtime b 
          JOIN `require` c on b.YEAR = c.YEAR
          AND b.class_no = c.class_no
          AND b.term = c.term
          JOIN require_list e ON c.SEQ_NO = e.SEQ_NO
          AND b.CLASS_NO = c.class_no
          AND b.TERM = c.term
          JOIN classroom f on b.room_id = f.room_id 
          JOIN BS_user bs on bs.idno = c.worker
          JOIN agent_set g ON g.item_id = c.worker 
          WHERE ";

        //$orderby = " ORDER BY c.class_name,b.term ";
        $orderby = " GROUP BY b.room_id ORDER BY c.class_name,b.term "; //讓同教室的合併

        $sql = $sql . " " . $where . " " . $orderby;
      
        $query = $this->db->query($sql);

        return $this->QueryToArray($query);
    }

    public function getLoopSql($year,$classno,$term,$courseDate){
      $sql = "select * from dining_info where year = '".$year."' and class_no = '".$classno."' and term = 
      '".$term."' and use_date = date_format('".$courseDate."','%Y-%m-%d')";
      $query = $this->db->query($sql);

      return $this->QueryToArray($query);
    }

    public function getLoopSqlP2($year,$classno,$term,$courseDate){
      $sql = "select count(1) as cnt from (select gid from card_log where year = '".$year."' and class_no = '".$classno."' and term = '".$term."'
       and use_date = date_format('".$courseDate."','%Y-%m-%d') group by gid)h";
      $query = $this->db->query($sql);

      return $this->QueryToArray($query);
    }

    public function getLoopSqlP3($year,$classno,$term,$courseDate){
      $sql_p = sprintf(" select * from (select case when '1140' between from_time and to_time then 'Y' 
      end is_lunch , to_time from periodtime where year=%s and class_no = %s and term = %s 
      and course_date=%s)h where is_lunch = 'Y'"
      ,$this->db->escape(addslashes($year)),$this->db->escape(addslashes($classno)),$this->db->escape(addslashes($term)),$this->db->escape(addslashes($courseDate)));

      $query = $this->db->query($sql_p);

      return $this->QueryToArray($query);
    }

    public function getLoopSqlP4($year,$classno,$term,$courseDate){
      $sql_a = sprintf("select arrival_time from arrival where year = %s and class_no = %s 
      and term = %s and course_date = %s"
      ,$this->db->escape(addslashes($year)),$this->db->escape(addslashes($classno)),$this->db->escape(addslashes($term)),$this->db->escape(date('Y-m-d',strtotime(addslashes($courseDate)))));

      $query = $this->db->query($sql_a);

      return $this->QueryToArray($query);
    }

    public function getLoopSqlP5($year,$classno,$term,$courseDate){
/*
      $sql_a = " select count(1) cnt from vegetarian where year = '".$year."' 
      and class_no = '".$classno."' and term = '".$term."' and course_date = date_format('".$courseDate."','%Y-%m-%d')";
*/
      //2021-04-26 排除未選員或未調訓人員
      $sql_a = "select count(1) cnt from vegetarian v 
                LEFT JOIN online_app oa ON  oa.class_no =  v.class_no and oa.term = v.term AND oa.year = v.year AND oa.id = v.idno
                WHERE v.year = '".$year."' 
                AND v.class_no = '".$classno."' 
                AND v.term = '".$term."' 
                AND v.course_date = date_format('".$courseDate."','%Y-%m-%d') 
                AND oa.yn_sel IN(3,8)";
      $query = $this->db->query($sql_a);

      return $this->QueryToArray($query);
     
    }

    public function getLoopSqlP6($year,$classno,$term,$courseDate){

      $sql_a = "select remark from remark_14e where year = '".$year."' and class_no = '".$classno."' 
                and term = '".$term."' and date_format(course_date,'%Y-%m-%d') = date_format('".$courseDate."','%Y-%m-%d')";
                
      $query = $this->db->query($sql_a);

      return $this->QueryToArray($query);
     
    }

    public function getLoopSqlP7($year,$classno,$term,$courseDate){
      $sql_a = sprintf("select teacher_vegt from teacher_vegetarian where year = %s and class_no = %s 
      and term = %s and course_date = %s"
      ,$this->db->escape(addslashes($year)),$this->db->escape(addslashes($classno)),$this->db->escape(addslashes($term)),$this->db->escape(date('Y-m-d',strtotime(addslashes($courseDate)))));

      $query = $this->db->query($sql_a);

      return $this->QueryToArray($query);
    }
    
    public function insertTeacherVegetarian($year,$classno,$term,$start_date,$teach_vegt_count)
    {   
      if(!empty($year) && !empty($classno) && !empty($term) && !empty($start_date)){
        $year = $this->db->escape(intval($year));
        $classno = $this->db->escape(addslashes($classno));
        $term = $this->db->escape(intval($term));
        $start_date = $this->db->escape(date('Y-m-d',strtotime(addslashes($start_date))));
        $teach_vegt_count = $this->db->escape(intval($teach_vegt_count));

        $sql_a = sprintf("select count(1) cnt from teacher_vegetarian where year = %s and class_no = %s and term = %s and course_date = %s", $year, $classno, $term, $start_date);

        $query = $this->db->query($sql_a);

        $checkExist = $this->QueryToArray($query);

        if($checkExist[0]['cnt'] > 0){
          $sql = sprintf("update teacher_vegetarian set teacher_vegt = %s where year = %s and class_no = %s and term = %s and course_date = %s", $teach_vegt_count, $year, $classno, $term, $start_date);
        } else {
          $sql = sprintf("insert into teacher_vegetarian(year,class_no,term,course_date,teacher_vegt) values(%s,%s,%s,%s,%s)", $year, $classno, $term, $start_date, $teach_vegt_count);
        }
        
        if($this->db->query($sql)){
          return true;
        }
      }

      return false;
    }

    public function insertVegetarianSearch($year,$classno,$term,$start_date)
    {   
      if(!empty($year) && !empty($classno) && !empty($term)){
        $arrival_time = date("H:i");
        $sql = sprintf("insert into arrival(year,class_no,term,course_date,arrival_time) values(%s,%s,%s,%s,%s)",$this->db->escape(addslashes($year)),$this->db->escape(addslashes($class_no)),$this->db->escape(addslashes($term)),$this->db->escape(date('Y-m-d',strtotime(addslashes($start_date)))),$this->db->escape(addslashes($arrival_time)));

        if($this->db->query($sql)){
          return true;
        }
      }

      return false;
    }
    public function insertVegetarianBinSearch($year,$classno,$term,$start_date)
    {   
      $year = $year;
      $class_no = $classno;
      $term = $term;

      if(!empty($year) && !empty($class_no) && !empty($term)){
        $arrival_time = '便當';
        $sql = sprintf("insert into arrival(year,class_no,term,course_date,arrival_time) values(%s,%s,%s,%s,%s)",$this->db->escape(addslashes($year)),$this->db->escape(addslashes($class_no)),$this->db->escape(addslashes($term)),$this->db->escape(date('Y-m-d',strtotime($start_date))),$this->db->escape(addslashes($arrival_time)));

        if($this->db->query($sql)){
          return true;
        }
      }

      return false;
    }
    
    public function updateVegetarianSearch($id)
    {   
        $sql = sprintf("update vegetarian set isget = 1,get_time = NOW() where id = '%s'",intval($id));
        
        if($this->db->query($sql)){
          return true;
        }
        
        return false;
    }

    public function updateRemark($year,$class_no,$term,$course_date,$remark){

      $sql = sprintf("SELECT count(1) cnt FROM remark_14e WHERE COURSE_DATE = %s and YEAR = %s and CLASS_NO = %s and TERM = %s",$this->db->escape(addslashes($course_date)),$this->db->escape(addslashes($year)),$this->db->escape(addslashes($class_no)),$this->db->escape(addslashes($term)));
      
       $classModel = $this->QueryToArray($this->db->query($sql))[0]['cnt'];
       $cnt = $classModel;   

       if($cnt > 0){
            $sql = sprintf("UPDATE remark_14e SET REMARK = %s WHERE COURSE_DATE = %s and YEAR = %s and CLASS_NO = %s and TERM = %s",$this->db->escape(addslashes($remark)),$this->db->escape(addslashes($course_date)),$this->db->escape(addslashes($year)),$this->db->escape(addslashes($class_no)),$this->db->escape(addslashes($term)));

            if ($this->db->query($sql) === false) {
                return false;
            } else {
                return 'OK';
            }
       } else {
            $sql = sprintf("INSERT INTO remark_14e (YEAR,CLASS_NO,TERM,COURSE_DATE,REMARK) VALUES (%s,%s,%s,%s,%s)",$this->db->escape(addslashes($year)),$this->db->escape(addslashes($class_no)),$this->db->escape(addslashes($term)),$this->db->escape(addslashes($course_date)),$this->db->escape(addslashes($remark)));

            if ($this->db->query($sql) === false) {
                return false;
            } else {
                return 'OK';
            }
       }
    }

}

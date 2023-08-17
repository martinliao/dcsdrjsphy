<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

require_once APPPATH . "models/Common_model.php";
class Eat_management_model extends Common_model
{
    public function getEatCountSearch($queryClassNo, $queryClassName, $appDateS, $appDateE, $rows='', $offset='')
    {
        $where = "1=1";
        if ($queryClassNo != ""){
          $where .= " AND A.CLASS_NO LIKE ".$this->db->escape("%".addslashes($queryClassNo)."%");  
        }
        if ($queryClassName != ""){
          $where .= " AND A.CLASS_NAME LIKE ".$this->db->escape("%".addslashes($queryClassName)."%");  
        }
        if ($appDateS != "" && $appDateE == ""){
          $where .= " and date_format(A.USE_DATE,'%Y%m%d') >= date(".$this->db->escape(addslashes($appDateS)).")";  
        }
        if ($appDateS == "" && $appDateE != ""){
          $where .= " and date_format(A.USE_DATE,'%Y%m%d') <= date(".$this->db->escape(addslashes($appDateE)).")";  
        }
        if ($appDateS != "" && $appDateE != ""){
          $where .= " and date_format(A.USE_DATE,'%Y%m%d') between date(".$this->db->escape(addslashes($appDateS)).") and date(".$this->db->escape(addslashes($appDateE)).")";  
        }
      
        $sql = "SELECT  
         E1.CNT AS TCNT1,
         E2.CNT AS TCNT2, 
         E3.CNT AS TCNT3,
         A.id,
         NVL(B.NAME,A.WORKER) AS WORKER_NAME, A.* FROM dining_student A 
         LEFT JOIN view_all_account B ON A.WORKER = B.PERSONAL_ID 
         left join `require` R on R.worker = A.worker 
          and A.year = R.year 
          and A.term = R.term 
          and R.CLASS_NO = A.CLASS_NO
         LEFT JOIN (SELECT YEAR, CLASS_NO, TERM, USE_DATE, COUNT(*) AS CNT FROM dining_teacher WHERE DINING_TYPE = 'A' GROUP BY YEAR, CLASS_NO, TERM, USE_DATE) E1 ON A.YEAR = E1.YEAR AND A.CLASS_NO = E1.CLASS_NO AND A.TERM = E1.TERM AND A.USE_DATE = E1.USE_DATE 
         LEFT JOIN (SELECT YEAR, CLASS_NO, TERM, USE_DATE, COUNT(*) AS CNT FROM dining_teacher WHERE DINING_TYPE = 'B' GROUP BY YEAR, CLASS_NO, TERM, USE_DATE) E2 ON A.YEAR = E2.YEAR AND A.CLASS_NO = E2.CLASS_NO AND A.TERM = E2.TERM AND A.USE_DATE = E2.USE_DATE
         LEFT JOIN (SELECT YEAR, CLASS_NO, TERM, USE_DATE, COUNT(*) AS CNT FROM dining_teacher WHERE DINING_TYPE = 'C' GROUP BY YEAR, CLASS_NO, TERM, USE_DATE) E3 ON A.YEAR = E3.YEAR AND A.CLASS_NO = E3.CLASS_NO AND A.TERM = E3.TERM AND A.USE_DATE = E3.USE_DATE 
       
         WHERE ";

         $this->update_dining_student($where);
       
        $orderby = " ORDER BY A.USE_DATE DESC, A.YEAR DESC, A.CLASS_NO, A.TERM ";

        $limit = "";
        if($rows != "" && $offset != "") {
          $limit = " limit " . $rows . " offset " . $offset;
        }
        else if($rows != "") {
          $limit = " limit " . $rows;
        }

        $sql = $sql . " " . $where . " and IFNULL(R.is_cancel, '0') = '0'  " . $orderby . $limit;
// die($sql);
        $query = $this->db->query($sql);

        return $this->QueryToArray($query);

    }

    public function update_dining_student($where) {
      $user = $this->flags->user["username"];

      $sql = "select id,year,class_no,term from dining_student A WHERE ".$where." ORDER BY A.USE_DATE DESC, A.YEAR DESC, A.CLASS_NO, A.TERM ";
      $data = $this->QueryToArray($this->db->query($sql));

      for($i=0; $i<sizeof($data); $i++) {
        $personals = $this->QueryToArray($this->db->query("select count(*) as count from online_app where year=".$this->db->escape(addslashes($data[$i]["year"]))." and class_no=".$this->db->escape(addslashes($data[$i]["class_no"]))." and term=".$this->db->escape(addslashes($data[$i]["term"]))." and yn_sel in ('8','3','1')"))[0]["count"];

        $sql = "update dining_student set PERSONS_1 = nvl2(PERSONS_1,".$this->db->escape(addslashes($personals)).",null),PERSONS_2 = nvl2(PERSONS_2,".$this->db->escape(addslashes($personals)).",null),PERSONS_3 = nvl2(PERSONS_3,".$this->db->escape(addslashes($personals)).",null), UPD_USER=".$this->db->escape(addslashes($user)).", UPD_DATE=SYSDATE()
        where id = ".$this->db->escape(addslashes($data[$i]['id']))."";

        $this->db->query($sql);

        $sql = "SELECT
        CASE WHEN NEWAMT1 = 0 THEN NULL ELSE NEWAMT1 END AS NEWAMT1,
        CASE WHEN NEWAMT2 = 0 THEN NULL ELSE NEWAMT2 END AS NEWAMT2,
        CASE WHEN NEWAMT3 = 0 THEN NULL ELSE NEWAMT3 END AS NEWAMT3,
        (NEWAMT1 + NEWAMT2 + NEWAMT3) AS NEWTOTAMT, '".$user."', SYSDATE()
        FROM
        (
          SELECT
          CASE WHEN UNIT1 = '1' THEN NEWP1 * AMT1 ELSE (NEWP1/10) * AMT1 END AS NEWAMT1,
          CASE WHEN UNIT2 = '1' THEN NEWP2 * AMT2 ELSE (NEWP2/10) * AMT2 END AS NEWAMT2,
          CASE WHEN UNIT3 = '1' THEN NEWP3 * AMT3 ELSE (NEWP3/10) * AMT3 END AS NEWAMT3
          FROM
          (
            SELECT
            NVL(A.PERSONS_1,0) + NVL(ADD_PERSONS_1,0) + NVL(E1.CNT,0) AS NEWP1, 
            NVL(A.PERSONS_2,0) + NVL(ADD_PERSONS_2,0) + NVL(E2.CNT,0) AS NEWP2, 
            NVL(A.PERSONS_3,0) + NVL(ADD_PERSONS_3,0) + NVL(E3.CNT,0) AS NEWP3, 
            NVL(D.BREAKFAST_TYPE,'1') AS UNIT1, NVL(D.BREAKFAST_MONEY,C1.ADD_VAL1) AS AMT1,
            NVL(D.LUNCH_TYPE,'1')     AS UNIT2, NVL(D.LUNCH_MONEY,C2.ADD_VAL1)     AS AMT2,
            NVL(D.DINNER_TYPE,'1')    AS UNIT3, NVL(D.DINNER_MONEY,C3.ADD_VAL1)    AS AMT3
            FROM dining_student A
            LEFT JOIN code_table C1 ON C1.TYPE_ID = '25' AND C1.ITEM_ID = 'A'
            LEFT JOIN code_table C2 ON C2.TYPE_ID = '25' AND C2.ITEM_ID = 'B'
            LEFT JOIN code_table C3 ON C3.TYPE_ID = '25' AND C3.ITEM_ID = 'C'
            LEFT JOIN dining D ON A.YEAR = D.YEAR AND A.CLASS_NO = D.CLASS_NO AND A.TERM = D.TERM
            LEFT JOIN (SELECT YEAR, CLASS_NO, TERM, USE_DATE, COUNT(*) AS CNT FROM dining_teacher WHERE DINING_TYPE = 'A' GROUP BY YEAR, CLASS_NO, TERM, USE_DATE) E1 ON A.YEAR = E1.YEAR AND A.CLASS_NO = E1.CLASS_NO AND A.TERM = E1.TERM AND A.USE_DATE = E1.USE_DATE
            LEFT JOIN (SELECT YEAR, CLASS_NO, TERM, USE_DATE, COUNT(*) AS CNT FROM dining_teacher WHERE DINING_TYPE = 'B' GROUP BY YEAR, CLASS_NO, TERM, USE_DATE) E2 ON A.YEAR = E2.YEAR AND A.CLASS_NO = E2.CLASS_NO AND A.TERM = E2.TERM AND A.USE_DATE = E2.USE_DATE
            LEFT JOIN (SELECT YEAR, CLASS_NO, TERM, USE_DATE, COUNT(*) AS CNT FROM dining_teacher WHERE DINING_TYPE = 'C' GROUP BY YEAR, CLASS_NO, TERM, USE_DATE) E3 ON A.YEAR = E3.YEAR AND A.CLASS_NO = E3.CLASS_NO AND A.TERM = E3.TERM AND A.USE_DATE = E3.USE_DATE
            WHERE A.id = ".$this->db->escape(addslashes($data[$i]['id']))."
          ) g
        ) h";

        $datas = $this->QueryToArray($this->db->query($sql));

        if(!isset($datas[0]["NEWAMT1"])) {
          $datas[0]["NEWAMT1"] = 'null';
        }
        if(!isset($datas[0]["NEWAMT2"])) {
          $datas[0]["NEWAMT2"] = 'null';
        }
        if(!isset($datas[0]["NEWAMT3"])) {
          $datas[0]["NEWAMT3"] = 'null';
        }
        if(!isset($datas[0]["NEWTOTAMT"])) {
          $datas[0]["NEWTOTAMT"] = 'null';
        }

        $sql = "UPDATE dining_student SET 
        amount_1 = ".$this->db->escape(addslashes($datas[0]["NEWAMT1"])).", 
        amount_2 = ".$this->db->escape(addslashes($datas[0]["NEWAMT2"])).", 
        amount_3 = ".$this->db->escape(addslashes($datas[0]["NEWAMT3"])).", 
        total_amount = ".$this->db->escape(addslashes($datas[0]["NEWTOTAMT"])).", 
        upd_user = ".$this->db->escape(addslashes($datas[0][$user])).", 
        upd_date = ".$this->db->escape(addslashes($datas[0]["SYSDATE()"]))."
        WHERE id = ".$this->db->escape(addslashes($data[$i]['id']))."";

        $query = $this->db->query($sql);
      }
    }

    public function getListCount($attrs=array())
    {
        $params = array(
            'conditions' => $attrs['conditions'],
        );

        if (isset($attrs['q'])) {
            $params['q'] = $attrs['q'];
        }
        $data = $this->getList($params);
        return count($data);
    }

    public function deleteDining_student($id){
      $sql = "delete FROM  dining_student where id = ".$this->db->escape(addslashes($id))."";   
      $query = $this->db->query($sql);
      if($query)
        return [true];
      else
        return [false];
    }

    public function selectDetailSql($id){
      $sql = "SELECT " .
      "E1.TEACHER_CNT1, E2.TEACHER_CNT2, E3.TEACHER_CNT3, " . 
      "NVL(D.BREAKFAST_TYPE,'1') AS TYPE1, NVL(D.BREAKFAST_MONEY,C1.ADD_VAL1) AS AMT1, " .
      "NVL(D.LUNCH_TYPE,'1') AS TYPE2, NVL(D.LUNCH_MONEY,C2.ADD_VAL1) AS AMT2, " .
      "NVL(D.DINNER_TYPE,'1') AS TYPE3, NVL(D.DINNER_MONEY,C3.ADD_VAL1) AS AMT3, " .
      "NVL(B.NAME,A.WORKER) AS WORKER_NAME, " .
      "NVL(A.PERSONS_1,0) + NVL(E1.TEACHER_CNT1,0) + NVL(A.ADD_PERSONS_1,0) + " . 
      "NVL(A.PERSONS_2,0) + NVL(E2.TEACHER_CNT2,0) + NVL(A.ADD_PERSONS_2,0) + " . 
      "NVL(A.PERSONS_3,0) + NVL(E3.TEACHER_CNT3,0) + NVL(A.ADD_PERSONS_3,0) AS TOT_PERSON, " .  
      "A.* FROM dining_student A " .
      "LEFT JOIN view_all_account B ON A.WORKER = B.USERNAME " .
      "LEFT JOIN code_table C1 ON C1.TYPE_ID = '25' AND C1.ITEM_ID = 'A' " .
      "LEFT JOIN code_table C2 ON C2.TYPE_ID = '25' AND C2.ITEM_ID = 'B' " .
      "LEFT JOIN code_table C3 ON C3.TYPE_ID = '25' AND C3.ITEM_ID = 'C' " .
      "LEFT JOIN dining D ON A.YEAR = D.YEAR AND A.CLASS_NO = D.CLASS_NO AND A.TERM = D.TERM " .
      "LEFT JOIN (SELECT YEAR, CLASS_NO, TERM, USE_DATE, COUNT(*) AS TEACHER_CNT1 FROM dining_teacher WHERE DINING_TYPE = 'A' GROUP BY YEAR, CLASS_NO, TERM, USE_DATE) E1 ON A.YEAR = E1.YEAR AND A.CLASS_NO = E1.CLASS_NO AND A.TERM = E1.TERM AND A.USE_DATE = E1.USE_DATE " . 
      "LEFT JOIN (SELECT YEAR, CLASS_NO, TERM, USE_DATE, COUNT(*) AS TEACHER_CNT2 FROM dining_teacher WHERE DINING_TYPE = 'B' GROUP BY YEAR, CLASS_NO, TERM, USE_DATE) E2 ON A.YEAR = E2.YEAR AND A.CLASS_NO = E2.CLASS_NO AND A.TERM = E2.TERM AND A.USE_DATE = E2.USE_DATE " .
      "LEFT JOIN (SELECT YEAR, CLASS_NO, TERM, USE_DATE, COUNT(*) AS TEACHER_CNT3 FROM dining_teacher WHERE DINING_TYPE = 'C' GROUP BY YEAR, CLASS_NO, TERM, USE_DATE) E3 ON A.YEAR = E3.YEAR AND A.CLASS_NO = E3.CLASS_NO AND A.TERM = E3.TERM AND A.USE_DATE = E3.USE_DATE " .
      "WHERE A.id = ".$this->db->escape(addslashes($id))."";
      $query = $this->db->query($sql);
      $result = $this->QueryToArray($query);
      return $result;
    }

    public function updateDetailSql($id,$setAddPeople1,$setAddPeople2,$setAddPeople3,$setMemo,$acc){
        $id = $this->db->escape(addslashes($id));
        $setAddPeople1 = $this->db->escape(addslashes($setAddPeople1));
        $setAddPeople2 = $this->db->escape(addslashes($setAddPeople2));
        $setAddPeople3 = $this->db->escape(addslashes($setAddPeople3));
        $setMemo = $this->db->escape(addslashes($setMemo));
        $acc = $this->db->escape(addslashes($acc));

      $sql = "update dining_student set " . 
      "ADD_PERSONS_1 = ".$this->db->escape(addslashes($setAddPeople1)).", " .
      "ADD_PERSONS_2 = ".$this->db->escape(addslashes($setAddPeople2)).", " .
      "ADD_PERSONS_3 = ".$this->db->escape(addslashes($setAddPeople3)).", " .
      "MEMO          = ".$this->db->escape(addslashes($setMemo)).", " .
      "upd_user = ".$this->db->escape(addslashes($acc)).", " .
      "upd_date = now() " .
      "where id = ".$this->db->escape(addslashes($id));
      $query = $this->db->query($sql);

      $sql = "UPDATE dining_student 
      left join
      ( SELECT id,CASE WHEN NEWAMT1 = 0 THEN NULL ELSE NEWAMT1 END AS NEWAMT1, CASE WHEN NEWAMT2 = 0 THEN NULL ELSE NEWAMT2 END AS NEWAMT2
      , CASE WHEN NEWAMT3 = 0 THEN NULL ELSE NEWAMT3 END AS NEWAMT3, (NEWAMT1 + NEWAMT2 + NEWAMT3) AS NEWTOTAMT 
      FROM ( SELECT id,CASE WHEN UNIT1 = '1' THEN NEWP1 * AMT1 ELSE (NEWP1/10) * AMT1 END AS NEWAMT1
      , CASE WHEN UNIT2 = '1' THEN NEWP2 * AMT2 ELSE (NEWP2/10) * AMT2 END AS NEWAMT2
      , CASE WHEN UNIT3 = '1' THEN NEWP3 * AMT3 ELSE (NEWP3/10) * AMT3 END AS NEWAMT3 
      FROM ( SELECT id,NVL(A.PERSONS_1,0) + NVL(ADD_PERSONS_1,0) + NVL(E1.CNT,0) AS NEWP1
      , NVL(A.PERSONS_2,0) + NVL(ADD_PERSONS_2,0) + NVL(E2.CNT,0) AS NEWP2
      , NVL(A.PERSONS_3,0) + NVL(ADD_PERSONS_3,0) + NVL(E3.CNT,0) AS NEWP3, NVL(D.BREAKFAST_TYPE,'1') AS UNIT1
      , NVL(D.BREAKFAST_MONEY,C1.ADD_VAL1) AS AMT1, NVL(D.LUNCH_TYPE,'1') AS UNIT2, NVL(D.LUNCH_MONEY,C2.ADD_VAL1) AS AMT2
      , NVL(D.DINNER_TYPE,'1') AS UNIT3, NVL(D.DINNER_MONEY,C3.ADD_VAL1) AS AMT3 FROM dining_student A 
      LEFT JOIN code_table C1 ON C1.TYPE_ID = '25' AND C1.ITEM_ID = 'A' 
      LEFT JOIN code_table C2 ON C2.TYPE_ID = '25' AND C2.ITEM_ID = 'B' 
      LEFT JOIN code_table C3 ON C3.TYPE_ID = '25' AND C3.ITEM_ID = 'C' 
      LEFT JOIN dining D ON A.YEAR = D.YEAR AND A.CLASS_NO = D.CLASS_NO AND A.TERM = D.TERM 
      LEFT JOIN (SELECT YEAR, CLASS_NO, TERM, USE_DATE, COUNT(*) AS CNT FROM dining_teacher 
      WHERE DINING_TYPE = 'A' GROUP BY YEAR, CLASS_NO, TERM, USE_DATE) E1 ON A.YEAR = E1.YEAR 
      AND A.CLASS_NO = E1.CLASS_NO AND A.TERM = E1.TERM AND A.USE_DATE = E1.USE_DATE 
      LEFT JOIN (SELECT YEAR, CLASS_NO, TERM, USE_DATE, COUNT(*) AS CNT FROM dining_teacher WHERE DINING_TYPE = 'B' 
      GROUP BY YEAR, CLASS_NO, TERM, USE_DATE) E2 ON A.YEAR = E2.YEAR AND A.CLASS_NO = E2.CLASS_NO AND A.TERM = E2.TERM 
      AND A.USE_DATE = E2.USE_DATE 
      LEFT JOIN (SELECT YEAR, CLASS_NO, TERM, USE_DATE, COUNT(*) AS CNT FROM dining_teacher WHERE DINING_TYPE = 'C' GROUP BY YEAR
      , CLASS_NO, TERM, USE_DATE) E3 ON A.YEAR = E3.YEAR AND A.CLASS_NO = E3.CLASS_NO 
      AND A.TERM = E3.TERM AND A.USE_DATE = E3.USE_DATE WHERE A.id = '".$id."' ) as zz ) as yy  ) as xx
       on dining_student.id=xx.id
      
       set 
      dining_student.AMOUNT_1=xx.NEWAMT1,
      dining_student.AMOUNT_2=xx.NEWAMT2,
      dining_student.AMOUNT_3=xx.NEWAMT3,
      dining_student.TOTAL_AMOUNT=xx.NEWTOTAMT  
      
       WHERE dining_student.id = ".$this->db->escape(addslashes($id))."
      ";
      $query = $this->db->query($sql);


      

      if($query)
        return [true];
      else
        return [false];
    }
    
}

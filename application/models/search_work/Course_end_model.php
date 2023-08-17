<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

require_once APPPATH . "models/Common_model.php";
class Course_end_model extends Common_model
{
    public function getCourseEndData($year, $start_date, $end_date, $type)
    {
        $where = "";
        $orderby = "";
        $sql = "	SELECT
        t.term, t.year, t.class_name, (IFNULL(t.range_real,0)+IFNULL(t.range_internet,0)) as range_sum, DATE_FORMAT(t.CLASSENDDATE,'%Y/%m/%d') as CLASSENDDATE,
        DATE_FORMAT(t.start_date1,'%Y/%m/%d') as start_date1,DATE_FORMAT(DATE_ADD(t.start_date1, INTERVAL 7 DAY),'%Y/%m/%d') as start_date2,
         DATE_FORMAT(t.end_date1,'%Y/%m/%d') as end_date1, DATE_FORMAT(DATE_ADD(t.end_date1, INTERVAL 7 DAY),'%Y/%m/%d') as end_date2,
        V.name as FIRST_NAME, '' as LAST_NAME,(
      SELECT
      IFNULL (ADD_VAL2, ADD_VAL1)
      FROM
          code_table CT
      WHERE
          CT.type_id = '26'
      AND CT.DESCRIPTION = V.name
  limit 1
  ) tel
    FROM `require` t
    LEFT JOIN BS_user V
        ON t.WORKER=V.idno";

        $where = "where t.class_no is not null and t.class_name is not null and t.year=".$this->db->escape(addslashes($year))."
         and t.end_date1>=".$this->db->escape(addslashes($start_date))." and t.end_date1<=".$this->db->escape(addslashes($end_date))."
         and t.class_status IN ('2', '3') AND IFNULL(t.is_cancel, '0') = '0'  AND t.type not in ('C','O') ";
        $orderby = " ORDER BY V.NAME,t.class_name,t.term asc ";

        $sql = $sql . " " . $where . " " . $orderby;

        $query = $this->db->query($sql);

        return $this->QueryToArray($query);

    }

    public function exportCourseEndData($year, $start_date, $end_date)
    {
        $where = "";
        $orderby = "";
        $sql = "	SELECT
        t.term, t.year, t.class_name, (IFNULL(t.range_real,0)+IFNULL(t.range_internet,0)) as range_sum, DATE_FORMAT(t.CLASSENDDATE,'%Y/%m/%d') as CLASSENDDATE,
        DATE_FORMAT(t.start_date1,'%Y/%m/%d') as start_date1,DATE_FORMAT(DATE_ADD(t.start_date1, INTERVAL 7 DAY),'%Y/%m/%d') as start_date2,
         DATE_FORMAT(t.end_date1,'%Y/%m/%d') as end_date1, DATE_FORMAT(DATE_ADD(t.end_date1, INTERVAL 7 DAY),'%Y/%m/%d') as end_date2,
        V.name as FIRST_NAME, '' as LAST_NAME,(
      SELECT
      IFNULL (ADD_VAL2, ADD_VAL1)
      FROM
          code_table CT
      WHERE
          CT.type_id = '26'
      AND CT.DESCRIPTION = V.name
  limit 1
  ) tel
    FROM `require` t
    LEFT JOIN BS_user V
        ON t.WORKER=V.idno";

        $where = "where t.class_no is not null and t.class_name is not null and t.year=".$this->db->escape(addslashes($year))."
         and t.end_date1>=".$this->db->escape(addslashes($start_date))." and t.end_date1<=".$this->db->escape(addslashes($end_date))."
         and t.class_status IN ('2', '3') AND IFNULL(t.is_cancel, '0') = '0'  AND t.type not in ('C','O') ";
        $orderby = " ORDER BY V.NAME,t.class_name,t.term asc ";

        $sql = $sql . " " . $where . " " . $orderby;

        $query = $this->db->query($sql);

        $rs = $this->QueryToArray($query);

        $filename = 'Export_Monthly_END_Schedule_Cases.csv';
        header("Content-type: application/csv");    //header抬頭設定
        header("Content-Disposition: attachment; filename=Export_Monthly_END_Schedule_Cases.csv");
        
        echo iconv('UTF-8', 'BIG5', "編號, \t");
        echo iconv('UTF-8', 'BIG5', "班期名稱, \t");
        echo iconv('UTF-8', 'BIG5', "期別, \t");
        echo iconv('UTF-8', 'BIG5', "研習時數, \t");
        echo iconv('UTF-8', 'BIG5', "承辦人, \t");
        echo iconv('UTF-8', 'BIG5', "開課起迄日, \t");
        echo iconv('UTF-8', 'BIG5', "應完成日, \t");
        echo iconv('UTF-8', 'BIG5', "實際完成日, \n");
        $i = 0;

        for($i=0;$i < sizeof($rs); $i++){
          echo iconv('UTF-8', 'BIG5', ($i+1). ", \t");
          echo iconv('UTF-8', 'BIG5', $rs[$i]['class_name']. ", \t");
          echo iconv('UTF-8', 'BIG5', $rs[$i]['term']. ", \t");
          echo iconv('UTF-8', 'BIG5', $rs[$i]['range_sum']. ", \t");
          echo iconv('UTF-8', 'BIG5', $rs[$i]['FIRST_NAME']. ", \t");
          echo iconv('UTF-8', 'BIG5', $rs[$i]['start_date1']. " - " . $rs[$i]['end_date1'] . ", \t");
          echo iconv('UTF-8', 'BIG5', $rs[$i]['start_date2']. " - " . $rs[$i]['end_date2'] . ", \t");
          echo iconv('UTF-8', 'BIG5', $rs[$i]['CLASSENDDATE']. ", \n");

        }

    }

}

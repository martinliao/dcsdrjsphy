<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

require_once APPPATH . "models/Common_model.php";
class Lecture_money_search_model extends Common_model
{
    public function getLectureMoneySearchData($teacher, $id, $start, $end, $perpage, $rows="", $offset="")
    {
        $rows = intVal($rows);
        $offset = intVal($offset);

        $where = "1=1";

        if ($teacher != ""){
        $where .= " AND TEACHER_NAME LIKE ".$this->db->escape("%".addslashes($teacher)."%")."";  
        }
        if ($id != ""){
        $where .= " AND TEACHER_ID LIKE ".$this->db->escape("%".addslashes($id."%"))."";  
        }
        if ($start != "" && $end == ""){
        $where .= " AND USE_DATE >= ".$this->db->escape(addslashes($start))."";  
        }
        if ($start == "" && $end != ""){
        $where .= " AND USE_DATE <= ".$this->db->escape(addslashes($end))." ";  
        }
        if ($start != "" && $end != ""){
        $where .= " AND USE_DATE between ".$this->db->escape(addslashes($start))." and ".$this->db->escape(addslashes($end))."";  
        }

        $where .= " and h.status is not null and IFNULL(r.is_cancel, '0') = '0'  ";

        // custom (b) by chiahua 排序
        // if($OrderBy != '' && $SortBy != ''){
        //     $subSQL = " order by {$OrderBy} {$SortBy}";
        // }
        // custom (e) by chiahua 排序

        $sql = "SELECT h.* FROM hour_traffic_tax h join `require` r on h.year=r.year and h.term=r.term and h.class_no=r.class_no " .
            "WHERE " . $where . " ORDER BY h.YEAR DESC, h.CLASS_NO, h.TERM, h.USE_DATE DESC";
        //echo "sql:".$sql;
        $sqlC = "select count(*) from ({$sql})a";       
        //$rsAll = db_excute($sql);
        //$total_query_records = $rsAll->RecordCount();
        
        $total_query_records = $this->db->query($sqlC)->num_rows();
        //$total_query_records=6;
        $page_size = 15;
        $total_page = ceil($total_query_records / $page_size);
        $cur_page   = ($perpage == '') ? 1 : intval($perpage);
        $rdsBegIdx = 1;
        $rdsEndIdx = $page_size;
        if ($cur_page <= 0)
        {
            $cur_page = 0;
            $rdsEndIdx = $total_query_records;
        }
        else if ($cur_page > $total_page)
        {
            $cur_page = 1;
            $rdsBegIdx = (($cur_page-1)*$page_size) ;
            $rdsEndIdx = $page_size;
        }
        else if ($cur_page <= $total_page)
        {
            $rdsBegIdx = (($cur_page-1)*$page_size ) +1 ;
            $rdsEndIdx = $cur_page*$page_size;		
        }
        if ($total_query_records==0){
        $cur_page = 0;
        }

        $p1 = (($cur_page==1) || ($cur_page==0)) ? "disabled" : 'onclick="page(-1)"';
        $p2 = (($cur_page==1) || ($cur_page==0)) ? "disabled" : 'onclick="page(-2)"';
        $p3 = (($cur_page==$total_page) || ($cur_page==0)) ? "disabled" : 'onclick="page(-3)"';
        $p4 = (($cur_page==$total_page) || ($cur_page==0)) ? "disabled" : 'onclick="page(-4)"';

        // $sql = "SELECT * FROM (SELECT @rownum := @rownum + 1 AS KEYNO, Z.* FROM (" . $sql . ") Z )b WHERE KEYNO between " . $rdsBegIdx . " AND " . $rdsEndIdx;  
        $sql = "SELECT h.*, t.rpno FROM hour_traffic_tax h join `require` r on h.year=r.year and h.term=r.term and h.class_no=r.class_no 
        left join teacher t on h.teacher_name=t.name  and h.isteacher=t.teacher and h.teacher_id and t.idno
        where ". $where." and h.status is not null 
        and IFNULL(r.is_cancel, '0') = '0'  ORDER BY h.YEAR DESC, h.CLASS_NO, h.TERM, h.USE_DATE DESC ";
        //echo "sql:".$sql;

        $limit = "";
        if($rows != "" && $offset != "") {
          $limit = " limit " . intVal($rows) . " offset " . intVal($offset);
        }
        else if($rows != "") {
          $limit = " limit " . intVal($rows);
        }

        $sql = $sql . " " . $limit;

        $rs = $this->QueryToArray($this->db->query($sql));

        

        // custom by chiahua 計算總計
        $sql1 = "SELECT sum(HOUR_FEE) as TT_HOUR_FEE, sum(CASE WHEN TRAFFIC_FEE < 0 THEN 0 ELSE TRAFFIC_FEE END) as TT_TRAFFIC_FEE, sum(SUBTOTAL) as TT_SUBTOTAL,sum(TAX) as TT_TAX,sum(AFTERTAX) as TT_AFTERTAX 
                FROM hour_traffic_tax h join `require` r on h.year=r.year and h.term=r.term and h.class_no=r.class_no " .
            "WHERE " . $where ;

        
        //echo "sql:".$sql1;
        $rs1 = $this->db->query($sql1);
        if($rs1 && $rs){
            $row1 = $this->QueryToArray($rs1)[0];
            $rs[0]["TT_HOUR_FEE"] = $row1['TT_HOUR_FEE'];
            $rs[0]["TT_TRAFFIC_FEE"] = $row1['TT_TRAFFIC_FEE'];
            //$TT_HOUTT_SUBTOTALR_FEE = $row1['TT_SUBTOTAL'];
            $rs[0]["TT_HOUTT_SUBTOTALR_FEE"] = $row1['TT_HOUR_FEE'] + $row1['TT_TRAFFIC_FEE'];
            $rs[0]["TT_TAX"] = $row1['TT_TAX'];
            $rs[0]["TT_AFTERTAX"] = $row1['TT_HOUR_FEE'] + $row1['TT_TRAFFIC_FEE'] - $row1['TT_TAX'];
        }

        
        
        //$query = $this->db->query($sql);

        return $rs;

    }

    public function getTeacherData($teacher_id)
    {
        //$sql = "SELECT teacher.*,bank_code.name as bank_name FROM teacher left outer join bank_code on teacher.bank_code=bank_code.item_id WHERE idno='".$teacher_id."'";  
        // $sql = "SELECT T.*,CC.CITY_NAME,CS.SUBCITY_NAME,C.DESCRIPTION FROM TEACHER T 
        // LEFT JOIN CODE_TABLE C ON C.TYPE_ID='14' AND C.ITEM_ID=T.BANKID 
        // LEFT JOIN co_city CC on T.CITY=CC.CITY
        // LEFT JOIN co_subcity CS on T.SUBCITY=CS.SUBCITY
        // WHERE T.idno = '{$teacher_id}'";
        $sql = "SELECT T.*,CC.CITY_NAME,CS.SUBCITY_NAME,C.DESCRIPTION FROM teacher T 
        LEFT JOIN code_table C ON C.TYPE_ID='14' AND C.ITEM_ID=T.bank_code 
        LEFT JOIN co_city CC on T.county=CC.CITY 
        LEFT JOIN co_subcity CS on T.district=CS.SUBCITY 
        WHERE T.idno = ".$this->db->escape(addslashes($teacher_id))."";
        
        $rs = $this->db->query($sql);

        return $this->QueryToArray($rs);

    }

    public function getHourFeeTax($teacher, $id, $start, $end, $perpage, $rows="", $offset="")
    {
        $rows = intVal($rows);
        $offset = intVal($offset);

        $where = "1=1";

        if ($teacher != ""){
            $where .= " AND hour_traffic_tax.TEACHER_NAME LIKE ".$this->db->escape("%".addslashes($teacher)."%")."";  
        }
        if ($id != ""){
            $where .= " AND hour_traffic_tax.TEACHER_ID LIKE ".$this->db->escape("%".addslashes($id."%"))."";  
        }
        if ($start != "" && $end == ""){
            $where .= " AND hour_traffic_tax.USE_DATE >= ".$this->db->escape(addslashes($start))."";  
        }
        if ($start == "" && $end != ""){
            $where .= " AND hour_traffic_tax.USE_DATE <= ".$this->db->escape(addslashes($end))." ";  
        }
        if ($start != "" && $end != ""){
            $where .= " AND hour_traffic_tax.USE_DATE between ".$this->db->escape(addslashes($start))." and ".$this->db->escape(addslashes($end))."";  
        }

        $where .= " and entry_date is not null and (hour_traffic_tax.error_info is not null or is_receive = 0)";

        $sql = sprintf("SELECT
                            hour_traffic_tax.seq,
                            hour_traffic_tax.class_name,
                            hour_traffic_tax.term,
                            hour_traffic_tax.teacher_name,
                            date_format(hour_traffic_tax.use_date,'%%Y-%%m-%%d') as use_date,
                            hour_traffic_tax.error_info,
                            hour_traffic_tax.is_receive,
                            teacher.email 
                        FROM
                            hour_traffic_tax
                            JOIN teacher ON hour_traffic_tax.teacher_id = teacher.idno 
                        WHERE %s
                        order by hour_traffic_tax.year,hour_traffic_tax.class_name,hour_traffic_tax.term,hour_traffic_tax.use_date",$where);

        $limit = "";
        if($rows != "" && $offset != "") {
          $limit = " limit " . intVal($rows) . " offset " . intVal($offset);
        }
        else if($rows != "") {
          $limit = " limit " . intVal($rows);
        }

        $sql = $sql . " " . $limit;

        $rs = $this->QueryToArray($this->db->query($sql));

        return $rs;
    }

    public function getHourFeeMailDetail($seq){
        $sql = sprintf("SELECT
                            hour_traffic_tax.`year`,
                            hour_traffic_tax.class_name,
                            hour_traffic_tax.term,
                            hour_traffic_tax.subtotal,
                            date_format(hour_traffic_tax.use_date,'%%Y-%%m-%%d') as use_date,
                            date_format(hour_traffic_tax.entry_date,'%%Y-%%m-%%d') as entry_date,
                            teacher.email 
                        FROM
                            hour_traffic_tax
                            JOIN teacher ON hour_traffic_tax.teacher_id = teacher.idno 
                        WHERE
                            hour_traffic_tax.seq = '%s' and entry_date is not null",intval($seq));

        $query = $this->db->query($sql);
        $result = $query->result_array();

        return $result;
    }

    public function updateHourFeeMailErronInfo($seq, $error_info){
        $this->db->set('error_info', $error_info);
        $this->db->where('seq', intval($seq));

        if($this->db->update('hour_traffic_tax')){
            return true;
        }

        return false;
    }

}

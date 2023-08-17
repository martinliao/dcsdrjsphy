<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

require_once APPPATH . "models/Common_model.php";
class Teach_pay_list2_model extends Common_model
{
    public function getTeacherPayList2($d1, $d2,$accout, $rows="", $offset="")
    {
        // $personData =  $this->getPersonId($accout);
        $where="";
        $sql = "select T.*, A.name as 
        WORKER_NAME from hour_traffic_tax  T left join BS_user A on 
        T.WORKER_ID = A.idno where T.TEACHER_NAME NOT IN ('教務組') and T.USE_DATE is not null 
        " . ($d1 ? " and T.USE_DATE  >= ".$this->db->escape(addslashes($d1))."" : "") . 
        ($d2 ? " and T.USE_DATE <= ".$this->db->escape(addslashes($d2))."" : "") .
        " {$where} order by T.YEAR, T.CLASS_NO, T.TERM, T.USE_DATE desc";

        $limit = "";
        if($rows != "" && $offset != "") {
          $limit = " limit " . intVal($rows) . " offset " . intVal($offset);
        }
        else if($rows != "") {
          $limit = " limit " . intVal($rows);
        }

        $sql = $sql . " " . $limit;
        
        $query = $this->db->query($sql);

        return $this->QueryToArray($query);

    }

    public function getPersonId($accout){
        $sql = "select idno from BS_user where username=".$this->db->escape(addslashes($accout))."";
        
        $query = $this->db->query($sql);

        return $this->QueryToArray($query);
    }



}

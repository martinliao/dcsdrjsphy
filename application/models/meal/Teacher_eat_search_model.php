<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

require_once APPPATH . "models/Common_model.php";
class Teacher_eat_search_model extends Common_model
{
    public function geTeacherEatSearch($queryClassNo, $queryClassName, $appDateS,$appDateE, $rows="", $offset="")
    {
        $where = "1=1";
        if ($queryClassNo != ""){
          $where .= " AND A.CLASS_NO LIKE ".$this->db->escape("%".addslashes($queryClassNo)."%")."";  
        }
        if ($queryClassName != ""){
          $where .= " AND A.CLASS_NAME LIKE ".$this->db->escape("%".addslashes($queryClassName)."%")."";  
        }
        if ($appDateS != "" && $appDateE == ""){
          $where .= " and A.USE_DATE >= ".$this->db->escape(addslashes($appDateS))."";  
        }
        if ($appDateS == "" && $appDateE != ""){
          $where .= " and A.USE_DATE <= ".$this->db->escape(addslashes($appDateE))."";  
        }
        if ($appDateS != "" && $appDateE != ""){
          $where .= " and A.USE_DATE between ".$this->db->escape(addslashes($appDateS))." and ".$this->db->escape(addslashes($appDateE))."";  
        }
      
        $orderby = "";
        $sql = "SELECT  C.DESCRIPTION AS DINING_TYPE_NAME, A.* FROM dining_teacher A 
         LEFT JOIN code_table C ON A.DINING_TYPE = C.ITEM_ID AND C.TYPE_ID = '25' 
         
         WHERE  ";

       
        $orderby = " ORDER BY A.USE_DATE DESC, A.YEAR DESC, A.CLASS_NO, A.TERM, A.DINING_TYPE, A.ID, A.TYPE ";

        $limit = "";
        if($rows != "" && $offset != "") {
          $limit = " limit " . intVal($rows) . " offset " . intVal($offset);
        }
        else if($rows != "") {
          $limit = " limit " . intVal($rows);
        }

        $sql = $sql . " " . $where . " " . $orderby . " " . $limit;

        $query = $this->db->query($sql);

        return $this->QueryToArray($query);

    }

}

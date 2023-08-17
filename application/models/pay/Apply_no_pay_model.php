<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

require_once APPPATH . "models/Common_model.php";
class Apply_no_pay_model extends Common_model
{
    public function getApplyNoPaySearch($year, $month, $rows="", $offset="")
    {
    	$where = "";
        if ($year != "" && $month != ""){
        	$y = $year + 1911;
			$start = $y.'-'.$month.'-01 00:00:00';

			if($month == 12){
				$end = ($y+1).'-01-01 00:00:00';
			} else {
				$end = $y.'-'.($month+1).'-01 00:00:00';
			}
          
          	$where = "AND use_date >= date(".$this->db->escape(addslashes($start)).")
					AND use_date < date(".$this->db->escape(addslashes($end)).")";
        }
        
      
        $sql = "SELECT
	                YEAR,
	                TERM,
	                CLASS_NAME,
	                TEACHER_NAME,
	                UNIT_HOUR_FEE,
	                HOUR_FEE,
	                TRAFFIC_FEE,
	                SUBTOTAL,
	                USE_DATE,
	                HRS
		        FROM
		            hour_traffic_tax
		        WHERE
                	status = '已設定為不請款' ";

       
        $orderby = "";

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

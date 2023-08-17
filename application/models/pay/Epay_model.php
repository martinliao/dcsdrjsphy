<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

require_once APPPATH . "models/Common_model.php";
class Epay_model extends Common_model
{
    public function getEpayData($d1, $d2, $rows="", $offset="")
    {
    	$where = "bill_date between ".$this->db->escape(addslashes($d1))." and ".$this->db->escape(addslashes($d2))."";
        $sql = "select distinct bill_date from hour_bill where bill_date is not null and {$where} order by bill_date";

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

    public function getEpayDetail($bldt) {
        
    	$sql = "select id as `key`, b.description as bankname, a.* from hour_bill a " .
        "left join code_table b on a.teacher_bank_id = b.item_id and b.type_id = '14' " .  
        "where a.bill_date = ".$this->db->escape(addslashes($bldt))." " .
        "order by a.teacher_name asc";
        // "order by nlssort(a.teacher_name, 'NLS_SORT=TCHINESE_STROKE_M') asc";

        $query = $this->db->query($sql);

        $datas =  $this->QueryToArray($query);
        for($i = 0 ; $i < sizeof($datas); $i++){
            $redata = $this->getSomeSqlCount($datas[$i]['bill_date'],$datas[$i]['teacher_id']);
            $datas[$i]['cnt'] = $redata[0]['cnt'];
            $TRAFFIC_FEE = ($datas[$i]['traffic_fee'] < 0 ? 0 : $datas[$i]['traffic_fee']);
            $AFTERTAX = $datas[$i]['hour_fee'] - ($datas[$i]['hour_fee'] * $datas[$i]['tax_rate']) + $TRAFFIC_FEE-($datas[$i]['hour_fee'] * $datas[$i]['h_tax_rate']);
            $this->afterTax($AFTERTAX,$datas[$i]['key']);
            $datas[$i]['aftertax'] = $AFTERTAX;
        }

        return $datas;
    }

    public function getSomeSqlCount($bdat,$tid){
        $sql = "select count(*) cnt from is_2htax where bill_date='{$bdat}' and teacher_id='{$tid}'";

        $query = $this->db->query($sql);

        return  $this->QueryToArray($query);
    }
    public function afterTax($AFTERTAX,$id){
        $sql = "UPDATE hour_bill SET AFTERTAX = {$AFTERTAX} WHERE id = '{$id}'";
        $query = $this->db->query($sql);
    }

    public function getprintData($date){
        $sql ="select id as `key`, b.description as bankname, a.* from hour_bill a 
        left join code_table b on a.teacher_bank_id = b.item_id and b.type_id = '14' 
        where a.bill_date = ".$this->db->escape(addslashes($date))." 
        order by a.aftertax  asc";
        $query = $this->db->query($sql);
        return $this->QueryToArray($query);
    }
}

<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Common_model extends MY_Model
{

     public function QueryToArray($query){
        $arrAll = array();
		if($query->num_rows() > 0)
		{
			foreach($query->result_array() as $row)
			{
				array_push($arrAll,$row);
			}
		}
		return $arrAll;
     }

     public function getDayOfWeek(){
         $dayOfWeekArr = array("",'日','一','二','三','四','五','六');
         return $dayOfWeekArr;
     }

     public function getYears(){
         
        $yearArr = array();
		for($i = 106 ; $i < 109 ; $i++){
            array_push($yearArr,$i);
        }
		return $yearArr;

     }

     public function sqlWhere($where,$col,$data){
         if($where == ""){
             $where = " Where ". $col . " = " . $data;
         }
         else{
             $where = $where. " And ". $col . " = " . $data;
         }
         return $where;
     }

     public function getDataRange($year,$type,$season,$startMonth,$endMonth){
        $dataArr = array(); 
        if($type == 1){
            $dataArr = $this->getSeasonRange($year,$season);
        }
        else if($type == 2){
            $dataArr = array($this->getMonthDate(1,$year,$startMonth==null?$endMonth:$startMonth)
            ,$this->getMonthDate(2,$year,$endMonth==null?$startMonth:$endMonth));
        }
        return $dataArr;
     }

     public function getSeasonRange($year,$season){
        $seasonarnge =array() ;
        $cyear = $year + 1911;
        if($season == 1){
            $seasonarnge = array($cyear."-"."01-"."01",$cyear."-"."03-"."31"); 
         }
         else if($season == 2){
            $seasonarnge = array($cyear."-"."04-"."01",$cyear."-"."06-"."30"); 
         }
         else if($season == 3){
            $seasonarnge = array($cyear."-"."07-"."01",$cyear."-"."09-"."31"); 
         }
         else if($season == 4){
            $seasonarnge = array($cyear."-"."10-"."01",$cyear."-"."12-"."31"); 
         }
         else {
            $seasonarnge = array($cyear."-"."01-"."01",$cyear."-"."12-"."31"); 
         }
         return $seasonarnge;
     }

     public function getMonthDate($SorE,$year,$month){
        $cyear = $year + 1911;
        $cdate = $cyear . "-".$month."-"."01";
        if($SorE == 2){
            $cdate = date("Y-m-t", strtotime($cdate));
        }
        else{
            $cdate = date("Y-m-01", strtotime($cdate));
        }
        return $cdate;
     }

     public function getOneYear($year){
        $cyear = $year + 1911;
        $oneYear=array($cyear."-"."01-"."01",$cyear."-"."12-"."31");
        return $oneYear;

     }

}
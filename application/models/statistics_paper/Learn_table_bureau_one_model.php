<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once(APPPATH."models/Common_model.php");
class Learn_table_bureau_one_model extends Common_model
{
     public function getDownLoadExcel($year,$bureau_id,$account,$range){
        $personalInfo =  $this->getPersonalInfo($account);
        
        // if(true){
        if(sizeof($personalInfo) == 2 && $personalInfo[0]!=""){
            $role = $this->getRole($bureau_id);
            // $actionFlag = false;
            // if(isset($role["CNT"]) && $role["CNT"]>0) {
			// 	$actionFlag = true;
			// }
			// if(false==$actionFlag) {
            //     return [false,2];
            // }

            $dataList = array();

            $startData = $this->getStartQueryData($bureau_id);

            for($i = 0 ; $i < sizeof($startData);$i++){
                $tmpAry = array();
				$tmpAry["name"] = $startData[$i]["NAME"];
				$tmpAry["bu_id"] = $startData[$i]["BUREAU_ID"];
				$tmpAry["H1"] = 0;
				$tmpAry["H2"] = 0;
				$tmpAry["H3"] = 0;
				$tmpAry["H4"] = 0;
				$tmpAry["H5"] = 0;
				$tmpAry["H6"] = 0;
				$tmpAry["H7"] = 0;
				$tmpAry["H8"] = 0;
				$dataList[$startData[$i]["BUREAU_ID"]] = $tmpAry;
            }
            
            $detailData = $this->getDetail($year,$bureau_id);
            for ($i = 0 ; $i < sizeof($detailData);$i++) {
                if ($detailData[$i]["IS_MIXED"]==1) {
                    if ($dataList[$detailData[$i]["BEAURAU_ID"]]) {
                        $dataList[$detailData[$i]["BEAURAU_ID"]]["H7"]+=$detailData[$i]["H1"]+$detailData[$i]["H2"]+$detailData[$i]["H3"];
                        $dataList[$detailData[$i]["BEAURAU_ID"]]["H8"]+=$detailData[$i]["CNT"];
                    }
                } elseif ($detailData[$i]["IS_MIXED"]==0) {
                    if ($detailData[$i]["IS_ASSESS"]==1) {
                        if ($dataList[$detailData[$i]["BEAURAU_ID"]]) {
                            $dataList[$detailData[$i]["BEAURAU_ID"]]["H1"]+=$detailData[$i]["H1"];
                            $dataList[$detailData[$i]["BEAURAU_ID"]]["H2"]+=$detailData[$i]["CNT"];
                        }
                    } elseif ($detailData[$i]["IS_ASSESS"]==0) {
                        if ($dataList[$detailData[$i]["BEAURAU_ID"]]) {
                            $dataList[$detailData[$i]["BEAURAU_ID"]]["H3"]+=$detailData[$i]["H2"];
                            $dataList[$detailData[$i]["BEAURAU_ID"]]["H4"]+=$detailData[$i]["CNT"];
                        }
                    }
                }
            }

            $detailData = $this->getEData($year,$bureau_id);

            
            for($i =0; $i < sizeof($detailData); $i++){
                if($dataList[$detailData[$i]["BEAURAU_ID"]]) {
					$dataList[$detailData[$i]["BEAURAU_ID"]]["H5"]+=$detailData[$i]["SHOR"];
					$dataList[$detailData[$i]["BEAURAU_ID"]]["H6"]+=$detailData[$i]["CNT"];
				}
            }

			$tmpAry = array("H1"=>0, "H2"=>0, "H3"=>0, "H4"=>0, "H5"=>0, "H6"=>0, "H7"=>0, "H8"=>0);
			foreach ($dataList as $k => $v) {
				$tmpAry["H1"]+=$v["H1"];
				$tmpAry["H2"]+=$v["H2"];
				$tmpAry["H3"]+=$v["H3"];
				$tmpAry["H4"]+=$v["H4"];
				$tmpAry["H5"]+=$v["H5"];
				$tmpAry["H6"]+=$v["H6"];
				$tmpAry["H7"]+=$v["H7"];
				$tmpAry["H8"]+=$v["H8"];
            }
            
            $body = "<tr>
					    <td>一級暨所屬合計</td>
					    <td>".$tmpAry["H3"]."</td>
					    <td>".$tmpAry["H4"]."</td>
					    <td>".$tmpAry["H1"]."</td>
					    <td>".$tmpAry["H2"]."</td>
					    <td>".$tmpAry["H5"]."</td>
					    <td>".$tmpAry["H6"]."</td>
					    <td>".$tmpAry["H7"]."</td>
					    <td>".$tmpAry["H8"]."</td>
					  </tr>";
			foreach ($dataList as $k => $v) {
				$body .= "<tr>
					    <td>".$v["name"]."</td>
					    <td>".$v["H3"]."</td>
					    <td>".$v["H4"]."</td>
					    <td>".$v["H1"]."</td>
					    <td>".$v["H2"]."</td>
					    <td>".$v["H5"]."</td>
					    <td>".$v["H6"]."</td>
					    <td>".$v["H7"]."</td>
					    <td>".$v["H8"]."</td>
					  </tr>";
            }
            header("Content-type:application/vnd.ms-excel");
            header("Content-Disposition:filename=21L.xls");
            
            echo "<html>
                    <body>
                    <h1>一級暨所屬研習時數統計表</h1>
                    <div style='text-align:left'>統計區間：".htmlspecialchars($range, ENT_HTML5|ENT_QUOTES)."</div>
                    <div style='text-align:right'>單位：小時／人次</div>
                    <table border='1' cellspacing='0' cellpadding='0' width='100%'>
                    <tr>
                    <th rowspan='2' width='269'>局處名稱\統計項目</th>
                    <th colspan='4' width='404'>實體課程</th>
                    <th colspan='2' width='202'>數位課程</th>
                    <th colspan='2' width='202'>混成課程</th>
                    </tr>
                    <tr>
                    <td colspan='2' style='text-align:center' width='202'>無考核時數/人次</td>
                    <td colspan='2' style='text-align:center' width='202'>有考核時數/人次</td>
                    <td colspan='2' style='text-align:center' width='202'>臺北e大時數/人次</td>
                    <td colspan='2' style='text-align:center' width='202'>總時數(e大+實體)/人次</td>
                    </tr>
                    $body
                    </table>
                    </body>
                    </html>";
                    return [true];

        }
        else{
            return [false,2];
        }
     }

     public function GetTimeInterval($post_year){
        // $timeInterval = ($post_year-1911)."0101~".($post_year-1911);
        $timeInterval = ($post_year)."0101~".($post_year);
        $getMonth = date("m");
        $getDay = date("d");
        if(intval($getMonth)<=2) {
            $timeInterval .= "0131";
        }
        else {
            if(intval($getDay)>=15) {
                $a_date = sprintf("%s-%s-01", $post_year, $getMonth-1);
                $timeInterval .= date("m", strtotime($a_date)).date("t", strtotime($a_date));
            }
            else {
                $a_date = sprintf("%s-%s-01", $post_year, $getMonth-2);
                $timeInterval .= date("m", strtotime($a_date)).date("t", strtotime($a_date));
            }
        }
        if($post_year<date("Y")) {
            $timeInterval = ($post_year-1911)."0101~".($post_year-1911)."1231";
            $timeInterval = ($post_year)."0101~".($post_year)."1231";
        }
        return $timeInterval;
     }

     public function getPersonalInfo($account){
        $personalInfo = array();
        $where = "";
        $orderby = "";
        $sql ="SELECT a.idno AS PERSONAL_ID, a.name AS FIRST_NAME FROM BS_user a WHERE a.USERNAME=".$this->db->escape(addslashes($account))." " ;
        
        $orderby = "  ";
        $sql = $sql . " " . $where . " ". $orderby;

        $query = $this->db->query($sql);

        $data = $this->QueryToArray($query);
        if(sizeof($data) > 0)
            $personalInfo = array($data[0]['PERSONAL_ID'],$data[0]['FIRST_NAME']);
        
        return $personalInfo;
     }

     public function getRole($beaurau_id){
        // $roleInfo = array();
        $where = "";
        $orderby = "";
        $sql ="SELECT count(1) cnt FROM BS_user a
                                                                WHERE a.bureau_id                                
                                IN (                                                                
                                SELECT DISTINCT bc.bureau_id
                                                                FROM bureau bc                                
                                                                LEFT JOIN second_category sc
                                                                        ON bc.bureau_id=sc.item_id
                                                                WHERE sc.short_name IS NOT NULL AND bc.bureau_level=3
                                                                        AND (bc.abolish_date IS NULL)
                                                                        AND bureau_id != '379920000Z')                                    
                                                                AND a.idno=".$this->db->escape(addslashes($beaurau_id))."" ;
        
        $orderby = "  ";
        $sql = $sql . " " . $where . " ". $orderby;

        $query = $this->db->query($sql);

        return $this->QueryToArray($query);
        
     }

     public function getStartQueryData($beaurau_id){
        $where = "";
        $orderby = "";
        $sql ="SELECT a.BUREAU_ID, a.NAME
        FROM bureau a
        WHERE a.abolish_date IS NULL
                AND (a.BUREAU_ID=".$this->db->escape(addslashes($beaurau_id))." OR a.PARENT_ID=".$this->db->escape(addslashes($beaurau_id)).")
        ORDER BY a.BUREAU_LEVEL, a.BUREAU_ID" ;
        
        $orderby = "  ";
        $sql = $sql . " " . $where . " ". $orderby;

        $query = $this->db->query($sql);

        return $this->QueryToArray($query);
     }

     public function getDetail($post_year,$session_beaurau_id){
        $where = "";
        $orderby = "";
        $sql ="SELECT c.bureau_id AS BEAURAU_ID, b.IS_MIXED, b.IS_ASSESS,
                    count(1) CNT, sum(a.H1) H1, sum(a.H2) H2, sum(a.H3) H3
            FROM lux_study_record_log a
            JOIN `require` b ON a.CLASS_NO=b.CLASS_NO
            AND a.YEAR=b.YEAR
            AND a.TERM=b.TERM
            JOIN BS_user c ON a.STU_ID=c.idno

            WHERE a.YEAR=".$this->db->escape(addslashes($post_year))." AND c.bureau_id in (
                    SELECT a.BUREAU_ID
                    FROM bureau a
                    WHERE a.abolish_date IS NULL
                    AND (a.BUREAU_ID=".$this->db->escape(addslashes($session_beaurau_id))." OR a.PARENT_ID=".$this->db->escape(addslashes($session_beaurau_id))."))                                                                           
            GROUP BY c.bureau_id, b.IS_MIXED, b.IS_ASSESS ";
       

        $orderby = "  ";
        $sql = $sql . " " . $where . " ". $orderby;

         $query = $this->db->query($sql);

        return $this->QueryToArray($query);
     }

     public function getEData($post_year,$session_beaurau_id){
        $where = "";
        $orderby = "";
        $sql ="SELECT count(1) CNT, sum(a.CERTHOUR) SHOR, c.bureau_id AS BEAURAU_ID
        FROM lux_elearn_record_log a
        JOIN BS_user c ON a.STU_ID=c.idno

        WHERE a.YEAR=".($post_year+1911)." AND c.bureau_id in (SELECT a.BUREAU_ID
                FROM bureau a
                WHERE a.abolish_date IS NULL
                AND (a.BUREAU_ID=".$this->db->escape(addslashes($session_beaurau_id))." OR a.PARENT_ID=".$this->db->escape(addslashes($session_beaurau_id))."))
        GROUP BY c.bureau_id
        ORDER BY c.bureau_id ";
       

        $orderby = "  ";
        $sql = $sql . " " . $where . " ". $orderby;

         $query = $this->db->query($sql);

        return $this->QueryToArray($query);
        

     }

}
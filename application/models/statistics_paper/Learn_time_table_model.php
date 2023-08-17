<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once(APPPATH."models/Common_model.php");
class Learn_time_table_model extends Common_model
{
     public function getDownLoadExcel($year,$bureau_id,$account,$range){
        $personalInfo =  $this->getPersonalInfo($account);
        $dataList = array();
        if(sizeof($personalInfo) == 2 && $personalInfo[0]!=""){
            // $auth = $this->GetAth($account);
            // $actionFlag = false;
            // for($i = 0; $i < sizeof($auth);$i++) {
			// 	if($auth[$i]["ROLE_ID"]=="10" || $auth[$i]["ROLE_ID"]=="12" || $auth[$i]["ROLE_ID"]=="50") {
			// 		$actionFlag = true;
			// 		break;
            //     }
			// }
			// if(false==$actionFlag) {
			// 	return [false,2];
            // }
            
            $Detail = $this->getDetail($year,$bureau_id);
            // print_r($Detail);
            for($i = 0; $i < sizeof($Detail);$i++) {

				if($Detail[$i]["IS_ASSESS"]==1&&$Detail[$i]["IS_MIXED"]==1) {
					$classType="混成";
				}
				else {
					$classType="實體";
				}
				if($Detail[$i]["TYPE"]=="A") {
					$category = "行政";
				}
				elseif($Detail[$i]["TYPE"]=="B") {
					$category = "發展";
				}
				else {
					$category = "數位";
				}
				$tmpCol = "";
				if($classType=="混成") {
					$tmpCol = ($Detail[$i]["h1"]+$Detail[$i]["h2"]+$Detail[$i]["h3"])."(".$Detail[$i]["h3"]."+".($Detail[$i]["h1"]+$Detail[$i]["h2"]).")";
				}
				$tmpAry = array($category, $Detail[$i]["REQ_BEAURAU"], $Detail[$i]["CLASS_NAME"]."(第".$Detail[$i]["term"]."期)",
					$classType, $Detail[$i]["FIRST_NAME"], $Detail[$i]["h2"]==0?"":$Detail[$i]["h2"], $Detail[$i]["h1"]==0||$classType=="混成"?"":$Detail[$i]["h1"], $Detail[$i]["h3"]==0||$classType=="混成"?"":$Detail[$i]["h3"], $tmpCol);
				array_push($dataList, $tmpAry);
            }
            
            $eData = $this->getEData($year,$bureau_id);

            for($i = 0 ; $i <sizeof($eData)  ; $eData++) {
				$tmpAry = array("數位", "", $eData[$i]["CNAME"], "數位", $eData[$i]["FIRST_NAME"], "", "", $eData[$i]["CERTHOUR"], "");
				array_push($dataList, $tmpAry);
            }
            

            header("Content-type:application/vnd.ms-excel");
			header("Content-Disposition:filename=21M.xls");
			$body = "";
			for($i=0;$i<count($dataList);$i++) {
			$fontColor = "";
			if($dataList[$i][3]=="混成") {
				$fontColor = "style='color:red'";
			}
			$body .= "<tr>
				    <td>".$dataList[$i][0]."</td>
				    <td>".$dataList[$i][1]."</td>
				    <td>".$dataList[$i][2]."</td>
				    <td $fontColor>".$dataList[$i][3]."</td>
				    <td>".$dataList[$i][4]."</td>
				    <td>".($dataList[$i][3]!="混成"?$dataList[$i][5]:"")."</td>
				    <td>".$dataList[$i][6]."</td>
				    <td>".$dataList[$i][7]."</td>
				    <td>".$dataList[$i][8]."</td>
				  </tr>";
		  	}
            echo "<html>
            <body>
            <h1>機關人員研習時數統計表</h1>
            <div style='text-align:left'>統計區間：".htmlspecialchars($range, ENT_HTML5|ENT_QUOTES)."</div>
            <div style='text-align:right'>單位：小時</div>
            <table border='1' cellspacing='0' cellpadding='0' width='100%'>
            <tr>
                <th rowspan='2'>類別</th>
                <th rowspan='2'>承辦機關</th>
                <th rowspan='2'>班期名稱(期別)</th>
                <th rowspan='2'>班期性質</th>
                <th rowspan='2'>結訓人</th>
                <th colspan='2'>實體課程</th>
                <th>數位課程</th>
                <th>混成課程</th>
            </tr>
            <tr>
                <td>無考核</td>
                <td>有考核</td>
                <td>臺北e大</td>
                <td>總時數(e大+實體)</td>
            </tr>
            $body
            </table>
            </body>
            </html>";

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

     public function GetAth($username){
        $where = "";
        $orderby = "";
        $sql ="SELECT r.role_id FROM account_role r WHERE r.ID=".$this->db->escape(addslashes($username))." " ;
        
        $orderby = "  ";
        $sql = $sql . " " . $where . " ". $orderby;

        $query = $this->db->query($sql);

        return $this->QueryToArray($query);
     }

     public function getDetail($post_year,$session_beaurau_id){
        $where = "";
        $orderby = "";
        $sql ="SELECT a.*, b.CLASS_NAME, b.IS_MIXED, b.IS_ASSESS, b.TYPE, c.name AS FIRST_NAME,
            (SELECT v.name FROM bureau v WHERE v.bureau_id=b.REQ_BEAURAU) REQ_BEAURAU
            FROM lux_study_record_log  a

            JOIN `require` b ON a.CLASS_NO=b.CLASS_NO
            AND a.YEAR=b.YEAR
            AND a.TERM=b.TERM
            JOIN BS_user  c ON a.STU_ID=c.idno
            

            WHERE a.YEAR=".$this->db->escape(addslashes($post_year))." AND c.bureau_id=".$this->db->escape(addslashes($session_beaurau_id))."
            ORDER BY c.name, a.MONTH, a.CREATE_DATE ";
       

        $orderby = "  ";
        $sql = $sql . " " . $where . " ". $orderby;

         $query = $this->db->query($sql);

        return $this->QueryToArray($query);
     }

     public function getEData($post_year,$session_beaurau_id){
        $where = "";
        $orderby = "";
        $sql ="  SELECT a.*, c.name AS FIRST_NAME FROM lux_elearn_record_log  a
        JOIN BS_user c ON a.STU_ID=c.idno
        
        WHERE a.YEAR=".$this->db->escape(addslashes($post_year))." AND c.bureau_id=".$this->db->escape(addslashes($session_beaurau_id))." ORDER BY c.name, a.MONTH, a.TIMECOMPLETE ;";
       

        $orderby = "  ";
        $sql = $sql . " " . $where . " ". $orderby;

         $query = $this->db->query($sql);

        return $this->QueryToArray($query);
        

     }

}
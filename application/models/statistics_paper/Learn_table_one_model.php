<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once(APPPATH."models/Common_model.php");
class Learn_table_one_model extends Common_model
{
     public function getDownLoadExcel($year,$bureau_id,$account,$range,$post_onegov){
        $personalInfo =  $this->getPersonalInfo($account);
        $dataList = array();
        if(sizeof($personalInfo) == 2 && $personalInfo[0]!=""){
            // $auth = $this->GetAth($account);
            // $actionFlag = false;
            // for($i = 0; $i < sizeof($auth);$i++) {
			// 	if($auth[$i]["ROLE_ID"]=="10" || $auth[$i]["ROLE_ID"]=="12" ) {
			// 		$actionFlag = true;
			// 		break;
            //     }
			// }
			// if(false==$actionFlag) {
			// 	return [false,1,$auth];
            // }
            
            $govAry = array();
			foreach ($this->qBureaus($post_onegov) as $k => $v) {
				$tmpAry = array();
				$tmpAry["name"] = $v["name"];
				$tmpAry["bu_id"] = $v["bureau_id"];
				$tmpAry["H1"] = 0;
				$tmpAry["H2"] = 0;
				$tmpAry["H3"] = 0;
				$tmpAry["H4"] = 0;
				$tmpAry["H5"] = 0;
				$tmpAry["H6"] = 0;
				$tmpAry["H7"] = 0;
				$tmpAry["H8"] = 0;
				$dataList[$v["bureau_id"]] = $tmpAry;
				array_push($govAry, $v);
            }
            
            if(1==$post_onegov) {
				$govAry = array();
				foreach ($this->qContentData() as $k => $v) {
					array_push($govAry, $v);
				}
            }
            
            if(count($govAry)>0) {
                $sqlIn = "";
				foreach ($govAry as $k => $v) {
					$sqlIn.=sprintf("'%s', ", $v["bureau_id"]);
				}
                $sqlIn = substr($sqlIn, 0, strlen($sqlIn)-2);
                
            }

            $retrunSqlIn = $this->GetsqlIn($year,$sqlIn);

            for($i = 0 ; $i < sizeof($retrunSqlIn); $i++) {
                if(1==$post_onegov) {
                    $saveBu = "";
                    foreach ($govAry as $k => $v) {
                        if($retrunSqlIn[$i]["BEAURAU_ID"]==$v["bureau_id"]) {
                            if($govAry[$k]["PARENT_ID"]=="A00000000A" || $govAry[$k]["PARENT_ID"]=="379000000A") {
                                $saveBu = $retrunSqlIn[$i]["BEAURAU_ID"];
                            }
                            else {
                                $saveBu = $v["PARENT_ID"];
                            }
                            break;
                        }
                    }
                    if($saveBu!="") {
                        if($retrunSqlIn[$i]["IS_MIXED"]==1) {
                            if($dataList[$saveBu]) {
                                $dataList[$saveBu]["H7"]+=$retrunSqlIn[$i]["H1"]+$retrunSqlIn[$i]["H2"]+$retrunSqlIn[$i]["H3"];
                                $dataList[$saveBu]["H8"]+=$retrunSqlIn[$i]["CNT"];
                            }
                        }
                        elseif($retrunSqlIn[$i]["IS_MIXED"]==0) {
                            if($retrunSqlIn[$i]["IS_ASSESS"]==1) {
                                if($dataList[$saveBu]) {
                                    $dataList[$saveBu]["H1"]+=$retrunSqlIn[$i]["H1"];
                                    $dataList[$saveBu]["H2"]+=$retrunSqlIn[$i]["CNT"];
                                }
                            }
                            elseif($retrunSqlIn[$i]["IS_ASSESS"]==0) {
                                if($dataList[$saveBu]) {
                                    $dataList[$saveBu]["H3"]+=$retrunSqlIn[$i]["H2"];
                                    $dataList[$saveBu]["H4"]+=$retrunSqlIn[$i]["CNT"];
                                }
                            }
                        }
                    }
                }
                else {
                    if($retrunSqlIn[$i]["IS_MIXED"]==1) {
                        if($dataList[$retrunSqlIn[$i]["BEAURAU_ID"]]) {
                            $dataList[$retrunSqlIn[$i]["BEAURAU_ID"]]["H7"]+=$retrunSqlIn[$i]["H1"]+$retrunSqlIn[$i]["H2"]+$retrunSqlIn[$i]["H3"];
                            $dataList[$retrunSqlIn[$i]["BEAURAU_ID"]]["H8"]+=$retrunSqlIn[$i]["CNT"];
                        }
                    }
                    elseif($retrunSqlIn[$i]["IS_MIXED"]==0) {
                        if($retrunSqlIn[$i]["IS_ASSESS"]==1) {
                            if($dataList[$retrunSqlIn[$i]["BEAURAU_ID"]]) {
                                $dataList[$retrunSqlIn[$i]["BEAURAU_ID"]]["H1"]+=$retrunSqlIn[$i]["H1"];
                                $dataList[$retrunSqlIn[$i]["BEAURAU_ID"]]["H2"]+=$retrunSqlIn[$i]["CNT"];
                            }
                        }
                        elseif($retrunSqlIn[$i]["IS_ASSESS"]==0) {
                            if($dataList[$retrunSqlIn[$i]["BEAURAU_ID"]]) {
                                $dataList[$retrunSqlIn[$i]["BEAURAU_ID"]]["H3"]+=$retrunSqlIn[$i]["H2"];
                                $dataList[$retrunSqlIn[$i]["BEAURAU_ID"]]["H4"]+=$retrunSqlIn[$i]["CNT"];
                            }
                        }
                    }
                }
            }

            $retrunDetail = $this->getDetail($year,$sqlIn);

            for($i = 0 ; $i <sizeof($retrunDetail); $i++) {
                if(1==$post_onegov) {
                    $saveBu = "";
                    foreach ($govAry as $k => $v) {
                        if($retrunDetail[$i]["BEAURAU_ID"]==$v["bureau_id"]) {
                            if($govAry[$k]["PARENT_ID"]=="A00000000A" || $govAry[$k]["PARENT_ID"]=="379000000A") {
                                $saveBu = $retrunDetail[$i]["BEAURAU_ID"];
                            }
                            else {
                                $saveBu = $v["PARENT_ID"];
                            }
                            break;
                        }
                    }
                    if($saveBu!="") {
                        if($dataList[$saveBu]) {
                            $dataList[$saveBu]["H5"]+=$retrunDetail[$i]["SHOR"];
                            $dataList[$saveBu]["H6"]+=$retrunDetail[$i]["CNT"];
                        }
                    }
                }
                else {
                    if($dataList[$retrunDetail[$i]["BEAURAU_ID"]]) {
                        $dataList[$retrunDetail[$i]["BEAURAU_ID"]]["H5"]+=$retrunDetail[$i]["SHOR"];
                        $dataList[$retrunDetail[$i]["BEAURAU_ID"]]["H6"]+=$retrunDetail[$i]["CNT"];
                    }
                }
            }
            
        header("Content-type:application/vnd.ms-excel");
            header("Content-Disposition:filename=21K$post_onegov.xls");
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
            $body = "";
            $fileTitle = "";
            $navDesc = "";
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

            if(1==$post_onegov) {
                $fileTitle = "一級暨所屬研習時數統計表";
                $navDesc = "一級暨所屬合計";
            }
            elseif(2==$post_onegov) {
                $fileTitle = "各局處研習時數統計表";
                $navDesc = "各機關合計";
            }
            $body .= "<tr>
                <td width='300'>$navDesc</td>
                <td width='100'>".$tmpAry["H1"]."</td>
                <td width='100'>".$tmpAry["H2"]."</td>
                <td width='100'>".$tmpAry["H3"]."</td>
                <td width='100'>".$tmpAry["H4"]."</td>
                <td width='100'>".$tmpAry["H5"]."</td>
                <td width='100'>".$tmpAry["H6"]."</td>
                <td width='100'>".$tmpAry["H7"]."</td>
                <td width='100'>".$tmpAry["H8"]."</td>
                </tr>";

                echo "<html>
                <body>
                <h1>$fileTitle</h1>
                <div style='text-align:left'>統計區間：".htmlspecialchars($range, ENT_HTML5|ENT_QUOTES)."</div>
                <div style='text-align:right'>單位：小時／人次</div>
                <table border='1' cellspacing='0' cellpadding='0' width='1110'>
                <tr>
                <th rowspan='2' width='300'>局處名稱\統計項目</th>
                <th colspan='4' width='400'>實體課程</th>
                <th colspan='2' width='200'>數位課程</th>
                <th colspan='2' width='200'>混成課程</th>
                </tr>
                <tr>
                <td colspan='2' style='text-align:center'>無考核時數/人次</td>
                <td colspan='2' style='text-align:center'>有考核時數/人次</td>
                <td colspan='2' style='text-align:center'>臺北e大時數/人次</td>
                <td colspan='2' style='text-align:center'>總時數(e大+實體)/人次</td>
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

     public function qBureaus($ytp=1) {
        if(1==$ytp) {
            $pGov = array("臺北市政府工務局",
                "臺北市政府文化局",
                "臺北市政府民政局",
                "臺北市政府交通局",
                "臺北市政府地政局",
                "臺北市政府社會局",
                "臺北市政府秘書處",
                "臺北市政府財政局",
                "臺北市政府捷運工程局",
                "臺北市政府教育局",
                "臺北市立大安高級工業職業學校",
                "臺北市政府產業發展局",
                "臺北市政府都市發展局",
                "臺北市政府勞動局",
                "臺北市政府衛生局",
                "臺北市政府環境保護局",
                "臺北市政府警察局",
                "臺北市政府觀光傳播局",
                "臺北自來水事業處");
            $sql = "SELECT bc.bureau_id, bc.name as name
                FROM bureau bc
                LEFT JOIN second_category sc
                    ON bc.bureau_id=sc.item_id
                WHERE sc.short_name IS NOT NULL AND bc.bureau_level=3
                    and (bc.abolish_date is null )
                    and bureau_id != '379920000Z'
                ORDER BY bc.PARENT_ID, bc.bureau_id ";
        }
        else {
            $sql = "SELECT a.bureau_id, a.name, a.PARENT_ID
                    FROM bureau a
                    WHERE (
                        a.BUREAU_ID IN (
                            SELECT DISTINCT bc.bureau_id
                            FROM bureau bc
                            LEFT JOIN second_category sc
                                ON bc.bureau_id=sc.item_id
                            WHERE sc.short_name IS NOT NULL AND bc.bureau_level=3
                                AND (bc.abolish_date is null )
                                AND bureau_id != '379920000Z'
                        )OR a.PARENT_ID IN (
                            SELECT DISTINCT bc.bureau_id
                            FROM bureau bc
                            LEFT JOIN second_category sc
                                ON bc.bureau_id=sc.item_id
                            WHERE sc.short_name IS NOT NULL AND bc.bureau_level=3
                                AND (bc.abolish_date is null )
                                AND bureau_id != '379920000Z'
                        )
                    )
                    AND a.abolish_date is null
                    AND a.bureau_id != '379920000Z'
                    AND (a.DEL_FLAG<>'C' OR a.DEL_FLAG IS NULL)
                    ORDER BY a.PARENT_ID, a.BUREAU_ID ";
        }

        $query = $this->db->query($sql);

        $qdatas = $this->QueryToArray($query);
        
        $bureau = array();
    
        $bureau["379000000A"] = array(
            'bureau_id'=>'379000000A',
            'name'=>'臺北市政府',
            'PARENT_ID'=>'A00000000A'
        );

        //填資料
        if(1==$ytp) {
            for ($i= 0; $i < sizeof($qdatas);$i++ ) {
                for($ij=0;$ij<count($pGov);$ij++) {
                    if($pGov[$ij]==$qdatas[$i]["name"]) {
                      $qdatas[$i]["name"] .= "暨所屬";
                    }
                }
                $bureau[$qdatas[$i]["bureau_id"]] = $qdatas[$i];
            }
        }
        else {
            $bureau = $this->sortAry($bureau);
        }

        return $bureau;
    }

    public function sortAry($ary) {
        $returnAry = array();
        $buLevel_1 = array();
        $buLevel_2 = array();
        echo "<pre>";
        foreach ($ary as $k => $v) {
            if($v["PARENT_ID"]=="A00000000A" || $v["PARENT_ID"]=="379000000A") {
                array_push($buLevel_1, $v);
            }
            else {
                array_push($buLevel_2, $v);
            }
        }
        for($i=0;$i<count($buLevel_1);$i++) {
            $returnAry[$buLevel_1[$i]["bureau_id"]] = $buLevel_1[$i];
            for($j=0;$j<count($buLevel_2);$j++) {
                if($buLevel_2[$j]["PARENT_ID"]==$buLevel_1[$i]["bureau_id"]) {
                    $returnAry[$buLevel_2[$j]["bureau_id"]] = $buLevel_2[$j];
                }
            }
        }
        return $returnAry;
    }
    
    //取完整的局處列表(含父機關)
    public function qContentData() {
        $sql = "SELECT a.bureau_id, a.name, a.PARENT_ID
                FROM bureau a
                WHERE (
                    a.BUREAU_ID IN (
                        SELECT DISTINCT bc.bureau_id
                        FROM bureau bc
                        LEFT JOIN second_category sc
                            ON bc.bureau_id=sc.item_id
                        WHERE sc.short_name IS NOT NULL AND bc.bureau_level=3
                            AND (bc.abolish_date is null )
                            AND bureau_id != '379920000Z'
                    )OR a.PARENT_ID IN (
                        SELECT DISTINCT bc.bureau_id
                        FROM bureau bc
                        LEFT JOIN second_category sc
                            ON bc.bureau_id=sc.item_id
                        WHERE sc.short_name IS NOT NULL AND bc.bureau_level=3
                            AND (bc.abolish_date is null )
                            AND bureau_id != '379920000Z'
                    )
                )
                AND a.abolish_date is null
                AND a.bureau_id != '379920000Z'
                AND (a.DEL_FLAG<>'C' OR a.DEL_FLAG IS NULL)
                ORDER BY a.bureau_id ";
                
        $query = $this->db->query($sql);

        $qdatas = $this->QueryToArray($query);

        $bureau = array();

        $bureau["379000000A"] = array(
            'bureau_id'=>'379000000A',
            'name'=>'臺北市政府',
            'PARENT_ID'=>'A00000000A'
        );
        //填資料
        for ($i= 0 ; $i < sizeof($qdatas);$i++) {
            $bureau[$qdatas[$i]["bureau_id"]] = $qdatas[$i];
        }
        return $bureau;
    }

     public function GetsqlIn($post_year,$sqlIn){
        $sqlIn = explode("," , $sqlIn);
        for($i=0; $i<count($sqlIn); $i++){
            $sqlIn[$i] = $this->db->escape(addslashes($sqlIn[$i])); 
        }
        $sqlIn = implode("," , $sqlIn);

        $sql_detail = sprintf("SELECT c.bureau_id AS BEAURAU_ID, b.IS_MIXED, b.IS_ASSESS,
										count(1) CNT, sum(a.H1) H1, sum(a.H2) H2, sum(a.H3) H3
									FROM lux_study_record_log a
									JOIN `require` b ON a.CLASS_NO=b.CLASS_NO
									AND a.YEAR=b.YEAR
									AND a.TERM=b.TERM
									JOIN BS_user c ON a.STU_ID=c.idno
									WHERE a.YEAR=%d AND c.bureau_id in (
										SELECT a.BUREAU_ID
										FROM bureau a
										WHERE a.abolish_date IS NULL
										AND c.bureau_id IN (%s) )
									GROUP BY c.bureau_id, b.IS_MIXED, b.IS_ASSESS", $this->db->escape(addslashes($post_year)), $sqlIn);


        $query = $this->db->query($sql_detail);

        return $this->QueryToArray($query);
     }

     public function getDetail($post_year,$sqlIn){
        $sqlIn = explode("," , $sqlIn);
        for($i=0; $i<count($sqlIn); $i++){
            $sqlIn[$i] = $this->db->escape(addslashes($sqlIn[$i])); 
        }
        $sqlIn = implode("," , $sqlIn);         
       $post_year = $post_year + 1911;
       $sql_detail = sprintf("SELECT count(1) CNT, sum(a.CERTHOUR) SHOR, c.bureau_id AS BEAURAU_ID
								FROM lux_elearn_record_log a
								JOIN BS_user c ON a.STU_ID=c.idno
								WHERE a.YEAR=%d AND c.bureau_id in (SELECT a.BUREAU_ID
									FROM bureau a
									WHERE a.abolish_date IS NULL
									AND c.bureau_id IN (%s) )
								GROUP BY c.bureau_id
								ORDER BY c.bureau_id", $this->db->escape(addslashes($post_year)), $sqlIn);

       
         $query = $this->db->query($sql_detail);

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
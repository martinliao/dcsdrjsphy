<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

require_once APPPATH . "models/Common_model.php";
class Pay_model extends Common_model
{
    public function getPayData($d1, $d2, $idno, $rightType)
    {
    	if(empty($d1) && empty($d2)){
            if(date('w') != "0")
                $weekend  = date("Y-m-d",mktime(0, 0, 0, date("m") ,date("d")+(7-date("w")),date("Y")));
            else
                $weekend  = date("Y-m-d",mktime(0, 0, 0, date("m") ,date("d")+(date("w")),date("Y")));
            // $where ="use_date >=to_date('{$weekend}','yyyy-mm-dd')";
            $where = "use_date <= '{$weekend}' and use_date >= (now() - 90)";
        }else{
            // $where ="use_date between to_date('{$d1}','yyyy-mm-dd') and to_date('{$d2}','yyyy-mm-dd')";
            if($d1 != '')
                $where = "use_date >= ".$this->db->escape(addslashes($d1))."";
            else 	
                $where = "use_date >= (now() - 90)";
                
            if($d2 != '')
                $where .= ( $where !='' ? " and " : "") ." use_date <= ".$this->db->escape(addslashes($d2))."";
        }

        $countIndex = 0;
        if($idno != 'admin') {
            for($i=0; $i<sizeof($rightType); $i++) {
                if($rightType[$i] != 9) {
                    $countIndex++;
                }
            }

            if($countIndex == sizeof($rightType)) {
                $where .= "AND B.worker=".$this->db->escape(addslashes($idno))."";
            }
        }

        $mailType3 = $this->ismail();
        $datas = $this->theMainSearchSql($where, $idno);
        $weekArray = array();
        $totalCount = 0;
        $outputDatas = [];
        for($i = 0 ; $i < sizeof($datas); $i++){
            $se = $this->getThisWeek($datas[$i]['use_date'], "", true);
            $kk = "{$se}#{$datas[$i]['YEAR']}#{$datas[$i]['class_no']}#{$datas[$i]['term']}";
            if(isset($mailType3["{$datas[$i]['YEAR']}_{$datas[$i]['class_no']}_{$datas[$i]['term']}"])) {
                if($mailType3["{$datas[$i]['YEAR']}_{$datas[$i]['class_no']}_{$datas[$i]['term']}"] == 'Y') { // 有mail給人事才要出現在列表
                    $weekArray[$kk] = $datas[$i]['class_name'];
                    $totalCount++;

                    $ranges = $this->getThisWeek($datas[$i]['use_date'], "", true);
                    $rs = explode("#",$ranges)[0];
                    $re = explode("#",$ranges)[1];
                    $countData = $this->getRecodeCount($datas[$i]["YEAR"],$datas[$i]["term"]
                    ,$datas[$i]["class_no"],$rs,$re);
                    $datas[$i]['rs'] = $rs;
                    $datas[$i]['re'] = $re;
                    $datas[$i]['count'] = $countData[0]['count'];

                    $outputDatas[] = $datas[$i];
                }
            }
        }

        // return $datas;
        
        // $outputDatas = [];
        // if($totalCount > 0) {
        //     for($i = 0 ; $i < sizeof($datas); $i++){
        //         if(isset($weekArray["{$se}#{$datas[$i]['YEAR']}#{$datas[$i]['class_no']}#{$datas[$i]['term']}"])){
        //             $ranges = $this->getThisWeek($datas[$i]['use_date'], "", true);
        //             $rs = explode("#",$ranges)[0];
        //             $re = explode("#",$ranges)[1];
        //             $countData = $this->getRecodeCount($datas[$i]["YEAR"],$datas[$i]["term"]
        //             ,$datas[$i]["class_no"],$rs,$re);
        //             $datas[$i]['rs'] = $rs;
        //             $datas[$i]['re'] = $re;
        //             $datas[$i]['count'] = $countData[0]['count'];

        //             $outputDatas[] = $datas[$i];
        //         }
        //     }
        // }
        // $datas = array(["count"=>,"rs"=>"2019-09-23","re"=>"2019-09-29","YEAR"=>"108","class_no"=>"AAA","term"=>"1","class_name"=>"name"]);

        return $outputDatas;

    }

    public function theMainSearchSql($where, $idno){
        $sql = "SELECT
            A.YEAR,
            A.class_no,
            A.term,
            A.class_name,
            B.IS_CANCEL,
            B.worker,
            date_format(use_date, '%Y-%m-%d') AS use_date
        FROM
            hour_traffic_tax A 
            LEFT JOIN `require` B ON B.year = A.year and B.term = A.term
            and B.class_no = A.class_no
        WHERE
            {$where} 
            -- AND B.worker='{$idno}'
            AND IFNULL(B.is_cancel, '0') = '0'
        AND (
            IFNULL (A.status, 'null') = 'null'
            OR A.status = '' 
            OR A.status = '待確認'
            OR A.status = '已設定為不請款'
        )
        AND A.teacher_name != '教務組'
        AND SUBSTR(A.CLASS_NO, 1, 3) != 'OX0'
        AND (
            A.YEAR,
            A.term,
            A.class_no,
            A.use_date,
            A.teacher_id
        ) IN (
            SELECT
                YEAR,
                term,
                class_id,
                use_date,
                teacher_id
            FROM
                room_use
        )
        GROUP BY
            A.YEAR,
            A.class_no,
            A.term,
            A.class_name,
            B.IS_CANCEL,
            A.use_date
        ORDER BY
            A.use_date DESC";

        $query = $this->db->query($sql);

        return $this->QueryToArray($query);
    }

    public function ismail(){
        $sql = "select YEAR,CLASS_NO,TERM from mail_log where mail_type='3'";
        $query = $this->db->query($sql);
        $mailData = $this->QueryToArray($query);
        $mailType3 = array();

        for($i = 0 ; $i < sizeof($mailData); $i++){
            $mailType3["{$mailData[$i]['YEAR']}_{$mailData[$i]['CLASS_NO']}_{$mailData[$i]['TERM']}"] = 'Y';
        }

        return $mailType3;
    }

    public function getRecodeCount($year,$term,$class_no,$d1,$d2){
        $sql="select count(*) as count from hour_traffic_tax_done where year='{$year}' 
        and class_no='{$class_no}' and term='{$term}' 
        and s_usedate='{$d1}'
        and e_usedate='{$d2}'";

        $query = $this->db->query($sql);

        return $this->QueryToArray($query);

    }

    //設請款已完成
    public function setPay($year,$term,$class_no,$d1,$d2){
        $sql = "insert into hour_traffic_tax_done (YEAR, TERM,CLASS_NO, S_USEDATE, E_USEDATE) 
        values ('". addslashes($year) . "','" . addslashes($term) . "','" . addslashes($class_no) . 
        "','" . addslashes($d1) . "','" . addslashes($d2) . "')";

        return $this->db->query($sql);
    }

    //設請款未完成
    public function deletePay($year,$term,$class_no,$d1,$d2){
        $sql = "delete from hour_traffic_tax_done where year=". $this->db->escape(addslashes($year)) . 
        " and term = " . $this->db->escape(addslashes($term)) . " and class_no = " . 
        $this->db->escape(addslashes($class_no)) . " and S_USEDATE = " . $this->db->escape(addslashes($d1)) . " 
        and E_USEDATE = " . $this->db->escape(addslashes($d2)) . "";

        return $this->db->query($sql);
    }

    public function setPrice($priceType,$umoney,$hmoney,$tmoney,$seq) {
        $setColumn = '';
        if($priceType == 'setunitprice') {
            $setColumn = "set unit_hour_fee=".$this->db->escape(addslashes($umoney)).", unit_hour_fee_is_changed='Y', hour_fee=".$this->db->escape(addslashes($hmoney)).", hour_fee_is_changed='Y' ";
        }
        else if($priceType == 'sethourprice') {
            $setColumn = "set unit_hour_fee=".$this->db->escape(addslashes($umoney)).", unit_hour_fee_is_changed='Y', hour_fee=".$this->db->escape(addslashes($hmoney)).", hour_fee_is_changed='Y' ";
        }
        else if($priceType == 'settrafficprice') {
            $setColumn = "set traffic_fee=".$this->db->escape(addslashes($tmoney)).", traffic_fee_is_changed='Y' ";
        }

        $sql = "update hour_traffic_tax ".$setColumn." where seq=".$this->db->escape(addslashes($seq))."";
        $this->db->query($sql);
        return "變更成功";
    }


    /*
    * $rtn == true : 回傳 $val的所屬周開始日和結束日
    * $rtn == false : 回傳本周的起始日和結束日
    */
    public function  getThisWeek($val, $msg_day, $rtn = false){
       if ($msg_day == "")
           $msg_day = "20";
           
       if($rtn == true){
           $tmp = explode('-', $val);
           $today = mktime(0,0,0, $tmp[1], $tmp[2], $tmp[0]);
       }else
           $today = mktime(0,0,0, date('m'), date('d'), date('Y'));
   
       $w = date('w', $today); // 今天是星期幾
   
       if($w == 0){
           // 取出本周的第一天
           $week_start = date('Y-m-d', $today - (86400*6));
           $week_end = date('Y-m-d', $today);
       }else{
            $week_start = date('Y-m-d', $today - (86400*($w-1)));
           $week_end = date('Y-m-d', $today + (86400*(7-$w)));

       }
   
       if($rtn == false){
           if($val >= $week_start && $val <= $week_end)
               return ' style = "background-color : #FCFAA0;" ';
           else{
               $before_day = ($today - strtotime($val))/86400;
               if($before_day < $msg_day)
               return ' style = "background-color : #BFCEDB;" ';
               else
               return  '';
           }
       }
       else{
           return $week_start . '#' .$week_end;
       }
   }
    public function getPayDetailData($startdate,$enddate,$year,$class,$term){
        // $d1 = $_REQUEST['s1'];
        // $d2 = $_REQUEST['s2'];
        // 載入縣市區的對照表
        $cityArr = array();
        $rs_city = $this->db->query("select CITY, CITY_NAME from co_city");
        $rs_city = $this->QueryToArray($rs_city);
        if($rs_city)
        for ($i=0; $i < sizeof($rs_city); $i++) { 
            $row_city=$rs_city[$i];
            $cityArr[$row_city['CITY']] = $row_city['CITY_NAME'];
        } 

        $subcityArr = array();
        $rs_subcity = $this->db->query("select CITY, SUBCITY, SUBCITY_NAME from co_subcity");
        $rs_subcity = $this->QueryToArray($rs_subcity);
        if($rs_subcity)
        for ($i=0; $i < sizeof($rs_subcity); $i++) { 
            $row_subcity=$rs_subcity[$i];
            $k = "{$row_subcity['CITY']}-{$row_subcity['SUBCITY']}";
            $subcityArr[$k] = $row_subcity['SUBCITY_NAME'];
        } 

        $year     = addslashes($year);
        $class_no = addslashes($class);
        $term     = addslashes($term);

        // custom (b) by chiahua 限定查詢某一周的資料
        $w1 = addslashes($startdate);
        $w2 = addslashes($enddate);
        $ww = " and date_format(a.use_date,'%Y-%m-%d') between '{$w1}' and '{$w2}' ";
        // custom (e) by chiahua 限定查詢某一周的資料

        // $dd1 = ($d1 != "" ? " and to_char(use_date,'yyyy-mm-dd') >= '{$d1}' " : "");
        // $dd2 = ($d2 != "" ? " and to_char(use_date,'yyyy-mm-dd') <= '{$d2}' " : "");


        $output=[];
        //$where = " teacher_name !='教務組' and use_date between to_date('{$d1}','yyyy-mm-dd') and to_date('{$d2}','yyyy-mm-dd') and year = '{$year}' and class_no = '{$class_no}' and term = '{$term}'";
        // $where = " ((status !='市庫支票' and status !='請款確認') or status is null ) and teacher_name !='教務組' {$dd1} {$dd2} and year = '{$year}' and class_no = '{$class_no}' and term = '{$term}'";
        $where = " ((a.status !='市庫支票' and a.status !='請款確認') or a.status is null ) and a.teacher_id not in ('A111111113') {$ww} and a.year = '{$year}' and a.class_no = '{$class_no}' and a.term = '{$term}'";

        // $sql = "select year, class_no, term, class_name from `require`  where year = '" . $year . "' and class_no = '" . $class_no . "' and term = '" . $term . "'";
        // $rs = $this->db->query($sql);
        // $data = $this->QueryToArray($rs);
        // $output[0]=$data;
        // #47263 僅顯示有課堂（年度、班期、期別、老師、日期）的資料

        $sql = "select a.seq, p.course_date, min(p.from_time) from_time, t.teacher as teacher, t.name, t.account_name as acct_name
                from hour_traffic_tax a join periodtime p on a.year=p.year and a.term=p.term and a.class_no=p.class_no and a.use_date=p.course_date 
                join room_use cr on a.year=cr.year and a.term=cr.term and a.class_no=cr.class_id and a.teacher_id=cr.teacher_id and cr.use_date = p.course_date and cr.use_period=p.id and cr.room_id=p.room_id 
                join teacher t on t.idno=cr.teacher_id and a.teacher_id = t.idno and t.teacher=cr.isteacher and a.isteacher = t.teacher
                where {$where} and 
                (a.year, a.class_no, a.term, a.teacher_id, a.use_date) in (select year, class_id, term, teacher_id, a.use_date from room_use)
                group by  a.seq, p.course_date, t.teacher, t.name, t.account_name	
                order by p.course_date, from_time, t.teacher desc, t.name, t.account_name ";

        $rs = $this->db->query($sql);
        $rs = $this->QueryToArray($rs);
        // $output[1]=$rs;
        for($j=0;$j<sizeof($rs);$j++){
            $seq_data=$rs[$j];
            $sql = "select * from hour_traffic_tax a where seq = '{$seq_data['seq']}'"; 
			$rs1 = $this->db->query($sql);
            $fields = $this->QueryToArray($rs1)[0];
            
			
		  // custom (b) by chiahua 如果狀態不是"請款確認"和"市庫支票"時，要再重新抓取講師的資料
		  $tb_type = $fields['teacher_bank_type'];
		  $tb_account = $fields['teacher_account'];
		  $tb_BANKID = $fields['teacher_bank_id'];
		  $isteacher = $fields['isteacher']; // 是否為講師(Y/N)

		  if($fields['status'] != '請款確認' && $fields['status'] != '市庫支票'){

			$tb_rs = $this->db->query("select name,bank_account as account,account_name as acct_name,bank_code as bankid,route as addr,county as city,district as subcity from teacher WHERE IDno = '{$fields['teacher_id']}' and TEACHER = '{$isteacher}'");
            $tb_row = $this->QueryToArray($tb_rs)[0];
            // $output[3]=$tb_row;

			$teacher_name = $tb_row['name']; // 姓名
			$teacher_acct_name = $tb_row['acct_name']; // 帳戶名稱
			$tb_account = $tb_row['account']; // 銀行帳號
			$tb_BANKID = $tb_row['bankid']; // 銀行代碼
            

            if(isset($cityArr[$tb_row['city']])) {
                $city = $cityArr[$tb_row['city']]; // 縣市
            }
            else {
                $city = ""; // 縣市
            }
            
            if(isset($subcityArr[$tb_row['city'] . '-'. $tb_row['subcity']])) {
                $subcity = $subcityArr[$tb_row['city'] . '-'. $tb_row['subcity']]; // 區
            }
            else {
                $subcity = "";
            }
            
            $teacher_addr = $tb_row['addr']; // 地址
            
            //再找出類型是銀行還是郵局
            if($this->QueryToArray($this->db->query("select memo from code_table where TYPE_ID=14 and ITEM_ID = '{$tb_BANKID}'")) == []) {
                $tb_type =  "";
            }
            else {
                $tb_type =  $this->QueryToArray($this->db->query("select memo from code_table where TYPE_ID=14 and ITEM_ID = '{$tb_BANKID}'"))[0]['memo'];
            }

			// 更新請款的銀行帳戶資料
		 	$bank_sql ="update hour_traffic_tax set TEACHER_NAME = '{$teacher_name}', teacher_bank_type = '{$tb_type}' , teacher_bank_id = '{$tb_BANKID}', teacher_account = '{$tb_account}', TEACHER_ACCT_NAME = '{$teacher_acct_name}', TEACHER_ADDR = '{$city}{$subcity}{$teacher_addr}' where TEACHER_ID = '{$fields['teacher_id']}' and YEAR = '{$year}' and CLASS_NO = '{$class_no}' and TERM = '{$term}' and STATUS != '請款確認' and STATUS != '市庫支票'";
			$this->db->query($bank_sql);

				$fields['teacher_name']    = $teacher_name;
				$fields['teacher_acct_name']    = $teacher_acct_name;
				$fields['teacher_addr']    = $city . $subcity . $teacher_addr;
                
		  }
		  // custom (e) by chiahua 如果狀態不是"請款確認"時，要再重新抓取講師的資料


        //   $col = ($col == '#ffffff') ? '#dcdcdc' : '#ffffff';
        //   echo '<tr height="30">';

        //   echo '<td align="center" bgcolor="' . $col . '">';
        //   if ($fields['STATUS'] == ""){
        //     echo '<input type="checkbox" value="' . $fields['SEQ'] . '" id="sel" name="sel">';
        //   }
        //   else{
        //     echo '<input type="checkbox" value="' . $fields['SEQ'] . '" id="sel" name="sel" disabled>';
        //   }

        //   echo '</td>';

        //   echo '<td align="center" bgcolor="' . $col . '">' . $fields['STATUS'] . '</td>';
        //   echo '<td align="center" value ="'.$fields['USE_DATE'].'" id="use_date'.$fields['SEQ'].'" bgcolor="' . $col . '">' . $fields['USE_DATE'] . '</td>';

        //   echo '<td align="center" bgcolor="' . $col . '">';
        //   if ($fields['STATUS'] == "已設定為不請款"){
        //     echo '<span onclick=modechg("' . $fields['SEQ'] . '","Y")><a href="#">設為請款</a></span>';
        //   }
        //   if ($fields['STATUS'] == ""){
        //     echo '<span onclick=modechg("' . $fields['SEQ'] . '","N")><a href="#">設為不請款</a></span>';
        //   }
        //   if ($fields['STATUS'] == "待確認"){
        //     echo '<span onclick=modechg("' . $fields['SEQ'] . '","A")><a href="#">取消確認</a></span>';
        //   }

        //   $re_sel1 = '';
        //   $re_sel2 = '';
        //   $re_sel3 = '';
         
        //   if($fields['REMARK'] == '領現金'){
        //     $re_sel1 = "selected";
        //   } elseif($fields['REMARK'] == '出席費'){
        //     $re_sel2 = "selected";
        //   } elseif($fields['REMARK'] == '監考費'){
        //     $re_sel3 = "selected";
        //   } 

        //   echo '<td align="center" bgcolor="' . $col . '">';
        //   echo '<select name="remark_'.$fields['SEQ'].'">
        //           <option value="無">無</option>
        //           <option value="領現金"'.$re_sel1.'>領現金</option>
        //           <option value="出席費"'.$re_sel2.'>出席費</option>
        //           <option value="監考費"'.$re_sel3.'>監考費</option>
        //         </select>';
        //   echo '</td>';

  
        //   echo '<td align="center"  value ="'.$fields['TEACHER_NAME'].'" id="thacher_name'.$fields['SEQ'].'" bgcolor="' . $col . '">' . $fields['TEACHER_NAME'] . '</td>';

		  // 取得講座的聘請類別
		  $description = $this->QueryToArray($this->db->query("select C.DESCRIPTION from teacher T left join code_table C on T.hire_type = C.ITEM_ID and C.TYPE_ID = '08' where T.IDno = '{$fields['teacher_id']}' and T.TEACHER = '{$isteacher}'"))[0]['DESCRIPTION'];
          $fields['description']    = $description;
        //   echo '<td align="center" bgcolor="' . $col . '">'.$description.'</td>';

		  //custom (b) by chiahua
		  // $bank_type = ($tb_type == 'bank' ? '銀行' : ($tb_type == 'post' ? '郵局' : ''));
        // 取得銀行名稱
        if($this->QueryToArray($this->db->query("select DESCRIPTION from code_table where TYPE_ID=14 and ITEM_ID = '{$tb_BANKID}'")) == []) {
            $bp_name = "";
        }
        else {
            $bp_name = $this->QueryToArray($this->db->query("select DESCRIPTION from code_table where TYPE_ID=14 and ITEM_ID = '{$tb_BANKID}'"))[0]['DESCRIPTION'];
        }
        $fields['bp_name']    = $bp_name;
        //   echo '<td align="left" bgcolor="' . $col . '">' . $bp_name . '</td>';
        //   echo '<td align="left" bgcolor="' . $col . '">' . $tb_BANKID . '</td>';
        //   echo '<td align="left" bgcolor="' . $col . '">' . $tb_account . '</td>';
		//   if ($fields['TEACHER_ACCT_NAME']!=$fields['TEACHER_NAME'])
		// 	echo '<td align="center" bgcolor="' . $col . '"><font color="red">' . $fields['TEACHER_ACCT_NAME'] . '</font></td>';
		//   else	
		//     echo '<td align="center" bgcolor="' . $col . '">' . $fields['TEACHER_ACCT_NAME'] . '</td>';
		//   // custom (e) by chiahua
        //   echo '<td align="left" bgcolor="' . $col . '">' . $fields['TEACHER_ADDR'] . '</td>';
        //   echo '<td align="left" value ="'.$fields['TEACHER_ID'].'" id="thacher_id'.$fields['SEQ'].'" bgcolor="' . $col . '">' . $fields['TEACHER_ID'] . '</td>';

        //   //時數
        //   echo '<td align="right" bgcolor="' . $col . '">' . $fields['HRS'] . '</td>';

		  // custom (b) by chiahua 重新抓取鐘點費，更新成最新狀態
		  if (trim($fields['status']) == "" || trim($fields['status']) == "待確認"){

			// custom (b) by chiahua 重新抓取課程的鐘點費類別，避免班期基本資料中的鐘點費類別有異動時，會抓不到對應的資料
			$get_ht_class_type = $this->QueryToArray($this->db->query("select ht_class_type from `require` where YEAR = {$year} and CLASS_NO = '{$class_no}' and TERM = '{$term}'"))[0]['ht_class_type'];

				$this->db->query("update hour_traffic_tax set HT_CLASS_TYPE = '{$get_ht_class_type}' where YEAR = {$year} and CLASS_NO = '{$class_no}' and TERM = '{$term}' and TEACHER_ID = '{$fields['teacher_id']}' and USE_DATE = '{$fields['use_date']}' " . (trim($fields['status']) == "" ? " and STATUS is null" : " and STATUS = '{$fields['status']}'"));
				$fields['ht_class_type'] = $get_ht_class_type;

			// custom (e) by chiahua 重新抓取課程的鐘點費類別，避免班期基本資料中的鐘點費類別有異動時，會抓不到對應的資料
			$sql = "";
			if ($fields['isteacher'] == 'Y') { //講師
				//$sql = "select count(*) from HOUR_FEE where CLASS_TYPE_ID = '{$fields['HT_CLASS_TYPE']}' and TEACHER_TYPE_ID = '{$fields['T_SOURCE']}' and ASSISTANT_TYPE_ID ".(trim($fields['A_SCOUCE']) == '' ? 'is null' : "='{$fields['A_SCOURCE']}'")." and TYPE = '1'";
				$count_fee = $this->QueryToArray($this->db->query("select count(*) as cnt from hour_fee where CLASS_TYPE_ID = '{$fields['ht_class_type']}' and TEACHER_TYPE_ID = '{$fields['t_source']}' and ASSISTANT_TYPE_ID ".(trim($fields['a_source']) == '' ? 'is null' : "='{$fields['a_source']}'")." and TYPE = '1'"))[0]['cnt'];
			}
			else { //助教
				//$sql = "select count(*) from HOUR_FEE where CLASS_TYPE_ID = '{$fields['HT_CLASS_TYPE']}' and ASSISTANT_TYPE_ID = '{$fields['T_SOURCE']}' and TYPE = '2'";
				$count_fee = $this->QueryToArray($this->db->query("select count(*) as cnt from hour_fee where CLASS_TYPE_ID = '{$fields['ht_class_type']}' and ASSISTANT_TYPE_ID = '{$fields['t_source']}' and TYPE = '2'"))[0]['cnt'];
            }

			//echo "sql:" . $sql;
			  //$sql = "select count(*) from HOUR_FEE where CLASS_TYPE_ID = '{$fields['HT_CLASS_TYPE']}' and TEACHER_TYPE_ID = '{$fields['T_SOURCE']}' and ASSISTANT_TYPE_ID ".(trim($fields['A_SCOUCE']) == '' ? 'is null' : "='{$fields['A_SCOURCE']}'")." and TYPE = '".($fields['ISTEACHER'] == 'Y' ? 1 : 2)."'";
              //echo "sql:". $sql;
			  if($count_fee == 1){

				  $rs_fee = $this->db->query("select * from hour_fee where CLASS_TYPE_ID = '{$fields['ht_class_type']}' and TEACHER_TYPE_ID = '{$fields['t_source']}' and ASSISTANT_TYPE_ID ".(trim($fields['a_source']) == '' ? 'is null' : "='{$fields['a_source']}'")." and TYPE = '".($fields['isteacher'] == 'Y' ? 1 : 2)."'");
				  $row_fee = $this->QueryToArray($rs_fee)[0];

				  // 單價和鐘點費和交通費都沒有被手動更新過才要自動更新
				  //if($fields['UNIT_HOUR_FEE_IS_CHANGED'] != 'Y' && $fields['HOUR_FEE_IS_CHANGED'] != 'Y' && $fields['TRAFFIC_FEE_IS_CHANGED'] != 'Y'){
				  if($fields['unit_hour_fee_is_changed'] == 'N')
					  $fields['unit_hour_fee']	= $row_fee['hour_fee'];
				  if($fields['hour_fee_is_changed'] == 'N')
					  $fields['hour_fee']		= $row_fee['hour_fee'] * $fields['hrs'];
				  if($fields['traffic_fee_is_changed'] == 'N')
					  $fields['traffic_fee']	= $row_fee['traffic_fee'];
				  //}
				  // $fields['TRAFFIC_FEE']	= $row_fee['TRAFFIC_FEE'];
				  $fields['hour_fee']		= $fields['hrs'] * $fields['unit_hour_fee']; // 鐘點費 = 時數 X 單價
				  $fields['subtotal']		= $fields['hour_fee'] + $fields['traffic_fee']; // 合計 = 鐘點費+交通費

				  // $this->db->query("update HOUR_TRAFFIC_TAX set UNIT_HOUR_FEE = {$fields['UNIT_HOUR_FEE']}, HOUR_FEE = {$fields['HOUR_FEE']}, SUBTOTAL = {$fields['SUBTOTAL']} where TEACHER_ID = '{$fields['TEACHER_ID']}' and YEAR = '{$year}' and CLASS_NO = '{$class_no}' and TERM = '{$term}'");

				  //$this->db->query("update HOUR_TRAFFIC_TAX set UNIT_HOUR_FEE = {$fields['UNIT_HOUR_FEE']}, HOUR_FEE = {$fields['HOUR_FEE']}, SUBTOTAL = {$fields['SUBTOTAL']} where SEQ = '{$fields['SEQ']}'");
				  $this->db->query("update hour_traffic_tax set UNIT_HOUR_FEE = {$fields['unit_hour_fee']}, HOUR_FEE = {$fields['hour_fee']}, SUBTOTAL = {$fields['subtotal']},TRAFFIC_FEE = {$fields['traffic_fee']} where SEQ = '{$fields['seq']}'");
				  //$sql = "update HOUR_TRAFFIC_TAX set UNIT_HOUR_FEE = {$fields['UNIT_HOUR_FEE']}, HOUR_FEE = {$fields['HOUR_FEE']}, SUBTOTAL = {$fields['SUBTOTAL']},TRAFFIC_FEE = {$fields['TRAFFIC_FEE']} where SEQ = '{$fields['SEQ']}'";
				  //echo "sql:". $sql;
			  }

		  }
		  // custom (e) by chiahua 重新抓取鐘點費，更新成最新狀態

          //單價
        //   echo '<td align="right" bgcolor="' . $col . '">';
        //   if ($fields['STATUS'] == "" || $fields['STATUS'] == "待確認"){ // custom by chiahua 加上待確認
        //     echo '<span onclick=amtChg("' . $fields['SEQ'] . '","1")><a href="#">' . $fields['UNIT_HOUR_FEE'] . '</a></span>';
        //   }
        //   else{
        //     echo '<span onclick=alert("目前狀態不允許修改")><a href="#">' . $fields['UNIT_HOUR_FEE'] . '</a></span>';
        //   }
        //   echo '</td>';

          //鐘點費
        //   echo '<td align="right" bgcolor="' . $col . '">';
        //   if ($fields['STATUS'] == "" || $fields['STATUS'] == "待確認"){ // custom by chiahua 加上待確認
        //     echo '<span onclick=amtChg("' . $fields['SEQ'] . '","2")><a href="#">' . $fields['HOUR_FEE'] . '</a></span>';
        //   }
        //   else{
        //     echo '<span onclick=alert("目前狀態不允許修改")><a href="#">' . $fields['HOUR_FEE'] . '</a></span>';
        //   }
        //   echo '</td>';

          //交通費
        //   echo '<td align="right" bgcolor="' . $col . '">';
        //   if ($fields['STATUS'] == "" || $fields['STATUS'] == "待確認"){ // custom by chiahua 加上待確認
		// 	// custom by chiahua 交通費遇到負一時，直接顯示0(不可以修改)，且合計欄位不可以將負一算進去
		// 	if($fields['TRAFFIC_FEE'] == "-1"){
		// 		echo '0';
		// 		$fields['TRAFFIC_FEE'] = 0;
		// 	}
		// 	else{
		// 		echo '<span onclick=tfeechg("' . $fields['SEQ'] . '")><a href="#">' . $fields['TRAFFIC_FEE'] . '</a></span>';
		// 	}
        //   }
        //   else{
		// 	// custom by chiahua 交通費遇到負一時，直接顯示0(不可以修改)，且合計欄位不可以將負一算進去
			if($fields['traffic_fee'] == "-1"){
				$fields['traffic_fee'] = 0;
			}
		// 	else
		// 		echo '<span onclick=alert("目前狀態不允許修改")><a href="#">' . $fields['TRAFFIC_FEE'] . '</a></span>';
        //   }
        //   echo '</td>';
		  // custom (b) chiahua 最後再重新計算一次總合
          $fields['subtotal'] = $fields['hour_fee'] + $fields['traffic_fee'];
		  $this->db->query("update hour_traffic_tax set SUBTOTAL = {$fields['subtotal']} where SEQ = '{$fields['seq']}'");
		  // custom (b) chiahua 最後再重新計算一次總合
        //   echo '<td align="right" bgcolor="' . $col . '">' . $fields['SUBTOTAL'] . '</td>';
        //   echo "</tr>";
          $i= $i + 1;
          $output[$j]=$fields;
        }

        return $output;
    }
    public function reenterPayDetailData($year,$class,$term,$startdate,$enddate){
            // 取得可以更新的日期資料 not 教務組
            $rs = $this->db->query("
                select distinct use_date
                from hour_traffic_tax
                where year = ".$this->db->escape(addslashes($year))."
                and class_no = ".$this->db->escape(addslashes($class))."
                and term = ".$this->db->escape(addslashes($term))."
                and use_date >= date(".$this->db->escape(addslashes($startdate)).")
                and use_date <= date(".$this->db->escape(addslashes($enddate)).")
                and (status is null or status='' or status = '待確認') and teacher_id not in ('A111111113')	
                and seq not in (select seq from hour_app)");
            $rs = $this->QueryToArray($rs);
            $updateLists = '';
            if($rs){
                for ($i=0; $i < sizeof($rs); $i++) { 
                    $row=$rs[$i];
                    $hLists[] = $row['use_date'];
                    $updateLists .= $row['use_date'] . '：重新轉入完成\n\n';
                }
            }
            if(sizeof($rs)==0){
                return("無資料");
            }

            $useDateAry = $hLists;
            for($ui=0;$ui<count($useDateAry);$ui++){
                $useDateAry[$ui] = $this->db->escape(addslashes($useDateAry[$ui]));
            }
            $useDateAry = implode(",",$useDateAry);

            // 可能之前沒有轉資料在HOUR_TRAFFIC_TAX
            // check use_date in room_use but not in HOUR_TRAFFIC_TAX
            $rs = $this->db->query("select distinct use_date
                    from room_use 
                    where year = ".$this->db->escape(addslashes($year))." and class_id = ".$this->db->escape(addslashes($class))." and term = ".$this->db->escape(addslashes($term))."
                    and use_date between date(".$this->db->escape(addslashes($startdate)).") and date(".$this->db->escape(addslashes($enddate)).") 
                    and use_date not IN ({$useDateAry}) and teacher_id is not null  order by use_date");
            $rs = $this->QueryToArray($rs);
            if ($rs) {
                for ($i=0; $i < sizeof($rs); $i++) { 
                    $row=$rs[$i];
                    $countNotUpdate = $this->QueryToArray($this->db->query("select count(*) as cnt from hour_traffic_tax 
                                        where year = ".$this->db->escape(addslashes($year))." and class_no = ".$this->db->escape(addslashes($class))." and term = ".$this->db->escape(addslashes($term))."
                                        and use_date = date(".$this->db->escape(addslashes($row['use_date'])).") and ((status is not null or status not in ('待確認')) 
                                        or seq in (select seq from hour_app))"))[0]['cnt'];
                    if ($countNotUpdate==0) { //請款狀態是未確認且沒有流水號才可重新轉入
                        $hLists[] = $row['use_date'];
                        $updateLists .= $row['use_date'] . '：重新轉入完成\n\n';
                    }
                }
            }
            // sort hLists
            if ($hLists) {
                sort($hLists);
            }

            // 取得不可以更新的日期資料
            $rs = $this->db->query("
                select distinct use_date, app_seq
                from hour_app
                left join hour_traffic_tax on hour_app.seq = hour_traffic_tax.seq
                where year = ".$this->db->escape(addslashes($year))."
                and class_no = ".$this->db->escape(addslashes($class))."
                and term = ".$this->db->escape(addslashes($term))."
                and use_date >= date(".$this->db->escape(addslashes($startdate)).")
                and use_date <= date(".$this->db->escape(addslashes($enddate)).")");
            $rs = $this->QueryToArray($rs);
            $noneUpdateLists = '';
            if($rs){
                for ($i=0; $i < sizeof($rs); $i++) { 
                    $row=$rs[$i];
                    $noneUpdateLists .= $row['use_date'] . '：已有流水號：' . $row['app_seq'] . '，若要重轉，請到13B先刪除該流水號\n\n';
                }
            }

            if($hLists){

                $_POST['hLists'] = implode('|', $hLists);

                $query_year = $year;
                $query_class_no = $class;
                $query_term = $term;
                $query_hlists = trim($_POST['hLists']);

                if($query_hlists != ''){
                    $getUseDate = explode('|', $query_hlists);
                    $cnt = count($getUseDate);
                    for($x = 0; $x < $cnt; $x++) {
                        $query_use_date = $getUseDate[$x];

                            //鐘點費(刪舊)
                            $sql = "delete from hour_traffic_tax where (year, class_no, term, use_date) in " .
                                "( " .
                                "  select year, class_id as class_no, term, use_date " .
                                "  from room_use where year = ".$this->db->escape(addslashes($query_year))." and class_id = ".$this->db->escape(addslashes($query_class_no))." and term = ".$this->db->escape(addslashes($query_term))." and use_date = date(".$this->db->escape(addslashes($query_use_date)).") " .
                                "  group by year, class_id, term, use_date " .
                                ")";
                            $this->db->query($sql);

                            //鐘點費(新增)
                            $sql = "insert into hour_traffic_tax (year, class_no, term, class_name, start_date, end_date, teacher_id, teacher_name, 
                            teacher_bank_type, teacher_bank_id, teacher_account, teacher_acct_name, teacher_addr, hrs, unit_hour_fee, 
                            traffic_fee, T_source, A_source, HT_class_type, use_date, IsTeacher, worker_id, hour_fee, subtotal, tax_rate, tax, 
                            aftertax, HOUR_FEE_IS_CHANGED,UNIT_HOUR_FEE_IS_CHANGED,traffic_fee_is_changed) 
                            
                            select a.year, a.class_no, a.term, b.class_name, b.start_date1 as start_date, b.end_date1 as end_date,
                             a.teacher_id, nvl(c1.name,c2.name), d.memo,
                            nvl(c1.bank_code,c2.bank_code) as bankid, nvl(c1.bank_account,c2.bank_account) as account,
                            nvl(c1.account_name,c2.account_name) as acct_name, nvl(c1.route,c2.route) as addr, a.hrs,
                            nvl(nvl(e1.hour_fee, e2.hour_fee),0) as unit_hour_fee,
                            case when nvl(nvl(e1.traffic_fee, e2.traffic_fee),0) < 0 then 0 else nvl(nvl(e1.traffic_fee, e2.traffic_fee),0) end as traffic_fee
                            , (case when a.IsTeacher = 'N' then c2.hire_type else c1.hire_type end) as T_source, c2.hire_type as A_sourse, b.HT_class_type
                            , a.use_date, a.IsTeacher, b.WORKER, 0,0,0,0,0,'N', 'N' , 'N'
                            from ( 
                            select year, class_id as class_no, term, use_date, teacher_id, nvl(IsTeacher,'N') as IsTeacher, sum(nvl(hrs,0)) as hrs
                             from room_use where year = ".$this->db->escape(addslashes($year))." and class_id = ".$this->db->escape(addslashes($class))." and term = ".$this->db->escape(addslashes($term))." and use_date = date_format(".$this->db->escape(addslashes($query_use_date)).",'%Y-%m-%d') 
                             and teacher_id is not null group by year, class_id, term, use_date, teacher_id, nvl(IsTeacher,'N') order by use_date ) a 
                             left join `require` b on a.year = b.year and a.class_no = b.class_no and a.term = b.term 
                             left join teacher c1 on a.teacher_id = c1.idno and c1.Teacher_type = '1' and (c1.del_flag is null or c1.del_flag not in ('Y')) 
                             left join teacher c2 on a.teacher_id = c2.idno and c2.Teacher_type = '2' and (c2.del_flag is null or c2.del_flag not in ('Y')) 
                             left join code_table d on nvl(c1.bank_code,c2.bank_code) = d.item_id and d.type_id = '14' 
                             left join hour_fee e1 on b.ht_class_type = e1.class_type_id and c1.hire_type = e1.teacher_type_id and e1.type = '1'  and a.IsTeacher = 'Y' 
                             left join hour_fee e2 on b.ht_class_type = e2.class_type_id and c2.hire_type = e2.teacher_type_id 
                             and c2.hire_type = e2.assistant_type_id and e2.type = '2' and a.IsTeacher = 'N' 
                             
                             where d.memo is not null" ;
                                $this->db->query($sql);

                            //鐘點費(計算1)
                            $sql = "update hour_traffic_tax set " .
                                "hour_fee = (unit_hour_fee * hrs), " .
                                "subtotal = (unit_hour_fee * hrs) + traffic_fee, " .
                                "tax_rate = case when (unit_hour_fee * hrs) + traffic_fee > 40000 then 5 else 0 end, " .
                                "unit_hour_fee_is_changed='N', hour_fee_is_changed='N', traffic_fee_is_changed='N'" . 
                                "where year = ".$this->db->escape(addslashes($query_year))." and class_no = ".$this->db->escape(addslashes($query_class_no))." and term = ".$this->db->escape(addslashes($query_term))." and use_date = date_format(".$this->db->escape(addslashes($query_use_date)).",'%Y-%m-%d')";
                            $this->db->query($sql);

                            //鐘點費(計算2)
                            $sql = "update hour_traffic_tax set " .
                                "tax = case when (hour_fee * tax_rate/100) < 2000 then 0 else (hour_fee * tax_rate/100) end, " .
                                "aftertax = subtotal - (case when (hour_fee * tax_rate/100) < 2000 then 0 else (hour_fee * tax_rate/100) end) " .
                                "where year = ".$this->db->escape(addslashes($query_year))." and class_no = ".$this->db->escape(addslashes($query_class_no))." and term = ".$this->db->escape(addslashes($query_term))." and use_date = date_format(".$this->db->escape(addslashes($query_use_date)).",'%Y-%m-%d')";
                            //$this->db->query($sql);
                    }


                }

        }
        return("重新轉入成功");
    }
    public function confirmPayDetailData($selectlist,$chklist,$tex_data,$umoney,$hmoney,$tmoney){
        $tex_data_arr = explode(",", substr($tex_data,0,-1));
        $result ="ok";

        foreach ($tex_data_arr as $value) {
            $teacher_data_arr = explode("_", $value);

            $sql="
            SELECT
            COUNT(1) AS rows_num
            FROM
            (
                SELECT
                    a.year,use_date,TEACHER_ACCT_NAME,IS_CANCEL,TRAFFIC_FEE
                FROM
                    hour_traffic_tax a
                JOIN `require` b ON b.YEAR = a .YEAR
                AND b.TERM = a .TERM
                AND b.CLASS_NO = a .CLASS_NO
            )ht
            WHERE
            date(ht.use_date) = ".$this->db->escape(addslashes($teacher_data_arr[1]))."
            AND ht.TEACHER_ACCT_NAME = ".$this->db->escape(addslashes($teacher_data_arr[0]))."
            AND IFNULL(ht.IS_CANCEL, '0') = '0'
            AND ht.TRAFFIC_FEE > 0
            ";
            $db_rs =  $this->db->query($sql);
            $db_rs = $this->QueryToArray($db_rs)[0]['rows_num'];	
            
            if($db_rs>1){ //當交通費請領金額大於0金額有一筆以上時，判斷為重覆請領
                $sql="
                    SELECT
                        ht.class_no,ht.year,ht.term
                    FROM
                    (
                        SELECT
                            a.class_no,a.year,a.term,a.TRAFFIC_FEE,a.TEACHER_ACCT_NAME,b.IS_CANCEL,a.USE_DATE
                        FROM
                            hour_traffic_tax a
                        JOIN `require` b ON b.year = a.year
                        AND b.TERM = a .TERM
                        AND b.CLASS_NO = a .CLASS_NO
                    )ht
                    WHERE
                    date(ht.use_date) = ".$this->db->escape(addslashes($teacher_data_arr[1]))."
                    AND ht.TEACHER_ACCT_NAME = ".$this->db->escape(addslashes($teacher_data_arr[0]))."
                    AND IFNULL(ht.IS_CANCEL, '0') = '0' 
                    AND ht.TRAFFIC_FEE > 0
                    ";

                $row = $this->db->query($sql);
                $row = $this->QueryToArray($row);
                $data = $row;
                $belong_cnt = 0;
                $other_cnt = 0;
                
                for($i=0;$i<count($data);$i++){
                    $sql_belong = sprintf("SELECT
                                                b.BELONGTO
                                            FROM
                                            room_use A
                                            JOIN classroom b ON A .room_id = b.room_id
                                            WHERE
                                                A .class_id = %s
                                            AND A . YEAR = %s
                                            AND A .term = %s
                                            AND date(A .use_date) = %s
                                            AND A .TEACHER_ID = %s",
                                            $this->db->escape(addslashes($data[$i]['class_no'])),
                                            $this->db->escape(addslashes($data[$i]['year'])),
                                            $this->db->escape(addslashes($data[$i]['term'])),
                                            $this->db->escape(addslashes($teacher_data_arr[1])),
                                            $this->db->escape(addslashes($teacher_data_arr[2]))
                                        );

                    $db_belong =  $this->QueryToArray($this->db->query($sql_belong))[0]['BELONGTO'];
                    if($db_belong == '68000'){
                        $belong_cnt++;
                    } elseif($db_belong == '68001') {
                        $other_cnt++;
                    }
                }

                if($belong_cnt > 0 && $other_cnt > 0){

                } else {
                    $result =  "講座[ ". $teacher_data_arr[0] ." ]，於".$teacher_data_arr[1]."同一天內交通費重複領取。請先進行該講座的交通費修改後，再執行本作業。";
                    break;
                }
                
            }
        }

        if($result!="ok"){
            return $result;
        }else{
            // echo($umoney);

            $selectAll  = $selectlist;
            $arry = explode(",,",$selectAll);
            $uarry = explode(",,",$umoney);
            $harry = explode(",,",$hmoney);
            $tarry = explode(",,",$tmoney);
            for ($i=0;$i<count($arry);$i++){
            if ($arry[$i]!=""){
                $sAry = explode("_", $arry[$i]);
                $uAry = explode("_", $uarry[$i]);
                $hAry = explode("_", $harry[$i]);
                $tAry = explode("_", $tarry[$i]);
                // ,unit_hour_fee_is_changed='Y', hour_fee_is_changed='Y', traffic_fee_is_changed='Y'
                $sql = "update hour_traffic_tax set remark=".$this->db->escape(addslashes($sAry[2])).", unit_hour_fee=".$this->db->escape(addslashes($uAry[2])).", hour_fee=".$this->db->escape(addslashes($hAry[2])).", traffic_fee=".$this->db->escape(addslashes($tAry[2]))." where seq=".$this->db->escape(addslashes($sAry[1]))."";
                $this->db->query($sql);
            }
            }
            
            $saveAll  = $chklist;
            $arry = explode(",,",$saveAll);
            for ($i=0;$i<count($arry);$i++){
            if ($arry[$i]!=""){
                $tmp = "待確認";
                $sql = "update hour_traffic_tax set status='{$tmp}' where seq=".$this->db->escape(addslashes($arry[$i]))."";
                $this->db->query($sql);
            }
            }
            return("待確認成功");
        }

    }
    public function invoicePayDetailData($editOne,$editValue){

        $cnt =  $this->QueryToArray($this->db->query("select count(*) as cnt from hour_app where seq=".$this->db->escape(addslashes($editOne))))[0]['cnt'];

        if ($cnt>0) {
            return "此筆資料已有流水號，不可變更狀態!!";
        } else {
            $tmp = "";
            if ($editValue == "Y"){
                $tmp = "";
            }
            if ($editValue == "N"){
                $tmp = "已設定為不請款";
            }
            if ($editValue == "A"){
                $tmp = "";
            }

            $sql = "update hour_traffic_tax set status=".$this->db->escape(addslashes($tmp))." where seq=".$this->db->escape(addslashes($editOne))."";
            $this->db->query($sql);
            return("成功");
        }
    }

    public function searchTrafficList() {
        $sql = "select * from code_table where type_id = '16' order by item_id";
        $rs = $this->db->query($sql);
        $data = $this->QueryToArray($rs);

        return $data;
    }
}

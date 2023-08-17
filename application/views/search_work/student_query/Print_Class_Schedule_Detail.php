<?php

//include "init.inc.php";
include ("fckeditor/fckeditor.php") ;  
ini_set("display_errors",1) ;
//$db->debug = true;
//將作業訊息登錄至系統日誌
//sys_log_insert(basename(__FILE__));

//$db->debug = true;

//-------- session變數初始化 START:
if (!function_exists('htmlspecialchars_decode')) {
		function htmlspecialchars_decode($string, $style=ENT_COMPAT) {
			$translation = array_flip(get_html_translation_table(HTML_SPECIALCHARS, $style));
			if ($style === ENT_QUOTES) {
					$translation['&#039;'] = '\'';
			}
			return strtr($string, $translation);
		}
	}
	                        
$year = $_REQUEST['year'];//年度
$class_no = $_REQUEST['class_no'];//班期代碼
$term = $_REQUEST['term'];//期別
$TMP_SEQ = $_REQUEST['tmp_seq'];
//$TMP_SEQ = 1;
//echo $TMP_SEQ;
//$item_id = ($_REQUEST['item_id']);
$item_id='';
if($item_id =='') $item_id =1;
$query_cond_string = sprintf("`require`.year=%s AND `require`.class_no=%s and `require`.term=%s ", $this->db->escape(addslashes($year)),$this->db->escape(addslashes($class_no),$this->db->escape(addslashes($term))));
//$query_detail_string = sprintf("P.year='%s' AND P.class_no='%s' and P.term='%s' ", $year,$class_no,$term);
//$query_detail_string = sprintf("a.year='%s' AND a.class_id=trim(ltrim('%s')) and a.term='%s' ", $year,$class_no,$term);
$query_detail_string = sprintf("a.use_date is not null and a.year=%s AND a.class_id=trim(ltrim(%s)) and a.term=%s ", $this->db->escape(addslashes($year)),$this->db->escape(addslashes($class_no)),$this->db->escape(addslashes($term)));
//查詢條件:
$query_cond = array( 
  'year'=> $year,
  'class_no' => $class_no,
  'term' => $term
);
//查詢
$sql = sprintf("
	select `require`.CLASS_CONTENT AS CLASS_CONTENT2 ,`require`.*,BS_user.name as FIRST_NAME,'' as LAST_NAME,BS_user.office_tel,code_table.DESCRIPTION,code_table.ADD_VAL1,code_table.ADD_VAL2 
	from `require` 
	left join BS_user on `require`.WORKER=BS_user.idno
	left join code_table on code_table.TYPE_ID='26' and BS_user.idno =  code_table.ITEM_ID 
	where %s	 
	",
	$query_cond_string
);        

$rs = $this->db->query($sql);
$rs = QueryToArray($rs);

$content = "";
for ($i=0; $i < sizeof($rs); $i++) { 
	$arr=$rs[$i];
	$data['rows'][] = $arr;
	#$content=$arr["CLASS_CONTENT2"];
	$WORKER = $arr["FIRST_NAME"];
	$OFFICE_TEL = $arr["office_tel"];
	$RANGE_REAL = $arr["range_real"]; // 實體時數
	$RANGE_INTERNET = $arr["range_internet"]; // 線上實數
	$DESCRIPTION = $arr["DESCRIPTION"];
	$ADD_VAL1 = $arr["ADD_VAL1"]; // 承辦人分機
	$ADD_VAL2 = $arr["ADD_VAL2"]; // 代理人分機
}    
  
if(empty($content)){
	#$sql ="select CONTENT as  CONTENT from TEMPLATE where item_id='01' and tmp_seq = {$item_id} ";
	
	$sql_cancel = sprintf("select count(1) cnt from `require` where class_no = %s and term = %s and year = %s and IFNULL(is_cancel, '0') = '1' ",$this->db->escape(addslashes($class_no)),$this->db->escape(addslashes($term)),$this->db->escape(addslashes($year)));
	$rs = $this->db->query($sql_cancel);
	$rs = QueryToArray($rs);
	$cancel_cnt = $rs[0]['cnt'];

	if($cancel_cnt > 0){
		$sql = "select * from(select @rownum:=@rownum+1 as ROWNUM,t.BODY as CONTENT from (select a.* from mail_log a
				where a.year = ".$this->db->escape(addslashes($year))."
					and a.class_no = ".$this->db->escape(addslashes($class_no))."
					and a.term = ".$this->db->escape(addslashes($term))."
					and MAIL_TYPE = '8'
				order by a.SEQ desc) t)a
				where a.ROWNUM <= 1";
	} else {
		$sql = "select * from(select @rownum:=@rownum+1 as ROWNUM,t.BODY as CONTENT from (select @rownum := 0,a.* from mail_log a
				where a.year = ".$this->db->escape(addslashes($year))."
					and a.class_no = ".$this->db->escape(addslashes($class_no))."
					and a.term = ".$this->db->escape(addslashes($term))."
					and MAIL_TYPE = '1'
				order by a.SEQ desc) t)a
				where a.ROWNUM <= 1";
	}
	

	$rs = $this->db->query($sql);
	$rs = QueryToArray($rs);
	for ($i=0; $i < sizeof($rs); $i++) {
		$arr=$rs[$i]; 
		$content = htmlspecialchars_decode($arr["CONTENT"]);
		$content = stripslashes($arr["CONTENT"]);
		/*
		$del_str1="您好：";
		$del_number="您好：</span></p>";
		$del_str2 = "公務人員訓練處　 教務組";
		$del_number2 ="本處地址";

		$content = substr($content,strpos($content ,$del_str1)+strlen($del_number),strpos($content ,$del_str2)-1);
		*/
		$content = str_replace("本信件為系統自動發送，請不要直接回覆", "", $content);
	}
}
else{
	$content = htmlspecialchars_decode($content);
}         
function QueryToArray($query){
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

function get_room_count($query_detail_string)
{
	global $db, $debug_mode; 
	$sql = "select count(*) as count from (select a.room_id  from room_use a where {$query_detail_string} group by room_id )";

	$room_count = $db->GetOne($sql);  
	
	return $room_count;
	
}


function get_list($query_detail_string)
{
		$weekarray=array("日","一","二","三","四","五","六");  
    
		$sql = sprintf("
		select rank() over(partition by use_date1, cday order by ltime, classroom_name) as key, z.* from 
		(
		select me.tt,a.use_period,a.use_date as use_date2,a.use_date as use_date1,
    a.use_id,a.year,a.class_id,a.term,to_char(a.use_date,'mm/dd') as use_date,to_char(a.use_date,'D') as cday,min(b.from_time) || max(b.to_time) as ltime
    ,c.description as class_name,IFNULL(cr.room_name, cr.room_sname) as classroom_name ,r.contactor,r.tel
    from room_use a 
    left outer join (
			select wmsys.wm_concat(use_period) as dd ,tt,use_date from ( 
			select  use_period,use_date,max(tt) as tt from (
			select rank() over(partition by use_date, use_period order by key) as key,use_period,wmsys.wm_concat(teacher_id) over(partition by use_date, use_period order by key)  as tt  ,use_date 
			from ( 
			select rank() over(partition by use_date, use_period order by teacher_id) as key,a.use_period,a.teacher_id,use_date 
			from room_use a 
			where 1=1 and  %s
			group by a.use_period,a.teacher_id,a.use_date 
			order by a.use_date,a.use_period,a.teacher_id ,key
			)  order by use_date,use_period 
			) group by use_period,use_date
			) group by tt,use_date order by use_date,dd
                		) me on a.use_period in (select regexp_substr(me.dd,'[^,]+',1,level) from dual connect by regexp_substr(me.dd,'[^,]+',1,level) is not null) and a.use_date=me.use_date
    left outer join periodtime b on a.use_period=b.id and a.year=b.year and a.term=b.term and a.class_id=b.class_no and me.use_date = b.course_date
    left outer join code_table c on a.use_id=c.item_id and c.type_id='17'
    left outer join venue_information cr on cr.room_id=a.room_id
    left outer join `require` r on a.year=r.year and a.term=r.term and a.class_id=r.class_no
		where 1=1 and  %s
		group by me.tt,a.use_period,a.use_date,a.use_date,a.use_id,a.year,a.class_id,a.term,a.use_date,c.description,IFNULL(cr.room_name, cr.room_sname),r.contactor,r.tel
		Order by a.year,a.class_id,a.term,a.use_date,min(b.from_time), a.use_period
		) z
		",
		$query_detail_string,
		$query_detail_string
		);
	
	global $db, $debug_mode; 
	//echo "sql:".$sql;
	//exit;
	$rs = $this->db->query($sql);
	
	if( $debug_mode && $db->ErrorMsg() ):
		db_error($sql);
		exit;
	endif;
	$list = array();
	
	while ($arr = $rs->FetchRow()):
		$arr["CDAY"] = $weekarray[$arr["CDAY"]-1];
		if($arr["LTIME"]!="")
		$arr["LTIME"] = substr($arr["LTIME"],0,2).":".substr($arr["LTIME"],2,2)."~".substr($arr["LTIME"],4,2).":".substr($arr["LTIME"],6,2);
		
		$sql =  "select  IFNULL(d.ALIAS,d.name)  as name,d.teacher_type as TEACHER,d.teacher_type as ASSISTANT,a.title,a.sort 
						 from room_use a left join teacher d on a.teacher_id=d.id  and a.isteacher=d.teacher_type
						 where a.year=".$this->db->escape(addslashes($arr["year"]))." AND a.class_id=".$this->db->escape(addslashes($arr["CLASS_ID"]))." and a.term=".$this->db->escape(addslashes($arr["term"]))." and a.use_id=".$this->db->escape(addslashes($arr["USE_ID"]))." and a.use_date=to_date(".$this->db->escape(addslashes($arr["USE_DATE2"])).",'yyyy-mm-dd') 
						 and a.teacher_id in (select regexp_substr(".$this->db->escape(addslashes($arr["TT"])).",'[^,]+',1,level) from dual connect by regexp_substr(".$this->db->escape(addslashes($arr["TT"])).",'[^,]+',1,level) is not null)
						 group by d.another_name,d.name,d.teacher_type,a.title,a.sort  order by d.teacher_type desc ,a.sort asc,d.teacher_type ";
		
		$rs1 = $this->db->query($sql);
		
		while ($arr1 = $rs1->FetchRow()):
			
			if($arr1 ["NAME"]=="教務組" || $arr1 ["TITLE"]=="無"){
				$arr["NAME"] .= $arr1 ["NAME"]."<br>";
			}
			elseif($arr1 ["TITLE"]!=""){
				$arr["NAME"] .= $arr1 ["NAME"]." ".$arr1 ["TITLE"]."<br>";
			}
			
			else{
				if($arr1 ["TEACHER"]=='Y'){
					$arr["NAME"] .= $arr1 ["NAME"]." "."老師"."<br>";
				}
				if($arr1 ["ASSISTANT"]=='Y'){
					$arr["NAME"] .= $arr1 ["NAME"]." "."(助)"."<br>";
				}
			
			}
		endwhile;
		if($arr["NAME"] ==''){
			$t_count=$db->GetOne("select count(*) from code_table where TYPE_ID = '17' and add_val2 = 'Y'  and item_id = '{$arr["USE_ID"]}'");
			if($t_count>0)
			$arr["NAME"] = '教務組';
			
			
		}
	
		if(($list['rows'][count($list['rows'])-1]["USE_DATE"]==$arr["USE_DATE"])&&($list['rows'][count($list['rows'])-1]["NAME"]==$arr["NAME"])&&($list['rows'][count($list['rows'])-1]["CLASS_NAME"]==$arr["CLASS_NAME"]))
		{
			
			$list['rows'][count($list['rows'])-1]["LTIME"] = substr($list['rows'][count($list['rows'])-1]["LTIME"],0,6).substr($arr["LTIME"],6,5);
		}
		else
		$list['rows'][] = $arr;
		
		
		
	endwhile;
	
	return $list;
}

function get_Classroom_Name_List($query_detail_string)
{
		$sql = sprintf("
		select distinct a.room_id,c.room_name as name 
    from room_use a
    left outer join periodtime b on a.use_period=b.id
    left outer join venue_information c on a.room_id=c.room_id
		Where 1=1 and  %s
		",
		
		$query_detail_string
		);
		//echo $sql;
	global $db, $debug_mode;
	$rs = $this->db->query($sql);
	if( $debug_mode && $db->ErrorMsg() ):
		db_error($sql);
		exit;
	endif;
	$list = array();
	while ($arr = $rs->FetchRow()):
		$list[$arr['COURSE_CODE']] = $arr;
	endwhile;
	
	return $list;
}

function get_mixlist($year,$class_no,$term)
{
		$sql="select CLASS_NAME,TEACHER_NAME,PLACE,to_char(start_date,'mm/dd') as start_date,to_char(end_date,'mm/dd') as end_date from `require`_online where year=".$this->db->escape(addslashes($year))." and class_no=".$this->db->escape(addslashes($class_no))." and term=".$this->db->escape(addslashes($term))." ORDER BY ID";
		//echo $sql;
	global $db, $debug_mode;
	$rs = $this->db->query($sql);
	if( $debug_mode && $db->ErrorMsg() ):
		db_error($sql);
		exit;
	endif;
	$list = array();
	while ($arr = $rs->FetchRow()):
		$list['rows'][] = $arr;
	endwhile;
	
	return $list;
}


?>
<?php if($TMP_SEQ=='1') { ?>
<div id="edit_form" style="margin-left:50px;">
	<form action='edit_class_schedule.php' name='query_form' method='post' >
		<table  style='width:100%'>
			<tr>
				<td><font size="4" face="標楷體">
					一、承辦人：<? echo ($WORKER != '' ? htmlspecialchars($WORKER . "(分機 {$ADD_VAL1})", ENT_HTML5|ENT_QUOTES) : ""); ?><? echo ($DESCRIPTION != '' ? htmlspecialchars("、代理人：". $DESCRIPTION . "(分機 {$ADD_VAL2})。", ENT_HTML5|ENT_QUOTES) : ""); ?>
					<br>
					<?php 
					$sel_number = QueryToArray($this->db->query("select count(*) as cnt from online_app where year=".$this->db->escape(addslashes($year))." and class_no = ".$this->db->escape(addslashes($class_no))." and term=".$this->db->escape(addslashes($term))." and yn_sel  in ('3','8','1')"))[0]['cnt']; 
					?>
					二、研習人數 <?=htmlspecialchars($sel_number, ENT_HTML5|ENT_QUOTES);?>人；研習總時數 <?= ($RANGE_REAL + $RANGE_INTERNET);?>小時
					<?= ($RANGE_INTERNET > 0 ? htmlspecialchars("(實體時數 {$RANGE_REAL}小時、線上時數 {$RANGE_INTERNET}小時)", ENT_HTML5|ENT_QUOTES) : ""); ?>。
					</font>
				<?php				
					$worker_mail = QueryToArray($this->db->query("select IFNULL(C.co_empdb_email,C.email) as workmail from `require` a LEFT JOIN BS_user C ON a.WORKER = C.idno  where a.year=".$this->db->escape(addslashes($year))." and a.class_no=".$this->db->escape(addslashes($class_no))." and a.term=".$this->db->escape(addslashes($term))." "))[0];
					$range = QueryToArray($this->db->query("select `range` as ran from `require` where year=".$this->db->escape(addslashes($year))." and class_no=".$this->db->escape(addslashes($class_no))." and term=".$this->db->escape(addslashes($term))." "))[0];
					$true_count = QueryToArray($this->db->query("select count(*) as cnt from online_app where year=".$this->db->escape(addslashes($year))." and class_no=".$this->db->escape(addslashes($class_no))." and term=".$this->db->escape(addslashes($term))." and yn_sel  in ('3','8','1') "))[0];
					$content =str_replace("@@@@",$worker_mail['workmail'],$content);
					$content =str_replace("@@@",$true_count['cnt'],$content);
					$content =str_replace("@@",$range['ran'],$content);
					$sBasePath = '/base/admin/search_work/course_record_student?year='.htmlspecialchars($year, ENT_HTML5|ENT_QUOTES).'&class_no='.htmlspecialchars($class_no, ENT_HTML5|ENT_QUOTES).'&term='.htmlspecialchars($term, ENT_HTML5|ENT_QUOTES).'&tmp_seq=0&act=detail' ;
					
					$oFCKeditor = new FCKeditor('FCKeditor1') ;
					$oFCKeditor->BasePath	= $sBasePath ;
					$oFCKeditor->Height	= 500 ;
					$oFCKeditor->Value  = trim(stripslashes($content));
					$oFCKeditor->Create() ;
					?>
				</td>
			</tr>
		<tr id="btn_print">
				<td align="center">
						<input type='button' name="btnSave" id="btnSave" value='儲存' onclick='submit();'  class='button'/>
			    	 <input type='button' name='btnprint' id='btnprint' value='列印' onclick='printScreen2();' class='button' />
			    	 <!--<input type='button' name='btnCancel' id='btnCancel' value='取消' onclick='btn_back();' class='button' />-->
			    	 <input type='hidden' name='year' id='year' value='<? echo htmlspecialchars($year, ENT_HTML5|ENT_QUOTES) ; ?>' />
			    	 <input type='hidden' name='class_no' id='class_no' value='<? echo htmlspecialchars($class_no, ENT_HTML5|ENT_QUOTES) ; ?>'  />
			    	 <input type='hidden' name='term' id='term' value='<? echo htmlspecialchars($term, ENT_HTML5|ENT_QUOTES) ; ?>'  />
				</td>
			</tr>
			
			
		</table>

	</form>
</div>
<?php  } 
else { ?>
	
		<div id="edit_form" style="margin-left:50px;">
	<form action='#' name='query_form' method='post' >
		<table  style="width:100%" >
			<tr>
				<td >
					<font size="4" face="標楷體">
					
			
					一、承辦人：<? echo ($WORKER != '' ? htmlspecialchars($WORKER . "(分機 {$ADD_VAL1})") : ""); ?><? echo ($DESCRIPTION != '' ? htmlspecialchars("、代理人：". $DESCRIPTION . "(分機 {$ADD_VAL2})。") : ""); ?>
					<br>
					<?php 
					$sel_number = QueryToArray($this->db->query("select count(*) as cnt from online_app where year=".$this->db->escape(addslashes($year))." and class_no = ".$this->db->escape(addslashes($class_no))." and term=".$this->db->escape(addslashes($term))." and yn_sel  in ('3','8','1')"))[0]['cnt']; 
					?>
					二、研習人數 <?=htmlspecialchars($sel_number, ENT_HTML5|ENT_QUOTES);?>人；研習總時數 <?= htmlspecialchars($RANGE_REAL + $RANGE_INTERNET, ENT_HTML5|ENT_QUOTES);?>小時
					<?= ($RANGE_INTERNET > 0 ? htmlspecialchars("(實體時數 {$RANGE_REAL}小時、線上時數 {$RANGE_INTERNET}小時)") : ""); ?>。
			<?php
				$worker_mail = QueryToArray($this->db->query("select IFNULL(C.co_empdb_email,C.email) as workmail from `require` a LEFT JOIN BS_user C ON a.WORKER = C.idno  where a.year=".$this->db->escape(addslashes($year))." and a.class_no=".$this->db->escape(addslashes($class_no))." and a.term=".$this->db->escape(addslashes($term))." "))[0];
				$range = QueryToArray($this->db->query("select `range` as ran from `require` where year=".$this->db->escape(addslashes($year))." and class_no=".$this->db->escape(addslashes($class_no))." and term=".$this->db->escape(addslashes($term))." "))[0];
				$true_count = QueryToArray($this->db->query("select count(*) as cnt from online_app where year=".$this->db->escape(addslashes($year))." and class_no=".$this->db->escape(addslashes($class_no))." and term=".$this->db->escape(addslashes($term))." and yn_sel  in ('3','8','1') "))[0];
				$content =str_replace("@@@@",htmlspecialchars($worker_mail['workmail']),$content);
				$content =str_replace("@@@",htmlspecialchars($true_count['cnt']),$content);
				$content =str_replace("@@",htmlspecialchars($range['ran']),$content);
				echo $content;
			?>
				</font>
				</td>
			</tr>
		<tr id="btn_print">
				<td align="center">
						
			    	 <input type='button' name='btnprint' id='btnprint' value='列印' onclick='printScreen();' class='button' />
			    	 <!--<input type='button' name='btnCancel' id='btnCancel' value='取消' onclick='btn_back();' class='button' />-->
				</td>
			</tr>
			
			
		</table>

	</form>
</div>


<?php } ?>
</body>
<script>
	
	
function printScreen(){
	
	document.getElementById('btn_print').style.display= "none";
	//var value = document.getElementById('require_query_form').innerHTML+document.getElementById('edit_form').innerHTML;
	var value = document.getElementById('edit_form').innerHTML;

	var printPage = window.open('','printPage','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,height=530,width=770px');	
	printPage.document.open();
	printPage.document.write("<HTML><head><title>課程表</title><link rel='stylesheet' type='text/css' href='css/master.css'/></head><BODY style='width:770px' onload='window.print();'>");
	printPage.document.write("<PRE>");
	printPage.document.write("<div id='master' style='margin-left:40px;width: 770px;'>");
	printPage.document.write(value);
	printPage.document.write("</div>");
	printPage.document.write("</PRE>");
	printPage.document.close("</BODY></HTML>");
}

function printScreen2(){
		
	document.getElementById('btn_print').style.display = "none";
	//var value = document.getElementById('require_query_form').innerHTML+document.getElementById('FCKeditor1').value;
	var value = document.getElementById('FCKeditor1').value;

	var printPage = window.open('','printPage','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,height=530,width=770px');	
	printPage.document.open();
	printPage.document.write("<HTML><head><title>課程表</title><link rel='stylesheet' type='text/css' href='css/master.css'/></head><BODY style='width:770px' onload='window.print();'>");
	printPage.document.write("<PRE>");
	printPage.document.write("<div id='master' style='margin-left:40px;width: 770px;'>");
	printPage.document.write(value);
	printPage.document.write("</div>");

	printPage.document.write("</PRE>");
	printPage.document.close("</BODY></HTML>");
}

function btn_back(){
	
	document.location = ("Print_Class_Schedule.php");
}
	
</script>
</html>

<?php
/**
	依據

**/

function arrangeEmailContent($mail_data, $key = null, $user_list_status = true){

	$phy_schedule_table = $user_list_table = "";

	$html = "";

	if (isset($key)){
		if (isset($mail_data['signatures'][$key])){
			// $html .= "<br>".$mail_data['signatures'][$key];
			$mail_data['mail_content'] = str_replace("@signatures@", $mail_data['signatures'][$key], $mail_data['mail_content']); // 承辦人MAIL
		}else{
			$mail_data['mail_content'] = str_replace("@signatures@", '', $mail_data['mail_content']); // 承辦人MAIL
		}
	}

	// 信件內文
	$html .= "<div>".$mail_data['mail_content']."</div>";	

	// 線上課程課表
	if (!empty($mail_data['online_schedule'])){
		$oneline_schedule_table = getOnlineScheduleHtml($mail_data['online_schedule']);
		// 課程表
		$html .= "
			<div align='center' style='width:100%'>臺北市政府公務人員訓練處&nbsp;&nbsp;&nbsp;&nbsp;線上課程表</div>
			<div align='center' style='width:100%'>".$mail_data['class_info']->year."年度　".$mail_data['class_info']->class_name."第".$mail_data['class_info']->term."期</div>
			<div align='center' style='width:100%'>班期代碼：".$mail_data['class_info']->class_no."</div>";		
		$html .= $oneline_schedule_table;			
	}



	// 實體課程課表
	if (!empty($mail_data['class_info']->course_schedule_file_path)){
		if (file_exists(DIR_UPLOAD_COURSE_SCHEDULE.basename($mail_data['class_info']->course_schedule_file_path))){
			$html .= "<img src='".base_url("files/upload_course_schedule/".basename($mail_data['class_info']->course_schedule_file_path))."'>";
		}
	}else if (!empty($mail_data['phy_schedule'])){

	    $tmp = $mail_data;
	    $mail_data['phy_schedule'] = [];
	    foreach ($tmp['phy_schedule'] as $schedule){
	    	$mail_data['phy_schedule'][] = clone $schedule;
	    }
		
		$phy_schedule_table = getPhyScheduleHtml($mail_data['phy_schedule']);
		$html .= "<div align='center' style='width:100%'>臺北市政府公務人員訓練處&nbsp;&nbsp;&nbsp;&nbsp;課程表</div>";
		$html .= "
			<div align='center' style='width:100%'>".$mail_data['class_info']->year."年度　".$mail_data['class_info']->class_name."第".$mail_data['class_info']->term."期</div>
			<div align='left' style='width:100%'>班期代碼：".$mail_data['class_info']->class_no."</div>";
		$html .= $phy_schedule_table;
		

	}

	$html .= "<div>".$mail_data['course_content']."</div>";

	// 如果研習人員有資料 代表這個email需要
	if (!empty($mail_data['user_list'])){
		$user_list_table = getUserList($mail_data['user_list']);
		$html .= "
			<div align='center'><b><font size='5'>臺北市政府公務人員訓練處".$mail_data['s_name']."</font> <b></div>
			<div align='center'>".$mail_data['class_info']->year."年度　".$mail_data['class_info']->class_name."第".$mail_data['class_info']->term."期</div>
		";

		if($user_list_status){
			$html .= $user_list_table;
		}
	}	

	return $html;
}

//for16F
function arrangeEmailContent2($mail_data, $key = ''){
	$phy_schedule_table = $user_list_table = "";

	$html = "";

	if (isset($key)){
		if (isset($mail_data['signatures'][$key])){
			// $html .= "<br>".$mail_data['signatures'][$key];
			$mail_data['mail_content'] = str_replace("@signatures@", $mail_data['signatures'][$key], $mail_data['mail_content']); // 承辦人MAIL
		}else{
			$mail_data['mail_content'] = str_replace("@signatures@", '', $mail_data['mail_content']); // 承辦人MAIL
		}
	}

	// 信件內文
	$html .= "<div>".$mail_data['mail_content']."</div>";	

	$html .= "
			<div align='center'><b><font size='5'>臺北市政府公務人員訓練處 研習記錄表</font> <b></div>
			<div align='center'><b><font size='5'>".$mail_data['class_info']->year."年度　".$mail_data['class_info']->class_name."第".$mail_data['class_info']->term."期</font> <b></div>
		";
	$coursedate_list = '';
	if(!empty($mail_data['room_uses'])){
		foreach($mail_data['room_uses'] as $room_use){
			$coursedate_list .= date('m/d',strtotime($room_use->use_date)).'、';
		}
	}
	
	$coursedate_list = mb_substr($coursedate_list,0,-1,'utf-8');

	$html .= "
			<div align='right'><b><font size='5'>【上課日期：".$coursedate_list."】</font> <b></div>
		";
		
	$leaves_list = '';
	if(!empty($mail_data['leaves'])){
		for($i=0;$i<count($mail_data['leaves']);$i++){
			if($mail_data['leaves'][$i]->beaurau_id == $key){
				if($mail_data['leaves'][$i]->va_code == '01'){
					$status = '請假';
				} else if($mail_data['leaves'][$i]->va_code == '02'){
					$status = '未請假';
				} else if($mail_data['leaves'][$i]->va_code == '03'){
					$status = '未留宿';
				} else {
					$status = '';
				}

				if($mail_data['leaves'][$i]->yn_sel == 4){
					$remark = '退訓';
				} elseif ($mail_data['leaves'][$i]->yn_sel == 5) {
					$remark = '未報到';
				} else {
					$remark = '';
				}

				if($mail_data['leaves'][$i]->yn_sel == 5){
					if($mail_data['leaves'][$i]->st_no == $mail_data['leaves'][$i-1]->st_no && $mail_data['leaves'][$i]->name == $mail_data['leaves'][$i-1]->name){
						continue;
					}
					$leaves_list .= "
						<tr>
							<td>".$mail_data['leaves'][$i]->st_no."</td>
							<td>".$mail_data['leaves'][$i]->bureau_name."</td>
							<td>".$mail_data['leaves'][$i]->name."</td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td>".$remark."</td>
						</tr>
					";
				} else {
					$leaves_list .= "
						<tr>
							<td>".$mail_data['leaves'][$i]->st_no."</td>
							<td>".$mail_data['leaves'][$i]->bureau_name."</td>
							<td>".$mail_data['leaves'][$i]->name."</td>
							<td>".$status."</td>
							<td>".$mail_data['leaves'][$i]->vacation_date."</td>
							<td>".$mail_data['leaves'][$i]->time."</td>
							<td>".$mail_data['leaves'][$i]->hours."</td>
							<td>".$remark."</td>
						</tr>
					";
				}
			}
		}
		

		$leaves = "<table border='1' style='width:100%;text-align:center;' >
						<thead>
							<tr>
								<th width='' align='center'>學號</th>
								<th width='150' align='center'>局處名稱</th>
								<th width='500' align='center'>姓名</th>
								<th width='250' align='center'>缺席情形</th>
								<th width='250' align='center'>請假日期</th>
								<th width='250' align='center'>請假時間</th>
								<th width='250' align='center'>請假時數</th>
								<th width='250' align='center'>備註</th>
							</tr>
						</thead>
						<tbody>
						".$leaves_list."
						</tbody>
					</table>";
		$html .= $leaves;
		$html .= "<br>";
		$html .= "<div align='left'><b><font size='3'>說明：<br><br>1.缺席情形欄<br><font style='margin-left:20px'>■「請假」：係已完成線上請假者。</font><br><font style='margin-left:20px'>■「未請假」：係未完成線上請假者。<br><br>2.備註欄<br><font style='margin-left:20px'>■「未報到」：係為應參加研習卻未參訓人員。</font><br><font style='margin-left:20px'>■「退訓」：係為已報到參加研習，但缺課時數逾該班期退訓標準人員。</font><br><br><font style='margin-left:20px'>為撙節訓練資源，請貴機關多加配合，避免類似情形發生。</font></font></font> <b></div>";
	}

	return $html;
}

function getUserList($user_data){
	$user_list_table = "";
	foreach ($user_data as $user) {
		if(!empty($user->out_gov_name)){
			$bureau_name = $user->out_gov_name;
		} else {
			$bureau_name = $user->bureau_name;
		}

		$user_list_table .= "
			<tr>
				<td>".$user->st_no."</td>
				<td>".$user->group_no."</td>
				<td>".$bureau_name."</td>
				<td>".$user->title."</td>
				<td>".hiddenName($user->name).$user->yn_sel."</td>
				<td>".$user->retirement."</td>
			</tr>
		";
	}

	$user_list_html = "
		<table border='1' style='width:100%;text-align:center;' >
			<thead>
				<tr>
					<th width='' align='center'>學號</th>
					<th width='150' align='center'>組別</th>
					<th width='500' align='center'>服務單位</th>
					<th width='250' align='center'>職稱</th>
					<th width='250' align='center'>姓名</th>
					<th width='250' align='center'>備註</th>
				</tr>
			</thead>
			<tbody>
			".$user_list_table."
			</tbody>
		</table>";

	return $user_list_html;	
}

/*
	將實體課程資訊做成表格
*/

function getPhyScheduleHtml($phy_data){
	

	$phy_schedule_table = $class_position = "";
	// 檢查上課地點是否一樣如果一樣則顯示在表頭不一樣則顯示在資料列中
	foreach ($phy_data as $phy) {
		if ($class_position === ""){
			$class_position = $phy->room_name;
		}else if($class_position != $phy->room_name){
			$class_position = false;
			break;
		}
	}
	
        //合併老師
        $index=[];
        for($i=count($phy_data)-1;$i>0;$i--){
    		if($phy_data[$i]->use_date==$phy_data[$i-1]->use_date&&$phy_data[$i]->room_name==$phy_data[$i-1]->room_name&&$phy_data[$i]->from_time==$phy_data[$i-1]->from_time&&$phy_data[$i]->to_time==$phy_data[$i-1]->to_time&&$phy_data[$i]->description==$phy_data[$i-1]->description){
    			if(strpos($phy_data[$i-1]->name,$phy_data[$i]->name)==false){
    				$phy_data[$i-1]->name=$phy_data[$i-1]->name.','.$phy_data[$i]->name;
    			}
    			
    			$index[$i]=$i;
    		}
    	}
    	$cnt_data=count($phy_data);
    	for($j=0;$j<$cnt_data;$j++){
    		if(isset($index[$j])){
    			unset($phy_data[$index[$j]]);
    			//echo $j;
    		}
    	}
    	//var_dump(count($index));
    	$phy_data=array_values($phy_data);
    	for($k=0;$k<count($phy_data);$k++){
    		$phy_data[$k]->teachers=explode(",",$phy_data[$k]->name);
    	}
    	
    	//合併相同的課程資訊
    	$merge_index=[];
    	for($z=count($phy_data)-1;$z>0;$z--){
    		
    		if($phy_data[$z]->use_date==$phy_data[$z-1]->use_date&&$phy_data[$z]->room_name==$phy_data[$z-1]->room_name&&$phy_data[$z]->description==$phy_data[$z-1]->description&&$phy_data[$z]->name==$phy_data[$z-1]->name){
    			$phy_data[$z-1]->to_time=$phy_data[$z]->to_time;
    			$merge_index[$z]=$z;
    		}
    	}
    	$delete_cnt=count($phy_data);
    	for($m=0;$m<$delete_cnt;$m++){
    		if(isset($merge_index[$m])){
    			unset($phy_data[$merge_index[$m]]);
    		}
    	}

    	$phy_data=array_values($phy_data);

    	

    	//}
    

	$schedule = [];

	/*foreach ($phy_data as $phy) {

		if (!empty($phy->from_time) && !empty($phy->to_time)){
			if (empty($schedule[$phy->use_date." ".$phy->from_time.$phy->to_time])){
				$schedule[$phy->use_date." ".$phy->from_time.$phy->to_time] = $phy;
			}
			if (empty($schedule[$phy->use_date." ".$phy->from_time.$phy->to_time]->teachers)){
				$schedule[$phy->use_date." ".$phy->from_time.$phy->to_time]->teachers = [];
			}

			if (array_search($phy->teacher_name, $schedule[$phy->use_date." ".$phy->from_time.$phy->to_time]->teachers) === false){
				$phy->teacher_name = (empty($phy->teacher_name)) ? $phy->name : $phy->teacher_name;
				$schedule[$phy->use_date." ".$phy->from_time.$phy->to_time]->teachers[] = $phy->teacher_name;
			}
			
		}
	}*/
	
	$schedule=$phy_data;



	$last_date = null;

	foreach ($schedule as $phy) {

		$week = get_chinese_weekday($phy->use_date);
		if (!empty($phy->from_time) && !empty($phy->to_time)){
			$phy_schedule_table .= "<tr>";

			if ($last_date == null or $last_date != $phy->use_date){
				$last_date = $phy->use_date;
				$phy_schedule_table .= "
					<td align=\"center\">".$phy->use_date."</td>
					<td align=\"center\">".$week."</td>
				";				
			}else{
				$phy_schedule_table .= "<td></td><td></td>";
			}

			$phy_schedule_table .= "
				<td align=\"center\">".substr($phy->from_time,0,2).":".substr($phy->from_time,2,2).'~'.substr($phy->to_time,0,2).":".substr($phy->to_time,2,2)."</td>
				<td align=\"center\">".$phy->description."</td>
			";

			$phy_schedule_table .= "<td align=\"center\">";
			foreach ($phy->teachers as $teacher){
				$phy_schedule_table .= $teacher."<br>";
			}
			//$phy_schedule_table .= $phy->name;
			$phy_schedule_table .= "</td>";
			if ($class_position === false){
				$phy_schedule_table .= "<td align=\"center\">".$phy->room_name."</td>";
			}
			$phy_schedule_table .= "</tr>";
		}
	}

	

	
	if ($class_position === false) {
		$position_header = "<th align=\"center\">上課地點</th>";
		$position_title = "";
	}else{
		$position_header = "";
		$position_title = "<tr><th align=\"center\">上課地點</th><th  colspan=\"4\" align=\"center\">".$class_position."</th></tr>";
	}

//$attend_class_position
	$phy_schedule_table_html = "
		<table border=\"1\" style='width:100%;text-align:center;' >
			<thead>
					".$position_title."
				<tr>
				<th width='' align=\"center\">日期</th>
				<th width='150' align=\"center\">星期</th>
				<th width='500' align=\"center\">時間</th>
				<th width='250' align=\"center\">課程</th>
				<th width='250' align=\"center\">講座</th>
					".$position_header."
				</tr>
			</thead>
			<tbody>
			".$phy_schedule_table."
			</tbody>
		</table>";
		


	return $phy_schedule_table_html;
}

/*
	將線上課表課程資訊做成表格
*/
function getOnlineScheduleHtml($online_data){
	$online_schedule_table = "";
	foreach ($online_data as $online) {
		$online_schedule_table .= "
			<tr>
				<td>".substr($online->start_date,0,10)."</td>
				<td>".substr($online->end_date,0,10)."</td>
				<td>".$online->class_name."</td>
				<td>".$online->teacher_name."</td>
				<td>".$online->place."</td>
			</tr>
		";
	}
	$online_schedule_html = "
		<div style='float:left; width:100%;'>線上課程表</div>
		<table border=\"1\" style=\"width:100%;text-align:center;\">
			<thead>
				<tr>
					<th  style='width:17%'>起日</th>
					<th  style='width:17%'>迄日</th>
					<th  style='width:28%'>線上課程名稱</th>
					<th  style='width:25%'>講座名稱</th>
					<th  style='width:13%'>上課地點</th>
				</tr>
			</thead>
			<tbody>".$online_schedule_table."</tbody>
		</table>";	
	return $online_schedule_html;
}

/*
	取代信件內容
*/
function replaceEmailContent($mail_content, $replace_data){
	// echo "<pre>"; var_dump($replace_data); die;

	// 產生線上課程連結
	$course_url = "";
	foreach ($replace_data['online_course'] as $onilne_course) {
		$course_url .= "線上課程名稱：";
		if (!empty($onilne_course->elearn_id)){
			$course_url .= "<a href= 'http://elearning.taipei/elearn/courseinfo/so.php?v=$onilne_course->elearn_id' target='_blank'> {$onilne_course->class_name}</a>";
		}else{
			$course_url .= "線上課程名稱：";
		}
		$course_url .= "<br>";
	}
	

	// 問卷連結
	$preq_link = "http://dcsdcourse.taipei.gov.tw/base/admin/preq.php?qid=".$replace_data['course']->preq_id;
	$preq_link = "<a href='{$preq_link}'>{$preq_link}</a>";
	// 
	$preq_date = "<font color='blue'>{$replace_data['course']->preq_start_date}至{$replace_data['course']->preq_end_date}</font>";

	// 上課時間
	if (empty($replace_data['course']->course_date)){
		$course_date = "";
	}else{
		$course_date = new DateTime($replace_data['course']->course_date);
	}
	//var_dump($course_date);

	
	$from_time = substr($replace_data['course']->from_time, 0,2).':'.substr($replace_data['course']->from_time, 2,2);
	$to_time = substr($replace_data['course']->to_time, 0,2).':'.substr($replace_data['course']->to_time, 2,2);

	if ($course_date == ""){
		$use_date = "";
	}else{
		$use_date = $course_date->format("m 月 d 日").$from_time."-".$to_time;
	}

	// title
	$title = "{$replace_data['course']->year}年度{$replace_data['course']->class_name} 第{$replace_data['course']->term}期";

    $mail_content = str_replace("@@@@@@@@@@@@@@@", $course_url, $mail_content);// 臺北e大線上課程連結
	$mail_content = str_replace("@@@@@@@@@@@@@@", $preq_link, $mail_content); // 問卷連結
	$mail_content = str_replace("@@@@@@@@@@@@@", $preq_date, $mail_content); // 問卷日期
	$mail_content = str_replace("@@@@@@@@@@@@", $replace_data['course']->worker_sub_phone, $mail_content); // 承辦人分機
	$mail_content = str_replace("@@@@@@@@@@@", $replace_data['course']->worker_name , $mail_content); // 承辦人姓名
	$mail_content = str_replace("@@@@@@@@@@", $use_date, $mail_content);  // 班務說明日期時間
	$mail_content = str_replace("@@@@@@@@@", $replace_data['course']->room_name, $mail_content); // 班務說明教室
	$mail_content = str_replace("@@@@@@@@", $replace_data['course']->mix_start_date, $mail_content); // 混成課程迄日
	$mail_content = str_replace("@@@@@@@", $replace_data['course']->mix_end_date, $mail_content); // 混成課程起日
	$mail_content = str_replace("@@@@@@", $title, $mail_content);  // 年度班期名稱期別
	if(isset($replace_data['course']->quit_class2) && $replace_data['course']->quit_class2 > 0) {
		$mail_content = str_replace("@@@@@",$replace_data['course']->quit_class2, $mail_content); // 退訓標準2
	}else{
		$mail_content = str_replace("@@@@@","1/".$replace_data['course']->quit_class, $mail_content); // 退訓標準
	}
	
	$mail_content = str_replace("@@@@",$replace_data['course']->worker_email, $mail_content); // 承辦人MAIL
	if (!empty($replace_data['student_count'])){
		$mail_content = str_replace("@@@",$replace_data['student_count']->student_count, $mail_content); // 研習人數
	}else{
		$mail_content = str_replace("@@@",0 , $mail_content); // 研習人數
	}
	$mail_content = str_replace("@@",$replace_data['course']->range, $mail_content); // 研習時數

		
	
	return $mail_content;
}

function getCurriculumHtml($mail_data)
{
	$html = "";
	if (!empty($mail_data['online_schedule'])){
		$oneline_schedule_table = getOnlineScheduleHtml($mail_data['online_schedule']);
		// 課程表
		$html .= "
			<div align='center' style='width:100%'>臺北市政府公務人員訓練處&nbsp;&nbsp;&nbsp;&nbsp;線上課程表</div>
			<div align='center' style='width:100%'>".$mail_data['class_info']->year."年度　".$mail_data['class_info']->class_name."第".$mail_data['class_info']->term."期</div>
			<div align='center' style='width:100%'>班期代碼：".$mail_data['class_info']->class_no."</div>";		
		$html .= $oneline_schedule_table;			
	}



	// 實體課程課表
	if (!empty($mail_data['class_info']->course_schedule_file_path)){
		if (file_exists(DIR_UPLOAD_COURSE_SCHEDULE.$mail_data['class_info']->course_schedule_file_path)){
			$html .= "<img src='".base_url("files/upload_course_schedule/".$mail_data['class_info']->course_schedule_file_path)."'>";
		}
	}else if (!empty($mail_data['phy_schedule'])){
		$phy_schedule_table = getPhyScheduleHtml($mail_data['phy_schedule']);
		$html .= "<div align='center' style='width:100%'>臺北市政府公務人員訓練處&nbsp;&nbsp;&nbsp;&nbsp;課程表</div>";
		$html .= "
			<div align='center' style='width:100%'>".$mail_data['class_info']->year."年度　".$mail_data['class_info']->class_name."第".$mail_data['class_info']->term."期</div>
			<div align='left' style='width:100%'>班期代碼：".$mail_data['class_info']->class_no."</div>";
		$html .= $phy_schedule_table;
		

	}	
	return $html;
}

class progress_helper
{
	public $ctrl;
	public function __construct(){
		// 當前controller
		$this->ctrl = &get_instance();
		$this->ctrl->load->model([
			"require_model", 
			"online_app_model", 
			"stud_modifylog_model",
			"require_his_model",
			"require_content_model",
			"require_content_his_model",
			"mail_log_model",
		]);
	}
	/*
		取消開班
	*/
	function cancelRequire($class_info){

		$require = $this->ctrl->require_model->find($class_info);
		if (!empty($require)){
			// 取消開班
			$result = $this->ctrl->require_model->update($class_info, ['is_cancel' => 1, 'room_code' => NULL]);

			// 將學員狀態改為取消參訓
			$conditon = $class_info;
			$conditon['yn_sel'] = 3;
			$this->ctrl->online_app_model->update($conditon, ['yn_sel' => '7']);
			
			// 新增取消開班紀錄log
			$online_apps = $this->ctrl->online_app_model->getStudent($class_info); // 取得所有學員
			foreach ($online_apps as $online_app){
				$stud_modify_log = [];
				$stud_modify_log['year'] = $online_app->year;
				$stud_modify_log['class_no'] = $online_app->class_no;
				$stud_modify_log['term'] = $online_app->term;
				$stud_modify_log['beaurau_id'] = $this->ctrl->flags->user['bureau_id']; // 操作者單位
				$stud_modify_log['id'] = $online_app->id;
				$stud_modify_log['st_no'] = $online_app->st_no;
				$stud_modify_log['modify_item'] = "取消開班";
				$stud_modify_log['modify_date'] = date("Y-m-d H:i:s"); 
				$stud_modify_log['upd_user'] = $this->ctrl->flags->user['username'];
				$stud_modify_log['s_beaurau_id'] = $online_app->stu_bureau_id; // 學員單位
				$this->ctrl->stud_modifylog_model->insert($stud_modify_log);
			}

			// 搬移計畫 ??
			// $this->ctrl->require_his_model->copyFromRequire($class_info); // 舊系統此功能已壞
			$this->ctrl->require_content_his_model->moveFromRequireContent($class_info);
			return true;
		}else{
			return false;
		}
	}
	/*
		新增寄信紀錄
	*/
	function insertMailLog($class_info, $email_info, $mail_type){
		$now_seq_no = $this->ctrl->mail_log_model->getSeqNo();
		$now_seq_no = (empty($now_seq_no)) ? 0 : $now_seq_no->seq;

		// 取得切換前的帳號
		$session = $this->ctrl->session->userdata($this->ctrl->site.$this->ctrl->session_id);
		if (isset($session['switch_ac'])){
			$his_user = $this->ctrl->bs_user_model->find(['id' => $session['member_userid']]);
		}

		if(isset($email_info['content2'])){
			$email_info_conten2 = $email_info['content2'];
		} else {
			$email_info_conten2 = null;
		}

		// 新增寄送紀錄
		$mail_log = [
			"year" => $class_info['year'],
			"class_no" => $class_info['class_no'],
			"term" => $class_info['term'],
			"body" => $email_info['content'],
			"body2" => $email_info_conten2,
			"subject" => $email_info['title'],
			"cre_user" => $this->ctrl->flags->user['username'],
			"cre_date" => date("Y-m-d H:i:s"),
			"mail_type" => $mail_type,
			"seq" => $now_seq_no + 1
		];

		if (!empty($his_user)){
			$mail_log['by_user'] = $his_user->username;
		}

		$this->ctrl->mail_log_model->insert($mail_log);		
	}
}
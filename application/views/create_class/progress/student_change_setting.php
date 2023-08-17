<?php echo "<div style='color:red;'>".validation_errors()."</div>";?>
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="fa fa-list fa-lg"></i> <?=$_LOCATION['name'];?>
            </div>
            <!-- 班期資訊 -->
            <div class="panel-body">
            	<table class="table table-bordered table-condensed table-hover" style="text-align: center;">
            		<thead>
            		</thead>
            		<tbody>
            			<tr>
            				<th rowspan="2">年度</th>
            				<td rowspan="2"><?=$stud['year'];?></td>
            				<th>班期代碼</th>
            				<td><?=$stud['class_no'];?></td>
            				<th rowspan="2">期別</th>
            				<td rowspan="2">第<?=$stud['term'];?>期</td>
            			</tr>
            			<tr>
            				<th>班級名稱</th>
            				<td><?=$stud['class_name'];?></td>
            			</tr>            			
            			<tr>
            				<th>開課起迄日期：</th>
            				<td><?=$stud['start_date1']."~".$stud['end_date1'];?></td>
            				<th>報名迄日：</th>
            				<td colspan="3"><?=$stud['apply_e_date'];?></td>
            			</tr>        			
            		</tbody>
            	</table>
            </div>
            <div class="panel-heading" style="border-top: 1px solid #ddd;">
                <i class="fa fa-list fa-lg"></i> 異動條件設定
            </div>            
            <div class="panel-body">
            	<form id="data-form" role="form" action="<?=$link_save;?>" method="POST">
            		<input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
	            	<table class="table table-bordered table-condensed table-hover">
	            		<thead>
	            			<tr>
	            				<th>異動條件</th>
	            				<th>設定</th>
	            				<th>說明</th>
	            			</tr>
	            		</thead>
	            		<tbody>
	            			<tr>
	            				<td>異動否</td>
	            				<td>
	            					<input type="radio" name="sd_modify" value="1" <?=($stud['sd_modify'] == 1) ? 'checked' : '';?>>是
	            					<input type="radio" name="sd_modify" value="0" <?=(empty($stud['sd_modify'])) ? 'checked' : '';?>>否
	            				</td>
	            				<td>是否開放「局處線上作業系統」之學員異動功能。</td>
	            			</tr>
	            			<tr>
	            				<td>人數</td>
	            				<td>
	            					<input type="text" name="sd_cnt" class="form-control" value="<?=$stud['sd_cnt']?>" required>
	            				</td>
	            				<td>請填列本班期人數額度限制。</td>
	            			</tr>     
	            			<tr>
	            				<td>取消參訓</td>
	            				<td>
	            					<input type="radio" name="sd_cancel" value="1" <?=($stud['sd_cancel'] == 1) ? 'checked' : '';?>>是
	            					<input type="radio" name="sd_cancel" value="0" <?=(empty($stud['sd_cancel'])) ? 'checked' : '';?>>否
	            				</td>
	            				<td>意指「原參加學員，因故無法參訓」。</td>
	            			</tr>     
	            			<tr>
	            				<td>互調</td>
	            				<td>
	            					<input type="radio" name="sd_change" value="1" <?=($stud['sd_change'] == 1) ? 'checked' : '';?>>是
	            					<input type="radio" name="sd_change" value="0" <?=(empty($stud['sd_change'])) ? 'checked' : '';?>>否
	            				</td>
	            				<td>意指「原參加學員與同單位另一期別學員互調」。</td>
	            			</tr>     
	            			<tr>
	            				<td>換期</td>
	            				<td>
	            					<input type="radio" name="sd_chgterm" value="1" <?=($stud['sd_chgterm'] == 1) ? 'checked' : '';?>>是
	            					<input type="radio" name="sd_chgterm" value="0" <?=(empty($stud['sd_chgterm'])) ? 'checked' : '';?>>否
	            				</td>
	            				<td>意指「原參加學員調另一期別」。</td>
	            			</tr>  
	            			<tr>
	            				<td>換員</td>
	            				<td>
	            					<input type="radio" name="sd_another" value="1" <?=($stud['sd_another'] == 1) ? 'checked' : '';?>>是
	            					<input type="radio" name="sd_another" value="0" <?=(empty($stud['sd_another'])) ? 'checked' : '';?>>否
	            				</td>
	            				<td>意指「原參加學員無法參訓，遴選另一名學員遞補」之學員異動功能。</td>
	            			</tr>   
	            			<tr>
	            				<td>異動媒合否</td>
	            				<td>
	            					<input type="radio" name="sd_wantchg" value="1" <?=($stud['sd_wantchg'] == 1) ? 'checked' : '';?>>是
	            					<input type="radio" name="sd_wantchg" value="0" <?=(empty($stud['sd_wantchg'])) ? 'checked' : '';?>>否
	            				</td>
	            				<td>意指是否開放29F異動媒合之功能。</td>
	            			</tr>               			            			               			            			            			            	
	            			<tr>
	            				<td>截止日期</td>
	            				<td>
	            					<input type="date" name="sd_edate" class="form-control" value="<?=$stud['sd_edate']?>">
	            				</td>
	            				<td>請設定異動截止日期。</td>
	            			</tr>      
	            			<tr>
	            				<td>截止日期時分</td>
	            				<td>
	            					<input type="time" name="sd_edate_h_m" class="form-control" value="<?=$stud['sd_edate_h_m']?>">
	            				</td>
	            				<td>請設定異動截止日期時分。(範例:17:30，如未設定系統預設為23:59)</td>
	            			</tr>                  			            					        			
	            		</tbody>
	            	</table>
            	</form>
            </div>            
        </div>
    </div>
</div>
<div class="row">
	<div class="col-lg-12">
		<div class="panel panel-default">
			<div class="panel&#45;heading">
                <i class="fa fa-list fa-lg"></i> <?=$_LOCATION['name'];?>
            </div>
			<div class="panel-body">
				<table width="99%">
				  <tr>
				    <td bgcolor="#eeeeee">
				      <table class="table table-bordered table-condensed" width="100%">
								<tr>
									<td width="120" align="center" bgcolor="#dcdcdc">年度</td>
									<td align="left" bgcolor="#ffffff">
									<?php
				            echo $class['year'];
				          ?>
				          </td>
								</tr>
								<tr>
									<td width="120" align="center" bgcolor="#dcdcdc">班期代碼</td>
									<td align="left" bgcolor="#ffffff">
									<?php
				            echo $class['class_no'];
				          ?>
				          </td>
								</tr>
								<tr>
									<td width="120" align="center" bgcolor="#dcdcdc">期別</td>
									<td align="left" bgcolor="#ffffff">
									<?php
				            echo $class['term'];
				          ?>
				          </td>
								</tr>
								<tr>
									<td width="120" align="center" bgcolor="#dcdcdc">名稱</td>
									<td align="left" bgcolor="#ffffff">
									<?php
				            echo $class['class_name'];
				          ?>
				          </td>
								</tr>
							</table>
				<form id="actQuery" method="POST" >
				<table width="100%" >
					<tr>
					<td bgcolor="#eeeeee">
					<table width="100%" class="table table-bordered table-striped table-condensed" id='show_table'>
						<tr>
						  <td align="center" bgcolor="#5D7B9D" width="80"><font color="#ffffff">組別</font></td>
						  <td align="center" bgcolor="#5D7B9D" width="80"><font color="#ffffff">學號</font></td>
						  <td align="center" bgcolor="#5D7B9D" width="80" style="display:none;"><font color="#ffffff">優先順序</font></td>
				          <td align="center" bgcolor="#5D7B9D"><font color="#ffffff">服務機關</font></td>
				          <td align="center" bgcolor="#5D7B9D"><font color="#ffffff">職稱</font></td>
				          <td align="center" bgcolor="#5D7B9D"><font color="#ffffff">姓名</font></td>
				          <td align="center" bgcolor="#5D7B9D"><font color="#ffffff">性別</font></td>
				          <?php if($filter['ShowTelChecked']==1 ) echo '<td align="center" bgcolor="#5D7B9D"><font color="#ffffff">電話</font></td>'; ?>
				          <td align="center" bgcolor="#5D7B9D"><font color="#ffffff">備註</font></td>
				        </tr>
    			        <?php 
    			        if (isset($memberData)){
	    			        foreach ($memberData as $key => $value) {
					        echo '<tr>
					        		<td align="center" >'.$value['group_no'].'</td>
						        	<td align="center" >'.$key.'</td>
						        	<td align="center" style="display:none;" ></td>
						        	<td align="center" >'.$value['bureau_name'].'</td>
						        	<td align="center" >'.$value['job_title'].'</td>
						        	<td align="center" >'.$value['name'].'</td>
						        	<td align="center" >'.$value['gender'].'</td>';
						        if($filter['ShowTelChecked']==1 ) echo '<td align="center" >'.$value['phone'].'</td>';

							    echo '<td align="center" >'.$value['stop_reason'].'</td>
						        </tr>';
					        }
				        }  ?>
				        
				       
					</table>
					<div>
						<a class="btn btn-default" href="<?=$link_refresh;?>" title="返回">返回</a>
					</div>
					</td>
					</tr>
				</table>
				</form>
			</div>
		</div>
	</div>
	<!-- /.col-lg-12 -->
</div>
<!-- /.row -->

<script>

function chgDis(phy_url){
  var h=160;
  var w=300;
  var left = (screen.width/2)-(w/2);
  var top = (screen.height/2)-(h/2);

  var myW=window.open(phy_url,'chgDis','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,width='+w+', height='+h+',top='+top+', left='+left);
  myW.focus();
}

function do_enrollment()
{
	 $('#actQuery').submit();
}
</script>
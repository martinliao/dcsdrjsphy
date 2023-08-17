<?php
$now = date('YmdHi');
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Content-type: application/vnd.ms-word");
	header("Content-Disposition: attachment; Filename=Priority-{$now}.doc");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>年度訓練班期優先順序表</title>
<link rel="stylesheet" type="text/css" href="<?=HTTP_CSS;?>printhtml.css">
	    <style type="text/css">    

        body {font-family:"標楷體","Times New Roman"}
		
		@page WordSection1
		{size:595.3pt 841.9pt;
		mso-page-orientation:portrait;
		margin:1.505cm 1.505cm 1.505cm 1.505cm;
		mso-header-margin:42.55pt;
		mso-footer-margin:49.6pt;
		mso-paper-source:0;
		layout-grid:19.05pt;}
		div.WordSection1
		{page:WordSection1;}
    </style>

</head>
<body>

<!--<form id="actQuery" method="POST" action="">-->
<div class=WordSection1>

<div class='title'>
	<font face="標楷體" size = "5">（<?php echo $bureau_name;?>）<?php echo $year;?>年度訓練需求優先順序表</font></div>
	<br>
	<table class="table" width="1024">
		<tr>
			<td   class='tdc1'  width="100" align="center" height="50"><font face="標楷體" size = "4">優&nbsp;先&nbsp;順&nbsp;序</font></td>
			<td   class='tdc1'  width="570" align="center" height="50"><font face="標楷體" size = "4">班&nbsp;期&nbsp;名&nbsp;稱</font></td>
			<td   class='tdc1' width="80" align="center" height="50"><font face="標楷體" size = "4">期&nbsp;數</font></td>
			<td  class='tdc1' width="250" align="center" height="50"><font face="標楷體" size = "4">重大政策</font></td>
		</tr>
		<?php
        for($i=0;$i<count($info);$i++){
        	$str = "";

        	if($info[$i]['map1']) {
        		$str .= "A營造永續環境<br>";
        	}
        	if($info[$i]['map2']) {
        		$str .= "B健全都市發展<br>";
        	}
        	if($info[$i]['map3']) {
        		$str .= "C發展多元文化<br>";
        	}
        	if($info[$i]['map4']) {
        		$str .= "D優化產業勞動<br>";
        	}
        	if($info[$i]['map5']) {
        		$str .= "E強化社會支持<br>";
        	}
        	if($info[$i]['map6']) {
        		$str .= "F打造優質教育<br>";
        	}
        	if($info[$i]['map7']) {
        		$str .= "G確保健康安全<br>";
        	}
        	if($info[$i]['map8']) {
        		$str .= "H實現良善治理<br>";
        	}
			if($info[$i]['map9']) {
        		$str .= "樂活宜居(45項)<br>";
        	}
			if($info[$i]['map10']) {
        		$str .= "友善共融(31項)<br>";
        	}
			if($info[$i]['map11']) {
        		$str .= "創新活力(37項)<br>";
        	}

			
          echo "<tr>";
          echo '<td class="tdc1" align="center" height="50"><font face="標楷體" size = "4">'.$info[$i]['sort'].'</font></td>';
          echo '<td class="tdc1" align="center" height="50"><font face="標楷體" size = "4">'.$info[$i]['class_name'].'</font></td>';
          echo '<td class="tdc1" align="center" height="50"><font face="標楷體" size = "4">'.$info[$i]['total_term'].'</font></td>';
          echo '<td class="tdc1" align="center" height="50"><font face="標楷體" size = "4">'.$str.'</font></td>';
          echo "</tr>";
          
          
        }
        ?>     
		<tr>
			<td   class='tdc1'   height="50"><font face="標楷體" size = "4">&nbsp;</font></td>
			<td   class='tdc1'   height="50"><font face="標楷體" size = "4">&nbsp;</font></td>
			<td   class='tdc1'   height="50"><font face="標楷體" size = "4">&nbsp;</font></td>
			<td   class='tdc1'   height="50"><font face="標楷體" size = "4">&nbsp;</font></td>
		</tr>
		<!--
		<tr>
			<td class='tdc1'><font face="標楷體" size = "4">需求機關</font></td>
			<td class='tdl' colspan = '5'>
				<table class='grid'>
				<tr>
					<td class='tdc' width="30%"><b><font face="標楷體" size = "4">人事訓練 承辦人</font></b></td>
					<td class='tdc' width="30%"><b><font face="標楷體" size = "4">人事訓練 主管</font></b></td>
					<td class='tdc' width="40%"><b><font face="標楷體" size = "4">機關首長</font></b></td>
				</tr>
				<tr>
					<td class='tdl'><b><font style="font-size:15px"><br><br>e-mail：<br><br>TEL：<font></b></td>
					<td class='tdl'><br><br><br><br></td>
				</tr>
				</table>
			</td>
		</tr>
		-->
		<!--
		<tr>
			<td   class='tdc1'  width="150" align="center" height="50"><font face="標楷體" size = "4">說&nbsp;明</font></td>
			<td   class='tdl'   height="50" colspan="4"><font face="標楷體" size = "4">本表不敷使用時，請自行列印</font></td>
		
		</tr>-->
	</table>
	
	<!--<input type='button' name='printhtml' id='printhtml' value='列印' class='button' onclick='print_page();'>-->
	

	


<!--</form>-->
</div>
</body>
</html>
<script>
function print_page() {   
   window.print();   
 }  

</script>

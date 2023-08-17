<html> 
<head> 
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"> 
<title></title> 
</head> 
<body> 
<table border="1">
  <tr height="39">
    <td>班期</td>
    <td>日期</td>
    <td>項目</td>
    <td><?=htmlspecialchars($list[0]['type'],ENT_HTML5|ENT_QUOTES);?>比例</td>
    <td>回收率</td>
    <td>開放意見</td>
    <td>處理方式</td>
  </tr>
  <tr>
    <td rowspan="<?=count($list[0]['QUESTION']);?>"><?=htmlspecialchars($list[0]['QD_YEAR'],ENT_HTML5|ENT_QUOTES).htmlspecialchars($list[0]['QD_CLASS_NAME'],ENT_HTML5|ENT_QUOTES);?></td> 
    <td rowspan="<?=count($list[0]['QUESTION']);?>"><?=htmlspecialchars($list[0]['QD_SDATE'],ENT_HTML5|ENT_QUOTES).'-'.htmlspecialchars($list[0]['QD_EDATE'],ENT_HTML5|ENT_QUOTES);?></td> 
    <td><?=htmlspecialchars($list[0]['QUESTION'][0]['question'],ENT_HTML5|ENT_QUOTES);?></td>
    <td><?=htmlspecialchars($list[0]['QUESTION'][0]['percent'],ENT_HTML5|ENT_QUOTES);?>%</td>  
    <td rowspan="<?=count($list[0]['QUESTION']);?>"><?=htmlspecialchars($list[0]['receive_rate'],ENT_HTML5|ENT_QUOTES);?>%</td>
    <td>
        <?php
            $ans_list = '';
            for($i=0;$i<count($list[0]['QUESTION'][0]['answer']);$i++){
                $ans_list = $ans_list.$list[0]['QUESTION'][0]['answer'][$i]['OD_CONTENT'].'('.$list[0]['QUESTION'][0]['answer'][$i]['OD_COUNT'].'人)'.'<br>';
            }
            echo $ans_list;
        ?>
    </td>
    <td></td>
  </tr>
  <?php
    for($i=1;$i<count($list[0]['QUESTION']);$i++){
        echo '<tr>';
        echo '<td>'.htmlspecialchars($list[0]['QUESTION'][$i]['question'],ENT_HTML5|ENT_QUOTES).'</td>';
        echo '<td>'.htmlspecialchars($list[0]['QUESTION'][$i]['percent'],ENT_HTML5|ENT_QUOTES).'%</td>';

        $ans_list = '';
        for($j=0;$j<count($list[0]['QUESTION'][$i]['answer']);$j++){
            $ans_list = $ans_list.$list[0]['QUESTION'][$i]['answer'][$j]['OD_CONTENT'].'('.$list[0]['QUESTION'][$i]['answer'][$j]['OD_COUNT'].'人)'.'<br>';
        }

        echo '<td>'.$ans_list.'</td>';
        echo '<td></td>';
    }
  ?>
  
</table>
</body> 
</html>
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
    <td>教室</td>
    <td>項目</td>
    <td>開放意見</td>
    <td>處理方式</td>
  </tr>
  <tr>
    <td rowspan="<?=isset($list[0]['QUESTION'])?count($list[0]['QUESTION']):'';?>"><?=$list[0]['QD_YEAR'].$list[0]['QD_CLASS_NAME'];?></td> 
    <td rowspan="<?=isset($list[0]['QUESTION'])?count($list[0]['QUESTION']):'';?>"><?=$list[0]['QD_SDATE'].'-'.$list[0]['QD_EDATE'];?></td> 
    <td rowspan="<?=isset($list[0]['QUESTION'])?count($list[0]['QUESTION']):'';?>"><?=$list[0]['QD_ROOM_NAME'];?></td>  
    <td><?=$list[0]['QUESTION'][0]['question'];?></td>
    <td>
        <?php
            $ans_list = '';
            if(isset($list[0]['QUESTION'])){
              for($i=0;$i<count($list[0]['QUESTION'][0]['answer']);$i++){
                  $ans_list = $ans_list.$list[0]['QUESTION'][0]['answer'][$i]['OD_CONTENT'].'('.$list[0]['QUESTION'][0]['answer'][$i]['OD_COUNT'].'人)'.'<br>';
              }
              echo $ans_list;
            }
        ?>
    </td>
    <td></td>
  </tr>
  <?php
    if(isset($list[0]['QUESTION'])){
      for($i=1;$i<count($list[0]['QUESTION']);$i++){
          echo '<tr>';
          echo '<td>'.$list[0]['QUESTION'][$i]['question'].'</td>';
          
          $ans_list = '';
          for($j=0;$j<count($list[0]['QUESTION'][$i]['answer']);$j++){
              $ans_list = $ans_list.$list[0]['QUESTION'][$i]['answer'][$j]['OD_CONTENT'].'('.$list[0]['QUESTION'][$i]['answer'][$j]['OD_COUNT'].'人)'.'<br>';
          }

          echo '<td>'.$ans_list.'</td>';
          echo '<td></td>';
      }
    }
  ?>
  
</table>
</body> 
</html>
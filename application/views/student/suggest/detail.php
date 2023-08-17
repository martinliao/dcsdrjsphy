<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel&#45;heading">
                <i class="fa fa-list fa-lg"></i> <?=$_LOCATION['name'];?>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <!-- /.table head -->
                <table class="table table-bordered table-condensed table-hover">
                    <thead>
                        <tr>
                            <th class="text-center" colspan="4"><?=$list[0]['year']?>年度 <?=$list[0]['class_name']?> 第<?=$list[0]['term']?>期 <?=$list[0]['worker']?></th>
                        <tr>
                            <th class="text-center" colspan="2">類別／項目</th>
                            <th class="text-center">反映意見</th>
                            <th class="text-center">處理情形或說明</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php 
                        $class1_no=0;
                        $c1_vars = array('is_a1_visible','is_a2_visible','is_a3_visible','is_a4_visible');
                        for($i=0;$i<count($list);$i++){
                            foreach($c1_vars as $val => $name){
                                if($list[$i][$name]=="Y"){
                                    $class1_no++;
                                }
                            }
                        }
                        //var_dump(count($list));

                        /*$c1_vars=array('s1','s2','s3','s4',,'s5','s6','s7','s8','a1','a2','a3','a4','a5','a6','a7','a8','a1_by','a2_by','a3_by','a4_by','a5_by','a6_by','a7_by','a8_by');
                        for($i=0;$i<count($list);$i++){
                            foreach($c1_vars as $val => $name){
                                if($list[$i][$name]==""){
                                    $class1_no="$nbsp";
                                }else{

                                }
                            }
                        }*/
                        $class2_no=0;
                        $c2_vars = array('is_a5_visible','is_a6_visible','is_a7_visible','is_a8_visible');
                        for($i=0;$i<count($list);$i++){
                            foreach($c2_vars as $val => $name){
                                if($list[$i][$name]=="Y"){
                                    $class2_no++;
                                }
                            }
                        }

                    ?>
                    <?php 
                        $field_no=0;
                        for($i=0;$i<count($list);$i++){
                                if($list[$i]['is_a1_visible']=="Y"){
                                    $field_no++;
                                    if($field_no==1){
                                        $A1_cont=sprintf("
					                        <tr>
                  		                        <th rowspan='%s'>課程<br>建議</th>
                  		                        <th>課程設計</th>
                  		                        <td style='text-align: left;'>%s</td>
                  		                        <td style='text-align: left;'>%s</td>
					                        </tr>\n",$class1_no, nl2br($list[$i]['s1']),nl2br($list[$i]['a1']));
                                    }else{
                                        $A1_cont=sprintf("
                                        <tr >
                                              <th>課程設計</th>
                                              <td style='text-align: left;'>%s</td>
                                              <td style='text-align: left;'>%s</td>
                                        </tr >\n",nl2br($list[$i]['s1']), nl2br($list[$i]['a1']));
                                    }
                                }
                                if($list[$i]['is_a2_visible']=="Y"){
                                    $field_no++;
                                    if($field_no==1){
                                        $A2_cont=sprintf("
					                        <tr>
                  		                        <th rowspan='%s'>課程<br>建議</th>
                  		                        <th>研習方式</th>
                  		                        <td style='text-align: left;'>%s</td>
                  		                        <td style='text-align: left;'>%s</td>
					                        </tr>\n",$class1_no, nl2br($list[$i]['s2']),nl2br($list[$i]['a2']));
                                    }else{
                                        $A2_cont=sprintf("
                                        <tr >
                                              <th>研習方式</th>
                                              <td style='text-align: left;'>%s</td>
                                              <td style='text-align: left;'>%s</td>
                                        </tr >\n",nl2br($list[$i]['s2']), nl2br($list[$i]['a2']));
                                    }
                                }

                                if($list[$i]['is_a3_visible']=="Y"){
                                    $field_no++;
                                    if($field_no==1){
                                        $A3_cont=sprintf("
					                        <tr>
                  		                        <th rowspan='%s'>課程<br>建議</th>
                  		                        <th>教材講義</th>
                  		                        <td style='text-align: left;'>%s</td>
                  		                        <td style='text-align: left;'>%s</td>
					                        </tr>\n",$class1_no, nl2br($list[$i]['s3']),nl2br($list[$i]['a3']));
                                    }else{
                                        $A3_cont=sprintf("
                                        <tr >
                                              <th>教材講義</th>
                                              <td style='text-align: left;'>%s</td>
                                              <td style='text-align: left;'>%s</td>
                                        </tr >\n",nl2br($list[$i]['s3']), nl2br($list[$i]['a3']));
                                    }
                                }

                                if($list[$i]['is_a4_visible']=="Y"){
                                    $field_no++;
                                    if($field_no==1){
                                        $A4_cont=sprintf("
					                        <tr>
                  		                        <th rowspan='%s'>課程<br>建議</th>
                  		                        <th>其他建議</th>
                  		                        <td style='text-align: left;'>%s</td>
                  		                        <td style='text-align: left;'>%s</td>
					                        </tr>\n",$class1_no, nl2br($list[$i]['s4']),nl2br($list[$i]['a4']));
                                    }else{
                                        $A4_cont=sprintf("
                                        <tr >
                                              <th>其他建議</th>
                                              <td style='text-align: left;'>%s</td>
                                              <td style='text-align: left;'>%s</td>
                                        </tr >\n",nl2br($list[$i]['s4']), nl2br($list[$i]['a4']));
                                    }
                                }


                                $field_no=0;
                                if($list[$i]['is_a5_visible']=="Y"){
                                    $field_no++;
                                    if($field_no==1){
                                        $A5_cont=sprintf("
					                        <tr>
                  		                        <th rowspan='%s'>行政<br>服務</th>
                  		                        <th>教室設備</th>
                  		                        <td style='text-align: left;'>%s</td>
                  		                        <td style='text-align: left;'>%s</td>
					                        </tr>\n",$class2_no, nl2br($list[$i]['s5']),nl2br($list[$i]['a5']));
                                    }else{
                                        $A5_cont=sprintf("
                                        <tr >
                                              <th>教室設備</th>
                                              <td style='text-align: left;'>%s</td>
                                              <td style='text-align: left;'>%s</td>
                                        </tr >\n",nl2br($list[$i]['s5']), nl2br($list[$i]['a5']));
                                    }
                                }
                                if($list[$i]['is_a6_visible']=="Y"){
                                    $field_no++;
                                    if($field_no==1){
                                        $A6_cont=sprintf("
					                        <tr>
                  		                        <th rowspan='%s'>行政<br>服務</th>
                  		                        <th>供餐用膳</th>
                  		                        <td style='text-align: left;'>%s</td>
                  		                        <td style='text-align: left;'>%s</td>
					                        </tr>\n",$class2_no, nl2br($list[$i]['s6']),nl2br($list[$i]['a6']));
                                    }else{
                                        $A6_cont=sprintf("
                                        <tr >
                                              <th>教室設備</th>
                                              <td style='text-align: left;'>%s</td>
                                              <td style='text-align: left;'>%s</td>
                                        </tr >\n",nl2br($list[$i]['s6']), nl2br($list[$i]['a6']));
                                    }
                                }
                                if($list[$i]['is_a7_visible']=="Y"){
                                    $field_no++;
                                    if($field_no==1){
                                        $A7_cont=sprintf("
					                        <tr>
                  		                        <th rowspan='%s'>行政<br>服務</th>
                  		                        <th>環境</th>
                  		                        <td style='text-align: left;'>%s</td>
                  		                        <td style='text-align: left;'>%s</td>
					                        </tr>\n",$class2_no, nl2br($list[$i]['s7']),nl2br($list[$i]['a7']));
                                    }else{
                                        $A7_cont=sprintf("
                                        <tr >
                                              <th>環境</th>
                                              <td style='text-align: left;'>%s</td>
                                              <td style='text-align: left;'>%s</td>
                                        </tr >\n",nl2br($list[$i]['s7']), nl2br($list[$i]['a7']));
                                    }
                                }

                                if($list[$i]['is_a8_visible']=="Y"){
                                    $field_no++;
                                    if($field_no==1){
                                        $A8_cont=sprintf("
					                        <tr>
                  		                        <th rowspan='%s'>行政<br>服務</th>
                  		                        <th>其他建議</th>
                  		                        <td style='text-align: left;'>%s</td>
                  		                        <td style='text-align: left;'>%s</td>
					                        </tr>\n",$class2_no, nl2br($list[$i]['s8']),nl2br($list[$i]['a8']));
                                    }else{
                                        $A8_cont=sprintf("
                                        <tr >
                                              <th>其他建議</th>
                                              <td style='text-align: left;'>%s</td>
                                              <td style='text-align: left;'>%s</td>
                                        </tr >\n",nl2br($list[$i]['s8']), nl2br($list[$i]['a8']));
                                    }
                                }
                                //var_dump($list[$i]['is_a8_visible']);
                            }
                            if(isset($A1_cont)){
                                echo $A1_cont;
                            }
                            if(isset($A2_cont)){
                                echo $A2_cont;
                            }
                            if(isset($A3_cont)){
                                echo $A3_cont;
                            }
                            if(isset($A4_cont)){
                                echo $A4_cont;
                            }
                            if(isset($A5_cont)){
                                echo $A5_cont;
                            }
                            if(isset($A6_cont)){
                                echo $A6_cont;
                            }
                            if(isset($A7_cont)){
                                echo $A7_cont;
                            }
                            if(isset($A8_cont)){
                                echo $A8_cont;
                            }
                           
                            
                    ?>
                  
                    </tbody>
                </table>
                <div style="text-align:center">
                    <a href="<?=base_url('student/suggest/')?>" class="btn btn-info">回上一頁</a>
               </div>
            </div>
            <!-- /.panel -->
        </div>
        <!-- /.col-lg-12 -->
    </div>
</div>
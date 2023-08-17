<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel&#45;heading">
                <?=$_LOCATION['function']['name'] ;?>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <form id="filter-form" role="form" method="POST" action="<?=$save_url;?>" class="form-inline" enctype="multipart/form-data">
                    <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
                    <input type="hidden" id="doActionImport" name="doActionImport" value="">
                    <div class="row">
                        <div class="col-xs-12">
                                <label class="control-label">年度:</label>
                                <input type="text" class="form-control" value="<?=$detail_data['year'];?>"disabled>
                                <label class="control-label">班期代碼:</label>
                                <input type="text" class="form-control" value="<?=$detail_data['class_no'];?>"disabled>
                                <label class="control-label">班期名稱:</label>
                                <input type="text" class="form-control" value="<?=$detail_data['class_name'];?>"disabled>
                                <label class="control-label">期別:</label>
                                <input type="text" class="form-control" value="<?=$detail_data['term'];?>"disabled>
                        </div>

                        <div class="col-xs-12">
                            <a type="button"  onclick="exportCSV()" class="btn btn-info" value="匯出" >匯出</a>
                            <a type="button" onclick="go_back()" value="返回" class="btn btn-info">返回</a>
                        </div>
                        <div class="col-xs-12">
                            <span style="color: red">
                                <p>A.考核通過：單指參加實體課程之評量表註記通過者。僅退訓或取消參訓人員屬未通過(不勾選)。</p>
                                <p>B.線上完成：指混成班期線上研習狀態；線上未完成者，上傳時數時會自動剔除該學員。</p>
                            </span>
                        </div>

                        <div class="col-xs-12">
                            <input type="file" name="impFile" id="impFile" class="form-control" style="width:300px" accept=".csv">
                            <a type="button" onclick="importCsv()" value="匯入" class="btn btn-info">匯入</a>
                            <a class="btn btn-info" target="_block" href="<?=base_url('files/example_files/12b.csv');?>">
                                下載範例檔
                            </a>
                        </div>
                    </div>

                </form>
                <!-- /.table head -->
                <table class="table table-bordered table-striped table-condensed"  >
                    <form id="actSave" method="POST" action="<?=$save_url;?>" >
                    <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
                    <input type="hidden" id="doAction" name="doAction" value="">
                    <thead>
                        <tr height="30">
                        <!--
                            <td width="80" align="center" bgcolor="#5D7B9D"><font color="#ffffff">功能</font></td>
                         -->
                            <?php
                                if(true) {
                                    echo "<td align=\"center\" width=\"8%\" bgcolor=\"#5D7B9D\"><font color=\"#ffffff\"><input type=\"checkbox\" id=\"checkAll\" name=\"checkAll\" value=\"\">考核通過否</font></td>";
                                    echo "<td align=\"center\" width=\"6%\" bgcolor=\"#5D7B9D\"><font color=\"#ffffff\">線上完成否</font></td>";
                                }
                            ?>
                            <td align="center" bgcolor="#5D7B9D"><font color="#ffffff">組別</font></td>
                            <td align="center" bgcolor="#5D7B9D"><font color="#ffffff">學號</font></td>
                            <td align="center" bgcolor="#5D7B9D"><font color="#ffffff">服務單位</font></td>
                            <td align="center" bgcolor="#5D7B9D"><font color="#ffffff">職稱</font></td>
                            <td align="center" bgcolor="#5D7B9D"><font color="#ffffff">姓名</font></td>
                            <?php
                                if (isset($model[0]) && is_array($model[0]['gradesInfo'])) {
                                    foreach ($model[0]['gradesInfo'] as $grade) {
                                        echo '<td align="center" bgcolor="#5D7B9D">
                                                 <font color="#ffffff">' . $grade['type_name']. '(' . $grade['proportion'] . '%)</font>
                                                 <input type="hidden" id="hid_P_'.$grade['grade_type'].'" name="scoreInfo_type['.$grade['grade_type'].']" value="'.$grade['proportion'].'" />
                                             </td>';
                                    }
                                }
                            ?>
                            <td align="center" bgcolor="#5D7B9D"><font color="#ffffff">總分</font></td>
                            <td align="center" bgcolor="#5D7B9D"><font color="#ffffff">加減分</font></td>
                            <td align="center" bgcolor="#5D7B9D"><font color="#ffffff">總成績</font></td>
                            <td align="center" bgcolor="#5D7B9D"><font color="#ffffff">等第</font></td>
                            <?php
                                if(true) {
                                    echo "<td align=\"center\" bgcolor=\"#5D7B9D\"><font color=\"#ffffff\">未通過說明</font></td>";
                                }
                            ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $col='';
                        foreach ($model as $row) {
                            $col = ($col == '#ffffff') ? '#dcdcdc' : '#ffffff';
                            echo '<tr class="score_row">';
        //                  echo '  <td align="center" bgcolor="' . $col . '">';
        //                  echo '      <input type="button" class="button" value="修改" onclick=upd("' . $row['id'] . '")>';
        //                  echo '  </td>';
                            if(true) {

                                if ($row['listData']["yn_sel"]=='4'||$row['listData']["yn_sel"]=='5') //5:取消報名, 4:退訓
                                {
                                    $col = '#FF69B4';   // ping color
                                }
                                echo '  <td align="center" bgcolor="' . $col . '">';
                                    if ($row['listData']["yn_sel"]=='4'||$row['listData']["yn_sel"]=='5') //5:取消報名, 4:退訓
                                    {
                                    }else{
                                    echo '<input type="checkbox" class="checkbox1" name="chkPerson['.$row['id'].']" '.($row['listData']["is_assess"]?"checked":"").' >';
                                    }

                                echo '</td>';
                                echo "  <td align='center' bgcolor='$col'><input type='hidden' name='onlineReady[".$row['id']."]' value='".($row['checkCourseFinish']==1?"1":"0")."'>".($row['checkCourseFinish']==1?"V":"")."</td>";
                            }
                            echo '  <td align="center" bgcolor="' . $col . '">' . $row['group_no'] . '</td>';
                            echo '  <td align="center" bgcolor="' . $col . '">' . $row['st_no'] . '</td>';
                            echo '  <td align="center" bgcolor="' . $col . '">' . $row['beaurau_name'] . '</td>';
                            echo '  <td align="center" bgcolor="' . $col . '">' . $row['title_name'] . '</td>';
                            echo '  <td align="center" bgcolor="' . $col . '">' . $row['name'] . '</td>';

                            //輸出分 類成績
                            $i = 1;
                            if (isset($model[0]) && is_array($model[0]['gradesInfo']) && count($model[0]['gradesInfo'])>0) {
                                foreach ($model[0]['gradesInfo'] as $grade) {
                                    echo '<td align="right"  bgcolor="' . $col . '"><input type="text" size="3" class="grade_score" ref="'.$grade['grade_type'].'" name="scoreInfo_S_'.$grade['grade_type'].'['.$row['id'].']" value="'.$row['s' . $i].'"></td>';
                                    $i = $i + 1;
                                }
                                echo '<td align="right"  bgcolor="' . $col . '"><span class="main_score"></span></td>';
                            } else {
                                echo '<td align="right"  bgcolor="' . $col . '"><input type="text" size="3" class="main_score" name="scoreInfo_SCORE['.$row['id'].']" value="'.$row['main_score'].'"></td>';
                            }
                            echo '<td align="right"  bgcolor="' . $col . '"><input type="text" size="3" class="modi_num" name="scoreInfo_MODI_NUM['.$row['id'].']" value="'.$row['modi_num'].'"></td>';
                            echo '<td align="right"  bgcolor="' . $col . '"><span class="final_score"></span></td>';
                            echo '<td align="right"  bgcolor="' . $col . '"><span class="p_score"></span></td>';
                            if(true) {
                                if ($row['listData']["notpass_desc"]=="")
                                {
                                    if ($row['listData']["yn_sel"]=="4")
                                        $row['listData']["notpass_desc"]="退訓";
                                    if ($row['listData']["yn_sel"]=="5")
                                        $row['listData']["notpass_desc"]="未報到";
                                }

                                echo '  <td align="center" bgcolor="' . $col . '"><input type="text" name="npassdesc['.$row['id'].']" value="'.$row['listData']["notpass_desc"].'" /></td>';
                            }
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                    </form>
                </table>
                <input type="button" class="button" id="save_btn" onclick="save()" value="儲存" />
            </div>
            <!-- /.panel -->
        </div>
        <!-- /.col-lg-12 -->
    </div>
</div>
<script>

    $(document).ready(function(){
        $('.grade_score').blur(function(){
            computeScore();
        });
        $('.modi_num').blur(function(){
            computeScore();
        });
        $('.main_score').blur(function(){
            computeScore();
        });
        computeScore();
    });

    $("#checkAll").change(function(){
        $(".checkbox1").prop('checked', $(this).prop("checked"));
    });

    function hasIllegalChar(str){
        return new RegExp(".*?script[^&gt;]*?.*?(&lt;\/.*?script.*?&gt;)*", "ig").test(str);
    }

    var computeScore = function () {
        $('.score_row').each(function(){
            var sum = 0;

            if ($(this).find('.grade_score').size() > 0) {
                $(this).find('.grade_score').each(function(){
                    //alert($(this).attr('ref'));
                    if(!hasIllegalChar($('#hid_P_'+$(this).attr('ref')).val())){
                        var proportion = parseFloat($('#hid_P_'+$(this).attr('ref')).val(), 10);
                        if ($(this).val()==='') {
                            var score = 0;
                        } else {
                            var score = parseFloat($(this).val(), 10);
                        }
                        sum += score * proportion / 100;
                    }

                });
                if (sum.toString()!=='NaN') {
                    $(this).find('.main_score:first').text(sum.toFixed(2));
                }
            } else {
                // 沒有分數類別就抓總分來算
                sum += parseFloat($(this).find('.main_score:first').val(), 10);
                if (sum.toString()!=='NaN') {
                    $(this).find('.main_score:first').val(sum);
                }
            }

            if (sum.toString()!=='NaN') {
                //計算加減分

                var finalScore = sum + parseFloat($(this).find('.modi_num:first').val(), 10);
                finalScore=finalScore.toFixed(2);
                $(this).find('.final_score:first').text(finalScore);
                $(this).find('.p_score:first').text(calePScore(finalScore));
            } else {
                $(this).find('.final_score:first').text('');
                $(this).find('.p_score:first').text('');
            }

        });
    }

    var calePScore = function (grade) {
        if (grade >= 100) {
            return '特優';
        } else if (grade >= 90) {
            return '優';
        } else if(grade >= 80) {
            return '甲';
        } else if(grade >= 70) {
            return '乙';
        } else if(grade >= 60) {
            return '丙';
        } else if(grade >= 50) {
            return '丁';
        } else if(grade >= 40) {
            return '戊';
        } else if(grade >= 30) {
            return '己';
        } else if(grade >= 20) {
            return '庚';
        } else if(grade >= 10) {
            return '辛';
        } else {
            return '壬';
        }
        return '-';
    };

function go_back(){
    document.location = '<?=base_url('management/point_create')?>';
}

function save(){
    document.all.doAction.value = 'save';
    obj = document.getElementById("actSave");
    obj.submit();
}

function importCsv(){
  document.all.doActionImport.value = "imp";
  obj = document.getElementById("filter-form");
  obj.submit();
}

function exportCSV(){
  document.location = '<?=base_url('management/point_create/exportcsv')?>?year=<?=$detail_data['year'];?>&class_no=<?=$detail_data['class_no'];?>&term=<?=$detail_data['term'];?>';
}

</script>
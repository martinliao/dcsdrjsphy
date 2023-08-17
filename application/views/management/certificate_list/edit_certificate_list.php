<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel&#45;heading">30O 書證管理區
                <?=$_LOCATION['function']['name'] ;?>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <form id="filter-form" role="form" method="POST" action="<?=$save_url;?>" class="form-inline">
                    <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
                    <input type="hidden" id="doActionImport" name="doActionImport" value="">
                    <input type="hidden" id="seq_no" name="seq_no" value="<?=$detail_data['seq_no'];?>">
                    <input type="hidden" id="range_real" name="range_real" value="<?=$detail_data['range_real'];?>">
                    <div class="row">
                        <div class="col-xs-12">
                                <label class="control-label">年度:</label>
                                <input type="text" class="form-control" value="<?=$detail_data['year'];?>">
                                <label class="control-label">關鍵字:</label>
                                
                                <input type="text" class="form-control" value="<?=$detail_data['term'];?>">
                        </div>
                        
                        <div class="col-xs-12">
                            <a type="button" onclick="go_back()" value="新增書證" class="btn btn-info">新增書證版型</a>
                        </div>
                        <div class="col-xs-12">
                            <label class="control-label">顯示筆數</label>
                            <?php
                                echo form_dropdown('rows', $choices['rows'], $filter['rows'], 'class="form-control" onchange="sendFun()"');
                            ?>
                        </div>
                        <!--
                        <div class="col-xs-12">
                            <span style="color: red">
                                <p>A.考核通過：單指參加實體課程之評量表註記通過者。僅退訓或取消參訓人員屬未通過(不勾選)。</p>
                                <p>B.線上完成：指混成班期線上研習狀態；線上未完成者，上傳時數時會自動剔除該學員。</p>
                            </span>
                        </div>
                        -->
                        
                    </div>

                </form>
                <!-- /.table head -->
                <table class="table table-bordered table-striped table-condensed"  >
                    
                <thead>
                        <tr bgcolor="#8CBBFF">
                            <th class="text-center">年度</th>
                            <th class="text-center">書證版名稱</th>
                            <th class="text-center">功能</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center">110</td>
                            <td class="text-center">110學年柯文哲市長府頒證書版型</td>
                            <td class="text-center">
                            <a href="<?=base_url("management/certificate_list/edit_certificate")?>" class="btn btn-info">修改</a>
                            <a href="<?=base_url("management/certificate_list/edit_certificate")?>" class="btn btn-info">刪除</a>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-center">110</td>
                            <td class="text-center">110學年處頒獎狀版型</td>
                            <td class="text-center">
                            <a href="<?=base_url("management/certificate_list/edit_certificate")?>" class="btn btn-info">修改</a>
                            <a href="<?=base_url("management/certificate_list/edit_certificate")?>" class="btn btn-info">刪除</a>
                            </td>
                        </tr>
                    </tbody>
                    


                    
                </table>
                
                <a type="button" onclick="go_back()" value="返回" class="btn btn-info">返回</a>
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
    document.location = '<?=base_url('management/certificate_list')?>';
}

function save(){
    document.all.doAction.value = 'save';
    obj = document.getElementById("actSave");
    obj.submit();
}


function exportCSV(){
  document.location = '<?=base_url('management/point_create/exportcsv')?>?year=<?=$detail_data['year'];?>&class_no=<?=$detail_data['class_no'];?>&term=<?=$detail_data['term'];?>';
}

</script>
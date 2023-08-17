<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel&#45;heading">
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
                                <input type="text" class="form-control" value="<?=$detail_data['year'];?>"disabled>
                                <label class="control-label">班期代碼:</label>
                                <input type="text" class="form-control" value="<?=$detail_data['class_no'];?>"disabled>
                                <label class="control-label">班期名稱:</label>
                                <input type="text" class="form-control" value="<?=$detail_data['class_name'];?>"disabled>
                                <label class="control-label">期別:</label>
                                <input type="text" class="form-control" value="<?=$detail_data['term'];?>"disabled>
                        </div>

                        <div class="col-xs-12">
                            <a href="<?=base_url("management/certificate_list/add/".$detail_data['seq_no'])?>" class="btn btn-info">新增中文書證</a>
                            <a href="<?=base_url("management/certificate_list/en_add/".$detail_data['seq_no'])?>" class="btn btn-info">新增英文書證</a>
                            <a href="<?=base_url("management/certificate_list/addOtherCertificate/".$detail_data['seq_no'])?>" class="btn btn-info">新增外製書證</a>  
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
                            <th class="text-center">班期代碼</th>
                            <th class="text-center">班期名稱</th>
                            <th class="text-center">期別</th>
                            <th class="text-center">書證名稱</th>
                            <th class="text-center">功能</th>
                        </tr>
                    </thead>
                    <tbody>
                    <!--
                        <tr>
                            <td class="text-center">110</td>
                            <td class="text-center">AA2940</td>
                            <td class="text-center">政府服務獎獲獎經驗分享觀摩會</td>
                            <td class="text-center">1</td>
                            <td class="text-center">結訓證書</td>
                            <td class="text-center">
                            <a href="<?=base_url("management/certificate_list/detail/25970")?>" class="btn btn-info">修改</a>
                            <a href="<?=base_url("management/certificate_list/detail/25970")?>" class="btn btn-info">刪除</a>
                            </td>
                        </tr>
                        -->
                        <?php
                            foreach ($list as $key => $cer_list) {
                                echo "<tr>";
                                echo '<td class="text-center">'.$detail_data['year'].'</td>';
                                echo '<td class="text-center">'.$detail_data['class_no'].'</td>';
                                echo '<td class="text-center">'.$detail_data['class_name'].'</td>';
                                echo '<td class="text-center">'.$detail_data['term'].'</td>';
                                echo '<td class="text-center">'.$cer_list['cer_name'].'</td>';
                                echo '<td class="text-center">';
                                echo '<a href="';
                                if ($cer_list['type_id'] == 0){
                                    echo base_url("management/certificate_list/editOtherCertificate/".htmlspecialchars($cer_list['id'], ENT_HTML5|ENT_QUOTES));
                                }else{
                                    if ($cer_list['category'] == 1){
                                        echo base_url("management/certificate_list/edit?seq=".$detail_data['seq_no'].'&cid='.$cer_list['id']);
                                    } else if($cer_list['category'] == 2){
                                        echo base_url("management/certificate_list/en_edit?seq=".$detail_data['seq_no'].'&cid='.$cer_list['id']);
                                    }
                                }

                                echo '" class="btn btn-info">修改</a>';
                                echo '&nbsp;<a type="button" onclick="delete_cer_list('.$cer_list['id'].')" value="刪除" class="btn btn-info">刪除</a>
                                    </td>
                                    ';
                                echo "</tr>";
                            }
                        ?>
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



function go_back(){
    document.location = '<?=base_url('management/certificate_list?')?>';
}

function delete_cer_list(id){
    var msg = "確定要刪除嗎？"; 
    if (confirm(msg)==true){ 
        var url = "<?=base_url('management/certificate_list/delete_cer_list?seq='.$detail_data['seq_no'].'&cid=')?>"+id;
        location.href = url;
    }
}



</script>
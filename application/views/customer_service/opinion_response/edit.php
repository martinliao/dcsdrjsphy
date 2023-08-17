<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel&#45;heading">
                <i class="fa fa-list fa-lg"></i> <?=$_LOCATION['name'];?>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <div class="row">
                    <div class="col-xs-12">
                        <form id="save" action="" method="post">
                        <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
                        <table class="table table-condensed table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th class="text-center" colspan="2">類別/項目</th>
                                    <th class="text-center">反映意見</th>
                                    <th class="text-center">處理情形或說明</th>
                                    <th class="text-center">顯示否</th>
                                </tr>
                                
                       
                                <?php 
                                    if($item=='s1'){
                                        $title1 = "課程<br>建議";
                                        $title2 = "課程設計";
                                        $S = $list[0]['s1'];
                                        $A = $list[0]['a1'];
                                        if ($list[0]['is_a1_visible']=="Y"){
                                            $IS_VISIBLE = '<select id="is_visible" name="is_visible"><option value="Y" selected>顯示</option><option value="N">不顯示</option></select>';
                                        }else{
                                            $IS_VISIBLE = '<select id="is_visible" name="is_visible"><option value="Y">顯示</option><option value="N"  selected >不顯示</option></select>';
                                        }    
                                    }

                                    if($item=='s2'){
                                        $title1 = "課程<br>建議";
                                        $title2 = "研習方式";
                                        $S = $list[0]['s2'];
                                        $A = $list[0]['a2'];
                                        if ($list[0]['is_a2_visible']=="Y"){
                                            $IS_VISIBLE = '<select id="is_visible" name="is_visible"><option value="Y" selected>顯示</option><option value="N">不顯示</option></select>';
                                        }else{
                                            $IS_VISIBLE = '<select id="is_visible" name="is_visible"><option value="Y">顯示</option><option value="N"  selected >不顯示</option></select>';
                                        }    
                                    }
                                    if($item=='s3'){
                                        $title1 = "課程<br>建議";
                                        $title2 = "教材講義";
                                        $S = $list[0]['s3'];
                                        $A = $list[0]['a3'];
                                        if ($list[0]['is_a3_visible']=="Y"){
                                            $IS_VISIBLE = '<select id="is_visible" name="is_visible"><option value="Y" selected>顯示</option><option value="N">不顯示</option></select>';
                                        }else{
                                            $IS_VISIBLE = '<select id="is_visible" name="is_visible"><option value="Y">顯示</option><option value="N"  selected >不顯示</option></select>';
                                        }    
                                    }
                                    if($item=='s4'){
                                        $title1 = "課程<br>建議";
                                        $title2 = "其他建議";
                                        $S = $list[0]['s4'];
                                        $A = $list[0]['a4'];
                                        if ($list[0]['is_a4_visible']=="Y"){
                                            $IS_VISIBLE = '<select id="is_visible" name="is_visible"><option value="Y" selected>顯示</option><option value="N">不顯示</option></select>';
                                        }else{
                                            $IS_VISIBLE = '<select id="is_visible" name="is_visible"><option value="Y">顯示</option><option value="N"  selected >不顯示</option></select>';
                                        }    
                                    }
                                    if($item=='s5'){
                                        $title1 = "行政<br>服務";
                                        $title2 = "教室設備";
                                        $S = $list[0]['s5'];
                                        $A = $list[0]['a5'];
                                        if ($list[0]['is_a5_visible']=="Y"){
                                            $IS_VISIBLE = '<select id="is_visible" name="is_visible"><option value="Y" selected>顯示</option><option value="N">不顯示</option></select>';
                                        }else{
                                            $IS_VISIBLE = '<select id="is_visible" name="is_visible"><option value="Y">顯示</option><option value="N"  selected >不顯示</option></select>';
                                        }    
                                    }
                                    if($item=='s6'){
                                        $title1 = "行政<br>服務";
                                        $title2 = "供餐用膳";
                                        $S = $list[0]['s6'];
                                        $A = $list[0]['a6'];
                                        if ($list[0]['is_a6_visible']=="Y"){
                                            $IS_VISIBLE = '<select id="is_visible" name="is_visible"><option value="Y" selected>顯示</option><option value="N">不顯示</option></select>';
                                        }else{
                                            $IS_VISIBLE = '<select id="is_visible" name="is_visible"><option value="Y">顯示</option><option value="N"  selected >不顯示</option></select>';
                                        }    
                                    }
                                    if($item=='s7'){
                                        $title1 = "行政<br>服務";
                                        $title2 = "環境";
                                        $S = $list[0]['s7'];
                                        $A = $list[0]['a7'];
                                        if ($list[0]['is_a7_visible']=="Y"){
                                            $IS_VISIBLE = '<select id="is_visible" name="is_visible"><option value="Y" selected>顯示</option><option value="N">不顯示</option></select>';
                                        }else{
                                            $IS_VISIBLE = '<select id="is_visible" name="is_visible"><option value="Y">顯示</option><option value="N"  selected >不顯示</option></select>';
                                        }    
                                    }
                                    if($item=='s8'){
                                        $title1 = "行政<br>服務";
                                        $title2 = "其他建議";
                                        $S = $list[0]['s8'];
                                        $A = $list[0]['a8'];
                                        if ($list[0]['is_a8_visible']=="Y"){
                                            $IS_VISIBLE = '<select id="is_visible" name="is_visible"><option value="Y" selected>顯示</option><option value="N">不顯示</option></select>';
                                        }else{
                                            $IS_VISIBLE = '<select id="is_visible" name="is_visible"><option value="Y">顯示</option><option value="N"  selected >不顯示</option></select>';
                                        }    
                                    }
                                ?>
                                <tr>
                                
                                    <th class="text-center" rowspan="5"><?=$title1?></th>
                                    <th class="text-center"><?=$title2?></th>
                                    <th class="text-center"><textarea name="S" id="S" cols="60" rows="5"><?=$S?></textarea><span style="color:red">(最多500字)</span></th> 
                                    <th class="text-center"><textarea name="A" id="A" cols="60" rows="5"><?=$A?></textarea><span style="color:red">(最多800字)</span></th>
                                    <th><?=$IS_VISIBLE?></th>
                                </tr>
                            </thead>
                        </table>
                            <input type="hidden" name="year" id="year">
                            <input type="hidden" name="term" id="term">
                            <input type="hidden" name="class_no" id="class_no">
                            <input type="hidden" name="mode" id="mode">
                            <input type="hidden" name="item" id="item">
                            <input type="hidden" id="query_class_name" name="query_class_name">
                            <input type="hidden" id="query_class_no" name="query_class_no">
                            <!--<input type="hidden" id="start_month" name="start_month" value="<?=$get['start_month']?>">
                            <input type="hidden" id="checkAll" name="checkAll" value="<?=$get['checkAll']?>">-->
                            <input type="hidden" id="rows" name="rows">
                        </form>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-4 ">
                        <button class="btn btn-info" onclick="return suggestSave('<?=htmlspecialchars($list[0]['year'], ENT_HTML5|ENT_QUOTES)?>','<?=htmlspecialchars($list[0]['term'], ENT_HTML5|ENT_QUOTES)?>','<?=htmlspecialchars($list[0]['class_no'], ENT_HTML5|ENT_QUOTES)?>');">儲存</button>
                        <a href="<?=base_url("customer_service/opinion_response/detail/".htmlspecialchars($list[0]['seq_no'], ENT_HTML5|ENT_QUOTES)."?".htmlspecialchars($_SERVER['QUERY_STRING'], ENT_HTML5|ENT_QUOTES)."")?>" class="btn btn-info">回上一頁</a>
                    </div>
                </div>
            </div>
            <!-- /.panel -->
        </div>
        <!-- /.col-lg-12 -->
    </div>
</div>
<script type="text/javascript">
function suggestSave(year,term,class_no)
{
    
    if(confirm("確認儲存?!")) {
        obj=document.getElementById("save");
        document.getElementById("year").value=year;
        document.getElementById("term").value=term;
        document.getElementById("class_no").value=class_no;
        document.getElementById("item").value="<?=$item?>";
        document.getElementById("mode").value="save";
        
        obj.action="<?=$save_19c?>";
        obj.submit();
    }   
}

</script>
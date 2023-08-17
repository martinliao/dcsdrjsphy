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
                        <?php 
                                    if($list[0]['is_annouce']=='Y'){
                                        $annouce_str="<th class='text-center' colspan='7'><h3>已公告(公告者:".$list[0]['annouce_by']."公告日期:".$list[0]['annouce_date']."</h3></th>";
                                    }else if($list[0]['is_annouce']=='N'){
                                        $annouce_str="<th class='text-center' colspan='7'><h3>已取消公告(取消公告者:".$list[0]['annouce_by']."取消公告日期:".$list[0]['annouce_date']."</h3></th>";
                                    }else{
                                        $annouce_str="";
                                    }
                        ?>
                        <?php 
                            $param='"'. $list[0]['year']  . '","' . $list[0]['class_no']  . '","' . $list[0]['term']  . '"';

                            if(in_array('23',$group_id)){
                                if($list[0]['is_annouce']=='Y'){
                                    $annouce_button="<input type='button' name='annouce_suggest' value='取消公告' onclick='unannouce(".$param.")'>";
                                }else{
                                    $annouce_button="<input type='button' name='annouce_suggest' value='確認公告' onclick='annouce(".$param.")'>";

                                }
                            }else{
                                $annouce_button="";
                            }

                        ?>
                        <table class="table table-condensed table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th class="text-center" colspan="7"><h2><?=$list[0]['year']?>年度<?=$list[0]['class_name']?> 第<?=$list[0]['term']?>期 <?=$list[0]['worker']?></h2></th>
                                </tr>
                                <tr>
                                    <?=$annouce_str?>
                                </tr>
                                <tr>
                                    <th class="text-center" colspan="2">類別/項目</th>
                                    <th class="text-center">反映意見</th>
                                    <th class="text-center">處理情形或說明</th>
                                    <th class="text-center">張貼人</th>
                                    <th class="text-center">顯示否</th>
                                    <th class="text-center">編輯</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <th class="text-center" rowspan="5">課程建議</th>
                                </tr>
                                <tr>
                                    <th class="text-center">課程設計</th>
                                    <td><?=nl2br($list[0]['s1'])?></td>
                                    <td><?=nl2br($list[0]['a1'])?></td>
                                    <td><?=$list[0]['a1_by']?></td>
                                    <td><?=$list[0]['is_a1_visible']?></td>
                                    <td class="text-center"><a href="<?=base_url("customer_service/opinion_response/edit/".htmlspecialchars($list[0]['seq_no'], ENT_HTML5|ENT_QUOTES)."?item=s1&".htmlspecialchars($_SERVER['QUERY_STRING'], ENT_HTML5|ENT_QUOTES)."")?>" class="btn btn-info">編輯</a></td>
                                </tr>
                                <tr>
                                    <th class="text-center">研習方式</th>
                                    <td><?=nl2br($list[0]['s2'])?></td>
                                    <td><?=nl2br($list[0]['a2'])?></td>
                                    <td><?=$list[0]['a2_by']?></td>
                                    <td><?=$list[0]['is_a2_visible']?></td>
                                    <td class="text-center"><a href="<?=base_url("customer_service/opinion_response/edit/".htmlspecialchars($list[0]['seq_no'], ENT_HTML5|ENT_QUOTES)."?item=s2&".htmlspecialchars($_SERVER['QUERY_STRING'], ENT_HTML5|ENT_QUOTES)."")?>" class="btn btn-info">編輯</a></td>
                                </tr>
                                <tr>
                                    <th class="text-center">教材講議</th>
                                    <td><?=nl2br($list[0]['s3'])?></td>
                                    <td><?=nl2br($list[0]['a3'])?></td>
                                    <td><?=$list[0]['a3_by']?></td>
                                    <td><?=$list[0]['is_a3_visible']?></td>
                                    <td class="text-center"><a href="<?=base_url("customer_service/opinion_response/edit/".htmlspecialchars($list[0]['seq_no'], ENT_HTML5|ENT_QUOTES)."?item=s3&".htmlspecialchars($_SERVER['QUERY_STRING'], ENT_HTML5|ENT_QUOTES)."")?>"  class="btn btn-info">編輯</a></td>
                                </tr>
                                <tr>
                                    <th class="text-center">其他建議</th>
                                    <td><?=nl2br($list[0]['s4'])?></td>
                                    <td><?=nl2br($list[0]['a4'])?></td>
                                    <td><?=$list[0]['a4_by']?></td>
                                    <td><?=$list[0]['is_a4_visible']?></td>
                                    <td class="text-center"><a href="<?=base_url("customer_service/opinion_response/edit/".htmlspecialchars($list[0]['seq_no'], ENT_HTML5|ENT_QUOTES)."?item=s4&".htmlspecialchars($_SERVER['QUERY_STRING'], ENT_HTML5|ENT_QUOTES)."")?>"  class="btn btn-info">編輯</a></td>
                                </tr>
                                <tr>
                                    <th class="text-center" rowspan="5">行政服務</th>
                                </tr>
                                <tr>
                                    <th class="text-center">教室設備</th>
                                    <td><?=nl2br($list[0]['s5'])?></td>
                                    <td><?=nl2br($list[0]['a5'])?></td>
                                    <td><?=$list[0]['a5_by']?></td>
                                    <td><?=$list[0]['is_a5_visible']?></td>
                                    <td class="text-center"><a href="<?=base_url("customer_service/opinion_response/edit/".htmlspecialchars($list[0]['seq_no'], ENT_HTML5|ENT_QUOTES)."?item=s5&".htmlspecialchars($_SERVER['QUERY_STRING'], ENT_HTML5|ENT_QUOTES)."")?>"  class="btn btn-info">編輯</a></td>
                                </tr>
                                <tr>
                                    <th class="text-center">供餐用膳</th>
                                    <td><?=nl2br($list[0]['s6'])?></td>
                                    <td><?=nl2br($list[0]['a6'])?></td>
                                    <td><?=$list[0]['a6_by']?></td>
                                    <td><?=$list[0]['is_a6_visible']?></td>
                                    <td class="text-center"><a href="<?=base_url("customer_service/opinion_response/edit/".htmlspecialchars($list[0]['seq_no'], ENT_HTML5|ENT_QUOTES)."?item=s6&".htmlspecialchars($_SERVER['QUERY_STRING'], ENT_HTML5|ENT_QUOTES)."")?>" class="btn btn-info">編輯</a></td>
                                </tr>
                                <tr>
                                    <th class="text-center">環境</th>
                                    <td><?=nl2br($list[0]['s7'])?></td>
                                    <td><?=nl2br($list[0]['a7'])?></td>
                                    <td><?=$list[0]['a7_by']?></td>
                                    <td><?=$list[0]['is_a7_visible']?></td>
                                    <td class="text-center"><a href="<?=base_url("customer_service/opinion_response/edit/".htmlspecialchars($list[0]['seq_no'], ENT_HTML5|ENT_QUOTES)."?item=s7&".htmlspecialchars($_SERVER['QUERY_STRING'], ENT_HTML5|ENT_QUOTES)."")?>" class="btn btn-info">編輯</a></td>
                                </tr>
                                <tr>
                                    <th class="text-center">其他建議</th>
                                    <td><?=nl2br($list[0]['s8'])?></td>
                                    <td><?=nl2br($list[0]['a8'])?></td>
                                    <td><?=$list[0]['a8_by']?></td>
                                    <td><?=$list[0]['is_a8_visible']?></td>
                                    <td class="text-center"><a href="<?=base_url("customer_service/opinion_response/edit/".htmlspecialchars($list[0]['seq_no'], ENT_HTML5|ENT_QUOTES)."?item=s8&".htmlspecialchars($_SERVER['QUERY_STRING'], ENT_HTML5|ENT_QUOTES)."")?>" class="btn btn-info">編輯</a></td>
                                </tr>
                            </tbody>
                        </table>
                        <?=$annouce_button?>
                        <a href="<?=base_url("customer_service/opinion_response/?".htmlspecialchars($_SERVER['QUERY_STRING'], ENT_HTML5|ENT_QUOTES)."")?>" class="btn btn-info">回上一頁</a>
                    </div>
                </div>
            </div>
            <form id="annouce_form" action=''>
                <input type="hidden" id="year" name="year">
                <input type="hidden" id="term" name="term">
                <input type="hidden" id="class_no" name="class_no">
                <input type="hidden" id="mode" name="mode">
                <!--<input type="hidden" id="query_class_no" name="query_class_no" value="<?=$filter['query_class_no']?>">
                <input type="hidden" id="query_year" name="query_year" value="<?=$filter['query_year']?>">
                <input type="hidden" id="query_class_name" name="query_class_name" value="<?=$filter['query_class_name']?>">
                <input type="hidden" id="start_month" name="start_month" value="<?=$filter['start_month']?>">
                <input type="hidden" id="checkAll" name="checkAll" value="<?=$filter['checkAll']?>">
                <input type="hidden" id="rows" name="rows" value="<?=$filter['rows']?>">-->
            </form>
            <!-- /.panel -->
        </div>
        <!-- /.col-lg-12 -->
    </div>
</div>
<script type="text/javascript">
function annouce(year, class_no, term)
{
    
    if(confirm("確認公告?!")) {
        obj=document.getElementById("annouce_form");
        document.getElementById("year").value=year;
        document.getElementById("term").value=term;
        document.getElementById("class_no").value=class_no;
        document.getElementById("mode").value="annouce";
        obj.action="<?=$annouce?>";
        obj.submit();
    }
}

function unannouce(year, class_no, term)
{
    if(confirm("確認取消公告?!")) {
        obj=document.getElementById("annouce_form");
        document.getElementById("year").value=year;
        document.getElementById("term").value=term;
        document.getElementById("class_no").value=class_no;
        document.getElementById("mode").value="unannouce";
        obj.action='<?=$annouce?>';
        obj.submit();
    }
}
</script>
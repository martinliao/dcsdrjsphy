
<div class="row">
<div class="col-lg-12">
    <div class="panel panel-default">
        <div class="panel-heading">
            <i class="fa fa-list fa-lg"></i> 10F 推播至台北通-推播紀錄
        </div>

        <div class="panel-body">
            <div class="row">
                <form id="filter-form" role="form" class="form-inline">
                    <input type="hidden" name="sort" value="" />
                    <div class="col-xs-6" >
                        <div class="form-group">
                            <label class="control-label">顯示筆數</label>
                            <?php
                                echo form_dropdown('rows', $choices['rows'], $filter['rows'], 'class="form-control"');
                            ?>
                        </div>
                    </div>
                    <div class="col-xs-6 text-right">
                        <div class="form-group">
                            <label class="control-label"><i class="fa fa-search"></i></label>
                            <input type="text" class="form-control" name="q" value="<?=$filter['q'];?>">
                            <a href="<?=htmlspecialchars($link_index,ENT_HTML5|ENT_QUOTES)?>"><button type="button" class="btn btn-info">回上一頁</button></a>
                        </div>
                    </div>
                </form>
            </div>

            <form id="list-form" method="post">
                <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
                <table class="table table-bordered table-condensed table-hover">
                    <thead>
                        <tr>
                            <th data-field="notification_context" style="width: 8%">推播內容<input type="button" class="btn btn-info" id="all-fun" type="button" onclick="allFun(this)" value="展開"></th>
                            <th data-field="push_time">推播時間</th>
                            <th class="sorting<?=($filter['sort']=='pusher_name asc')?'_asc':'';?><?=($filter['sort']=='pusher_name desc')?'_desc':'';?>" data-field="pusher_name" >推播人</th>
                            <th data-field="type">推播對象</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($list as $row) { ?>
                        <tr>
                            <td><button class="btn btn-info" type="button" onclick="showFun('<?=htmlspecialchars($row['id'],ENT_HTML5|ENT_QUOTES);?>')">詳細內容</button><br><div class="push-content" style="display: none;" id="detail_<?=htmlspecialchars($row['id'],ENT_HTML5|ENT_QUOTES);?>"><?=htmlspecialchars_decode($row['message_content'])?></div></td>
                            <td><?=htmlspecialchars($row['push_time'],ENT_HTML5|ENT_QUOTES);?></td>
                            <td><?=htmlspecialchars($row['pusher_name'],ENT_HTML5|ENT_QUOTES);?></td>
                            <td >
                                <?php if ($row['type'] == 1) { ?>
                                    本府員工
                                <?php } else if($row['type'] == 2) {?>
                                    特定人員
                                <?php } ?>
                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </form>
            <div class="row">
                <div class="col-lg-4">
                    Showing <?=$filter['offset']+1;?> to <?=(ceil($filter['total']/$filter['rows']) == $filter['page'])?$filter['total']:$filter['offset']+$filter['rows'];?> of <?=$filter['total'];?> entries
                </div>
                <div class="col-lg-8 text-right">
                    <?=$this->pagination->create_links();?>
                </div>
            </div>
        </div>
        <!-- /.panel-body -->
    </div>
    <!-- /.panel -->
</div>
<!-- /.col-lg-12 -->
</div>
<!-- /.row -->

<!-- Page-Level Demo Scripts - Tables - Use for reference -->
<script src="<?=HTTP_PLUGIN;?>jquery.highlight-3.js"></script>
<script>
$(document).ready(function() {
$('#filter-form select').change(function(){
    $('#filter-form').submit();
});

// <?php if (isset($filter['q']) && $filter['q'] != ''){ ?>
// $('#list-form').highlight('<?=$filter['q'];?>');
// <?php } ?>



});

function allFun(obj)
{
    if(obj.value == '展開'){
        $(".push-content").show();
        obj.value = '收合';
    } else if(obj.value == '收合'){
        $(".push-content").hide();
        obj.value = '展開';
    }
}

function showFun(id)
{
    if($("#detail_"+id).is(':hidden')){
        // $("#content").show();
        $("#detail_"+id).show();
    } else {
        $("#detail_"+id).hide();
    }
} 
</script>

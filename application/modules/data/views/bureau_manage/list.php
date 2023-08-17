<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="fa fa-list fa-lg"></i> <?=$_LOCATION['name'];?>
            </div>
            <!-- /.panel-heading -->

            <div class="panel-body">
                <div class="row">
                    <form id="filter-form" role="form" class="form-inline">
                        <input type="hidden" name="sort" value="" />
                        <div class="col-xs-12">
                            <div class="form-group">
                                <label class="control-label">局處代碼</label>
                                <input type="text" class="form-control" name="bureau_id" value="<?=$filter['bureau_id'];?>">
                            </div>
                            <div class="form-group">
                                <label class="control-label">局處名稱</label>
                                <input type="text" class="form-control" name="name" value="<?=$filter['name'];?>">
                            </div>
                            <div class="form-group">
                                <label class="control-label">身分</label>
                                <?php
                                    echo form_dropdown('position', $choices['position'], $filter['position'], 'class="form-control"');
                                ?>
                            </div>
                        </div>
                        <div class="col-xs-12">
                            <div class="form-group">
                                <label class="control-label">裁撤註記(勾選〔含裁撤機關〕，如未勾選，則不含裁撤機關)</label>
                                <input type="checkbox" style="zoom:200%" class="form-control" name="del_flag" value="C" <?= isset($filter['del_flag']) && $filter['del_flag']=='C'?'checked':'';?>>
                            </div>
                        </div>
                        <div class="col-xs-12">
                            <span style="color: red">
                                <p>(1) 新成立的機關--請按【新增】按鈕進行新增。</p>
                                <p>(2) 【原局處資料更新】：僅更改機關名稱(代碼不變)或機關整個被裁撤。</p>
                                <p>(3) 【轉新局處】：機關代碼、名稱皆已變更，儲存後學員基本資料會全部轉入新局處。</p> 
                                <p>(4) 匯入或轉入資料時，請廠商至DB修改特殊字元的機關名稱(例如:臺北市立瑠公國民中學)</p>
                            </span>
                        </div>
                        <div class="col-xs-12">
                            <div class="form-group">
                                <button class="btn btn-info">查詢</button>
                            </div>
                        </div>
                        <div class="col-xs-6" >
                            <div class="form-group">
                                <label class="control-label">顯示筆數</label>
                                <?php
                                    echo form_dropdown('rows', $choices['rows'], $filter['rows'], 'class="form-control" onchange="sendFun()"');
                                ?>
                            </div>
                        </div>
                    </form>
                </div>
                <form id="list-form" method="post">
                    <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
                    <table class="table table-bordered table-condensed table-hover">
                        <thead>
                            <tr>
                                <th class="sorting<?=($filter['sort']=='bureau_id asc')?'_asc':'';?><?=($filter['sort']=='bureau_id desc')?'_desc':'';?>" data-field="bureau_id" >局處代碼</th>
                                <th class="sorting<?=($filter['sort']=='name asc')?'_asc':'';?><?=($filter['sort']=='name desc')?'_desc':'';?>" data-field="name" >局處名稱</th>
                                <th class="sorting<?=($filter['sort']=='bureau_level asc')?'_asc':'';?><?=($filter['sort']=='bureau_level desc')?'_desc':'';?>" data-field="bureau_level" >機關層級</th>
                                <th class="sorting<?=($filter['sort']=='effective_date asc')?'_asc':'';?><?=($filter['sort']=='effective_date desc')?'_desc':'';?>" data-field="effective_date" >機關生效日</th>
                                <th class="sorting<?=($filter['sort']=='abolish_date asc')?'_asc':'';?><?=($filter['sort']=='abolish_date desc')?'_desc':'';?>" data-field="abolish_date" >機關裁撤日期</th>
                                <th class="sorting<?=($filter['sort']=='del_flag asc')?'_asc':'';?><?=($filter['sort']=='del_flag desc')?'_desc':'';?>" data-field="del_flag" >裁撤註記</th>
                                <th class="sorting<?=($filter['sort']=='position asc')?'_asc':'';?><?=($filter['sort']=='position desc')?'_desc':'';?>" data-field="position" >身分</th>
                                <th>功能</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($list as $row) { ?>
                            <tr>
                                <td><?=$row['bureau_id'];?></td>
                                <td><?=$row['name'];?></td>
                                <td><?=$row['bureau_level'];?></td>
                                <td><?=$row['effective_date'];?></td>
                                <td><?=$row['abolish_date'];?></td>
                                <td><?=$row['del_flag'];?></td>
                                <td><?=$row['position']=='0'?'公務機關':'私人機關';?></td>
                                <td class="text-center" id="btn_group">
                                    <?php if (isset($row['link_view'])) { ?>
                                    <a type="button" class="btn btn-outline btn-success btn-xs btn-toggle" title="View" href="<?=$row['link_view'];?>">
                                        <i class="fa fa-eye fa-lg"></i>
                                    </a>
                                    <?php } ?>
                                    <?php if (isset($row['link_edit'])) { ?>
                                    <a type="button" class="btn btn-outline btn-warning btn-xs btn-toggle" href="<?=$row['link_edit'];?>">
                                        <i class="fa fa-pencil fa-lg">原局處資料更新</i>
                                    </a>
                                    <a type="button" class="btn btn-outline btn-warning btn-xs btn-toggle" href="<?=$row['link_transfer'];?>">
                                        <i class="fa fa-pencil fa-lg">轉新局處</i>
                                    </a>
                                    <?php } ?>
                                    <?php if (isset($row['link_delete'])) { ?>
                                    <button type="button" class="btn btn-outline btn-danger btn-xs" onclick="ajaxDelete(this, '確認要刪除選單「<?=$row['name'];?>」?', '<?=$row['link_delete'];?>')">
                                        <i class="fa fa-trash fa-lg"></i>
                                    </button>
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
<script src="<?=HTTP_PLUGIN;?>jquery.highlight-3.js"></script>
<script>
/*$(document).ready(function() {
    $('#filter-form select').change(function(){
        $('#filter-form').submit();
    });

    <?php if (isset($filter['q']) && $filter['q'] != ''){ ?>
    $('#list-form').highlight('<?=$filter['q'];?>');
    <?php } ?>
});*/


function sendFun(){
    var obj = document.getElementById('filter-form');
    obj.submit();
}
</script>

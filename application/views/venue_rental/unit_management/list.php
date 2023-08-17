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
                        <div class="col-xs-12" >
                            <div class="form-group">
                                <label class="control-label">單位名稱</i></label>
                                <input type="text" class="form-control" name="app_name" value="<?=$filter['app_name'];?>">
                            </div>
                        </div>
                        <div class="col-xs-6" >
                            <div class="form-group">
                                <label class="control-label">顯示筆數</label>
                                <?php
                                    echo form_dropdown('rows', $choices['rows'], $filter['rows'], 'class="form-control"');
                                ?>
                            </div>
                        </div>
                        <div class="col-xs-6 text-right">

                        </div>
                    </form>
                </div>
                <form id="list-form" method="post">
                    <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
                    <table class="table table-bordered table-condensed table-hover">
                        <thead>
                            <tr>
                                <th style="width: 35px;" class="text-center"><input type="checkbox" id="chkall"></th>
                                <th class="sorting<?=($filter['sort']=='app_name asc')?'_asc':'';?><?=($filter['sort']=='app_name desc')?'_desc':'';?>" data-field="app_name" >單位名稱</th>
                                <th class="sorting<?=($filter['sort']=='contact_name asc')?'_asc':'';?><?=($filter['sort']=='contact_name desc')?'_desc':'';?>" data-field="contact_name" >聯絡人姓名</th>
                                <th class="sorting<?=($filter['sort']=='tel asc')?'_asc':'';?><?=($filter['sort']=='tel desc')?'_desc':'';?>" data-field="tel" >電話</th>
                                <th class="sorting<?=($filter['sort']=='fax asc')?'_asc':'';?><?=($filter['sort']=='fax desc')?'_desc':'';?>" data-field="fax" >傳真</th>
                                <th class="sorting<?=($filter['sort']=='email asc')?'_asc':'';?><?=($filter['sort']=='email desc')?'_desc':'';?>" data-field="email" >Email</th>
                                <th class="sorting<?=($filter['sort']=='addr asc')?'_asc':'';?><?=($filter['sort']=='addr desc')?'_desc':'';?>" data-field="addr" >通訊地址</th>
                                <th class="sorting<?=($filter['sort']=='is_public asc')?'_asc':'';?><?=($filter['sort']=='is_public desc')?'_desc':'';?>" data-field="is_public" >是否為市府單位</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($list as $row) { ?>
                            <tr>
                                <td class="text-center"><input type="checkbox" name="rowid[]" value="<?=$row['app_id'];?>"></td>
                                <td><?=$row['app_name'];?></td>
                                <td><?=$row['contact_name'];?></td>
                                <td><?=$row['tel'];?></td>
                                <td><?=$row['fax'];?></td>
                                <td><?=$row['email'];?></td>
                                <td><?=$row['addr'];?></td>
                                <td><?=$row['is_public'];?></td>
                                <td class="text-center" id="btn_group">
                                    <?php if (isset($row['link_view'])) { ?>
                                    <a type="button" class="btn btn-outline btn-success btn-xs btn-toggle" title="View" href="<?=$row['link_view'];?>">
                                        <i class="fa fa-eye fa-lg"></i>
                                    </a>
                                    <?php } ?>
                                    <?php if (isset($row['link_edit'])) { ?>
                                    <a type="button" class="btn btn-outline btn-warning btn-xs btn-toggle" href="<?=$row['link_edit'];?>">
                                        <i class="fa fa-pencil fa-lg"></i>
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
$(document).ready(function() {
    $('#filter-form select').change(function(){
        $('#filter-form').submit();
    });

    <?php if (isset($filter['q']) && $filter['q'] != ''){ ?>
    $('#list-form').highlight('<?=$filter['q'];?>');
    <?php } ?>
});
</script>

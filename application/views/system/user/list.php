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

                        <div class="col-xs-6" >
                            <div class="form-group">
                                <label class="control-label">顯示筆數</label>
                                <?php
                                    echo form_dropdown('rows', $choices['rows'], $filter['rows'], 'class="form-control"');
                                ?>
                            </div>
                        </div>
                        <div class="col-xs-6 text-right">
                            <span style="color:red">【姓名或身分證字號擇一輸入】</span>
                            <div class="form-group">
                                <label class="control-label"><i class="fa fa-search"></i></label>
                                <input type="text" class="form-control" name="q" value="<?=$filter['q'];?>">
                            </div>
                        </div>
                    </form>
                </div>
                <form id="list-form" method="post">
                    <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
                    <table class="table table-bordered table-condensed table-hover">
                        <thead>
                            <tr>
                                <th style="width: 35px;" class="text-center"><input type="checkbox" id="chkall"></th>
                                <th class="sorting<?=($filter['sort']=='name asc')?'_asc':'';?><?=($filter['sort']=='name desc')?'_desc':'';?>" data-field="name" >姓名</th>
                                <th class="sorting<?=($filter['sort']=='username asc')?'_asc':'';?><?=($filter['sort']=='username desc')?'_desc':'';?>" data-field="username" >帳號</th>
                                <th class="sorting<?=($filter['sort']=='email asc')?'_asc':'';?><?=($filter['sort']=='email desc')?'_desc':'';?>" data-field="email" >E-Mail</th>
                                <th class="sorting<?=($filter['sort']=='telephone asc')?'_asc':'';?><?=($filter['sort']=='telephone desc')?'_desc':'';?>" data-field="telephone" >聯絡電話</th>
                                <th class="sorting<?=($filter['sort']=='enable asc')?'_asc':'';?><?=($filter['sort']=='enable desc')?'_desc':'';?>" data-field="enable" >啟用</th>
                                <th></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($list as $row) { ?>
                            <tr class="<?=($row['enable']==0)?'text-danger':'';?>">
                                <td class="text-center"><input type="checkbox" name="rowid[]" value="<?=$row['id'];?>"></td>
                                <td><?=$row['name'];?></td>
                                <td><?=$row['username'];?></td>
                                <td><?=$row['office_email'];?></td>
                                <td><?=$row['telephone'];?></td>
                                <td><?=($row['enable'] == 1)?'<span class="text-success">是</span>':'否';?></td>
                                <td class="text-center" id="btn_group">
                                    <?php if (isset($row['link_switch'])) { ?>
                                    <a type="button" class="btn btn-primary btn-xs btn-toggle" href="<?=$row['link_switch'];?>" <?=isset($easNadmins[$row['username']]) ? 'disabled' : ''?>>
                                        <i>切換帳號</i>
                                    </a>
                                    <?php } ?>
                                </td>
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

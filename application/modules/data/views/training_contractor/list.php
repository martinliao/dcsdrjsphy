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
                                <th class="sorting<?=($filter['sort']=='username asc')?'_asc':'';?><?=($filter['sort']=='username desc')?'_desc':'';?>" data-field="username" >帳號</th>
                                <th class="sorting<?=($filter['sort']=='name asc')?'_asc':'';?><?=($filter['sort']=='name desc')?'_desc':'';?>" data-field="name" >姓名</th>
                                <th class="sorting<?=($filter['sort']=='co_usrnick asc')?'_asc':'';?><?=($filter['sort']=='co_usrnick desc')?'_desc':'';?>" data-field="co_usrnick" >暱稱</th>
                                <th class="sorting<?=($filter['sort']=='bureau_name asc')?'_asc':'';?><?=($filter['sort']=='bureau_name desc')?'_desc':'';?>" data-field="bureau_name" >局處名稱</th>
                                <th class="sorting<?=($filter['sort']=='job_title asc')?'_asc':'';?><?=($filter['sort']=='job_title desc')?'_desc':'';?>" data-field="job_title" >職稱</th>
                                <th class="sorting<?=($filter['sort']=='office_tel asc')?'_asc':'';?><?=($filter['sort']=='office_tel desc')?'_desc':'';?>" data-field="office_tel" >公司電話</th>
                                <th class="sorting<?=($filter['sort']=='office_fax asc')?'_asc':'';?><?=($filter['sort']=='office_fax desc')?'_desc':'';?>" data-field="office_fax" >公司傳真</th>
                                <th class="sorting<?=($filter['sort']=='email asc')?'_asc':'';?><?=($filter['sort']=='email desc')?'_desc':'';?>" data-field="email" >Email</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($list as $row) { ?>
                            <tr>
                                <td><?=$row['username'];?></td>
                                <td><?=$row['name'];?></td>
                                <td><?=$row['co_usrnick'];?></td>
                                <td><?=$row['bureau_name'];?></td>
                                <td><?=$row['job_title_name'];?></td>
                                <td><?=$row['office_tel'];?></td>
                                <td><?=$row['office_fax'];?></td>
                                <td><?=$row['email'];?></td>
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

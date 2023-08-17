
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="fa fa-list fa-lg"></i> <?=$_LOCATION['name'];?>
            </div>

            <div class="panel-body">
                <div class="row">
                    <form id="filter-form" role="form" class="form-inline">
                        <div class="col-xs-12" >
                            <div class="form-group">
                                <label class="control-label">角色</label>
                                <?php
                                    $choices['group'] = array(''=>'請選擇角色') + $choices['group'];
                                    echo form_dropdown('user_group_id', $choices['group'], $filter['user_group_id'], 'class="form-control"');
                                ?>
                            </div>
                        </div>
                        <div class="col-xs-12" >
                            <div class="form-group">
                                <label class="control-label">局處</i></label>
                                <input type="text" class="form-control" name="b_name" value="<?=$filter['b_name'];?>">
                            </div>
                            <div class="form-group">
                                <label class="control-label">帳號</i></label>
                                <input type="text" class="form-control" name="username" value="<?=$filter['username'];?>">
                            </div>
                            <div class="form-group">
                                <label class="control-label">身分證</i></label>
                                <input type="text" class="form-control" name="idno" value="<?=$filter['idno'];?>">
                            </div>
                            <div class="form-group">
                                <label class="control-label">姓名</i></label>
                                <input type="text" class="form-control" name="name" value="<?=$filter['name'];?>">
                            </div>
                            <button class="btn btn-info btn-sm">查詢</button>
                            <?php if(isset($role_import)){ ?>
                            <a class="btn btn-info btn-sm" onclick="importCSV()" >匯入</a>
                            <?php } ?>
                        </div>
                        <div class="col-xs-6" >
                            <div class="form-group">
                                <label class="control-label">顯示筆數</label>
                                <?php
                                    echo form_dropdown('rows', $choices['rows'], $filter['rows'], 'class="form-control"');
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
                                <th style="width: 35px;" class="text-center"><input type="checkbox" id="chkall"></th>
                                <th>局處名稱</th>
                                <th>姓名</th>
                                <th>身分證</th>
                                <th>帳號</th>
                                <th>角色</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($list as $row) { ?>
                            <tr>
                                <td class="text-center"><input type="checkbox" name="rowid[]" value="<?=$row['id'];?>"></td>
                                <td><?=$row['b_name'];?></td>
                                <td><?=$row['name'];?></td>
                                <td><?=$row['idno'];?></td>
                                <td><?=$row['username'];?></td>
                                <td><?=$choices['group'][$row['group_id']];?></td>
                                <td class="text-center" id="btn_group">

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

<!-- Page-Level Demo Scripts - Tables - Use for reference -->
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

function importCSV()
{
    var myW=window.open('<?=$role_import;?>','import','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,height=500,width=700');
    myW.focus();
}
</script>

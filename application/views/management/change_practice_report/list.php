<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel&#45;heading">
                <i class="fa fa-list fa-lg"></i> <?=$_LOCATION['name'];?>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <div class="row">
                    <form id="filter-form" role="form" class="form-inline">
                        <input type="hidden" name="sort" value="" />
                        <div class="col-xs-12">
                            <div class="form-group">
                                <label class="control-label">年度</label>
                                <?php
                                    echo form_dropdown('query_year', $choices['query_year'], $filter['query_year'], 'class="form-control"');
                                ?>
                            </div>
                            <div class="form-group">
                                <label class="control-label">班期代碼</label>
                                <input type="text" class="form-control" name="class_no" value="<?=$filter['class_no']?>">
                            </div>
                            <div class="form-group">
                                <label class="control-label">班期名稱</label>
                                <input type="text" class="form-control" name="class_name" value="<?=$filter['class_name']?>">
                            </div>
                            <button class="btn btn-info btn-sm">查詢</button>
                            <div class="row">
                                <div class="col-md-12">
                                    <label class="control-label">顯示筆數</label>
                                    <?php
                                    echo form_dropdown('rows', $choices['rows'], $filter['rows'], 'class="form-control" onchange="sendFun()"');
                                ?>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <form action="<?=base_url("management/change_practice_report/detail")?>">
                    <!-- /.table head -->
                    <table class="table table-bordered table-condensed table-hover">
                        <thead>
                            <tr bgcolor="#8CBBFF">
                                <th><input type="checkbox" id="checkAll" />選取</th>
                                <th>班期代碼</th>
                                <th>期別</th>
                                <th>班期名稱</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($requires as $require): ?>
                            <tr>
                                <td><input type="checkbox" name="seq_no[]" value="<?=$require->seq_no?>" /></td>
                                <td><?=$require->class_no?></td>
                                <td><?=$require->term?></td>
                                <td><a href="<?=base_url("management/change_practice_report/detail?seq_no={$require->seq_no}") ?>"><?=$require->class_name?></a>
                                </td>
                            </tr>
                            <tr>
                            <?php endforeach ?>
                        </tbody>
                    </table>
                   <button class="btn btn-info btn-sm">確定</button>                    
                </form>
                <!-- /.table end -->
                <div class="row">
                    <div class="col-lg-4">
                        Showing 10 entries
                    </div>
                    <div class="col-lg-8  text-right">
                        <?=$this->pagination->create_links();?>
                    </div>
                </div>
            </div>
            <!-- /.panel -->
        </div>
        <!-- /.col-lg-12 -->
    </div>
</div>
<!-- /.row -->
<script src="<?=HTTP_PLUGIN;?>jquery.highlight-3.js"></script>
<script>
/*
    $(document).ready(function() {
    $('#filter-form select').change(function(){
        $('#filter-form').submit();
    });

    <?php if (isset($filter['q']) && $filter['q'] != ''){ ?>
    $('#list-form').highlight('<?=$filter['q'];?>');
    <?php } ?>
});

<!-- /.select all head -->
$(function(){
$('#checkAll').change(function() {
    //get all checkbox which want to change
    var checkboxes = $(this).closest('form').find('input[name="seq_no[]"]:checkbox');
    if($(this).is(':checked')) {
        checkboxes.prop('checked', 'checked');
    } else {
        checkboxes.removeAttr('checked');
    }
});
  $('input[name="check"]').change(function(){
  checkOrRemoveCheckAll();
  });
});
function checkOrRemoveCheckAll(){
if($('input[name="check"]:checked').length == $('input[name="check"]').length)
    {
        $('#checkAll').prop("checked", "checked");
    }
    else
    {
        $('#checkAll').removeAttr("checked");
    }
}
<!-- /.select all end -->*/
</script>
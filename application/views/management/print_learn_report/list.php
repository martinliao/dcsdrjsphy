<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel&#45;heading">
                <i class="fa fa-list fa-lg"></i> <?=$_LOCATION['name'];?>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <form id="filter-form" role="form" class="form-inline">
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="form-group">
                                <label class="control-label">年度</label>
                                <?php
                                    echo form_dropdown('query_year', $choices['query_year'], $filter['query_year'], 'class="form-control"');
                                ?>
                            </div>
                            <div class="form-group">
                                <label class="control-label">班期代碼:</label>
                                <input type="text" name="class_no" class="form-control" value="<?=$filter['class_no']?>">
                            </div>
                            <div class="form-group">
                                <label class="control-label">班期名稱:</label>
                                <input type="text" name="class_name" class="form-control" value="<?=$filter['class_name']?>">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <label class="control-label">顯示筆數</label>
                            <?php
                                echo form_dropdown('rows', $choices['rows'], $filter['rows'], 'class="form-control" onchange="sendFun()"');
                            ?>
                            <button class="btn btn-info btn-sm">搜尋</button>
                        </div>
                    </div>
                    
                </form>
                <!-- /.table head -->
                <table class="table table-bordered table-condensed table-hover">
                    <thead>
                        <tr style="background:#8CBBFF">
                            <th class="text-center"><input type="checkbox" id="chkall" onclick="checkAll(this,'seq_no[]')">全選</th>
                            <th class="text-center">班期代碼</th>
                            <th class="text-center">期別</th>
                            <th class="text-center">班期名稱</th>   
                        </tr>
                    </thead>
                    <tbody>
                        <form action="<?=base_url("management/print_learn_report/detail")?>">
                        <?php foreach($requires as $require): ?>
                        <tr class="text-center">
                            <td><input type="checkbox" name="seq_no[]" value="<?=$require->seq_no?>"></td>
                            <td><?=$require->class_no?></td>
                            <td><?=$require->term?></td>
                            <td><a href="<?=base_url("management/print_learn_report/detail?seq_no={$require->seq_no}")?>"><?=$require->class_name?></a></td>
                        </tr>
                        <?php endforeach ?>
                        <input type="submit" name="" class="btn btn-info btn-sm" value="確定">
                        </form>
                    </tbody>
                </table>
                <form>
                    <div class="row ">
                        <div class="col-lg-4">
                            Showing 10 entries
                        </div>
                        <div class="col-lg-8  text-right">
                            <?=$this->pagination->create_links();?>
                        </div>
                    </div>
                </form>
            </div>
            <!-- /.panel -->
        </div>
        <!-- /.col-lg-12 -->
    </div>
</div>
<script>
function checkAll(id,check)
{
    var checkboxs=document.getElementsByName(check);
    for(var i=0;i<checkboxs.length;i++)
    {
        checkboxs[i].checked=id.checked;
    }
}
</script>
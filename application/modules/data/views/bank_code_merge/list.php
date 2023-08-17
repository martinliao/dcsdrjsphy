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
                            <label class="control-label">關鍵字查詢</label>
                            <input type="text" class="form-control" name="q" value="<?=$filter['q'];?>">
                            <button class="btn btn-info">查詢</button>
                            <a onclick="btn_import()" class="btn btn-info">匯入</a>
                        </div>

                        <div class="col-xs-12">
                            <label class="control-label">顯示筆數</label>
                            <?php
                                echo form_dropdown('rows', $choices['rows'], $filter['rows'], 'class="form-control" onchange="sendFun()"');
                            ?>
                        </div>
                    </div>
                </form>
                <!-- /.table head -->
                <table class="table table-bordered table-condensed table-hover">
                    <thead>
                        <tr>
                            <th class="text-center">銀行代碼</th>
                            <th class="text-center">銀行名稱</th>
                            <th class="text-center">異動</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($list as $row){ ?>
                        <tr class="text-center">
                            <td><?=$row['item_id'];?></td>
                            <td><?=$row['name'];?></td>
                            <td><a  href="<?=$row['link_edit'];?>" class="btn btn-info">異動</a></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <div class="row">
                    <div class="col-lg-4">
                        Showing <?=$filter['offset']+1;?> to <?=(ceil($filter['total']/$filter['rows']) == $filter['page'])?$filter['total']:$filter['offset']+$filter['rows'];?> of <?=$filter['total'];?> entries
                    </div>
                    <div class="col-lg-8  text-right">
                        <?=$this->pagination->create_links();?>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.panel -->
    </div>
    <!-- /.col-lg-12 -->
</div>
</div>
<script>
function btn_import() {
    window.open('<?=$btn_import;?>','openImport','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,height=530,width=600');
}
</script>
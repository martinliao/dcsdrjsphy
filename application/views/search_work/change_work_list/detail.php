<!-- <?php print_r($datas); ?> -->
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel&#45;heading">
                <i class="fa fa-list fa-lg"></i> <?=$_LOCATION['name'];?>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <!-- /.table head -->
                <table class="table table-bordered table-condensed table-hover">
                    <thead>
                        <tr class="text-center">
                            <th align="center">服務單位</th>
                            <th align="center">學號</th>
                            <th align="center">姓名</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($datas as $data): ?>
                        <tr>
                            <td><?= $data["description"] ?></td>
                            <td><?= $data["st_no"] ?></td>
                            <td><?= $data["name"] ?></td>
                        </tr>
                        <?php endforeach?>
                    </tbody>
                </table>
            </div>
            <!-- /.panel -->
        </div>
        <!-- /.col-lg-12 -->
    </div>
</div>
<div class="row">
    <div class="col-xs-12">
        <a href="<?=base_url('search_work/change_work_list')?>" class="btn btn-info">返回</a>
    </div>
</div>
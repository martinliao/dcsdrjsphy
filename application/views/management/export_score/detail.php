<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel&#45;heading">
                <?=$_LOCATION['function']['name'] ;?>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">

                <!-- /.table head -->
                <table class="table table-bordered table-condensed table-hover">
                    <thead>
                        <tr>
                            <th class="text-center">下載</th>
                            <th class="text-center">檔案名稱</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($fileAry as $row){ ?>
                        <tr>
                            <td><a href="<?= $row['PATH']; ?>">下載檔案</a></td>
                            <td><?=$row['NAME'];?></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
            <!-- /.panel -->
        </div>
        <!-- /.col-lg-12 -->
    </div>
</div>
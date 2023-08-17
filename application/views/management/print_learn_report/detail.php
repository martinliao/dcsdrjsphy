
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel&#45;heading">
                <i class="fa fa-list fa-lg"></i> <?=$_LOCATION['name'];?>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <!-- /.table head -->
                <div>
                    <table class="table table-bordered table-condensed table-hover">
                        <thead>
                            <tr>
                                <th>發文單位</th>
                            </tr>
                        </thead>
                        <tbody>    
                            <tr>
                                <td>
                                <?php foreach($bureaus as $key => $bureau): ?>
                                    <?=$bureau->name?>
                                    <?php if(count($bureaus) != $key + 1 ): ?>
                                        <?="、"?>
                                    <?php endif ?>                             
                                <?php endforeach ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- /.panel -->
        </div>
        <!-- /.col-lg-12 -->
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="fa fa-list fa-lg"></i> 進度查詢
            </div>
            <!-- /.panel-heading -->
            
            <div class="panel-body">
                <table class="table table-bordered table-condensed table-hover">
                    <thead>
                        <tr>
                            <th>明細</th>
                            <th>異動時間</th>
                            <th>輸入者</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php 
                        for($i=0;$i<count($form);$i++){
                            if(!empty($form[$i]['name'])){
                                $name = $form[$i]['name'];
                            } else {
                                $name = 'SYSTEM';
                            }
                            echo '<tr>';
                            echo '<td>'.$form[$i]['detail'].'</td>';
                            echo '<td>'.$form[$i]['modify_time'].'</td>';
                            echo '<td>'.$name.'</td>';
                            echo '</tr>';
                        }
                    ?>
                    </tbody>
                </table>
            </div>
            <!-- /.panel-body -->
        </div>
        <!-- /.panel -->
    </div>
    <!-- /.col-lg-12 -->
</div>
<!-- /.row -->
<script src="<?=HTTP_PLUGIN;?>jquery.highlight-3.js"></script>

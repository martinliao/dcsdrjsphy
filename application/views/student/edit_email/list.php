<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="fa fa-list fa-lg"></i> <?=$_LOCATION['name'];?>
            </div>
            <!-- /.panel-heading -->

            <div class="panel-body">
                    <p style="color: red">※〈私人信箱〉如有誤，請學員點選修改鍵進行修正，俾順利收到課程研習通知。</p>
                    <p style="color: red">※〈公務信箱〉係介接人事WebHR資訊系統，如有誤須惠請機關人事修正。</p>
                    <table class="table table-bordered table-condensed table-hover">
                        <thead>
                            <tr>
                                <th>私人Email信箱</th>
                                <th>功能</th>
                            </tr>
                        </thead>
                        <tbody>
                       
                            <tr>
                                <td><?=$email;?></td>
                                <td class="text-center" id="btn_group">
                                    <?php if (isset($link_edit)) { ?>
                                    <a type="button" class="btn btn-outline btn-warning btn-xs btn-toggle" href="<?=$link_edit;?>">
                                        <i class="fa fa-pencil fa-lg"></i>
                                    </a>
                                    <?php } ?>
                                </td>
                            </tr>
    
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

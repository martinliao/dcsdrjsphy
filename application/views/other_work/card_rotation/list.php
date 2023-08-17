<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel&#45;heading">
                <i class="fa fa-list fa-lg"></i> <?=$_LOCATION['name'];?>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                
                <div class="row">
                    <div class="col-xs-12">
                        <a href="<?=base_url('other_work/card_rotation/add') ?>" class="btn btn-info" style="margin-bottom: 10px">新增</a>
                        <table class="table table-hover table-condensed table-bordered">
                            <thead>
                            <tr>
                                <th class="text-center">修改</th>
                                <th class="text-center">名稱</th>
                                <th class="text-center">網址</th>
                                <th class="text-center">建立時間</th>
                                <th class="text-center">建立者</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                                for($i=0;$i<count($list);$i++){
                                    echo '<tr>';
                                    echo '<td class="text-center">';
                                    echo '<a href="'.$list[$i]['link_edit'].'" class="btn btn-info" style="margin-right:5px">修改</a>';
                                    echo '<a href="#" class="btn btn-info" onclick="delFun(\''.$list[$i]['link_del'].'\')">刪除</a>';
                                    echo '</td>';
                                    echo '<td>'.$list[$i]['name'].'</td>';
                                    echo '<td>'.$list[$i]['url'].'</td>';
                                    echo '<td>'.date('Y-m-d',strtotime($list[$i]['create_datetime'])).'</td>';
                                    echo '<td class="text-center">'.$list[$i]['creator'].'</td>';
                                    echo '</tr>';
                                }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- /.panel -->
        </div>
        <!-- /.col-lg-12 -->
    </div>
</div>

<script src="<?=HTTP_PLUGIN;?>jquery.highlight-3.js"></script>
<script>
function delFun(del_url){
  if(confirm("是否確認刪除")){
    location.href = del_url;
  } 
}
</script>

<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="fa fa-list fa-lg"></i> <?=$_LOCATION['name'];?>
            </div>
            <!-- /.panel-heading -->

            <div class="panel-body">
                <div class="row">
                    <form id="filter-form" role="form" class="form-inline">
                        <input type="hidden" name="sort" value="" />
                        <input type="hidden" name="identity_type" value="3" />
                        
                        <div class="col-xs-12" >
                            <div class="form-group">
                                <label class="control-label" style="min-width:100px;">新身分證字號</i></label>
                                <input type="text" class="form-control" id="idno" name="idno" value="<?=$filter['idno'];?>">
                            </div>
                        </div>
                        <div class="col-xs-12" >
                            <div class="form-group">
                                <label class="control-label" style="min-width:100px;">原始身分證</i></label>
                                <input type="text" class="form-control" id="old_idno" name="old_idno" value="<?=$filter['old_idno'];?>">
                            </div>
                        </div>
                        <div class="col-xs-12" >
                            <div class="form-group">
                                <label class="control-label" style="min-width:100px;">居留證號</i></label>
                                <input type="text" class="form-control" id="rpno" name="rpno" value="<?=$filter['rpno'];?>">
                            </div>
                        </div>
                        <div class="col-xs-12" >
                            <div class="form-group">
                                <label class="control-label" style="min-width:100px;">講師名稱</i></label>
                                <input type="text" class="form-control" name="name" value="<?=$filter['name'];?>">
                            </div>
                        </div>
                    
                        <div class="col-xs-12" >
                            <div class="form-group">
                                <label class="control-label">顯示筆數</label>
                                <?php
                                    echo form_dropdown('rows', $choices['rows'], $filter['rows'], 'class="form-control"');
                                ?>
                            </div>
                        </div>
                        <div class="col-xs-12" >
                            <div class="form-group">
                                <button type="submit" class="btn btn-info">查詢</button>
                                <a id="clear" class="btn btn-warning">清除</a>
                               
                            </div>
                        </div>

                        <!-- <div class="col-xs-6 text-right">
                            <button>查詢</button>
                        </div> -->
                    </form>
                </div>
                <form id="list-form" method="post">
                    <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
                    <table class="table table-bordered table-condensed table-hover">
                        <thead>
                            <tr style="background-color: blue;color: white">
                                <th style="width: 15%;">功能</th>
                                <th  style="width: 8%;" class="sorting<?=($filter['sort']=='idno asc')?'_asc':'';?><?=($filter['sort']=='idno desc')?'_desc':'';?>" data-field="idno" >新身分證字號</th>
                                <th  style="width: 8%;" class="sorting<?=($filter['sort']=='old_idno asc')?'_asc':'';?><?=($filter['sort']=='old_idno desc')?'_desc':'';?>" data-field="old_idno" >原始身分證</th>
                                <th  style="width: 8%;" class="sorting<?=($filter['sort']=='rpno asc')?'_asc':'';?><?=($filter['sort']=='rpno desc')?'_desc':'';?>" data-field="rpno" >居留證</th>
                                <th  style="width: 17%;"  class="sorting<?=($filter['sort']=='name asc')?'_asc':'';?><?=($filter['sort']=='name desc')?'_desc':'';?>" data-field="name" >姓名</th>
                                <th  style="width: 5%;" class="sorting<?=($filter['sort']=='teacher_type asc')?'_asc':'';?><?=($filter['sort']=='teacher_type desc')?'_desc':'';?>" data-field="teacher_type" >講師或助教</th>
                                <th  style="width: 5%;" class="sorting<?=($filter['sort']=='identity_type asc')?'_asc':'';?><?=($filter['sort']=='identity_type desc')?'_desc':'';?>" data-field="identity_type" >身分別</th>
                                <th  style="width: 30%;" >可授課程</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($list as $row) { ?>
                            <tr>
                                <td id="btn_group">
                                    <?php if (isset($row['link_view'])) { ?>
                                    <a type="button" class="btn btn-outline btn-success btn-xs btn-toggle" title="View" href="<?=$row['link_view'];?>">
                                        <i class="fa fa-eye fa-lg"></i>
                                    </a>
                                    <?php } ?>
                                    <?php if (isset($row['link_edit'])) { ?>
                                    <!-- <a type="button" class="btn btn-outline btn-warning btn-xs btn-toggle" href="<?=$row['link_edit'];?>"> -->
                                        <!-- <i class="fa fa-pencil fa-lg"></i> -->
                                        <a href="<?=$row['link_edit'];?>" class="btn btn-primary">修改</a>
                                    </a>
                                    <?php } ?>
                                    
                                    
                                    <?php
                                        $add_teacher_type = ($row['teacher_type'] == '1') ? '2' : '1';
                                    ?>
                                    
                                    <?php if (isset($row['link_delete'])) { ?>
                                    <button type="button" class="btn btn-outline btn-danger btn-xs" onclick="ajaxDelete(this, '確認要刪除選單「<?=$row['name'];?>」?', '<?=$row['link_delete'];?>')">
                                        <i class="fa fa-trash fa-lg"></i>
                                    </button>
                                    <?php } ?>
                                </td>
                                <td><?=$row['idno'];?></td>
                                <td><?=$row['old_idno'];?></td>
                                <td><?=$row['rpno'];?></td>
                                <td><?=$row['name'];?></td>
                                <td><?=$choices['teacher_type'][$row['teacher_type']];?></td>
                                <?php if(!empty($row['identity_type'])){ ?>
                                <td><?=$choices['identity_type'][$row['identity_type']];?></td>
                                <?php }else{ ?>
                                <td></td>
                                <?php } ?>
                                <td>
                                    <?php foreach($row['course_lis'] as $key => $course) { ?>
                                        <?=$key;?>-<?=$course;?><br>
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
                       
                        <?php if (isset($link_refresh)) { ?>
                            <a class="btn btn-default" href="<?=$link_refresh;?>" title="Refresh">重整</a>
                        <?php } ?>
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
<script src="<?=HTTP_PLUGIN;?>jquery.highlight-3.js"></script>
<script>
$(document).ready(function() {
    $('#filter-form select').change(function(){
        $('#filter-form').submit();
    });

    <?php if (isset($filter['q']) && $filter['q'] != ''){ ?>
    $('#list-form').highlight('<?=$filter['q'];?>');
    <?php } ?>

    $("#clear").click(function(){
        $("input[name=idno]")[0].value="";
        $("input[name=old_idno]")[0].value="";
        $("input[name=rpno]")[0].value="";
        $("input[name=name]")[0].value="";
        $("input[name=course_name]")[0].value="";
        $("input[name=queryFile]")[0].value="";
    });
});
function openCanteach(id){
        window.open("<?=base_url('management/lecture_notes_assignments?query_search_from=2B#')?>", "_blank", "toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,height=800,width=1500");
}

function addTeacher(addurl){
    var idno = document.getElementById('idno').value;
    location.href = addurl + '&key=' + idno;
}
</script>

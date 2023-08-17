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
                                    echo form_dropdown('year', $choices['year'], $filter['year'], 'class="form-control"');
                                ?>
                            </div>
                            <div class="form-group">
                                <label class="control-label">班期代碼:</label>
                                <input type="text" name="class_no" class="form-control" value="<?=$filter['class_no']?>">
                            </div>
                            <div class="form-group">
                                <label class="control-label">班期名稱:</label>
                                <input type="text" name="class_name" value="<?=$filter['class_name']?>" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <button class="btn btn-info btn-sm">搜尋</button>
                        </div>
                    </div>
                    <div class="row">
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
                        <tr bgcolor="#8CBBFF">
                            <th class="text-center">班期代碼</th>
                            <th class="text-center">期別</th>
                            <th class="text-center">班期名稱</th>
                            <th class="text-center" style="background-color: #facd7a">Mail研習記錄</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($requires as $require): ?>
                        <tr>
                            <td><a href="#" onclick="print('<?=$require->year?>', '<?=$require->class_no?>', '<?=$require->term?>')"><?=$require->class_no?></a></td>
                            <td><?=$require->term?></td>
                            <td><?=$require->class_name?></td>
                            <td class="text-center"><a href="<?=base_url('management/print_learn_list/mail_select/3?year='.$require->year.'&term='.$require->term.'&class_no='.$require->class_no.'&start_date='.date("Y-m-d",strtotime($require->start_date1)).'&end_date='.date("Y-m-d",strtotime($require->end_date1)).'&class_name='.$require->class_name)?>"><?=($require->learn_send==1)?'<font style="color:red">是</font>/設':'否/設'?></a></td>
                        </tr>
                        <?php endforeach ?>
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


<script type="text/javascript">
    function print(year, class_no, term){
        var url = "<?=base_url("management/print_learn_list/print?")?>year=" + year + "&class_no=" + class_no + "&term=" + term;
        var printWindow = window.open(url, "MsgWindow", "width=1024,height=800,toolbar=no, menubar=no, scrollbars=year, resizable=yes, location=no, status=no"); 
        printWindow.focus();
    }
</script>
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
                                <!--<select name='year' id='query_year'>
                                    <option value='109' selected>109</option>
                                    <option value='108'>108</option>
                                    <option value='107'>107</option>
                                    <option value='106'>106</option>
                                    <option value='105'>105</option>
                                    <option value='104'>104</option>
                                    <option value='103'>103</option>
                                </select>-->
                                <?php
                                    echo form_dropdown('query_year', $choices['query_year'], $filter['query_year'], 'class="form-control"');
                                ?>
                            </div>
                            <div class="form-group">
                                <label class="control-label">班期代碼:</label>
                                <input type="text" name="class_no" class="form-control">
                            </div>
                            <div class="form-group">
                                <label class="control-label">班期名稱:</label>
                                <input type="text" name="class_name" class="form-control">
                            </div>
                            <button class="btn btn-info btn-sm">查詢</button>
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
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($requires as $require): ?>
                        <tr>
                            <td class="text-center"><?=$require->class_no?></td>
                            <td><?=$require->term?></td>
                            <td><a href="<?=base_url("management/vm_transaction/detail?year={$require->year}&term={$require->term}&class_no={$require->class_no}&".createLastPageQuery())?>"><?=$require->class_name?></a></td>
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
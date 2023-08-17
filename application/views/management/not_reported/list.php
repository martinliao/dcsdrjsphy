<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel&#45;heading">
                <i class="fa fa-list fa-lg"></i> <?=$_LOCATION['name'];?>
                <!--<?php if (empty($filter['worker'])): ?>
                    <a href="<?=base_url("management/not_reported?worker=1")?>" class="btn btn-info btn-sm">承辦人</a>
                    <a href="<?=base_url("management/not_reported?worker=1")?>" class="btn btn-info btn-sm">管理者</a>
                <?php else :?>
                    <a href="<?=base_url("management/not_reported")?>" class="btn btn-info btn-sm">管理者</a>
                    <a href="<?=base_url("management/not_reported")?>" class="btn btn-info btn-sm">承辦人</a>
                <?php endif ?>-->            
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <form id="filter-form" role="form" class="form-inline">
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="form-group">
                                <label class="control-label">年度</label>
                                <?php
                                    echo form_dropdown('query_year', $choices['query_year'], $filter['query_year'], 'class="form-control"');
                                ?>
                                <!--<select name="query_year">
                                <option value="109">109</option>
                                <option value="108">108</option>
                                <option value="107">107</option>
                                <option value="106">106</option>
                                <option value="105">105</option>
                                <option value="104">104</option>
                                <option value="103">103</option>
                                <option value="102">102</option>
                                <option value="101">101</option>
                                <option value="100">100</option>
                                <option value="99">99</option>
                            </select>-->
                            </div>
                            <div class="form-group">
                                <label class="control-label">班期代碼:</label>
                                <input type="text" name="class_no" class="form-control" style="width: 95px;" value="<?=$filter['class_no']?>">
                            </div>
                            <div class="form-group">
                                <label class="control-label">班期名稱:</label>
                                <input type="text" name="class_name" class="form-control" style="width: 288px;" value="<?=$filter['class_name']?>">
                            </div>
                            <div class="form-group">
                                <label><input type="checkbox" name="checkAll" class="form-control"<?php if($filter['checkAll']=='on'){?> checked <?php }?>>查詢所有班期</label>
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
                            <th class="text-center">列序</th>
                            <th class="text-center">班期代碼</th>
                            <th class="text-center">班期期別</th>
                            <th class="text-center">班期名稱</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i=1;?>
                        <?php foreach ($requires as $key => $require): ?>
                        <tr>
                            <td><?=$i?></td>
                            <td><a href="<?=base_url("management/not_reported/detail?year={$require->year}&class_no={$require->class_no}&term={$require->term}")?>"><?=$require->class_no?></a></td>
                            <td><?=$require->term?></td>
                            <td><?=$require->class_name?></td>
                        </tr>
                        <?php $i++; endforeach ?>
                    </tbody>
                </table>
                <form>
                    <div class="row ">
                        <div class="col-lg-4">
                            Showing <?=count($requires)?> / <?=$paginate_config['total_rows']?> entries
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
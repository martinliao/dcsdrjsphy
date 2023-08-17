<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel&#45;heading">
                <i class="fa fa-list fa-lg"></i> <?=$_LOCATION['name'];?>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <form id="data-form" role="form" method="post" action="<?=$link_setup;?>">
                <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
                <tr>
                    <td>
                        <table class="table table-bordered table-condensed table-hover">
                            <tr>
                                <td>A. 設定【加入會員】提醒通知</td>
                                <td>
                                    <table class="table table-bordered table-condensed table-hover">
                                        <tr>
                                            <td><input type="checkbox" name="urge[register][-10]" value="1"
                                                    <?=(!empty($list[0]['enable']) && $list[0]['enable']=='1')?'checked':''?>>10天前通知</td>
                                        </tr>
                                        <tr>
                                            <td><input type="checkbox" name="urge[register][-5]" value="1" <?=(!empty($list[1]['enable']) && $list[1]['enable']=='1')?'checked':''?>>5天前通知
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td>B. 設定【自動稽催】排程</td>
                                <td>
                                    <table class="table table-bordered table-condensed table-hover">
                                        <tr>
                                            <td rowspan="4">開課前</td>
                                            <td>寄送時間</td>
                                            <td colspan="2">寄送對象</td>
                                        </tr>
                                        <tr>
                                            <td><input type="checkbox" name="urge[openClass][-7][enable]" value="1"
                                                    <?=(!empty($list[2]['enable']) && $list[2]['enable']=='1')?'checked':''?>>7天前</td>
                                            <td><input type="checkbox" name="urge[openClass][-7][staff]" value="1" <?=(!empty($list[2]['yn_staff']) && $list[2]['yn_staff']=='1')?'checked':''?>>人事
                                            </td>
                                            <td><input type="checkbox" name="urge[openClass][-7][stu]" value="1"
                                                    <?=(!empty($list[2]['yn_stud']) && $list[2]['yn_stud']=='1')?'checked':''?>>學員</td>
                                        </tr>
                                        <tr>
                                            <td><input type="checkbox" name="urge[openClass][-3][enable]" value="1"
                                                    <?=(!empty($list[3]['enable']) && $list[3]['enable']=='1')?'checked':''?>>3天前</td>
                                            <td><input type="checkbox" name="urge[openClass][-3][staff]" value="1" <?=(!empty($list[3]['yn_staff']) && $list[3]['yn_staff']=='1')?'checked':''?>>人事
                                            </td>
                                            <td><input type="checkbox" name="urge[openClass][-3][stu]" value="1"
                                                    <?=(!empty($list[3]['yn_stud']) && $list[3]['yn_stud']=='1')?'checked':''?>>學員</td>
                                        </tr>
                                        <tr>
                                            <td><input type="checkbox" name="urge[openClass][-1][enable]" value="1"
                                                    <?=(!empty($list[4]['enable']) && $list[4]['enable']=='1')?'checked':''?>>1天前</td>
                                            <td><input type="checkbox" name="urge[openClass][-1][staff]" value="1" <?=(!empty($list[4]['yn_staff']) && $list[4]['yn_staff']=='1')?'checked':''?>>人事
                                            </td>
                                            <td><input type="checkbox" name="urge[openClass][-1][stu]" value="1"
                                                    <?=(!empty($list[4]['yn_stud']) && $list[4]['yn_stud']=='1')?'checked':''?>>學員</td>
                                        </tr>
                                        <tr>
                                            <td rowspan="4">開課後</td>
                                            <td>寄送時間</td>
                                            <td colspan="2">寄送對象</td>
                                        </tr>
                                        <tr>
                                            <td><input type="checkbox" name="urge[openClass][1][enable]" value="1"
                                                    <?=(!empty($list[5]['enable']) && $list[5]['enable']=='1')?'checked':''?>>1天後</td>
                                            <td><input type="checkbox" name="urge[openClass][1][staff]" value="1"
                                                    <?=(!empty($list[5]['yn_staff']) && $list[5]['yn_staff']=='1')?'checked':''?>>人事</td>
                                            <td><input type="checkbox" name="urge[openClass][1][stu]" value="1"
                                                    <?=(!empty($list[5]['yn_stud']) && $list[5]['yn_stud']=='1')?'checked':''?>>學員</td>
                                        </tr>
                                        <tr>
                                            <td><input type="checkbox" name="urge[openClass][2][enable]" value="1"
                                                    <?=(!empty($list[6]['enable']) && $list[6]['enable']=='1')?'checked':''?>>2天後</td>
                                            <td><input type="checkbox" name="urge[openClass][2][staff]" value="1" <?=(!empty($list[6]['yn_staff']) && $list[6]['yn_staff']=='1')?'checked':''?>>人事
                                            </td>
                                            <td><input type="checkbox" name="urge[openClass][2][stu]" value="1"
                                                    <?=(!empty($list[6]['yn_stud']) && $list[6]['yn_stud']=='1')?'checked':''?>>學員</td>
                                        </tr>
                                        <tr>
                                            <td><input type="checkbox" name="urge[openClass][3][enable]" value="1"
                                                    <?=(!empty($list[7]['enable']) && $list[7]['enable']=='1')?'checked':''?>>3天後</td>
                                            <td><input type="checkbox" name="urge[openClass][3][staff]" value="1" <?=(!empty($list[7]['yn_staff']) && $list[7]['yn_staff']=='1')?'checked':''?>>人事
                                            </td>
                                            <td><input type="checkbox" name="urge[openClass][3][stu]" value="1"
                                                    <?=(!empty($list[7]['yn_stud']) && $list[7]['yn_stud']=='1')?'checked':''?>>學員</td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" class="text-center">
                                    <input type="submit" class="btn btn-info" value="設定"></input>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <!-- /.table end -->
            </form>
            </div>
            <!-- /.panel -->
        </div>
        <!-- /.col-lg-12 -->
    </div>
</div>
<!-- /.row -->
<script src="<?=HTTP_PLUGIN;?>jquery.highlight-3.js"></script>
<?php
if (
    !empty($form['map1']) || !empty($form['map2']) || !empty($form['map3']) || !empty($form['map4']) || !empty($form['map5']) || !empty($form['map6']) || !empty($form['map7']) || !empty($form['map8'])
    || !empty($form['map9']) || !empty($form['map10']) || !empty($form['map11'])
) {
    $fmap = 'Y';
} else {
    $fmap = 'N';
}
?>
<?php if (validation_errors()) { ?>
    <div class="alert alert-danger">
        <button class="close" data-dismiss="alert" type="button">×</button>
        <?= validation_errors(); ?>
    </div>
<?php } ?>

<style type="text/css">
    .checkbox-inline input[type=checkbox],
    .radio-inline input[type=radio] {
        position: absolute;
        margin-top: 4px \9;
        margin-left: -14px;
    }

    /*.radio_margin{
        margin-left: -13px;
    }*/
</style>

<form id="data-form" role="form" method="post" action="<?= $link_save2; ?>">
    <input type="hidden" name="<?= $csrf['name']; ?>" value="<?= $csrf['hash']; ?>" />
    <p style="color: red">目前為預設值修改模式</p>
    <div class="form-group col-xs-5">
        <label class="control-label">目標</label>
        <input class="form-control" id="obj" name="obj" placeholder="" value="<?= set_value('obj', $form['obj']); ?>">
        <?= form_error('obj'); ?>
    </div>
    <div class="form-group col-xs-1">
        <div class="checkbox-inline text-right" style="margin-left: 0px">
            <label>
                <input type="checkbox" name="force_obj" value="Y" style="zoom:1.5;" <?= set_checkbox('force_obj', 'Y', $form['force_obj'] == 'Y' ? TRUE : FALSE); ?>>
                <span>強制</span>
            </label>
        </div>
    </div>

    <div class="form-group col-xs-5">
        <label class="control-label">對象</label>
        <input class="form-control" id="respondant" name="respondant" placeholder="" value="<?= set_value('respondant', $form['respondant']); ?>">
        <?= form_error('respondant'); ?>
    </div>
    <div class="form-group col-xs-1">
        <div class="checkbox-inline text-right" style="margin-left: 0px">
            <label>
                <input type="checkbox" name="force_respondant" value="Y" style="zoom:1.5;" <?= set_checkbox('force_respondant', 'Y', $form['force_respondant'] == 'Y' ? TRUE : FALSE); ?>>
                <span>強制</span>
            </label>
        </div>
    </div>

    <div class="form-group col-xs-5">
        <label class="control-label">鐘點費類別</label>
        <?php
        echo form_dropdown('ht_class_type', $choices['ht_class_type'], set_value('ht_class_type', $form['ht_class_type']), "class='form-control' $user_bureau_status");
        ?>
        <?= form_error('ht_class_type'); ?>
    </div>
    <div class="form-group col-xs-1">
        <div class="checkbox-inline text-right" style="margin-left: 0px">
            <label>
                <input type="checkbox" name="force_ht_class_type" value="Y" style="zoom:1.5;" <?= set_checkbox('force_ht_class_type', 'Y', $form['force_ht_class_type'] == 'Y' ? TRUE : FALSE); ?>>
                <span>強制</span>
            </label>
        </div>
    </div>

    <div class="form-group col-xs-6 required <?=form_error('no_persons')?'has-error':'';?>">
        <label class="control-label">本期人數</label>
        <input class="form-control" id="no_persons" name="no_persons" placeholder="" value="<?=set_value('no_persons', $form['no_persons']); ?>">
        <?=form_error('no_persons'); ?>
    </div>

    <div class="form-group col-xs-6 <?=form_error('min_no_persons')?'has-error':'';?>">
        <label class="control-label">人數下限</label>
        <input class="form-control" id="min_no_persons" name="min_no_persons" placeholder="" value="<?=set_value('min_no_persons', $form['min_no_persons']); ?>">
        <?=form_error('min_no_persons'); ?>
    </div>

    <div class="form-group col-xs-6 <?=form_error('max_no_persons')?'has-error':'';?>">
        <label class="control-label">人數上限</label>
        <input class="form-control" id="max_no_persons" name="max_no_persons" placeholder="" value="<?=set_value('max_no_persons', $form['max_no_persons']); ?>">
        <?=form_error('max_no_persons'); ?>
    </div>

    <div class="form-group col-xs-5">
        <label class="control-label">班期屬性</label>
        <?php
        echo form_dropdown('classify', $choices['classify'], set_value('classify', $form['classify']), "class='form-control'");
        ?>
        <?= form_error('classify'); ?>
    </div>
    <div class="form-group col-xs-1">
        <div class="checkbox-inline text-right" style="margin-left: 0px">
            <label>
                <input type="checkbox" name="force_classify" value="Y" style="zoom:1.5;" <?= set_checkbox('force_classify', 'Y', $form['force_classify'] == 'Y' ? TRUE : FALSE); ?>>
                <span>強制</span>
            </label>
        </div>
    </div>

    <div class="form-group col-xs-6">
        <label class="control-label">訓練方式(一)住班或通勤</label>
        <?php
        echo form_dropdown('class_cate', $choices['class_cate'], set_value('class_cate', $form['class_cate']), 'class="form-control"');
        ?>
        <?= form_error('class_cate'); ?>
    </div>

    <div class="form-group col-xs-6">
        <label class="control-label">訓練方式(二)全天或半天</label>
        <?php
        echo form_dropdown('class_cate1', $choices['class_cate1'], set_value('class_cate1', $form['class_cate1']), 'class="form-control"');
        ?>
        <?= form_error('class_cate1'); ?>
    </div>

    <div class="form-group col-xs-6">
        <label class="control-label">訓練方式(三)連續或間斷</label>
        <?php
        echo form_dropdown('class_cate2', $choices['class_cate2'], set_value('class_cate2', $form['class_cate2']), 'class="form-control"');
        ?>
        <?= form_error('class_cate2'); ?>
    </div>

    <div class="form-group col-xs-6">
        <label class="control-label">訓練期程(週)</label>
        <input class="form-control" id="range_week" name="range_week" placeholder="" value="<?= set_value('range_week', $form['range_week']); ?>">
        <?= form_error('range_week'); ?>
    </div>

    <div class="form-group col-xs-6">
        <label class="control-label">訓練期程(小時)</label>
        <input class="form-control" id="range" name="range" placeholder="" value="<?= set_value('range', $form['range']); ?>">
        <?= form_error('range'); ?>
    </div>

    <div class="form-group col-xs-5 required <?= form_error('weights') ? 'has-error' : ''; ?>">
        <label class="control-label">權重</label>
        <input class="form-control" id="weights" name="weights" placeholder="" value="<?= set_value('weights', ($form['weights'] > 0) ? $form['weights'] : '1'); ?>">
        <?= form_error('weights'); ?>
    </div>
    <div class="form-group col-xs-1">
        <div class="checkbox-inline text-right" style="margin-left: 0px">
            <label>
                <input type="checkbox" name="force_weights" value="Y" style="zoom:1.5;" <?= set_checkbox('force_weights', 'Y', $form['force_weights'] == 'Y' ? TRUE : FALSE); ?>>
                <span>強制</span>
            </label>
        </div>
    </div>

    <div class="form-group col-xs-5">
        <label class="control-label">選員方式</label>
        <?php
        echo form_dropdown('app_type', $choices['app_type'], set_value('app_type', $form['app_type']), 'class="form-control"');
        ?>
        <?= form_error('app_type'); ?>
    </div>
    <div class="form-group col-xs-1">
        <div class="checkbox-inline text-right" style="margin-left: 0px">
            <label>
                <input type="checkbox" name="force_app_type" value="Y" style="zoom:1.5;" <?= set_checkbox('force_app_type', 'Y', $form['force_app_type'] == 'Y' ? TRUE : FALSE); ?>>
                <span>強制</span>
            </label>
        </div>
    </div>


    <div class="form-group col-xs-6">
        <label class="control-label">草案、確定計畫、新增計畫</label>
        <?php
        echo form_dropdown('class_status', $choices['class_status'], set_value('class_status', $form['class_status']), "class='form-control' ");
        ?>
        <?= form_error('class_status'); ?>
    </div>
    <div class="form-group col-xs-6">
        <label class="control-label">季別</label>
        <?php
        echo form_dropdown('reason', $choices['reason'], set_value('reason', $form['reason']), "class='form-control'");
        ?>
        <?= form_error('reason'); ?>
    </div>

    <div class="form-group col-xs-5">
        <label class="control-label">同班不同期可重複受訓否</label>
        <div>
            <div class="radio-inline">
                <label>
                    <input id="isappsameclass" type="radio" value="1" style="zoom:1.5;" name="isappsameclass" <?= $form['isappsameclass'] == '1' ? 'checked' : '' ?>>
                    <span>YES</span>
                </label>
            </div>
            <div class="radio-inline">
                <label>
                    <input id="isappsameclass" type="radio" value="2" style="zoom:1.5;" name="isappsameclass" <?= (empty($form['isappsameclass']) || $form['isappsameclass'] == '2') ? 'checked' : '' ?>>
                    <span>NO</span>
                </label>
            </div>
        </div>
    </div>
    <div class="form-group col-xs-1">
        <div class="checkbox-inline text-right" style="margin-left: 0px">
            <label>
                <input type="checkbox" name="force_isappsameclass" value="Y" style="zoom:1.5;" <?= set_checkbox('force_isappsameclass', 'Y', $form['force_isappsameclass'] == 'Y' ? TRUE : FALSE); ?>>
                <span>強制</span>
            </label>
        </div>
    </div>
    <div class="form-group col-xs-5">
        <label class="control-label">參訓限制條件權限下放</label>
        <div>
            <div class="radio-inline">
                <label>
                    <input id="is_start" type="radio" value="Y" name="is_start" style="zoom:1.5;" <?= $form['is_start'] == 'Y' ? 'checked' : '' ?>>
                    <span>YES</span>
                </label>
            </div>
            <div class="radio-inline">
                <label>
                    <input id="is_start" type="radio" value="N" name="is_start" style="zoom:1.5;" <?= (empty($form['is_start']) || $form['is_start'] == 'N') ? 'checked' : '' ?>>
                    <span>NO</span>
                </label>
            </div>
        </div>
        <?= form_error('is_start'); ?>
    </div>
    <div class="form-group col-xs-1">
        <div class="checkbox-inline text-right" style="margin-left: 0px">
            <label>
                <input type="checkbox" name="force_is_start" value="Y" style="zoom:1.5;" <?= set_checkbox('force_is_start', 'Y', $form['force_is_start'] == 'Y' ? TRUE : FALSE); ?>>
                <span>強制</span>
            </label>
        </div>
    </div>

    <div class="form-group col-xs-5 <?= form_error('segmemo') ? 'has-error' : ''; ?>">
        <label class="control-label">辦班時段<font style="color: red">(最多200個中文字)</font></label>
        <textarea class="form-control" id="segmemo" name="segmemo" maxlength='200' cols='100' rows='2'><?= set_value('segmemo', $form['segmemo']); ?></textarea>
        <?= form_error('segmemo'); ?>
    </div>
    <div class="form-group col-xs-1">
        <div class="checkbox-inline text-right" style="margin-left: 0px">
            <label>
                <input type="checkbox" name="force_segmemo" value="Y" style="zoom:1.5;" <?= set_checkbox('force_segmemo', 'Y', $form['force_segmemo'] == 'Y' ? TRUE : FALSE); ?>>
                <span>強制</span>
            </label>
        </div>
    </div>

    <div class="form-group col-xs-6">
        <label class="control-label">課程內容(舊資料)(僅供參考)</label>
        <textarea class="form-control" id="content" name="content" maxlength='400' cols='100' rows='4'><?= set_value('content', $form['content']); ?></textarea>
        <?= form_error('content'); ?>
    </div>

    <div class="row col-xs-12">
        <div class="form-group col-xs-5">
            <label class="control-label">教學方式</label>
            <div>
                <div class="checkbox-inline" style="margin-left: 0px">
                    <label>
                        <input id="way1" type="checkbox" value="Y" name="way1" style="zoom:1.5;" <?= set_checkbox('way2', 'Y', $form['way1'] == 'Y' ? TRUE : FALSE); ?>>
                        <span>1.講授</span>
                    </label>
                </div>
                <div class="checkbox-inline" style="margin-left: 0px">
                    <label>
                        <input id="way2" type="checkbox" value="Y" name="way2" style="zoom:1.5;" <?= set_checkbox('way2', 'Y', $form['way2'] == 'Y' ? TRUE : FALSE); ?>>
                        <span>2.實習</span>
                    </label>
                </div>
                <div class="checkbox-inline" style="margin-left: 0px">
                    <label>
                        <input id="way3" type="checkbox" value="Y" name="way3" style="zoom:1.5;" <?= set_checkbox('way3', 'Y', $form['way3'] == 'Y' ? TRUE : FALSE); ?>>
                        <span>3.研討</span>
                    </label>
                </div>
                <div class="checkbox-inline" style="margin-left: 0px">
                    <label>
                        <input id="way4" type="checkbox" value="Y" name="way4" style="zoom:1.5;" <?= set_checkbox('way4', 'Y', $form['way4'] == 'Y' ? TRUE : FALSE); ?>>
                        <span>4.習作</span>
                    </label>
                </div>
                <div class="checkbox-inline" style="margin-left: 0px">
                    <label>
                        <input id="way5" type="checkbox" value="Y" name="way5" style="zoom:1.5;" <?= set_checkbox('way5', 'Y', $form['way5'] == 'Y' ? TRUE : FALSE); ?>>
                        <span>5.討論</span>
                    </label>
                </div>
                <div class="checkbox-inline" style="margin-left: 0px">
                    <label>
                        <input id="way6" type="checkbox" value="Y" name="way6" style="zoom:1.5;" <?= set_checkbox('way6', 'Y', $form['way6'] == 'Y' ? TRUE : FALSE); ?>>
                        <span>6.座談</span>
                    </label>
                </div>
                <div class="checkbox-inline" style="margin-left: 0px">
                    <label>
                        <input id="way7" type="checkbox" value="Y" name="way7" style="zoom:1.5;" <?= set_checkbox('way7', 'Y', $form['way7'] == 'Y' ? TRUE : FALSE); ?>>
                        <span>7.演練</span>
                    </label>
                </div>
                <div class="checkbox-inline" style="margin-left: 0px">
                    <label>
                        <input id="way8" type="checkbox" value="Y" name="way8" style="zoom:1.5;" <?= set_checkbox('way8', 'Y', $form['way8'] == 'Y' ? TRUE : FALSE); ?>>
                        <span>8.說唱</span>
                    </label>
                </div>
                <div class="checkbox-inline" style="margin-left: 0px">
                    <label>
                        <input id="way9" type="checkbox" value="Y" name="way9" style="zoom:1.5;" <?= set_checkbox('way9', 'Y', $form['way9'] == 'Y' ? TRUE : FALSE); ?>>
                        <span>9.表演</span>
                    </label>
                </div>
                <div class="checkbox-inline" style="margin-left: 0px">
                    <label>
                        <input id="way10" type="checkbox" value="Y" name="way10" style="zoom:1.5;" <?= set_checkbox('way10', 'Y', $form['way10'] == 'Y' ? TRUE : FALSE); ?>>
                        <span>10.參觀活動</span>
                    </label>
                </div>
                <div class="checkbox-inline" style="margin-left: 0px">
                    <label>
                        <input id="way11" type="checkbox" value="Y" name="way11" style="zoom:1.5;" <?= set_checkbox('way11', 'Y', $form['way11'] == 'Y' ? TRUE : FALSE); ?>>
                        <span>11.案例討論</span>
                    </label>
                </div>
                <div class="checkbox-inline" style="margin-left: 0px">
                    <label>
                        <input id="way12" type="checkbox" value="Y" name="way12" style="zoom:1.5;" <?= set_checkbox('way12', 'Y', $form['way12'] == 'Y' ? TRUE : FALSE); ?>>
                        <span>12.角色扮演</span>
                    </label>
                </div>
                <div class="checkbox-inline" style="margin-left: 0px">
                    <label>
                        <input id="way13" type="checkbox" value="Y" name="way13" style="zoom:1.5;" <?= set_checkbox('way13', 'Y', $form['way13'] == 'Y' ? TRUE : FALSE); ?>>
                        <span>13.實地參觀</span>
                    </label>
                </div>
                <div class="checkbox-inline" style="margin-left: 0px">
                    <label>
                        <input id="way14" type="checkbox" value="Y" name="way14" style="zoom:1.5;" <?= set_checkbox('way14', 'Y', $form['way14'] == 'Y' ? TRUE : FALSE); ?>>
                        <span>14.模擬演練</span>
                    </label>
                </div>
                <div class="checkbox-inline" style="margin-left: 0px">
                    <label>
                        <input id="way15" type="checkbox" value="Y" name="way15" style="zoom:1.5;" <?= set_checkbox('way15', 'Y', $form['way15'] == 'Y' ? TRUE : FALSE); ?>>
                        <span>15.電腦實機</span>
                    </label>
                </div>
                <div class="checkbox-inline" style="margin-left: 0px">
                    <label>
                        <input id="way16" type="checkbox" value="Y" name="way16" style="zoom:1.5;" <?= set_checkbox('way16', 'Y', $form['way16'] == 'Y' ? TRUE : FALSE); ?>>
                        <span>16.視聽教材</span>
                    </label>
                </div>
                <div>
                    <label class="control-label">17.其他</label>
                    <input class="form-control" id="way17" name="way17" placeholder="" value="<?= set_value('way17', $form['way17']); ?>">
                    <?= form_error('way17'); ?>
                </div>
            </div>
        </div>
        <div class="form-group col-xs-1">
            <div class="checkbox-inline text-right" style="margin-left: 0px">
                <label>
                    <input type="checkbox" name="force_way" value="Y" style="zoom:1.5;" <?= set_checkbox('force_way', 'Y', $form['force_way'] == 'Y' ? TRUE : FALSE); ?>>
                    <span>強制</span>
                </label>
            </div>
        </div>
        <div class="form-group col-xs-5">
            <label class="control-label">考核方式</label>
            <div>
                <div class="checkbox-inline" style="margin-left: 0px">
                    <label>
                        <input id="type1" type="checkbox" value="1" name="type1" style="zoom:1.5;" <?= set_checkbox('type2', '1', $form['type1'] == '1' ? TRUE : FALSE); ?>>
                        <span>測驗</span>
                    </label>
                </div>
                <div class="checkbox-inline" style="margin-left: 0px">
                    <label>
                        <input id="type2" type="checkbox" value="1" name="type2" style="zoom:1.5;" <?= set_checkbox('type2', '1', $form['type2'] == '1' ? TRUE : FALSE); ?>>
                        <span>書面報告</span>
                    </label>
                </div>
                <div class="checkbox-inline" style="margin-left: 0px">
                    <label>
                        <input id="type3" type="checkbox" value="1" name="type3" style="zoom:1.5;" <?= set_checkbox('type3', '1', $form['type3'] == '1' ? TRUE : FALSE); ?>>
                        <span>成果發表</span>
                    </label>
                </div>
                <div class="checkbox-inline" style="margin-left: 0px">
                    <label>
                        <input id="type4" type="checkbox" value="1" name="type4" style="zoom:1.5;" <?= set_checkbox('type4', '1', $form['type4'] == '1' ? TRUE : FALSE); ?>>
                        <span>實作演練</span>
                    </label>
                </div>
                <div class="checkbox-inline" style="margin-left: 0px">
                    <label>
                        <input id="type5" type="checkbox" value="1" name="type5" style="zoom:1.5;" <?= set_checkbox('type5', '1', $form['type5'] == '1' ? TRUE : FALSE); ?>>
                        <span>心得分享</span>
                    </label>
                </div>
                <div class="checkbox-inline" style="margin-left: 0px">
                    <label>
                        <input id="type6" type="checkbox" value="1" name="type6" style="zoom:1.5;" <?= set_checkbox('type6', '1', $form['type6'] == '1' ? TRUE : FALSE); ?>>
                        <span>案例研討</span>
                    </label>
                </div>
                <div class="checkbox-inline" style="margin-left: 0px">
                    <label>
                        <input id="type7" type="checkbox" value="1" name="type7" style="zoom:1.5;" <?= set_checkbox('type7', '1', $form['type7'] == '1' ? TRUE : FALSE); ?>>
                        <span>意見交流</span>
                    </label>
                </div>
                <div class="form-group">
                    <label class="control-label">其他<font style="color: red">(請輸入其他考核方式)</font></label>
                    <input class="form-control" id="type8" name="type8" placeholder="" value="<?= set_value('type8', $form['type8']); ?>">
                    <?= form_error('type8'); ?>
                </div>
            </div>
        </div>
        <div class="form-group col-xs-1">
            <div class="checkbox-inline text-right" style="margin-left: 0px">
                <label>
                    <input type="checkbox" name="force_type" value="Y" style="zoom:1.5;" <?= set_checkbox('force_type', 'Y', $form['force_type'] == 'Y' ? TRUE : FALSE); ?>>
                    <span>強制</span>
                </label>
            </div>
        </div>
    </div>

    <div class="form-group col-xs-5 required <?= form_error('is_assess') ? 'has-error' : ''; ?>">
        <label class="control-label">考核班期</label>
        <?php
        echo form_dropdown('is_assess', $choices['is_assess'], set_value('is_assess', $form['is_assess']), "class='form-control' id='is_assess' ");
        ?>
        <?= form_error('is_assess'); ?>
    </div>
    <div class="form-group col-xs-1">
        <div class="checkbox-inline text-right" style="margin-left: 0px">
            <label>
                <input type="checkbox" name="force_is_assess" value="Y" style="zoom:1.5;" <?= set_checkbox('force_is_assess', 'Y', $form['force_is_assess'] == 'Y' ? TRUE : FALSE); ?>>
                <span>強制</span>
            </label>
        </div>
    </div>
    <div class="form-group col-xs-5">
        <label class="control-label">重大政策</label>
        <div>
            <div class="radio-inline">
                <label>
                    <input type="radio" value="Y" name="fmap" style="zoom:1.5;" onclick="fmap_on();" <?= $fmap == 'Y' ? 'checked' : '' ?>>
                    <span>是</span>
                </label>
            </div>
            <div class="radio-inline">
                <label>
                    <input type="radio" value="N" name="fmap" style="zoom:1.5;" onclick="fmap_off();" <?= ($fmap == 'N') ? 'checked' : '' ?>>
                    <span>否</span>
                </label>
            </div>
        </div>
        <div>
            <div class="checkbox-inline" style="margin-left: 0px">
                <label>
                    <input id="map9" type="checkbox" value="1" name="map9" style="zoom:1.5;" onclick="chooseOne(this);" <?= $form['map9'] == '1' ? 'checked' : '' ?>>
                    <span>樂活宜居(45項)</span>
                </label>
            </div>
            <div class="checkbox-inline" style="margin-left: 0px">
                <label>
                    <input id="map10" type="checkbox" value="1" name="map10" style="zoom:1.5;" onclick="chooseOne(this);" <?= $form['map10'] == '1' ? 'checked' : '' ?>>
                    <span>友善共融(31項)</span>
                </label>
            </div>
            <div class="checkbox-inline" style="margin-left: 0px">
                <label>
                    <input id="map11" type="checkbox" value="1" name="map11" style="zoom:1.5;" onclick="chooseOne(this);" <?= $form['map11'] == '1' ? 'checked' : '' ?>>
                    <span>創新活力(37項)</span>
                </label>
            </div>
        </div>
        <?= form_error('fmap'); ?>
    </div>
    <div class="form-group col-xs-1">
        <div class="checkbox-inline text-right" style="margin-left: 0px">
            <label>
                <input type="checkbox" name="force_map" value="Y" style="zoom:1.5;" <?= set_checkbox('force_map', 'Y', $form['force_map'] == 'Y' ? TRUE : FALSE); ?>>
                <span>強制</span>
            </label>
        </div>
    </div>


        <div class="form-group col-xs-6 required <?= form_error('is_mixed') ? 'has-error' : ''; ?>">
            <label class="control-label">混成班期<font style="color: red">如選擇{是}，請新增線上課程</font></label>
            <?php
            echo form_dropdown('is_mixed', $choices['is_mixed'], set_value('is_mixed', $form['is_mixed']), 'class="form-control" id="is_mixed"');
            ?>
            <?= form_error('is_mixed'); ?>
        </div>

        <div class="form-group col-xs-5 required">
            <label class="control-label">政策行銷班期</label>
            <div>
                <div class="radio-inline">
                    <label>
                        <input id="policy_class" type="radio" value="Y" name="policy_class" style="zoom:1.5;" <?= set_radio('policy_class', 'Y', $form['policy_class'] == 'Y' ? TRUE : FALSE); ?>>
                        <span>是</span>
                    </label>
                </div>
                <div class="radio-inline">
                    <label>
                        <input id="policy_class" type="radio" value="N" name="policy_class" style="zoom:1.5;" <?= set_radio('policy_class', 'N', $form['policy_class'] == 'N' ? TRUE : FALSE); ?>>
                        <span>否</span>
                    </label>
                </div>
            </div>
            <?= form_error('policy_class'); ?>
        </div>
        <div class="form-group col-xs-1">
            <div class="checkbox-inline text-right" style="margin-left: 0px">
                <label>
                    <input type="checkbox" name="force_policy_class" value="Y" style="zoom:1.5;" <?= set_checkbox('force_policy_class', 'Y', $form['force_policy_class'] == 'Y' ? TRUE : FALSE); ?>>
                    <span>強制</span>
                </label>
            </div>
        </div>


    <div class="form-group col-xs-5 required <?= form_error('env_class') ? 'has-error' : ''; ?>">
        <label class="control-label">環境教育班期</label>
        <div>
            <div class="radio-inline">
                <label>
                    <input id="env_class" type="radio" value="Y" name="env_class" style="zoom:1.5;" <?= set_radio('env_class', 'Y', $form['env_class'] == 'Y' ? TRUE : FALSE); ?>>
                    <span>是(結訓學員可取得環境教育研習時數)</span>
                </label>
            </div>
            <div class="radio-inline">
                <label>
                    <input id="env_class" type="radio" value="N" name="env_class" style="zoom:1.5;" <?= set_radio('env_class', 'N', $form['env_class'] == 'N' ? TRUE : FALSE); ?>>
                    <span>否</span>
                </label>
            </div>
        </div>
        <?= form_error('env_class'); ?>
    </div>
    <div class="form-group col-xs-1">
        <div class="checkbox-inline text-right" style="margin-left: 0px">
            <label>
                <input type="checkbox" name="force_env_class" value="Y" style="zoom:1.5;" <?= set_checkbox('force_env_class', 'Y', $form['force_env_class'] == 'Y' ? TRUE : FALSE); ?>>
                <span>強制</span>
            </label>
        </div>
    </div>


    <div class="form-group col-xs-5 required">
        <label class="control-label">開放退休人員選課</label>
        <div>
            <div class="radio-inline">
                <label>
                    <input id="open_retirement" type="radio" value="Y" name="open_retirement" style="zoom:1.5;" <?= set_radio('open_retirement', 'Y', $form['open_retirement'] == 'Y' ? TRUE : FALSE); ?>>
                    <span>YES</span>
                </label>
            </div>
            <div class="radio-inline">
                <label>
                    <input id="open_retirement" type="radio" value="N" name="open_retirement" style="zoom:1.5;" <?= set_radio('open_retirement', 'N', $form['open_retirement'] == 'N' ? TRUE : FALSE); ?>>
                    <span>NO</span>
                </label>
            </div>
        </div>
        <?= form_error('open_retirement'); ?>
    </div>
    <div class="form-group col-xs-1">
        <div class="checkbox-inline text-right" style="margin-left: 0px">
            <label>
                <input type="checkbox" name="force_open_retirement" value="Y" style="zoom:1.5;" <?= set_checkbox('force_open_retirement', 'Y', $form['force_open_retirement'] == 'Y' ? TRUE : FALSE); ?>>
                <span>強制</span>
            </label>
        </div>
    </div>

    <div class="form-group col-xs-5">
        <label class="control-label">特殊情況</label>
        <div>
            <div class="checkbox-inline">
                <label>
                    <input type="checkbox" value="Y" name="not_hourfee" style="zoom:1.5;" <?= $form['not_hourfee'] == 'Y' ? 'checked' : '' ?>>
                    <span>無須支應講座鐘點費</span>
                </label>
            </div>
            <div class="checkbox-inline">
                <label>
                    <input type="checkbox" value="Y" id="not_location" name="not_location" style="zoom:1.5;" <?= ($form['not_location'] == 'Y') ? 'checked' : '' ?>>
                    <span>上課地點非公訓處</span>
                </label>
            </div>
            <div class="form-group">
                <input id="special_status" type="checkbox" value="9" name="special_status" style="zoom:1.5;" <?= $form['special_status'] == '9' ? 'checked' : '' ?>>
                <label class="control-label">其他(請敘明)</label>
                <input class="form-control" id="special_status_other" name="special_status_other" placeholder="" value="<?= set_value('special_status_other', $form['special_status_other']); ?>">
            </div>
        </div>
        <?= form_error('special_status'); ?>
    </div>
    <div class="form-group col-xs-1">
        <div class="checkbox-inline text-right" style="margin-left: 0px">
            <label>
                <input type="checkbox" name="force_special_status_other" value="Y" style="zoom:1.5;" <?= set_checkbox('force_special_status_other', 'Y', $form['force_special_status_other'] == 'Y' ? TRUE : FALSE); ?>>
                <span>強制</span>
            </label>
        </div>
    </div>
</form>
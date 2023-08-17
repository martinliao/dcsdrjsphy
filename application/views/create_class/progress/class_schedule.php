<style type="text/css">
    .progress {
      list-style-type: none;
      margin: 0;
      padding: 0;
      overflow: hidden;
      height: 30px;
      background-color: white;
    }
    .progress li {
      float: left;
      margin: 0;
      overflow: hidden;
    }
    .progress span {
      display: block;
      float: left;
    }
    .progress .step {
      height: 3.2rem;
      width: 10rem;
      font: bold 1.4rem/2.2rem simsun, arial;
      color: #999;
      text-align: center;
      background: #e3e3e3;
    }
    /* 箭头区域-非箭头部分 背景色 默认灰色 */
    .progress .triangle {
      position: relative;
      height: 3.2rem;
      background: #e3e3e3;
      width: 1.3rem;
      overflow: hidden;
    }
    .progress em,
    .progress i {
      display: block;
      position: absolute;
      top: -2px;
      overflow: hidden;
      width: 0;
      height: 0;
      border-width: 17px;
      border-style: dashed solid dashed dashed;
    }
    /* em 箭头区域-箭头部分 头部边框 白色 */
    .progress em {
      left: 0px;
      border-color: transparent transparent transparent #FFF;
    }
    /* i 箭头区域-箭头部分 背景颜色 默认灰色*/
    .progress i {
      left: -2px;
      border-color: transparent transparent transparent #e3e3e3;
    }
    .progress .first {
      width: 11rem;
      border-radius: 5px 0 0 5px;
    }
    /* 最后一个的背景色透明 */
    .progress .last {
      background: transparent;
    }
    /* 选中状态下 当前节点 左边箭头区域-非箭头部分 颜色蓝色（形成箭尾） */
    .progress .on .triangle-left {
      background: #5fb3dc;
    }
    /* 选中状态下 当前节点 右边箭头区域-箭头部分 颜色蓝色 */
    .progress .on .triangle-right i {
      border-color: transparent transparent transparent #5fb3dc;
    }
    .progress .on .step {
      background: #5fb3dc;
      color: #FFF;
    }
    /* 选中状态下 下一个兄弟节点 左边箭头区域-箭头部分 颜色蓝色 */
    .progress .on + li .triangle-left i {
      border-color: transparent transparent transparent #5fb3dc;
    }
</style>


<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="fa fa-list fa-lg"></i> 進度查詢
            </div>
            <!-- /.panel-heading -->
            
            <div class="panel-body">
            <center>
                <ul class="progress" style="width: 50%">
                <?php if($form['schedule'] == '規劃階段'){?>
                    <li class="on">
                        <span class="step first">規劃階段</span>
                    </li>
                    <li>
                        <span class="triangle triangle-left"><em></em><i></i></span>
                        <span class="step">建班階段</span>
                    </li>
                    <li>
                        <span class="triangle triangle-left"><em></em><i></i></span>
                        <span class="step">帶班階段</span>
                    </li>
                    <li>
                        <span class="triangle triangle-left"><em></em><i></i></span>
                        <span class="step">評估階段</span>
                    </li>
                    <li>
                        <span class="triangle triangle-left"><em></em><i></i></span>
                        <span class="step">請款階段</span>
                        <span class="triangle triangle-right last"><em></em><i></i></span>
                    </li>
                <?php } else if($form['schedule'] == '建班階段'){?>
                    <li class="on">
                        <span class="step first">規劃階段</span>
                    </li>
                    <li class="on">
                        <span class="triangle triangle-left"><em></em><i></i></span>
                        <span class="step">建班階段</span>
                    </li>
                    <li>
                        <span class="triangle triangle-left"><em></em><i></i></span>
                        <span class="step">帶班階段</span>
                    </li>
                    <li>
                        <span class="triangle triangle-left"><em></em><i></i></span>
                        <span class="step">評估階段</span>
                    </li>
                    <li>
                        <span class="triangle triangle-left"><em></em><i></i></span>
                        <span class="step">請款階段</span>
                        <span class="triangle triangle-right last"><em></em><i></i></span>
                    </li>
                <?php } else if($form['schedule'] == '帶班階段'){?>
                    <li class="on">
                        <span class="step first">規劃階段</span>
                    </li>
                    <li class="on">
                        <span class="triangle triangle-left"><em></em><i></i></span>
                        <span class="step">建班階段</span>
                    </li>
                    <li class="on">
                        <span class="triangle triangle-left"><em></em><i></i></span>
                        <span class="step">帶班階段</span>
                    </li>
                    <li>
                        <span class="triangle triangle-left"><em></em><i></i></span>
                        <span class="step">評估階段</span>
                    </li>
                    <li>
                        <span class="triangle triangle-left"><em></em><i></i></span>
                        <span class="step">請款階段</span>
                        <span class="triangle triangle-right last"><em></em><i></i></span>
                    </li>
                <?php } else if($form['schedule'] == '評估階段'){?>
                    <li class="on">
                        <span class="step first">規劃階段</span>
                    </li>
                    <li class="on">
                        <span class="triangle triangle-left"><em></em><i></i></span>
                        <span class="step">建班階段</span>
                    </li>
                    <li class="on">
                        <span class="triangle triangle-left"><em></em><i></i></span>
                        <span class="step">帶班階段</span>
                    </li>
                    <li class="on">
                        <span class="triangle triangle-left"><em></em><i></i></span>
                        <span class="step">評估階段</span>
                    </li>
                    <li>
                        <span class="triangle triangle-left"><em></em><i></i></span>
                        <span class="step">請款階段</span>
                        <span class="triangle triangle-right last"><em></em><i></i></span>
                    </li>
                <?php } else if($form['schedule'] == '請款階段'){?>
                    <li class="on">
                        <span class="step first">規劃階段</span>
                    </li>
                    <li class="on">
                        <span class="triangle triangle-left"><em></em><i></i></span>
                        <span class="step">建班階段</span>
                    </li>
                    <li class="on">
                        <span class="triangle triangle-left"><em></em><i></i></span>
                        <span class="step">帶班階段</span>
                    </li>
                    <li class="on">
                        <span class="triangle triangle-left"><em></em><i></i></span>
                        <span class="step">評估階段</span>
                    </li>
                    <li class="on">
                        <span class="triangle triangle-left"><em></em><i></i></span>
                        <span class="step">請款階段</span>
                        <span class="triangle triangle-right last"><em></em><i></i></span>
                    </li>
                <?php } else {?>
                    <li>
                        <span class="step first">規劃階段</span>
                    </li>
                    <li>
                        <span class="triangle triangle-left"><em></em><i></i></span>
                        <span class="step">建班階段</span>
                    </li>
                    <li>
                        <span class="triangle triangle-left"><em></em><i></i></span>
                        <span class="step">帶班階段</span>
                    </li>
                    <li>
                        <span class="triangle triangle-left"><em></em><i></i></span>
                        <span class="step">評估階段</span>
                    </li>
                    <li>
                        <span class="triangle triangle-left"><em></em><i></i></span>
                        <span class="step">請款階段</span>
                        <span class="triangle triangle-right last"><em></em><i></i></span>
                    </li>
                <?php } ?>
                </ul>
                <br>
                <table class="table table-bordered table-condensed table-hover" style="width:80%;">
                    <tr>
                        <td style="width: 10%">年度</td>
                        <td><?=$form['year']?></td>
                    </tr>
                    <tr>
                        <td>班期代碼</td>
                        <td><?=$form['class_no']?></td>
                    </tr>
                    <tr>
                        <td>期別</td>
                        <td><?=$form['term']?></td>
                    </tr>
                    <tr>
                        <td>班期名稱</td>
                        <td><?=$form['class_name']?></td>
                    </tr>
                    <tr>
                        <td>進度</td>
                        <td><?=$form['schedule']?></td>
                    </tr>
                </table>
            </center>
            </div>
            <!-- /.panel-body -->
        </div>
        <!-- /.panel -->
    </div>
    <!-- /.col-lg-12 -->
</div>
<!-- /.row -->
<script src="<?=HTTP_PLUGIN;?>jquery.highlight-3.js"></script>

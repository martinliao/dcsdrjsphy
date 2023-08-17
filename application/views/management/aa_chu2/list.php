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
                        <div class="col-xs-12" >
                            <div class="form-group">
                                <label class="control-label">年度</label>
                                <?php
                                    echo form_dropdown('year', $choices['year'], $filter['year'], 'class="form-control" id="penYear"');
                                ?>
                            </div>
                            <div class="form-group">
                                <label class="control-label">季別</label>
                                <?php
                                    echo form_dropdown('season', $choices['season_List'], $filter['season'], 'class="form-control" id="argSeason"');
                                ?>
                            </div>
                            <div class="form-group">
                                <label class="control-label">班期類別</label>
                                <?php
                                    echo form_dropdown('category', $choices['category'], $filter['category'], 'class="form-control" id="argSchedule"');
                                ?>
                            </div>
                        </div>
                        <div class="col-xs-12">
                            <div class="form-group">
                                <label class="control-label">顯示方式:</label>
                                    <div class="radio-inline">
                                        <label>
                                            <input id="show_1" type="radio" value="1" name="show" <?=set_radio('show', '1', $filter['show']==1);?>>
                                            全部
                                        </label>
                                    </div>
                                    <div class="radio-inline">
                                        <label>
                                            <input id="show_1" type="radio" value="2" name="show" <?=set_radio('show', '2', $filter['show']==2);?>>
                                            已選員
                                        </label>
                                    </div>
                            </div>
                            <a class="btn btn-info btn-sm" onclick='return Check_input_validate()' >查詢</a>
                        </div>
                    </div>
                </form>
            </div>
            <!-- /.panel -->
        </div>
        <!-- /.col-lg-12 -->
    </div>
</div>

<script>

    function Check_input_validate()
    {
        if( !document.getElementById("penYear").value )
        {
            alert('請選擇年度');
            return false;
        }
        if( !document.getElementById("argSeason").value )
        {
            alert('請選擇季別');
            return false;
        }
        var show_type=document.getElementsByName('show');
        if(show_type[0].checked==false && show_type[1].checked==false){
            alert('請選擇顯示方式');
            return false;
        }

        var strYEAR=document.getElementById("penYear").value;
        var strargSeason=document.getElementById("argSeason").value;
        var strargSchedule=document.getElementById("argSchedule").value;
        var searshow=document.getElementsByName("show");
        //var strshow=document.getElementsByName("show").checked.value;
        if(searshow[0].checked==true){
            strshow="";
        }else{
            strshow='2';
        }
        //var strshow="";

        window.open('<?=$link_pdf;?>?year='+strYEAR+'&season='+strargSeason+'&category='+strargSchedule+'&show='+strshow);
        //return true;
    }


</script>
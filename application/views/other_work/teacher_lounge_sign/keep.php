<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel&#45;heading">
                <i class="fa fa-list fa-lg"></i> 保留休息室
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <form id="form-list" method="POST">
                <input type="hidden" name="mode" id="mode" value=""></input>
                <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
                <!-- /.table head -->
                <table  border="1" class="table table-bordered table-condensed table-hover">
                    <thead>
                        <tr>
                            <td style="background-color: #cfc8c8"><font style="color:red">*</font>保留日期</td>
                            <td class="text-center" style="background-color: #f5f5f5">
                                <div class="input-group" id="start_date">
                                    <input type="text" class="form-control datepicker" value="<?=$start_date?>" id="datepicker1" name="start_date">
                                    <span class="input-group-addon" style="cursor: pointer;" id="datepicker2"><i
                                            class="fa fa-calendar"></i></span>
                                    <input type="text" class="form-control datepicker" value="<?=$end_date?>" id="test1" name="end_date">
                                    <span class="input-group-addon" style="cursor: pointer;" id="test2"><i
                                        class="fa fa-calendar"></i>
                                    </span>
                                </div>
                            </td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="background-color: #cfc8c8">
                                <font style="color:red">*</font>休息室
                            </td>
                            <td style="background: #dfdfdf">
                                <select name="lounge">
                                    <option value="C301">C301</option>
                                    <option value="C302">C302</option>
                                    <option value="C303">C303</option>
                                    <option value="C304">C304</option>
                                    <option value="C305">C305</option>
                                </select>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div id="filter-form" role="form" class="form-inline">
                    <div class="row">
                        <div class="col-xs-12">
                           <button id="save" class="btn btn-info btn-sm" style="background-color: #03a9f4">儲存</button>
                        </div>
                    </div>
                </div>

                <?php
                    for($i=0;$i<count($data_list);$i++){
                        echo '<table  border="1" class="table table-bordered table-condensed table-hover">';
                        echo '<thead>';
                        echo '<tr>';
                        echo '<td style="background-color: #cfc8c8">保留日期</td>';
                        echo '<td style="background-color: #f5f5f5">';
                        echo $data_list[$i]['start_date'].'~'.$data_list[$i]['end_date'];
                        echo '</td>';      
                        echo '</tr>';
                        echo '</thead>';
                        echo '<tbody>';
                        echo '<tr>';
                        echo '<td style="background-color: #cfc8c8">';
                        echo '休息室';
                        echo '</td>';
                        echo '<td style="background: #dfdfdf">';
                        echo $data_list[$i]['lounge'];                
                        echo '</td>';
                        echo '</tr>';
                        echo '</tbody>';
                        echo '</table>';
                        echo '<div id="filter-form" role="form" class="form-inline">';
                        echo '<div class="row">';
                        echo '<div class="col-xs-12">';
                        echo '<button type="submit" name="cancel" value="'.$data_list[$i]['id'].'" class="btn btn-info btn-sm" style="background-color: #ffc107;color:black;font-weight:bolder">取消保留</button>';
                        echo '</div>';
                        echo '</div>';
                        echo '</div>';
                    }
                ?>
                </form>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
function dateCompare(sDate1, sDate2){
   var aDate, oDate1, oDate2, iDays;
   
   oDate1 = new Date(sDate1);    
   oDate2 = new Date(sDate2); 
   if(oDate1 > oDate2){
        return true;
   }

   return  false; 
}

$(document).ready(function() {
    <?php
        if($reload){
            echo 'self.opener.location.reload();';
        }
    ?>

    $("#test1").datepicker();
    $('#test2').click(function(){
        $("#test1").focus();
    });
    $('#save').click(function(){
        if(dateCompare($('#datepicker1').val(),$('#test1').val())){
            alert('起日不可大於迄日');
            return false;
        }

        $('#mode').val('save');
        $( "#form-list" ).submit();
    });

    $("#datepicker1").datepicker();
        $('#datepicker2').click(function(){  
            $("#datepicker1").focus();   
        });
    });
</script>
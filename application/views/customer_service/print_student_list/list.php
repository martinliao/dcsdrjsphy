<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel&#45;heading">
                <i class="fa fa-list fa-lg"></i> <?=$_LOCATION['name'];?>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <form id="filter-form" role="form" class="form-inline">
                    <input type="hidden" name="sort" value="" />
                    <div class="col-xs-12">
                        <div class="form-group row">
                            <label class="control-label">年度</label>
                            <?php
                                    echo form_dropdown('year', $choices['year'], $filter['year'], 'class="form-control"');
                            ?>
                        </div>
                        <div class="form-group">
                            <label class="control-label">班期代碼</label>
                                <input type="text" class="form-control" name="class_no" value="<?=$filter['class_no'];?>">
                        </div>
                        <div class="form-group">
                            <label class="control-label">班期名稱</label>
                                <input type="text" class="form-control" name="class_name" value="<?=$filter['class_name'];?>">
                        </div>
                        <label> 
                            <!--<input type="checkbox" class="form-control" id="allQuery" value="<?=$filter['allQueryChecked']?>">-->
                        <input type="checkbox" style="height: auto;" name="checkAllClass" value="1" <?= isset($filter['checkAllClass']) && $filter['checkAllClass']=='1'?'checked':'';?>>
                        查詢所有研習人員名冊</label>  
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="form-group row">
                                    <label class="control-label">顯示資料欄位</label>
                                    <input type="checkbox" id="ShowTel" class="form-control">電話
                                    <input type="checkbox" id="retirement" class="form-control">退休

                                <div class="form-group">
                                    <label class="control-label">資料顯示方式</label>
                                    <input type="checkbox" id="csv"  value="<?=$filter['csv'];?>" class="form-control">下載CSV
                                    <input type="checkbox" id="html" class="form-control">HTML
                                </div>
                                </div>
                                <input id="allQueryChecked" name="allQueryChecked" type="hidden" value="">
                            </div>
                            <div class="col-xs-12">  
                                <div class="form-group row">
                                <label class="control-label">顯示筆數</label>
                                <?php
                                    echo form_dropdown('rows', $choices['rows'], $filter['rows'], 'class="form-control" onchange="sendFun()"');
                                ?>
                                <button class="btn btn-info btn-sm" onclick="serch();">查詢</button>
                                </div>
                            </div>
                        </div>
                    </div>    
                </form>
                <!-- /.table head -->
                <table class="table table-bordered table-condensed table-hover">
                    <thead>
                        <tr bgcolor="#5D7B9D"  style="color:white";>
                            <th class="sorting<?=($filter['sort']=='class_no asc')?'_asc':'';?><?=($filter['sort']=='class_no desc')?'_desc':'';?>" data-field="class_no" >班期代碼</th>
                            <th class="sorting<?=($filter['sort']=='term asc')?'_asc':'';?><?=($filter['sort']=='term desc')?'_desc':'';?>" data-field="term" >期別</th>
                            <th class="sorting<?=($filter['sort']=='class_name asc')?'_asc':'';?><?=($filter['sort']=='class_name desc')?'_desc':'';?>" data-field="class_name" >班期名稱</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($list as $row) { ?>
                            <tr>
                                <td ><a href="javascript:void(0);" onclick="select(<?=$row['link_regist'];?>);"><?=$row['class_no'];?></a></td>
                                <td><?=$row['term'];?></td>
                                <td><?=$row['class_name'];?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <form>
                <div class="row ">
                    <div class="col-lg-4">
                        Showing <?=$filter['offset']+1;?> to <?=(ceil($filter['total']/$filter['rows']) == $filter['page'])?$filter['total']:$filter['offset']+$filter['rows'];?> of <?=$filter['total'];?> entries
                    </div>
                    <div class="col-lg-8  text-right">
                        <?=$this->pagination->create_links();?>
                    </div>
                </div>
                </form>
            </div>
            <!-- /.table end -->
        </div>
        <!-- /.panel -->
    </div>
</div>
<!-- /.col-lg-12 -->
<script src="<?=HTTP_PLUGIN;?>jquery.highlight-3.js"></script>
<script>
function sendFun(){
    var obj = document.getElementById('filter-form');
    obj.submit();
}

if(allQueryChecked.value =='1')
{
    $("#allQuery").prop("checked",true);
}else{
    $("#allQuery").prop("checked",false);
}

function serch(){
//  if($("#allQuery").attr("checked")){
  if(document.getElementById("allQuery").checked==true) {
     allQueryChecked.value ='1';
  }else{
     allQueryChecked.value ='0';
  }
}
function select(seq_no){
    if (seq_no == undefined){
        alert('找無此資料');
    }
    if(document.getElementById("ShowTel").checked==true) {
        var ShowTel ='1';
    }else{
        var ShowTel ='0';
    }
    if(document.getElementById("retirement").checked==true) {
        var retirement ='1';
    }else{
        var retirement ='0';
    }
    if(document.getElementById("csv").checked==true) {
        var csv ='1';
        window.location.href='<?=base_url('customer_service/print_student_list/csv?csv=1&ShowTel=');?>'+ShowTel+'&seq_no='+seq_no+'&retirement='+retirement;
        $("body").unblock();  //取消load畫面
        return false;
    }else if(document.getElementById("html").checked==true){
        var url ='<?=base_url('customer_service/print_student_list/show/');?>'+seq_no+'?csv=0&html=1&ShowTel='+ShowTel+'&retirement='+retirement;
        window.open(url);
        // $("body").unblock();  //取消load畫面
        
    }else{
      var csv ='0';
        var url ='<?=base_url('customer_service/print_student_list/show/');?>'+seq_no+'?csv=0&ShowTel='+ShowTel+'&retirement='+retirement;
        window.open(url);
        //window.location.href= url;
    }
}
function conditions(seq_no){//ajax暫流
  var url = '<?=base_url('customer_service/print_student_list/show');?>';
  console.log(seq_no);
  if (seq_no == undefined){
    alert('找無此資料');
  }
  if(document.getElementById("ShowTel").checked==true) {
    var ShowTel ='1';
  }else{
    var ShowTel ='0';
  } 
  if(document.getElementById("csv").checked==true) {
    var csv ='1';
    window.location.href='<?=base_url('customer_service/print_student_list/csv?csv=1&ShowTel=');?>'+ShowTel+'&seq_no='+seq_no;
    return false;
  }else{
    var csv ='0';
    $.ajax({
        url: url,
        data: {
            csv: csv,
            seq_no: seq_no
        },
        type: "GET",
        dataType: 'json',
        success: function(response){
          console.log(response);
          if(response.status == "0"){
            console.log(response.msg.memberData);
            var Str = '<table width="99%"><tr><td bgcolor="#eeeeee"><table class="table table-bordered table-condensed" width="100%">'
                      +'<tr><td width="120" align="center" bgcolor="#dcdcdc">年度</td>'
                      +'<td align="left" bgcolor="#ffffff">'+response.msg.class.year+'</td></tr>'
                      +'<tr><td width="120" align="center" bgcolor="#dcdcdc">班期代碼</td>'
                      +'<td align="left" bgcolor="#ffffff">'+response.msg.class.class_no+'</td></tr>'
                      +'<tr><td width="120" align="center" bgcolor="#dcdcdc">期別</td>'
                      +'<td align="left" bgcolor="#ffffff">'+response.msg.class.term+'</td></tr>'
                      +'<tr><td width="120" align="center" bgcolor="#dcdcdc">名稱</td>'
                      +'<td align="left" bgcolor="#ffffff">'+response.msg.class.class_name+'</td></tr></table>'
                      +'<form id="actQuery" method="POST" >'
                      +'<table width="100%" ><tr><td bgcolor="#eeeeee">'
                        +'<table width="100%" class="table table-bordered table-striped table-condensed" id="show_table">'
                        +'<tr>'
                      +'<td align="center" bgcolor="#5D7B9D" width="80"><font color="#ffffff">學號</font></td>'
                      +'<td align="center" bgcolor="#5D7B9D" width="80" style="display:none;"><font color="#ffffff">優先順序</font></td>'
                      +'<td align="center" bgcolor="#5D7B9D"><font color="#ffffff">服務單位</font></td>'
                      +'<td align="center" bgcolor="#5D7B9D"><font color="#ffffff">職稱</font></td>'
                      +'<td align="center" bgcolor="#5D7B9D"><font color="#ffffff">姓名</font></td>'
                      +'<td align="center" bgcolor="#5D7B9D"><font color="#ffffff">性別</font></td>';
            if(ShowTel == 1){
                Str += '<td align="center" bgcolor="#5D7B9D"><font color="#ffffff">電話</font></td>';
            }
            Str += '<td align="center" bgcolor="#5D7B9D"><font color="#ffffff">備註</font></td>'
                      +'</tr>';
            Str += '<tr></tr><tr></tr><tr></tr>';
            if (response.msg.memberData != undefined){
                $.each( response.msg.memberData, function( key, value ) {
                    Str += '<tr><td align="center" >'+key+'</td>'
                         +'<td align="center" style="display:none;" ></td>'
                         +'<td align="center" >'+value.bureau_name+'</td>'
                         +'<td align="center" >'+value.job_title+'</td>'
                         +'<td align="center" >'+value.name+'</td>'
                         +'<td align="center" >'+value.gender+'</td>';
                    if(ShowTel == 1){     
                        Str +='<td align="center" >'+value.phone+'</td>';
                    } 
                    Str +='<td align="center" >'+value.stop_reason+'</td></tr>';    
                });
            }
            Str += '</table><div><a class="btn btn-default" href="http://dcsdcourse.taipei.gov.tw/base/admin/customer_service/print_student_list/" title="返回">返回</a>'
                    +'</div></td></tr></table></form>';                  
            $(".panel-body").html(Str);
            return false;
          }else{
            alert(response.msg);
          }
        },
       error: function(data){
            console.log('error');
      }
    });  
  }
}
</script>
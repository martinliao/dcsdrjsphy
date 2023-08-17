<div class="row">
	<div class="col-lg-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<?=$_LOCATION['function']['name'] ;?>
			</div>
			<center>
			<div>
				<?= $class['year'];?>年度  <?= $class['class_name'];?>  第<?= $class['term'];?>期 可報名人數：<?= $class['limil_max'];?>
				<BR>開班起迄日:<?= substr($class['start_date1'], 0, 10);?>至<?= substr($class['end_date1'], 0, 10);?>  承辦人：<?= $class['worker'];?>
			</div></center>
			<?php if(empty($regist_list)) { ?>
				<div class='page_info' style='color:red;font-size:100%;'>查無資料</div>
				<div><a class="btn btn-default" href="<?=$link_cancel;?>" title="返回">返回</a></div>
			<?php }else{ ?>
			<div class="panel-body">
				<form id="actQuery" method="POST" >
					<input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
					<a name='reset_st_no' class="btn btn-default" title='依已選員自動重排學號' onclick="do_reset_st_no()" <?=($class['disableCount']>0)?'disabled':'';?> > 依已選員自動重排學號 </a>
					<a name='reset_st_no' class="btn btn-default" title='依已選員亂數重排學號' onclick="reset_rand_st_no()" <?=($class['disableCount']>0)?'disabled':'';?> > 依已選員亂數重排學號 </a>
					<a name='reset_chk' class="btn btn-default" title='設定全部不選' onclick="do_reset_chk()" <?=($class['disableCount']>0)?'disabled':'';?> > 設定全部不選 </a>
					<a name='change_chk' class="btn btn-default" title='設定全部選' onclick="all_change_chk('true')" <?=($class['disableCount']>0)?'disabled':'';?> > 設定全部選 </a>
					<br>
					組別設定 : <input type='text' name='group_set' id='group_set' value="<?=$class['max_group'];?>" maxlength="2" size="3" />
					<a name='btn_group_same' class="btn btn-default" title='依已選員設定同局同組別' onclick=getCheck_reset_same_group_no() <?=($class['disableCount']>0)?'disabled':'';?> > 依已選員設定同局同組別 </a>
					<a name='btn_group_diff' class="btn btn-default" title='依已選員設定同局不同組別' onclick=do_reset_diff_group_no() <?=($class['disableCount']>0)?'disabled':'';?> > 依已選員設定同局不同組別 </a>
					<a name='btn_group_del' class="btn btn-default" title='組別全刪' onclick=do_reset_group() <?=($class['disableCount']>0)?'disabled':'';?> > 組別全刪 </a>

					<table width="100%" class="table table-bordered table-striped table-condensed" name="tbdata" id="tbdata">
						<thead>
						  <tr>
						   	<th bgcolor="#5D7B9D" style="width:40px;"><font color="#ffffff">列序</font></th>

						    <th bgcolor="#5D7B9D" style="width:60px;"><font color="#ffffff">選員否</font></th>
						    <th bgcolor="#5D7B9D" style="width:80px;" id="a_header4"><font color="#ffffff"><u>學號</u></font></th>
						    <th bgcolor="#5D7B9D" style="width:80px;" id="a_header5"><font color="#ffffff"><u>組別</u></font></th>
						    <th bgcolor="#5D7B9D" style="width:80px;" id="a_header3"><font color="#ffffff"><u>優先順序</u></font></th>

							<th bgcolor="#5D7B9D" style="width:200px;" id="a_header"><font color="#ffffff"><u>局處名稱</u></font></th>
							<th bgcolor="#5D7B9D" style="width:100px;" id="a_header1"><font color="#ffffff"><u>身分證ID</u></font></th>
							<th bgcolor="#5D7B9D" style="width:100px;"><font color="#ffffff">姓名</font></th>
							<th bgcolor="#5D7B9D" style="width:140px;"><font color="#ffffff">職稱</font></th>
							<th bgcolor="#5D7B9D" style="width:140px;"><font color="#ffffff">退休</font></th>
							<th bgcolor="#5D7B9D" style="width:60px;"><font color="#ffffff">身障需求註記</font></th>
							<th bgcolor="#5D7B9D" style="width:100px;" id="a_header2"><font color="#ffffff"><u>報名日期</u></font></th>
							<th bgcolor="#5D7B9D" style="width:100px;"><font color="#ffffff">第幾次報名</font></th>
							<th bgcolor="#5D7B9D" style="width:100px;"><font color="#ffffff">原始期數</font></th>
						  </tr>
						</thead>

						<tbody>
							<?php $count_row = '1';?>
							<?php foreach($regist_list as $row){ ?>
									<?php $isEnabled = "";?>
									<?php $isChecked = "";?>
									<?php
									if($row['yn_sel'] == 3 || $row['yn_sel'] == 8){
										//如果狀態為選員或調訓
										$isEnabled = "";
										$isChecked = "checked";
									}
									if($row['yn_sel'] == 1 || $row['yn_sel'] == 4 || $row['yn_sel'] == 5 || $row['yn_sel'] == 8){
										//如果狀態為結訓
										$isChecked = "checked";
										$isEnabled = "disabled";
									}
									if($row['yn_sel'] == 4){
										//如果狀態為退訓
										$isEnabled = "disabled";
										$isChecked = "";
									}
									if($row['yn_sel'] == 5){
										//如果狀態為未報到
										$isEnabled = "disabled";
										$isChecked = "";
									}

									?>

								<tr>
									<td ><?=$count_row;?></td>
									<td >
										<?php if($row['yn_sel'] == '1' || $row['yn_sel'] == '3' || $row['yn_sel'] == '8'){ ?>
											<input type='checkbox' name='chkDateEdit' onClick='chekcBoxClick(this,"<?=$row['persons'];?>","<?=$row['bureau_id'];?>")' id='chkYN<?=$count_row;?>' value='<?=$row['id'];?>' checked>
										<?php }else{ ?>
											<input type='checkbox' name='chkDateEdit' onClick='chekcBoxClick(this,"<?=$row['persons'];?>","<?=$row['bureau_id'];?>")' id='chkYN<?=$count_row;?>' value='<?=$row['id'];?>' <?=$isChecked;?> <?=$isEnabled;?>>
										<?php } ?>

									</td>
									<td >
										<input type='text' id='txtSTNO'  name='txtSTNO' value='<?=$row['st_no'];?>'  <?=$isEnabled;?>  size='3' onblur="sort_val(this)">

									</td>
									<td >
										<input type='text' id='txtGROUP_NO'  name='txtGROUP_NO' value='<?=$row['group_no'];?>'  <?=$isEnabled;?>  size='3' >
										<input type='hidden' id='txtGROUP_NO_1'  name='txtGROUP_NO_1' value='<?=$row['group_no'];?>' >
										<input type='hidden' id='hidINSERT_ORDER' value='<?=$row['insert_order'];?>'></input>
										<input type='hidden' name='txtSTNO_1' id='txtSTNO_1' value='<?=$row['st_no'];?>' >
										<input type='hidden' id='hidBEAURAU_ID' value='<?=$row['bureau_id'];?>'>
										<input type='hidden' id='hidINSERT_ORDER' value='<?=$row['insert_order'];?>'>

									</td>
									<td ><?=$row['insert_order'];?> <input type='hidden' name='hidInsertOrder' value='<?=$row['id'];?>,<?=$row['insert_order'];?>'></td>
									<td >
										<?=$row['bea_name'];?>
									</td>
									<td ><?=$row['id'];?></td>
									<td ><?=$row['name'];?></td>
									<td ><?=$row['pos_name'];?></td>
									<td ><?=($row['retirement']=='0')?'<font color="#FF0000">V</font>':'';?></td>
									<td ><?=$row['phydis'];?></td>
									<td ><?=$row['insert_date'];?></td>
									<td ><?=$row['priority'];?></td>
									<td ><?=$row['ori_term'];?></td>
									<td style="display:none">
										<input type='hidden' name='hidORI_YN_SEL' id='hidORI_YN_SEL' value='<?=$row['yn_sel'];?>'></input>
									</td>
								</tr>

							<?php $count_row++; } ?>
						</tbody>
					</table>
					<div>
						<input type='hidden' id='pmode' name='pmode' value='' >
						<input type='hidden' name='year' value='<?=$class['year'];?>' >
						<input type='hidden' name='class_no' value='<?=$class['class_no'];?>' >
						<input type='hidden' name='term' value='<?=$class['term'];?>' >
						<input type='hidden' id='max' name='max' value='<?=$class['counter'];?>'>
						<input type='hidden' name='query_year'        value='<?=$class['year'];?>' >
						<input type='hidden' name='query_class_no'    value='<?=$class['class_no'];?>' >
						<input type='hidden' name='query_class_name'  value='<?=$class['class_name'];?>' >
						<input type='hidden' name='query_term'        value='<?=$class['term'];?>' >
						<input type='hidden' name='pageType' id='pageType' value='<?=$class['pageType'];?>' />
						<input type='hidden' id='id' name='id' value='' >
						<input type='hidden' id='st_no' name='st_no' value='' >
						<input type='hidden' id='yn_sel' name='yn_sel' value='' >
						<input type='hidden' id='old_yn_sel' name='old_yn_sel' value='' >
						<input type='hidden' id='group_no' name='group_no' value='' >
						<input type='hidden' name='insert_order' value='' >
						<a id="btn_save" class="btn btn-default" onclick="do_update()" title="選員完成" <?=($class['disableCount']>0)?'disabled':'';?> >選員完成</a>
						<a class="btn btn-default" href="<?=$link_cancel;?>" title="返回">返回</a>
					</div>

				</form>
				<?php } ?>
			</div>
		</div>
	</div>
	<!-- /.col-lg-12 -->
</div>
<!-- /.row -->
<script src="<?=HTTP_JS;?>jquery.sortElements.js"></script>
<script>
$(window).load(function(){
 	var table = $('#tbdata');
    $('#a_header')
        .wrapInner('<span title="sort this column"/>')
        .each(function(){
            var th = $(this);
            thIndex = th.index(),
        	inverse = false;
        	th.click(function(){
                table.find('td').filter(function(){
                    return $(this).index() === thIndex;
                }).sortElements(function(a, b){
    				if($(a).find('#hidBEAURAU_ID').val()=='') {
    					var compA ='999999999999';
    				} else {
    					var compA = $(a).find('#hidBEAURAU_ID').val();
    				}

    				if($(b).find('#hidBEAURAU_ID').val()=='') {
    					var compB = '99999999999';
    				} else {
    					var compB = $(b).find('#hidBEAURAU_ID').val();
    				}

    				//局處相同時 則用優先順序排序
    				if ( compA == compB ) {
    				    if ($(a).find('#hidINSERT_ORDER').val()=='') {
    						compA ='999999999999';
    					} else {
    						compA = $(a).find('#hidINSERT_ORDER').val();
    					}

    				    if ($(b).find('#hidINSERT_ORDER').val()=='') {
    						compB = '99999999999';
    					} else {
    						compB = $(b).find('#hidINSERT_ORDER').val();
    					}

    				    return (parseInt(compA) < parseInt(compB)) ? -1 : 1;
    				} else {
    				    return inverse ?((compA < compB)? 1 : -1):((compA < compB)? -1 : 1);
    				}
    				}, function(){
                    // parentNode is the element we want to move
                    return this.parentNode;
    			});

    			inverse = !inverse;


            });

    	});

 });

 $(window).load(function(){
 var table = $('#tbdata');
    $('#a_header1')
        .wrapInner('<span title="sort this personal id"/>')
        .each(function(){
            var th = $(this),
                thIndex = th.index(),
                inverse = false;
            th.click(function(){
                table.find('td').filter(function(){
                    return $(this).index() === thIndex;
                }).sortElements(function(a, b){
                    return $.text([a]) > $.text([b]) ?
                        inverse ? -1 : 1
                        : inverse ? 1 : -1;
                }, function(){
                    // parentNode is the element we want to move
                    return this.parentNode;
                });
                inverse = !inverse;

            });
        });

     });
 $(window).load(function(){
 var table = $('#tbdata');
    $('#a_header2')
        .wrapInner('<span title="sort this insert date"/>')
        .each(function(){
            var th = $(this),
                thIndex = th.index(),
                inverse = false;
            th.click(function(){
                table.find('td').filter(function(){
                    return $(this).index() === thIndex;
                }).sortElements(function(a, b){
                    return $.text([a]) > $.text([b]) ?
                        inverse ? -1 : 1
                        : inverse ? 1 : -1;
                }, function(){
                    // parentNode is the element we want to move
                    return this.parentNode;
                });
                inverse = !inverse;

            });
        });

     });

$(window).load(function(){
 var table = $('#tbdata');
    $('#a_header3')
        .wrapInner('<span title="sort this order"/>')
        .each(function(){
            var th = $(this),
                thIndex = th.index(),
                inverse = false;
            th.click(function(){
                table.find('td').filter(function(){
                    return $(this).index() === thIndex;
                }).sortElements(function(a, b){
                    if($.text([a])=='')
                    compA='99';
                    else
                    compA=$.text([a]);
                    if($.text([b])=='')
                    compB='99';
                    else
                    compB=$.text([b]);
                    return parseInt(compA) > parseInt(compB) ?
                        inverse ? -1 : 1
                        : inverse ? 1 : -1;
                }, function(){
                    // parentNode is the element we want to move
                    return this.parentNode;
                });
                inverse = !inverse;

            });
        });

     });




$(window).load(function(){
 	var table = $('#tbdata');
	$('#a_header4')
    .wrapInner('<span title="sort student number"/>')
    .each(function(){
     	var th = $(this);
    	 thIndex = th.index(),
    	inverse = false;
    	th.click(function(){
            table.find('td').filter(function(){
                    return $(this).index() === thIndex;
                }).sortElements(function(a, b){
    			if ($(a).find('#txtSTNO_1').val()=='') {
    				var compA = inverse ? -1:999;

    			} else {
    				var compA = parseInt($(a).find('#txtSTNO_1').val());
    			}

    			if ($(b).find('#txtSTNO_1').val()=='') {
    				var compB = inverse ? -1:999;
    			} else {
    				var compB = parseInt($(b).find('#txtSTNO_1').val());
    			}



				    return inverse ?((compA < compB)? 1 : -1):((compA < compB)? -1 : 1);

					}, function(){
                    // parentNode is the element we want to move
                    return this.parentNode;
    		});

			inverse = !inverse;



        });

    });

 });


$(window).load(function(){
 	var table = $('#tbdata');
	$('#a_header5')
    .wrapInner('<span title="sort this group"/>')
    .each(function(){
     	var th = $(this);
    	 thIndex = th.index(),
    	inverse = false;
    	th.click(function(){
            table.find('td').filter(function(){
                    return $(this).index() === thIndex;
                }).sortElements(function(a, b){
    			if ($(a).find('#txtGROUP_NO').val()=='') {
    				var compA = inverse ? -1:999;

    			} else {
    				var compA = parseInt($(a).find('#txtGROUP_NO').val());
    			}

    			if ($(b).find('#txtGROUP_NO').val()=='') {
    				var compB = inverse ? -1:999;
    			} else {
    				var compB = parseInt($(b).find('#txtGROUP_NO').val());
    			}


    			//組別相同時 則用學號排序
				if ( compA == compB ) {
				    if ($(a).find("[name='txtSTNO_1']").val()=='') {
						compA = 999;
					} else {
						compA = $(a).find("[name='txtSTNO_1']").val();
					}

				    if ($(b).find("[name='txtSTNO_1']").val()=='') {
						compB = 999;
					} else {
						compB = $(b).find("[name='txtSTNO_1']").val();
					}

				    return (parseInt(compA) < parseInt(compB)) ? -1 : 1;
				} else {
				    return inverse ?((compA < compB)? 1 : -1):((compA < compB)? -1 : 1);
				}
					}, function(){
                    // parentNode is the element we want to move
                    return this.parentNode;


    		});

			inverse = !inverse;



        });

    });

});


var max = '<?=$class['counter'];?>';
var flag = false;
function chekcBoxClick(obj,strPERSONS,strBEAURAU_ID)
{
	var i_Count=0;
	var row =0;
	$("#tbdata tr").each(function() {
    if (row>0 )
    {
        var pr= $(this).find("td").last().html();
        var dochk =$(this).find("td").eq(1).find("input").first().attr("checked");
		var hidd =$(this).find("td").eq(1).find("input").last().attr("value");
        if(dochk && hidd==strBEAURAU_ID)
		{
          i_Count++;
		}
    }
    row++;
	});
	// if(i_Count>parseInt(strPERSONS))
	// {
		// obj.checked=false;
		// alert("選人人數已超過局處配當人數");
	// }

	//alert(max);
	if (max=="")
		max=0;
	var tdItm=obj.parentElement;
	var trItm=tdItm.parentNode;
	var row1 = trItm.rowIndex-1;
	var obj_1=document.getElementsByName("txtSTNO");
	var obj_2=document.getElementsByName("txtSTNO_1");
    var len = obj_1.length;
	if(obj.checked==true){
		max_num=0;
		for (i = 0; i < len; i++)
	  	{
	      if(max_num<parseInt(obj_1[i].value))
	      {
	      	max_num = parseInt(obj_1[i].value);
	      	//alert(max_num);
	      }
	 	}
	    //obj_1[row1].value=max_num+1;
		//obj_2[row1].value=max_num+1;
		if (!flag){
			flag=true;
				obj_1[row1].value=parseInt(max)+1;
				obj_2[row1].value=parseInt(max)+1;
				max++;
		}
		else{
			obj_1[row1].value=max_num+1;
			obj_2[row1].value=max_num+1;
		}

	}
	else
	{
		obj_1[row1].value='';
		obj_2[row1].value='';


		max--;
	}

}


function do_reset_chk(){
  $("input[name='chkDateEdit']").each(function(){
      if($(this).prop("disabled")==false)
      $(this).prop("checked", false);
  });

  $("input[name='txtSTNO']").each(function(){
      if($(this).prop("disabled")==false)
      this.value='';
  });

  $("input[name='txtGROUP_NO']").each(function(){
      if($(this).prop("disabled")==false)
      this.value='';
  });
}
function all_change_chk(argtype){
	chkEDIT=$('[name=chkDateEdit]').length;
	var obj_1=document.getElementsByName("txtSTNO_1");
	var obj_2=document.getElementsByName("txtSTNO");
	if (chkEDIT==undefined) {
	    $('[name=chkDateEdit]').checked=argtype;
	} else {
		for(i=0;i<chkEDIT;i++) {
			obj_1[i].value=i+1;
			obj_2[i].value=i+1;
			$('[name=chkDateEdit]')[i].checked='checked';
		}
	}
}
function do_reset_st_no(){
    var st_no=0;
    var chk ;

    for (i=0;i<$('[name=chkDateEdit]').length;i++) {
		if ($('[name=chkDateEdit]')[i].checked ==true) {
			st_no++;
			$('[name=txtSTNO]')[i].value=st_no;
			$('[name=txtSTNO_1]')[i].value=st_no;
		} else {
			$('[name=txtSTNO]')[i].value='';
			$('[name=txtSTNO_1]')[i].val='';
		}
	}
}

function do_update() {
	var id_arr='';
	var st_no_arr='';
 	var group_no_arr='';
 	var yn_sel_arr='';
 	var insert_order_arr='';
 	var flag='';
 	//check limit;
 	var yn_sel_3=0;
 	var yn_sel_limit=0;
 	var yn_sel_limit_SEVER='<?= $class['limil_max'];?>';
 	if (yn_sel_limit_SEVER !='')
 	{
   		yn_sel_limit=parseInt(yn_sel_limit_SEVER);
 	}

	var row =0;
	var needConfrim = false;

	$("#tbdata tr").each(function()
	{
    	if (row>0 )
    	{
        	var pr= $(this).find("td").last().html();
        	var dochk =$(this).find("td").eq(1).find("input").first().prop("checked");
        	if ( (pr =="1") && (!dochk) ) {
          		needConfrim=true;
        	}
    	}
    	row++;
	});

	if (needConfrim==true)
	{
    	if(!confirm("尚有第一次報名人員未選員，確定存檔嗎？"))
    	{
       		return ;
    	}
	}

	var ori_yn_sel = new Array();
 	$("#tbdata :checkbox").each(function()
 	{
    	var tdItm=this.parentElement;
		var trItm=tdItm.parentNode;
		var row1 = trItm.rowIndex;

        //打勾的狀態
    	if (this.checked)
    	{
        	if ($('[name=txtSTNO]')[row1-1].value === '')
        	{
    			alert("請輸入學號 "+row1);
    			flag='out';
    			return;
    		}

	    	yn_sel_arr+=",3";
	    	yn_sel_3++;
    	}
    	else
    	{
        	yn_sel_arr+=",2";
    	}
    	id_arr+=","+this.value;

    	ori_yn_sel.push($('[name=hidORI_YN_SEL]')[row1-1].value);
 	});

 	if (flag=='out') {
 		return;
 	}

 	//原來的狀態
	var ori_yn_sel_str = ori_yn_sel.join();



  	//txtSTNO=form1.txtSTNO;
  	//txtGROUP_NO=form1.txtGROUP_NO;
  	for (i=0;i<$('[name=chkDateEdit]').length;i++)
  	{
		st_no_arr+=","+$('[name=txtSTNO]')[i].value;
		group_no_arr+=","+$('[name=txtGROUP_NO]')[i].value;
  	}

 	if (id_arr.substring(0,1)==",")
 	{
    	id_arr=id_arr.substring(1,id_arr.length);
 	}
 	if (st_no_arr.substring(0,1)==",")
 	{
    	st_no_arr=st_no_arr.substring(1,st_no_arr.length);
 	}
 	if (group_no_arr.substring(0,1)==",")
 	{
    	group_no_arr=group_no_arr.substring(1,group_no_arr.length);
 	}
 	if (yn_sel_arr.substring(0,1)==",")
 	{
    	yn_sel_arr=yn_sel_arr.substring(1,yn_sel_arr.length);
 	}

 	$('#pmode').val('upd');
 	$('#id').val(id_arr);
 	$('#st_no').val(st_no_arr);
 	$('#group_no').val(group_no_arr);
 	$('#yn_sel').val(yn_sel_arr);
 	$('#old_yn_sel').val(ori_yn_sel_str);
 	//alert($('#yn_sel').val()+' _ '+$('#st_no').val()+ ' _ '+$('#group_no').val()+' _ '+$('#old_yn_sel').val());
 	var $form = $('#actQuery');
	var url = '<?=base_url('management/election/ajax/do_election');?>';

    $.ajax({
        url: url,
        data: $form.serialize(),
        type: "POST",
        dataType: 'json',
        success: function(response){
                    if (response.status) {
                    	console.log(response);
                    	alert('選員作業更新成功')
                    	location.reload();
                    } else {
                    	console.log(response);
                    }
                }

    });

}

function go_page(pagenum){
  document.location = ('ii1.php?pagenum=' + pagenum);
}
function ii1_query_form(value,query){
  document.getElementById("right_deck").style.display = value;
  document.getElementById("left_deck").style.display = query;
}

//$temp = 最多的那個單位的array , $unit = 全部的array 但最前面要跟$temp 一樣
function random_sort(unit,temp) {
    temp = $.shuffle(temp);

    //產生插入縫隙位置的array
    insert_position = new Array(temp.length);

    //先將最多人數單位中間插人
    for (var i=0;i<temp.length-1;i++) {
        //先隨便找一個位置
        var x = $.randomBetween(0,temp.length-2);

        //如果這位置已經有人的話
        while (typeof(insert_position[x]) != 'undefined') {
            x = $.randomBetween(0,temp.length-2);
        }

        //如果這位置沒有人的話 就坐進去
        insert_position[x]=unit[temp.length+i];

        //當超出總數
        if (temp.length+i==unit.length-1) {
            break;
        }
    }

    //將排入的位置排入
    for (var i=insert_position.length;i>0;i--) {
        if(typeof(insert_position[i-1])!='undefined') {
            temp = sorting_A(temp,i,insert_position[i-1]);
        }
    }

    //注意$temp size為變動值
    tnum=temp.length;
    for(var i=0;i<(unit.length-tnum);i++){

        x = $.randomBetween(0,tnum+i);	//目前有x個位置可以插入取代 所有>max x>temp

        if(typeof(temp[x])!='undefined' && temp[x] != unit[tnum+i]){	//$temp[x] != 被插入的
            if((x>0 && temp[x-1] != unit[tnum+i]) || x==0){	//x>0 且$temp[x-1] != 被插入值 OR X==0
                temp = sorting_A(temp,x,unit[tnum+i]);
            }else{	//插入x>0 但$temp[x-1] == 被插入值
                i--;
            }
        }else if(typeof(temp[x])=='undefined' && temp[x-1] != unit[tnum+i]){
            temp = sorting_A(temp,x,unit[tnum+i]);
        }else{
            i--;
        }
    }

	return temp;

}
//random=插入位置,value=插入值
//將在array中插入位置之後的值往後一位
function sorting_A(array,random,value) {
    count=array.length;
    array.push(1);
    while(count != random){
        array[count]=array[count-1];
        count--;
    }
    array[random]=value;

    return array;
}

/*
 * jQuery shuffle
 *
 * Copyright (c) 2008 Ca-Phun Ung <caphun at yelotofu dot com>
 * Dual licensed under the MIT (MIT-LICENSE.txt)
 * and GPL (GPL-LICENSE.txt) licenses.
 *
 * http://yelotofu.com/labs/jquery/snippets/shuffle/
 *
 * Shuffles an array or the children of a element container.
 * This uses the Fisher-Yates shuffle algorithm <http://jsfromhell.com/array/shuffle [v1.0]>
 */

(function($){

	$.fn.shuffle = function() {
		return this.each(function(){
			var items = $(this).children().clone(true);
			return (items.length) ? $(this).html($.shuffle(items)) : this;
		});
	}

	$.shuffle = function(arr) {
		for(var j, x, i = arr.length; i; j = parseInt(Math.random() * i), x = arr[--i], arr[i] = arr[j], arr[j] = x);
		return arr;
	}

})(jQuery);


/*
 * JQuery Random Plugin
 *
 * Adds two random number functions to jQuery -
 * one to find a random number and one to find a random number between a max and min limit.
 *
 * Version 1.0
 *
 * by Christian Bruun - 23. jan 2009
 *
 * Like it/use it? Send me an e-mail: rockechris@rockechris.com
 *
 * License: None. Use and abuse. Comes with no warranty, of course!
 *
 *
 * Usage:
 * $.random(int);
 * $.randomBetween(min, max);
 *
 * Code found at:
 * http://www.merlyn.demon.co.uk/js-randm.htm
 *
 */
jQuery.extend({
	random: function(X) {
	    return Math.floor(X * (Math.random() % 1));
	},
	randomBetween: function(MinV, MaxV) {
	  return MinV + jQuery.random(MaxV - MinV + 1);
	}
});

var time_limit=10;
var time=0;
function check_rand(studentList) {
    var flag=true;
    for (var i=1;i<studentList.length;i++) {
        if ($(studentList[i]).find("td input#hidBEAURAU_ID")[0].value == $(studentList[i-1]).find("td input#hidBEAURAU_ID")[0].value ) {
            flag=false;
            break;
        }
	}

	return flag;
}

function do_reset_rand_st_no(unit1, bearuauList) {

    var newlist = new Array();
    var newcount = new Array();
    var index;
    for (var i=0;i<bearuauList.length;i++) {
        index = $.inArray(bearuauList[i], newlist);
        if (index == -1) {
            newlist.push(bearuauList[i]);
            newcount.push(1);
        } else {
            newcount[index] ++;
        }
    }

    Array.max = function( array ){
        return Math.max.apply( Math, array );
    };

    var buread_id = newlist[jQuery.inArray(Array.max(newcount), newcount)];

    var newlist = new Array();
    var mostlist = new Array();
    var otherlist = new Array();
    for (var i=0;i<unit1.length;i++) {
        if (bearuauList[i] == buread_id) {
            mostlist.push(unit1[i]);
        } else {
            otherlist.push(unit1[i]);
        }
    }
    var mostNumber = mostlist.length;
    var otherNumber = otherlist.length;

    newlist = mostlist.concat(otherlist);
    result = random_sort(newlist,mostlist);

    if (mostNumber-1 <= otherNumber && check_rand(result)===false && time<=time_limit) {
        time = time+1;
        return do_reset_rand_st_no(unit1, bearuauList);
    } else {
        return result;
    }
}

function getCheck_rand_st_no() {

	//init
	var unit0 = $("#tbdata").find("tbody tr");
	var bearuauList = $("#tbdata").find("tbody tr td input#hidBEAURAU_ID");
	var checkList = new Array();
	var uncheckList = new Array();
	var checkBearuauList = new Array();
	var uncheckBearuauList = new Array();
	for (var i=0;i<unit0.length;i++) {
		if ($(unit0[i]).find("td :checkbox")[0].checked == true) {
		    checkList.push($(unit0[i]).clone());
		    checkBearuauList.push($(unit0[i]).find("td input#hidBEAURAU_ID")[0].value);
		} else {
		    uncheckList.push($(unit0[i]).clone());
		    uncheckBearuauList.push($(unit0[i]).find("td input#hidBEAURAU_ID")[0].value);
		}
	}

	//do random
	var result = do_reset_rand_st_no(checkList, checkBearuauList);

	//append DOM
	$("#tbdata tbody tr").remove();
    for (var i=0;i<result.length;i++) {
        $("#tbdata tbody").append(result[i]);
    }
    for (var i=0;i<uncheckList.length;i++) {
        $("#tbdata tbody").append(uncheckList[i]);
    }
    do_reset_st_no();

}

function reset_rand_st_no() {
    time=0;
    getCheck_rand_st_no();
}

categoryTable = document.getElementById("tbdata");

function Move_up(src) {
    //上移一行
    var rowIndex=src.parentElement.parentElement.rowIndex;
    if (rowIndex>=2)
    	change_row(rowIndex-1,rowIndex);
}

function Move_down(src) {
	//下移一行
    var rowIndex=src.parentElement.parentElement.rowIndex;
    var tl = document.getElementById("tbdata");
    if (rowIndex<tl.rows.length-1)
           change_row(rowIndex+1,rowIndex);
}

function change_row(line1,line2) {
	//執行交换
	data_count = 0;
	obj_1=categoryTable.rows[line1].cells[3];
	obj_2=categoryTable.rows[line2].cells[3];
	val_1 = $(obj_1).find("input").val();
	val_2 = $(obj_2).find("input").val();
	$(obj_1).find("input").val(val_2);
	$(obj_2).find("input").val(val_1);
 	if ((line2==1)) {
 		categoryTable.rows[line2].cells[1].innerHTML = '<a href="#" onclick="Move_up(this)"><img src="<?=HTTP_IMG;?>desc.gif" /></a>\n<a href="#" onclick="Move_down(this)"><img src="<?=HTTP_IMG;?>asc.gif" /></a>';
 		categoryTable.rows[line1].cells[1].innerHTML = '<a href="#" onclick="Move_down(this)"><img src="<?=HTTP_IMG;?>asc.gif" /></a>';
 	}

 	if ((line1==1)) {
 		categoryTable.rows[line1].cells[1].innerHTML = '<a href="#" onclick="Move_up(this)"><img src="<?=HTTP_IMG;?>desc.gif" /></a>\n<a href="#" onclick="Move_down(this)"><img src="<?=HTTP_IMG;?>asc.gif" /></a>';
 		categoryTable.rows[line2].cells[1].innerHTML = '<a href="#" onclick="Move_down(this)"><img src="<?=HTTP_IMG;?>asc.gif" /></a>';
 	}

 	if ((line1==data_count)) {
 		categoryTable.rows[line1].cells[1].innerHTML = '<a href="#" onclick="Move_up(this)"><img src="<?=HTTP_IMG;?>desc.gif" /></a>\n<a href="#" onclick="Move_down(this)"><img src="<?=HTTP_IMG;?>asc.gif" /></a>';
 		categoryTable.rows[line2].cells[1].innerHTML = '<a href="#" onclick="Move_up(this)"><img src="<?=HTTP_IMG;?>desc.gif" /></a>';
 	}

 	if ((line2==data_count)) {
 		categoryTable.rows[line2].cells[1].innerHTML = '<a href="#" onclick="Move_up(this)"><img src="<?=HTTP_IMG;?>desc.gif" /></a>\n<a href="#" onclick="Move_down(this)"><img src="<?=HTTP_IMG;?>asc.gif" /></a>';
 		categoryTable.rows[line1].cells[1].innerHTML = '<a href="#" onclick="Move_up(this)"><img src="<?=HTTP_IMG;?>desc.gif" /></a>';
 	}

 	categoryTable.rows[line1].swapNode(categoryTable.rows[line2]);
}

function keep_show() {
	var obj_1=document.getElementsByName("txtSTNO");
	var len = obj_1.length;

	for (i = 0; i < len; i++) {
	    if (i==0)
	    	categoryTable.rows[i+1].cells[1].innerHTML = '<a href="#" onclick="Move_down(this)"><img src="<?=HTTP_IMG;?>asc.gif" /></a>';
	    else if (i==(len-1))
	    	categoryTable.rows[i+1].cells[1].innerHTML = '<a href="#" onclick="Move_up(this)"><img src="<?=HTTP_IMG;?>desc.gif" /></a>';
	    else
	    	categoryTable.rows[i+1].cells[1].innerHTML = '<a href="#" onclick="Move_up(this)"><img src="<?=HTTP_IMG;?>desc.gif" /></a>\n<a href="#" onclick="Move_down(this)"><img src="<?=HTTP_IMG;?>asc.gif" /></a>';
	}
}


function sort_val(obj) {
	var tdItm=obj.parentElement;
	var trItm=tdItm.parentNode;
	var row1 = trItm.rowIndex-1;
	var obj_1=document.getElementsByName("txtSTNO_1");
	var obj_2=document.getElementsByName("txtSTNO");
    var len = obj_1.length;
    var checked = false;
  	for (i = 0; i < max; i++) {
    	if (i!=row1) {
      		if (obj[i].value==obj_1[i].value) {
      			obj_2[i].value = obj_1[row1].value;
      			obj_1[i].value = obj_1[row1].value;
      		}
		}
  	}
	obj_1[row1].value = obj.value;
}

//取得打勾狀態
function get_all_chkstate() {
    //var stateArray = new Array();
    var stateArray = {};

	var chkEDIT=$('[name=chkDateEdit]').length;
	for(i=0;i<chkEDIT;i++) {
	    stateArray[$($('[name=chkDateEdit]')[i]).attr('id')] = $('[name=chkDateEdit]')[i].checked;
	}
	return stateArray;
}

//算出適當的分組方式
function getGroupSetting(check_count, group_set) {
    var minRange = Math.floor(check_count/group_set);
    var maxRange = Math.ceil(check_count/group_set);
    var resultArray = new Array();

    for (var i=0;i<group_set;i++) {
        resultArray.push(minRange);
    }

    for (var i=0;i<(check_count-minRange*group_set);i++) {
        resultArray[i] = resultArray[i]+1;
    }

    return resultArray;
}

//依已選員設定同局同組別
function do_reset_same_group_no(unit1, bearuauList) {
    var bearuauGroupList = new Array();
    var newcount = new Array();

    //
    for (var i=0;i<bearuauList.length;i++) {
	    bearuauGroupList.push(new Array());
	}
    var index=-1;
    for (var i=0;i<unit1.length;i++) {
        index = $.inArray(unit1[i].find("td input#hidBEAURAU_ID")[0].value, bearuauList);
        if (index != -1) {
            bearuauGroupList[index].push(unit1[i]);
        }
    }

    //
    var stateList = get_all_chkstate();
    var group_set=parseInt(document.getElementById("group_set").value,10);
	var check_count = 0;
	for(var key in stateList) {
		if (stateList[key] == true) {
			check_count ++;
		}
	}
	var groupMember = getGroupSetting(check_count, group_set);

	//如果有剛好的
	var trueGroup = new Array();
	index = -1;
	var removeList = bearuauGroupList.length;
	for (var i=0;i<bearuauGroupList.length;i++) {
	    index = $.inArray(bearuauGroupList[i].length, groupMember);
		if (index!=-1) {
		    groupMember.splice(index, 1);
		    trueGroup.push(bearuauGroupList.splice(i, 1)[0]);
		    i = i-1;
		}
	}


	if (trueGroup.length==group_set) {
		return trueGroup;
	}

	//如果同一局處的人太多
	for (var i=0;i<groupMember.length;i++) {
	    for (var j=0;j<bearuauGroupList.length;j++) {
	        if (bearuauGroupList[j].length>groupMember[i]) {
	            groupMember.splice(i, 1);
			    tmp = bearuauGroupList[j].splice(0, groupMember[i]);
			    trueGroup.push(tmp);
			    i = i-1;
			}
	    }
	}

	//如果有剛好的
	index = -1;
	for (var i=0;i<bearuauGroupList.length;i++) {
	    index = $.inArray(bearuauGroupList[i].length, groupMember);
		if (index!=-1) {
		    groupMember.splice(index, 1);
		    trueGroup.push(bearuauGroupList.splice(i, 1)[0]);
		    i = i-1;
		}
	}

	if (trueGroup.length==group_set) {
		return trueGroup;
	}

	//如果同一局處的人太少
	var allList = new Array();
	for (var i=0;i<bearuauGroupList.length;i++) {
	    for (var j=0;j<bearuauGroupList[i].length;j++) {

	        allList.push(bearuauGroupList[i][j]);

	    }
	}


	var newGroup = new Array();
	for (var i=0;i<groupMember.length;i++) {

	    newGroup = allList.splice(0, groupMember[i]);

	    trueGroup.push(newGroup);
	}

	if (trueGroup.length==group_set) {
		return trueGroup;
	}

	return trueGroup;
}

function getCheck_reset_same_group_no() {

	if ((document.getElementById("group_set").value=='')||(document.getElementById("group_set").value=='0')) {
		alert("組別設定需填寫數字!");
		return;
	}

	//init
	var unit0 = $("#tbdata").find("tbody tr");
	var bearuauList = $("#tbdata").find("tbody tr td input#hidBEAURAU_ID");
	var checkList = new Array();
	var uncheckList = new Array();
	var checkBearuauList = new Array();
	var uncheckBearuauList = new Array();
	for (var i=0;i<unit0.length;i++) {
		if ($(unit0[i]).find("td :checkbox")[0].checked == true) {
		    checkList.push($(unit0[i]));
		    index = $.inArray($(unit0[i]).find("td input#hidBEAURAU_ID")[0].value, checkBearuauList);
		    if (index == -1) {
		        checkBearuauList.push($(unit0[i]).find("td input#hidBEAURAU_ID")[0].value);
		    }
		} else {
		    uncheckList.push($(unit0[i]).clone());
		}
	}

	if (parseInt(document.getElementById("group_set").value,10) > checkList.length) {
		alert("組別設定需小於選員數!");
		return;
	}

	var stateList = get_all_chkstate();

	//do random
	var tmpResult = do_reset_same_group_no(checkList, checkBearuauList);
	var result = new Array();

	for (var i=0;i<tmpResult.length;i++) {
	    for (var j=0;j<tmpResult[i].length;j++) {
	        tmpResult[i][j].find("#txtGROUP_NO")[0].value = (i+1);
	        result.push(tmpResult[i][j]);
	    }
	}

	//append DOM
	$("#tbdata tbody tr").remove();
    for (var i=0;i<result.length;i++) {
        $("#tbdata tbody").append(result[i]);
    }
    for (var i=0;i<uncheckList.length;i++) {
        $("#tbdata tbody").append(uncheckList[i]);
    }

	for (var key in stateList) {
        $('#'+key).attr('checked', stateList[key]);
    }

}

//依已選員設定同局不同組別
function do_reset_diff_group_no() {
	if((document.getElementById("group_set").value=='')||(document.getElementById("group_set").value=='0')) {
		alert("組別設定需填寫數字!")
		return;
	}
	var stateList = get_all_chkstate();
	var table = $('#tbdata');
    var th = $('#a_header'),
	thIndex = th.index(),
	inverse = false;
	table.find('td').filter(function(){
        return $(this).index() === thIndex;
    }).sortElements(function(a, b){
    	return $.text([a]) > $.text([b]) ?
          inverse ? -1 : 1
          : inverse ? 1 : -1;
  	}, function(){
    	// parentNode is the element we want to move
      	return this.parentNode;
  	});
  	inverse = !inverse;

  	group_no = 0;
  	group_set=parseInt(document.getElementById("group_set").value);
	chkEDIT=$('[name=chkDateEdit]').length;
	txtGROUP_NO=document.getElementsByName('txtGROUP_NO');
	for (i=0;i<chkEDIT;i++) {
		if (stateList[$('[name=chkDateEdit]')[i].id] ==true) {
			group_no++;

	     	txtGROUP_NO[i].value=group_no;
	     	if (group_no==group_set) {
	     			group_no = 0;
	     	}
		} else {
			txtGROUP_NO[i].value='';
		}
	}

	for (var key in stateList) {
        $('#'+key).attr('checked', stateList[key]);
    }



}

function do_reset_group(){
   document.getElementsByName('group_set').value='';
  var stateList = get_all_chkstate();
	chkEDIT=$('[name=chkDateEdit]').length;
	txtGROUP_NO=document.getElementsByName('txtGROUP_NO');
	for (i=0;i<chkEDIT;i++) {
		if (stateList[$('[name=chkDateEdit]')[i].id] ==true) {


	     	txtGROUP_NO[i].value='';


		}
	}
}
function showText() {

	var person=prompt("請輸入人數!","");

	if (person!=null){
	  //go_detail(person);
	  newSort(person);
	}

}
function go_detail(num){
  var arg='';
  var arg='ii1.php?pagenum=1&num='+num+'&pageType=1';
  document.location =arg;
}
function no_detail() {

	//alert($("input[name=chkDateEdit]:checked").length);

	var strArray = "";

	for(var i=0;i<$("input[name=chkDateEdit]:checked").length;i++) {

		strArray += $("input[name=chkDateEdit]:checked").eq(i).val() + ",";

	}
	//alert(strArray);
	var arg='<?=$link_ids;?>?pagenum=1&id='+strArray+'&pageType=1';
	document.location =arg;
}
checkboxId();
function checkboxId (){

	var id1 = '<?=$class['id1'];?>';
	//alert(id1);
	if(id1 != '') {
		var id1Array = id1.split(",");

		for(var i=0;i<id1Array.length;i++) {

			$("input[name=chkDateEdit]").each(function(){

				if($(this).val() == id1Array[i]) {
					$(this).attr("checked",true);

				}

			})

		}

	}

}

newSort('<?=$class['num'];?>');

function newSort(num){

	var obj=document.getElementsByName("chkDateEdit");

	if(num != '') {
		// alert(obj.length);
		for(var i=0;i<obj.length;i++) {
			$('[name=chkDateEdit]')[i].checked='';

		}

		if(obj.length<num){
			num = obj.length;
		}
		for(var i=0;i<num;i++) {
			$('[name=chkDateEdit]')[i].checked='checked';
		}
		resort(num);
	}


}
var max = '<?=$class['counter'];?>';


function resort(num){
	var obj_1=document.getElementsByName("txtSTNO");
	var obj_2=document.getElementsByName("txtSTNO_1");
    var len = obj_1.length;
	//if(obj.checked==true){
		max_num=0;
		var counter = 0;
		//alert("test");
		//alert(<?=$class['num'];?>);
		for (i = 0; i < num; i++)
	  	{
	  		if (i<len){
				if ((obj_1[i].value)==""){//如為空值
					counter++;
					obj_1[i].value = counter;
				}
				else{
					counter = parseInt(obj_1[i].value);
				}
			}
			else{
				break;
			}
	 	}
	 //}
}

</script>
<!DOCTYPE html>
<html>
<head>
	<title></title>
	<link href="<?=HTTP_PLUGIN;?>bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
	<link href="<?=HTTP_PLUGIN;?>font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
	<link href="<?=HTTP_PLUGIN;?>animate/animate.css" rel="stylesheet">
	<link href="<?=HTTP_PLUGIN;?>metisMenu/dist/metisMenu.min.css" rel="stylesheet">

</head>
<body>
   <div id="wrapper">
        <!-- Page Content -->
      <div id="page-wrapper">
          <div class="page-header">
              <div class="container-fluid">
                  <div class="row">                   	
                      <div class="col-xs-12" style="text-align: center;">                      	
                      	<form class="form-inline">
                        	<div class="form-group">
                        		<label class="control-label" for="keyword">請輸入關鍵字</label>
                        		<input type="text" id="keyword" name="keyword" class="form-control">
                        		<label class="control-label" for="agency">包含裁撤機關</label>
                        		<input type="checkbox" id="agency" name="agency" value="Y">
                        		<input type="submit" name="" class="btn btn-info" value="查詢">
                        	</div>
                      	</form>
                      </div>
                 
                  </div>
              </div>
          </div>
          
          <div class="container-fluid">
						<div class="container">
							<table class="table">
								<thead>
									<th>選取</th>
									<th>局處代碼</th>
									<th>局處名稱</th>
									<th>裁撤註記</th>
								</thead>
								<tbody>
									<?php foreach ($list['data'] as $row) : ?>
									<tr>
										<td><input type="radio" name="" onclick="choice('<?=$row->bureau_id; ?>', '<?=$row->name; ?>')"></td>
										<td><?=$row->bureau_id;?></td>
										<td><?=$row->name;?></td>
										<td style="text-align: center;"><?=$row->del_flag;?></td>
									</tr>
									<?php endforeach ?>
								</tbody>
							</table>
					</div>
      	</div>
          <div class="col-lg-8 text-center">
            <?=$this->pagination->create_links();?>
          </div>
      </div>
        <!-- /#page-wrapper -->
   </div>

<script type="text/javascript">
	var query_id = '<?=$query_id;?>';
	function choice(id, name){
		if (query_id == '1'){
		  window.opener.filltext('limit_beaurau_id', id, 'limit_beaurau_name', name);
		}else{
		  window.opener.filltext('req_beaurau_id', id, 'req_beaurau_name', name);
		}
		window.close();	
	}
</script>
</body>
</html>






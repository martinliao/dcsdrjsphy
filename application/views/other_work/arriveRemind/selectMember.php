<!DOCTYPE html>
<html>
<head>
	<title></title>
	<link href="<?=HTTP_PLUGIN;?>bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
	<link href="<?=HTTP_PLUGIN;?>font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
	<style type="text/css">
		table{
			border-collapse:collapse;
		}
		label{
			width: 150px;
		}
		table td{
			text-align: center;
		}
	</style>
</head>
<body>

	<div style="width:100%;padding:20px;">
		<form>
			<div>
				<label>身分證</label>
				<input type="text" name="idno" value="<?=$filter['idno']?>">
			</div>
			<div>
				<label>姓名</label>
				<input type="text" name="member_name" value="<?=$filter['member_name']?>">
			</div>
			<div>
				<label>身分</label>
				<select name="queryType">
					<option value='student' <?=($filter['queryType'] == 'student') ? 'selected' : '' ?>>學員</option>
					<option value="teacher" <?=($filter['queryType'] == 'teacher') ? 'selected' : '' ?>>講座</option>
				</select>
			</div>		
			<div>
				<button>搜尋</button>
			</div>
		</form>
		<div>
			<table border=1 style="width: 100%">
				<thead>
					<th width="100"></th>
					<th>身分證</th>
					<th>姓名</th>
				</thead>
				<tbody>
				<?php foreach($members as $member): ?>
					<tr>
						<td><button onclick="choose('<?=$member->idno?>', '<?=$member->name?>', '<?=$filter['queryType']?>')">選擇</button></td>
						<td><?=$member->idno?></td>
						<td><?=$member->name?></td>
					</tr>
				<?php endforeach ?>			
				</tbody>
			</table>

            <div class="col-lg-8 text-center">
                <?=$this->pagination->create_links();?>
            </div>
		
		</div>
	</div>
</body>
<script type="text/javascript">
	function choose(idno, name, queryType){
		window.opener.choose(idno, name, queryType);
		window.opener.focus();
		window.close();
	}
</script>
</html>
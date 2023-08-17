<?php if (validation_errors()) { ?>
<div class="alert alert-danger">
	<button class="close" data-dismiss="alert" type="button">×</button>
	<?=validation_errors();?>
</div>
<?php } ?>
<form id="data-form" role="form" method="post" action="<?=$link_save;?>">
	<input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
	<input type="hidden" name="action_id" value="<?=$form['action_id']; ?>" />
	<div class="form-group required <?=form_error('port')?'has-error':'';?>">
		<label class="control-label">Port</label>
		<?php
			echo form_dropdown('port', $choices['port'], set_value('port', $form['port']), 'class="form-control "');
		?>
		<?=form_error('port'); ?>
	</div>
	<div class="form-group required <?=form_error('port')?'has-error':'';?>">
		<label class="control-label">Parent</label>
		<?php
			$choices['parent'] = array(0=>'-- None --') + $choices['parent'];
			echo form_dropdown('parent_id', $choices['parent'], $form['parent_id'], 'class="form-control "');
		?>
		<?=form_error('parent_id'); ?>
	</div>
	<div class="form-group <?=form_error('icon')?'has-error':'';?>">
		<label class="control-label">ICON</label>
		<input class="form-control" name="icon" placeholder="fa-glass" value="<?=set_value('icon', $form['icon']); ?>">
		<?=form_error('icon'); ?>
	</div>
	<div class="form-group required <?=form_error('name')?'has-error':'';?>">
		<label class="control-label">Name</label>
		<input class="form-control" name="name" placeholder="" value="<?=set_value('name', $form['name']); ?>">
		<?=form_error('name'); ?>
	</div>
	<div class="form-group required <?=form_error('link')?'has-error':'';?>">
		<label class="control-label">Link</label>
		<input class="form-control" name="link" placeholder="" value="<?=set_value('link', $form['link']); ?>">
		<?=form_error('link'); ?>
	</div>
	<div class="form-group <?=form_error('actions')?'has-error':'';?>">
		<label class="control-label">Actions</label>
		<input class="form-control" name="actions" placeholder="add,view,edit" value="<?=set_value('actions', $form['actions_to_string']); ?>">
		<?=form_error('actions'); ?>
	</div>
	<div class="form-group required <?=form_error('sort_order')?'has-error':'';?>">
		<label class="control-label">Sort Order</label>
		<input class="form-control" name="sort_order" placeholder="" value="<?=set_value('sort_order', $form['sort_order']); ?>">
		<?=form_error('sort_order'); ?>
	</div>
	<div class="form-group required <?=form_error('auth')?'has-error':'';?>">
        <label class="control-label">檢查權限</label>
		<div>
			<div class="radio-inline">
				<label>
					<input type="radio" value="1" name="auth" <?=set_radio('auth', '1', $form['auth']==1);?>>
					<span style="color: green;">是　</span>
				</label>
			</div>
			<div class="radio-inline">
				<label>
					<input type="radio" value="0" name="auth" <?=set_radio('auth', '0', $form['auth']==0);?>>
					<span style="color: red;">否　</span>
				</label>
			</div>
			<?=form_error('auth'); ?>
		</div>
	</div>
	<div class="form-group required <?=form_error('enable')?'has-error':'';?>">
		<label class="control-label">是否啟用</label>
		<div>
			<div class="radio-inline">
				<label>
					<input type="radio" value="1" name="enable" <?=set_radio('enable', '1', $form['enable']==1);?>>
					<span style="color: green;">是　</span>
				</label>
			</div>
			<div class="radio-inline">
				<label>
					<input type="radio" value="0" name="enable" <?=set_radio('enable', '0', $form['enable']==0);?>>
					<span style="color: red;">否　</span>
				</label>
			</div>
			<?=form_error('enable'); ?>
		</div>
	</div>
</form>

<script>
var choices_parent = CI.choices_parent;
$(function() {
	$('select[name=port]').change(function(){
		var port = $(this).val();
		$('select[name=parent_id] option').remove();
		console.log(choices_parent)
		for (i in choices_parent) {
			if (port == i) {
				console.log(choices_parent[port])
				for (id in choices_parent[port]) {
					var name = choices_parent[port][id];
					$('select[name=parent_id]').append('<option value="'+ id +'">'+ name +'</option>');
				}
			}
		}
	});

	$('select[name=parent_id]').change(function(){
		toggleAction($(this).val());
	});
});

$().ready(function(){
	toggleAction($('select[name=parent_id]').val());
});

function toggleAction(val) {
	if (val == '0') {
		$('#checkboxAction').hide();
	} else {
		$('#checkboxAction').show();
	}
}
</script>

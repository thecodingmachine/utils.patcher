<?php use Mouf\Utils\Patcher\PatchInterface;
/* @var $this Mouf\Utils\Patcher\Controllers\PatchController */ ?>
<h1>Patches list</h1>

<?php if (empty($this->patchesArray)): ?>
<div class="alert alert-info">No patches have been registered yet.</div>
<?php 
else:

if ($this->nbAwaiting == 0 && $this->nbError == 0) {
?>
<div class="alert alert-success">There are no patches that need to be executed.</div>
<?php 
} else {
?>
<form action="runAllPatches" method="post">
	<input name="name" type="hidden" value="<?php echo plainstring_to_htmlprotected($this->instanceName); ?>"></input>
	<input name="selfedit" type="hidden" value="<?php echo plainstring_to_htmlprotected($this->selfedit); ?>"></input>
<?php 
	echo '<button class="btn btn-large btn-success patch-run-all"><i class="icon-arrow-right icon-white"></i> Apply ';
	if ($this->nbAwaiting != 0) {
		echo $this->nbAwaiting." awaiting patch".(($this->nbAwaiting != 1)?"es":"");
		if ($this->nbError != 0) {
			echo " and ";
		}
	}
	if ($this->nbError != 0) {
		echo $this->nbError." patch".(($this->nbError != 1)?"es":"")." in error";
	}
	echo '</button>';
?>
</form>
<?php
}
?>
<table class="table table-stripped">
	<tr>
		<th style="width:10%">Status</th>
		<th style="width:20%">Name</th>
		<th style="width:40%">Description</th>
		<th style="width:30%">Actions</th>
	</tr>
<?php 
foreach ($this->patchesArray as $patch): ?>
	<tr data-uniquename="<?php echo plainstring_to_htmlprotected($patch['uniqueName']) ?>">
		<td><?php 
		switch ($patch['status']) {
			case PatchInterface::STATUS_AWAITING:
				echo '<span class="label">Awaiting</span>';
				break;
			case PatchInterface::STATUS_APPLIED:
				echo '<span class="label label-success">Applied</span>';
				break;
			case PatchInterface::STATUS_ERROR:
				echo '<span class="label label-important">Error</span>';
				break;
			case PatchInterface::STATUS_SKIPPED:
				echo '<span class="label label-info">Skipped</span>';
				break;
		};
		
		?>
		</td>
		<td><?php echo plainstring_to_htmlprotected($patch['uniqueName']) ?></td>
		<td><?php echo plainstring_to_htmlprotected($patch['description']) ?></td>
		<td>
		<?php 
		
		echo '<button class="btn btn-mini btn-success patch-apply" '.(($patch['status'] == PatchInterface::STATUS_APPLIED)?'disabled="disabled"':'').'><i class="icon-arrow-right icon-white"></i> Apply</button>';
		echo ' <button class="btn btn-mini btn-info patch-skip" '.(($patch['status'] == PatchInterface::STATUS_APPLIED || $patch['status'] == PatchInterface::STATUS_SKIPPED)?'disabled="disabled"':'').'><i class="icon-share-alt icon-white"></i> Skip</button>';
		if ($patch['canRevert']) {
			echo ' <button class="btn btn-mini btn-inverse patch-revert" '.(($patch['status'] == PatchInterface::STATUS_AWAITING)?'disabled="disabled"':'').'><i class="icon-arrow-left icon-white"></i> Revert</button>';
		}
		if ($patch['edit_url']) {
			echo ' <a class="btn btn-mini btn-danger patch-edit" href="'.ROOT_URL.$patch['edit_url'].'"><i class="icon-edit icon-white"></i> Edit</a>';
		}
		
		?>
		</td>
	</tr>
	<?php if ($patch['status'] == PatchInterface::STATUS_ERROR): ?>
	<tr>
		<td colspan="4">
			<div class="alert alert-error">
			<strong>Last error message</strong>: <?php echo plainstring_to_htmlprotected($patch['error_message']);?>
			</div>
		</td>
	</tr>
	<?php endif; ?>
<?php 
endforeach;
?>
</table>
<?php
endif; 

// Empty form that gets submitted on button click.
?>
<form id="patchesForm" action="runPatch" method="post">
	<input name="name" type="hidden" value="<?php echo plainstring_to_htmlprotected($this->instanceName); ?>"></input>
	<input name="selfedit" type="hidden" value="<?php echo plainstring_to_htmlprotected($this->selfedit); ?>"></input>
	<input id="uniqueName" name="uniqueName" type="hidden"></input>
	<input id="action" name="action" type="hidden"></input>
</form>


<script type="text/javascript">
$(".patch-apply").click(function() {
	var uniqueName = $(this).parents("tr").first().data("uniquename");
	$('#uniqueName').val(uniqueName);
	$('#action').val('apply');
	$('#patchesForm').submit();
});

$(".patch-revert").click(function() {
	var uniqueName = $(this).parents("tr").first().data("uniquename");
	$('#uniqueName').val(uniqueName);
	$('#action').val('revert');
	$('#patchesForm').submit();
});

$(".patch-skip").click(function() {
	var uniqueName = $(this).parents("tr").first().data("uniquename");
	$('#uniqueName').val(uniqueName);
	$('#action').val('skip');
	$('#patchesForm').submit();
});
</script>
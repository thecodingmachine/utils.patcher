<?php use Mouf\Utils\Patcher\PatchInterface;
/* @var $this Mouf\Utils\Patcher\Controllers\PatchController */ ?>
<h1>Apply patches</h1>

Please select the patch types you want to apply:

<form action="applyAllPatches" method="post">
    <input name="name" type="hidden" value="<?php echo plainstring_to_htmlprotected($this->instanceName); ?>"></input>
    <input name="selfedit" type="hidden" value="<?php echo plainstring_to_htmlprotected($this->selfedit); ?>"></input>
<?php foreach ($this->nbPatchesByType as $name => $number): ?>
    <label class="checkbox"><input type="checkbox" name="types[]" value="<?= plainstring_to_htmlprotected($name) ?>" <?php if ($name == '') { echo "checked readonly"; } ?> /> <?= plainstring_to_htmlprotected($name?:'default') ?> (<?= $number ?> patch<?= $number > 1 ? 'es':'' ?>)</label>
<?php endforeach; ?>

    <button class="btn btn-large btn-success"><i class="icon-arrow-right icon-white"></i> Apply selected patches</button>
</form>
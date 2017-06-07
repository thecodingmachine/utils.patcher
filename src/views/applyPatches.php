<?php use Mouf\Utils\Patcher\PatchInterface;
/* @var $this Mouf\Utils\Patcher\Controllers\PatchController */ ?>
<?php if ($this->action === 'reset'): ?>
    <h1>Reset database</h1>
    <div class="alert alert-danger"><strong>Warning!</strong> You are about to reset your complete database!</div>
<?php else: ?>
    <h1>Apply patches</h1>
<?php endif; ?>

Please select the patch types you want to apply:

<form action="applyAllPatches" method="post">
    <input name="name" type="hidden" value="<?php echo plainstring_to_htmlprotected($this->instanceName); ?>"></input>
    <input name="selfedit" type="hidden" value="<?php echo plainstring_to_htmlprotected($this->selfedit); ?>"></input>
    <input name="action" type="hidden" value="<?php echo plainstring_to_htmlprotected($this->action); ?>"></input>
<?php foreach ($this->nbPatchesByType as $name => $number): ?>
    <label class="checkbox"><input type="checkbox" name="types[]" value="<?= plainstring_to_htmlprotected($name) ?>" <?php if ($name == '') { echo "checked readonly"; } ?> /> <?= plainstring_to_htmlprotected($name?:'default') ?> (<?= $number ?> patch<?= $number > 1 ? 'es':'' ?>)</label>
<?php endforeach; ?>

    <?php if ($this->action === 'reset'): ?>
        <button class="btn btn-large btn-danger"><i class="icon-remove icon-white"></i> Yes, I want to reset the database</button>
    <?php else: ?>
        <button class="btn btn-large btn-success"><i class="icon-arrow-right icon-white"></i> Apply selected patches</button>
    <?php endif; ?>
</form>
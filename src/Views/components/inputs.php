<?php
$type = $type ?? 'text';
$name = $name ?? '';
$label = $label ?? '';
$placeholder = $placeholder ?? '';
$value = $value ?? '';
$required = $required ?? false;
?>
<div class="space-y-1">
    <?php if (!empty($label)): ?>
        <label class="block text-xs font-medium text-slate-700"><?= $label ?></label>
    <?php endif; ?>
    <input type="<?= $type ?>" 
           name="<?= $name ?>" 
           placeholder="<?= $placeholder ?>" 
           value="<?= $value ?>" 
           <?= $required ? 'required' : '' ?>
           class="block w-full px-3.5 py-2 text-xs text-slate-800 bg-white border border-slate-200 rounded-lg placeholder-slate-400 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-all">
</div>

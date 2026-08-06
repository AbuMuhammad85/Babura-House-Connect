<?php
$type = $type ?? 'button';
$variant = $variant ?? 'primary';
$text = $text ?? 'Click Me';
$classes = 'px-4 py-2 rounded-lg text-xs font-semibold shadow-sm transition-all focus:outline-none ';

switch ($variant) {
    case 'primary':
        $classes .= 'bg-primary text-white hover:bg-primary/95 shadow-primary/20 hover:shadow-lg';
        break;
    case 'secondary':
        $classes .= 'bg-slate-800 text-white hover:bg-slate-700 shadow-slate-800/10 hover:shadow-lg';
        break;
    case 'outline':
        $classes .= 'border border-slate-200 bg-white text-slate-700 hover:bg-slate-50';
        break;
    case 'danger':
        $classes .= 'bg-red-600 text-white hover:bg-red-500 shadow-red-600/10 hover:shadow-lg';
        break;
}
?>
<button type="<?= $type ?>" class="<?= $classes ?>">
    <?= $text ?>
</button>

<?php
$type = $type ?? 'info';
$text = $text ?? 'Active';
$classes = 'bg-slate-100 text-slate-700 border-slate-200';

switch ($type) {
    case 'success':
        $classes = 'bg-green-50 text-green-700 border-green-200/50';
        break;
    case 'warning':
        $classes = 'bg-amber-50 text-amber-700 border-amber-200/50';
        break;
    case 'danger':
        $classes = 'bg-red-50 text-red-700 border-red-200/50';
        break;
    case 'info':
        $classes = 'bg-blue-50 text-blue-700 border-blue-200/50';
        break;
}
?>
<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold border <?= $classes ?>">
    <?= $text ?>
</span>

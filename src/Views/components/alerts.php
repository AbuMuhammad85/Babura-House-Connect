<?php
$type = $type ?? 'info';
$message = $message ?? '';
if (empty($message)) return;

$classes = 'bg-blue-50 border-blue-200 text-blue-800';
$icon = 'fa-circle-info';

switch ($type) {
    case 'success':
        $classes = 'bg-green-50 border-green-200 text-green-800';
        $icon = 'fa-circle-check';
        break;
    case 'error':
        $classes = 'bg-red-50 border-red-200 text-red-800';
        $icon = 'fa-circle-exclamation';
        break;
    case 'warning':
        $classes = 'bg-amber-50 border-amber-200 text-amber-800';
        $icon = 'fa-triangle-exclamation';
        break;
}
?>
<div class="p-4 rounded-xl border flex items-start space-x-3 text-xs leading-relaxed <?= $classes ?>" role="alert" data-aos="fade-in">
    <i class="fa-solid <?= $icon ?> text-sm mt-0.5 shrink-0"></i>
    <div class="flex-grow">
        <?= $message ?>
    </div>
</div>

<?php
$title = $title ?? 'No Listings Found';
$message = $message ?? "We couldn't find any properties matching your criteria.";
$icon = $icon ?? 'fa-house-circle-exclamation';
$actionUrl = $actionUrl ?? '';
$actionText = $actionText ?? '';
?>
<div class="flex flex-col items-center justify-center text-center p-8 bg-white border border-slate-100 rounded-2xl shadow-sm" data-aos="fade-up">
    <div class="w-16 h-16 rounded-2xl bg-slate-50 text-slate-400 flex items-center justify-center text-2xl mb-4 animate-pulse">
        <i class="fa-solid <?= $icon ?>"></i>
    </div>
    <h3 class="font-bold text-slate-800 text-base"><?= $title ?></h3>
    <p class="text-xs text-text-muted mt-1.5 max-w-sm leading-relaxed"><?= $message ?></p>
    <?php if (!empty($actionUrl) && !empty($actionText)): ?>
        <a href="<?= url($actionUrl) ?>" class="mt-5 px-4 py-2 bg-primary text-white rounded-lg text-xs font-semibold hover:bg-primary/95 shadow-sm hover:shadow-lg transition-all">
            <?= $actionText ?>
        </a>
    <?php endif; ?>
</div>

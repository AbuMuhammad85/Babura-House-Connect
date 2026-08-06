<?php
$title = $title ?? 'Notification Title';
$message = $message ?? 'Notification Message';
$date = $date ?? 'August 6, 2026';
$read = $read ?? false;
?>

<div class="p-4 rounded-xl border transition-all duration-300 <?= $read ? 'bg-white border-slate-100 opacity-75' : 'bg-green-50/30 border-green-100 shadow-sm' ?>">
    <div class="flex items-start justify-between">
        <div class="flex space-x-3">
            <span class="p-2 rounded-lg text-sm flex items-center justify-center relative shrink-0 mt-0.5 <?= $read ? 'bg-slate-100 text-slate-500' : 'bg-primary/10 text-primary' ?>">
                <i class="fa-solid fa-envelope"></i>
                <?php if (!$read): ?>
                    <span class="absolute top-0 right-0 w-2.5 h-2.5 bg-accent rounded-full border border-white"></span>
                <?php endif; ?>
            </span>
            <div>
                <h4 class="text-sm font-bold text-slate-800 <?= $read ? '' : 'text-primary' ?>"><?= $title ?></h4>
                <p class="text-xs text-text-muted mt-1 leading-relaxed"><?= $message ?></p>
                <span class="text-[10px] text-slate-400 block mt-2"><?= $date ?></span>
            </div>
        </div>
        
        <div class="flex items-center space-x-2 shrink-0">
            <?php if (!$read): ?>
                <button class="text-xs text-primary hover:underline font-semibold">Mark Read</button>
            <?php endif; ?>
            <button class="text-slate-400 hover:text-red-500 p-1 rounded-md transition-colors" title="Delete notification">
                <i class="fa-regular fa-trash-can text-xs"></i>
            </button>
        </div>
    </div>
</div>

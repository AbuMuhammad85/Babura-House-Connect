<?php
$name = $name ?? 'Musa Haruna';
$rating = $rating ?? 5;
$date = $date ?? '2 weeks ago';
$comment = $comment ?? 'The property is exactly as shown in the images.';
?>

<div class="bg-slate-50 rounded-xl p-4 border border-slate-100/50">
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-2">
            <span class="w-8 h-8 rounded-full bg-slate-200 text-slate-700 flex items-center justify-center font-bold text-xs uppercase">
                <?= substr($name, 0, 2) ?>
            </span>
            <div>
                <h5 class="font-semibold text-xs text-slate-800"><?= $name ?></h5>
                <span class="text-[10px] text-slate-400 block"><?= $date ?></span>
            </div>
        </div>
        <!-- Stars -->
        <div class="flex items-center space-x-0.5 text-accent text-xs">
            <?php for ($i = 1; $i <= 5; $i++): ?>
                <i class="fa-<?= $i <= $rating ? 'solid' : 'regular' ?> fa-star"></i>
            <?php endfor; ?>
        </div>
    </div>
    <p class="text-xs text-slate-600 mt-2.5 leading-relaxed">
        <?= $comment ?>
    </p>
</div>

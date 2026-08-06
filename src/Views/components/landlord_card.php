<?php
$id = $id ?? 1;
$name = $name ?? 'Alhaji Ibrahim Babura';
$avatar = $avatar ?? '/assets/images/landlord1.jpg';
$verified = $verified ?? true;
$joined = $joined ?? 'June 2024';
$listings_count = $listings_count ?? 8;
$rating = $rating ?? 4.8;
?>

<div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-sm hover:shadow-lg transition-all duration-300 flex items-center space-x-4">
    <!-- Avatar -->
    <div class="w-14 h-14 rounded-full bg-primary/10 text-primary flex items-center justify-center font-bold text-xl uppercase relative shrink-0">
        <?= substr($name, 0, 2) ?>
        <?php if ($verified): ?>
            <span class="absolute bottom-0 right-0 w-4 h-4 bg-primary text-white text-[9px] rounded-full flex items-center justify-center border-2 border-white" title="Verified Landlord">
                <i class="fa-solid fa-check"></i>
            </span>
        <?php endif; ?>
    </div>
    
    <!-- Info -->
    <div class="flex-grow min-w-0">
        <h4 class="font-bold text-slate-800 text-sm truncate hover:text-primary transition-colors">
            <a href="<?= url('/landlord/' . $id) ?>"><?= $name ?></a>
        </h4>
        <p class="text-xs text-text-muted mt-0.5">Joined <?= $joined ?></p>
        <div class="flex items-center space-x-3 mt-2 text-xs">
            <span class="text-slate-600 font-semibold flex items-center">
                <i class="fa-solid fa-house text-primary/70 mr-1"></i> <?= $listings_count ?> Listings
            </span>
            <span class="text-slate-600 font-semibold flex items-center">
                <i class="fa-solid fa-star text-accent mr-1"></i> <?= $rating ?>
            </span>
        </div>
    </div>
    
    <div>
        <a href="<?= url('/landlord/' . $id) ?>" class="p-2 rounded-lg bg-slate-50 hover:bg-primary/10 hover:text-primary text-text-muted transition-colors flex items-center justify-center" aria-label="View Profile">
            <i class="fa-solid fa-chevron-right text-sm"></i>
        </a>
    </div>
</div>

<?php
$id = $id ?? 1;
$title = $title ?? 'Luxury 3 Bedroom Flat';
$location = $location ?? 'Kofar Gabas, Babura';
$price = $price ?? 150000;
$period = $period ?? 'year';
$beds = $beds ?? 3;
$baths = $baths ?? 2;
$size = $size ?? 120;
$image = $image ?? '/assets/images/house1.jpg';
$type = $type ?? 'Flat';
$verified = $verified ?? true;
?>

<div class="bg-white rounded-2xl overflow-hidden shadow-sm border border-slate-100 hover:shadow-xl hover:border-slate-200 transition-all duration-300 group flex flex-col h-full" data-aos="fade-up">
    <!-- Image Header -->
    <div class="relative overflow-hidden aspect-video bg-slate-100 flex items-center justify-center">
        <!-- SVG Placeholder -->
        <div class="absolute inset-0 flex items-center justify-center bg-gradient-to-tr from-slate-200 to-slate-100">
            <i class="fa-regular fa-image text-slate-400 text-3xl"></i>
        </div>
        
        <!-- Badges -->
        <div class="absolute top-3 left-3 z-10 flex flex-col gap-1.5">
            <?php if ($verified): ?>
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-green-50 text-green-700 border border-green-200/50">
                    <i class="fa-solid fa-circle-check mr-1 text-[10px]"></i> Verified
                </span>
            <?php endif; ?>
            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-white/90 backdrop-blur-md text-slate-800 shadow-sm">
                <?= $type ?>
            </span>
        </div>
        
        <!-- Favorite Button -->
        <button class="absolute top-3 right-3 z-10 p-2 rounded-full bg-white/80 backdrop-blur-md text-slate-600 hover:text-red-500 hover:bg-white shadow-sm hover:scale-110 transition-all" aria-label="Add to favorites">
            <i class="fa-regular fa-heart"></i>
        </button>
    </div>

    <!-- Details Body -->
    <div class="p-4 flex-grow flex flex-col justify-between">
        <div>
            <!-- Location -->
            <p class="text-xs text-text-muted flex items-center mb-1">
                <i class="fa-solid fa-location-dot text-primary/70 mr-1.5"></i> <?= $location ?>
            </p>
            
            <!-- Title -->
            <h3 class="font-bold text-slate-800 text-base line-clamp-1 group-hover:text-primary transition-colors">
                <a href="<?= url('/house/' . $id) ?>"><?= $title ?></a>
            </h3>
        </div>

        <div class="mt-4">
            <!-- Amenities Grid -->
            <div class="grid grid-cols-3 gap-2 py-3 border-t border-b border-slate-100 text-xs text-text-muted">
                <div class="flex items-center justify-center space-x-1.5">
                    <i class="fa-solid fa-bed text-primary/70"></i>
                    <span class="font-medium text-slate-700"><?= $beds ?> Beds</span>
                </div>
                <div class="flex items-center justify-center space-x-1.5">
                    <i class="fa-solid fa-bath text-primary/70"></i>
                    <span class="font-medium text-slate-700"><?= $baths ?> Baths</span>
                </div>
                <div class="flex items-center justify-center space-x-1.5">
                    <i class="fa-solid fa-maximize text-primary/70"></i>
                    <span class="font-medium text-slate-700"><?= $size ?> m²</span>
                </div>
            </div>

            <!-- Price and Details Button -->
            <div class="flex justify-between items-center mt-4">
                <div>
                    <span class="text-lg font-bold text-primary"><?= formatNaira($price) ?></span>
                    <span class="text-xs text-text-muted">/<?= $period ?></span>
                </div>
                <a href="<?= url('/house/' . $id) ?>" class="px-3.5 py-1.5 text-xs font-semibold text-white bg-slate-800 hover:bg-primary hover:shadow-lg hover:shadow-primary/20 rounded-lg transition-all">
                    View Details
                </a>
            </div>
        </div>
    </div>
</div>

<section class="py-12 bg-background">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Profile Banner/Header Card -->
        <div class="bg-white rounded-2xl border border-slate-100 p-6 sm:p-8 shadow-sm flex flex-col md:flex-row items-center md:items-start gap-6 mb-8" data-aos="fade-up">
            <!-- Large Avatar -->
            <div class="w-24 h-24 sm:w-32 sm:h-32 rounded-3xl bg-primary/10 text-primary flex items-center justify-center font-bold text-4xl uppercase relative shrink-0">
                <?= substr($landlord['name'], 0, 2) ?>
                <?php if ($landlord['verified']): ?>
                    <span class="absolute bottom-1 right-1 w-6 h-6 bg-primary text-white text-xs rounded-full flex items-center justify-center border-2 border-white" title="Verified Landlord">
                        <i class="fa-solid fa-check"></i>
                    </span>
                <?php endif; ?>
            </div>

            <!-- Profile Info Details -->
            <div class="flex-grow text-center md:text-left space-y-3">
                <div class="flex flex-col sm:flex-row sm:items-center sm:gap-3 justify-center md:justify-start">
                    <h1 class="text-2xl font-extrabold text-slate-800"><?= $landlord['name'] ?></h1>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-green-50 text-green-700 border border-green-200/50 self-center">
                        <i class="fa-solid fa-circle-check mr-1.5 text-[10px]"></i> Verified Landlord
                    </span>
                </div>
                
                <p class="text-xs text-text-muted">Member since <?= $landlord['joined'] ?> &bull; Active in Babura, Jigawa</p>
                
                <!-- Stats Row -->
                <div class="flex items-center justify-center md:justify-start space-x-6 py-2 text-xs">
                    <div>
                        <span class="text-lg font-bold text-slate-800"><?= count($landlord['listings']) ?></span>
                        <span class="text-text-muted block">Listings</span>
                    </div>
                    <div class="border-l border-slate-200 h-8"></div>
                    <div>
                        <span class="text-lg font-bold text-slate-800"><?= $landlord['rating'] ?></span>
                        <span class="text-text-muted block">Rating (<?= $landlord['reviews_count'] ?> reviews)</span>
                    </div>
                </div>

                <!-- Bio About Description -->
                <div class="pt-4 border-t border-slate-100">
                    <h4 class="font-bold text-slate-800 text-xs mb-1.5">About Landlord</h4>
                    <p class="text-xs text-slate-600 leading-relaxed max-w-2xl"><?= $landlord['about'] ?></p>
                </div>
            </div>

            <!-- Contact Box (Right) -->
            <div class="shrink-0 w-full md:w-64 bg-slate-50 rounded-2xl p-5 border border-slate-100/50 flex flex-col space-y-3">
                <p class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Direct Contact</p>
                <a href="tel:<?= $landlord['phone'] ?>" class="w-full bg-white hover:bg-slate-50 text-slate-800 border border-slate-200 font-semibold py-2 px-3 rounded-lg text-xs transition-all flex items-center justify-center space-x-2">
                    <i class="fa-solid fa-phone text-primary"></i>
                    <span>Call Landlord</span>
                </a>
                <a href="mailto:<?= $landlord['email'] ?>" class="w-full bg-primary hover:bg-primary/95 text-white font-semibold py-2 px-3 rounded-lg text-xs transition-all flex items-center justify-center space-x-2 shadow-sm shadow-primary/10">
                    <i class="fa-solid fa-envelope"></i>
                    <span>Email Landlord</span>
                </a>
            </div>
        </div>

        <!-- Listings Grid Title -->
        <div class="mb-6">
            <h2 class="text-xl font-bold text-slate-800">Properties Listed By <?= $landlord['name'] ?></h2>
            <p class="text-xs text-text-muted mt-1">Rent apartments safely and securely from this verified listing author.</p>
        </div>

        <!-- Listings Grid Container -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php foreach ($landlord['listings'] as $listing): ?>
                <?php component('house_card', $listing); ?>
            <?php endforeach; ?>
        </div>

    </div>
</section>

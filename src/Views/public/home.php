<!-- Hero Section -->
<section class="relative bg-slate-900 text-white overflow-hidden py-20 lg:py-32">
    <!-- Visual background pattern overlay -->
    <div class="absolute inset-0 bg-cover bg-center opacity-15" style="background-image: url('https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=1200&q=80');"></div>
    <div class="absolute inset-0 bg-gradient-to-tr from-slate-950 via-slate-900 to-primary/20"></div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 z-10 flex flex-col items-center text-center">
        <span class="px-3.5 py-1.5 rounded-full text-xs font-semibold bg-primary/20 border border-primary/30 text-primary mb-6 inline-block" data-aos="fade-down">
            Jigawa State Expansion Ready
        </span>
        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight max-w-4xl leading-tight" data-aos="fade-up">
            Find Your Next Perfect Home in <span class="text-primary">Babura Town</span>
        </h1>
        <p class="mt-6 text-base sm:text-lg text-slate-300 max-w-2xl leading-relaxed" data-aos="fade-up" data-aos-delay="100">
            A trusted renting portal connecting local landlords and verified tenants directly. Simple search, secure negotiations, and instant verifications.
        </p>

        <!-- Search Box -->
        <div class="mt-10 w-full max-w-4xl bg-white/10 backdrop-blur-md border border-white/10 p-4 rounded-2xl shadow-2xl" data-aos="fade-up" data-aos-delay="200">
            <form action="<?= url('/browse') ?>" method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-3 bg-white p-3 rounded-xl shadow-lg">
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 text-left uppercase pl-2 mb-1">Location</label>
                    <div class="relative">
                        <i class="fa-solid fa-location-dot absolute left-3 top-2.5 text-slate-400 text-xs"></i>
                        <input type="text" name="location" placeholder="e.g. Kofar Gabas" class="w-full pl-8 pr-3 py-1.5 text-xs border border-transparent focus:border-primary focus:outline-none rounded-lg text-slate-800">
                    </div>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 text-left uppercase pl-2 mb-1">House Type</label>
                    <div class="relative">
                        <i class="fa-solid fa-house absolute left-3 top-2.5 text-slate-400 text-xs"></i>
                        <select name="type" class="w-full pl-8 pr-3 py-1.5 text-xs border border-transparent focus:border-primary focus:outline-none rounded-lg text-slate-800 bg-white">
                            <option value="">Any Type</option>
                            <option value="Flat">Flat</option>
                            <option value="Bungalow">Bungalow</option>
                            <option value="Self-Contain">Self-Contain</option>
                            <option value="Duplex">Duplex</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 text-left uppercase pl-2 mb-1">Max Budget</label>
                    <div class="relative">
                        <i class="fa-solid fa-naira-sign absolute left-3 top-2.5 text-slate-400 text-xs"></i>
                        <input type="number" name="price" placeholder="e.g. 150000" class="w-full pl-8 pr-3 py-1.5 text-xs border border-transparent focus:border-primary focus:outline-none rounded-lg text-slate-800">
                    </div>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full bg-primary hover:bg-primary/95 text-white font-semibold py-2.5 px-4 rounded-lg text-xs transition-all shadow-md shadow-primary/20 flex items-center justify-center space-x-2">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <span>Search Houses</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

<!-- Featured Listings Section -->
<section class="py-16 sm:py-24 bg-background">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-end mb-12">
            <div>
                <span class="text-xs font-bold text-primary uppercase tracking-widest">Selected Homes</span>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-800 mt-2">Featured Properties in Babura</h2>
            </div>
            <a href="<?= url('/browse') ?>" class="text-xs font-semibold text-primary hover:text-primary/80 flex items-center space-x-1 hover:underline">
                <span>View All Properties</span>
                <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php foreach ($listings as $listing): ?>
                <?php component('house_card', $listing); ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- How It Works Section -->
<section class="py-16 sm:py-24 bg-white border-t border-b border-slate-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto mb-16">
            <span class="text-xs font-bold text-primary uppercase tracking-widest">Process</span>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-800 mt-2">How Babura House Connect Works</h2>
            <p class="mt-4 text-xs text-text-muted leading-relaxed">
                We simplify the renting ecosystem for landlords and tenants with high transparency.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="text-center p-6 space-y-4" data-aos="fade-up">
                <span class="w-12 h-12 rounded-2xl bg-primary/10 text-primary flex items-center justify-center font-bold text-lg mx-auto">1</span>
                <h3 class="font-bold text-slate-800 text-sm">Browse Listings</h3>
                <p class="text-xs text-text-muted leading-relaxed">
                    Filter and view properties in Sabo Gari, Kofar Gabas, GRA, or university areas with detailed photos.
                </p>
            </div>
            <div class="text-center p-6 space-y-4" data-aos="fade-up" data-aos-delay="100">
                <span class="w-12 h-12 rounded-2xl bg-accent/10 text-accent flex items-center justify-center font-bold text-lg mx-auto">2</span>
                <h3 class="font-bold text-slate-800 text-sm">Contact Landlord</h3>
                <p class="text-xs text-text-muted leading-relaxed">
                    Instantly contact the landlord directly. We verify landlord IDs to ensure rent security.
                </p>
            </div>
            <div class="text-center p-6 space-y-4" data-aos="fade-up" data-aos-delay="200">
                <span class="w-12 h-12 rounded-2xl bg-blue-100/50 text-blue-600 flex items-center justify-center font-bold text-lg mx-auto">3</span>
                <h3 class="font-bold text-slate-800 text-sm">Move In Happily</h3>
                <p class="text-xs text-text-muted leading-relaxed">
                    Secure your agreement receipt, complete payment terms safely, and collect your keys.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action Section -->
<section class="py-16 sm:py-24 bg-gradient-to-tr from-slate-900 to-slate-950 text-white relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 z-10 relative flex flex-col md:flex-row items-center justify-between">
        <div class="max-w-2xl text-center md:text-left space-y-4">
            <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Are You a House Owner in Babura?</h2>
            <p class="text-xs text-slate-300 max-w-xl leading-relaxed">
                Join our network of certified landlords. Start listing your rooms, apartments, and commercial spaces. Reach thousands of active tenants today.
            </p>
        </div>
        <div class="mt-8 md:mt-0 flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-4 shrink-0">
            <a href="<?= url('/register') ?>" class="px-6 py-3 rounded-lg text-xs font-semibold bg-primary text-white hover:bg-primary/95 shadow-md shadow-primary/25 text-center transition-all">
                List Your House Now
            </a>
            <a href="<?= url('/contact') ?>" class="px-6 py-3 rounded-lg text-xs font-semibold border border-slate-700 bg-transparent text-white hover:bg-slate-800 text-center transition-all">
                Contact Support
            </a>
        </div>
    </div>
</section>

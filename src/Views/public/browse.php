<!-- Browse Page Container -->
<section class="py-12 bg-background" x-data="{ mobileFiltersOpen: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Page Breadcrumb & Header -->
        <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between">
            <div>
                <span class="text-xs text-text-muted">Portal / Browse</span>
                <h1 class="text-2xl font-extrabold text-slate-800 mt-1">Available Rental Properties</h1>
            </div>
            
            <!-- Filters Toggle button for mobile -->
            <button @click="mobileFiltersOpen = true" class="mt-4 md:hidden inline-flex items-center px-4 py-2 border border-slate-200 bg-white rounded-lg text-xs font-semibold text-slate-700 hover:bg-slate-50 transition-colors">
                <i class="fa-solid fa-sliders mr-2"></i> Filter Listings
            </button>
        </div>

        <div class="flex flex-col lg:flex-row gap-8">
            
            <!-- Left Filters Sidebar (Desktop) -->
            <aside class="hidden lg:block w-72 shrink-0 bg-white border border-slate-100 rounded-2xl p-6 shadow-sm self-start">
                <h3 class="font-bold text-slate-800 text-sm mb-6 flex items-center justify-between">
                    <span>Filters</span>
                    <i class="fa-solid fa-sliders text-primary/80"></i>
                </h3>
                
                <form action="<?= url('/browse') ?>" method="GET" class="space-y-6">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-2">Location</label>
                        <select name="location" class="w-full px-3.5 py-2 text-xs border border-slate-200 focus:border-primary focus:outline-none rounded-lg text-slate-800 bg-white">
                            <option value="">All Locations</option>
                            <option value="Kofar Gabas">Kofar Gabas</option>
                            <option value="Sabo Gari">Sabo Gari</option>
                            <option value="GRA">GRA</option>
                            <option value="Tashar Dan-Baba">Tashar Dan-Baba</option>
                            <option value="Kofar Arewa">Kofar Arewa</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-2">House Type</label>
                        <select name="type" class="w-full px-3.5 py-2 text-xs border border-slate-200 focus:border-primary focus:outline-none rounded-lg text-slate-800 bg-white">
                            <option value="">Any Type</option>
                            <option value="Flat">Flat</option>
                            <option value="Bungalow">Bungalow</option>
                            <option value="Self-Contain">Self-Contain</option>
                            <option value="Duplex">Duplex</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-2">Price Range (₦)</label>
                        <div class="space-y-2">
                            <input type="number" name="min_price" placeholder="Min" class="w-full px-3.5 py-2 text-xs border border-slate-200 focus:border-primary focus:outline-none rounded-lg text-slate-800">
                            <input type="number" name="max_price" placeholder="Max" class="w-full px-3.5 py-2 text-xs border border-slate-200 focus:border-primary focus:outline-none rounded-lg text-slate-800">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-2">Bedrooms</label>
                        <div class="grid grid-cols-4 gap-2">
                            <?php foreach ([1, 2, 3, '4+'] as $b): ?>
                                <button type="button" class="py-2 text-center text-xs font-semibold border border-slate-200 rounded-lg hover:border-primary hover:text-primary transition-all bg-white"><?= $b ?></button>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-primary hover:bg-primary/95 text-white font-semibold py-2.5 px-4 rounded-lg text-xs transition-all shadow-md shadow-primary/20">
                        Apply Filters
                    </button>
                </form>
            </aside>

            <!-- Mobile Filters Drawer -->
            <div class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-sm lg:hidden" x-show="mobileFiltersOpen" @click="mobileFiltersOpen = false" x-cloak></div>
            <div class="fixed inset-y-0 left-0 z-50 w-80 max-w-sm bg-white p-6 shadow-2xl transition-transform transform lg:hidden"
                 :class="mobileFiltersOpen ? 'translate-x-0' : '-translate-x-full'" x-cloak>
                <div class="flex items-center justify-between mb-6">
                    <h3 class="font-bold text-slate-800 text-sm">Filters</h3>
                    <button @click="mobileFiltersOpen = false" class="text-slate-400 hover:text-slate-600">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>
                <!-- Filters Form Mobile -->
                <form action="<?= url('/browse') ?>" method="GET" class="space-y-6">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-2">Location</label>
                        <select name="location" class="w-full px-3.5 py-2 text-xs border border-slate-200 focus:border-primary focus:outline-none rounded-lg text-slate-800 bg-white">
                            <option value="">All Locations</option>
                            <option value="Kofar Gabas">Kofar Gabas</option>
                            <option value="Sabo Gari">Sabo Gari</option>
                            <option value="GRA">GRA</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-2">House Type</label>
                        <select name="type" class="w-full px-3.5 py-2 text-xs border border-slate-200 focus:border-primary focus:outline-none rounded-lg text-slate-800 bg-white">
                            <option value="">Any Type</option>
                            <option value="Flat">Flat</option>
                            <option value="Bungalow">Bungalow</option>
                        </select>
                    </div>
                    <button type="submit" class="w-full bg-primary text-white py-2.5 rounded-lg text-xs font-semibold">Apply Filters</button>
                </form>
            </div>

            <!-- Right House Grid Results -->
            <div class="flex-grow">
                <div class="mb-6 flex justify-between items-center text-xs text-text-muted">
                    <p>Showing <span class="font-bold text-slate-700"><?= count($listings) ?></span> matching properties</p>
                    <div>
                        <select class="border border-slate-200 bg-white rounded-lg px-2.5 py-1.5 focus:outline-none text-slate-700">
                            <option>Default Sorting</option>
                            <option>Price: Low to High</option>
                            <option>Price: High to Low</option>
                        </select>
                    </div>
                </div>

                <?php if (empty($listings)): ?>
                    <?php component('empty_states', [
                        'title' => 'No Properties Found',
                        'message' => 'Try expanding your filters or search keywords to find matching listings.',
                        'icon' => 'fa-house-circle-exclamation'
                    ]); ?>
                <?php else: ?>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        <?php foreach ($listings as $listing): ?>
                            <?php component('house_card', $listing); ?>
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- Pagination Component -->
                    <div class="mt-12">
                        <?php component('pagination', [
                            'currentPage' => 1,
                            'totalPages' => 3,
                            'baseUrl' => '/browse'
                        ]); ?>
                    </div>
                <?php endif; ?>
            </div>
            
        </div>
    </div>
</section>

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
                        <select name="area_id" class="w-full px-3.5 py-2 text-xs border border-slate-200 focus:border-primary focus:outline-none rounded-lg text-slate-800 bg-white">
                            <option value="">All Areas</option>
                            <?php foreach ($areas as $area): ?>
                                <option value="<?= $area['id'] ?>" <?= ($filters['area_id'] ?? '') == $area['id'] ? 'selected' : '' ?>><?= htmlspecialchars($area['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-2">House Type</label>
                        <select name="type" class="w-full px-3.5 py-2 text-xs border border-slate-200 focus:border-primary focus:outline-none rounded-lg text-slate-800 bg-white">
                            <option value="">Any Type</option>
                            <?php foreach (['single_room' => 'Single Room', 'room_and_parlor' => 'Room & Parlor', 'two_bedroom' => 'Two Bedroom', 'three_bedroom' => 'Three Bedroom', 'four_bedroom' => 'Four Bedroom', 'self_contain' => 'Self-Contain', 'flat' => 'Flat', 'duplex' => 'Duplex', 'compound_house' => 'Compound House', 'shop' => 'Shop', 'other' => 'Other'] as $val => $label): ?>
                                <option value="<?= $val ?>" <?= ($filters['type'] ?? '') === $val ? 'selected' : '' ?>><?= $label ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-2">Price Range (₦)</label>
                        <div class="space-y-2">
                            <input type="number" name="min_price" value="<?= htmlspecialchars($filters['min_price'] ?? '') ?>" placeholder="Min" class="w-full px-3.5 py-2 text-xs border border-slate-200 focus:border-primary focus:outline-none rounded-lg text-slate-800 bg-white">
                            <input type="number" name="max_price" value="<?= htmlspecialchars($filters['max_price'] ?? '') ?>" placeholder="Max" class="w-full px-3.5 py-2 text-xs border border-slate-200 focus:border-primary focus:outline-none rounded-lg text-slate-800 bg-white">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-2">Bedrooms</label>
                        <select name="bedrooms" class="w-full px-3.5 py-2 text-xs border border-slate-200 focus:border-primary focus:outline-none rounded-lg text-slate-800 bg-white">
                            <option value="">Any Bedrooms</option>
                            <option value="1" <?= ($filters['bedrooms'] ?? '') == '1' ? 'selected' : '' ?>>1 Bedroom</option>
                            <option value="2" <?= ($filters['bedrooms'] ?? '') == '2' ? 'selected' : '' ?>>2 Bedrooms</option>
                            <option value="3" <?= ($filters['bedrooms'] ?? '') == '3' ? 'selected' : '' ?>>3 Bedrooms</option>
                            <option value="4" <?= ($filters['bedrooms'] ?? '') == '4' ? 'selected' : '' ?>>4 Bedrooms</option>
                            <option value="4+" <?= ($filters['bedrooms'] ?? '') == '4+' ? 'selected' : '' ?>>4+ Bedrooms</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-2">Bathrooms</label>
                        <select name="bathrooms" class="w-full px-3.5 py-2 text-xs border border-slate-200 focus:border-primary focus:outline-none rounded-lg text-slate-800 bg-white">
                            <option value="">Any Bathrooms</option>
                            <option value="1" <?= ($filters['bathrooms'] ?? '') == '1' ? 'selected' : '' ?>>1 Bathroom</option>
                            <option value="2" <?= ($filters['bathrooms'] ?? '') == '2' ? 'selected' : '' ?>>2 Bathrooms</option>
                            <option value="3" <?= ($filters['bathrooms'] ?? '') == '3' ? 'selected' : '' ?>>3 Bathrooms</option>
                            <option value="4" <?= ($filters['bathrooms'] ?? '') == '4' ? 'selected' : '' ?>>4+ Bathrooms</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-2">Rent Period</label>
                        <select name="rent_period" class="w-full px-3.5 py-2 text-xs border border-slate-200 focus:border-primary focus:outline-none rounded-lg text-slate-800 bg-white">
                            <option value="">Any Period</option>
                            <option value="year" <?= ($filters['rent_period'] ?? '') === 'year' ? 'selected' : '' ?>>per Year</option>
                            <option value="month" <?= ($filters['rent_period'] ?? '') === 'month' ? 'selected' : '' ?>>per Month</option>
                        </select>
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
                        <select name="area_id" class="w-full px-3.5 py-2 text-xs border border-slate-200 focus:border-primary focus:outline-none rounded-lg text-slate-800 bg-white">
                            <option value="">All Areas</option>
                            <?php foreach ($areas as $area): ?>
                                <option value="<?= $area['id'] ?>" <?= ($filters['area_id'] ?? '') == $area['id'] ? 'selected' : '' ?>><?= htmlspecialchars($area['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-2">House Type</label>
                        <select name="type" class="w-full px-3.5 py-2 text-xs border border-slate-200 focus:border-primary focus:outline-none rounded-lg text-slate-800 bg-white">
                            <option value="">Any Type</option>
                            <?php foreach (['single_room' => 'Single Room', 'room_and_parlor' => 'Room & Parlor', 'two_bedroom' => 'Two Bedroom', 'three_bedroom' => 'Three Bedroom', 'four_bedroom' => 'Four Bedroom', 'self_contain' => 'Self-Contain', 'flat' => 'Flat', 'duplex' => 'Duplex', 'compound_house' => 'Compound House', 'shop' => 'Shop', 'other' => 'Other'] as $val => $label): ?>
                                <option value="<?= $val ?>" <?= ($filters['type'] ?? '') === $val ? 'selected' : '' ?>><?= $label ?></option>
                            <?php endforeach; ?>
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

<section class="py-12 bg-background">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
        <?php if (\App\Helpers\Flash::has('error')): ?>
            <?php component('alerts', ['type' => 'error', 'message' => \App\Helpers\Flash::get('error')]); ?>
        <?php endif; ?>
        <?php if (\App\Helpers\Flash::has('success')): ?>
            <?php component('alerts', ['type' => 'success', 'message' => \App\Helpers\Flash::get('success')]); ?>
        <?php endif; ?>
        
        <!-- Breadcrumb navigation -->
        <nav class="flex text-xs text-text-muted mb-6 space-x-2">
            <a href="<?= url('/') ?>" class="hover:text-primary transition-colors">Home</a>
            <span>/</span>
            <a href="<?= url('/browse') ?>" class="hover:text-primary transition-colors">Browse</a>
            <span>/</span>
            <span class="text-slate-800 font-medium"><?= $house['title'] ?></span>
        </nav>

        <!-- Main Details Grid Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Left Info Panel (2 Columns width) -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Gallery Carousel (Swiper Container) -->
                <div class="bg-white rounded-2xl overflow-hidden shadow-sm border border-slate-100 p-4 space-y-4">
                    <div class="aspect-video relative rounded-xl overflow-hidden bg-slate-100">
                        <?php if (!empty($house['images'])): ?>
                            <img src="<?= url($house['images'][0]) ?>" id="main-gallery-image" class="w-full h-full object-cover">
                        <?php else: ?>
                            <div class="absolute inset-0 bg-gradient-to-tr from-slate-200 to-slate-100 flex items-center justify-center">
                                <i class="fa-regular fa-images text-slate-400 text-5xl"></i>
                            </div>
                        <?php endif; ?>
                        
                        <div class="absolute top-4 left-4 z-10">
                            <?php if ($house['verified']): ?>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-50 text-green-700 border border-green-200/50 shadow-sm">
                                    <i class="fa-solid fa-circle-check mr-1.5"></i> Verified Property
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <?php if (count($house['images'] ?? []) > 1): ?>
                        <div class="grid grid-cols-4 gap-2">
                            <?php foreach ($house['images'] as $img): ?>
                                <button type="button" onclick="document.getElementById('main-gallery-image').src = '<?= url($img) ?>'" class="aspect-video rounded-lg overflow-hidden border border-slate-200 hover:border-primary transition-all">
                                    <img src="<?= url($img) ?>" class="w-full h-full object-cover">
                                </button>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Specs Header Card -->
                <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div>
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-primary/10 text-primary uppercase tracking-wider"><?= $house['type'] ?></span>
                            <h1 class="text-2xl font-extrabold text-slate-800 mt-2"><?= $house['title'] ?></h1>
                            <p class="text-xs text-text-muted mt-1 flex items-center">
                                <i class="fa-solid fa-location-dot text-primary/70 mr-1.5"></i> <?= $house['location'] ?>
                            </p>
                        </div>
                        <div class="text-left sm:text-right shrink-0 flex items-center space-x-3">
                            <div>
                                <p class="text-xs text-text-muted">Rent Price</p>
                                <p class="text-2xl font-extrabold text-primary"><?= formatNaira($house['price']) ?></p>
                                <p class="text-[10px] text-text-muted">per <?= $house['period'] ?></p>
                            </div>
                            
                            <?php if (\App\Helpers\Auth::check() && \App\Helpers\Auth::role() === 'tenant'): ?>
                                <form action="<?= url($isFavorited ? '/tenant/favorites/remove' : '/tenant/favorites/add') ?>" method="POST" class="inline">
                                    <?= \App\Helpers\CSRF::field() ?>
                                    <input type="hidden" name="house_id" value="<?= $house['id'] ?>">
                                    <button type="submit" class="p-3.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 transition-all flex items-center justify-center shrink-0 <?= $isFavorited ? 'text-red-500 border-red-200' : 'text-slate-450' ?>" title="<?= $isFavorited ? 'Remove from Favorites' : 'Add to Favorites' ?>">
                                        <i class="fa-<?= $isFavorited ? 'solid' : 'regular' ?> fa-heart text-base"></i>
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Amenities Quick Grid -->
                    <div class="grid grid-cols-3 gap-4 pt-6 border-t border-slate-100 text-center">
                        <div class="p-3 bg-slate-50 rounded-xl">
                            <p class="text-xs text-text-muted">Bedrooms</p>
                            <p class="text-base font-bold text-slate-800 mt-1"><i class="fa-solid fa-bed text-primary/70 mr-1.5"></i> <?= $house['beds'] ?></p>
                        </div>
                        <div class="p-3 bg-slate-50 rounded-xl">
                            <p class="text-xs text-text-muted">Bathrooms</p>
                            <p class="text-base font-bold text-slate-800 mt-1"><i class="fa-solid fa-bath text-primary/70 mr-1.5"></i> <?= $house['baths'] ?></p>
                        </div>
                        <div class="p-3 bg-slate-50 rounded-xl">
                            <p class="text-xs text-text-muted">Area Size</p>
                            <p class="text-base font-bold text-slate-800 mt-1"><i class="fa-solid fa-maximize text-primary/70 mr-1.5"></i> <?= $house['size'] ?> m²</p>
                        </div>
                    </div>
                </div>

                <!-- Description & Overview -->
                <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm space-y-4">
                    <h3 class="font-bold text-slate-800 text-base">Property Description</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        <?= $house['description'] ?>
                    </p>
                </div>

                <!-- Amenities List -->
                <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm space-y-4">
                    <h3 class="font-bold text-slate-800 text-base">Features & Amenities</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <?php foreach ($house['amenities'] as $amenity): ?>
                            <div class="flex items-center space-x-2 text-xs text-slate-700">
                                <i class="fa-solid fa-circle-check text-primary text-sm shrink-0"></i>
                                <span><?= $amenity ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Video Tour -->
                <?php if (!empty($house['video'])): ?>
                    <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm space-y-4">
                        <h3 class="font-bold text-slate-800 text-base">Property Video Tour</h3>
                        <div class="aspect-video rounded-xl overflow-hidden bg-slate-900">
                            <video src="<?= url($house['video']) ?>" controls class="w-full h-full"></video>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Reviews section -->
                <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm space-y-6">
                    <div class="flex justify-between items-center">
                        <h3 class="font-bold text-slate-800 text-base">Tenant Reviews</h3>
                        <span class="text-xs text-text-muted"><?= count($house['reviews']) ?> reviews</span>
                    </div>
                    <div class="space-y-4">
                        <?php foreach ($house['reviews'] as $review): ?>
                            <?php component('review_card', $review); ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Right Contact Card (1 Column width) -->
            <div class="space-y-6">
                <!-- Landlord Card Component -->
                <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm space-y-6">
                    <h3 class="font-bold text-slate-800 text-base">Listed By Landlord</h3>
                    <?php component('landlord_card', $house['landlord']); ?>
                    
                    <div class="space-y-2.5 pt-4 border-t border-slate-100 text-xs text-text-muted">
                        <div class="flex justify-between">
                            <span>Phone:</span>
                            <span class="font-semibold text-slate-800"><?= $house['landlord']['phone'] ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span>Active listings:</span>
                            <span class="font-semibold text-slate-800"><?= $house['landlord']['listings_count'] ?> active listings</span>
                        </div>
                    </div>
                </div>
                <!-- Direct Message/Inquiry Form -->
                <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm space-y-4">
                    <h3 class="font-bold text-slate-800 text-sm">Send Rental Inquiry</h3>
                    <?php if (\App\Helpers\Auth::check() && \App\Helpers\Auth::role() === 'tenant'): ?>
                        <form action="<?= url('/house/' . $house['id'] . '/inquire') ?>" method="POST" class="space-y-4">
                            <?= \App\Helpers\CSRF::field() ?>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Your Name</label>
                                <input type="text" readonly value="<?= htmlspecialchars(\App\Helpers\Auth::user('full_name')) ?>" class="w-full px-3.5 py-2 text-xs border border-slate-200 bg-slate-50 rounded-lg text-slate-500 focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Message</label>
                                <textarea name="message" rows="4" required class="w-full px-3.5 py-2 text-xs border border-slate-200 focus:border-primary focus:outline-none rounded-lg text-slate-800 bg-white resize-none">Hello, I am interested in your listing: '<?= htmlspecialchars($house['title']) ?>' and would like to arrange a viewing.</textarea>
                            </div>
                            <button type="submit" class="w-full bg-primary hover:bg-primary/95 text-white font-semibold py-2.5 px-4 rounded-lg text-xs transition-all shadow-md shadow-primary/20 flex items-center justify-center space-x-2">
                                <i class="fa-regular fa-paper-plane"></i>
                                <span>Send Message</span>
                            </button>
                        </form>
                    <?php elseif (\App\Helpers\Auth::check() && \App\Helpers\Auth::role() !== 'tenant'): ?>
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-100 text-center text-xs text-text-muted">
                            Only tenants can send inquiries.
                        </div>
                    <?php else: ?>
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-100 text-center space-y-3">
                            <p class="text-xs text-text-muted">You must be logged in as a tenant to contact the landlord.</p>
                            <a href="<?= url('/login') ?>" class="w-full bg-slate-800 hover:bg-slate-900 text-white font-semibold py-2 px-4 rounded-lg text-xs transition-all flex items-center justify-center space-x-1.5">
                                <i class="fa-solid fa-right-to-bracket text-[10px]"></i>
                                <span>Sign In to Inquire</span>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Report Listing Form -->
                <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm space-y-4">
                    <h3 class="font-bold text-slate-800 text-sm flex items-center text-red-650">
                        <i class="fa-solid fa-triangle-exclamation mr-1.5"></i>
                        <span>Report Suspicious Property</span>
                    </h3>
                    <?php if (\App\Helpers\Auth::check()): ?>
                        <form action="<?= url('/house/' . $house['id'] . '/report') ?>" method="POST" class="space-y-4">
                            <?= \App\Helpers\CSRF::field() ?>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Reason for Report</label>
                                <select name="reason" required class="w-full px-3.5 py-2 text-xs border border-slate-200 focus:border-primary focus:outline-none rounded-lg text-slate-850 bg-white">
                                    <option value="misleading">Misleading information</option>
                                    <option value="fake">Fake property listing</option>
                                    <option value="unavailable">Property is no longer available</option>
                                    <option value="pricing">Incorrect pricing or fraud</option>
                                    <option value="other">Other issues</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Detailed Description</label>
                                <textarea name="description" rows="3" required placeholder="Provide details of the issue..." class="w-full px-3.5 py-2 text-xs border border-slate-200 focus:border-primary focus:outline-none rounded-lg text-slate-800 bg-white resize-none"></textarea>
                            </div>
                            <button type="submit" class="w-full bg-red-55/10 hover:bg-red-50 text-red-600 font-semibold py-2 px-4 rounded-lg text-xs transition-all flex items-center justify-center space-x-1.5">
                                <i class="fa-solid fa-flag text-[10px]"></i>
                                <span>Submit Report</span>
                            </button>
                        </form>
                    <?php else: ?>
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-100 text-center space-y-3">
                            <p class="text-xs text-text-muted">You must be logged in to report this property.</p>
                            <a href="<?= url('/login') ?>" class="w-full bg-slate-800 hover:bg-slate-900 text-white font-semibold py-2 px-4 rounded-lg text-xs transition-all flex items-center justify-center space-x-1.5">
                                <i class="fa-solid fa-right-to-bracket text-[10px]"></i>
                                <span>Sign In to Report</span>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>>
            
        </div>
    </div>
</section>

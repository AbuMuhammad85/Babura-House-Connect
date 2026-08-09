<?php
use App\Helpers\Flash;
?>

<div class="space-y-6 max-w-4xl">
    <div>
        <h2 class="text-xl font-bold text-slate-800">Add New Rental Property</h2>
        <p class="text-xs text-text-muted mt-1">Submit your property specifications. It will be verified before publishing online.</p>
    </div>

    <?php if (Flash::has('error')): ?>
        <?php component('alerts', ['type' => 'error', 'message' => Flash::get('error')]); ?>
    <?php endif; ?>
    <?php if (Flash::has('success')): ?>
        <?php component('alerts', ['type' => 'success', 'message' => Flash::get('success')]); ?>
    <?php endif; ?>

    <!-- Add Listing Form -->
    <div class="bg-white border border-slate-100 rounded-2xl p-6 sm:p-8 shadow-sm">
        <form action="<?= url('/landlord/add-house') ?>" method="POST" enctype="multipart/form-data" class="space-y-6">
            <?= \App\Helpers\CSRF::field() ?>
            
            <!-- Basic Details -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Property Title</label>
                    <input type="text" name="title" required placeholder="e.g. Modern 3 Bedroom Flat with steady water" class="w-full px-3.5 py-2 text-xs border border-slate-200 focus:border-primary focus:outline-none rounded-lg text-slate-800 bg-white">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Location Area (Babura)</label>
                    <select name="area_id" required class="w-full px-3.5 py-2 text-xs border border-slate-200 focus:border-primary focus:outline-none rounded-lg text-slate-800 bg-white">
                        <?php foreach ($areas as $area): ?>
                            <option value="<?= $area['id'] ?>"><?= htmlspecialchars($area['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Detailed Address</label>
                    <input type="text" name="address" required placeholder="e.g. Near Federal University, Sabo Gari" class="w-full px-3.5 py-2 text-xs border border-slate-200 focus:border-primary focus:outline-none rounded-lg text-slate-800 bg-white">
                </div>
            </div>

            <!-- Price and specs -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Rent Price (₦)</label>
                    <input type="number" name="price" required placeholder="e.g. 150000" class="w-full px-3.5 py-2 text-xs border border-slate-200 focus:border-primary focus:outline-none rounded-lg text-slate-800 bg-white">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Rent Period</label>
                    <select name="period" class="w-full px-3.5 py-2 text-xs border border-slate-200 focus:border-primary focus:outline-none rounded-lg text-slate-800 bg-white">
                        <option value="year">per Year</option>
                        <option value="month">per Month</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Property Type</label>
                    <select name="type" class="w-full px-3.5 py-2 text-xs border border-slate-200 focus:border-primary focus:outline-none rounded-lg text-slate-800 bg-white">
                        <option value="single_room">Single Room</option>
                        <option value="room_and_parlor">Room & Parlor</option>
                        <option value="two_bedroom">Two Bedroom</option>
                        <option value="three_bedroom">Three Bedroom</option>
                        <option value="four_bedroom">Four Bedroom</option>
                        <option value="self_contain">Self-Contain</option>
                        <option value="flat">Flat</option>
                        <option value="duplex">Duplex</option>
                        <option value="compound_house">Compound House</option>
                        <option value="shop">Shop</option>
                        <option value="other">Other</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Total Size (m²)</label>
                    <input type="number" name="size" required placeholder="e.g. 120" class="w-full px-3.5 py-2 text-xs border border-slate-200 focus:border-primary focus:outline-none rounded-lg text-slate-800 bg-white">
                </div>
            </div>

            <!-- Rooms specs and files uploads -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Bedrooms count</label>
                    <input type="number" name="beds" required placeholder="3" class="w-full px-3.5 py-2 text-xs border border-slate-200 focus:border-primary focus:outline-none rounded-lg text-slate-800 bg-white">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Bathrooms count</label>
                    <input type="number" name="baths" required placeholder="2" class="w-full px-3.5 py-2 text-xs border border-slate-200 focus:border-primary focus:outline-none rounded-lg text-slate-800 bg-white">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Property Images (Max 5)</label>
                    <input type="file" multiple name="images[]" required class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Property Video (Optional)</label>
                    <input type="file" name="video" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20">
                </div>
            </div>

            <!-- Description -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Detailed Description</label>
                <textarea name="description" rows="5" required placeholder="Describe electricity, security, accessibility, water flow..." class="w-full px-3.5 py-2 text-xs border border-slate-200 focus:border-primary focus:outline-none rounded-lg text-slate-800 bg-white resize-none"></textarea>
            </div>

            <!-- Amenities -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-2">Amenities / Features</label>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                    <?php foreach (['Fenced Yard', 'Steady Water Pump', 'Prepaid Meter', '24/7 Security', 'Ample Parking', 'Fully Tiled Rooms'] as $amenity): ?>
                        <div class="flex items-center">
                            <input type="checkbox" id="<?= str_replace(' ', '_', $amenity) ?>" name="amenities[]" value="<?= $amenity ?>" class="w-4 h-4 rounded text-primary focus:ring-primary border-slate-200">
                            <label for="<?= str_replace(' ', '_', $amenity) ?>" class="ml-2 text-xs text-slate-700"><?= $amenity ?></label>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 flex space-x-3 justify-end">
                <a href="<?= url('/landlord/listings') ?>" class="px-4 py-2 border border-slate-200 bg-white text-slate-700 rounded-lg text-xs font-semibold hover:bg-slate-50 transition-colors">Cancel</a>
                <button type="submit" class="bg-primary hover:bg-primary/95 text-white font-semibold py-2 px-6 rounded-lg text-xs transition-all shadow-md">
                    Publish Listing
                </button>
            </div>
        </form>
    </div>
</div>

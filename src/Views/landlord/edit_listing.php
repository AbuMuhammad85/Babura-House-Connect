<?php
use App\Helpers\Flash;

$amenitiesList = ['Fenced Yard', 'Steady Water Pump', 'Prepaid Meter', '24/7 Security', 'Ample Parking', 'Fully Tiled Rooms'];
$activeAmenities = array_map('trim', explode(',', $house['amenities'] ?? ''));
?>

<div class="space-y-6 max-w-4xl">
    <div>
        <h2 class="text-xl font-bold text-slate-800">Edit Property Listing</h2>
        <p class="text-xs text-text-muted mt-1">Modify property details. Save to trigger a re-verification if critical fields are altered.</p>
    </div>

    <?php if (Flash::has('error')): ?>
        <?php component('alerts', ['type' => 'error', 'message' => Flash::get('error')]); ?>
    <?php endif; ?>
    <?php if (Flash::has('success')): ?>
        <?php component('alerts', ['type' => 'success', 'message' => Flash::get('success')]); ?>
    <?php endif; ?>

    <!-- Edit Form Container -->
    <div class="bg-white border border-slate-100 rounded-2xl p-6 sm:p-8 shadow-sm">
        <form action="<?= url('/landlord/listings/edit/' . $house['id']) ?>" method="POST" class="space-y-6">
            <?= \App\Helpers\CSRF::field() ?>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Property Title</label>
                    <input type="text" name="title" value="<?= htmlspecialchars($house['title']) ?>" required class="w-full px-3.5 py-2 text-xs border border-slate-200 focus:border-primary focus:outline-none rounded-lg text-slate-800 bg-white">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Location Area (Babura)</label>
                    <select name="area_id" required class="w-full px-3.5 py-2 text-xs border border-slate-200 focus:border-primary focus:outline-none rounded-lg text-slate-800 bg-white">
                        <?php foreach ($areas as $area): ?>
                            <option value="<?= $area['id'] ?>" <?= $house['area_id'] == $area['id'] ? 'selected' : '' ?>><?= htmlspecialchars($area['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Detailed Address</label>
                    <input type="text" name="address" value="<?= htmlspecialchars($house['address']) ?>" required class="w-full px-3.5 py-2 text-xs border border-slate-200 focus:border-primary focus:outline-none rounded-lg text-slate-800 bg-white">
                </div>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Rent Price (₦)</label>
                    <input type="number" name="price" value="<?= (int)$house['rent_amount'] ?>" required class="w-full px-3.5 py-2 text-xs border border-slate-200 focus:border-primary focus:outline-none rounded-lg text-slate-800 bg-white">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Rent Period</label>
                    <select name="period" class="w-full px-3.5 py-2 text-xs border border-slate-200 focus:border-primary focus:outline-none rounded-lg text-slate-800 bg-white">
                        <option value="year" <?= $house['rent_period'] === 'year' ? 'selected' : '' ?>>per Year</option>
                        <option value="month" <?= $house['rent_period'] === 'month' ? 'selected' : '' ?>>per Month</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Property Type</label>
                    <select name="type" class="w-full px-3.5 py-2 text-xs border border-slate-200 focus:border-primary focus:outline-none rounded-lg text-slate-800 bg-white">
                        <?php foreach (['single_room' => 'Single Room', 'room_and_parlor' => 'Room & Parlor', 'two_bedroom' => 'Two Bedroom', 'three_bedroom' => 'Three Bedroom', 'four_bedroom' => 'Four Bedroom', 'self_contain' => 'Self-Contain', 'flat' => 'Flat', 'duplex' => 'Duplex', 'compound_house' => 'Compound House', 'shop' => 'Shop', 'other' => 'Other'] as $val => $label): ?>
                            <option value="<?= $val ?>" <?= $house['house_type'] === $val ? 'selected' : '' ?>><?= $label ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Total Size (m²)</label>
                    <input type="number" name="size" value="<?= $house['size'] ?>" required class="w-full px-3.5 py-2 text-xs border border-slate-200 focus:border-primary focus:outline-none rounded-lg text-slate-800 bg-white">
                </div>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Bedrooms count</label>
                    <input type="number" name="beds" value="<?= $house['bedrooms'] ?>" required class="w-full px-3.5 py-2 text-xs border border-slate-200 focus:border-primary focus:outline-none rounded-lg text-slate-800 bg-white">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Bathrooms count</label>
                    <input type="number" name="baths" value="<?= $house['bathrooms'] ?>" required class="w-full px-3.5 py-2 text-xs border border-slate-200 focus:border-primary focus:outline-none rounded-lg text-slate-800 bg-white">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Detailed Description</label>
                <textarea name="description" rows="5" required class="w-full px-3.5 py-2 text-xs border border-slate-200 focus:border-primary focus:outline-none rounded-lg text-slate-800 bg-white resize-none"><?= htmlspecialchars($house['description']) ?></textarea>
            </div>

            <!-- Amenities -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-2">Amenities / Features</label>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                    <?php foreach ($amenitiesList as $amenity): ?>
                        <div class="flex items-center">
                            <input type="checkbox" id="<?= str_replace(' ', '_', $amenity) ?>" name="amenities[]" value="<?= $amenity ?>" <?= in_array($amenity, $activeAmenities) ? 'checked' : '' ?> class="w-4 h-4 rounded text-primary focus:ring-primary border-slate-200">
                            <label for="<?= str_replace(' ', '_', $amenity) ?>" class="ml-2 text-xs text-slate-700"><?= $amenity ?></label>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 flex space-x-3 justify-end">
                <a href="<?= url('/landlord/listings') ?>" class="px-4 py-2 border border-slate-200 bg-white text-slate-700 rounded-lg text-xs font-semibold hover:bg-slate-50 transition-colors">Cancel</a>
                <button type="submit" class="bg-primary hover:bg-primary/95 text-white font-semibold py-2 px-6 rounded-lg text-xs transition-all shadow-md">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

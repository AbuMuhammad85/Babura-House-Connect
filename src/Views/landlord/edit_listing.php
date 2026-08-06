<div class="space-y-6 max-w-4xl">
    <div>
        <h2 class="text-xl font-bold text-slate-800">Edit Property Listing</h2>
        <p class="text-xs text-text-muted mt-1">Modify property details. Save to trigger a re-verification if critical fields are altered.</p>
    </div>

    <!-- Edit Form Container -->
    <div class="bg-white border border-slate-100 rounded-2xl p-6 sm:p-8 shadow-sm">
        <form action="<?= url('/landlord/listings/edit/' . $house['id']) ?>" method="POST" class="space-y-6">
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Property Title</label>
                    <input type="text" name="title" value="<?= $house['title'] ?>" required class="w-full px-3.5 py-2 text-xs border border-slate-200 focus:border-primary focus:outline-none rounded-lg text-slate-800 bg-white">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Location Area (Babura)</label>
                    <select name="location" class="w-full px-3.5 py-2 text-xs border border-slate-200 focus:border-primary focus:outline-none rounded-lg text-slate-800 bg-white">
                        <option <?= $house['location'] === 'Kofar Gabas' ? 'selected' : '' ?>>Kofar Gabas</option>
                        <option <?= $house['location'] === 'Sabo Gari' ? 'selected' : '' ?>>Sabo Gari</option>
                        <option <?= $house['location'] === 'GRA' ? 'selected' : '' ?>>GRA</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Rent Price (₦)</label>
                    <input type="number" name="price" value="<?= $house['price'] ?>" required class="w-full px-3.5 py-2 text-xs border border-slate-200 focus:border-primary focus:outline-none rounded-lg text-slate-800 bg-white">
                </div>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Rent Period</label>
                    <select name="period" class="w-full px-3.5 py-2 text-xs border border-slate-200 focus:border-primary focus:outline-none rounded-lg text-slate-800 bg-white">
                        <option value="year" <?= $house['period'] === 'year' ? 'selected' : '' ?>>per Year</option>
                        <option value="month" <?= $house['period'] === 'month' ? 'selected' : '' ?>>per Month</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Total Size (m²)</label>
                    <input type="number" name="size" value="<?= $house['size'] ?>" required class="w-full px-3.5 py-2 text-xs border border-slate-200 focus:border-primary focus:outline-none rounded-lg text-slate-800 bg-white">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Bedrooms count</label>
                    <input type="number" name="beds" value="<?= $house['beds'] ?>" required class="w-full px-3.5 py-2 text-xs border border-slate-200 focus:border-primary focus:outline-none rounded-lg text-slate-800 bg-white">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Bathrooms count</label>
                    <input type="number" name="baths" value="<?= $house['baths'] ?>" required class="w-full px-3.5 py-2 text-xs border border-slate-200 focus:border-primary focus:outline-none rounded-lg text-slate-800 bg-white">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Detailed Description</label>
                <textarea name="description" rows="5" required class="w-full px-3.5 py-2 text-xs border border-slate-200 focus:border-primary focus:outline-none rounded-lg text-slate-800 bg-white resize-none"><?= $house['description'] ?></textarea>
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

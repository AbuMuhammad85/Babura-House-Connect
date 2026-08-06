<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Manage Listings</h2>
            <p class="text-xs text-text-muted mt-1">Configure, inspect, or edit your rental listings in Babura.</p>
        </div>
        <a href="<?= url('/landlord/add-house') ?>" class="px-4 py-2 bg-primary text-white hover:bg-primary/95 text-xs font-semibold rounded-lg shadow-sm hover:shadow-lg transition-all flex items-center justify-center space-x-1.5 self-start">
            <i class="fa-solid fa-circle-plus"></i>
            <span>Add New Property</span>
        </a>
    </div>

    <!-- Listings Table Container -->
    <?php if (empty($listings)): ?>
        <?php component('empty_states', [
            'title' => 'No Listings Created Yet',
            'message' => 'Publish your rooms or flats here to start getting tenant inquiries.',
            'icon' => 'fa-house-circle-exclamation',
            'actionUrl' => '/landlord/add-house',
            'actionText' => 'Create First Listing'
        ]); ?>
    <?php else: ?>
        <div class="overflow-x-auto bg-white border border-slate-100 rounded-2xl shadow-sm">
            <table class="min-w-full divide-y divide-slate-100 text-left">
                <thead class="bg-slate-50 text-[10px] font-bold uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-6 py-3">Property Details</th>
                        <th class="px-6 py-3">Price</th>
                        <th class="px-6 py-3">Views</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Verification</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white text-xs text-slate-700">
                    <?php foreach ($listings as $listing): ?>
                        <tr>
                            <!-- Image + Title -->
                            <td class="px-6 py-4 flex items-center space-x-3">
                                <span class="w-10 h-10 rounded-lg bg-slate-100 text-slate-400 flex items-center justify-center shrink-0">
                                    <i class="fa-regular fa-image"></i>
                                </span>
                                <div>
                                    <span class="font-bold text-slate-800 line-clamp-1"><?= $listing['title'] ?></span>
                                    <span class="text-[10px] text-text-muted mt-0.5 block flex items-center"><i class="fa-solid fa-location-dot mr-1"></i> <?= $listing['location'] ?></span>
                                </div>
                            </td>
                            <!-- Price -->
                            <td class="px-6 py-4 font-bold text-slate-800">
                                <?= formatNaira($listing['price']) ?>
                            </td>
                            <!-- Views -->
                            <td class="px-6 py-4 text-text-muted">
                                <?= $listing['views'] ?> views
                            </td>
                            <!-- Status -->
                            <td class="px-6 py-4">
                                <?php 
                                $statusType = 'warning';
                                if ($listing['status'] === 'Active') $statusType = 'success';
                                ?>
                                <?php component('badges', ['type' => $statusType, 'text' => $listing['status']]); ?>
                            </td>
                            <!-- Verification -->
                            <td class="px-6 py-4">
                                <?php if ($listing['verified']): ?>
                                    <span class="text-green-700 font-semibold flex items-center"><i class="fa-solid fa-circle-check mr-1 text-[10px]"></i> Verified</span>
                                <?php else: ?>
                                    <span class="text-slate-400 flex items-center"><i class="fa-solid fa-circle-minus mr-1 text-[10px]"></i> Pending</span>
                                <?php endif; ?>
                            </td>
                            <!-- Actions -->
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end space-x-2">
                                    <a href="<?= url('/landlord/listings/edit/' . $listing['id']) ?>" class="p-1.5 rounded-lg border border-slate-200 bg-white text-slate-600 hover:text-primary hover:border-primary/50 transition-colors flex items-center justify-center" title="Edit Listing">
                                        <i class="fa-regular fa-pen-to-square text-xs"></i>
                                    </a>
                                    <button class="p-1.5 rounded-lg border border-slate-200 bg-white text-slate-600 hover:text-red-600 hover:border-red-300 transition-colors flex items-center justify-center" title="Delete Listing">
                                        <i class="fa-regular fa-trash-can text-xs"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

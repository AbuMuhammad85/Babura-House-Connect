<div class="space-y-6">
    <div>
        <h2 class="text-xl font-bold text-slate-800">Manage Listings</h2>
        <p class="text-xs text-text-muted mt-1">Approve, reject, or remove housing listings from the public marketplace.</p>
    </div>

    <!-- Listings Table Container -->
    <div class="overflow-x-auto bg-white border border-slate-100 rounded-2xl shadow-sm">
        <table class="min-w-full divide-y divide-slate-100 text-left">
            <thead class="bg-slate-50 text-[10px] font-bold uppercase tracking-wider text-slate-500">
                <tr>
                    <th class="px-6 py-3">Property Title</th>
                    <th class="px-6 py-3">Listed By Landlord</th>
                    <th class="px-6 py-3">Price</th>
                    <th class="px-6 py-3">Status</th>
                    <th class="px-6 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 bg-white text-xs text-slate-700">
                <?php foreach ($houses as $house): ?>
                    <tr>
                        <td class="px-6 py-4">
                            <span class="font-bold text-slate-850 block"><?= $house['title'] ?></span>
                            <span class="text-[10px] text-text-muted mt-0.5 block">Property ID: #HC-<?= $house['id'] ?></span>
                        </td>
                        <td class="px-6 py-4 font-semibold text-slate-700">
                            <?= $house['landlord'] ?>
                        </td>
                        <td class="px-6 py-4 font-bold text-slate-800">
                            <?= formatNaira($house['price']) ?>
                        </td>
                        <td class="px-6 py-4">
                            <?php 
                            $statusType = 'warning';
                            if ($house['status'] === 'Approved') $statusType = 'success';
                            ?>
                            <?php component('badges', ['type' => $statusType, 'text' => $house['status']]); ?>
                        </td>
                        <!-- Action buttons -->
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end space-x-2">
                                <?php if ($house['status'] !== 'Approved'): ?>
                                    <button class="px-2.5 py-1.5 bg-primary hover:bg-primary/95 text-white font-semibold rounded-lg text-[10px] shadow-sm transition-all">
                                        Approve
                                    </button>
                                <?php endif; ?>
                                <a href="<?= url('/house/' . $house['id']) ?>" target="_blank" class="px-2.5 py-1.5 border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-semibold rounded-lg text-[10px] transition-all">
                                    Preview
                                </a>
                                <button class="p-1.5 border border-slate-200 bg-white hover:bg-red-50 text-slate-400 hover:text-red-650 hover:border-red-200 rounded-lg transition-colors flex items-center justify-center" title="Delete Listing">
                                    <i class="fa-regular fa-trash-can text-xs"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="space-y-6">
    <div>
        <h2 class="text-xl font-bold text-slate-800">Manage Reviews</h2>
        <p class="text-xs text-text-muted mt-1">Moderate tenant reviews left on landlord properties in Babura.</p>
    </div>

    <!-- Reviews Moderation Table -->
    <div class="overflow-x-auto bg-white border border-slate-100 rounded-2xl shadow-sm">
        <table class="min-w-full divide-y divide-slate-100 text-left">
            <thead class="bg-slate-50 text-[10px] font-bold uppercase tracking-wider text-slate-500">
                <tr>
                    <th class="px-6 py-3">Tenant / Property Info</th>
                    <th class="px-6 py-3">Rating / Feedback Comment</th>
                    <th class="px-6 py-3">Status</th>
                    <th class="px-6 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 bg-white text-xs text-slate-700">
                <?php foreach ($reviews as $review): ?>
                    <tr>
                        <td class="px-6 py-4">
                            <span class="font-bold text-slate-850 block"><?= $review['user'] ?></span>
                            <span class="text-[10px] text-text-muted mt-0.5 block">On: <?= $review['house'] ?></span>
                        </td>
                        <td class="px-6 py-4 space-y-2 max-w-sm">
                            <!-- Stars -->
                            <div class="flex items-center space-x-0.5 text-accent text-[10px]">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <i class="fa-<?= $i <= $review['rating'] ? 'solid' : 'regular' ?> fa-star"></i>
                                <?php endfor; ?>
                            </div>
                            <p class="text-[11px] text-slate-600 italic leading-relaxed">&ldquo;<?= $review['comment'] ?>&rdquo;</p>
                        </td>
                        <td class="px-6 py-4">
                            <?php 
                            $statusType = 'warning';
                            if ($review['status'] === 'Approved') $statusType = 'success';
                            ?>
                            <?php component('badges', ['type' => $statusType, 'text' => $review['status']]); ?>
                        </td>
                        <!-- Actions -->
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end space-x-2">
                                <?php if ($review['status'] !== 'Approved'): ?>
                                    <button class="px-2.5 py-1.5 bg-primary hover:bg-primary/95 text-white font-semibold rounded-lg text-[10px] shadow-sm transition-all">
                                        Approve
                                    </button>
                                <?php endif; ?>
                                <button class="px-2.5 py-1.5 border border-slate-200 bg-white hover:bg-red-50 text-red-600 hover:border-red-200 font-semibold rounded-lg text-[10px] transition-all">
                                    Flag / Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

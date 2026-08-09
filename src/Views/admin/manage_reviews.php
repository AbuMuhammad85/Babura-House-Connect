<?php
use App\Helpers\Flash;
?>

<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Manage Reviews</h2>
            <p class="text-xs text-text-muted mt-1">Moderate tenant reviews left on landlord properties in Babura.</p>
        </div>
        
        <!-- Filters Form -->
        <form action="<?= url('/admin/manage-reviews') ?>" method="GET" class="flex items-center space-x-2">
            <select name="rating" onchange="this.form.submit()" class="px-3 py-1.5 text-xs border border-slate-200 focus:outline-none rounded-lg text-slate-800 bg-white">
                <option value="">All Ratings</option>
                <option value="5" <?= ($filters['rating'] ?? '') === '5' ? 'selected' : '' ?>>5 Stars</option>
                <option value="4" <?= ($filters['rating'] ?? '') === '4' ? 'selected' : '' ?>>4 Stars</option>
                <option value="3" <?= ($filters['rating'] ?? '') === '3' ? 'selected' : '' ?>>3 Stars</option>
                <option value="2" <?= ($filters['rating'] ?? '') === '2' ? 'selected' : '' ?>>2 Stars</option>
                <option value="1" <?= ($filters['rating'] ?? '') === '1' ? 'selected' : '' ?>>1 Star</option>
            </select>
            <select name="status" onchange="this.form.submit()" class="px-3 py-1.5 text-xs border border-slate-200 focus:outline-none rounded-lg text-slate-800 bg-white">
                <option value="">All Statuses</option>
                <option value="published" <?= ($filters['status'] ?? '') === 'published' ? 'selected' : '' ?>>Published</option>
                <option value="hidden" <?= ($filters['status'] ?? '') === 'hidden' ? 'selected' : '' ?>>Hidden</option>
            </select>
        </form>
    </div>

    <?php if (Flash::has('error')): ?>
        <?php component('alerts', ['type' => 'error', 'message' => Flash::get('error')]); ?>
    <?php endif; ?>
    <?php if (Flash::has('success')): ?>
        <?php component('alerts', ['type' => 'success', 'message' => Flash::get('success')]); ?>
    <?php endif; ?>

    <!-- Reviews Moderation Table -->
    <div class="overflow-x-auto bg-white border border-slate-100 rounded-2xl shadow-sm">
        <?php if (empty($reviews)): ?>
            <div class="p-8 text-center text-xs text-text-muted">
                <i class="fa-regular fa-star text-2xl text-slate-300 mb-2 block"></i>
                <p>No reviews found matching the filters.</p>
            </div>
        <?php else: ?>
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
                                <span class="font-bold text-slate-850 block"><?= htmlspecialchars($review['user']) ?></span>
                                <span class="text-[10px] text-text-muted mt-0.5 block">On: <?= htmlspecialchars($review['house']) ?></span>
                            </td>
                            <td class="px-6 py-4 space-y-2 max-w-sm">
                                <!-- Stars -->
                                <div class="flex items-center space-x-0.5 text-accent text-[10px]">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <i class="fa-<?= $i <= $review['rating'] ? 'solid' : 'regular' ?> fa-star"></i>
                                    <?php endfor; ?>
                                </div>
                                <p class="text-[11px] text-slate-650 italic leading-relaxed">&ldquo;<?= htmlspecialchars($review['comment']) ?>&rdquo;</p>
                            </td>
                            <td class="px-6 py-4">
                                <?php 
                                $statusType = 'warning';
                                if ($review['status'] === 'published') $statusType = 'success';
                                if ($review['status'] === 'hidden') $statusType = 'neutral';
                                ?>
                                <?php component('badges', ['type' => $statusType, 'text' => ucfirst($review['status'])]); ?>
                            </td>
                            <!-- Actions -->
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end space-x-2">
                                    <?php if ($review['status'] === 'hidden'): ?>
                                        <form action="<?= url('/admin/reviews/publish') ?>" method="POST" class="inline">
                                            <?= \App\Helpers\CSRF::field() ?>
                                            <input type="hidden" name="id" value="<?= $review['id'] ?>">
                                            <button type="submit" class="px-2.5 py-1.5 bg-primary hover:bg-primary/95 text-white font-semibold rounded-lg text-[10px] shadow-sm transition-all">
                                                Approve / Publish
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <form action="<?= url('/admin/reviews/hide') ?>" method="POST" class="inline">
                                            <?= \App\Helpers\CSRF::field() ?>
                                            <input type="hidden" name="id" value="<?= $review['id'] ?>">
                                            <button type="submit" class="px-2.5 py-1.5 border border-slate-200 bg-white hover:bg-red-50 text-red-650 hover:border-red-200 font-semibold rounded-lg text-[10px] transition-all">
                                                Hide Review
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <!-- Pagination component call -->
            <?php 
            $basePageUrl = '/admin/manage-reviews';
            $queryParams = [];
            if (!empty($filters['rating'])) $queryParams['rating'] = $filters['rating'];
            if (!empty($filters['status'])) $queryParams['status'] = $filters['status'];
            if (!empty($queryParams)) {
                $basePageUrl .= '?' . http_build_query($queryParams);
            }
            ?>
            <?php component('pagination', [
                'currentPage' => $currentPage,
                'totalPages' => $totalPages,
                'baseUrl' => url($basePageUrl)
            ]); ?>
        <?php endif; ?>
    </div>
</div>

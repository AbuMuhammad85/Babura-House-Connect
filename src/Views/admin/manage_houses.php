<?php
use App\Helpers\Flash;
?>

<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Manage Listings</h2>
            <p class="text-xs text-text-muted mt-1">Approve, reject, or remove housing listings from the public marketplace.</p>
        </div>
        
        <!-- Filters Form -->
        <form action="<?= url('/admin/manage-houses') ?>" method="GET" class="flex flex-wrap items-center gap-2">
            <select name="status" onchange="this.form.submit()" class="px-3 py-1.5 text-xs border border-slate-200 focus:outline-none rounded-lg text-slate-800 bg-white">
                <option value="">All Statuses</option>
                <option value="pending_approval" <?= ($filters['status'] ?? '') === 'pending_approval' ? 'selected' : '' ?>>Pending Review</option>
                <option value="published" <?= ($filters['status'] ?? '') === 'published' ? 'selected' : '' ?>>Approved</option>
                <option value="rejected" <?= ($filters['status'] ?? '') === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                <option value="archived" <?= ($filters['status'] ?? '') === 'archived' ? 'selected' : '' ?>>Deactivated</option>
            </select>

            <select name="area_id" onchange="this.form.submit()" class="px-3 py-1.5 text-xs border border-slate-200 focus:outline-none rounded-lg text-slate-800 bg-white">
                <option value="">All Areas</option>
                <?php foreach ($areas as $area): ?>
                    <option value="<?= $area['id'] ?>" <?= (int)($filters['area_id'] ?? 0) === (int)$area['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($area['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>
    </div>

    <?php if (Flash::has('error')): ?>
        <?php component('alerts', ['type' => 'error', 'message' => Flash::get('error')]); ?>
    <?php endif; ?>
    <?php if (Flash::has('success')): ?>
        <?php component('alerts', ['type' => 'success', 'message' => Flash::get('success')]); ?>
    <?php endif; ?>

    <!-- Listings Table Container -->
    <div class="overflow-x-auto bg-white border border-slate-100 rounded-2xl shadow-sm">
        <?php if (empty($houses)): ?>
            <div class="p-8 text-center text-xs text-text-muted">
                <i class="fa-solid fa-house-circle-exclamation text-2xl text-slate-300 mb-2"></i>
                <p>No house listings found matching the filters.</p>
            </div>
        <?php else: ?>
            <table class="min-w-full divide-y divide-slate-100 text-left">
                <thead class="bg-slate-50 text-[10px] font-bold uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-6 py-3">Property Title</th>
                        <th class="px-6 py-3">Listed By Landlord</th>
                        <th class="px-6 py-3">Price</th>
                        <th class="px-6 py-3">Date Registered</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white text-xs text-slate-700">
                    <?php foreach ($houses as $house): ?>
                        <tr>
                            <td class="px-6 py-4">
                                <span class="font-bold text-slate-850 block"><?= htmlspecialchars($house['title']) ?></span>
                                <span class="text-[10px] text-text-muted mt-0.5 block flex items-center"><i class="fa-solid fa-location-dot mr-1"></i> <?= htmlspecialchars($house['area_name']) ?> (ID: #HC-<?= $house['id'] ?>)</span>
                            </td>
                            <td class="px-6 py-4 font-semibold text-slate-700">
                                <?= htmlspecialchars($house['landlord_name']) ?>
                            </td>
                            <td class="px-6 py-4 font-bold text-slate-800">
                                ₦<?= number_format($house['rent_amount']) ?> <span class="text-[10px] text-slate-400 font-normal">/ <?= $house['rent_period'] ?></span>
                            </td>
                            <td class="px-6 py-4 text-text-muted">
                                <?= date('M d, Y', strtotime($house['created_at'])) ?>
                            </td>
                            <td class="px-6 py-4">
                                <?php 
                                $statusType = 'warning';
                                $statusLabel = $house['status'];
                                if ($house['status'] === 'published') {
                                    $statusType = 'success';
                                    $statusLabel = 'Approved';
                                } elseif ($house['status'] === 'pending_approval') {
                                    $statusType = 'warning';
                                    $statusLabel = 'Pending Review';
                                } elseif ($house['status'] === 'rejected') {
                                    $statusType = 'danger';
                                    $statusLabel = 'Rejected';
                                } elseif ($house['status'] === 'archived') {
                                    $statusType = 'neutral';
                                    $statusLabel = 'Deactivated';
                                }
                                ?>
                                <?php component('badges', ['type' => $statusType, 'text' => $statusLabel]); ?>
                            </td>
                            
                            <!-- Action buttons with CSRF POST forms -->
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end space-x-2">
                                    <?php if ($house['status'] === 'pending_approval' || $house['status'] === 'rejected'): ?>
                                        <form action="<?= url('/admin/houses/approve') ?>" method="POST" class="inline">
                                            <?= \App\Helpers\CSRF::field() ?>
                                            <input type="hidden" name="id" value="<?= $house['id'] ?>">
                                            <button type="submit" class="px-2.5 py-1.5 bg-primary hover:bg-primary/95 text-white font-semibold rounded-lg text-[10px] shadow-sm transition-all">
                                                Approve
                                            </button>
                                        </form>
                                    <?php endif; ?>
 
                                    <?php if ($house['status'] === 'pending_approval'): ?>
                                        <form action="<?= url('/admin/houses/reject') ?>" method="POST" class="inline-flex items-center space-x-1">
                                            <?= \App\Helpers\CSRF::field() ?>
                                            <input type="hidden" name="id" value="<?= $house['id'] ?>">
                                            <input type="text" name="notes" placeholder="Rejection reason..." required class="px-2 py-1 text-[10px] border border-slate-200 rounded-lg focus:outline-none focus:border-primary max-w-[120px] bg-white">
                                            <button type="submit" class="px-2 py-1.5 bg-red-50 hover:bg-red-100 text-red-650 font-semibold rounded-lg text-[10px] transition-all">
                                                Reject
                                            </button>
                                        </form>
                                    <?php endif; ?>
 
                                    <a href="<?= url('/house/' . $house['id']) ?>" target="_blank" class="px-2.5 py-1.5 border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-semibold rounded-lg text-[10px] transition-all">
                                        Preview
                                    </a>
 
                                    <?php if ($house['status'] !== 'archived'): ?>
                                        <form action="<?= url('/admin/houses/deactivate') ?>" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to deactivate this listing?');">
                                            <?= \App\Helpers\CSRF::field() ?>
                                            <input type="hidden" name="id" value="<?= $house['id'] ?>">
                                            <button type="submit" class="p-1.5 border border-slate-200 bg-white hover:bg-red-50 text-slate-400 hover:text-red-650 hover:border-red-200 rounded-lg transition-colors flex items-center justify-center" title="Deactivate Listing">
                                                <i class="fa-regular fa-trash-can text-xs"></i>
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
            $basePageUrl = '/admin/manage-houses';
            $queryParams = [];
            if (!empty($filters['status'])) $queryParams['status'] = $filters['status'];
            if (!empty($filters['area_id'])) $queryParams['area_id'] = $filters['area_id'];
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

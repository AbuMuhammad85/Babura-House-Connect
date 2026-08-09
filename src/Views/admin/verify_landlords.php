<?php
use App\Helpers\Flash;
?>

<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Verify Landlord Documents</h2>
            <p class="text-xs text-text-muted mt-1">Review government IDs and local land allocation records to certify landlords.</p>
        </div>
        
        <!-- Filters Form -->
        <form action="<?= url('/admin/verify-landlords') ?>" method="GET" class="flex items-center space-x-2">
            <select name="verification_status" onchange="this.form.submit()" class="px-3 py-1.5 text-xs border border-slate-200 focus:outline-none rounded-lg text-slate-800 bg-white">
                <option value="">All Statuses</option>
                <option value="pending" <?= ($filters['verification_status'] ?? '') === 'pending' ? 'selected' : '' ?>>Pending</option>
                <option value="approved" <?= ($filters['verification_status'] ?? '') === 'approved' ? 'selected' : '' ?>>Approved</option>
                <option value="rejected" <?= ($filters['verification_status'] ?? '') === 'rejected' ? 'selected' : '' ?>>Rejected</option>
            </select>
        </form>
    </div>

    <?php if (Flash::has('error')): ?>
        <?php component('alerts', ['type' => 'error', 'message' => Flash::get('error')]); ?>
    <?php endif; ?>
    <?php if (Flash::has('success')): ?>
        <?php component('alerts', ['type' => 'success', 'message' => Flash::get('success')]); ?>
    <?php endif; ?>

    <!-- Verification Table Grid -->
    <div class="overflow-x-auto bg-white border border-slate-100 rounded-2xl shadow-sm">
        <?php if (empty($landlords)): ?>
            <div class="p-8 text-center text-xs text-text-muted">
                <i class="fa-solid fa-circle-check text-2xl text-green-500 mb-2"></i>
                <p>No landlord verifications found matching the filter.</p>
            </div>
        <?php else: ?>
            <table class="min-w-full divide-y divide-slate-100 text-left">
                <thead class="bg-slate-50 text-[10px] font-bold uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-6 py-3">Landlord Name</th>
                        <th class="px-6 py-3">Contact Info</th>
                        <th class="px-6 py-3">Registration Date</th>
                        <th class="px-6 py-3">Files Review</th>
                        <th class="px-6 py-3">NIN Last 4</th>
                        <th class="px-6 py-3">Active Listings</th>
                        <th class="px-6 py-3">Verification Status</th>
                        <th class="px-6 py-3">Account Status</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white text-xs text-slate-700">
                    <?php foreach ($landlords as $landlord): ?>
                        <tr>
                            <td class="px-6 py-4 font-bold text-slate-850">
                                <?= htmlspecialchars($landlord['landlord_name']) ?>
                            </td>
                            <td class="px-6 py-4 space-y-0.5 text-text-muted">
                                <p><?= htmlspecialchars($landlord['landlord_email']) ?></p>
                                <p class="text-[10px]"><?= htmlspecialchars($landlord['landlord_phone']) ?></p>
                            </td>
                            <td class="px-6 py-4 text-text-muted">
                                <?= htmlspecialchars(date('M d, Y', strtotime($landlord['registered_at']))) ?>
                            </td>
                            
                            <!-- Secure serving document routes -->
                            <td class="px-6 py-4 space-x-2">
                                <?php if ($landlord['verification_id']): ?>
                                    <a href="<?= url('/admin/verifications/file/' . $landlord['verification_id'] . '/id') ?>" target="_blank" class="px-2.5 py-1 bg-slate-50 hover:bg-slate-100 text-slate-700 border border-slate-200 rounded-lg font-medium text-[10px] flex items-center inline-flex space-x-1">
                                        <i class="fa-solid fa-file-pdf text-red-500"></i>
                                        <span><?= htmlspecialchars($landlord['id_type'] ?: 'ID Card') ?></span>
                                    </a>
                                    <a href="<?= url('/admin/verifications/file/' . $landlord['verification_id'] . '/ownership') ?>" target="_blank" class="px-2.5 py-1 bg-slate-50 hover:bg-slate-100 text-slate-700 border border-slate-200 rounded-lg font-medium text-[10px] flex items-center inline-flex space-x-1">
                                        <i class="fa-solid fa-file-invoice text-blue-500"></i>
                                        <span>Ownership</span>
                                    </a>
                                    <a href="<?= url('/admin/verifications/file/' . $landlord['verification_id'] . '/photo') ?>" target="_blank" class="px-2.5 py-1 bg-slate-50 hover:bg-slate-100 text-slate-700 border border-slate-200 rounded-lg font-medium text-[10px] flex items-center inline-flex space-x-1">
                                        <i class="fa-solid fa-image text-green-500"></i>
                                        <span>Selfie</span>
                                    </a>
                                <?php else: ?>
                                    <span class="text-slate-400">[No Uploads]</span>
                                <?php endif; ?>
                            </td>
                            
                            <td class="px-6 py-4 font-mono text-slate-600 font-bold">
                                <?= $landlord['nin_last4'] ? '****-****-' . htmlspecialchars($landlord['nin_last4']) : '<span class="text-slate-400 font-normal">-</span>' ?>
                            </td>

                            <td class="px-6 py-4 font-bold text-center">
                                <?= (int)$landlord['active_listings_count'] ?>
                            </td>
                            
                            <td class="px-6 py-4">
                                <?php 
                                $statusType = 'warning';
                                if ($landlord['verification_status'] === 'approved') $statusType = 'success';
                                if ($landlord['verification_status'] === 'rejected') $statusType = 'danger';
                                ?>
                                <?php component('badges', ['type' => $statusType, 'text' => ucfirst($landlord['verification_status'])]); ?>
                            </td>

                            <td class="px-6 py-4">
                                <?php $accType = $landlord['account_status'] === 'active' ? 'success' : 'danger'; ?>
                                <?php component('badges', ['type' => $accType, 'text' => ucfirst($landlord['account_status'])]); ?>
                            </td>
                            
                            <!-- Action buttons utilizing POST forms with CSRF protection -->
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end space-x-2">
                                    <?php if ($landlord['verification_status'] === 'pending' && $landlord['verification_id']): ?>
                                        <form action="<?= url('/admin/verifications/approve') ?>" method="POST" class="inline">
                                            <?= \App\Helpers\CSRF::field() ?>
                                            <input type="hidden" name="id" value="<?= $landlord['verification_id'] ?>">
                                            <button type="submit" class="px-3 py-1.5 bg-primary hover:bg-primary/95 text-white font-semibold rounded-lg text-[10px] shadow-sm transition-all">
                                                Approve
                                            </button>
                                        </form>
                                        
                                        <form action="<?= url('/admin/verifications/reject') ?>" method="POST" class="inline-flex items-center space-x-1">
                                            <?= \App\Helpers\CSRF::field() ?>
                                            <input type="hidden" name="id" value="<?= $landlord['verification_id'] ?>">
                                            <input type="text" name="notes" placeholder="Reason..." required class="px-2 py-1 text-[10px] border border-slate-200 rounded-lg focus:outline-none focus:border-primary max-w-[120px] bg-white">
                                            <button type="submit" class="px-2.5 py-1.5 bg-red-50 hover:bg-red-100 text-red-650 font-semibold rounded-lg text-[10px] transition-all">
                                                Reject
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <span class="text-slate-400 text-[10px]">No Actions</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- Pagination component call -->
            <?php 
            $basePageUrl = '/admin/verify-landlords';
            if (!empty($filters['verification_status'])) {
                $basePageUrl .= '?verification_status=' . $filters['verification_status'];
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

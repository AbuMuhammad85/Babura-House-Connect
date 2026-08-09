<?php
use App\Helpers\Flash;
?>

<div class="space-y-6">
    <div>
        <h2 class="text-xl font-bold text-slate-800">Rental Inquiries</h2>
        <p class="text-xs text-text-muted mt-1">Manage connection requests submitted by tenants interested in your properties.</p>
    </div>

    <?php if (Flash::has('error')): ?>
        <?php component('alerts', ['type' => 'error', 'message' => Flash::get('error')]); ?>
    <?php endif; ?>
    <?php if (Flash::has('success')): ?>
        <?php component('alerts', ['type' => 'success', 'message' => Flash::get('success')]); ?>
    <?php endif; ?>

    <!-- Inquiries Table -->
    <div class="overflow-x-auto bg-white border border-slate-100 rounded-2xl shadow-sm">
        <?php if (empty($inquiries)): ?>
            <div class="p-8 text-center text-xs text-text-muted">
                <i class="fa-solid fa-paper-plane text-2xl text-slate-300 mb-2"></i>
                <p>No inquiries received yet.</p>
            </div>
        <?php else: ?>
            <table class="min-w-full divide-y divide-slate-100 text-left">
                <thead class="bg-slate-50 text-[10px] font-bold uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-6 py-3">Property</th>
                        <th class="px-6 py-3">Tenant Name</th>
                        <th class="px-6 py-3">Contact</th>
                        <th class="px-6 py-3">Message</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white text-xs text-slate-700">
                    <?php foreach ($inquiries as $inquiry): ?>
                        <tr>
                            <td class="px-6 py-4 font-bold text-slate-850">
                                <?= htmlspecialchars($inquiry['house_title']) ?>
                            </td>
                            <td class="px-6 py-4 font-semibold text-slate-700">
                                <?= htmlspecialchars($inquiry['tenant_name']) ?>
                            </td>
                            <td class="px-6 py-4 space-y-0.5 text-[11px] text-text-muted">
                                <div class="flex items-center"><i class="fa-solid fa-phone mr-1"></i> <?= htmlspecialchars($inquiry['tenant_phone']) ?></div>
                                <div class="flex items-center"><i class="fa-solid fa-envelope mr-1"></i> <?= htmlspecialchars($inquiry['tenant_email']) ?></div>
                            </td>
                            <td class="px-6 py-4 max-w-xs truncate text-slate-650" title="<?= htmlspecialchars($inquiry['message']) ?>">
                                <?= htmlspecialchars($inquiry['message']) ?>
                            </td>
                            <td class="px-6 py-4">
                                <?php 
                                $statusType = 'warning';
                                if ($inquiry['status'] === 'contacted') $statusType = 'success';
                                if ($inquiry['status'] === 'closed') $statusType = 'neutral';
                                ?>
                                <?php component('badges', ['type' => $statusType, 'text' => ucfirst($inquiry['status'])]); ?>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <form action="<?= url('/landlord/inquiries/update') ?>" method="POST" class="inline-flex items-center space-x-1">
                                    <?= \App\Helpers\CSRF::field() ?>
                                    <input type="hidden" name="id" value="<?= $inquiry['id'] ?>">
                                    <select name="status" class="px-2 py-1 text-[10px] border border-slate-200 rounded-lg focus:outline-none focus:border-primary bg-white">
                                        <option value="pending" <?= $inquiry['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                                        <option value="contacted" <?= $inquiry['status'] === 'contacted' ? 'selected' : '' ?>>Contacted</option>
                                        <option value="closed" <?= $inquiry['status'] === 'closed' ? 'selected' : '' ?>>Closed</option>
                                    </select>
                                    <button type="submit" class="px-2 py-1 bg-slate-800 hover:bg-slate-900 text-white rounded-lg text-[10px] font-semibold transition-all">
                                        Update
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

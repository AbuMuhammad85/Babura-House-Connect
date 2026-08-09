<?php
use App\Helpers\Flash;
?>

<div class="space-y-6">
    <div>
        <h2 class="text-xl font-bold text-slate-800">My Rental Inquiries</h2>
        <p class="text-xs text-text-muted mt-1">Track connection requests and responses from property landlords.</p>
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
            <?php component('empty_states', [
                'title' => 'No Inquiries Sent Yet',
                'message' => 'View property listings and click "Send Inquiry" to connect with landlords.',
                'icon' => 'fa-paper-plane',
                'actionUrl' => '/browse',
                'actionText' => 'Browse Houses'
            ]); ?>
        <?php else: ?>
            <table class="min-w-full divide-y divide-slate-100 text-left">
                <thead class="bg-slate-50 text-[10px] font-bold uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-6 py-3">Property Name</th>
                        <th class="px-6 py-3">Landlord</th>
                        <th class="px-6 py-3">Message</th>
                        <th class="px-6 py-3">Date</th>
                        <th class="px-6 py-3">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white text-xs text-slate-700">
                    <?php foreach ($inquiries as $inquiry): ?>
                        <tr>
                            <td class="px-6 py-4 font-bold text-slate-850">
                                <a href="<?= url('/house/' . $inquiry['house_id']) ?>" class="hover:text-primary transition-colors">
                                    <?= htmlspecialchars($inquiry['house_title']) ?>
                                </a>
                            </td>
                            <td class="px-6 py-4 font-semibold text-slate-700">
                                <?= htmlspecialchars($inquiry['landlord_name']) ?>
                            </td>
                            <td class="px-6 py-4 max-w-xs truncate text-text-muted">
                                <?= htmlspecialchars($inquiry['message']) ?>
                            </td>
                            <td class="px-6 py-4">
                                <?= date('M d, Y', strtotime($inquiry['created_at'])) ?>
                            </td>
                            <td class="px-6 py-4">
                                <?php 
                                $statusType = 'warning';
                                if ($inquiry['status'] === 'contacted') $statusType = 'success';
                                if ($inquiry['status'] === 'closed') $statusType = 'neutral';
                                ?>
                                <?php component('badges', ['type' => $statusType, 'text' => ucfirst($inquiry['status'])]); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

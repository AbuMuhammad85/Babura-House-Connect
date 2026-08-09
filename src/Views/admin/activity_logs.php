<?php
use App\Helpers\Flash;
?>

<div class="space-y-6">
    <div>
        <h2 class="text-xl font-bold text-slate-800">System Activity & Audit Logs</h2>
        <p class="text-xs text-text-muted mt-1">Review operational actions, log-ins, edits, and administrative overrides.</p>
    </div>

    <!-- Logs Table Container -->
    <div class="overflow-x-auto bg-white border border-slate-100 rounded-2xl shadow-sm">
        <?php if (empty($logs)): ?>
            <div class="p-8 text-center text-xs text-text-muted">
                <i class="fa-solid fa-clock-rotate-left text-2xl text-slate-350 mb-2"></i>
                <p>No activity logs found matching the current page.</p>
            </div>
        <?php else: ?>
            <table class="min-w-full divide-y divide-slate-100 text-left">
                <thead class="bg-slate-50 text-[10px] font-bold uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-6 py-3">User Profile</th>
                        <th class="px-6 py-3">Role</th>
                        <th class="px-6 py-3">Action Type</th>
                        <th class="px-6 py-3">Description</th>
                        <th class="px-6 py-3">IP Address</th>
                        <th class="px-6 py-3">Date / Time</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white text-xs text-slate-700">
                    <?php foreach ($logs as $log): ?>
                        <tr>
                            <td class="px-6 py-4 flex items-center space-x-3">
                                <span class="w-7 h-7 rounded-lg bg-slate-100 text-slate-500 font-bold flex items-center justify-center text-[10px] uppercase shrink-0">
                                    <?= substr($log['user_name'] ?? 'G', 0, 2) ?>
                                </span>
                                <span class="font-bold text-slate-850"><?= htmlspecialchars($log['user_name'] ?? 'System/Guest') ?></span>
                            </td>
                            <td class="px-6 py-4">
                                <?php if ($log['role']): ?>
                                    <?php 
                                    $roleType = 'info';
                                    if ($log['role'] === 'landlord') $roleType = 'success';
                                    if ($log['role'] === 'admin') $roleType = 'danger';
                                    ?>
                                    <?php component('badges', ['type' => $roleType, 'text' => ucfirst($log['role'])]); ?>
                                <?php else: ?>
                                    <span class="text-slate-400">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-mono bg-slate-100 px-1.5 py-0.5 rounded text-[10px] text-slate-700">
                                    <?= htmlspecialchars($log['action']) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-text-muted">
                                <?= htmlspecialchars($log['description']) ?>
                            </td>
                            <td class="px-6 py-4 font-mono text-slate-650">
                                <?= htmlspecialchars($log['ip_address']) ?>
                            </td>
                            <td class="px-6 py-4 text-text-muted">
                                <?= date('M d, Y H:i:s', strtotime($log['created_at'])) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- Pagination component call -->
            <?php component('pagination', [
                'currentPage' => $currentPage,
                'totalPages' => $totalPages,
                'baseUrl' => url('/admin/activity-logs')
            ]); ?>
        <?php endif; ?>
    </div>
</div>

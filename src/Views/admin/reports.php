<?php
use App\Helpers\Flash;
?>

<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-800">System Reports & Complaints</h2>
            <p class="text-xs text-text-muted mt-1">Review user-submitted complaints, suspicious property listings, and export transaction histories.</p>
        </div>
        
        <!-- Filters Form -->
        <form action="<?= url('/admin/reports') ?>" method="GET" class="flex items-center space-x-2">
            <select name="status" onchange="this.form.submit()" class="px-3 py-1.5 text-xs border border-slate-200 focus:outline-none rounded-lg text-slate-800 bg-white">
                <option value="">All Statuses</option>
                <option value="pending" <?= ($filters['status'] ?? '') === 'pending' ? 'selected' : '' ?>>Pending</option>
                <option value="investigating" <?= ($filters['status'] ?? '') === 'investigating' ? 'selected' : '' ?>>Investigating</option>
                <option value="resolved" <?= ($filters['status'] ?? '') === 'resolved' ? 'selected' : '' ?>>Resolved</option>
                <option value="dismissed" <?= ($filters['status'] ?? '') === 'dismissed' ? 'selected' : '' ?>>Dismissed</option>
            </select>
        </form>
    </div>

    <?php if (Flash::has('error')): ?>
        <?php component('alerts', ['type' => 'error', 'message' => Flash::get('error')]); ?>
    <?php endif; ?>
    <?php if (Flash::has('success')): ?>
        <?php component('alerts', ['type' => 'success', 'message' => Flash::get('success')]); ?>
    <?php endif; ?>

    <!-- Complains Table -->
    <div class="bg-white border border-slate-100 rounded-2xl shadow-sm overflow-hidden p-6 space-y-4">
        <h3 class="font-bold text-slate-800 text-sm">User Complaints Registry</h3>
        <div class="overflow-x-auto">
            <?php if (empty($reports)): ?>
                <div class="p-8 text-center text-xs text-text-muted bg-slate-50/50 rounded-xl">
                    <i class="fa-solid fa-circle-check text-2xl text-slate-300 mb-2"></i>
                    <p>No complaints found matching the filters.</p>
                </div>
            <?php else: ?>
                <table class="min-w-full divide-y divide-slate-100 text-left">
                    <thead class="bg-slate-50 text-[10px] font-bold uppercase tracking-wider text-slate-500">
                        <tr>
                            <th class="px-6 py-3">Property</th>
                            <th class="px-6 py-3">Reporter</th>
                            <th class="px-6 py-3">Complaint / Reason</th>
                            <th class="px-6 py-3">Description</th>
                            <th class="px-6 py-3">Date</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white text-xs text-slate-700">
                        <?php foreach ($reports as $report): ?>
                            <tr>
                                <td class="px-6 py-4 font-bold text-slate-850">
                                    <?php if ($report['house_title']): ?>
                                        <a href="<?= url('/house/' . $report['house_id']) ?>" target="_blank" class="hover:text-primary transition-colors">
                                            <?= htmlspecialchars($report['house_title']) ?>
                                        </a>
                                    <?php else: ?>
                                        <span class="text-slate-400">[Deleted Listing]</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 font-semibold text-slate-700">
                                    <?= htmlspecialchars($report['reporter_name'] ?? 'Anonymous') ?>
                                </td>
                                <td class="px-6 py-4 font-semibold">
                                    <span class="px-2 py-0.5 rounded text-[10px] bg-red-50 text-red-655 border border-red-100">
                                        <?= htmlspecialchars(ucfirst($report['reason'])) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 max-w-xs truncate text-text-muted" title="<?= htmlspecialchars($report['description']) ?>">
                                    <?= htmlspecialchars($report['description']) ?>
                                </td>
                                <td class="px-6 py-4 text-text-muted">
                                    <?= date('M d, Y', strtotime($report['created_at'])) ?>
                                </td>
                                <td class="px-6 py-4">
                                    <?php 
                                    $statusType = 'warning';
                                    if ($report['status'] === 'resolved') $statusType = 'success';
                                    if ($report['status'] === 'dismissed') $statusType = 'neutral';
                                    if ($report['status'] === 'investigating') $statusType = 'info';
                                    ?>
                                    <?php component('badges', ['type' => $statusType, 'text' => ucfirst($report['status'])]); ?>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <form action="<?= url('/admin/reports/update') ?>" method="POST" class="inline-flex items-center space-x-1">
                                        <?= \App\Helpers\CSRF::field() ?>
                                        <input type="hidden" name="id" value="<?= $report['id'] ?>">
                                        <select name="status" class="px-2 py-1 text-[10px] border border-slate-200 rounded-lg focus:outline-none focus:border-primary bg-white text-slate-700">
                                            <option value="pending" <?= $report['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                                            <option value="investigating" <?= $report['status'] === 'investigating' ? 'selected' : '' ?>>Investigating</option>
                                            <option value="resolved" <?= $report['status'] === 'resolved' ? 'selected' : '' ?>>Resolved</option>
                                            <option value="dismissed" <?= $report['status'] === 'dismissed' ? 'selected' : '' ?>>Dismissed</option>
                                        </select>
                                        <button type="submit" class="px-2.5 py-1 bg-slate-900 hover:bg-slate-800 text-white rounded-lg text-[10px] font-semibold transition-all">
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

        <!-- Pagination component call -->
        <?php if (!empty($reports)): ?>
            <?php 
            $basePageUrl = '/admin/reports';
            if (!empty($filters['status'])) {
                $basePageUrl .= '?status=' . $filters['status'];
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

<?php
use App\Helpers\Flash;
?>

<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Manage Users</h2>
            <p class="text-xs text-text-muted mt-1">Review, activate, suspend, or manage platform tenant and landlord accounts.</p>
        </div>
        
        <!-- Filters Form -->
        <form action="<?= url('/admin/manage-users') ?>" method="GET" class="flex items-center space-x-2">
            <select name="role" onchange="this.form.submit()" class="px-3 py-1.5 text-xs border border-slate-200 focus:outline-none rounded-lg text-slate-800 bg-white">
                <option value="">All Roles</option>
                <option value="tenant" <?= ($filters['role'] ?? '') === 'tenant' ? 'selected' : '' ?>>Tenant</option>
                <option value="landlord" <?= ($filters['role'] ?? '') === 'landlord' ? 'selected' : '' ?>>Landlord</option>
                <option value="admin" <?= ($filters['role'] ?? '') === 'admin' ? 'selected' : '' ?>>Admin</option>
            </select>
            <select name="status" onchange="this.form.submit()" class="px-3 py-1.5 text-xs border border-slate-200 focus:outline-none rounded-lg text-slate-800 bg-white">
                <option value="">All Statuses</option>
                <option value="active" <?= ($filters['status'] ?? '') === 'active' ? 'selected' : '' ?>>Active</option>
                <option value="suspended" <?= ($filters['status'] ?? '') === 'suspended' ? 'selected' : '' ?>>Suspended</option>
            </select>
        </form>
    </div>

    <?php if (Flash::has('error')): ?>
        <?php component('alerts', ['type' => 'error', 'message' => Flash::get('error')]); ?>
    <?php endif; ?>
    <?php if (Flash::has('success')): ?>
        <?php component('alerts', ['type' => 'success', 'message' => Flash::get('success')]); ?>
    <?php endif; ?>

    <!-- Users Table Container -->
    <div class="overflow-x-auto bg-white border border-slate-100 rounded-2xl shadow-sm">
        <table class="min-w-full divide-y divide-slate-100 text-left">
            <thead class="bg-slate-50 text-[10px] font-bold uppercase tracking-wider text-slate-500">
                <tr>
                    <th class="px-6 py-3">User Profile Name</th>
                    <th class="px-6 py-3">Email Address</th>
                    <th class="px-6 py-3">Phone</th>
                    <th class="px-6 py-3">Account Role</th>
                    <th class="px-6 py-3">Registered On</th>
                    <th class="px-6 py-3">Last Log-In</th>
                    <th class="px-6 py-3">Status</th>
                    <th class="px-6 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 bg-white text-xs text-slate-700">
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td class="px-6 py-4 flex items-center space-x-3">
                            <span class="w-8 h-8 rounded-lg bg-slate-100 text-slate-500 font-bold flex items-center justify-center text-xs uppercase shrink-0">
                                <?= substr($user['full_name'] ?? 'U', 0, 2) ?>
                            </span>
                            <span class="font-bold text-slate-850"><?= htmlspecialchars($user['full_name']) ?></span>
                        </td>
                        <td class="px-6 py-4 text-text-muted">
                            <?= htmlspecialchars($user['email'] ?? '[No Email]') ?>
                        </td>
                        <td class="px-6 py-4 text-text-muted">
                            <?= htmlspecialchars($user['phone']) ?>
                        </td>
                        <td class="px-6 py-4">
                            <?php 
                            $roleType = 'info';
                            if ($user['role'] === 'landlord') $roleType = 'success';
                            if ($user['role'] === 'admin') $roleType = 'danger';
                            ?>
                            <?php component('badges', ['type' => $roleType, 'text' => ucfirst($user['role'])]); ?>
                        </td>
                        <td class="px-6 py-4 text-text-muted">
                            <?= date('M d, Y', strtotime($user['created_at'])) ?>
                        </td>
                        <td class="px-6 py-4 text-text-muted">
                            <?= $user['last_login'] ? date('M d, Y H:i', strtotime($user['last_login'])) : '<span class="text-slate-400">Never</span>' ?>
                        </td>
                        <td class="px-6 py-4">
                            <?php 
                            $statusType = $user['status'] === 'active' ? 'success' : 'danger';
                            ?>
                            <?php component('badges', ['type' => $statusType, 'text' => ucfirst($user['status'])]); ?>
                        </td>
                        <!-- Actions -->
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end space-x-2">
                                <a href="<?= url('/admin/tenants/view/' . $user['id']) ?>" class="px-2.5 py-1.5 border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-semibold rounded-lg text-[10px] transition-all">
                                    View
                                </a>
                                
                                <?php if ($user['status'] === 'active'): ?>
                                    <form action="<?= url('/admin/users/suspend') ?>" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to suspend this account?');">
                                        <?= \App\Helpers\CSRF::field() ?>
                                        <input type="hidden" name="id" value="<?= $user['id'] ?>">
                                        <button type="submit" class="px-2.5 py-1.5 border border-slate-200 bg-white hover:bg-red-50 text-red-650 hover:border-red-200 font-semibold rounded-lg text-[10px] transition-all">
                                            Suspend
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <form action="<?= url('/admin/users/activate') ?>" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to activate this account?');">
                                        <?= \App\Helpers\CSRF::field() ?>
                                        <input type="hidden" name="id" value="<?= $user['id'] ?>">
                                        <button type="submit" class="px-2.5 py-1.5 border border-slate-200 bg-white hover:bg-green-50 text-green-700 hover:border-green-200 font-semibold rounded-lg text-[10px] transition-all">
                                            Activate
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
        $basePageUrl = '/admin/manage-users';
        $queryParams = [];
        if (!empty($filters['role'])) $queryParams['role'] = $filters['role'];
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
    </div>
</div>

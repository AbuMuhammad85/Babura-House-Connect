<div class="space-y-6">
    <div>
        <h2 class="text-xl font-bold text-slate-800">Manage Users</h2>
        <p class="text-xs text-text-muted mt-1">Review, activate, suspend, or manage platform tenant and landlord accounts.</p>
    </div>

    <!-- Users Table Container -->
    <div class="overflow-x-auto bg-white border border-slate-100 rounded-2xl shadow-sm">
        <table class="min-w-full divide-y divide-slate-100 text-left">
            <thead class="bg-slate-50 text-[10px] font-bold uppercase tracking-wider text-slate-500">
                <tr>
                    <th class="px-6 py-3">User Profile Name</th>
                    <th class="px-6 py-3">Email Address</th>
                    <th class="px-6 py-3">Account Role</th>
                    <th class="px-6 py-3">Status</th>
                    <th class="px-6 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 bg-white text-xs text-slate-700">
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td class="px-6 py-4 flex items-center space-x-3">
                            <span class="w-8 h-8 rounded-lg bg-slate-100 text-slate-500 font-bold flex items-center justify-center text-xs uppercase shrink-0">
                                <?= substr($user['name'], 0, 2) ?>
                            </span>
                            <span class="font-bold text-slate-850"><?= $user['name'] ?></span>
                        </td>
                        <td class="px-6 py-4 text-text-muted">
                            <?= $user['email'] ?>
                        </td>
                        <td class="px-6 py-4">
                            <?php 
                            $roleType = 'info';
                            if ($user['role'] === 'Landlord') $roleType = 'success';
                            if ($user['role'] === 'Admin') $roleType = 'danger';
                            ?>
                            <?php component('badges', ['type' => $roleType, 'text' => $user['role']]); ?>
                        </td>
                        <td class="px-6 py-4">
                            <?php component('badges', ['type' => 'success', 'text' => $user['status']]); ?>
                        </td>
                        <!-- Actions -->
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end space-x-2">
                                <button class="px-2.5 py-1.5 border border-slate-200 bg-white hover:bg-red-50 text-red-650 hover:border-red-200 font-semibold rounded-lg text-[10px] transition-all">
                                    Suspend
                                </button>
                                <button class="p-1.5 border border-slate-200 bg-white text-slate-500 rounded-lg flex items-center justify-center hover:bg-slate-50 transition-colors" title="Edit Role">
                                    <i class="fa-regular fa-pen-to-square text-xs"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

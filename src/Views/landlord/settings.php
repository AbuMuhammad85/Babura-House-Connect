<?php
use App\Helpers\Flash;
?>

<div class="space-y-6 max-w-3xl">
    <div>
        <h2 class="text-xl font-bold text-slate-800">Account Settings</h2>
        <p class="text-xs text-text-muted mt-1">Configure security preferences and password updates.</p>
    </div>

    <?php if (Flash::has('error')): ?>
        <?php component('alerts', ['type' => 'error', 'message' => Flash::get('error')]); ?>
    <?php endif; ?>
    <?php if (Flash::has('success')): ?>
        <?php component('alerts', ['type' => 'success', 'message' => Flash::get('success')]); ?>
    <?php endif; ?>

    <!-- Password Block -->
    <div class="bg-white border border-slate-100 rounded-2xl p-6 sm:p-8 shadow-sm space-y-6">
        <h3 class="font-bold text-slate-800 text-sm border-b border-slate-50 pb-3">Update Password</h3>
        
        <form action="<?= url('/landlord/settings/password') ?>" method="POST" class="space-y-4 max-w-md">
            <?= \App\Helpers\CSRF::field() ?>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Current Password</label>
                <input type="password" name="current_password" required placeholder="••••••••" class="w-full px-3.5 py-2 text-xs border border-slate-200 focus:border-primary focus:outline-none rounded-lg text-slate-800 bg-white">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">New Password</label>
                <input type="password" name="new_password" required placeholder="••••••••" class="w-full px-3.5 py-2 text-xs border border-slate-200 focus:border-primary focus:outline-none rounded-lg text-slate-800 bg-white">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Confirm New Password</label>
                <input type="password" name="confirm_password" required placeholder="••••••••" class="w-full px-3.5 py-2 text-xs border border-slate-200 focus:border-primary focus:outline-none rounded-lg text-slate-800 bg-white">
            </div>

            <div>
                <button type="submit" class="bg-primary hover:bg-primary/95 text-white font-semibold py-2 px-4 rounded-lg text-xs transition-all shadow-sm">
                    Change Password
                </button>
            </div>
        </form>
    </div>
</div>

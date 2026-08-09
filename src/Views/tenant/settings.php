<?php
use App\Helpers\Flash;
?>

<div class="space-y-6 max-w-3xl">
    <div>
        <h2 class="text-xl font-bold text-slate-800">Account Settings</h2>
        <p class="text-xs text-text-muted mt-1">Configure your login credentials and security settings.</p>
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
        
        <form action="<?= url('/tenant/settings/password') ?>" method="POST" class="space-y-4 max-w-md">
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

    <!-- Alerts configuration -->
    <div class="bg-white border border-slate-100 rounded-2xl p-6 sm:p-8 shadow-sm space-y-6">
        <h3 class="font-bold text-slate-800 text-sm border-b border-slate-50 pb-3">Notification Preferences</h3>
        
        <div class="space-y-4">
            <div class="flex items-start justify-between">
                <div class="space-y-0.5">
                    <h4 class="text-xs font-bold text-slate-700">Email Notifications</h4>
                    <p class="text-[10px] text-text-muted">Receive monthly reports and landlord response notifications.</p>
                </div>
                <input type="checkbox" checked class="w-4 h-4 rounded text-primary focus:ring-primary border-slate-200">
            </div>
            
            <div class="flex items-start justify-between">
                <div class="space-y-0.5">
                    <h4 class="text-xs font-bold text-slate-700">SMS Alerts</h4>
                    <p class="text-[10px] text-text-muted">Get direct text updates on emergency rent actions.</p>
                </div>
                <input type="checkbox" class="w-4 h-4 rounded text-primary focus:ring-primary border-slate-200">
            </div>
        </div>
    </div>
</div>

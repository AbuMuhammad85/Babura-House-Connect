<?php
use App\Helpers\Flash;
use App\Core\Config;
?>

<div class="space-y-6 max-w-3xl">
    <div>
        <h2 class="text-xl font-bold text-slate-800">System Settings</h2>
        <p class="text-xs text-text-muted mt-1">Configure global application variables, verification guidelines, and parameters.</p>
    </div>

    <?php if (Flash::has('error')): ?>
        <?php component('alerts', ['type' => 'error', 'message' => Flash::get('error')]); ?>
    <?php endif; ?>
    <?php if (Flash::has('success')): ?>
        <?php component('alerts', ['type' => 'success', 'message' => Flash::get('success')]); ?>
    <?php endif; ?>

    <!-- Configuration block -->
    <div class="bg-white border border-slate-100 rounded-2xl p-6 sm:p-8 shadow-sm space-y-6">
        <h3 class="font-bold text-slate-800 text-sm border-b border-slate-50 pb-3">Global Configuration</h3>
        
        <form action="<?= url('/admin/settings') ?>" method="POST" class="space-y-6">
            <?= \App\Helpers\CSRF::field() ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Platform Title</label>
                    <input type="text" name="site_name" value="<?= htmlspecialchars(Config::get('app.name', 'Babura House Connect')) ?>" required class="w-full px-3.5 py-2 text-xs border border-slate-200 focus:border-primary focus:outline-none rounded-lg text-slate-800 bg-white">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Support Contact Email</label>
                    <input type="email" name="support_email" value="<?= htmlspecialchars(Config::get('app.support_email', 'support@houseconnect.ng')) ?>" required class="w-full px-3.5 py-2 text-xs border border-slate-200 focus:border-primary focus:outline-none rounded-lg text-slate-800 bg-white">
                </div>
            </div>

            <!-- Maintenance Mode Toggles -->
            <div class="flex items-start justify-between border-t border-b border-slate-50 py-4">
                <div class="space-y-0.5">
                    <h4 class="text-xs font-bold text-slate-700">Maintenance Mode</h4>
                    <p class="text-[10px] text-text-muted">Put the platform offline for updates or service migrations.</p>
                </div>
                <input type="checkbox" name="maintenance_mode" value="1" <?= Config::get('app.maintenance', false) ? 'checked' : '' ?> class="w-4 h-4 rounded text-primary focus:ring-primary border-slate-200">
            </div>

            <!-- SMTP Settings -->
            <div class="space-y-4">
                <h4 class="font-bold text-slate-800 text-xs">SMTP Mailer Settings</h4>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">SMTP Host</label>
                        <input type="text" name="smtp_host" value="<?= htmlspecialchars(Config::get('app.smtp_host', 'smtp.mailtrap.io')) ?>" class="w-full px-3.5 py-2 text-xs border border-slate-200 focus:border-primary focus:outline-none rounded-lg text-slate-800 bg-white">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">SMTP Port</label>
                        <input type="number" name="smtp_port" value="<?= htmlspecialchars(Config::get('app.smtp_port', '2525')) ?>" class="w-full px-3.5 py-2 text-xs border border-slate-200 focus:border-primary focus:outline-none rounded-lg text-slate-800 bg-white">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Encryption</label>
                        <select name="smtp_encryption" class="w-full px-3.5 py-2 text-xs border border-slate-200 focus:border-primary focus:outline-none rounded-lg text-slate-800 bg-white">
                            <?php $enc = Config::get('app.smtp_encryption', 'TLS'); ?>
                            <option value="TLS" <?= $enc === 'TLS' ? 'selected' : '' ?>>TLS</option>
                            <option value="SSL" <?= $enc === 'SSL' ? 'selected' : '' ?>>SSL</option>
                            <option value="None" <?= $enc === 'None' ? 'selected' : '' ?>>None</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 flex justify-end">
                <button type="submit" class="bg-slate-900 hover:bg-slate-800 text-white font-semibold py-2.5 px-6 rounded-lg text-xs transition-all shadow-sm">
                    Save System Settings
                </button>
            </div>
        </form>
    </div>
</div>

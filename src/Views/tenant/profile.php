<?php
use App\Helpers\Flash;
?>

<div class="space-y-6 max-w-3xl">
    <div>
        <h2 class="text-xl font-bold text-slate-800">My Profile</h2>
        <p class="text-xs text-text-muted mt-1">Manage your public information for landlord applications.</p>
    </div>

    <?php if (Flash::has('error')): ?>
        <?php component('alerts', ['type' => 'error', 'message' => Flash::get('error')]); ?>
    <?php endif; ?>
    <?php if (Flash::has('success')): ?>
        <?php component('alerts', ['type' => 'success', 'message' => Flash::get('success')]); ?>
    <?php endif; ?>

    <!-- Profile Form -->
    <div class="bg-white border border-slate-100 rounded-2xl p-6 sm:p-8 shadow-sm space-y-6">
        <div class="flex items-center space-x-4">
            <?php if (!empty($profile['profile_photo'])): ?>
                <img src="<?= url($profile['profile_photo']) ?>" alt="Avatar" class="w-16 h-16 rounded-2xl object-cover border border-slate-100 shrink-0">
            <?php else: ?>
                <span class="w-16 h-16 rounded-2xl bg-primary/10 text-primary flex items-center justify-center font-bold text-2xl uppercase shrink-0">
                    <?= substr($profile['name'] ?? 'U', 0, 2) ?>
                </span>
            <?php endif; ?>
            <div>
                <h3 class="font-bold text-slate-800 text-sm"><?= htmlspecialchars($profile['name']) ?></h3>
                <p class="text-[11px] text-text-muted mt-0.5">Tenant Account &bull; Verified Member</p>
            </div>
        </div>

        <form action="<?= url('/tenant/profile') ?>" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-6 border-t border-slate-100">
            <?= \App\Helpers\CSRF::field() ?>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Full Name</label>
                <input type="text" name="full_name" value="<?= htmlspecialchars($profile['name']) ?>" required class="w-full px-3.5 py-2 text-xs border border-slate-200 focus:border-primary focus:outline-none rounded-lg text-slate-800 bg-white">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Email Address</label>
                <input type="email" value="<?= htmlspecialchars($profile['email']) ?>" disabled class="w-full px-3.5 py-2 text-xs border border-slate-200 rounded-lg text-slate-500 bg-slate-50/50">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Phone Number</label>
                <input type="tel" name="phone" value="<?= htmlspecialchars($profile['phone']) ?>" required class="w-full px-3.5 py-2 text-xs border border-slate-200 focus:border-primary focus:outline-none rounded-lg text-slate-800 bg-white">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Occupation</label>
                <input type="text" name="occupation" value="<?= htmlspecialchars($profile['occupation']) ?>" class="w-full px-3.5 py-2 text-xs border border-slate-200 focus:border-primary focus:outline-none rounded-lg text-slate-800 bg-white">
            </div>

            <div class="sm:col-span-2">
                <label class="block text-xs font-semibold text-slate-700 mb-1">Profile Photo</label>
                <input type="file" name="profile_photo" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20">
                <p class="text-[10px] text-text-muted mt-1">JPEG, PNG, WebP format. Maximum size 5MB.</p>
            </div>

            <div class="sm:col-span-2">
                <label class="block text-xs font-semibold text-slate-700 mb-1">Residential Address / Bio</label>
                <textarea name="bio" rows="3" class="w-full px-3.5 py-2 text-xs border border-slate-200 focus:border-primary focus:outline-none rounded-lg text-slate-800 bg-white resize-none"><?= htmlspecialchars($profile['address']) ?></textarea>
            </div>

            <div>
                <button type="submit" class="bg-primary hover:bg-primary/95 text-white font-semibold py-2 px-4 rounded-lg text-xs transition-all shadow-sm hover:shadow-lg">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

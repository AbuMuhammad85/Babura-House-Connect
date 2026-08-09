<?php
use App\Helpers\Flash;
?>

<div class="space-y-6 max-w-4xl">
    <div class="flex items-center space-x-3">
        <a href="<?= url('/admin/manage-users') ?>" class="text-slate-400 hover:text-slate-600 transition-colors">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <div>
            <h2 class="text-xl font-bold text-slate-800">Tenant Details Profile</h2>
            <p class="text-xs text-text-muted mt-1">Review tenant credentials, favorites, recent search inquiries, and suspend/activate status.</p>
        </div>
    </div>

    <?php if (Flash::has('error')): ?>
        <?php component('alerts', ['type' => 'error', 'message' => Flash::get('error')]); ?>
    <?php endif; ?>
    <?php if (Flash::has('success')): ?>
        <?php component('alerts', ['type' => 'success', 'message' => Flash::get('success')]); ?>
    <?php endif; ?>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        <!-- Profile summary card -->
        <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm space-y-4">
            <div class="flex flex-col items-center text-center space-y-3">
                <span class="w-16 h-16 rounded-full bg-slate-100 text-slate-500 font-extrabold flex items-center justify-center text-lg uppercase">
                    <?= substr($tenant['full_name'] ?? 'T', 0, 2) ?>
                </span>
                <div>
                    <h3 class="font-bold text-slate-800"><?= htmlspecialchars($tenant['full_name']) ?></h3>
                    <p class="text-[10px] uppercase font-bold text-primary tracking-wider">Tenant Profile</p>
                </div>
            </div>
            
            <div class="border-t border-slate-50 pt-4 space-y-3 text-xs">
                <div class="flex justify-between">
                    <span class="text-text-muted">Email</span>
                    <span class="font-semibold text-slate-700"><?= htmlspecialchars($tenant['email'] ?? '[No Email]') ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-text-muted">Phone Line</span>
                    <span class="font-semibold text-slate-700"><?= htmlspecialchars($tenant['phone']) ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-text-muted">Registered On</span>
                    <span class="font-semibold text-slate-700"><?= date('M d, Y', strtotime($tenant['created_at'])) ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-text-muted">Last Log-In</span>
                    <span class="font-semibold text-slate-700"><?= $tenant['last_login'] ? date('M d, Y H:i', strtotime($tenant['last_login'])) : 'Never' ?></span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-text-muted">Account Status</span>
                    <?php $statusColor = $tenant['status'] === 'active' ? 'success' : 'danger'; ?>
                    <?php component('badges', ['type' => $statusColor, 'text' => ucfirst($tenant['status'])]); ?>
                </div>
            </div>

            <div class="border-t border-slate-50 pt-4">
                <?php if ($tenant['status'] === 'active'): ?>
                    <form action="<?= url('/admin/users/suspend') ?>" method="POST" onsubmit="return confirm('Suspend this tenant account?');">
                        <?= \App\Helpers\CSRF::field() ?>
                        <input type="hidden" name="id" value="<?= $tenant['id'] ?>">
                        <button type="submit" class="w-full py-2 bg-red-50 hover:bg-red-100 text-red-655 font-bold rounded-lg text-xs transition-all text-center">
                            Suspend Account
                        </button>
                    </form>
                <?php else: ?>
                    <form action="<?= url('/admin/users/activate') ?>" method="POST" onsubmit="return confirm('Activate this tenant account?');">
                        <?= \App\Helpers\CSRF::field() ?>
                        <input type="hidden" name="id" value="<?= $tenant['id'] ?>">
                        <button type="submit" class="w-full py-2 bg-green-50 hover:bg-green-100 text-green-755 font-bold rounded-lg text-xs transition-all text-center">
                            Activate Account
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>

        <!-- Tenant interactions statistics -->
        <div class="md:col-span-2 space-y-6">
            
            <!-- Favorites List -->
            <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm space-y-4">
                <h3 class="font-bold text-slate-800 text-sm border-b border-slate-50 pb-2">Favorited Apartments</h3>
                <?php if (empty($favorites)): ?>
                    <p class="text-xs text-text-muted">This tenant has not added any apartments to favorites yet.</p>
                <?php else: ?>
                    <div class="divide-y divide-slate-50 text-xs">
                        <?php foreach ($favorites as $fav): ?>
                            <div class="py-2.5 flex justify-between items-center">
                                <div>
                                    <p class="font-bold text-slate-800"><?= htmlspecialchars($fav['title']) ?></p>
                                    <p class="text-[10px] text-text-muted"><?= htmlspecialchars($fav['area_name']) ?> • Rent: ₦<?= number_format($fav['rent_amount']) ?>/<?= $fav['rent_period'] ?></p>
                                </div>
                                <a href="<?= url('/house/' . $fav['house_id']) ?>" target="_blank" class="text-primary font-bold hover:underline">View</a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Inquiries List -->
            <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm space-y-4">
                <h3 class="font-bold text-slate-800 text-sm border-b border-slate-50 pb-2">Recent Inquiries Submitted</h3>
                <?php if (empty($inquiries)): ?>
                    <p class="text-xs text-text-muted">This tenant has not sent any inquiry requests yet.</p>
                <?php else: ?>
                    <div class="divide-y divide-slate-50 text-xs space-y-3">
                        <?php foreach ($inquiries as $inq): ?>
                            <div class="py-2 space-y-1">
                                <div class="flex justify-between items-center">
                                    <span class="font-bold text-slate-800"><?= htmlspecialchars($inq['house_title']) ?></span>
                                    <span class="text-[10px] text-text-muted"><?= date('M d, Y', strtotime($inq['created_at'])) ?></span>
                                </div>
                                <p class="text-text-muted italic bg-slate-50 p-2 rounded text-[11px]">"<?= htmlspecialchars($inq['message']) ?>"</p>
                                <div class="flex justify-between items-center text-[10px] pt-1">
                                    <span class="text-text-muted">Landlord: <span class="font-bold text-slate-700"><?= htmlspecialchars($inq['landlord_name']) ?></span></span>
                                    <?php $inqColor = $inq['status'] === 'pending' ? 'warning' : 'success'; ?>
                                    <?php component('badges', ['type' => $inqColor, 'text' => ucfirst($inq['status'])]); ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

        </div>
    </div>
</div>

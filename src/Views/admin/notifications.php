<?php
use App\Helpers\Flash;
?>

<div class="space-y-6 max-w-4xl">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Admin Alerts & Notifications</h2>
            <p class="text-xs text-text-muted mt-1">Review system alerts, landlord registrations, listing approvals, and complaints.</p>
        </div>

        <?php if (!empty($notifications)): ?>
            <form action="<?= url('/admin/notifications/mark-all-read') ?>" method="POST" class="inline">
                <?= \App\Helpers\CSRF::field() ?>
                <button type="submit" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white font-bold rounded-lg text-xs transition-all shadow-sm">
                    <i class="fa-solid fa-check-double mr-1.5"></i> Mark All as Read
                </button>
            </form>
        <?php endif; ?>
    </div>

    <?php if (Flash::has('error')): ?>
        <?php component('alerts', ['type' => 'error', 'message' => Flash::get('error')]); ?>
    <?php endif; ?>
    <?php if (Flash::has('success')): ?>
        <?php component('alerts', ['type' => 'success', 'message' => Flash::get('success')]); ?>
    <?php endif; ?>

    <div class="bg-white border border-slate-100 rounded-2xl shadow-sm overflow-hidden">
        <?php if (empty($notifications)): ?>
            <div class="p-12 text-center text-xs text-text-muted">
                <i class="fa-solid fa-bell-slash text-3xl text-slate-300 mb-2 block"></i>
                No notifications logged for your account.
            </div>
        <?php else: ?>
            <div class="divide-y divide-slate-100">
                <?php foreach ($notifications as $notification): ?>
                    <div class="p-5 flex items-start justify-between space-x-4 hover:bg-slate-50/50 transition-colors <?= !$notification['read_at'] ? 'bg-primary/5' : '' ?>">
                        <div class="flex items-start space-x-3 text-xs">
                            <span class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 <?= !$notification['read_at'] ? 'bg-primary/10 text-primary' : 'bg-slate-100 text-slate-400' ?>">
                                <i class="fa-solid fa-bell"></i>
                            </span>
                            <div class="space-y-1">
                                <div class="flex items-center space-x-2">
                                    <h4 class="font-bold text-slate-800"><?= htmlspecialchars($notification['title']) ?></h4>
                                    <?php if (!$notification['read_at']): ?>
                                        <span class="px-1.5 py-0.5 rounded text-[8px] font-bold bg-primary text-white uppercase tracking-wider">New</span>
                                    <?php endif; ?>
                                </div>
                                <p class="text-text-muted text-[11px] leading-relaxed"><?= htmlspecialchars($notification['message']) ?></p>
                                <p class="text-[10px] text-slate-400"><?= date('M d, Y • H:i', strtotime($notification['created_at'])) ?></p>
                            </div>
                        </div>

                        <div class="flex items-center space-x-2">
                            <?php if (!$notification['read_at']): ?>
                                <form action="<?= url('/admin/notifications/mark-read') ?>" method="POST" class="inline">
                                    <?= \App\Helpers\CSRF::field() ?>
                                    <input type="hidden" name="id" value="<?= $notification['id'] ?>">
                                    <button type="submit" class="px-2 py-1 bg-white border border-slate-200 hover:bg-slate-50 text-slate-600 rounded text-[10px] font-semibold transition-all" title="Mark as Read">
                                        Mark Read
                                    </button>
                                </form>
                            <?php endif; ?>
                            <form action="<?= url('/admin/notifications/delete') ?>" method="POST" class="inline" onsubmit="return confirm('Delete this notification?');">
                                <?= \App\Helpers\CSRF::field() ?>
                                <input type="hidden" name="id" value="<?= $notification['id'] ?>">
                                <button type="submit" class="px-2 py-1 bg-white border border-slate-200 hover:bg-red-50 text-red-550 rounded text-[10px] font-semibold transition-all" title="Delete">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Pagination component call -->
            <?php component('pagination', [
                'currentPage' => $currentPage,
                'totalPages' => $totalPages,
                'baseUrl' => url('/admin/notifications')
            ]); ?>
        <?php endif; ?>
    </div>
</div>

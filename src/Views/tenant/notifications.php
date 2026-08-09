<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Notifications</h2>
            <p class="text-xs text-text-muted mt-1">Stay updated with rental alerts, system changes, and landlord replies.</p>
        </div>
        <?php if (!empty($notifications)): ?>
            <form action="<?= url('/tenant/notifications/mark-all-read') ?>" method="POST" class="inline">
                <?= \App\Helpers\CSRF::field() ?>
                <button type="submit" class="text-xs font-semibold text-primary hover:underline">Mark All Read</button>
            </form>
        <?php endif; ?>
    </div>

    <?php if (empty($notifications)): ?>
        <?php component('empty_states', [
            'title' => 'Inbox is Empty',
            'message' => 'We will send you alert updates when landlords interact with your bids or messages.',
            'icon' => 'fa-bell-slash'
        ]); ?>
    <?php else: ?>
        <div class="space-y-4 max-w-3xl">
            <?php foreach ($notifications as $notification): ?>
                <?php component('notification_card', $notification); ?>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <?php if (isset($totalPages) && $totalPages > 1): ?>
            <div class="pt-4 max-w-3xl">
                <?php component('pagination', [
                    'currentPage' => $currentPage,
                    'totalPages' => $totalPages,
                    'baseUrl' => url('/tenant/notifications')
                ]); ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

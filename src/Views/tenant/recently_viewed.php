<div class="space-y-6">
    <div>
        <h2 class="text-xl font-bold text-slate-800">Recently Viewed</h2>
        <p class="text-xs text-text-muted mt-1">Houses you visited recently on Babura House Connect.</p>
    </div>

    <?php if (empty($listings)): ?>
        <?php component('empty_states', [
            'title' => 'No Recently Viewed Houses',
            'message' => 'Start viewing details of listed apartments to build your viewing history.',
            'icon' => 'fa-clock-rotate-left',
            'actionUrl' => '/browse',
            'actionText' => 'Browse Properties'
        ]); ?>
    <?php else: ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($listings as $listing): ?>
                <?php component('house_card', $listing); ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

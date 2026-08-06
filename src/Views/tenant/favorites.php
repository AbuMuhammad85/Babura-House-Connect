<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-xl font-bold text-slate-800">My Favorites</h2>
            <p class="text-xs text-text-muted mt-1">Houses you bookmarked for later consideration.</p>
        </div>
    </div>

    <?php if (empty($listings)): ?>
        <?php component('empty_states', [
            'title' => 'No Favorites Added',
            'message' => 'Bookmark properties while browsing to access them easily here.',
            'icon' => 'fa-heart',
            'actionUrl' => '/browse',
            'actionText' => 'Browse Houses'
        ]); ?>
    <?php else: ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($listings as $listing): ?>
                <?php component('house_card', $listing); ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

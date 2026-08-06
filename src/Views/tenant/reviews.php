<div class="space-y-6">
    <div>
        <h2 class="text-xl font-bold text-slate-800">My Reviews</h2>
        <p class="text-xs text-text-muted mt-1">Manage and track reviews you submitted for homes in Babura.</p>
    </div>

    <?php if (empty($reviews)): ?>
        <?php component('empty_states', [
            'title' => 'No Reviews Submitted',
            'message' => 'Share your rental experiences to help others make informed decisions.',
            'icon' => 'fa-star'
        ]); ?>
    <?php else: ?>
        <div class="space-y-4 max-w-4xl">
            <?php foreach ($reviews as $review): ?>
                <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm space-y-4">
                    <div class="flex justify-between items-start">
                        <div>
                            <h4 class="font-bold text-slate-800 text-sm"><?= $review['house'] ?></h4>
                            <span class="text-[10px] text-slate-400 block mt-0.5">Submitted on <?= $review['date'] ?></span>
                        </div>
                        
                        <div class="flex flex-col items-end gap-1.5">
                            <!-- Star Rating -->
                            <div class="flex items-center space-x-0.5 text-accent text-xs">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <i class="fa-<?= $i <= $review['rating'] ? 'solid' : 'regular' ?> fa-star"></i>
                                <?php endfor; ?>
                            </div>
                            
                            <!-- Badges component -->
                            <?php 
                            $badgeType = 'warning';
                            if ($review['status'] === 'approved') $badgeType = 'success';
                            if ($review['status'] === 'rejected') $badgeType = 'danger';
                            ?>
                            <?php component('badges', ['type' => $badgeType, 'text' => ucfirst($review['status'])]); ?>
                        </div>
                    </div>
                    
                    <p class="text-xs text-slate-600 leading-relaxed border-t border-slate-50 pt-3">
                        &ldquo;<?= $review['comment'] ?>&rdquo;
                    </p>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

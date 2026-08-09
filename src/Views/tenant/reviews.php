<?php
use App\Helpers\Flash;
?>

<div class="space-y-8">
    <div>
        <h2 class="text-xl font-bold text-slate-800">My Reviews</h2>
        <p class="text-xs text-text-muted mt-1">Manage and track reviews you submitted for homes in Babura.</p>
    </div>

    <?php if (Flash::has('error')): ?>
        <?php component('alerts', ['type' => 'error', 'message' => Flash::get('error')]); ?>
    <?php endif; ?>
    <?php if (Flash::has('success')): ?>
        <?php component('alerts', ['type' => 'success', 'message' => Flash::get('success')]); ?>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Review History List (2 Columns width) -->
        <div class="lg:col-span-2 space-y-4">
            <h3 class="font-bold text-slate-800 text-sm">Review History</h3>
            <?php if (empty($reviews)): ?>
                <?php component('empty_states', [
                    'title' => 'No Reviews Submitted',
                    'message' => 'Share your rental experiences to help others make informed decisions.',
                    'icon' => 'fa-star'
                ]); ?>
            <?php else: ?>
                <div class="space-y-4">
                    <?php foreach ($reviews as $review): ?>
                        <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm space-y-4">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h4 class="font-bold text-slate-800 text-sm"><?= htmlspecialchars($review['house']) ?></h4>
                                    <span class="text-[10px] text-slate-400 block mt-0.5">Submitted on <?= htmlspecialchars($review['date']) ?></span>
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
                                    $statusLabel = $review['status'];
                                    if ($review['status'] === 'published') {
                                        $badgeType = 'success';
                                        $statusLabel = 'Approved';
                                    } elseif ($review['status'] === 'hidden') {
                                        $badgeType = 'neutral';
                                        $statusLabel = 'Hidden';
                                    } elseif ($review['status'] === 'reported') {
                                        $badgeType = 'danger';
                                        $statusLabel = 'Reported';
                                    }
                                    ?>
                                    <?php component('badges', ['type' => $badgeType, 'text' => $statusLabel]); ?>
                                </div>
                            </div>
                            
                            <p class="text-xs text-slate-600 leading-relaxed border-t border-slate-50 pt-3">
                                &ldquo;<?= htmlspecialchars($review['comment']) ?>&rdquo;
                            </p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Write a Review Form (1 Column width) -->
        <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm self-start space-y-4">
            <h3 class="font-bold text-slate-800 text-sm">Write a Property Review</h3>
            <form action="<?= url('/tenant/reviews/create') ?>" method="POST" class="space-y-4">
                <?= \App\Helpers\CSRF::field() ?>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Select Property</label>
                    <select name="house_id" required class="w-full px-3.5 py-2 text-xs border border-slate-200 focus:border-primary focus:outline-none rounded-lg text-slate-800 bg-white">
                        <?php foreach ($houses as $house): ?>
                            <option value="<?= $house['id'] ?>"><?= htmlspecialchars($house['title']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Rating (1 to 5 Stars)</label>
                    <select name="rating" required class="w-full px-3.5 py-2 text-xs border border-slate-200 focus:border-primary focus:outline-none rounded-lg text-slate-800 bg-white">
                        <option value="5">⭐⭐⭐⭐⭐ (5 - Excellent)</option>
                        <option value="4">⭐⭐⭐⭐ (4 - Good)</option>
                        <option value="3">⭐⭐⭐ (3 - Average)</option>
                        <option value="2">⭐⭐ (2 - Poor)</option>
                        <option value="1">⭐ (1 - Terrible)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Your Review Comment</label>
                    <textarea name="comment" rows="4" required placeholder="Describe electricity, water pump speed, prepaid meters, or landlord support..." class="w-full px-3.5 py-2 text-xs border border-slate-200 focus:border-primary focus:outline-none rounded-lg text-slate-800 bg-white resize-none"></textarea>
                </div>
                <button type="submit" class="w-full bg-primary hover:bg-primary/95 text-white font-semibold py-2.5 px-4 rounded-lg text-xs transition-all shadow-md shadow-primary/20 flex items-center justify-center">
                    Submit Review
                </button>
            </form>
        </div>
    </div>
</div>

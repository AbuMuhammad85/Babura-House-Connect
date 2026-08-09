<!-- Landlord Dashboard Grid -->
<div class="space-y-6">
    
    <!-- Stats Cards Row -->
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
        <!-- Card 1: Total Listings -->
        <div class="bg-white border border-slate-100 rounded-2xl p-4 shadow-sm flex flex-col justify-between h-24">
            <span class="text-[10px] uppercase font-bold tracking-wider text-slate-400">Total Listings</span>
            <div class="flex items-baseline justify-between mt-2">
                <p class="text-xl font-extrabold text-slate-800"><?= $stats['total_listings'] ?></p>
                <span class="text-green-700 text-xs flex flex-wrap gap-1">
                    <span class="px-1 bg-green-50 text-[9px] font-semibold text-green-600 rounded">Pub: <?= $stats['published_listings'] ?></span>
                    <span class="px-1 bg-yellow-50 text-[9px] font-semibold text-yellow-600 rounded">Pend: <?= $stats['pending_listings'] ?></span>
                </span>
            </div>
        </div>
        <!-- Card 2: Inquiries -->
        <div class="bg-white border border-slate-100 rounded-2xl p-4 shadow-sm flex flex-col justify-between h-24">
            <span class="text-[10px] uppercase font-bold tracking-wider text-slate-400">Total Inquiries</span>
            <div class="flex items-baseline justify-between mt-2">
                <p class="text-xl font-extrabold text-slate-800"><?= $stats['total_inquiries'] ?></p>
                <span class="text-blue-500 text-xs flex flex-wrap gap-1">
                    <span class="px-1 bg-blue-50 text-[9px] font-semibold text-blue-600 rounded font-bold">New: <?= $stats['pending_inquiries'] ?></span>
                </span>
            </div>
        </div>
        <!-- Card 3: Rating Average -->
        <div class="bg-white border border-slate-100 rounded-2xl p-4 shadow-sm flex flex-col justify-between h-24">
            <span class="text-[10px] uppercase font-bold tracking-wider text-slate-400">Average Rating</span>
            <div class="flex items-baseline justify-between mt-2">
                <p class="text-xl font-extrabold text-slate-800"><?= number_format($stats['rating_average'], 2) ?></p>
                <span class="text-amber-500 text-xs"><i class="fa-solid fa-star"></i> (<?= $stats['review_count'] ?>)</span>
            </div>
        </div>
        <!-- Card 4: Rejected & Inactive -->
        <div class="bg-white border border-slate-100 rounded-2xl p-4 shadow-sm flex flex-col justify-between h-24">
            <span class="text-[10px] uppercase font-bold tracking-wider text-slate-400">Rejected / Inactive</span>
            <div class="flex items-baseline justify-between mt-2">
                <p class="text-xl font-extrabold text-slate-800"><?= $stats['rejected_listings'] + $stats['inactive_listings'] ?></p>
                <span class="text-red-500 text-xs flex flex-wrap gap-1">
                    <span class="px-1 bg-red-50 text-[9px] font-semibold text-red-650 rounded">Rej: <?= $stats['rejected_listings'] ?></span>
                    <span class="px-1 bg-slate-50 text-[9px] font-semibold text-slate-500 rounded">Arch: <?= $stats['inactive_listings'] ?></span>
                </span>
            </div>
        </div>
        <!-- Card 5: Unread Alerts -->
        <div class="bg-white border border-slate-100 rounded-2xl p-4 shadow-sm flex flex-col justify-between h-24">
            <span class="text-[10px] uppercase font-bold tracking-wider text-slate-400">Unread Alerts</span>
            <div class="flex items-baseline justify-between mt-2">
                <p class="text-xl font-extrabold text-slate-800"><?= $stats['unread_notifications'] ?></p>
                <span class="text-green-700 text-xs"><i class="fa-solid fa-bell"></i></span>
            </div>
        </div>
    </div>

    <!-- Analytics Chart & Inquiries Table Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Graph Area (2 Columns) -->
        <div class="lg:col-span-2 bg-white rounded-2xl p-5 border border-slate-100 shadow-sm space-y-4">
            <h3 class="font-bold text-slate-800 text-sm">Listing Performance (Views &amp; Inquiries)</h3>
            <div class="h-64 relative">
                <canvas id="landlordChart"></canvas>
            </div>
        </div>

        <!-- Inquiries table summary (1 Column) -->
        <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-sm space-y-4 flex flex-col justify-between h-[340px]">
            <div>
                <div class="flex justify-between items-center">
                    <h3 class="font-bold text-slate-800 text-sm">Recent Inquiries</h3>
                    <a href="<?= url('/landlord/inquiries') ?>" class="text-[11px] text-primary hover:underline font-semibold">View All</a>
                </div>
                <div class="mt-4 space-y-4 max-h-56 overflow-y-auto pr-1">
                    <?php if (empty($recentInquiries)): ?>
                        <p class="text-xs text-text-muted">No rental inquiries received yet.</p>
                    <?php else: ?>
                        <?php foreach ($recentInquiries as $inquiry): ?>
                            <div class="flex justify-between items-center text-xs pt-1">
                                <div class="space-y-0.5">
                                    <p class="font-bold text-slate-800"><?= htmlspecialchars($inquiry['tenant_name']) ?></p>
                                    <p class="text-[10px] text-text-muted"><?= htmlspecialchars($inquiry['house_title']) ?></p>
                                </div>
                                <div class="text-right space-y-1">
                                    <span class="text-[9px] text-slate-400 block"><?= date('M d', strtotime($inquiry['created_at'])) ?></span>
                                    <span class="px-2 py-0.5 rounded-full text-[9px] font-semibold bg-slate-100 text-slate-650"><?= ucfirst($inquiry['status']) ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
            <a href="<?= url('/landlord/listings') ?>" class="w-full text-center py-2 bg-slate-50 border border-slate-100 hover:bg-slate-100 text-xs font-semibold text-slate-700 rounded-lg mt-4 block">
                Manage Listings
            </a>
        </div>

    </div>

    <!-- Recent Listings Feed -->
    <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-sm space-y-4">
        <div class="flex justify-between items-center">
            <h3 class="font-bold text-slate-800 text-sm">Recent Listings</h3>
            <a href="<?= url('/landlord/listings') ?>" class="text-xs text-primary hover:underline font-semibold">View All</a>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php if (empty($recentListings)): ?>
                <div class="sm:col-span-3 text-center py-8 text-xs text-text-muted">
                    <i class="fa-solid fa-house-chimney-window text-2xl text-slate-200 mb-2 block"></i>
                    <p>No listings added yet. Click <a href="<?= url('/landlord/add-house') ?>" class="text-primary hover:underline font-semibold">Add House</a> to get started.</p>
                </div>
            <?php else: ?>
                <?php foreach ($recentListings as $listing): ?>
                    <?php 
                    $mapped = [
                        'id' => $listing['id'],
                        'title' => $listing['title'],
                        'location' => $listing['area_name'] . ', Babura',
                        'price' => $listing['rent_amount'],
                        'period' => $listing['rent_period'],
                        'beds' => $listing['bedrooms'],
                        'baths' => $listing['bathrooms'],
                        'size' => $listing['size'],
                        'image' => \App\Core\Database::fetch("SELECT file_path FROM house_images WHERE house_id = :id AND is_primary = 1 LIMIT 1", ['id' => $listing['id']])['file_path'] ?? '',
                        'type' => ucfirst(str_replace('_', ' ', $listing['house_type'])),
                        'verified' => true
                    ];
                    component('house_card', $mapped); 
                    ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Recent Notifications Feed -->
    <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-sm space-y-4">
        <div class="flex justify-between items-center">
            <h3 class="font-bold text-slate-800 text-sm">Recent Alerts</h3>
            <a href="<?= url('/landlord/notifications') ?>" class="text-xs text-primary hover:underline font-semibold">View All</a>
        </div>
        <div class="space-y-3">
            <?php if (empty($recentNotifications)): ?>
                <p class="text-xs text-text-muted text-center py-4">No recent notification alerts.</p>
            <?php else: ?>
                <?php foreach ($recentNotifications as $notif): ?>
                    <div class="p-3.5 rounded-xl border border-slate-100 flex items-start justify-between text-xs bg-slate-50/50">
                        <div>
                            <p class="font-bold text-slate-800"><?= htmlspecialchars($notif['title']) ?></p>
                            <p class="text-text-muted mt-1"><?= htmlspecialchars($notif['message']) ?></p>
                            <span class="text-[10px] text-slate-400 mt-2 block"><?= date('F j, Y', strtotime($notif['created_at'])) ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

</div>

<!-- Chart.js Graph Initialization -->
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const ctx = document.getElementById('landlordChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug'],
                datasets: [
                    {
                        label: 'Views',
                        data: [120, 240, 310, 220, 480, 520, 600, 750],
                        backgroundColor: '#16A34A',
                        borderRadius: 4
                    },
                    {
                        label: 'Inquiries',
                        data: [5, 12, 14, 8, 18, 20, 24, 28],
                        backgroundColor: '#F59E0B',
                        borderRadius: 4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        grid: {
                            color: '#f1f5f9'
                        },
                        ticks: {
                            font: {
                                size: 10
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: 10
                            }
                        }
                    }
                }
            }
        });
    });
</script>

<?php
use App\Helpers\Flash;
?>

<!-- Tenant Dashboard Grid -->
<div class="space-y-6">
    
    <!-- Stats Cards Row -->
    <div class="grid grid-cols-2 lg:grid-cols-6 gap-4">
        <!-- Card 1: Favorites -->
        <div class="bg-white border border-slate-100 rounded-2xl p-4 shadow-sm flex flex-col justify-between h-24">
            <span class="text-[10px] uppercase font-bold tracking-wider text-slate-400">Favorites</span>
            <div class="flex items-baseline justify-between mt-2">
                <p class="text-xl font-extrabold text-slate-800"><?= $stats['favorites'] ?></p>
                <span class="text-red-500 text-xs"><i class="fa-solid fa-heart"></i></span>
            </div>
        </div>
        <!-- Card 2: Viewed -->
        <div class="bg-white border border-slate-100 rounded-2xl p-4 shadow-sm flex flex-col justify-between h-24">
            <span class="text-[10px] uppercase font-bold tracking-wider text-slate-400">Viewed</span>
            <div class="flex items-baseline justify-between mt-2">
                <p class="text-xl font-extrabold text-slate-800"><?= $stats['viewed'] ?></p>
                <span class="text-blue-500 text-xs"><i class="fa-solid fa-clock-rotate-left"></i></span>
            </div>
        </div>
        <!-- Card 3: Reviews -->
        <div class="bg-white border border-slate-100 rounded-2xl p-4 shadow-sm flex flex-col justify-between h-24">
            <span class="text-[10px] uppercase font-bold tracking-wider text-slate-400">Reviews</span>
            <div class="flex items-baseline justify-between mt-2">
                <p class="text-xl font-extrabold text-slate-800"><?= $stats['reviews'] ?></p>
                <span class="text-amber-500 text-xs"><i class="fa-solid fa-star"></i></span>
            </div>
        </div>
        <!-- Card 4: Active Inquiries -->
        <div class="bg-white border border-slate-100 rounded-2xl p-4 shadow-sm flex flex-col justify-between h-24">
            <span class="text-[10px] uppercase font-bold tracking-wider text-slate-400">Active Inquiries</span>
            <div class="flex items-baseline justify-between mt-2">
                <p class="text-xl font-extrabold text-slate-800"><?= $stats['active_inquiries'] ?></p>
                <span class="text-indigo-500 text-xs"><i class="fa-regular fa-paper-plane"></i></span>
            </div>
        </div>
        <!-- Card 5: Accepted Inquiries -->
        <div class="bg-white border border-slate-100 rounded-2xl p-4 shadow-sm flex flex-col justify-between h-24">
            <span class="text-[10px] uppercase font-bold tracking-wider text-slate-400">Accepted</span>
            <div class="flex items-baseline justify-between mt-2">
                <p class="text-xl font-extrabold text-slate-800"><?= $stats['accepted_inquiries'] ?></p>
                <span class="text-emerald-500 text-xs"><i class="fa-regular fa-circle-check"></i></span>
            </div>
        </div>
        <!-- Card 6: Notifications -->
        <div class="bg-white border border-slate-100 rounded-2xl p-4 shadow-sm flex flex-col justify-between h-24">
            <span class="text-[10px] uppercase font-bold tracking-wider text-slate-400">Unread Alerts</span>
            <div class="flex items-baseline justify-between mt-2">
                <p class="text-xl font-extrabold text-slate-800"><?= $stats['notifications'] ?></p>
                <span class="text-green-700 text-xs"><i class="fa-solid fa-bell"></i></span>
            </div>
        </div>
    </div>

    <!-- Chart & Recent Activity Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Search Trends Chart (2 Columns width) -->
        <div class="lg:col-span-2 bg-white rounded-2xl p-5 border border-slate-100 shadow-sm space-y-4">
            <h3 class="font-bold text-slate-800 text-sm">Monthly Rental Search activity</h3>
            <div class="h-64 relative">
                <canvas id="searchChart"></canvas>
            </div>
        </div>

        <!-- Recent Activities Feed (1 Column width) -->
        <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-sm space-y-4 flex flex-col justify-between">
            <div>
                <h3 class="font-bold text-slate-800 text-sm">Recent Activity</h3>
                <div class="space-y-4 divide-y divide-slate-50 mt-2">
                    <?php if (empty($recentActivities)): ?>
                        <p class="text-xs text-text-muted pt-3">No recent activities logged.</p>
                    <?php else: ?>
                        <?php foreach ($recentActivities as $activity): ?>
                            <div class="pt-3 flex items-start space-x-3 text-xs">
                                <span class="w-2 h-2 rounded-full mt-1.5 shrink-0 bg-primary"></span>
                                <div class="flex-grow space-y-0.5">
                                    <p class="text-slate-700 font-medium"><?= htmlspecialchars($activity['message']) ?></p>
                                    <span class="text-[10px] text-slate-400 block"><?= $activity['time'] ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
            <a href="<?= url('/tenant/profile') ?>" class="w-full text-center py-2 bg-slate-50 border border-slate-100 hover:bg-slate-100 text-xs font-semibold text-slate-700 rounded-lg mt-4 block">
                Manage Profile
            </a>
        </div>

    </div>

    <!-- Tenant Rental Inquiries -->
    <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-sm space-y-4">
        <div class="flex justify-between items-center">
            <h3 class="font-bold text-slate-800 text-sm">Recent Rental Inquiries</h3>
            <a href="<?= url('/tenant/inquiries') ?>" class="text-xs text-primary hover:underline font-semibold">View All</a>
        </div>
        <div class="overflow-x-auto">
            <?php if (empty($recentInquiries)): ?>
                <?php component('empty_states', [
                    'title' => 'No Inquiries Sent Yet',
                    'message' => 'Your recent rental requests will appear here once submitted.',
                    'icon' => 'fa-paper-plane',
                    'actionUrl' => '/browse',
                    'actionText' => 'Browse Houses'
                ]); ?>
            <?php else: ?>
                <table class="min-w-full divide-y divide-slate-100 text-left">
                    <thead class="bg-slate-50 text-[10px] font-bold uppercase tracking-wider text-slate-500">
                        <tr>
                            <th class="px-4 py-2.5">Property Name</th>
                            <th class="px-4 py-2.5">Landlord</th>
                            <th class="px-4 py-2.5">Message</th>
                            <th class="px-4 py-2.5">Date</th>
                            <th class="px-4 py-2.5">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white text-xs text-slate-750">
                        <?php foreach ($recentInquiries as $inquiry): ?>
                            <tr>
                                <td class="px-4 py-3 font-bold text-slate-850">
                                    <a href="<?= url('/house/' . $inquiry['house_id']) ?>" class="hover:text-primary transition-colors">
                                        <?= htmlspecialchars($inquiry['house_title']) ?>
                                    </a>
                                </td>
                                <td class="px-4 py-3 font-semibold text-slate-700">
                                    <?= htmlspecialchars($inquiry['landlord_name']) ?>
                                </td>
                                <td class="px-4 py-3 max-w-xs truncate text-text-muted">
                                    <?= htmlspecialchars($inquiry['message']) ?>
                                </td>
                                <td class="px-4 py-3 text-slate-400">
                                    <?= date('M d, Y', strtotime($inquiry['created_at'])) ?>
                                </td>
                                <td class="px-4 py-3">
                                    <?php 
                                    $statusType = 'warning';
                                    if ($inquiry['status'] === 'contacted' || $inquiry['status'] === 'accepted') $statusType = 'success';
                                    if ($inquiry['status'] === 'closed' || $inquiry['status'] === 'rejected') $statusType = 'neutral';
                                    ?>
                                    <?php component('badges', ['type' => $statusType, 'text' => ucfirst($inquiry['status'])]); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>

    <!-- Favorite Properties -->
    <div class="space-y-4 pt-4">
        <div class="flex justify-between items-center">
            <h3 class="font-bold text-slate-800 text-sm">Favorite Houses</h3>
            <a href="<?= url('/tenant/favorites') ?>" class="text-xs text-primary hover:underline font-semibold">View All</a>
        </div>
        <?php if (empty($favListings)): ?>
            <div class="p-8 text-center text-xs text-text-muted bg-white border border-slate-100 rounded-2xl">
                <i class="fa-solid fa-heart text-2xl text-slate-200 mb-2"></i>
                <p>Your favorite houses will appear here.</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($favListings as $listing): ?>
                    <?php component('house_card', $listing); ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Recently Viewed Properties -->
    <div class="space-y-4 pt-4">
        <div class="flex justify-between items-center">
            <h3 class="font-bold text-slate-800 text-sm">Recently Viewed Houses</h3>
            <a href="<?= url('/tenant/recently-viewed') ?>" class="text-xs text-primary hover:underline font-semibold">View All</a>
        </div>
        <?php if (empty($viewedListings)): ?>
            <div class="p-8 text-center text-xs text-text-muted bg-white border border-slate-100 rounded-2xl">
                <i class="fa-solid fa-clock-rotate-left text-2xl text-slate-200 mb-2"></i>
                <p>Properties you recently viewed will be tracked here.</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($viewedListings as $listing): ?>
                    <?php component('house_card', $listing); ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

</div>

<!-- Chart.js initialization script -->
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const ctx = document.getElementById('searchChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug'],
                datasets: [{
                    label: 'Properties Viewed',
                    data: [12, 19, 3, 5, 2, 28, 30, 38],
                    borderColor: '#16A34A',
                    backgroundColor: 'rgba(22, 163, 74, 0.05)',
                    fill: true,
                    tension: 0.4,
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
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

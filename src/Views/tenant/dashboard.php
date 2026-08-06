<!-- Tenant Dashboard Grid -->
<div class="space-y-6">
    
    <!-- Stats Cards Row -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card 1: Favorites -->
        <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm flex items-center justify-between">
            <div class="space-y-1">
                <span class="text-xs text-text-muted">Favorites</span>
                <p class="text-2xl font-extrabold text-slate-800"><?= $stats['favorites'] ?></p>
            </div>
            <span class="w-10 h-10 rounded-xl bg-red-50 text-red-500 flex items-center justify-center text-sm"><i class="fa-solid fa-heart"></i></span>
        </div>
        <!-- Card 2: Viewed -->
        <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm flex items-center justify-between">
            <div class="space-y-1">
                <span class="text-xs text-text-muted">Recently Viewed</span>
                <p class="text-2xl font-extrabold text-slate-800"><?= $stats['viewed'] ?></p>
            </div>
            <span class="w-10 h-10 rounded-xl bg-blue-50 text-blue-500 flex items-center justify-center text-sm"><i class="fa-solid fa-clock-rotate-left"></i></span>
        </div>
        <!-- Card 3: Reviews -->
        <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm flex items-center justify-between">
            <div class="space-y-1">
                <span class="text-xs text-text-muted">Reviews</span>
                <p class="text-2xl font-extrabold text-slate-800"><?= $stats['reviews'] ?></p>
            </div>
            <span class="w-10 h-10 rounded-xl bg-accent/15 text-accent flex items-center justify-center text-sm"><i class="fa-solid fa-star"></i></span>
        </div>
        <!-- Card 4: Notifications -->
        <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm flex items-center justify-between">
            <div class="space-y-1">
                <span class="text-xs text-text-muted">Unread Alerts</span>
                <p class="text-2xl font-extrabold text-slate-800"><?= $stats['notifications'] ?></p>
            </div>
            <span class="w-10 h-10 rounded-xl bg-green-50 text-green-700 flex items-center justify-center text-sm"><i class="fa-solid fa-bell"></i></span>
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
        <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-sm space-y-4">
            <h3 class="font-bold text-slate-800 text-sm">Recent Activity</h3>
            <div class="space-y-4 divide-y divide-slate-50">
                <?php foreach ($recentActivities as $activity): ?>
                    <div class="pt-3 flex items-start space-x-3 text-xs">
                        <span class="w-2 h-2 rounded-full mt-1.5 shrink-0 bg-primary"></span>
                        <div class="flex-grow space-y-0.5">
                            <p class="text-slate-700 font-medium"><?= $activity['message'] ?></p>
                            <span class="text-[10px] text-slate-400 block"><?= $activity['time'] ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

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

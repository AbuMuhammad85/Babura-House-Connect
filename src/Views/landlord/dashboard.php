<!-- Landlord Dashboard Grid -->
<div class="space-y-6">
    
    <!-- Stats Cards Row -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card 1: Active Listings -->
        <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm flex items-center justify-between">
            <div class="space-y-1">
                <span class="text-xs text-text-muted">Active Listings</span>
                <p class="text-2xl font-extrabold text-slate-800"><?= $stats['active_listings'] ?></p>
            </div>
            <span class="w-10 h-10 rounded-xl bg-green-50 text-green-700 flex items-center justify-center text-sm"><i class="fa-solid fa-house-user"></i></span>
        </div>
        <!-- Card 2: Views -->
        <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm flex items-center justify-between">
            <div class="space-y-1">
                <span class="text-xs text-text-muted">Total Views</span>
                <p class="text-2xl font-extrabold text-slate-800"><?= number_format($stats['total_views']) ?></p>
            </div>
            <span class="w-10 h-10 rounded-xl bg-blue-50 text-blue-500 flex items-center justify-center text-sm"><i class="fa-solid fa-eye"></i></span>
        </div>
        <!-- Card 3: Inquiries -->
        <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm flex items-center justify-between">
            <div class="space-y-1">
                <span class="text-xs text-text-muted">Bids / Inquiries</span>
                <p class="text-2xl font-extrabold text-slate-800"><?= $stats['inquiries'] ?></p>
            </div>
            <span class="w-10 h-10 rounded-xl bg-accent/15 text-accent flex items-center justify-center text-sm"><i class="fa-solid fa-message"></i></span>
        </div>
        <!-- Card 4: Earnings -->
        <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm flex items-center justify-between">
            <div class="space-y-1">
                <span class="text-xs text-text-muted">Monthly Earnings</span>
                <p class="text-2xl font-extrabold text-slate-800"><?= formatNaira($stats['monthly_earnings']) ?></p>
            </div>
            <span class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center text-sm"><i class="fa-solid fa-naira-sign"></i></span>
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
        <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-sm space-y-4 flex flex-col justify-between">
            <div>
                <h3 class="font-bold text-slate-800 text-sm">Recent Inquiries</h3>
                <div class="mt-4 space-y-4">
                    <?php foreach ($recentInquiries as $inquiry): ?>
                        <div class="flex justify-between items-center text-xs">
                            <div class="space-y-0.5">
                                <p class="font-bold text-slate-800"><?= $inquiry['name'] ?></p>
                                <p class="text-[10px] text-text-muted"><?= $inquiry['house'] ?></p>
                            </div>
                            <div class="text-right space-y-1">
                                <span class="text-[10px] text-slate-400 block"><?= $inquiry['date'] ?></span>
                                <span class="px-2 py-0.5 rounded-full text-[9px] font-semibold bg-slate-100 text-slate-600"><?= $inquiry['status'] ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <a href="<?= url('/landlord/listings') ?>" class="w-full text-center py-2 bg-slate-50 border border-slate-100 hover:bg-slate-100 text-xs font-semibold text-slate-700 rounded-lg mt-4 block">
                Manage Listings
            </a>
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

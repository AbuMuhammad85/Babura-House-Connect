<!-- Admin Control Panel Dashboard Grid -->
<div class="space-y-6">
    
    <!-- Stats Row -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card 1: Users -->
        <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm flex items-center justify-between">
            <div class="space-y-1">
                <span class="text-xs text-text-muted">Total Active Users</span>
                <p class="text-2xl font-extrabold text-slate-800"><?= number_format($stats['total_users']) ?></p>
            </div>
            <span class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-sm"><i class="fa-solid fa-users"></i></span>
        </div>
        <!-- Card 2: Landlords -->
        <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm flex items-center justify-between">
            <div class="space-y-1">
                <span class="text-xs text-text-muted">Registered Landlords</span>
                <p class="text-2xl font-extrabold text-slate-800"><?= $stats['total_landlords'] ?></p>
            </div>
            <span class="w-10 h-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center text-sm"><i class="fa-solid fa-user-tie"></i></span>
        </div>
        <!-- Card 3: Houses -->
        <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm flex items-center justify-between">
            <div class="space-y-1">
                <span class="text-xs text-text-muted">Total Properties</span>
                <p class="text-2xl font-extrabold text-slate-800"><?= $stats['total_listings'] ?></p>
            </div>
            <span class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-sm"><i class="fa-solid fa-house-circle-check"></i></span>
        </div>
        <!-- Card 4: Pending -->
        <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm flex items-center justify-between animate-pulse">
            <div class="space-y-1">
                <span class="text-xs text-text-muted">Pending Verifications</span>
                <p class="text-2xl font-extrabold text-red-600"><?= $stats['pending_verifications'] ?></p>
            </div>
            <span class="w-10 h-10 rounded-xl bg-red-50 text-red-600 flex items-center justify-center text-sm"><i class="fa-solid fa-shield-exclamation"></i></span>
        </div>
    </div>

    <!-- Analytics & Actions Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Graph (2 Columns) -->
        <div class="lg:col-span-2 bg-white rounded-2xl p-5 border border-slate-100 shadow-sm space-y-4">
            <h3 class="font-bold text-slate-800 text-sm">Platform Monthly Growth</h3>
            <div class="h-64 relative">
                <canvas id="adminChart"></canvas>
            </div>
        </div>

        <!-- Pending Verifications Action Panel (1 Column) -->
        <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-sm space-y-4 flex flex-col justify-between">
            <div>
                <h3 class="font-bold text-slate-800 text-sm">Urgent Verifications</h3>
                <div class="mt-4 space-y-4">
                    <?php foreach ($pendingLandlords as $landlord): ?>
                        <div class="flex justify-between items-center text-xs">
                            <div>
                                <p class="font-bold text-slate-800"><?= $landlord['name'] ?></p>
                                <p class="text-[10px] text-text-muted"><?= $landlord['phone'] ?></p>
                            </div>
                            <a href="<?= url('/admin/verify-landlords') ?>" class="px-2.5 py-1 bg-primary/10 hover:bg-primary text-primary hover:text-white transition-all rounded-lg font-semibold text-[10px]">
                                Review
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <a href="<?= url('/admin/verify-landlords') ?>" class="w-full text-center py-2 bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold rounded-lg mt-4 block">
                Manage Landlords
            </a>
        </div>

    </div>
</div>

<!-- Chart.js Graph Initialization -->
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const ctx = document.getElementById('adminChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug'],
                datasets: [
                    {
                        label: 'Total Tenants',
                        data: [200, 420, 580, 710, 890, 1020, 1150, 1240],
                        borderColor: '#3B82F6',
                        fill: false,
                        tension: 0.3
                    },
                    {
                        label: 'Total Properties',
                        data: [50, 110, 180, 240, 310, 390, 440, 482],
                        borderColor: '#16A34A',
                        fill: false,
                        tension: 0.3
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    });
</script>

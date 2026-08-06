<div class="space-y-6">
    <div>
        <h2 class="text-xl font-bold text-slate-800">Platform Analytics</h2>
        <p class="text-xs text-text-muted mt-1">Audit billing revenue, active listings, and user distribution metrics across Jigawa State.</p>
    </div>

    <!-- Analytics Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Revenue Chart -->
        <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm space-y-4">
            <h3 class="font-bold text-slate-800 text-sm">Monthly Subscription Revenue (₦)</h3>
            <div class="h-64 relative">
                <canvas id="revChart"></canvas>
            </div>
        </div>

        <!-- Density Chart -->
        <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm space-y-4">
            <h3 class="font-bold text-slate-800 text-sm">User Distribution Tiers</h3>
            <div class="h-64 relative">
                <canvas id="userDistChart"></canvas>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Revenue Chart
        const ctxRev = document.getElementById('revChart').getContext('2d');
        new Chart(ctxRev, {
            type: 'bar',
            data: {
                labels: ['Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug'],
                datasets: [{
                    label: 'Revenue Earned (₦)',
                    data: [150000, 310000, 280000, 490000, 520000, 680000],
                    backgroundColor: '#16A34A',
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });

        // User Distribution Chart
        const ctxDist = document.getElementById('userDistChart').getContext('2d');
        new Chart(ctxDist, {
            type: 'pie',
            data: {
                labels: ['Tenants', 'Standard Landlords', 'Premium Landlords'],
                datasets: [{
                    data: [1240, 120, 36],
                    backgroundColor: ['#3B82F6', '#F59E0B', '#16A34A']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    });
</script>

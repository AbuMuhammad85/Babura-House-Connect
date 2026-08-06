<div class="space-y-6">
    <div>
        <h2 class="text-xl font-bold text-slate-800">Listing Analytics</h2>
        <p class="text-xs text-text-muted mt-1">Review views, engagement, and geographic search patterns of prospects.</p>
    </div>

    <!-- Analytics Graphs -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- View Traffic Over Time -->
        <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm space-y-4">
            <h3 class="font-bold text-slate-800 text-sm">Monthly Traffic Views</h3>
            <div class="h-64 relative">
                <canvas id="trafficChart"></canvas>
            </div>
        </div>

        <!-- Inquiry Breakdown -->
        <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm space-y-4">
            <h3 class="font-bold text-slate-800 text-sm">Tenant Source Locations (Jigawa)</h3>
            <div class="h-64 relative">
                <canvas id="locationChart"></canvas>
            </div>
        </div>

    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Traffic Chart
        const ctxTraffic = document.getElementById('trafficChart').getContext('2d');
        new Chart(ctxTraffic, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug'],
                datasets: [{
                    label: 'Page Views',
                    data: [150, 290, 430, 390, 620, 710, 880, 1020],
                    borderColor: '#16A34A',
                    backgroundColor: 'rgba(22, 163, 74, 0.05)',
                    fill: true,
                    tension: 0.4,
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });

        // Location Source Chart
        const ctxLoc = document.getElementById('locationChart').getContext('2d');
        new Chart(ctxLoc, {
            type: 'doughnut',
            data: {
                labels: ['Babura Town', 'Dutse', 'Gumel', 'Hadejia', 'Others'],
                datasets: [{
                    data: [65, 15, 10, 5, 5],
                    backgroundColor: ['#16A34A', '#3B82F6', '#F59E0B', '#EF4444', '#94A3B8']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    });
</script>

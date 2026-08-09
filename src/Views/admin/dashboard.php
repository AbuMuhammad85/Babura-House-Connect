<!-- Admin Control Panel Dashboard -->
<div class="space-y-6">
    
    <div>
        <h2 class="text-xl font-bold text-slate-800">Admin Dashboard</h2>
        <p class="text-xs text-text-muted mt-1">Platform overview, statistics aggregates, moderation queue status, and quick control panel actions.</p>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- User Metrics -->
        <div class="bg-white border border-slate-100 rounded-2xl p-4 shadow-sm space-y-3">
            <div class="flex items-center justify-between">
                <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">User Directory</span>
                <span class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-xs"><i class="fa-solid fa-users"></i></span>
            </div>
            <div class="grid grid-cols-2 gap-2 text-xs">
                <div>
                    <p class="text-[10px] text-text-muted">Total Users</p>
                    <p class="font-extrabold text-slate-800 text-sm"><?= number_format($stats['total_users']) ?></p>
                </div>
                <div>
                    <p class="text-[10px] text-text-muted">Tenants</p>
                    <p class="font-extrabold text-slate-800 text-sm"><?= number_format($stats['total_tenants']) ?></p>
                </div>
                <div>
                    <p class="text-[10px] text-text-muted">Landlords</p>
                    <p class="font-extrabold text-slate-800 text-sm"><?= number_format($stats['total_landlords']) ?></p>
                </div>
                <div>
                    <p class="text-[10px] text-text-muted">Verified</p>
                    <p class="font-extrabold text-green-600 text-sm"><?= number_format($stats['verified_landlords']) ?></p>
                </div>
            </div>
        </div>

        <!-- Property Metrics -->
        <div class="bg-white border border-slate-100 rounded-2xl p-4 shadow-sm space-y-3">
            <div class="flex items-center justify-between">
                <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Properties</span>
                <span class="w-8 h-8 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center text-xs"><i class="fa-solid fa-house-chimney"></i></span>
            </div>
            <div class="grid grid-cols-2 gap-2 text-xs">
                <div>
                    <p class="text-[10px] text-text-muted">Total Houses</p>
                    <p class="font-extrabold text-slate-800 text-sm"><?= number_format($stats['total_houses']) ?></p>
                </div>
                <div>
                    <p class="text-[10px] text-text-muted">Pending</p>
                    <p class="font-extrabold text-amber-600 text-sm"><?= number_format($stats['pending_listings']) ?></p>
                </div>
                <div>
                    <p class="text-[10px] text-text-muted">Published</p>
                    <p class="font-extrabold text-green-600 text-sm"><?= number_format($stats['published_houses']) ?></p>
                </div>
                <div>
                    <p class="text-[10px] text-text-muted">Inactive</p>
                    <p class="font-extrabold text-slate-400 text-sm"><?= number_format($stats['inactive_houses']) ?></p>
                </div>
            </div>
        </div>

        <!-- Interactions & Moderation Queue -->
        <div class="bg-white border border-slate-100 rounded-2xl p-4 shadow-sm space-y-3">
            <div class="flex items-center justify-between">
                <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Feedbacks & Complaints</span>
                <span class="w-8 h-8 rounded-lg bg-red-50 text-red-600 flex items-center justify-center text-xs"><i class="fa-solid fa-comments"></i></span>
            </div>
            <div class="grid grid-cols-2 gap-2 text-xs">
                <div>
                    <p class="text-[10px] text-text-muted">Total Reviews</p>
                    <p class="font-extrabold text-slate-800 text-sm"><?= number_format($stats['total_reviews']) ?></p>
                </div>
                <div>
                    <p class="text-[10px] text-text-muted">Pending Inquiries</p>
                    <p class="font-extrabold text-slate-800 text-sm"><?= number_format($stats['pending_inquiries']) ?></p>
                </div>
                <div>
                    <p class="text-[10px] text-text-muted">Pending Reports</p>
                    <p class="font-extrabold text-red-650 text-sm"><?= number_format($stats['pending_reports']) ?></p>
                </div>
                <div>
                    <p class="text-[10px] text-text-muted">Unread Alerts</p>
                    <p class="font-extrabold text-red-600 text-sm"><?= number_format($stats['unread_admin_notifications']) ?></p>
                </div>
            </div>
        </div>

        <!-- Urgencies Summary Box -->
        <div class="bg-white border border-slate-100 rounded-2xl p-4 shadow-sm space-y-3 flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Pending Tasks</span>
                <span class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center text-xs"><i class="fa-solid fa-bell"></i></span>
            </div>
            <div class="space-y-1.5">
                <div class="flex justify-between items-center text-xs">
                    <span class="text-text-muted">Landlords to Verify</span>
                    <span class="font-bold text-slate-800 bg-slate-100 px-2 py-0.5 rounded-full text-[10px]"><?= $stats['pending_verifications'] ?></span>
                </div>
                <div class="flex justify-between items-center text-xs">
                    <span class="text-text-muted">Pending Listings</span>
                    <span class="font-bold text-slate-800 bg-slate-100 px-2 py-0.5 rounded-full text-[10px]"><?= $stats['pending_listings'] ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions Panel -->
    <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-sm space-y-4">
        <h3 class="font-bold text-slate-800 text-sm border-b border-slate-50 pb-2">Administrative Quick Actions</h3>
        <div class="grid grid-cols-2 md:grid-cols-6 gap-3">
            <a href="<?= url('/admin/verify-landlords') ?>" class="flex flex-col items-center justify-center p-4 bg-slate-50 hover:bg-slate-100 border border-slate-100 rounded-xl text-center transition-all group">
                <i class="fa-solid fa-user-shield text-base text-primary mb-2 transition-transform group-hover:scale-110"></i>
                <span class="text-[10px] font-bold text-slate-700">Verifications</span>
                <span class="text-[9px] text-text-muted mt-0.5"><?= $stats['pending_verifications'] ?> Pending</span>
            </a>
            <a href="<?= url('/admin/manage-houses') ?>" class="flex flex-col items-center justify-center p-4 bg-slate-50 hover:bg-slate-100 border border-slate-100 rounded-xl text-center transition-all group">
                <i class="fa-solid fa-house-circle-exclamation text-base text-purple-600 mb-2 transition-transform group-hover:scale-110"></i>
                <span class="text-[10px] font-bold text-slate-700">Listings</span>
                <span class="text-[9px] text-text-muted mt-0.5"><?= $stats['pending_listings'] ?> Pending</span>
            </a>
            <a href="<?= url('/admin/reports') ?>" class="flex flex-col items-center justify-center p-4 bg-slate-50 hover:bg-slate-100 border border-slate-100 rounded-xl text-center transition-all group">
                <i class="fa-solid fa-triangle-exclamation text-base text-red-600 mb-2 transition-transform group-hover:scale-110"></i>
                <span class="text-[10px] font-bold text-slate-700">Complaints</span>
                <span class="text-[9px] text-text-muted mt-0.5"><?= $stats['pending_reports'] ?> Active</span>
            </a>
            <a href="<?= url('/admin/manage-users') ?>" class="flex flex-col items-center justify-center p-4 bg-slate-50 hover:bg-slate-100 border border-slate-100 rounded-xl text-center transition-all group">
                <i class="fa-solid fa-users text-base text-blue-600 mb-2 transition-transform group-hover:scale-110"></i>
                <span class="text-[10px] font-bold text-slate-700">Users Directory</span>
                <span class="text-[9px] text-text-muted mt-0.5">Manage users</span>
            </a>
            <a href="<?= url('/admin/manage-reviews') ?>" class="flex flex-col items-center justify-center p-4 bg-slate-50 hover:bg-slate-100 border border-slate-100 rounded-xl text-center transition-all group">
                <i class="fa-solid fa-comments text-base text-amber-600 mb-2 transition-transform group-hover:scale-110"></i>
                <span class="text-[10px] font-bold text-slate-700">Moderate Reviews</span>
                <span class="text-[9px] text-text-muted mt-0.5">View & hide</span>
            </a>
            <a href="<?= url('/admin/areas') ?>" class="flex flex-col items-center justify-center p-4 bg-slate-50 hover:bg-slate-100 border border-slate-100 rounded-xl text-center transition-all group">
                <i class="fa-solid fa-map-location-dot text-base text-green-600 mb-2 transition-transform group-hover:scale-110"></i>
                <span class="text-[10px] font-bold text-slate-700">Areas Configuration</span>
                <span class="text-[9px] text-text-muted mt-0.5">Babura local zones</span>
            </a>
        </div>
    </div>

    <!-- Secondary Dashboard Info Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Urgent Verifications Action Queue (1 Column) -->
        <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-sm flex flex-col justify-between space-y-4">
            <div>
                <h3 class="font-bold text-slate-800 text-sm border-b border-slate-50 pb-2">Verification Alerts</h3>
                <div class="mt-4 space-y-4">
                    <?php if (empty($pendingLandlords)): ?>
                        <div class="py-6 text-center text-xs text-text-muted">
                            <i class="fa-solid fa-circle-check text-green-500 text-lg mb-1 block"></i>
                            Queue is clean
                        </div>
                    <?php else: ?>
                        <?php foreach ($pendingLandlords as $landlord): ?>
                            <div class="flex justify-between items-center text-xs">
                                <div>
                                    <p class="font-bold text-slate-800"><?= htmlspecialchars($landlord['name']) ?></p>
                                    <p class="text-[10px] text-text-muted"><?= htmlspecialchars($landlord['phone']) ?></p>
                                </div>
                                <a href="<?= url('/admin/verify-landlords') ?>" class="px-2.5 py-1 bg-primary/10 hover:bg-primary text-primary hover:text-white transition-all rounded-lg font-semibold text-[10px]">
                                    Review
                                </a>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
            <a href="<?= url('/admin/verify-landlords') ?>" class="w-full text-center py-2 bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold rounded-lg mt-4 block">
                Manage Landlords
            </a>
        </div>

        <!-- Recent System Activity (2 Columns) -->
        <div class="lg:col-span-2 bg-white rounded-2xl p-5 border border-slate-100 shadow-sm space-y-4">
            <h3 class="font-bold text-slate-800 text-sm border-b border-slate-50 pb-2">Recent System Activity</h3>
            <div class="flow-root">
                <ul role="list" class="-mb-8">
                    <?php if (empty($recentActivities)): ?>
                        <div class="py-12 text-center text-xs text-text-muted">No activity logs found.</div>
                    <?php else: ?>
                        <?php foreach ($recentActivities as $idx => $act): ?>
                            <li>
                                <div class="relative pb-8">
                                    <?php if ($idx !== count($recentActivities) - 1): ?>
                                        <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-slate-100" aria-hidden="true"></span>
                                    <?php endif; ?>
                                    <div class="relative flex space-x-3 text-xs">
                                        <div>
                                            <span class="h-8 w-8 rounded-full bg-slate-100 text-slate-500 flex items-center justify-center ring-8 ring-white">
                                                <i class="fa-solid fa-clock-rotate-left"></i>
                                            </span>
                                        </div>
                                        <div class="flex-grow min-w-0 pt-1.5 flex justify-between space-x-4">
                                            <div>
                                                <p class="font-bold text-slate-800"><?= htmlspecialchars($act['description']) ?></p>
                                                <p class="text-[10px] text-text-muted mt-0.5">By: <span class="font-semibold"><?= htmlspecialchars($act['user_name'] ?? 'System/Guest') ?></span> (IP: <?= htmlspecialchars($act['ip_address']) ?>)</p>
                                            </div>
                                            <div class="whitespace-nowrap text-right text-[10px] text-text-muted">
                                                <time><?= date('M d, H:i', strtotime($act['created_at'])) ?></time>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>
        </div>

    </div>
</div>

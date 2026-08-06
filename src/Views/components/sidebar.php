<?php
$role = $role ?? 'tenant';
$links = [];

if ($role === 'tenant') {
    $links = [
        ['label' => 'Dashboard', 'url' => '/tenant/dashboard', 'icon' => 'fa-gauge'],
        ['label' => 'My Favorites', 'url' => '/tenant/favorites', 'icon' => 'fa-heart'],
        ['label' => 'Recently Viewed', 'url' => '/tenant/recently-viewed', 'icon' => 'fa-clock-rotate-left'],
        ['label' => 'Notifications', 'url' => '/tenant/notifications', 'icon' => 'fa-bell'],
        ['label' => 'My Reviews', 'url' => '/tenant/reviews', 'icon' => 'fa-star'],
        ['label' => 'My Profile', 'url' => '/tenant/profile', 'icon' => 'fa-user'],
        ['label' => 'Settings', 'url' => '/tenant/settings', 'icon' => 'fa-gear'],
    ];
    $portalName = "Tenant Portal";
} elseif ($role === 'landlord') {
    $links = [
        ['label' => 'Dashboard', 'url' => '/landlord/dashboard', 'icon' => 'fa-gauge'],
        ['label' => 'Verification', 'url' => '/landlord/verification', 'icon' => 'fa-shield-halved'],
        ['label' => 'Add House', 'url' => '/landlord/add-house', 'icon' => 'fa-circle-plus'],
        ['label' => 'Manage Listings', 'url' => '/landlord/listings', 'icon' => 'fa-house-user'],
        ['label' => 'Analytics', 'url' => '/landlord/analytics', 'icon' => 'fa-chart-pie'],
        ['label' => 'Subscription', 'url' => '/landlord/subscription', 'icon' => 'fa-credit-card'],
        ['label' => 'Profile', 'url' => '/landlord/profile', 'icon' => 'fa-user'],
        ['label' => 'Settings', 'url' => '/landlord/settings', 'icon' => 'fa-gear'],
    ];
    $portalName = "Landlord Portal";
} elseif ($role === 'admin') {
    $links = [
        ['label' => 'Dashboard', 'url' => '/admin/dashboard', 'icon' => 'fa-gauge'],
        ['label' => 'Verify Landlords', 'url' => '/admin/verify-landlords', 'icon' => 'fa-user-shield'],
        ['label' => 'Manage Houses', 'url' => '/admin/manage-houses', 'icon' => 'fa-house-laptop'],
        ['label' => 'Manage Users', 'url' => '/admin/manage-users', 'icon' => 'fa-users'],
        ['label' => 'Manage Reviews', 'url' => '/admin/manage-reviews', 'icon' => 'fa-comments'],
        ['label' => 'Reports', 'url' => '/admin/reports', 'icon' => 'fa-file-lines'],
        ['label' => 'Subscriptions', 'url' => '/admin/subscriptions', 'icon' => 'fa-wallet'],
        ['label' => 'Analytics', 'url' => '/admin/analytics', 'icon' => 'fa-chart-line'],
        ['label' => 'Settings', 'url' => '/admin/settings', 'icon' => 'fa-gears'],
    ];
    $portalName = "Admin Control";
}
?>

<!-- Mobile Sidebar Backdrop -->
<div class="fixed inset-0 z-40 bg-slate-900/50 backdrop-blur-sm lg:hidden" 
     x-show="sidebarOpen" 
     @click="sidebarOpen = false" 
     x-transition:enter="transition ease-in-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in-out duration-300"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     x-cloak>
</div>

<!-- Sidebar Sidebar Container -->
<aside class="fixed inset-y-0 left-0 z-50 flex flex-col w-64 bg-slate-900 text-slate-400 border-r border-slate-800 transition-transform duration-300 transform lg:translate-x-0 lg:static lg:inset-0 shrink-0"
       :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">
    
    <!-- Sidebar Header logo -->
    <div class="flex items-center justify-between h-16 px-4 bg-slate-950 border-b border-slate-800">
        <a href="<?= url('/') ?>" class="flex items-center space-x-2 text-white">
            <span class="p-1.5 rounded-lg bg-primary text-white">
                <i class="fa-solid fa-house-chimney text-base"></i>
            </span>
            <span class="font-bold text-base tracking-tight">House Connect</span>
        </a>
        <button @click="sidebarOpen = false" class="text-slate-400 hover:text-white lg:hidden" aria-label="Close sidebar">
            <i class="fa-solid fa-xmark text-lg"></i>
        </button>
    </div>

    <!-- Active Portal label -->
    <div class="px-4 py-3 bg-slate-950/40 border-b border-slate-800/60">
        <p class="text-xs uppercase tracking-wider text-slate-500 font-semibold"><?= $portalName ?></p>
    </div>

    <!-- Navigation Links -->
    <nav class="flex-grow py-4 px-2 space-y-1 overflow-y-auto">
        <?php foreach ($links as $link): ?>
            <?php $active = isActive($link['url'], 'bg-primary text-white font-medium') ?: 'hover:bg-slate-800/50 hover:text-slate-200'; ?>
            <a href="<?= url($link['url']) ?>" 
               class="flex items-center px-4 py-2.5 rounded-lg text-sm transition-all group <?= $active ?>">
                <i class="fa-solid <?= $link['icon'] ?> text-base mr-3 <?= isActive($link['url'], 'text-white') ?: 'text-slate-500 group-hover:text-slate-300' ?>"></i>
                <span><?= $link['label'] ?></span>
            </a>
        <?php endforeach; ?>
    </nav>

    <!-- Sidebar Footer / Logout -->
    <div class="p-4 border-t border-slate-800 bg-slate-950/20">
        <a href="<?= url('/logout') ?>" 
           class="flex items-center px-4 py-2.5 rounded-lg text-sm text-red-400 hover:bg-red-500/10 hover:text-red-300 transition-all font-medium">
            <i class="fa-solid fa-right-from-bracket text-base mr-3"></i>
            <span>Log Out</span>
        </a>
    </div>
</aside>

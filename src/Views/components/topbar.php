<?php
$role = $role ?? 'tenant';
$username = 'Garba Danladi';
$userAvatar = '/assets/images/tenant_avatar.jpg';

if ($role === 'landlord') {
    $username = 'Alhaji Ibrahim Babura';
    $userAvatar = '/assets/images/landlord_avatar.jpg';
} elseif ($role === 'admin') {
    $username = 'Admin System';
    $userAvatar = '/assets/images/admin_avatar.jpg';
}
?>

<header class="flex items-center justify-between h-16 px-4 md:px-6 bg-white border-b border-slate-100 shadow-sm shrink-0">
    <div class="flex items-center space-x-4">
        <!-- Mobile Sidebar Toggle -->
        <button @click="sidebarOpen = !sidebarOpen" class="p-2 rounded-lg text-slate-500 hover:bg-slate-100 lg:hidden" aria-label="Open sidebar">
            <i class="fa-solid fa-bars text-lg"></i>
        </button>
        
        <!-- Page Title -->
        <h1 class="text-lg font-semibold text-slate-800 hidden sm:block"><?= $title ?? 'Dashboard' ?></h1>
    </div>

    <!-- Right Side Actions -->
    <div class="flex items-center space-x-4">
        <!-- Notification Dropdown -->
        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open" class="p-2 rounded-lg text-slate-500 hover:bg-slate-100 hover:text-primary relative transition-colors" aria-label="Open notifications">
                <i class="fa-solid fa-bell text-lg"></i>
                <span class="absolute top-1 right-1 w-2 h-2 bg-accent rounded-full border border-white"></span>
            </button>
            
            <!-- Dropdown Content -->
            <div x-show="open" @click.away="open = false" 
                 x-transition:enter="transition ease-out duration-100"
                 x-transition:enter-start="transform opacity-0 scale-95"
                 x-transition:enter-end="transform opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-75"
                 x-transition:leave-start="transform opacity-100 scale-100"
                 x-transition:leave-end="transform opacity-0 scale-95"
                 class="absolute right-0 mt-2 w-80 bg-white border border-slate-100 rounded-xl shadow-xl z-50 py-2" x-cloak>
                <div class="px-4 py-2 border-b border-slate-100 flex justify-between items-center">
                    <span class="font-semibold text-sm text-text-main">Notifications</span>
                    <a href="<?= url('/' . $role . '/notifications') ?>" class="text-xs text-primary hover:underline">View All</a>
                </div>
                <div class="max-h-64 overflow-y-auto divide-y divide-slate-100">
                    <div class="px-4 py-3 hover:bg-slate-50 transition-colors">
                        <p class="text-xs font-semibold text-slate-700">Rent Due Reminder</p>
                        <p class="text-xs text-text-muted mt-1">Your rental payment for Luxury 3 Bedroom Flat is due soon.</p>
                        <span class="text-[10px] text-slate-400 block mt-1">3 hours ago</span>
                    </div>
                    <div class="px-4 py-3 hover:bg-slate-50 transition-colors">
                        <p class="text-xs font-semibold text-slate-700">Listing Approved</p>
                        <p class="text-xs text-text-muted mt-1">Your bungalow listing in Sabo Gari is now online.</p>
                        <span class="text-[10px] text-slate-400 block mt-1">1 day ago</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Profile Dropdown -->
        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open" class="flex items-center space-x-2 p-1.5 rounded-xl hover:bg-slate-100 transition-colors" aria-label="Profile menu">
                <span class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center font-bold text-sm uppercase">
                    <?= substr($username, 0, 2) ?>
                </span>
                <span class="text-sm font-medium text-slate-700 hidden md:block"><?= $username ?></span>
                <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 hidden md:block"></i>
            </button>

            <!-- Profile Dropdown Content -->
            <div x-show="open" @click.away="open = false" 
                 x-transition:enter="transition ease-out duration-100"
                 x-transition:enter-start="transform opacity-0 scale-95"
                 x-transition:enter-end="transform opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-75"
                 x-transition:leave-start="transform opacity-100 scale-100"
                 x-transition:leave-end="transform opacity-0 scale-95"
                 class="absolute right-0 mt-2 w-48 bg-white border border-slate-100 rounded-xl shadow-xl z-50 py-1" x-cloak>
                <div class="px-4 py-2 border-b border-slate-100">
                    <p class="text-xs text-slate-400 font-medium">Logged in as</p>
                    <p class="text-sm font-semibold text-slate-700 truncate"><?= $username ?></p>
                </div>
                <a href="<?= url('/' . $role . '/profile') ?>" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 hover:text-primary transition-colors"><i class="fa-regular fa-user mr-2"></i> My Profile</a>
                <a href="<?= url('/' . $role . '/settings') ?>" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 hover:text-primary transition-colors"><i class="fa-regular fa-compass mr-2"></i> Settings</a>
                <div class="border-t border-slate-100 my-1"></div>
                <a href="<?= url('/logout') ?>" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50 hover:text-red-700 transition-colors"><i class="fa-solid fa-right-from-bracket mr-2"></i> Log Out</a>
            </div>
        </div>
    </div>
</header>

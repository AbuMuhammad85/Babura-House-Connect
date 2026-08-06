<nav class="sticky top-0 z-50 bg-white/90 backdrop-blur-md shadow-sm border-b border-slate-100" x-data="{ mobileOpen: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <!-- Logo -->
                <a href="<?= url('/') ?>" class="flex items-center space-x-2">
                    <span class="p-2 rounded-lg bg-primary/10 text-primary">
                        <i class="fa-solid fa-house-chimney text-lg"></i>
                    </span>
                    <span class="font-bold text-xl tracking-tight text-text-main">
                        Babura House <span class="text-primary font-semibold">Connect</span>
                    </span>
                </a>
            </div>
            
            <!-- Desktop Links -->
            <div class="hidden md:flex items-center space-x-8">
                <a href="<?= url('/') ?>" class="text-sm font-medium hover:text-primary transition-colors py-5 <?= isActive('/', 'text-primary border-b-2 border-primary') ?> <?= !isActive('/', 'text-text-muted') ?>">Home</a>
                <a href="<?= url('/browse') ?>" class="text-sm font-medium hover:text-primary transition-colors py-5 <?= isActive('/browse', 'text-primary border-b-2 border-primary') ?> <?= !isActive('/browse', 'text-text-muted') ?>">Browse Houses</a>
                <a href="<?= url('/about') ?>" class="text-sm font-medium hover:text-primary transition-colors py-5 <?= isActive('/about', 'text-primary border-b-2 border-primary') ?> <?= !isActive('/about', 'text-text-muted') ?>">About Us</a>
                <a href="<?= url('/contact') ?>" class="text-sm font-medium hover:text-primary transition-colors py-5 <?= isActive('/contact', 'text-primary border-b-2 border-primary') ?> <?= !isActive('/contact', 'text-text-muted') ?>">Contact</a>
            </div>

            <!-- CTA Buttons -->
            <div class="hidden md:flex items-center space-x-4">
                <a href="<?= url('/login') ?>" class="text-sm font-medium text-text-muted hover:text-primary transition-colors">Sign In</a>
                <a href="<?= url('/register') ?>" class="px-4 py-2 rounded-lg text-sm font-medium bg-primary text-white hover:bg-primary/90 shadow-sm shadow-primary/20 hover:shadow-lg transition-all transform hover:-translate-y-0.5">Register</a>
            </div>

            <!-- Mobile Menu Button -->
            <div class="flex items-center md:hidden">
                <button @click="mobileOpen = !mobileOpen" class="p-2 rounded-md text-text-muted hover:text-primary hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-primary/20" aria-label="Toggle menu">
                    <i class="fa-solid text-lg" :class="mobileOpen ? 'fa-xmark' : 'fa-bars'"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div class="md:hidden bg-white border-b border-slate-100" x-show="mobileOpen" x-transition.opacity x-cloak>
        <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3 shadow-inner">
            <a href="<?= url('/') ?>" class="block px-3 py-2 rounded-md text-base font-medium hover:bg-slate-50 hover:text-primary transition-all <?= isActive('/', 'bg-primary/10 text-primary') ?> <?= !isActive('/', 'text-text-muted') ?>">Home</a>
            <a href="<?= url('/browse') ?>" class="block px-3 py-2 rounded-md text-base font-medium hover:bg-slate-50 hover:text-primary transition-all <?= isActive('/browse', 'bg-primary/10 text-primary') ?> <?= !isActive('/browse', 'text-text-muted') ?>">Browse Houses</a>
            <a href="<?= url('/about') ?>" class="block px-3 py-2 rounded-md text-base font-medium hover:bg-slate-50 hover:text-primary transition-all <?= isActive('/about', 'bg-primary/10 text-primary') ?> <?= !isActive('/about', 'text-text-muted') ?>">About Us</a>
            <a href="<?= url('/contact') ?>" class="block px-3 py-2 rounded-md text-base font-medium hover:bg-slate-50 hover:text-primary transition-all <?= isActive('/contact', 'bg-primary/10 text-primary') ?> <?= !isActive('/contact', 'text-text-muted') ?>">Contact</a>
            
            <div class="pt-4 pb-2 border-t border-slate-100 mt-2 flex flex-col space-y-2 px-3">
                <a href="<?= url('/login') ?>" class="w-full text-center py-2 text-base font-medium text-text-muted hover:text-primary hover:bg-slate-50 rounded-md transition-colors">Sign In</a>
                <a href="<?= url('/register') ?>" class="w-full text-center py-2 rounded-md text-base font-medium bg-primary text-white hover:bg-primary/90 shadow-sm transition-all">Register</a>
            </div>
        </div>
    </div>
</nav>

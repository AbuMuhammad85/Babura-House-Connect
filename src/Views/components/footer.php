<footer class="bg-slate-900 text-slate-400 py-12 border-t border-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <div class="space-y-4">
                <a href="<?= url('/') ?>" class="flex items-center space-x-2 text-white">
                    <span class="p-2 rounded-lg bg-primary/20 text-primary">
                        <i class="fa-solid fa-house-chimney text-lg"></i>
                    </span>
                    <span class="font-bold text-xl tracking-tight">
                        Babura House <span class="text-primary font-semibold">Connect</span>
                    </span>
                </a>
                <p class="text-sm">
                    Connecting verified landlords and tenants within Babura Town. Building trust and convenience in home rentals.
                </p>
                <div class="flex space-x-4">
                    <a href="#" class="hover:text-white transition-colors"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="#" class="hover:text-white transition-colors"><i class="fa-brands fa-x-twitter"></i></a>
                    <a href="#" class="hover:text-white transition-colors"><i class="fa-brands fa-instagram"></i></a>
                </div>
            </div>
            
            <div>
                <h3 class="text-white font-semibold text-sm uppercase tracking-wider mb-4">Quick Links</h3>
                <ul class="space-y-2 text-sm">
                    <li><a href="<?= url('/') ?>" class="hover:text-white transition-colors">Home</a></li>
                    <li><a href="<?= url('/browse') ?>" class="hover:text-white transition-colors">Browse Houses</a></li>
                    <li><a href="<?= url('/about') ?>" class="hover:text-white transition-colors">About Us</a></li>
                    <li><a href="<?= url('/contact') ?>" class="hover:text-white transition-colors">Contact</a></li>
                    <li><a href="<?= url('/privacy') ?>" class="hover:text-white transition-colors">Privacy Policy</a></li>
                    <li><a href="<?= url('/terms') ?>" class="hover:text-white transition-colors">Terms &amp; Conditions</a></li>
                </ul>
            </div>

            <div>
                <h3 class="text-white font-semibold text-sm uppercase tracking-wider mb-4">Portals</h3>
                <ul class="space-y-2 text-sm">
                    <li><a href="<?= url('/tenant/dashboard') ?>" class="hover:text-white transition-colors">Tenant Dashboard</a></li>
                    <li><a href="<?= url('/landlord/dashboard') ?>" class="hover:text-white transition-colors">Landlord Dashboard</a></li>
                    <li><a href="<?= url('/admin/login') ?>" class="hover:text-white transition-colors">Admin Login</a></li>
                </ul>
            </div>

            <div>
                <h3 class="text-white font-semibold text-sm uppercase tracking-wider mb-4">Contact Info</h3>
                <ul class="space-y-2 text-sm">
                    <li><i class="fa-solid fa-location-dot mr-2"></i> Kofar Gabas, Babura, Jigawa State</li>
                    <li><i class="fa-solid fa-phone mr-2"></i> +234 803 123 4567</li>
                    <li><i class="fa-solid fa-envelope mr-2"></i> info@houseconnect.ng</li>
                </ul>
            </div>
        </div>
        
        <div class="mt-8 pt-8 border-t border-slate-800 text-center text-xs">
            <p>&copy; <?= date('Y') ?> Babura House Connect. All rights reserved. Jigawa Expansion Project.</p>
        </div>
    </div>
</footer>

<section class="py-16 sm:py-24 bg-background flex items-center justify-center">
    <div class="w-full max-w-md px-4" data-aos="fade-up">
        
        <!-- Register Card -->
        <div class="bg-white border border-slate-100 rounded-2xl p-6 sm:p-8 shadow-sm space-y-6">
            
            <div class="text-center space-y-2">
                <span class="p-3 rounded-2xl bg-primary/10 text-primary inline-flex items-center justify-center mb-2">
                    <i class="fa-solid fa-user-plus text-xl"></i>
                </span>
                <h1 class="text-xl font-extrabold text-slate-800">Create New Account</h1>
                <p class="text-xs text-text-muted">Register to search or lease verified houses in Babura.</p>
            </div>

            <?php if (\App\Helpers\Flash::has('error')): ?>
                <?php component('alerts', ['type' => 'error', 'message' => \App\Helpers\Flash::get('error')]); ?>
            <?php endif; ?>
            <?php if (\App\Helpers\Flash::has('success')): ?>
                <?php component('alerts', ['type' => 'success', 'message' => \App\Helpers\Flash::get('success')]); ?>
            <?php endif; ?>

            <form action="<?= url('/register') ?>" method="POST" class="space-y-4">
                <?= \App\Helpers\CSRF::field() ?>
                
                <!-- Full Name -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Full Name</label>
                    <div class="relative">
                        <i class="fa-regular fa-user absolute left-3.5 top-3 text-slate-400 text-xs"></i>
                        <input type="text" name="name" required placeholder="e.g. Garba Danladi" class="w-full pl-9 pr-3.5 py-2 text-xs border border-slate-200 focus:border-primary focus:outline-none rounded-lg text-slate-800 bg-white">
                    </div>
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Email Address</label>
                    <div class="relative">
                        <i class="fa-regular fa-envelope absolute left-3.5 top-3 text-slate-400 text-xs"></i>
                        <input type="email" name="email" required placeholder="name@example.com" class="w-full pl-9 pr-3.5 py-2 text-xs border border-slate-200 focus:border-primary focus:outline-none rounded-lg text-slate-800 bg-white">
                    </div>
                </div>

                <!-- Phone -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Phone Number</label>
                    <div class="relative">
                        <i class="fa-solid fa-phone absolute left-3.5 top-3 text-slate-400 text-xs"></i>
                        <input type="tel" name="phone" required placeholder="e.g. +234 803 123 4567" class="w-full pl-9 pr-3.5 py-2 text-xs border border-slate-200 focus:border-primary focus:outline-none rounded-lg text-slate-800 bg-white">
                    </div>
                </div>

                <!-- Role -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">I Want to Register as a</label>
                    <div class="relative">
                        <i class="fa-solid fa-users-gear absolute left-3.5 top-3 text-slate-400 text-xs"></i>
                        <select name="role" class="w-full pl-9 pr-3.5 py-2 text-xs border border-slate-200 focus:border-primary focus:outline-none rounded-lg text-slate-800 bg-white">
                            <option value="tenant">Tenant (Looking for rent)</option>
                            <option value="landlord">Landlord (Listing properties)</option>
                        </select>
                    </div>
                </div>

                <!-- Password -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Password</label>
                    <div class="relative">
                        <i class="fa-solid fa-key absolute left-3.5 top-3 text-slate-400 text-xs"></i>
                        <input type="password" name="password" required placeholder="••••••••" class="w-full pl-9 pr-3.5 py-2 text-xs border border-slate-200 focus:border-primary focus:outline-none rounded-lg text-slate-800 bg-white">
                    </div>
                </div>

                <!-- Confirm Password -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Confirm Password</label>
                    <div class="relative">
                        <i class="fa-solid fa-key absolute left-3.5 top-3 text-slate-400 text-xs"></i>
                        <input type="password" name="confirm_password" required placeholder="••••••••" class="w-full pl-9 pr-3.5 py-2 text-xs border border-slate-200 focus:border-primary focus:outline-none rounded-lg text-slate-800 bg-white">
                    </div>
                </div>

                <!-- Terms and Conditions -->
                <div class="flex items-start">
                    <input type="checkbox" id="terms" required class="w-4 h-4 mt-0.5 rounded text-primary focus:ring-primary border-slate-200 shrink-0">
                    <label for="terms" class="ml-2 block text-xs text-text-muted leading-relaxed">I agree to Babura House Connect's <a href="<?= url('/terms') ?>" class="text-primary hover:underline">Terms &amp; Conditions</a> &amp; <a href="<?= url('/privacy') ?>" class="text-primary hover:underline">Privacy Policy</a></label>
                </div>

                <!-- Submit -->
                <button type="submit" class="w-full bg-primary hover:bg-primary/95 text-white font-semibold py-2.5 px-4 rounded-lg text-xs transition-all shadow-md shadow-primary/20 flex items-center justify-center space-x-2">
                    <i class="fa-solid fa-user-plus"></i>
                    <span>Register Account</span>
                </button>
            </form>

            <div class="border-t border-slate-100 pt-4 text-center">
                <p class="text-xs text-text-muted">Already have an account? <a href="<?= url('/login') ?>" class="text-primary font-semibold hover:underline">Sign In here</a></p>
            </div>
        </div>

    </div>
</section>

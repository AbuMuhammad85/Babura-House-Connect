<section class="py-16 sm:py-24 bg-background flex items-center justify-center">
    <div class="w-full max-w-md px-4" data-aos="fade-up">
        
        <!-- Login Card -->
        <div class="bg-white border border-slate-100 rounded-2xl p-6 sm:p-8 shadow-sm space-y-6">
            
            <div class="text-center space-y-2">
                <span class="p-3 rounded-2xl bg-primary/10 text-primary inline-flex items-center justify-center mb-2">
                    <i class="fa-solid fa-lock text-xl"></i>
                </span>
                <h1 class="text-xl font-extrabold text-slate-800">Sign In to Your Account</h1>
                <p class="text-xs text-text-muted">Enter details below to access your Babura House portal.</p>
            </div>

            <?php if (\App\Helpers\Flash::has('error')): ?>
                <?php component('alerts', ['type' => 'error', 'message' => \App\Helpers\Flash::get('error')]); ?>
            <?php endif; ?>
            <?php if (\App\Helpers\Flash::has('success')): ?>
                <?php component('alerts', ['type' => 'success', 'message' => \App\Helpers\Flash::get('success')]); ?>
            <?php endif; ?>

            <form action="<?= url('/login') ?>" method="POST" class="space-y-4">
                <?= \App\Helpers\CSRF::field() ?>
                
                <!-- Email Input -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Email Address</label>
                    <div class="relative">
                        <i class="fa-regular fa-envelope absolute left-3.5 top-3 text-slate-400 text-xs"></i>
                        <input type="email" name="email" required placeholder="name@example.com" class="w-full pl-9 pr-3.5 py-2 text-xs border border-slate-200 focus:border-primary focus:outline-none rounded-lg text-slate-800 bg-white">
                    </div>
                </div>

                <!-- Password Input -->
                <div x-data="{ showPassword: false }">
                    <div class="flex justify-between items-center mb-1">
                        <label class="block text-xs font-semibold text-slate-700">Password</label>
                        <a href="<?= url('/forgot-password') ?>" class="text-[10px] text-primary hover:underline font-semibold">Forgot Password?</a>
                    </div>
                    <div class="relative">
                        <i class="fa-solid fa-key absolute left-3.5 top-3 text-slate-400 text-xs"></i>
                        <input :type="showPassword ? 'text' : 'password'" name="password" required placeholder="••••••••" class="w-full pl-9 pr-10 py-2 text-xs border border-slate-200 focus:border-primary focus:outline-none rounded-lg text-slate-800 bg-white">
                        <button type="button" @click="showPassword = !showPassword" class="absolute right-3 top-2 text-slate-400 hover:text-slate-600 focus:outline-none" aria-label="Toggle password visibility">
                            <i class="fa-solid text-xs" :class="showPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
                        </button>
                    </div>
                </div>

                <!-- Role Selection -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Select Access Portal</label>
                    <div class="relative">
                        <i class="fa-solid fa-circle-user absolute left-3.5 top-3 text-slate-400 text-xs"></i>
                        <select name="role" class="w-full pl-9 pr-3.5 py-2 text-xs border border-slate-200 focus:border-primary focus:outline-none rounded-lg text-slate-800 bg-white">
                            <option value="tenant">Tenant Dashboard Portal</option>
                            <option value="landlord">Landlord Listing Portal</option>
                            <option value="admin">Admin Control Panel</option>
                        </select>
                    </div>
                </div>

                <!-- Remember Me -->
                <div class="flex items-center">
                    <input type="checkbox" id="remember" class="w-4 h-4 rounded text-primary focus:ring-primary border-slate-200">
                    <label for="remember" class="ml-2 block text-xs text-text-muted">Remember my session</label>
                </div>

                <!-- Submit -->
                <button type="submit" class="w-full bg-primary hover:bg-primary/95 text-white font-semibold py-2.5 px-4 rounded-lg text-xs transition-all shadow-md shadow-primary/20 flex items-center justify-center space-x-2">
                    <i class="fa-solid fa-right-to-bracket"></i>
                    <span>Sign In Now</span>
                </button>
            </form>

            <div class="border-t border-slate-100 pt-4 text-center">
                <p class="text-xs text-text-muted">Don't have an account? <a href="<?= url('/register') ?>" class="text-primary font-semibold hover:underline">Register here</a></p>
            </div>
        </div>

    </div>
</section>

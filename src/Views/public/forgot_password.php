<section class="py-16 sm:py-24 bg-background flex items-center justify-center">
    <div class="w-full max-w-md px-4" data-aos="fade-up">
        
        <!-- Reset Card -->
        <div class="bg-white border border-slate-100 rounded-2xl p-6 sm:p-8 shadow-sm space-y-6">
            
            <div class="text-center space-y-2">
                <span class="p-3 rounded-2xl bg-primary/10 text-primary inline-flex items-center justify-center mb-2">
                    <i class="fa-solid fa-key text-xl"></i>
                </span>
                <h1 class="text-xl font-extrabold text-slate-800">Forgot Your Password?</h1>
                <p class="text-xs text-text-muted">Enter your email and we'll send a link to reset your password.</p>
            </div>

            <!-- Success Alert Component helper -->
            <?php if (isset($success)): ?>
                <?php component('alerts', ['type' => 'success', 'message' => $success]); ?>
            <?php endif; ?>

            <form action="<?= url('/forgot-password') ?>" method="POST" class="space-y-4">
                
                <!-- Email -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Email Address</label>
                    <div class="relative">
                        <i class="fa-regular fa-envelope absolute left-3.5 top-3 text-slate-400 text-xs"></i>
                        <input type="email" name="email" required placeholder="name@example.com" class="w-full pl-9 pr-3.5 py-2 text-xs border border-slate-200 focus:border-primary focus:outline-none rounded-lg text-slate-800 bg-white">
                    </div>
                </div>

                <!-- Submit -->
                <button type="submit" class="w-full bg-primary hover:bg-primary/95 text-white font-semibold py-2.5 px-4 rounded-lg text-xs transition-all shadow-md shadow-primary/20 flex items-center justify-center space-x-2">
                    <i class="fa-regular fa-paper-plane"></i>
                    <span>Send Reset Link</span>
                </button>
            </form>

            <div class="border-t border-slate-100 pt-4 text-center">
                <a href="<?= url('/login') ?>" class="text-xs text-primary font-semibold hover:underline flex items-center justify-center space-x-1">
                    <i class="fa-solid fa-arrow-left text-[10px]"></i>
                    <span>Back to Sign In</span>
                </a>
            </div>
        </div>

    </div>
</section>

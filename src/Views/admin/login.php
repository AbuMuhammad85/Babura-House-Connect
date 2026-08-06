<section class="py-16 sm:py-24 bg-background flex items-center justify-center">
    <div class="w-full max-w-md px-4" data-aos="fade-up">
        
        <div class="bg-white border border-slate-100 rounded-2xl p-6 sm:p-8 shadow-sm space-y-6">
            
            <div class="text-center space-y-2">
                <span class="p-3 rounded-2xl bg-slate-900 text-white inline-flex items-center justify-center mb-2">
                    <i class="fa-solid fa-user-shield text-xl"></i>
                </span>
                <h1 class="text-xl font-extrabold text-slate-800">Admin Portal Sign In</h1>
                <p class="text-xs text-text-muted">Enter administrative credentials below to authenticate.</p>
            </div>

            <form action="<?= url('/admin/login') ?>" method="POST" class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Admin Email</label>
                    <div class="relative">
                        <i class="fa-regular fa-envelope absolute left-3.5 top-3 text-slate-400 text-xs"></i>
                        <input type="email" name="email" required placeholder="admin@houseconnect.ng" class="w-full pl-9 pr-3.5 py-2 text-xs border border-slate-200 focus:border-primary focus:outline-none rounded-lg text-slate-800 bg-white">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Password</label>
                    <div class="relative">
                        <i class="fa-solid fa-key absolute left-3.5 top-3 text-slate-400 text-xs"></i>
                        <input type="password" name="password" required placeholder="••••••••" class="w-full pl-9 pr-3.5 py-2 text-xs border border-slate-200 focus:border-primary focus:outline-none rounded-lg text-slate-800 bg-white">
                    </div>
                </div>

                <button type="submit" class="w-full bg-slate-900 hover:bg-slate-800 text-white font-semibold py-2.5 px-4 rounded-lg text-xs transition-all shadow-md flex items-center justify-center space-x-2">
                    <i class="fa-solid fa-right-to-bracket"></i>
                    <span>Access Admin Portal</span>
                </button>
            </form>
        </div>

    </div>
</section>

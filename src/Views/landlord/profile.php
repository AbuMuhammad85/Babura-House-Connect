<div class="space-y-6 max-w-3xl">
    <div>
        <h2 class="text-xl font-bold text-slate-800">Landlord Profile</h2>
        <p class="text-xs text-text-muted mt-1">Configure your contact details. This is shown on property pages.</p>
    </div>

    <!-- Profile Form -->
    <div class="bg-white border border-slate-100 rounded-2xl p-6 sm:p-8 shadow-sm space-y-6">
        <div class="flex items-center space-x-4">
            <span class="w-16 h-16 rounded-2xl bg-primary/10 text-primary flex items-center justify-center font-bold text-2xl uppercase shrink-0">
                <?= substr($profile['name'], 0, 2) ?>
            </span>
            <div>
                <h3 class="font-bold text-slate-800 text-sm"><?= $profile['name'] ?></h3>
                <p class="text-[11px] text-text-muted mt-0.5">Verified Partner Landlord &bull; Active in Jigawa</p>
            </div>
        </div>

        <form action="#" class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-6 border-t border-slate-100">
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Company / Agency Name</label>
                <input type="text" value="<?= $profile['company'] ?>" class="w-full px-3.5 py-2 text-xs border border-slate-200 focus:border-primary focus:outline-none rounded-lg text-slate-800 bg-white">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Full Representative Name</label>
                <input type="text" value="<?= $profile['name'] ?>" class="w-full px-3.5 py-2 text-xs border border-slate-200 focus:border-primary focus:outline-none rounded-lg text-slate-800 bg-white">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Email Address</label>
                <input type="email" value="<?= $profile['email'] ?>" disabled class="w-full px-3.5 py-2 text-xs border border-slate-200 rounded-lg text-slate-500 bg-slate-50/50">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Support Call Line</label>
                <input type="tel" value="<?= $profile['phone'] ?>" class="w-full px-3.5 py-2 text-xs border border-slate-200 focus:border-primary focus:outline-none rounded-lg text-slate-800 bg-white">
            </div>

            <div class="sm:col-span-2">
                <label class="block text-xs font-semibold text-slate-700 mb-1">Office / Residential Address</label>
                <textarea rows="3" class="w-full px-3.5 py-2 text-xs border border-slate-200 focus:border-primary focus:outline-none rounded-lg text-slate-800 bg-white resize-none"><?= $profile['address'] ?></textarea>
            </div>

            <div>
                <button type="submit" class="bg-primary hover:bg-primary/95 text-white font-semibold py-2 px-4 rounded-lg text-xs transition-all shadow-sm hover:shadow-lg">
                    Save Profile
                </button>
            </div>
        </form>
    </div>
</div>

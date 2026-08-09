<?php
use App\Helpers\Flash;
?>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Left: List Areas -->
    <div class="lg:col-span-2 space-y-6">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Babura Town Areas</h2>
            <p class="text-xs text-text-muted mt-1">Configure active neighborhood zones within Babura Town limits for house listing registrations.</p>
        </div>

        <?php if (Flash::has('error')): ?>
            <?php component('alerts', ['type' => 'error', 'message' => Flash::get('error')]); ?>
        <?php endif; ?>
        <?php if (Flash::has('success')): ?>
            <?php component('alerts', ['type' => 'success', 'message' => Flash::get('success')]); ?>
        <?php endif; ?>

        <!-- Table Container -->
        <div class="overflow-x-auto bg-white border border-slate-100 rounded-2xl shadow-sm">
            <?php if (empty($areas)): ?>
                <div class="p-8 text-center text-xs text-text-muted">
                    <i class="fa-solid fa-map-location-dot text-2xl text-slate-350 mb-2"></i>
                    <p>No neighborhood zones registered yet.</p>
                </div>
            <?php else: ?>
                <table class="min-w-full divide-y divide-slate-100 text-left">
                    <thead class="bg-slate-50 text-[10px] font-bold uppercase tracking-wider text-slate-500">
                        <tr>
                            <th class="px-6 py-3">Area / Zone Name</th>
                            <th class="px-6 py-3">Slug</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white text-xs text-slate-700">
                        <?php foreach ($areas as $area): ?>
                            <tr>
                                <td class="px-6 py-4 font-bold text-slate-850">
                                    <?= htmlspecialchars($area['name']) ?>
                                </td>
                                <td class="px-6 py-4 font-mono text-slate-500">
                                    <?= htmlspecialchars($area['slug']) ?>
                                </td>
                                <td class="px-6 py-4">
                                    <?php 
                                    $statusType = $area['is_active'] ? 'success' : 'neutral';
                                    $statusText = $area['is_active'] ? 'Active' : 'Inactive';
                                    ?>
                                    <?php component('badges', ['type' => $statusType, 'text' => $statusText]); ?>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <form action="<?= url('/admin/areas/toggle') ?>" method="POST" class="inline">
                                        <?= \App\Helpers\CSRF::field() ?>
                                        <input type="hidden" name="id" value="<?= $area['id'] ?>">
                                        <button type="submit" class="px-2.5 py-1.5 border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-semibold rounded-lg text-[10px] transition-all">
                                            <?= $area['is_active'] ? 'Deactivate' : 'Activate' ?>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>

    <!-- Right: Add Area Form -->
    <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm space-y-4 h-fit">
        <h3 class="font-bold text-slate-800 text-sm border-b border-slate-50 pb-2">Register Local Area</h3>
        <p class="text-[10px] text-text-muted leading-relaxed">
            All registered locations must reside strictly inside Babura Town boundaries (e.g., Kofar Gabas, Kofar Yamma, Tsohon Gari). Platform properties are restricted to these local zones.
        </p>

        <form action="<?= url('/admin/areas/create') ?>" method="POST" class="space-y-4">
            <?= \App\Helpers\CSRF::field() ?>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Area Name</label>
                <input type="text" name="name" placeholder="e.g. Kofar Gabas" required class="w-full px-3 py-2 text-xs border border-slate-200 focus:border-primary focus:outline-none rounded-lg text-slate-800 bg-white">
            </div>
            <button type="submit" class="w-full py-2 bg-slate-900 hover:bg-slate-800 text-white font-semibold rounded-lg text-xs transition-all shadow-sm">
                Register Area
            </button>
        </form>
    </div>
</div>

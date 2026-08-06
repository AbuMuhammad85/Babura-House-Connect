<div class="space-y-6">
    <div>
        <h2 class="text-xl font-bold text-slate-800">Verify Landlord Documents</h2>
        <p class="text-xs text-text-muted mt-1">Review government IDs and local land allocation records to certify landlords.</p>
    </div>

    <!-- Verification Table Grid -->
    <div class="overflow-x-auto bg-white border border-slate-100 rounded-2xl shadow-sm">
        <table class="min-w-full divide-y divide-slate-100 text-left">
            <thead class="bg-slate-50 text-[10px] font-bold uppercase tracking-wider text-slate-500">
                <tr>
                    <th class="px-6 py-3">Landlord Name</th>
                    <th class="px-6 py-3">Contact info</th>
                    <th class="px-6 py-3">Submitted Date</th>
                    <th class="px-6 py-3">Files Review</th>
                    <th class="px-6 py-3">Verification Status</th>
                    <th class="px-6 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 bg-white text-xs text-slate-700">
                <?php foreach ($landlords as $landlord): ?>
                    <tr>
                        <td class="px-6 py-4 font-bold text-slate-850">
                            <?= $landlord['name'] ?>
                        </td>
                        <td class="px-6 py-4 space-y-0.5 text-text-muted">
                            <p><?= $landlord['email'] ?></p>
                            <p class="text-[10px]"><?= $landlord['phone'] ?></p>
                        </td>
                        <td class="px-6 py-4 text-text-muted">
                            <?= $landlord['date'] ?>
                        </td>
                        <!-- Document reviewer links -->
                        <td class="px-6 py-4 space-x-2">
                            <button class="px-2.5 py-1 bg-slate-50 hover:bg-slate-100 text-slate-700 border border-slate-200 rounded-lg font-medium text-[10px] flex items-center inline-flex space-x-1">
                                <i class="fa-solid fa-file-pdf text-red-500"></i>
                                <span>National ID</span>
                            </button>
                            <button class="px-2.5 py-1 bg-slate-50 hover:bg-slate-100 text-slate-700 border border-slate-200 rounded-lg font-medium text-[10px] flex items-center inline-flex space-x-1">
                                <i class="fa-solid fa-file-invoice text-blue-500"></i>
                                <span>Proof of Land</span>
                            </button>
                        </td>
                        <td class="px-6 py-4">
                            <?php 
                            $statusType = 'warning';
                            if ($landlord['status'] === 'Verified') $statusType = 'success';
                            ?>
                            <?php component('badges', ['type' => $statusType, 'text' => $landlord['status']]); ?>
                        </td>
                        <!-- Action buttons -->
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end space-x-2">
                                <?php if ($landlord['status'] !== 'Verified'): ?>
                                    <button class="px-3 py-1.5 bg-primary hover:bg-primary/95 text-white font-semibold rounded-lg text-[10px] shadow-sm transition-all">
                                        Approve
                                    </button>
                                    <button class="px-3 py-1.5 bg-red-50 hover:bg-red-100 text-red-600 font-semibold rounded-lg text-[10px] transition-all">
                                        Reject
                                    </button>
                                <?php else: ?>
                                    <button class="px-3 py-1.5 border border-slate-200 bg-white hover:bg-slate-55 text-slate-500 font-semibold rounded-lg text-[10px] transition-all">
                                        Block Landlord
                                    </button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

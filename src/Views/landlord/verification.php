<?php
use App\Helpers\Flash;
use App\Helpers\Auth;
use App\Core\Database;

$userId = Auth::user('id');
$profile = Database::fetch(
    "SELECT verification_status FROM landlord_profiles WHERE user_id = :user_id",
    ['user_id' => $userId]
);
$status = $profile['verification_status'] ?? 'pending';

$rejectionNotes = '';
if ($status === 'rejected') {
    $verification = Database::fetch(
        "SELECT admin_notes FROM landlord_verifications WHERE landlord_id = (SELECT id FROM landlord_profiles WHERE user_id = :user_id) ORDER BY id DESC LIMIT 1",
        ['user_id' => $userId]
    );
    $rejectionNotes = $verification['admin_notes'] ?? '';
}
?>

<div class="space-y-6 max-w-3xl">
    <div>
        <h2 class="text-xl font-bold text-slate-800">Identity &amp; Ownership Verification</h2>
        <p class="text-xs text-text-muted mt-1">Submit documents to obtain the verified badge and publish listings.</p>
    </div>

    <?php if (Flash::has('error')): ?>
        <?php component('alerts', ['type' => 'error', 'message' => Flash::get('error')]); ?>
    <?php endif; ?>
    <?php if (Flash::has('success')): ?>
        <?php component('alerts', ['type' => 'success', 'message' => Flash::get('success')]); ?>
    <?php endif; ?>
    <?php if (Flash::has('warning')): ?>
        <?php component('alerts', ['type' => 'warning', 'message' => Flash::get('warning')]); ?>
    <?php endif; ?>

    <!-- Status Alert Component helper -->
    <div class="bg-white border border-slate-100 rounded-2xl p-6 sm:p-8 shadow-sm space-y-6">
        <?php if ($status === 'approved'): ?>
            <div class="flex items-center space-x-3">
                <span class="p-3 rounded-2xl bg-green-50 text-green-600 flex items-center justify-center text-lg animate-pulse">
                    <i class="fa-solid fa-circle-check"></i>
                </span>
                <div>
                    <h3 class="font-bold text-slate-800 text-sm">Verification Status: Approved</h3>
                    <p class="text-xs text-text-muted mt-0.5">Your identity and land ownership documents have been verified. You can now add and manage listings.</p>
                </div>
            </div>
        <?php elseif ($status === 'pending'): ?>
            <div class="flex items-center space-x-3">
                <span class="p-3 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-lg animate-spin">
                    <i class="fa-solid fa-hourglass-half"></i>
                </span>
                <div>
                    <h3 class="font-bold text-slate-800 text-sm">Verification Status: Pending Review</h3>
                    <p class="text-xs text-text-muted mt-0.5">Your submitted documents are currently being checked by the Babura admin team.</p>
                </div>
            </div>
        <?php else: ?>
            <div class="flex items-center space-x-3">
                <span class="p-3 rounded-2xl bg-red-50 text-red-600 flex items-center justify-center text-lg">
                    <i class="fa-solid fa-circle-xmark"></i>
                </span>
                <div>
                    <h3 class="font-bold text-slate-800 text-sm">Verification Status: Rejected</h3>
                    <p class="text-xs text-text-muted mt-0.5">Your verification request was rejected. Please review the reasons below and resubmit.</p>
                </div>
            </div>
            <?php if (!empty($rejectionNotes)): ?>
                <div class="p-4 bg-red-50/50 border border-red-100 rounded-xl text-xs text-red-805">
                    <strong>Reason for Rejection:</strong> <?= htmlspecialchars($rejectionNotes) ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
        
        <?php if ($status !== 'approved'): ?>
            <div class="border-t border-slate-100 pt-6">
                <h4 class="font-bold text-slate-800 text-xs mb-4">Submit Verification Documents</h4>
                
                <form action="<?= url('/landlord/verification') ?>" method="POST" enctype="multipart/form-data" class="space-y-6">
                    <?= \App\Helpers\CSRF::field() ?>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Government ID Type</label>
                            <select name="id_type" class="w-full px-3.5 py-2 text-xs border border-slate-200 focus:border-primary focus:outline-none rounded-lg text-slate-800 bg-white">
                                <option value="NIN">National ID Card (NIN)</option>
                                <option value="Voters Card">INEC Voter's Card</option>
                                <option value="Drivers License">Driver's License</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">National Identification Number (NIN)</label>
                            <input type="text" name="nin" required placeholder="e.g. 12345678901" class="w-full px-3.5 py-2 text-xs border border-slate-200 focus:border-primary focus:outline-none rounded-lg text-slate-800 bg-white">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Upload ID File (Front/Back)</label>
                            <input type="file" name="id_document" required class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20">
                        </div>
                        
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Upload Selfie / Verification Photo (with ID card)</label>
                            <input type="file" name="verification_photo" required class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20">
                        </div>
                    </div>

                    <!-- Proof of Land Ownership -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Proof of Ownership Type</label>
                            <select name="ownership_type" class="w-full px-3.5 py-2 text-xs border border-slate-200 focus:border-primary focus:outline-none rounded-lg text-slate-800 bg-white">
                                <option value="C of O">Certificate of Occupancy (C of O)</option>
                                <option value="Land Allocation">Local Government Land Allocation Document</option>
                                <option value="Purchase Agreement">Signed Purchase Agreement &amp; Receipt</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Upload Ownership Document File (PDF/Image)</label>
                            <input type="file" name="ownership_document" required class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20">
                        </div>
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="bg-primary hover:bg-primary/95 text-white font-semibold py-2.5 px-6 rounded-lg text-xs transition-all shadow-md">
                            Submit Verification Data
                        </button>
                    </div>
                </form>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="space-y-6 max-w-3xl">
    <div>
        <h2 class="text-xl font-bold text-slate-800">Identity &amp; Ownership Verification</h2>
        <p class="text-xs text-text-muted mt-1">Submit documents to obtain the verified badge and publish listings.</p>
    </div>

    <!-- Status Alert Component helper -->
    <div class="bg-white border border-slate-100 rounded-2xl p-6 sm:p-8 shadow-sm space-y-6">
        <div class="flex items-center space-x-3">
            <span class="p-3 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-lg">
                <i class="fa-solid fa-hourglass-half"></i>
            </span>
            <div>
                <h3 class="font-bold text-slate-800 text-sm">Verification Status: Pending Review</h3>
                <p class="text-xs text-text-muted mt-0.5">Your submitted documents are currently being checked by the Babura admin team.</p>
            </div>
        </div>
        
        <div class="border-t border-slate-100 pt-6">
            <h4 class="font-bold text-slate-800 text-xs mb-4">Submit Verification Documents</h4>
            
            <form action="#" class="space-y-6">
                <!-- ID Card Select -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Government ID Type</label>
                        <select class="w-full px-3.5 py-2 text-xs border border-slate-200 focus:border-primary focus:outline-none rounded-lg text-slate-800 bg-white">
                            <option>National ID Card (NIN)</option>
                            <option>INEC Voter's Card</option>
                            <option>Driver's License</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Upload ID File (Front/Back)</label>
                        <input type="file" required class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20">
                    </div>
                </div>

                <!-- Proof of Land Ownership -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Proof of Ownership Type</label>
                        <select class="w-full px-3.5 py-2 text-xs border border-slate-200 focus:border-primary focus:outline-none rounded-lg text-slate-800 bg-white">
                            <option>Certificate of Occupancy (C of O)</option>
                            <option>Local Government Land Allocation Document</option>
                            <option>Signed Purchase Agreement &amp; Receipt</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Upload Document File (PDF/Image)</label>
                        <input type="file" required class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20">
                    </div>
                </div>

                <div class="pt-4">
                    <button type="submit" class="bg-primary hover:bg-primary/95 text-white font-semibold py-2.5 px-6 rounded-lg text-xs transition-all shadow-md">
                        Re-Submit Verification Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

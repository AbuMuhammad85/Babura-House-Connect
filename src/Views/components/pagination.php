<?php
$currentPage = $currentPage ?? 1;
$totalPages = $totalPages ?? 5;
$baseUrl = $baseUrl ?? '#';
?>

<div class="flex items-center justify-between border-t border-slate-100 px-4 py-3 sm:px-6">
    <div class="flex flex-1 justify-between sm:hidden">
        <a href="<?= $baseUrl ?>?page=<?= max(1, $currentPage - 1) ?>" class="relative inline-flex items-center rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">Previous</a>
        <a href="<?= $baseUrl ?>?page=<?= min($totalPages, $currentPage + 1) ?>" class="relative ml-3 inline-flex items-center rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">Next</a>
    </div>
    <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
        <div>
            <p class="text-xs text-text-muted">
                Showing page <span class="font-semibold text-slate-700"><?= $currentPage ?></span> of <span class="font-semibold text-slate-700"><?= $totalPages ?></span> pages
            </p>
        </div>
        <div>
            <nav class="isolate inline-flex -space-x-px rounded-lg shadow-sm border border-slate-100" aria-label="Pagination">
                <a href="<?= $baseUrl ?>?page=<?= max(1, $currentPage - 1) ?>" class="relative inline-flex items-center rounded-l-lg px-2 py-2 text-slate-400 hover:bg-slate-50 hover:text-slate-500 focus:z-20 focus:outline-none transition-colors border-r border-slate-100">
                    <i class="fa-solid fa-chevron-left text-xs"></i>
                </a>
                
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="<?= $baseUrl ?>?page=<?= $i ?>" class="relative inline-flex items-center px-4 py-2 text-xs font-semibold <?= $i === $currentPage ? 'bg-primary text-white focus:z-20' : 'text-slate-600 hover:bg-slate-50 focus:z-20' ?> transition-colors border-r border-slate-100">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
                
                <a href="<?= $baseUrl ?>?page=<?= min($totalPages, $currentPage + 1) ?>" class="relative inline-flex items-center rounded-r-lg px-2 py-2 text-slate-400 hover:bg-slate-50 hover:text-slate-500 focus:z-20 focus:outline-none transition-colors">
                    <i class="fa-solid fa-chevron-right text-xs"></i>
                </a>
            </nav>
        </div>
    </div>
</div>

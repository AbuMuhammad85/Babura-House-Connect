<?php
$id = $id ?? 'modal-id';
$title = $title ?? 'Modal Title';
?>

<div x-show="open" 
     class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     x-cloak>
     
    <div @click.away="open = false" 
         class="bg-white rounded-2xl w-full max-w-lg shadow-2xl border border-slate-100 overflow-hidden transform transition-all"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95 translate-y-4"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-95 translate-y-4">
         
        <!-- Header -->
        <div class="px-6 py-4 bg-slate-50 border-b border-slate-100 flex justify-between items-center">
            <h3 class="font-bold text-slate-800 text-base"><?= $title ?></h3>
            <button @click="open = false" class="text-slate-400 hover:text-slate-600 rounded-lg p-1 hover:bg-slate-100 transition-all" aria-label="Close modal">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>
        
        <!-- Content -->
        <div class="px-6 py-6 text-sm text-text-muted leading-relaxed">
            <?= $content ?? 'Modal body contents placeholder...' ?>
        </div>
    </div>
</div>

<section class="py-16 sm:py-24 bg-background flex flex-col items-center justify-center text-center px-4">
    <div class="w-16 h-16 rounded-2xl bg-red-50 text-red-500 border border-red-100 flex items-center justify-center text-2xl mb-4">
        <i class="fa-solid fa-triangle-exclamation"></i>
    </div>
    <h1 class="text-3xl font-extrabold text-slate-800">404 - Page Not Found</h1>
    <p class="text-xs text-text-muted mt-2 max-w-sm leading-relaxed">
        The page you are looking for does not exist or has been moved. Check the URL and try again.
    </p>
    <a href="<?= url('/') ?>" class="mt-6 px-5 py-2.5 bg-primary text-white hover:bg-primary/95 text-xs font-semibold rounded-lg shadow-sm hover:shadow-lg transition-all">
        Go Back Home
    </a>
</section>

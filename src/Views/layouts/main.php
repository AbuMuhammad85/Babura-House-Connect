<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Babura House Connect' ?> - A Trusted House Renting Platform for Babura</title>
    
    <!-- Meta Descriptions for SEO -->
    <meta name="description" content="Babura House Connect is Jigawa's leading platform connecting verified landlords and tenants within Babura Town and environs. Search, view, and rent apartments securely.">
    
    <!-- Tailwind CSS 4 -->
    <link rel="stylesheet" href="<?= asset('/css/style.css') ?>">
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    
    <!-- Swiper.js CSS (for galleries) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    
    <!-- AOS Animations CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" />
    
    <!-- Alpine.js (Deferred) -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="flex flex-col min-h-screen bg-background text-text-main font-poppins selection:bg-primary/20 selection:text-primary">
    
    <!-- Header/Navbar -->
    <?php component('navbar'); ?>

    <!-- Main Dynamic Content -->
    <main class="flex-grow">
        <?= $content ?>
    </main>

    <!-- Footer -->
    <?php component('footer'); ?>

    <!-- AOS Script Initialization -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            AOS.init({
                duration: 800,
                once: true,
                offset: 50
            });
        });
    </script>
    
    <!-- Swiper.js Script -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
</body>
</html>

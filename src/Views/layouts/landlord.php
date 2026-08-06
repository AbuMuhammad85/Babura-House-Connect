<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Landlord Dashboard' ?> - Babura House Connect</title>
    
    <!-- Tailwind CSS 4 -->
    <link rel="stylesheet" href="<?= asset('/css/style.css') ?>">
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    
    <!-- Chart.js for landlord analytics -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Alpine.js (Deferred) -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-background text-text-main font-poppins min-h-screen" x-data="{ sidebarOpen: false }">
    <div class="flex overflow-hidden h-screen">
        
        <!-- Sidebar Component for Landlord -->
        <?php component('sidebar', ['role' => 'landlord']); ?>

        <!-- Right Side Panel -->
        <div class="flex flex-col flex-grow overflow-hidden">
            
            <!-- Topbar Component -->
            <?php component('topbar', ['role' => 'landlord']); ?>

            <!-- View Specific Body Content -->
            <main class="flex-grow p-4 md:p-6 overflow-y-auto">
                <?= $content ?>
            </main>
            
        </div>
    </div>
</body>
</html>

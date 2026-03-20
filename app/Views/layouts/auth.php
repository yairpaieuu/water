<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php
    $authAppName = 'AquaCRM';
    try {
        $nameRow = \App\Core\Database::getInstance()->fetch(
            "SELECT `value` FROM `settings` WHERE `key` = 'app_name' LIMIT 1"
        );
        if ($nameRow && !empty($nameRow['value'])) {
            $authAppName = $nameRow['value'];
        }
    } catch (\Throwable) {}
    ?>
    <title><?= htmlspecialchars($pageTitle ?? 'Login', ENT_QUOTES, 'UTF-8') ?> &mdash; <?= htmlspecialchars($authAppName, ENT_QUOTES, 'UTF-8') ?></title>
    <meta name="description" content="<?= htmlspecialchars($authAppName, ENT_QUOTES, 'UTF-8') ?> – Domestic Water Purification CRM">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: { DEFAULT: '#0ea5e9', 50: '#f0f9ff', 100: '#e0f2fe', 500: '#0ea5e9', 600: '#0284c7', 700: '#0369a1' }
                    }
                }
            }
        }
    </script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- App CSS -->
    <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body class="h-full bg-gradient-to-br from-slate-900 via-blue-950 to-slate-900 flex items-center justify-center min-h-screen p-4">

    <!-- Decorative background blobs -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none" aria-hidden="true">
        <div class="absolute -top-40 -right-40 w-96 h-96 bg-primary-500/10 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-blue-600/10 rounded-full blur-3xl"></div>
    </div>

    <!-- Auth card -->
    <div class="relative w-full max-w-md">
        <?= $content ?>
    </div>

    <!-- App JS -->
    <script src="/assets/js/app.js"></script>
</body>
</html>

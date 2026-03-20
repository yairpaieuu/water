<?php
// Fetch app name and logo once at the top so they are available everywhere in this layout
$sidebarAppName = 'AquaCRM';
$sidebarLogo    = '';
try {
    $db = \App\Core\Database::getInstance();
    $nameRow = $db->fetch("SELECT `value` FROM `settings` WHERE `key` = 'app_name' LIMIT 1");
    if ($nameRow && !empty($nameRow['value'])) {
        $sidebarAppName = $nameRow['value'];
    }
    $logoRow = $db->fetch("SELECT `value` FROM `settings` WHERE `key` = 'app_logo' LIMIT 1");
    if ($logoRow && !empty($logoRow['value'])) {
        $sidebarLogo = $logoRow['value'];
    }
} catch (\Throwable) {}
?>
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? $sidebarAppName, ENT_QUOTES, 'UTF-8') ?> &mdash; <?= htmlspecialchars($sidebarAppName, ENT_QUOTES, 'UTF-8') ?></title>
    <meta name="description" content="<?= htmlspecialchars($sidebarAppName, ENT_QUOTES, 'UTF-8') ?> – Domestic Water Purification CRM">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: { DEFAULT: '#0ea5e9', 50: '#f0f9ff', 100: '#e0f2fe', 500: '#0ea5e9', 600: '#0284c7', 700: '#0369a1' },
                        navy:    { DEFAULT: '#0f172a', 800: '#1e293b', 900: '#0f172a' }
                    }
                }
            }
        }
    </script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

    <!-- App CSS -->
    <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body class="h-full bg-slate-100 font-sans antialiased flex" x-data="{ sidebarOpen: false, sidebarCollapsed: false }">

<!-- Mobile sidebar backdrop -->
<div
    x-show="sidebarOpen"
    x-transition:enter="transition-opacity ease-linear duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition-opacity ease-linear duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    @click="sidebarOpen = false"
    class="fixed inset-0 z-20 bg-black/50 lg:hidden"
    style="display:none"
></div>

<!-- Sidebar -->
<aside
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    class="fixed inset-y-0 left-0 z-30 flex flex-col w-64 bg-navy-900 transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-auto lg:z-auto"
    style="background-color:#0f172a"
>
    <!-- Logo -->
    <div class="flex items-center justify-between h-16 px-4 border-b border-slate-700 flex-shrink-0">
        <a href="/dashboard" class="flex items-center gap-2">
            <?php if ($sidebarLogo): ?>
            <img src="<?= htmlspecialchars('/assets/uploads/' . $sidebarLogo, ENT_QUOTES, 'UTF-8') ?>"
                 alt="Logo"
                 class="h-8 w-auto object-contain">
            <?php else: ?>
            <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-primary-500">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
            </div>
            <?php endif; ?>
            <span class="text-white font-bold text-lg tracking-tight"><?= htmlspecialchars($sidebarAppName, ENT_QUOTES, 'UTF-8') ?></span>
        </a>
        <button @click="sidebarOpen = false" class="lg:hidden text-slate-400 hover:text-white">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1 sidebar-nav">
        <?php
        $currentUri = $_SERVER['REQUEST_URI'] ?? '/';
        $navItems = [
            ['href' => '/dashboard', 'label' => 'Dashboard', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>'],
        ];
        $crmChildren = [
            ['href' => '/customers', 'label' => 'Customers'],
            ['href' => '/leads',     'label' => 'Leads'],
        ];
        $salesChildren = [
            ['href' => '/orders',   'label' => 'Orders'],
            ['href' => '/invoices', 'label' => 'Invoices'],
        ];
        $groups = [
            ['label' => 'CRM',       'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>', 'children' => $crmChildren],
            ['label' => 'Sales',     'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>', 'children' => $salesChildren],
            ['label' => 'Inventory', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>', 'children' => [['href'=>'/inventory','label'=>'Products'],['href'=>'/stock','label'=>'Stock']]],
            ['label' => 'Services',  'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>', 'children' => [['href'=>'/jobs','label'=>'Jobs'],['href'=>'/contracts','label'=>'Contracts']]],
            ['label' => 'HRM',       'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>', 'children' => [['href'=>'/employees','label'=>'Employees'],['href'=>'/attendance','label'=>'Attendance']]],
            ['label' => 'Reports',   'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>', 'children' => [['href'=>'/reports/sales','label'=>'Sales Report'],['href'=>'/reports/services','label'=>'Service Report']]],
            ['label' => 'Settings',  'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>', 'children' => [['href'=>'/settings','label'=>'General'],['href'=>'/users','label'=>'Users']]],
        ];
        ?>

        <!-- Dashboard link -->
        <a href="/dashboard"
           class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors
                  <?= str_starts_with($currentUri, '/dashboard') || $currentUri === '/'
                        ? 'bg-primary-500 text-white'
                        : 'text-slate-300 hover:bg-slate-700 hover:text-white' ?>">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            Dashboard
        </a>

        <?php foreach ($groups as $group):
            $groupActive = false;
            foreach ($group['children'] as $child) {
                if (str_starts_with($currentUri, $child['href'])) { $groupActive = true; break; }
            }
        ?>
        <div x-data="{ open: <?= $groupActive ? 'true' : 'false' ?> }">
            <button @click="open = !open"
                    class="w-full flex items-center justify-between gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors
                           <?= $groupActive ? 'bg-primary-600 text-white' : 'text-slate-300 hover:bg-slate-700 hover:text-white' ?>">
                <span class="flex items-center gap-3">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <?= $group['icon'] ?>
                    </svg>
                    <?= htmlspecialchars($group['label'], ENT_QUOTES, 'UTF-8') ?>
                </span>
                <svg class="w-4 h-4 transition-transform" :class="open ? 'rotate-90' : ''"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </button>
            <div x-show="open" x-collapse class="mt-1 ml-4 pl-4 border-l border-slate-700 space-y-1">
                <?php foreach ($group['children'] as $child): ?>
                <a href="<?= htmlspecialchars($child['href'], ENT_QUOTES, 'UTF-8') ?>"
                   class="block px-3 py-1.5 rounded-md text-sm transition-colors
                          <?= str_starts_with($currentUri, $child['href'])
                                ? 'bg-primary-500/20 text-white font-semibold'
                                : 'text-slate-400 hover:text-white hover:bg-slate-700' ?>">
                    <?= htmlspecialchars($child['label'], ENT_QUOTES, 'UTF-8') ?>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </nav>

    <!-- User info at bottom -->
    <?php $authUser = \App\Core\Auth::user(); ?>
    <div class="flex-shrink-0 p-4 border-t border-slate-700">
        <div class="flex items-center gap-3">
            <div class="flex-shrink-0 w-8 h-8 rounded-full bg-primary-500 flex items-center justify-center text-white text-sm font-semibold">
                <?= htmlspecialchars(mb_substr($authUser['name'] ?? 'U', 0, 1), ENT_QUOTES, 'UTF-8') ?>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-white truncate">
                    <?= htmlspecialchars($authUser['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                </p>
                <p class="text-xs text-slate-400 capitalize">
                    <?= htmlspecialchars($authUser['role'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                </p>
            </div>
            <a href="/logout" title="Logout"
               class="text-slate-400 hover:text-red-400 transition-colors"
               onclick="return confirm('Logout?')">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
            </a>
        </div>
    </div>
</aside>

<!-- Main wrapper -->
<div class="flex flex-col flex-1 min-h-screen">

    <!-- Top navbar -->
    <header class="sticky top-0 z-10 flex items-center justify-between h-16 px-4 bg-white border-b border-slate-200 shadow-sm">
        <!-- Mobile hamburger -->
        <button @click="sidebarOpen = true" class="lg:hidden text-slate-500 hover:text-slate-700 focus:outline-none">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>

        <div class="hidden lg:block">
            <h1 class="text-lg font-semibold text-slate-700">
                <?= htmlspecialchars($pageTitle ?? 'Dashboard', ENT_QUOTES, 'UTF-8') ?>
            </h1>
        </div>

        <div class="flex items-center gap-3">
            <!-- Notifications -->
            <button class="relative p-2 text-slate-500 hover:text-slate-700 hover:bg-slate-100 rounded-full transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-red-500 rounded-full"></span>
            </button>

            <!-- User avatar dropdown -->
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" class="flex items-center gap-2 text-sm text-slate-700 hover:text-slate-900">
                    <div class="w-8 h-8 rounded-full bg-primary-500 flex items-center justify-center text-white font-semibold text-sm">
                        <?= htmlspecialchars(mb_substr($authUser['name'] ?? 'U', 0, 1), ENT_QUOTES, 'UTF-8') ?>
                    </div>
                    <span class="hidden sm:block font-medium">
                        <?= htmlspecialchars($authUser['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                    </span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open" @click.outside="open = false"
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-slate-100 py-1 z-50"
                     style="display:none">
                    <a href="/profile" class="flex items-center gap-2 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        My Profile
                    </a>
                    <a href="/settings" class="flex items-center gap-2 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Settings
                    </a>
                    <hr class="my-1 border-slate-100">
                    <a href="/logout" class="flex items-center gap-2 px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        Logout
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Flash messages -->
    <?php
    $flashSuccess = \App\Core\Session::getFlash('success');
    $flashError   = \App\Core\Session::getFlash('error');
    ?>
    <?php if ($flashSuccess): ?>
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
         class="mx-4 mt-4 flex items-center gap-3 p-4 bg-green-50 border border-green-200 rounded-xl text-green-800 text-sm">
        <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <?= htmlspecialchars($flashSuccess, ENT_QUOTES, 'UTF-8') ?>
        <button @click="show = false" class="ml-auto text-green-500 hover:text-green-700">&times;</button>
    </div>
    <?php endif; ?>
    <?php if ($flashError): ?>
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 7000)"
         class="mx-4 mt-4 flex items-center gap-3 p-4 bg-red-50 border border-red-200 rounded-xl text-red-800 text-sm">
        <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <?= htmlspecialchars($flashError, ENT_QUOTES, 'UTF-8') ?>
        <button @click="show = false" class="ml-auto text-red-500 hover:text-red-700">&times;</button>
    </div>
    <?php endif; ?>

    <!-- Page content -->
    <main class="flex-1 p-4 md:p-6">
        <?= $content ?>
    </main>

    <!-- Footer -->
    <footer class="px-4 py-3 text-center text-xs text-slate-400 border-t border-slate-200">
        &copy; <?= date('Y') ?> <?= htmlspecialchars($sidebarAppName, ENT_QUOTES, 'UTF-8') ?> &mdash; Domestic Water Purification Solutions
    </footer>
</div>

<!-- App JS -->
<script src="/assets/js/app.js"></script>
</body>
</html>

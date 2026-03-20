<?php
/**
 * Flash message partial.
 * Reads success / error / warning flashes from the session.
 * Include this at the top of any view that needs inline alerts.
 *
 * Usage:  <?php include VIEW_PATH . '/partials/flash.php'; ?>
 */
declare(strict_types=1);

$_flash_success = \App\Core\Session::getFlash('success');
$_flash_error   = \App\Core\Session::getFlash('error');
$_flash_warning = \App\Core\Session::getFlash('warning');
?>

<?php if ($_flash_success): ?>
<div x-data="{ show: true }"
     x-show="show"
     x-init="setTimeout(() => show = false, 5000)"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100 translate-y-0"
     x-transition:leave-end="opacity-0 -translate-y-2"
     role="alert"
     class="flex items-start gap-3 p-4 mb-4 bg-green-50 border border-green-200 rounded-xl text-green-800 text-sm shadow-sm">
    <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
    <p class="flex-1"><?= e($_flash_success) ?></p>
    <button @click="show = false" type="button"
            class="flex-shrink-0 text-green-500 hover:text-green-700 transition-colors"
            aria-label="Dismiss">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
    </button>
</div>
<?php endif; ?>

<?php if ($_flash_error): ?>
<div x-data="{ show: true }"
     x-show="show"
     x-init="setTimeout(() => show = false, 7000)"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100 translate-y-0"
     x-transition:leave-end="opacity-0 -translate-y-2"
     role="alert"
     class="flex items-start gap-3 p-4 mb-4 bg-red-50 border border-red-200 rounded-xl text-red-800 text-sm shadow-sm">
    <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
    <p class="flex-1"><?= e($_flash_error) ?></p>
    <button @click="show = false" type="button"
            class="flex-shrink-0 text-red-500 hover:text-red-700 transition-colors"
            aria-label="Dismiss">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
    </button>
</div>
<?php endif; ?>

<?php if ($_flash_warning): ?>
<div x-data="{ show: true }"
     x-show="show"
     x-init="setTimeout(() => show = false, 6000)"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100 translate-y-0"
     x-transition:leave-end="opacity-0 -translate-y-2"
     role="alert"
     class="flex items-start gap-3 p-4 mb-4 bg-yellow-50 border border-yellow-200 rounded-xl text-yellow-800 text-sm shadow-sm">
    <svg class="w-5 h-5 text-yellow-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
    </svg>
    <p class="flex-1"><?= e($_flash_warning) ?></p>
    <button @click="show = false" type="button"
            class="flex-shrink-0 text-yellow-500 hover:text-yellow-700 transition-colors"
            aria-label="Dismiss">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
    </button>
</div>
<?php endif; ?>

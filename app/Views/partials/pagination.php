<?php
/**
 * Pagination partial.
 *
 * Required variables:
 *   int    $total    – total number of records
 *   int    $page     – current page (1-based)
 *   int    $perPage  – records per page
 *   string $baseUrl  – URL without ?page=… (other query params already included)
 *
 * Usage:
 *   <?php include VIEW_PATH . '/partials/pagination.php'; ?>
 */
declare(strict_types=1);

if (empty($total) || empty($perPage) || $perPage <= 0) {
    return;
}

$totalPages  = (int)ceil($total / $perPage);
$currentPage = max(1, (int)($page ?? 1));

if ($totalPages <= 1) {
    return;
}

/** Build a page URL, preserving existing query string parameters. */
$buildUrl = function (int $p) use ($baseUrl): string {
    $sep = str_contains($baseUrl, '?') ? '&' : '?';
    return e($baseUrl . $sep . 'page=' . $p);
};

/**
 * Compute the window of page numbers to show (at most 7 slots).
 * Always shows first page, last page, current ±2, and ellipsis where needed.
 */
$pages = [];
if ($totalPages <= 7) {
    $pages = range(1, $totalPages);
} else {
    $pages[] = 1;
    if ($currentPage > 4) {
        $pages[] = '…';
    }
    $start = max(2, $currentPage - 2);
    $end   = min($totalPages - 1, $currentPage + 2);
    for ($i = $start; $i <= $end; $i++) {
        $pages[] = $i;
    }
    if ($currentPage < $totalPages - 3) {
        $pages[] = '…';
    }
    $pages[] = $totalPages;
}

$firstOnPage = ($currentPage - 1) * $perPage + 1;
$lastOnPage  = min($currentPage * $perPage, $total);
?>
<div class="flex flex-col sm:flex-row items-center justify-between gap-4 mt-6">
    <!-- Record range info -->
    <p class="text-sm text-slate-500">
        Showing
        <span class="font-medium text-slate-700"><?= e($firstOnPage) ?></span>
        &ndash;
        <span class="font-medium text-slate-700"><?= e($lastOnPage) ?></span>
        of
        <span class="font-medium text-slate-700"><?= e($total) ?></span>
        result<?= $total !== 1 ? 's' : '' ?>
    </p>

    <!-- Page links -->
    <nav aria-label="Pagination" class="flex items-center gap-1">
        <!-- Previous -->
        <?php if ($currentPage > 1): ?>
        <a href="<?= $buildUrl($currentPage - 1) ?>"
           class="inline-flex items-center gap-1 px-3 py-1.5 text-sm font-medium text-slate-600 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 hover:text-slate-800 transition-colors"
           aria-label="Previous page">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            <span class="hidden sm:inline">Prev</span>
        </a>
        <?php else: ?>
        <span class="inline-flex items-center gap-1 px-3 py-1.5 text-sm font-medium text-slate-400 bg-white border border-slate-200 rounded-lg cursor-not-allowed" aria-disabled="true">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            <span class="hidden sm:inline">Prev</span>
        </span>
        <?php endif; ?>

        <!-- Page numbers -->
        <?php foreach ($pages as $p): ?>
            <?php if ($p === '…'): ?>
            <span class="px-2 py-1.5 text-sm text-slate-400 select-none" aria-hidden="true">&hellip;</span>
            <?php elseif ((int)$p === $currentPage): ?>
            <span class="inline-flex items-center justify-center w-9 h-9 text-sm font-semibold text-white bg-sky-500 rounded-lg shadow-sm" aria-current="page">
                <?= e($p) ?>
            </span>
            <?php else: ?>
            <a href="<?= $buildUrl((int)$p) ?>"
               class="inline-flex items-center justify-center w-9 h-9 text-sm font-medium text-slate-600 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 hover:text-slate-800 transition-colors"
               aria-label="Page <?= e($p) ?>">
                <?= e($p) ?>
            </a>
            <?php endif; ?>
        <?php endforeach; ?>

        <!-- Next -->
        <?php if ($currentPage < $totalPages): ?>
        <a href="<?= $buildUrl($currentPage + 1) ?>"
           class="inline-flex items-center gap-1 px-3 py-1.5 text-sm font-medium text-slate-600 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 hover:text-slate-800 transition-colors"
           aria-label="Next page">
            <span class="hidden sm:inline">Next</span>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </a>
        <?php else: ?>
        <span class="inline-flex items-center gap-1 px-3 py-1.5 text-sm font-medium text-slate-400 bg-white border border-slate-200 rounded-lg cursor-not-allowed" aria-disabled="true">
            <span class="hidden sm:inline">Next</span>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </span>
        <?php endif; ?>
    </nav>
</div>

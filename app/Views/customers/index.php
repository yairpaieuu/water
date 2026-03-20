<?php
/**
 * Customers – Index
 * Variables: $customers, $branches, $total, $page, $perPage, $search, $branchFilter
 */
declare(strict_types=1);

$pageTitle = 'Customers';

/** Return a Tailwind badge class for a customer status string. */
function customerStatusBadge(string $status): string {
    return match ($status) {
        'active'   => 'bg-green-100 text-green-700 border border-green-200',
        'inactive' => 'bg-red-100 text-red-700 border border-red-200',
        'prospect' => 'bg-yellow-100 text-yellow-700 border border-yellow-200',
        default    => 'bg-slate-100 text-slate-600 border border-slate-200',
    };
}

$baseUrl = '/customers?' . http_build_query(array_filter([
    'search'       => $search       ?? '',
    'branch_id'    => $branchFilter ?? '',
]));
?>

<!-- Page header -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Customers</h1>
        <p class="text-sm text-slate-500 mt-0.5">
            <?= e($total ?? 0) ?> total customer<?= ($total ?? 0) !== 1 ? 's' : '' ?>
        </p>
    </div>
    <a href="/customers/create"
       class="inline-flex items-center gap-2 bg-sky-500 hover:bg-sky-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors shadow-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Add Customer
    </a>
</div>

<!-- Search / filter bar -->
<div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 mb-6">
    <form method="GET" action="/customers" class="flex flex-col sm:flex-row gap-3">
        <div class="flex-1 relative">
            <label for="search" class="sr-only">Search customers</label>
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input type="search"
                   id="search"
                   name="search"
                   value="<?= e($search ?? '') ?>"
                   placeholder="Search by name, code, phone, email…"
                   class="w-full pl-9 pr-4 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent">
        </div>

        <div>
            <label for="branch_id" class="sr-only">Filter by branch</label>
            <select id="branch_id" name="branch_id"
                    class="border border-slate-300 rounded-lg px-3 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-sky-500 w-full sm:w-48">
                <option value="">All Branches</option>
                <?php foreach ($branches as $b): ?>
                <option value="<?= e($b['id']) ?>"
                    <?= ((int)($branchFilter ?? 0) === (int)$b['id']) ? 'selected' : '' ?>>
                    <?= e($b['name']) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit"
                class="inline-flex items-center justify-center gap-2 bg-sky-500 hover:bg-sky-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
            </svg>
            Filter
        </button>

        <?php if (!empty($search) || !empty($branchFilter)): ?>
        <a href="/customers"
           class="inline-flex items-center justify-center px-4 py-2 rounded-lg border border-slate-300 text-slate-600 hover:bg-slate-50 text-sm font-medium transition-colors">
            Clear
        </a>
        <?php endif; ?>
    </form>
</div>

<!-- Table card -->
<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
    <?php if (empty($customers)): ?>
    <!-- Empty state -->
    <div class="flex flex-col items-center justify-center py-20 text-center px-4">
        <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mb-4">
            <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
        </div>
        <h3 class="text-base font-semibold text-slate-700 mb-1">No customers found</h3>
        <?php if (!empty($search) || !empty($branchFilter)): ?>
        <p class="text-sm text-slate-500 mb-4">Try adjusting your search or filter.</p>
        <a href="/customers" class="text-sm text-sky-600 hover:underline">Clear filters</a>
        <?php else: ?>
        <p class="text-sm text-slate-500 mb-4">Get started by adding your first customer.</p>
        <a href="/customers/create"
           class="inline-flex items-center gap-2 bg-sky-500 hover:bg-sky-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Add Customer
        </a>
        <?php endif; ?>
    </div>
    <?php else: ?>
    <!-- Desktop table -->
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200">
                    <th class="text-left px-4 py-3 font-semibold text-slate-600 whitespace-nowrap">Code</th>
                    <th class="text-left px-4 py-3 font-semibold text-slate-600">Name</th>
                    <th class="text-left px-4 py-3 font-semibold text-slate-600 hidden sm:table-cell">Phone</th>
                    <th class="text-left px-4 py-3 font-semibold text-slate-600 hidden md:table-cell">Email</th>
                    <th class="text-left px-4 py-3 font-semibold text-slate-600 hidden lg:table-cell">City</th>
                    <th class="text-left px-4 py-3 font-semibold text-slate-600 hidden lg:table-cell">Branch</th>
                    <th class="text-left px-4 py-3 font-semibold text-slate-600">Status</th>
                    <th class="text-right px-4 py-3 font-semibold text-slate-600">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php foreach ($customers as $c): ?>
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-4 py-3 font-mono text-xs text-sky-600 font-medium whitespace-nowrap">
                        <?= e($c['customer_code']) ?>
                    </td>
                    <td class="px-4 py-3">
                        <div class="font-medium text-slate-800">
                            <?= e(trim($c['first_name'] . ' ' . $c['last_name'])) ?>
                        </div>
                        <!-- Show phone on mobile below name -->
                        <div class="text-xs text-slate-500 sm:hidden mt-0.5"><?= e($c['phone'] ?? '') ?></div>
                    </td>
                    <td class="px-4 py-3 text-slate-600 hidden sm:table-cell whitespace-nowrap">
                        <?= e($c['phone'] ?? '—') ?>
                    </td>
                    <td class="px-4 py-3 text-slate-600 hidden md:table-cell">
                        <?= e($c['email'] ?? '—') ?>
                    </td>
                    <td class="px-4 py-3 text-slate-600 hidden lg:table-cell">
                        <?= e($c['city'] ?? '—') ?>
                    </td>
                    <td class="px-4 py-3 hidden lg:table-cell">
                        <?php if (!empty($c['branch_name'])): ?>
                        <span class="inline-flex items-center gap-1 text-xs text-slate-600">
                            <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                            <?= e($c['branch_name']) ?>
                        </span>
                        <?php else: ?>
                        <span class="text-slate-400">—</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-4 py-3">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize <?= customerStatusBadge($c['status'] ?? '') ?>">
                            <?= e($c['status'] ?? 'unknown') ?>
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right whitespace-nowrap">
                        <a href="/customers/<?= e($c['id']) ?>"
                           class="inline-flex items-center gap-1 text-xs text-sky-600 hover:text-sky-800 font-medium mr-3 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            View
                        </a>
                        <a href="/customers/<?= e($c['id']) ?>/edit"
                           class="inline-flex items-center gap-1 text-xs text-slate-600 hover:text-slate-900 font-medium transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Edit
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="px-4 py-4 border-t border-slate-100">
        <?php
        $baseUrl = '/customers?' . http_build_query(array_filter([
            'search'    => $search       ?? '',
            'branch_id' => $branchFilter ?? '',
        ]));
        include VIEW_PATH . '/partials/pagination.php';
        ?>
    </div>
    <?php endif; ?>
</div>

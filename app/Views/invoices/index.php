<?php
declare(strict_types=1);

$invoices = $invoices ?? [];
$status   = $status   ?? '';
$branchId = $branchId ?? 0;
$from     = $from     ?? '';
$to       = $to       ?? '';

function invStatusBadge(string $s): string
{
    return match ($s) {
        'confirmed' => 'bg-sky-100 text-sky-700 border border-sky-200',
        'delivered' => 'bg-green-100 text-green-700 border border-green-200',
        default     => 'bg-slate-100 text-slate-600 border border-slate-200',
    };
}

function invPaymentBadge(string $s): string
{
    return match ($s) {
        'pending' => 'bg-amber-100 text-amber-700 border border-amber-200',
        'partial' => 'bg-orange-100 text-orange-700 border border-orange-200',
        'paid'    => 'bg-emerald-100 text-emerald-700 border border-emerald-200',
        default   => 'bg-slate-100 text-slate-600 border border-slate-200',
    };
}
?>

<!-- Page header -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Invoices</h1>
        <p class="text-sm text-slate-500 mt-0.5"><?= count($invoices) ?> invoice<?= count($invoices) !== 1 ? 's' : '' ?></p>
    </div>
</div>

<!-- Filter bar -->
<div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 mb-6">
    <form method="GET" action="/invoices" class="flex flex-col sm:flex-row flex-wrap gap-3 items-start sm:items-center">
        <span class="text-sm font-medium text-slate-600 whitespace-nowrap">Filter by:</span>

        <select name="status"
                class="border border-slate-300 rounded-lg px-3 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-sky-500 w-full sm:w-40">
            <option value="">All Statuses</option>
            <option value="confirmed" <?= $status === 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
            <option value="delivered" <?= $status === 'delivered' ? 'selected' : '' ?>>Delivered</option>
        </select>

        <input type="date" name="from" value="<?= htmlspecialchars($from, ENT_QUOTES, 'UTF-8') ?>"
               placeholder="From date"
               class="border border-slate-300 rounded-lg px-3 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-sky-500 w-full sm:w-36">
        <input type="date" name="to" value="<?= htmlspecialchars($to, ENT_QUOTES, 'UTF-8') ?>"
               placeholder="To date"
               class="border border-slate-300 rounded-lg px-3 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-sky-500 w-full sm:w-36">

        <button type="submit"
                class="inline-flex items-center justify-center gap-2 bg-sky-500 hover:bg-sky-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
            </svg>
            Filter
        </button>

        <?php if ($status !== '' || $from !== '' || $to !== ''): ?>
        <a href="/invoices"
           class="inline-flex items-center justify-center px-4 py-2 rounded-lg border border-slate-300 text-slate-600 hover:bg-slate-50 text-sm font-medium transition-colors">
            Clear
        </a>
        <?php endif; ?>
    </form>
</div>

<!-- Table -->
<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
    <?php if (empty($invoices)): ?>
    <div class="flex flex-col items-center justify-center py-20 text-center px-4">
        <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mb-4">
            <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
        </div>
        <h3 class="text-base font-semibold text-slate-700 mb-1">No invoices found</h3>
        <p class="text-sm text-slate-500">Invoices appear here once a sales order is confirmed or delivered.</p>
    </div>
    <?php else: ?>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50">
                <tr>
                    <th class="text-left px-4 py-3 font-semibold text-slate-600 whitespace-nowrap">Invoice #</th>
                    <th class="text-left px-4 py-3 font-semibold text-slate-600">Customer</th>
                    <th class="text-left px-4 py-3 font-semibold text-slate-600 hidden md:table-cell">Date</th>
                    <th class="text-right px-4 py-3 font-semibold text-slate-600">Total</th>
                    <th class="text-center px-4 py-3 font-semibold text-slate-600">Status</th>
                    <th class="text-center px-4 py-3 font-semibold text-slate-600 hidden sm:table-cell">Payment</th>
                    <th class="text-right px-4 py-3 font-semibold text-slate-600">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php foreach ($invoices as $inv): ?>
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-4 py-3 font-mono text-xs text-sky-600 font-medium whitespace-nowrap">
                        <?= htmlspecialchars($inv['sale_code'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                    </td>
                    <td class="px-4 py-3 font-medium text-slate-800 truncate max-w-[160px]">
                        <?= htmlspecialchars($inv['customer_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?>
                    </td>
                    <td class="px-4 py-3 text-slate-600 whitespace-nowrap hidden md:table-cell">
                        <?= e(date('d M Y', strtotime($inv['sale_date'] ?? 'now'))) ?>
                    </td>
                    <td class="px-4 py-3 text-right font-medium text-slate-800 whitespace-nowrap">
                        LKR <?= number_format((float)($inv['total'] ?? 0), 2) ?>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize
                                     <?= invStatusBadge($inv['status'] ?? '') ?>">
                            <?= htmlspecialchars($inv['status'] ?? '—', ENT_QUOTES, 'UTF-8') ?>
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center hidden sm:table-cell">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize
                                     <?= invPaymentBadge($inv['payment_status'] ?? '') ?>">
                            <?= htmlspecialchars($inv['payment_status'] ?? '—', ENT_QUOTES, 'UTF-8') ?>
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right whitespace-nowrap">
                        <a href="/invoices/<?= e($inv['id']) ?>"
                           class="inline-flex items-center gap-1 text-xs text-sky-600 hover:text-sky-800 font-medium transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            View
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

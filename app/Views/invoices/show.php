<?php
declare(strict_types=1);

$sale   = $sale   ?? [];
$items  = $items  ?? [];

$pageTitle = 'Invoice — ' . ($sale['sale_code'] ?? '');

// Fetch business name for the invoice header
$invoiceAppName = 'AquaCRM';
try {
    $nameRow = \App\Core\Database::getInstance()->fetch(
        "SELECT `value` FROM `settings` WHERE `key` = 'app_name' LIMIT 1"
    );
    if ($nameRow && !empty($nameRow['value'])) {
        $invoiceAppName = $nameRow['value'];
    }
} catch (\Throwable) {}

function invShowPaymentBadge(string $s): string
{
    return match ($s) {
        'pending' => 'bg-amber-100 text-amber-700 border border-amber-200',
        'partial' => 'bg-orange-100 text-orange-700 border border-orange-200',
        'paid'    => 'bg-emerald-100 text-emerald-700 border border-emerald-200',
        default   => 'bg-slate-100 text-slate-600 border border-slate-200',
    };
}
?>

<!-- Print styles -->
<style>
@media print {
    aside, header, footer, .no-print { display: none !important; }
    body { background: white !important; }
    .shadow-sm, .shadow { box-shadow: none !important; }
}
</style>

<!-- Page header -->
<div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 mb-6 no-print">
    <div class="flex items-center gap-3">
        <a href="/invoices"
           class="flex items-center justify-center w-9 h-9 rounded-lg border border-slate-200 bg-white text-slate-500 hover:text-slate-700 hover:bg-slate-50 transition-colors shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <div class="flex items-center gap-2 flex-wrap">
                <h1 class="text-2xl font-bold text-slate-800">
                    Invoice <?= htmlspecialchars($sale['sale_code'] ?? '—', ENT_QUOTES, 'UTF-8') ?>
                </h1>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize <?= invShowPaymentBadge($sale['payment_status'] ?? '') ?>">
                    <?= htmlspecialchars($sale['payment_status'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                </span>
            </div>
            <p class="text-sm text-slate-500 mt-0.5">
                <?= e(date('d F Y', strtotime($sale['sale_date'] ?? 'now'))) ?>
            </p>
        </div>
    </div>
    <div class="flex items-center gap-2 no-print">
        <button type="button" onclick="window.print()"
                class="inline-flex items-center gap-2 bg-white border border-slate-300 text-slate-600 hover:bg-slate-50 px-4 py-2 rounded-lg text-sm font-medium transition-colors shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
            </svg>
            Print
        </button>
        <a href="/orders/<?= e($sale['id'] ?? '') ?>"
           class="inline-flex items-center gap-2 bg-sky-500 hover:bg-sky-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors shadow-sm">
            View Order
        </a>
    </div>
</div>

<!-- Invoice card -->
<div class="bg-white rounded-xl shadow-sm border border-slate-200 p-8 max-w-3xl mx-auto">

    <!-- Header: Company + Invoice title -->
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-6 mb-8 pb-6 border-b border-slate-200">
        <div>
            <h2 class="text-2xl font-bold text-sky-600"><?= htmlspecialchars($invoiceAppName, ENT_QUOTES, 'UTF-8') ?></h2>
            <p class="text-sm text-slate-500 mt-1">Water Purification Services</p>
        </div>
        <div class="text-right">
            <p class="text-3xl font-extrabold text-slate-800 tracking-tight uppercase">Invoice</p>
            <p class="text-sm font-mono text-sky-600 mt-1"><?= htmlspecialchars($sale['sale_code'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
            <p class="text-xs text-slate-500 mt-0.5">Date: <?= e(date('d M Y', strtotime($sale['sale_date'] ?? 'now'))) ?></p>
        </div>
    </div>

    <!-- Bill To -->
    <div class="mb-8">
        <p class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Bill To</p>
        <p class="font-semibold text-slate-800">
            <?= htmlspecialchars($sale['customer_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?>
        </p>
        <?php if (!empty($sale['customer_phone'])): ?>
        <p class="text-sm text-slate-500"><?= htmlspecialchars($sale['customer_phone'], ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>
        <?php if (!empty($sale['customer_email'])): ?>
        <p class="text-sm text-slate-500"><?= htmlspecialchars($sale['customer_email'], ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>
    </div>

    <!-- Line items table -->
    <div class="overflow-x-auto mb-8">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b-2 border-slate-200">
                    <th class="text-left pb-2 font-semibold text-slate-600">Description</th>
                    <th class="text-center pb-2 font-semibold text-slate-600 w-16">Qty</th>
                    <th class="text-right pb-2 font-semibold text-slate-600 w-28">Unit Price</th>
                    <th class="text-right pb-2 font-semibold text-slate-600 w-24">Discount</th>
                    <th class="text-right pb-2 font-semibold text-slate-600 w-28">Amount</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php foreach ($items as $item): ?>
                <tr>
                    <td class="py-3 font-medium text-slate-800"><?= htmlspecialchars($item['product_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="py-3 text-center text-slate-600"><?= (int)($item['quantity'] ?? 0) ?></td>
                    <td class="py-3 text-right text-slate-600">LKR <?= number_format((float)($item['unit_price'] ?? 0), 2) ?></td>
                    <td class="py-3 text-right text-slate-600">
                        <?= ($item['discount'] ?? 0) > 0 ? 'LKR ' . number_format((float)$item['discount'], 2) : '—' ?>
                    </td>
                    <td class="py-3 text-right font-medium text-slate-800">LKR <?= number_format((float)($item['total'] ?? 0), 2) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Totals -->
    <div class="flex justify-end mb-8">
        <div class="w-full sm:w-72 space-y-2 text-sm">
            <div class="flex justify-between text-slate-600">
                <span>Subtotal</span>
                <span>LKR <?= number_format((float)($sale['subtotal'] ?? 0), 2) ?></span>
            </div>
            <?php if (($sale['discount'] ?? 0) > 0): ?>
            <div class="flex justify-between text-slate-600">
                <span>Discount</span>
                <span class="text-red-600">− LKR <?= number_format((float)$sale['discount'], 2) ?></span>
            </div>
            <?php endif; ?>
            <?php if (($sale['tax'] ?? 0) > 0): ?>
            <div class="flex justify-between text-slate-600">
                <span>Tax</span>
                <span>LKR <?= number_format((float)$sale['tax'], 2) ?></span>
            </div>
            <?php endif; ?>
            <div class="border-t border-slate-200 pt-2 flex justify-between font-bold text-slate-800 text-base">
                <span>Total</span>
                <span>LKR <?= number_format((float)($sale['total'] ?? 0), 2) ?></span>
            </div>
            <?php if (($sale['paid_amount'] ?? 0) > 0): ?>
            <div class="flex justify-between text-emerald-600 font-medium">
                <span>Paid</span>
                <span>LKR <?= number_format((float)$sale['paid_amount'], 2) ?></span>
            </div>
            <?php $balance = (float)($sale['total'] ?? 0) - (float)($sale['paid_amount'] ?? 0); ?>
            <?php if ($balance > 0): ?>
            <div class="flex justify-between text-red-600 font-semibold">
                <span>Balance Due</span>
                <span>LKR <?= number_format($balance, 2) ?></span>
            </div>
            <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Payment status stamp -->
    <div class="text-center border-t border-slate-200 pt-6">
        <?php if (($sale['payment_status'] ?? '') === 'paid'): ?>
        <span class="inline-block px-6 py-2 border-4 border-emerald-400 text-emerald-600 text-xl font-extrabold uppercase tracking-widest rounded-lg opacity-80">
            PAID
        </span>
        <?php elseif (($sale['payment_status'] ?? '') === 'partial'): ?>
        <span class="inline-block px-6 py-2 border-4 border-orange-400 text-orange-600 text-xl font-extrabold uppercase tracking-widest rounded-lg opacity-80">
            PARTIAL
        </span>
        <?php else: ?>
        <span class="inline-block px-6 py-2 border-4 border-amber-400 text-amber-600 text-xl font-extrabold uppercase tracking-widest rounded-lg opacity-80">
            UNPAID
        </span>
        <?php endif; ?>
        <?php if (!empty($sale['notes'])): ?>
        <p class="text-xs text-slate-500 mt-4"><?= htmlspecialchars($sale['notes'], ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>
    </div>
</div>

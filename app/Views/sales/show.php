<?php
/**
 * Sales – Show
 * Variables: $sale (array), $items (array), $customer (array)
 */
declare(strict_types=1);

$sale     = $sale     ?? [];
$items    = $items    ?? [];
$customer = $customer ?? [];

$pageTitle = $sale['sale_code'] ?? 'Order';

function showSaleStatusBadge(string $s): string
{
    return match ($s) {
        'quotation' => 'bg-blue-100 text-blue-700 border border-blue-200',
        'confirmed' => 'bg-sky-100 text-sky-700 border border-sky-200',
        'delivered' => 'bg-green-100 text-green-700 border border-green-200',
        'cancelled' => 'bg-red-100 text-red-700 border border-red-200',
        default     => 'bg-slate-100 text-slate-600 border border-slate-200',
    };
}

function showPaymentBadge(string $s): string
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
    .print-full { width: 100% !important; max-width: none !important; }
    body { background: white !important; }
    .shadow-sm, .shadow { box-shadow: none !important; }
}
</style>

<!-- Page header -->
<div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 mb-6 no-print">
    <div class="flex items-center gap-3">
        <a href="/orders"
           class="flex items-center justify-center w-9 h-9 rounded-lg border border-slate-200 bg-white text-slate-500 hover:text-slate-700 hover:bg-slate-50 transition-colors shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <div class="flex items-center gap-2 flex-wrap">
                <h1 class="text-2xl font-bold text-slate-800"><?= e($sale['sale_code'] ?? '—') ?></h1>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize <?= showSaleStatusBadge($sale['status'] ?? '') ?>">
                    <?= e($sale['status'] ?? '') ?>
                </span>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize <?= showPaymentBadge($sale['payment_status'] ?? '') ?>">
                    <?= e($sale['payment_status'] ?? '') ?>
                </span>
            </div>
            <p class="text-sm text-slate-500 mt-0.5">
                <?= e(date('d F Y', strtotime($sale['sale_date'] ?? 'now'))) ?>
            </p>
        </div>
    </div>

    <div class="flex items-center gap-2 flex-wrap">
        <button type="button" onclick="window.print()"
                class="inline-flex items-center gap-2 bg-white border border-slate-300 text-slate-600 hover:bg-slate-50 px-4 py-2 rounded-lg text-sm font-medium transition-colors shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            Print
        </button>
    </div>
</div>

<!-- Main content grid -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 print-full">

    <!-- LEFT col (2/3): Details + Items -->
    <div class="lg:col-span-2 space-y-6">

        <!-- Sale Details card -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <h2 class="text-base font-semibold text-slate-700 mb-4">Sale Details</h2>
            <dl class="grid grid-cols-2 gap-x-6 gap-y-3 text-sm">
                <div>
                    <dt class="text-slate-500">Order Code</dt>
                    <dd class="font-mono font-medium text-sky-600"><?= e($sale['sale_code'] ?? '—') ?></dd>
                </div>
                <div>
                    <dt class="text-slate-500">Sale Date</dt>
                    <dd class="font-medium text-slate-800"><?= e(date('d M Y', strtotime($sale['sale_date'] ?? 'now'))) ?></dd>
                </div>
                <div>
                    <dt class="text-slate-500">Order Status</dt>
                    <dd>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize <?= showSaleStatusBadge($sale['status'] ?? '') ?>">
                            <?= e($sale['status'] ?? '—') ?>
                        </span>
                    </dd>
                </div>
                <div>
                    <dt class="text-slate-500">Payment Status</dt>
                    <dd>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize <?= showPaymentBadge($sale['payment_status'] ?? '') ?>">
                            <?= e($sale['payment_status'] ?? '—') ?>
                        </span>
                    </dd>
                </div>
                <div>
                    <dt class="text-slate-500">Customer</dt>
                    <dd class="font-medium text-slate-800">
                        <a href="/customers/<?= e($customer['id'] ?? '') ?>" class="text-sky-600 hover:underline">
                            <?= e(trim(($customer['first_name'] ?? '') . ' ' . ($customer['last_name'] ?? ''))) ?>
                        </a>
                    </dd>
                </div>
                <div>
                    <dt class="text-slate-500">Created</dt>
                    <dd class="text-slate-700"><?= e(date('d M Y H:i', strtotime($sale['created_at'] ?? 'now'))) ?></dd>
                </div>
                <?php if (!empty($sale['notes'])): ?>
                <div class="col-span-2">
                    <dt class="text-slate-500">Notes</dt>
                    <dd class="text-slate-700 whitespace-pre-wrap"><?= e($sale['notes']) ?></dd>
                </div>
                <?php endif; ?>
            </dl>
        </div>

        <!-- Line Items card -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200">
                <h2 class="text-base font-semibold text-slate-700">Line Items</h2>
            </div>
            <?php if (empty($items)): ?>
            <p class="text-sm text-slate-400 text-center py-10">No line items found for this order.</p>
            <?php else: ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="text-left px-4 py-3 font-semibold text-slate-600">Product</th>
                            <th class="text-center px-4 py-3 font-semibold text-slate-600 w-16">Qty</th>
                            <th class="text-right px-4 py-3 font-semibold text-slate-600 w-28">Unit Price</th>
                            <th class="text-right px-4 py-3 font-semibold text-slate-600 w-24">Discount</th>
                            <th class="text-right px-4 py-3 font-semibold text-slate-600 w-28">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php foreach ($items as $item): ?>
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 font-medium text-slate-800"><?= e($item['product_name'] ?? '—') ?></td>
                            <td class="px-4 py-3 text-center text-slate-600"><?= e($item['quantity'] ?? 0) ?></td>
                            <td class="px-4 py-3 text-right text-slate-600">LKR <?= number_format((float)($item['unit_price'] ?? 0), 2) ?></td>
                            <td class="px-4 py-3 text-right text-slate-600">
                                <?php if (($item['discount'] ?? 0) > 0): ?>
                                    LKR <?= number_format((float)$item['discount'], 2) ?>
                                <?php else: ?>
                                    —
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 text-right font-medium text-slate-800">
                                LKR <?= number_format((float)($item['total'] ?? 0), 2) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>

        <!-- Customer Info -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <h2 class="text-base font-semibold text-slate-700 mb-4">Customer Information</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-slate-500">Name</p>
                    <p class="font-medium text-slate-800">
                        <?= e(trim(($customer['first_name'] ?? '') . ' ' . ($customer['last_name'] ?? ''))) ?>
                    </p>
                </div>
                <?php if (!empty($customer['phone'])): ?>
                <div>
                    <p class="text-slate-500">Phone</p>
                    <p class="font-medium text-slate-800"><?= e($customer['phone']) ?></p>
                </div>
                <?php endif; ?>
                <?php if (!empty($customer['email'])): ?>
                <div>
                    <p class="text-slate-500">Email</p>
                    <p class="font-medium text-slate-800"><?= e($customer['email']) ?></p>
                </div>
                <?php endif; ?>
                <?php if (!empty($customer['address']) || !empty($customer['city'])): ?>
                <div class="sm:col-span-2">
                    <p class="text-slate-500">Address</p>
                    <p class="font-medium text-slate-800">
                        <?= e(implode(', ', array_filter([$customer['address'] ?? '', $customer['city'] ?? '']))) ?>
                    </p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- RIGHT col (1/3): Financial summary + Status update -->
    <div class="space-y-6">

        <!-- Financial Summary -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <h2 class="text-base font-semibold text-slate-700 mb-4">Financial Summary</h2>
            <div class="space-y-3 text-sm">
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
                <div class="flex justify-between text-slate-600">
                    <span>Tax</span>
                    <span>LKR <?= number_format((float)($sale['tax'] ?? 0), 2) ?></span>
                </div>
                <div class="border-t border-slate-200 pt-3 flex justify-between font-bold text-slate-800 text-base">
                    <span>Total</span>
                    <span>LKR <?= number_format((float)($sale['total'] ?? 0), 2) ?></span>
                </div>
            </div>
        </div>

        <!-- Status Update form -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 no-print">
            <h2 class="text-base font-semibold text-slate-700 mb-4">Update Status</h2>
            <form method="POST" action="/orders/<?= e($sale['id'] ?? '') ?>/update">
                <?= \App\Core\CSRF::field() ?>
                <div class="space-y-4">
                    <div>
                        <label for="update_status" class="block text-sm font-medium text-slate-700 mb-1">Order Status</label>
                        <select id="update_status" name="status"
                                class="block w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500">
                            <?php foreach (['quotation' => 'Quotation', 'confirmed' => 'Confirmed', 'delivered' => 'Delivered', 'cancelled' => 'Cancelled'] as $val => $label): ?>
                            <option value="<?= e($val) ?>" <?= ($sale['status'] ?? '') === $val ? 'selected' : '' ?>>
                                <?= e($label) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label for="update_payment" class="block text-sm font-medium text-slate-700 mb-1">Payment Status</label>
                        <select id="update_payment" name="payment_status"
                                class="block w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500">
                            <?php foreach (['pending' => 'Pending', 'partial' => 'Partial', 'paid' => 'Paid'] as $val => $label): ?>
                            <option value="<?= e($val) ?>" <?= ($sale['payment_status'] ?? '') === $val ? 'selected' : '' ?>>
                                <?= e($label) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit"
                            class="w-full inline-flex items-center justify-center gap-2 bg-sky-500 hover:bg-sky-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

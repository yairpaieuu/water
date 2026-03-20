<?php
/**
 * Sales – Index
 * Variables: $sales (array), $total, $page, $perPage, $statusFilter, $paymentFilter
 */
declare(strict_types=1);

$pageTitle     = 'Sales Orders';
$statusFilter  = $statusFilter  ?? '';
$paymentFilter = $paymentFilter ?? '';
$sales         = $sales         ?? [];
$total         = $total         ?? 0;
$page          = $page          ?? 1;
$perPage       = $perPage       ?? 20;

function saleStatusBadge(string $status): string
{
    return match ($status) {
        'quotation' => 'bg-blue-100 text-blue-700 border border-blue-200',
        'confirmed' => 'bg-sky-100 text-sky-700 border border-sky-200',
        'delivered' => 'bg-green-100 text-green-700 border border-green-200',
        'cancelled' => 'bg-red-100 text-red-700 border border-red-200',
        default     => 'bg-slate-100 text-slate-600 border border-slate-200',
    };
}

function paymentStatusBadge(string $status): string
{
    return match ($status) {
        'pending' => 'bg-amber-100 text-amber-700 border border-amber-200',
        'partial' => 'bg-orange-100 text-orange-700 border border-orange-200',
        'paid'    => 'bg-emerald-100 text-emerald-700 border border-emerald-200',
        default   => 'bg-slate-100 text-slate-600 border border-slate-200',
    };
}

// Stats derived from $sales or passed separately; fall back to counting in-memory
$countTotal     = $total;
$countConfirmed = 0;
$countDelivered = 0;
$monthRevenue   = 0.0;
$currentYM      = date('Y-m');
foreach ($sales as $s) {
    if (($s['status'] ?? '') === 'confirmed') { $countConfirmed++; }
    if (($s['status'] ?? '') === 'delivered') { $countDelivered++; }
    if (str_starts_with($s['sale_date'] ?? '', $currentYM)) {
        $monthRevenue += (float)($s['total'] ?? 0);
    }
}

$baseUrl = '/orders?' . http_build_query(array_filter([
    'status'         => $statusFilter,
    'payment_status' => $paymentFilter,
]));
?>

<!-- Page header -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Sales Orders</h1>
        <p class="text-sm text-slate-500 mt-0.5">
            <?= e($total) ?> total order<?= $total !== 1 ? 's' : '' ?>
        </p>
    </div>
    <a href="/orders/create"
       class="inline-flex items-center gap-2 bg-sky-500 hover:bg-sky-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors shadow-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        New Order
    </a>
</div>

<!-- Stats row -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <!-- Total Orders -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4">
        <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Total Orders</p>
        <p class="mt-1 text-2xl font-bold text-slate-800"><?= e($countTotal) ?></p>
        <div class="mt-2 flex items-center gap-1 text-xs text-slate-500">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            All time
        </div>
    </div>

    <!-- Confirmed -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4">
        <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Confirmed</p>
        <p class="mt-1 text-2xl font-bold text-sky-600"><?= e($countConfirmed) ?></p>
        <div class="mt-2 flex items-center gap-1 text-xs text-slate-500">
            <span class="inline-block w-2 h-2 rounded-full bg-sky-400"></span>
            Awaiting delivery
        </div>
    </div>

    <!-- Delivered -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4">
        <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Delivered</p>
        <p class="mt-1 text-2xl font-bold text-green-600"><?= e($countDelivered) ?></p>
        <div class="mt-2 flex items-center gap-1 text-xs text-slate-500">
            <span class="inline-block w-2 h-2 rounded-full bg-green-400"></span>
            Completed
        </div>
    </div>

    <!-- This Month Revenue -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4">
        <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">This Month</p>
        <p class="mt-1 text-2xl font-bold text-emerald-600">
            LKR <?= number_format($monthRevenue, 2) ?>
        </p>
        <div class="mt-2 flex items-center gap-1 text-xs text-slate-500">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <?= date('F Y') ?>
        </div>
    </div>
</div>

<!-- Filter bar -->
<div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 mb-6">
    <form method="GET" action="/orders" class="flex flex-col sm:flex-row gap-3 items-start sm:items-center">
        <span class="text-sm font-medium text-slate-600 whitespace-nowrap">Filter by:</span>

        <div>
            <label for="status_filter" class="sr-only">Order Status</label>
            <select id="status_filter" name="status"
                    class="border border-slate-300 rounded-lg px-3 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-sky-500 w-full sm:w-44">
                <option value="">All Statuses</option>
                <?php foreach (['quotation' => 'Quotation', 'confirmed' => 'Confirmed', 'delivered' => 'Delivered', 'cancelled' => 'Cancelled'] as $val => $label): ?>
                <option value="<?= e($val) ?>" <?= $statusFilter === $val ? 'selected' : '' ?>>
                    <?= e($label) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label for="payment_filter" class="sr-only">Payment Status</label>
            <select id="payment_filter" name="payment_status"
                    class="border border-slate-300 rounded-lg px-3 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-sky-500 w-full sm:w-44">
                <option value="">All Payments</option>
                <?php foreach (['pending' => 'Pending', 'partial' => 'Partial', 'paid' => 'Paid'] as $val => $label): ?>
                <option value="<?= e($val) ?>" <?= $paymentFilter === $val ? 'selected' : '' ?>>
                    <?= e($label) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit"
                class="inline-flex items-center justify-center gap-2 bg-sky-500 hover:bg-sky-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
            </svg>
            Filter
        </button>

        <?php if ($statusFilter !== '' || $paymentFilter !== ''): ?>
        <a href="/orders"
           class="inline-flex items-center justify-center px-4 py-2 rounded-lg border border-slate-300 text-slate-600 hover:bg-slate-50 text-sm font-medium transition-colors">
            Clear
        </a>
        <?php endif; ?>
    </form>
</div>

<!-- Table card -->
<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
    <?php if (empty($sales)): ?>
    <!-- Empty state -->
    <div class="flex flex-col items-center justify-center py-20 text-center px-4">
        <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mb-4">
            <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
        </div>
        <h3 class="text-base font-semibold text-slate-700 mb-1">No orders found</h3>
        <?php if ($statusFilter !== '' || $paymentFilter !== ''): ?>
        <p class="text-sm text-slate-500 mb-4">Try adjusting your filters.</p>
        <a href="/orders" class="text-sm text-sky-600 hover:underline">Clear filters</a>
        <?php else: ?>
        <p class="text-sm text-slate-500 mb-4">Create your first sales order to get started.</p>
        <a href="/orders/create"
           class="inline-flex items-center gap-2 bg-sky-500 hover:bg-sky-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            New Order
        </a>
        <?php endif; ?>
    </div>

    <?php else: ?>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50">
                <tr>
                    <th class="text-left px-4 py-3 font-semibold text-slate-600 whitespace-nowrap">Order Code</th>
                    <th class="text-left px-4 py-3 font-semibold text-slate-600">Customer</th>
                    <th class="text-left px-4 py-3 font-semibold text-slate-600 hidden md:table-cell">Date</th>
                    <th class="text-center px-4 py-3 font-semibold text-slate-600 hidden lg:table-cell">Items</th>
                    <th class="text-right px-4 py-3 font-semibold text-slate-600">Total</th>
                    <th class="text-center px-4 py-3 font-semibold text-slate-600">Status</th>
                    <th class="text-center px-4 py-3 font-semibold text-slate-600 hidden sm:table-cell">Payment</th>
                    <th class="text-right px-4 py-3 font-semibold text-slate-600">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php foreach ($sales as $sale): ?>
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-4 py-3 font-mono text-xs text-sky-600 font-medium whitespace-nowrap">
                        <?= e($sale['sale_code'] ?? '') ?>
                    </td>
                    <td class="px-4 py-3">
                        <div class="font-medium text-slate-800 truncate max-w-[160px]">
                            <?= e($sale['customer_name'] ?? '—') ?>
                        </div>
                        <div class="text-xs text-slate-400 sm:hidden mt-0.5 capitalize">
                            <?= e($sale['payment_status'] ?? '') ?>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-slate-600 whitespace-nowrap hidden md:table-cell">
                        <?= e(date('d M Y', strtotime($sale['sale_date'] ?? 'now'))) ?>
                    </td>
                    <td class="px-4 py-3 text-center text-slate-600 hidden lg:table-cell">
                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-slate-100 text-xs font-medium text-slate-700">
                            <?= e($sale['items_count'] ?? '0') ?>
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right font-medium text-slate-800 whitespace-nowrap">
                        LKR <?= number_format((float)($sale['total'] ?? 0), 2) ?>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize
                                     <?= saleStatusBadge($sale['status'] ?? '') ?>">
                            <?= e($sale['status'] ?? '—') ?>
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center hidden sm:table-cell">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize
                                     <?= paymentStatusBadge($sale['payment_status'] ?? '') ?>">
                            <?= e($sale['payment_status'] ?? '—') ?>
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right whitespace-nowrap">
                        <a href="/orders/<?= e($sale['id']) ?>"
                           class="inline-flex items-center gap-1 text-xs text-sky-600 hover:text-sky-800 font-medium mr-3 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            View
                        </a>
                        <button type="button"
                                onclick="confirmDelete(<?= e((string)$sale['id']) ?>, '<?= e($sale['sale_code'] ?? '') ?>')"
                                class="inline-flex items-center gap-1 text-xs text-red-500 hover:text-red-700 font-medium transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Delete
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="px-4 py-4 border-t border-slate-100">
        <?php include VIEW_PATH . '/partials/pagination.php'; ?>
    </div>
    <?php endif; ?>
</div>

<!-- Hidden delete form -->
<form id="deleteForm" method="POST" style="display:none">
    <?= \App\Core\CSRF::field() ?>
    <input type="hidden" name="_method" value="DELETE">
</form>

<script>
function confirmDelete(id, code) {
    Swal.fire({
        title: 'Delete Order?',
        html: `Are you sure you want to delete order <strong>${code}</strong>?<br>This action cannot be undone.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#94a3b8',
        confirmButtonText: 'Yes, delete it',
        cancelButtonText: 'Cancel',
    }).then(result => {
        if (result.isConfirmed) {
            const form = document.getElementById('deleteForm');
            form.action = `/orders/${id}/delete`;
            form.submit();
        }
    });
}
</script>

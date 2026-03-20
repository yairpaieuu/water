<?php
/**
 * Customers – Show
 * Variables: $customer (array), $contracts (array), $recentJobs (array), $branch (array)
 */
declare(strict_types=1);

$pageTitle  = trim(($customer['first_name'] ?? '') . ' ' . ($customer['last_name'] ?? ''));
$fullName   = $pageTitle;

$statusBadge = match ($customer['status'] ?? '') {
    'active'   => 'bg-green-100 text-green-700 border border-green-200',
    'inactive' => 'bg-red-100 text-red-700 border border-red-200',
    'prospect' => 'bg-yellow-100 text-yellow-700 border border-yellow-200',
    default    => 'bg-slate-100 text-slate-600 border border-slate-200',
};

$sourceLabels = [
    'walk_in'         => 'Walk-in',
    'referral'        => 'Referral',
    'online'          => 'Online',
    'site_survey'     => 'Site Survey',
    'lead_conversion' => 'Lead Conversion',
];

$jobStatusBadge = function (string $s): string {
    return match ($s) {
        'pending'     => 'bg-yellow-100 text-yellow-700',
        'assigned'    => 'bg-blue-100 text-blue-700',
        'in_progress' => 'bg-sky-100 text-sky-700',
        'completed'   => 'bg-green-100 text-green-700',
        'cancelled'   => 'bg-red-100 text-red-700',
        default       => 'bg-slate-100 text-slate-600',
    };
};
?>

<!-- Page header -->
<div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 mb-6">
    <div class="flex items-start gap-3">
        <a href="/customers"
           class="mt-1 flex items-center justify-center w-9 h-9 rounded-lg border border-slate-200 bg-white text-slate-500 hover:text-slate-700 hover:bg-slate-50 transition-colors shadow-sm flex-shrink-0"
           title="Back to customers">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <div class="flex flex-wrap items-center gap-2">
                <h1 class="text-2xl font-bold text-slate-800"><?= e($fullName) ?></h1>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize <?= $statusBadge ?>">
                    <?= e($customer['status'] ?? '') ?>
                </span>
            </div>
            <p class="text-sm text-slate-500 mt-0.5 font-mono"><?= e($customer['customer_code'] ?? '') ?></p>
        </div>
    </div>

    <div class="flex items-center gap-2 flex-wrap sm:flex-nowrap">
        <a href="/customers/<?= e($customer['id']) ?>/edit"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-slate-300 bg-white text-slate-700 hover:bg-slate-50 text-sm font-medium transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            Edit
        </a>
        <button type="button"
                onclick="confirmDelete()"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 text-sm font-medium transition-colors border border-red-200">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
            Delete
        </button>
    </div>
</div>

<!-- Quick actions -->
<div class="flex flex-wrap gap-3 mb-6">
    <a href="/contracts/create?customer_id=<?= e($customer['id']) ?>"
       class="inline-flex items-center gap-2 bg-sky-500 hover:bg-sky-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors shadow-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        Add Service Contract
    </a>
    <a href="/jobs/create?customer_id=<?= e($customer['id']) ?>"
       class="inline-flex items-center gap-2 bg-white hover:bg-slate-50 text-slate-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors shadow-sm border border-slate-200">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
        Create Job
    </a>
</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

    <!-- Customer details card -->
    <div class="xl:col-span-2 space-y-6">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <h2 class="text-base font-semibold text-slate-700 mb-5 pb-4 border-b border-slate-100">
                Customer Details
            </h2>
            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-5 text-sm">
                <div>
                    <dt class="text-slate-500 font-medium mb-0.5">Customer Code</dt>
                    <dd class="text-slate-800 font-mono"><?= e($customer['customer_code'] ?? '—') ?></dd>
                </div>
                <div>
                    <dt class="text-slate-500 font-medium mb-0.5">Full Name</dt>
                    <dd class="text-slate-800 font-medium"><?= e($fullName) ?: '—' ?></dd>
                </div>
                <div>
                    <dt class="text-slate-500 font-medium mb-0.5">Phone</dt>
                    <dd class="text-slate-800">
                        <?php if (!empty($customer['phone'])): ?>
                        <a href="tel:<?= e($customer['phone']) ?>" class="text-sky-600 hover:underline">
                            <?= e($customer['phone']) ?>
                        </a>
                        <?php else: ?>—<?php endif; ?>
                    </dd>
                </div>
                <div>
                    <dt class="text-slate-500 font-medium mb-0.5">Alternate Phone</dt>
                    <dd class="text-slate-800">
                        <?php if (!empty($customer['alt_phone'])): ?>
                        <a href="tel:<?= e($customer['alt_phone']) ?>" class="text-sky-600 hover:underline">
                            <?= e($customer['alt_phone']) ?>
                        </a>
                        <?php else: ?>—<?php endif; ?>
                    </dd>
                </div>
                <div>
                    <dt class="text-slate-500 font-medium mb-0.5">Email</dt>
                    <dd class="text-slate-800">
                        <?php if (!empty($customer['email'])): ?>
                        <a href="mailto:<?= e($customer['email']) ?>" class="text-sky-600 hover:underline">
                            <?= e($customer['email']) ?>
                        </a>
                        <?php else: ?>—<?php endif; ?>
                    </dd>
                </div>
                <div>
                    <dt class="text-slate-500 font-medium mb-0.5">Branch</dt>
                    <dd class="text-slate-800"><?= e($branch['name'] ?? $customer['branch_name'] ?? '—') ?></dd>
                </div>
                <div class="sm:col-span-2">
                    <dt class="text-slate-500 font-medium mb-0.5">Address</dt>
                    <dd class="text-slate-800">
                        <?php
                        $addr = array_filter([
                            $customer['address']     ?? '',
                            $customer['city']        ?? '',
                            $customer['state']       ?? '',
                            $customer['postal_code'] ?? '',
                        ]);
                        echo $addr ? e(implode(', ', $addr)) : '—';
                        ?>
                    </dd>
                </div>
                <div>
                    <dt class="text-slate-500 font-medium mb-0.5">Source</dt>
                    <dd class="text-slate-800"><?= e($sourceLabels[$customer['source'] ?? ''] ?? ucfirst($customer['source'] ?? '—')) ?></dd>
                </div>
                <div>
                    <dt class="text-slate-500 font-medium mb-0.5">Member Since</dt>
                    <dd class="text-slate-800">
                        <?= !empty($customer['created_at']) ? e(date('d M Y', strtotime($customer['created_at']))) : '—' ?>
                    </dd>
                </div>
                <?php if (!empty($customer['notes'])): ?>
                <div class="sm:col-span-2">
                    <dt class="text-slate-500 font-medium mb-0.5">Notes</dt>
                    <dd class="text-slate-800 whitespace-pre-wrap"><?= e($customer['notes']) ?></dd>
                </div>
                <?php endif; ?>
            </dl>
        </div>

        <!-- Service Contracts -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
                <h2 class="text-base font-semibold text-slate-700">Service Contracts</h2>
                <a href="/contracts/create?customer_id=<?= e($customer['id']) ?>"
                   class="inline-flex items-center gap-1 text-xs text-sky-600 hover:text-sky-800 font-medium transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add Contract
                </a>
            </div>
            <?php if (empty($contracts)): ?>
            <div class="flex flex-col items-center py-12 text-center px-4">
                <svg class="w-10 h-10 text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <p class="text-sm text-slate-500">No service contracts yet.</p>
            </div>
            <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Contract</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide hidden sm:table-cell">Type</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Next Service</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide hidden md:table-cell">Status</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php foreach ($contracts as $contract): ?>
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-4 py-3 font-mono text-xs text-sky-600 font-medium">
                                <?= e($contract['contract_code'] ?? '—') ?>
                            </td>
                            <td class="px-4 py-3 text-slate-600 capitalize hidden sm:table-cell">
                                <?= e(str_replace('_', ' ', $contract['service_type'] ?? '—')) ?>
                            </td>
                            <td class="px-4 py-3 text-slate-700">
                                <?php if (!empty($contract['next_service_date'])): ?>
                                    <?php
                                    $nextDate = strtotime($contract['next_service_date']);
                                    $isOverdue = $nextDate < time();
                                    ?>
                                    <span class="<?= $isOverdue ? 'text-red-600 font-medium' : '' ?>">
                                        <?= e(date('d M Y', $nextDate)) ?>
                                        <?php if ($isOverdue): ?>
                                        <span class="ml-1 text-xs text-red-500">(Overdue)</span>
                                        <?php endif; ?>
                                    </span>
                                <?php else: ?>
                                    <span class="text-slate-400">—</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 hidden md:table-cell">
                                <?php
                                $cBadge = match ($contract['status'] ?? '') {
                                    'active'    => 'bg-green-100 text-green-700',
                                    'expired'   => 'bg-red-100 text-red-700',
                                    'suspended' => 'bg-yellow-100 text-yellow-700',
                                    default     => 'bg-slate-100 text-slate-600',
                                };
                                ?>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium capitalize <?= $cBadge ?>">
                                    <?= e($contract['status'] ?? 'unknown') ?>
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="/contracts/<?= e($contract['id']) ?>"
                                   class="text-xs text-sky-600 hover:text-sky-800 font-medium transition-colors">View</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>

        <!-- Recent Jobs -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
                <h2 class="text-base font-semibold text-slate-700">Recent Jobs</h2>
                <a href="/jobs/create?customer_id=<?= e($customer['id']) ?>"
                   class="inline-flex items-center gap-1 text-xs text-sky-600 hover:text-sky-800 font-medium transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Create Job
                </a>
            </div>
            <?php if (empty($recentJobs)): ?>
            <div class="flex flex-col items-center py-12 text-center px-4">
                <svg class="w-10 h-10 text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                </svg>
                <p class="text-sm text-slate-500">No service jobs yet.</p>
            </div>
            <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Job</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide hidden sm:table-cell">Type</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide hidden md:table-cell">Scheduled</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Status</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php foreach ($recentJobs as $job): ?>
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-4 py-3 font-mono text-xs text-sky-600 font-medium">
                                <?= e($job['job_code'] ?? '—') ?>
                            </td>
                            <td class="px-4 py-3 text-slate-600 capitalize hidden sm:table-cell">
                                <?= e(str_replace('_', ' ', $job['job_type'] ?? '—')) ?>
                            </td>
                            <td class="px-4 py-3 text-slate-600 hidden md:table-cell">
                                <?= !empty($job['scheduled_date']) ? e(date('d M Y', strtotime($job['scheduled_date']))) : '—' ?>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium capitalize <?= $jobStatusBadge($job['status'] ?? '') ?>">
                                    <?= e(str_replace('_', ' ', $job['status'] ?? 'unknown')) ?>
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="/jobs/<?= e($job['id']) ?>"
                                   class="text-xs text-sky-600 hover:text-sky-800 font-medium transition-colors">View</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Sidebar summary -->
    <div class="space-y-6">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <h2 class="text-base font-semibold text-slate-700 mb-4">Summary</h2>
            <div class="space-y-4">
                <div class="flex items-center justify-between text-sm">
                    <span class="text-slate-500">Active Contracts</span>
                    <span class="font-semibold text-slate-800">
                        <?= count(array_filter($contracts ?? [], fn($c) => ($c['status'] ?? '') === 'active')) ?>
                    </span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-slate-500">Total Jobs</span>
                    <span class="font-semibold text-slate-800"><?= count($recentJobs ?? []) ?></span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-slate-500">Status</span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize <?= $statusBadge ?>">
                        <?= e($customer['status'] ?? '') ?>
                    </span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-slate-500">Source</span>
                    <span class="text-slate-700"><?= e($sourceLabels[$customer['source'] ?? ''] ?? '—') ?></span>
                </div>
            </div>
        </div>

        <!-- Contact card -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <h2 class="text-base font-semibold text-slate-700 mb-4">Contact</h2>
            <div class="space-y-3 text-sm">
                <?php if (!empty($customer['phone'])): ?>
                <a href="tel:<?= e($customer['phone']) ?>"
                   class="flex items-center gap-3 text-slate-700 hover:text-sky-600 transition-colors group">
                    <div class="w-8 h-8 rounded-lg bg-sky-50 group-hover:bg-sky-100 flex items-center justify-center flex-shrink-0 transition-colors">
                        <svg class="w-4 h-4 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                    </div>
                    <?= e($customer['phone']) ?>
                </a>
                <?php endif; ?>
                <?php if (!empty($customer['email'])): ?>
                <a href="mailto:<?= e($customer['email']) ?>"
                   class="flex items-center gap-3 text-slate-700 hover:text-sky-600 transition-colors group">
                    <div class="w-8 h-8 rounded-lg bg-sky-50 group-hover:bg-sky-100 flex items-center justify-center flex-shrink-0 transition-colors">
                        <svg class="w-4 h-4 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <span class="truncate"><?= e($customer['email']) ?></span>
                </a>
                <?php endif; ?>
                <?php if (!empty($customer['city']) || !empty($customer['address'])): ?>
                <div class="flex items-start gap-3 text-slate-600">
                    <div class="w-8 h-8 rounded-lg bg-slate-50 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <span><?= e(implode(', ', array_filter([$customer['city'] ?? '', $customer['state'] ?? '']))) ?: '—' ?></span>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Hidden delete form -->
<form id="deleteForm" method="POST" action="/customers/<?= e($customer['id']) ?>/delete" class="hidden">
    <?= \App\Core\CSRF::field() ?>
</form>

<script>
function confirmDelete() {
    Swal.fire({
        title: 'Delete Customer?',
        text: 'This will permanently delete ' + <?= json_encode($fullName) ?> + ' and cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Yes, delete',
        cancelButtonText: 'Cancel',
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('deleteForm').submit();
        }
    });
}
</script>

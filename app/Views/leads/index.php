<?php
/**
 * Leads – Index
 * Variables: $leads (array), $branches (array), $statusFilter (string)
 */
declare(strict_types=1);

$pageTitle    = 'Leads';
$statusFilter = $statusFilter ?? '';

$statuses = [
    ''          => 'All',
    'new'       => 'New',
    'contacted' => 'Contacted',
    'qualified' => 'Qualified',
    'proposal'  => 'Proposal',
    'won'       => 'Won',
    'lost'      => 'Lost',
];

$statusBadge = function (string $s): string {
    return match ($s) {
        'new'       => 'bg-blue-100 text-blue-700 border border-blue-200',
        'contacted' => 'bg-sky-100 text-sky-700 border border-sky-200',
        'qualified' => 'bg-purple-100 text-purple-700 border border-purple-200',
        'proposal'  => 'bg-orange-100 text-orange-700 border border-orange-200',
        'won'       => 'bg-green-100 text-green-700 border border-green-200',
        'lost'      => 'bg-red-100 text-red-700 border border-red-200',
        default     => 'bg-slate-100 text-slate-600 border border-slate-200',
    };
};

$sourceLabels = [
    'walk_in'   => 'Walk-in',
    'referral'  => 'Referral',
    'online'    => 'Online',
    'marketing' => 'Marketing',
    'other'     => 'Other',
];
?>

<!-- Page header -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Leads</h1>
        <p class="text-sm text-slate-500 mt-0.5"><?= count($leads ?? []) ?> lead<?= count($leads ?? []) !== 1 ? 's' : '' ?> shown</p>
    </div>
    <a href="/leads/create"
       class="inline-flex items-center gap-2 bg-sky-500 hover:bg-sky-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors shadow-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Add Lead
    </a>
</div>

<!-- Status filter tabs + branch filter -->
<div class="bg-white rounded-xl shadow-sm border border-slate-200 mb-6 overflow-hidden">
    <!-- Status tabs -->
    <div class="flex items-center gap-1 px-4 pt-4 overflow-x-auto pb-0 border-b border-slate-100">
        <?php foreach ($statuses as $val => $label): ?>
        <a href="<?= e('/leads?' . http_build_query(array_filter(['status' => $val, 'branch_id' => $_GET['branch_id'] ?? '']))) ?>"
           class="flex-shrink-0 px-4 py-2.5 text-sm font-medium border-b-2 transition-colors -mb-px
                  <?= $statusFilter === $val
                       ? 'border-sky-500 text-sky-600'
                       : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300' ?>">
            <?= e($label) ?>
            <?php if ($val === $statusFilter && !empty($leads)): ?>
            <span class="ml-1.5 inline-flex items-center justify-center w-5 h-5 rounded-full bg-sky-500 text-white text-xs font-bold">
                <?= count($leads) ?>
            </span>
            <?php endif; ?>
        </a>
        <?php endforeach; ?>
    </div>

    <!-- Branch filter -->
    <div class="px-4 py-3 flex items-center gap-3">
        <form method="GET" action="/leads" class="flex items-center gap-2">
            <?php if ($statusFilter): ?>
            <input type="hidden" name="status" value="<?= e($statusFilter) ?>">
            <?php endif; ?>
            <label for="branch_filter" class="text-xs font-medium text-slate-500 whitespace-nowrap">Branch:</label>
            <select id="branch_filter" name="branch_id"
                    onchange="this.form.submit()"
                    class="border border-slate-300 rounded-lg px-3 py-1.5 text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-sky-500">
                <option value="">All Branches</option>
                <?php foreach ($branches as $b): ?>
                <option value="<?= e($b['id']) ?>" <?= (($_GET['branch_id'] ?? '') == $b['id']) ? 'selected' : '' ?>>
                    <?= e($b['name']) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </form>
    </div>
</div>

<!-- Table card -->
<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
    <?php if (empty($leads)): ?>
    <div class="flex flex-col items-center justify-center py-20 text-center px-4">
        <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mb-4">
            <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
        </div>
        <h3 class="text-base font-semibold text-slate-700 mb-1">
            <?= $statusFilter ? 'No ' . e($statuses[$statusFilter] ?? $statusFilter) . ' leads' : 'No leads found' ?>
        </h3>
        <p class="text-sm text-slate-500 mb-4">
            <?= $statusFilter ? 'Try a different status filter.' : 'Start capturing leads to grow your pipeline.' ?>
        </p>
        <?php if ($statusFilter): ?>
        <a href="/leads" class="text-sm text-sky-600 hover:underline">View all leads</a>
        <?php else: ?>
        <a href="/leads/create"
           class="inline-flex items-center gap-2 bg-sky-500 hover:bg-sky-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Add Lead
        </a>
        <?php endif; ?>
    </div>
    <?php else: ?>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200">
                    <th class="text-left px-4 py-3 font-semibold text-slate-600 whitespace-nowrap">Code</th>
                    <th class="text-left px-4 py-3 font-semibold text-slate-600">Name</th>
                    <th class="text-left px-4 py-3 font-semibold text-slate-600 hidden sm:table-cell">Phone</th>
                    <th class="text-left px-4 py-3 font-semibold text-slate-600 hidden md:table-cell">Source</th>
                    <th class="text-left px-4 py-3 font-semibold text-slate-600">Status</th>
                    <th class="text-left px-4 py-3 font-semibold text-slate-600 hidden lg:table-cell">Assigned To</th>
                    <th class="text-left px-4 py-3 font-semibold text-slate-600 hidden xl:table-cell">Branch</th>
                    <th class="text-left px-4 py-3 font-semibold text-slate-600 hidden xl:table-cell">Date</th>
                    <th class="text-right px-4 py-3 font-semibold text-slate-600">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php foreach ($leads as $lead): ?>
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-4 py-3 font-mono text-xs text-sky-600 font-medium whitespace-nowrap">
                        <?= e($lead['lead_code'] ?? '') ?>
                    </td>
                    <td class="px-4 py-3">
                        <div class="font-medium text-slate-800">
                            <?= e(trim(($lead['first_name'] ?? '') . ' ' . ($lead['last_name'] ?? ''))) ?>
                        </div>
                        <div class="text-xs text-slate-500 sm:hidden mt-0.5"><?= e($lead['phone'] ?? '') ?></div>
                    </td>
                    <td class="px-4 py-3 text-slate-600 hidden sm:table-cell whitespace-nowrap">
                        <?= e($lead['phone'] ?? '—') ?>
                    </td>
                    <td class="px-4 py-3 text-slate-600 hidden md:table-cell">
                        <?= e($sourceLabels[$lead['source'] ?? ''] ?? ucfirst($lead['source'] ?? '—')) ?>
                    </td>
                    <td class="px-4 py-3">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize <?= $statusBadge($lead['status'] ?? '') ?>">
                            <?= e($lead['status'] ?? 'unknown') ?>
                        </span>
                    </td>
                    <td class="px-4 py-3 text-slate-600 hidden lg:table-cell">
                        <?= e($lead['assigned_to_name'] ?? '—') ?>
                    </td>
                    <td class="px-4 py-3 text-slate-600 hidden xl:table-cell">
                        <?= e($lead['branch_name'] ?? '—') ?>
                    </td>
                    <td class="px-4 py-3 text-slate-500 hidden xl:table-cell whitespace-nowrap">
                        <?= !empty($lead['created_at']) ? e(date('d M Y', strtotime($lead['created_at']))) : '—' ?>
                    </td>
                    <td class="px-4 py-3 text-right whitespace-nowrap">
                        <a href="/leads/<?= e($lead['id']) ?>"
                           class="inline-flex items-center gap-1 text-xs text-sky-600 hover:text-sky-800 font-medium mr-2 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            View
                        </a>
                        <?php if (in_array($lead['status'] ?? '', ['qualified', 'proposal'])): ?>
                        <a href="/leads/<?= e($lead['id']) ?>/convert"
                           class="inline-flex items-center gap-1 text-xs text-green-600 hover:text-green-800 font-medium transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            Convert
                        </a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<?php
$today = date('Y-m-d');
$sevenDays = date('Y-m-d', strtotime('+7 days'));
$dueSoon = array_filter($contracts, fn($c) => !empty($c['next_service_date']) && $c['next_service_date'] >= $today && $c['next_service_date'] <= $sevenDays && ($c['status'] ?? '') === 'active');
?>
<?php ob_start(); ?>
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-slate-800">Service Contracts</h1>
        <a href="/contracts/create" class="inline-flex items-center gap-2 bg-sky-500 hover:bg-sky-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            New Contract
        </a>
    </div>

    <?php include VIEW_PATH . '/partials/flash.php'; ?>

    <!-- Upcoming Service Due Banner -->
    <?php if(!empty($dueSoon)): ?>
    <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 flex items-start gap-3">
        <svg class="w-5 h-5 text-amber-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
        <div>
            <p class="font-semibold text-amber-800">Upcoming Services Due</p>
            <p class="text-sm text-amber-700"><?= count($dueSoon) ?> contract(s) have service due within the next 7 days.</p>
            <div class="mt-2 flex flex-wrap gap-2">
                <?php foreach($dueSoon as $c): ?>
                    <a href="/contracts/<?= $c['id'] ?>" class="text-xs bg-amber-100 text-amber-800 px-2 py-1 rounded-full hover:bg-amber-200"><?= e($c['contract_code'] ?? $c['id']) ?> — <?= e($c['customer_name'] ?? '') ?> (<?= e($c['next_service_date']) ?>)</a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Table -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="text-left px-4 py-3 font-semibold text-slate-600">Contract Code</th>
                        <th class="text-left px-4 py-3 font-semibold text-slate-600">Customer</th>
                        <th class="text-left px-4 py-3 font-semibold text-slate-600">Product</th>
                        <th class="text-left px-4 py-3 font-semibold text-slate-600">Service Type</th>
                        <th class="text-left px-4 py-3 font-semibold text-slate-600">Installation</th>
                        <th class="text-left px-4 py-3 font-semibold text-slate-600">Next Service</th>
                        <th class="text-left px-4 py-3 font-semibold text-slate-600">Status</th>
                        <th class="text-left px-4 py-3 font-semibold text-slate-600">Branch</th>
                        <th class="text-left px-4 py-3 font-semibold text-slate-600">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if(empty($contracts)): ?>
                        <tr><td colspan="9" class="px-4 py-8 text-center text-slate-500">No contracts found.</td></tr>
                    <?php else: ?>
                        <?php foreach($contracts as $c): ?>
                            <?php
                            $nextDate = $c['next_service_date'] ?? '';
                            $isOverdue = $nextDate && $nextDate < $today && ($c['status'] ?? '') === 'active';
                            $isDueSoon = $nextDate && $nextDate >= $today && $nextDate <= $sevenDays && ($c['status'] ?? '') === 'active';
                            ?>
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-4 py-3 font-mono font-medium text-slate-800"><?= e($c['contract_code'] ?? '') ?></td>
                                <td class="px-4 py-3 text-slate-700"><?= e($c['customer_name'] ?? '') ?></td>
                                <td class="px-4 py-3 text-slate-600"><?= e($c['product_name'] ?? '') ?></td>
                                <td class="px-4 py-3 text-slate-600"><?= e($c['service_type_name'] ?? '') ?></td>
                                <td class="px-4 py-3 text-slate-600"><?= e($c['installation_date'] ?? '') ?></td>
                                <td class="px-4 py-3 <?= $isOverdue ? 'text-red-600 font-semibold' : ($isDueSoon ? 'text-amber-600 font-medium' : 'text-slate-600') ?>">
                                    <?= e($nextDate ?: '—') ?>
                                    <?php if($isOverdue): ?><span class="ml-1 text-xs">⚠ Overdue</span><?php endif; ?>
                                </td>
                                <td class="px-4 py-3">
                                    <?php $st = $c['status'] ?? 'active'; ?>
                                    <span class="px-2 py-1 rounded-full text-xs font-medium <?= $st === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-700' ?>">
                                        <?= ucfirst($st) ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-slate-600"><?= e($c['branch_name'] ?? '') ?></td>
                                <td class="px-4 py-3">
                                    <div class="flex gap-2">
                                        <a href="/contracts/<?= $c['id'] ?>" class="text-sky-600 hover:text-sky-800 font-medium">View</a>
                                        <a href="/contracts/<?= $c['id'] ?>/edit" class="text-slate-500 hover:text-slate-700">Edit</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if(($total ?? 0) > ($perPage ?? 20)): ?>
        <div class="px-4 py-3 border-t border-slate-200">
            <?php include VIEW_PATH . '/partials/pagination.php'; ?>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php $content = ob_get_clean(); include VIEW_PATH . '/layouts/app.php'; ?>

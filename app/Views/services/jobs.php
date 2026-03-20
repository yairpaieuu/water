<?php
$statusColors = [
    'pending'   => 'bg-yellow-100 text-yellow-800',
    'assigned'  => 'bg-blue-100 text-blue-800',
    'in_progress'=> 'bg-indigo-100 text-indigo-800',
    'completed' => 'bg-green-100 text-green-800',
    'cancelled' => 'bg-red-100 text-red-800',
];
$typeColors = [
    'installation' => 'bg-purple-100 text-purple-800',
    'maintenance'  => 'bg-sky-100 text-sky-800',
    'repair'       => 'bg-orange-100 text-orange-800',
    'survey'       => 'bg-teal-100 text-teal-800',
];
$priorityColors = [
    'urgent' => 'bg-red-100 text-red-800',
    'high'   => 'bg-orange-100 text-orange-800',
    'normal' => 'bg-blue-100 text-blue-800',
    'low'    => 'bg-gray-100 text-gray-700',
];

$pending   = 0; $inProgress = 0; $completedToday = 0; $overdue = 0;
$today = date('Y-m-d');
foreach ($jobs as $j) {
    if ($j['status'] === 'pending')      $pending++;
    if ($j['status'] === 'in_progress')  $inProgress++;
    if ($j['status'] === 'completed' && substr($j['updated_at'] ?? '', 0, 10) === $today) $completedToday++;
    if (!in_array($j['status'], ['completed','cancelled']) && !empty($j['scheduled_date']) && $j['scheduled_date'] < $today) $overdue++;
}
?>
<?php ob_start(); ?>
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-slate-800">Service Jobs</h1>
        <a href="/jobs/create" class="inline-flex items-center gap-2 bg-sky-500 hover:bg-sky-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            New Job
        </a>
    </div>

    <?php include VIEW_PATH . '/partials/flash.php'; ?>

    <!-- Stats -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-yellow-100 flex items-center justify-center">
                <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3"/><circle cx="12" cy="12" r="9" stroke-width="2"/></svg>
            </div>
            <div><p class="text-xs text-slate-500">Pending</p><p class="text-xl font-bold text-slate-800"><?= $pending ?></p></div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-indigo-100 flex items-center justify-center">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            </div>
            <div><p class="text-xs text-slate-500">In Progress</p><p class="text-xl font-bold text-slate-800"><?= $inProgress ?></p></div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-green-100 flex items-center justify-center">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            </div>
            <div><p class="text-xs text-slate-500">Completed Today</p><p class="text-xl font-bold text-slate-800"><?= $completedToday ?></p></div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-red-100 flex items-center justify-center">
                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
            </div>
            <div><p class="text-xs text-slate-500">Overdue</p><p class="text-xl font-bold text-red-600"><?= $overdue ?></p></div>
        </div>
    </div>

    <!-- Filters & View Toggle -->
    <div x-data="{ view: 'table' }" class="space-y-4">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4">
            <form method="GET" action="/jobs" class="flex flex-wrap gap-3 items-end">
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Status</label>
                    <select name="status" class="border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                        <option value="">All Status</option>
                        <?php foreach(['pending','assigned','in_progress','completed','cancelled'] as $s): ?>
                            <option value="<?= $s ?>" <?= $statusFilter === $s ? 'selected' : '' ?>><?= ucfirst(str_replace('_',' ',$s)) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Branch</label>
                    <select name="branch_id" class="border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                        <option value="">All Branches</option>
                        <?php foreach($branches as $b): ?>
                            <option value="<?= $b['id'] ?>" <?= $branchFilter == $b['id'] ? 'selected' : '' ?>><?= e($b['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Date</label>
                    <input type="date" name="date" value="<?= e($dateFilter ?? '') ?>" class="border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                </div>
                <button type="submit" class="inline-flex items-center gap-2 bg-sky-500 hover:bg-sky-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">Filter</button>
                <a href="/jobs" class="inline-flex items-center gap-2 bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 px-4 py-2 rounded-lg text-sm font-medium transition-colors">Reset</a>
                <div class="ml-auto flex items-center gap-2">
                    <button type="button" @click="view='table'" :class="view==='table' ? 'bg-sky-500 text-white' : 'bg-white border border-slate-300 text-slate-700'" class="px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 6h18M3 14h18M3 18h18"/></svg>
                    </button>
                    <button type="button" @click="view='kanban'" :class="view==='kanban' ? 'bg-sky-500 text-white' : 'bg-white border border-slate-300 text-slate-700'" class="px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"/></svg>
                    </button>
                </div>
            </form>
        </div>

        <!-- Table View -->
        <div x-show="view==='table'" class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="text-left px-4 py-3 font-semibold text-slate-600">Job Code</th>
                            <th class="text-left px-4 py-3 font-semibold text-slate-600">Customer</th>
                            <th class="text-left px-4 py-3 font-semibold text-slate-600">Type</th>
                            <th class="text-left px-4 py-3 font-semibold text-slate-600">Status</th>
                            <th class="text-left px-4 py-3 font-semibold text-slate-600">Priority</th>
                            <th class="text-left px-4 py-3 font-semibold text-slate-600">Scheduled</th>
                            <th class="text-left px-4 py-3 font-semibold text-slate-600">Technician</th>
                            <th class="text-left px-4 py-3 font-semibold text-slate-600">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php if(empty($jobs)): ?>
                            <tr><td colspan="8" class="px-4 py-8 text-center text-slate-500">No jobs found.</td></tr>
                        <?php else: ?>
                            <?php foreach($jobs as $job): ?>
                                <?php $isOverdue = !in_array($job['status'],['completed','cancelled']) && !empty($job['scheduled_date']) && $job['scheduled_date'] < $today; ?>
                                <tr class="<?= $isOverdue ? 'bg-red-50' : 'hover:bg-slate-50' ?> transition-colors">
                                    <td class="px-4 py-3 font-mono font-medium text-slate-800"><?= e($job['job_code'] ?? '') ?></td>
                                    <td class="px-4 py-3 text-slate-700"><?= e($job['customer_name'] ?? '') ?></td>
                                    <td class="px-4 py-3">
                                        <?php $type = $job['job_type'] ?? 'maintenance'; ?>
                                        <span class="px-2 py-1 rounded-full text-xs font-medium <?= $typeColors[$type] ?? 'bg-gray-100 text-gray-700' ?>"><?= ucfirst($type) ?></span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <?php $status = $job['status'] ?? 'pending'; ?>
                                        <span class="px-2 py-1 rounded-full text-xs font-medium <?= $statusColors[$status] ?? '' ?>"><?= ucfirst(str_replace('_',' ',$status)) ?></span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <?php $priority = $job['priority'] ?? 'normal'; ?>
                                        <span class="px-2 py-1 rounded-full text-xs font-medium <?= $priorityColors[$priority] ?? '' ?>"><?= ucfirst($priority) ?></span>
                                    </td>
                                    <td class="px-4 py-3 text-slate-600 <?= $isOverdue ? 'text-red-600 font-semibold' : '' ?>">
                                        <?= e($job['scheduled_date'] ?? '') ?> <?= !empty($job['scheduled_time']) ? e($job['scheduled_time']) : '' ?>
                                    </td>
                                    <td class="px-4 py-3 text-slate-600"><?= e($job['technician_name'] ?? '—') ?></td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-2">
                                            <a href="/jobs/<?= $job['id'] ?>" class="text-sky-600 hover:text-sky-800 font-medium">View</a>
                                            <a href="/jobs/<?= $job['id'] ?>/edit" class="text-slate-500 hover:text-slate-700">Edit</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Kanban View -->
        <div x-show="view==='kanban'" x-cloak>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <?php foreach(['pending'=>'Pending','assigned'=>'Assigned','in_progress'=>'In Progress','completed'=>'Completed'] as $st => $label): ?>
                    <div class="bg-slate-50 rounded-xl border border-slate-200 p-3">
                        <h3 class="font-semibold text-slate-700 mb-3 flex items-center justify-between">
                            <?= $label ?>
                            <span class="text-xs font-normal bg-white border border-slate-200 rounded-full px-2 py-0.5">
                                <?= count(array_filter($jobs, fn($j) => $j['status'] === $st)) ?>
                            </span>
                        </h3>
                        <div class="space-y-2">
                            <?php foreach(array_filter($jobs, fn($j) => $j['status'] === $st) as $job): ?>
                                <a href="/jobs/<?= $job['id'] ?>" class="block bg-white rounded-lg border border-slate-200 p-3 hover:shadow-md transition-shadow">
                                    <p class="font-mono text-xs text-slate-500 mb-1"><?= e($job['job_code'] ?? '') ?></p>
                                    <p class="font-medium text-slate-800 text-sm"><?= e($job['customer_name'] ?? '') ?></p>
                                    <div class="flex items-center gap-1 mt-2">
                                        <?php $type = $job['job_type'] ?? 'maintenance'; $priority = $job['priority'] ?? 'normal'; ?>
                                        <span class="px-1.5 py-0.5 rounded text-xs <?= $typeColors[$type] ?? '' ?>"><?= ucfirst($type) ?></span>
                                        <span class="px-1.5 py-0.5 rounded text-xs <?= $priorityColors[$priority] ?? '' ?>"><?= ucfirst($priority) ?></span>
                                    </div>
                                    <?php if(!empty($job['scheduled_date'])): ?>
                                        <p class="text-xs text-slate-500 mt-1"><?= e($job['scheduled_date']) ?></p>
                                    <?php endif; ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
<?php $content = ob_get_clean(); include VIEW_PATH . '/layouts/app.php'; ?>

<?php
$statusColors = ['pending'=>'bg-yellow-100 text-yellow-800','assigned'=>'bg-blue-100 text-blue-800','in_progress'=>'bg-indigo-100 text-indigo-800','completed'=>'bg-green-100 text-green-800','cancelled'=>'bg-red-100 text-red-800'];
$typeColors   = ['installation'=>'bg-purple-100 text-purple-800','maintenance'=>'bg-sky-100 text-sky-800','repair'=>'bg-orange-100 text-orange-800','survey'=>'bg-teal-100 text-teal-800'];
$status = $job['status'] ?? 'pending';
$type   = $job['job_type'] ?? 'maintenance';
?>
<?php ob_start(); ?>
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-wrap items-center gap-3">
        <a href="/jobs" class="text-slate-500 hover:text-slate-700">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <h1 class="text-2xl font-bold text-slate-800"><?= e($job['job_code'] ?? 'Job') ?></h1>
        <span class="px-2 py-1 rounded-full text-xs font-medium <?= $typeColors[$type] ?? 'bg-gray-100 text-gray-700' ?>"><?= ucfirst($type) ?></span>
        <span class="px-2 py-1 rounded-full text-xs font-medium <?= $statusColors[$status] ?? '' ?>"><?= ucfirst(str_replace('_',' ',$status)) ?></span>
        <div class="ml-auto flex gap-2">
            <a href="/jobs/<?= $job['id'] ?>/edit" class="inline-flex items-center gap-2 bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 px-4 py-2 rounded-lg text-sm font-medium transition-colors">Edit</a>
        </div>
    </div>

    <?php include VIEW_PATH . '/partials/flash.php'; ?>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Job Details -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 space-y-4">
            <h2 class="font-semibold text-slate-800 text-lg border-b border-slate-100 pb-2">Job Details</h2>
            <dl class="space-y-3 text-sm">
                <div class="flex justify-between"><dt class="text-slate-500">Job Code</dt><dd class="font-mono font-medium text-slate-800"><?= e($job['job_code'] ?? '') ?></dd></div>
                <div class="flex justify-between"><dt class="text-slate-500">Priority</dt><dd><?= ucfirst($job['priority'] ?? 'normal') ?></dd></div>
                <div class="flex justify-between"><dt class="text-slate-500">Scheduled Date</dt><dd><?= e($job['scheduled_date'] ?? '') ?></dd></div>
                <div class="flex justify-between"><dt class="text-slate-500">Scheduled Time</dt><dd><?= e($job['scheduled_time'] ?? '—') ?></dd></div>
                <div class="flex justify-between"><dt class="text-slate-500">Assigned Tech</dt><dd><?= e(($technician['first_name'] ?? '') . ' ' . ($technician['last_name'] ?? '')) ?: '—' ?></dd></div>
                <?php if($contract): ?>
                <div class="flex justify-between"><dt class="text-slate-500">Contract</dt><dd><a href="/contracts/<?= $contract['id'] ?>" class="text-sky-600 hover:underline"><?= e($contract['contract_code'] ?? $contract['id']) ?></a></dd></div>
                <?php endif; ?>
                <?php if(!empty($job['notes'])): ?>
                <div><dt class="text-slate-500 mb-1">Notes</dt><dd class="text-slate-700 bg-slate-50 rounded-lg p-2"><?= nl2br(e($job['notes'])) ?></dd></div>
                <?php endif; ?>
            </dl>
        </div>

        <!-- Customer Details -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 space-y-4">
            <h2 class="font-semibold text-slate-800 text-lg border-b border-slate-100 pb-2">Customer Details</h2>
            <?php if($customer): ?>
            <dl class="space-y-3 text-sm">
                <div class="flex justify-between"><dt class="text-slate-500">Name</dt><dd class="font-medium text-slate-800"><a href="/customers/<?= $customer['id'] ?>" class="text-sky-600 hover:underline"><?= e($customer['name']) ?></a></dd></div>
                <?php if(!empty($customer['phone'])): ?><div class="flex justify-between"><dt class="text-slate-500">Phone</dt><dd><?= e($customer['phone']) ?></dd></div><?php endif; ?>
                <?php if(!empty($customer['email'])): ?><div class="flex justify-between"><dt class="text-slate-500">Email</dt><dd><?= e($customer['email']) ?></dd></div><?php endif; ?>
                <?php if(!empty($customer['address'])): ?><div class="flex justify-between"><dt class="text-slate-500">Address</dt><dd class="text-right max-w-xs"><?= e($customer['address']) ?></dd></div><?php endif; ?>
            </dl>
            <?php else: ?>
                <p class="text-slate-500 text-sm">No customer linked.</p>
            <?php endif; ?>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Update Status -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <h2 class="font-semibold text-slate-800 text-lg border-b border-slate-100 pb-2 mb-4">Update Status</h2>
            <form method="POST" action="/jobs/<?= $job['id'] ?>/status" class="space-y-3">
                <?= \App\Core\CSRF::field() ?>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">New Status</label>
                    <select name="status" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500">
                        <?php foreach(['pending'=>'Pending','assigned'=>'Assigned','in_progress'=>'In Progress','completed'=>'Completed','cancelled'=>'Cancelled'] as $v => $l): ?>
                            <option value="<?= $v ?>" <?= $status === $v ? 'selected' : '' ?>><?= $l ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Status Notes</label>
                    <textarea name="status_notes" rows="2" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500" placeholder="Reason for update..."></textarea>
                </div>
                <button type="submit" class="inline-flex items-center gap-2 bg-sky-500 hover:bg-sky-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">Update Status</button>
            </form>
        </div>

        <!-- Complete Job -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <h2 class="font-semibold text-slate-800 text-lg border-b border-slate-100 pb-2 mb-4">Complete Job</h2>
            <form method="POST" action="/jobs/<?= $job['id'] ?>/complete" class="space-y-3">
                <?= \App\Core\CSRF::field() ?>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Completion Notes</label>
                    <textarea name="completion_notes" rows="2" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500" placeholder="What was done..."><?= e($job['completion_notes'] ?? '') ?></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Service Cost (RM)</label>
                    <input type="number" name="cost" step="0.01" min="0" value="<?= e($job['cost'] ?? '') ?>" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500" placeholder="0.00">
                </div>
                <button type="submit" class="inline-flex items-center gap-2 bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Mark Complete
                </button>
            </form>
        </div>
    </div>

    <!-- Parts Used -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <h2 class="font-semibold text-slate-800 text-lg border-b border-slate-100 pb-2 mb-4">Parts Used</h2>
        <?php if(!empty($job['parts_used'])): ?>
            <p class="text-sm text-slate-700 whitespace-pre-wrap"><?= e($job['parts_used']) ?></p>
        <?php else: ?>
            <p class="text-sm text-slate-500">No parts recorded.</p>
        <?php endif; ?>
    </div>

    <!-- Timeline Placeholder -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <h2 class="font-semibold text-slate-800 text-lg border-b border-slate-100 pb-2 mb-4">Timeline</h2>
        <div class="flex items-center gap-3 text-sm text-slate-500">
            <div class="w-2 h-2 rounded-full bg-sky-400"></div>
            <span>Job created on <?= e($job['created_at'] ?? '') ?></span>
        </div>
        <?php if(!empty($job['updated_at']) && $job['updated_at'] !== $job['created_at']): ?>
        <div class="flex items-center gap-3 text-sm text-slate-500 mt-2">
            <div class="w-2 h-2 rounded-full bg-green-400"></div>
            <span>Last updated <?= e($job['updated_at']) ?></span>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php $content = ob_get_clean(); include VIEW_PATH . '/layouts/app.php'; ?>

<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center gap-3">
        <a href="/jobs" class="text-slate-500 hover:text-slate-700">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <h1 class="text-2xl font-bold text-slate-800">Create Service Job</h1>
    </div>

    <?php include VIEW_PATH . '/partials/flash.php'; ?>

    <?php if(!empty($errors)): ?>
        <div class="bg-red-50 border border-red-200 rounded-xl p-4">
            <ul class="list-disc list-inside space-y-1 text-sm text-red-700">
                <?php foreach($errors as $e): ?><li><?= e($e) ?></li><?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" action="/jobs/store" x-data="{ selectedCustomer: '' }" class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 space-y-5">
        <?= \App\Core\CSRF::field() ?>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <!-- Customer -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-slate-700 mb-1">Customer <span class="text-red-500">*</span></label>
                <select name="customer_id" x-model="selectedCustomer" required class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                    <option value="">— Select Customer —</option>
                    <?php foreach($customers as $c): ?>
                        <option value="<?= $c['id'] ?>"><?= e($c['name']) ?> <?= !empty($c['phone']) ? '('.e($c['phone']).')' : '' ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Contract -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Contract (optional)</label>
                <select name="contract_id" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                    <option value="">— No Contract —</option>
                    <?php foreach($contracts as $c): ?>
                        <option value="<?= $c['id'] ?>"><?= e($c['contract_code'] ?? $c['id']) ?> - <?= e($c['customer_name'] ?? '') ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Job Type -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Job Type <span class="text-red-500">*</span></label>
                <select name="job_type" required class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                    <?php foreach(['installation'=>'Installation','maintenance'=>'Maintenance','repair'=>'Repair','survey'=>'Survey'] as $v => $l): ?>
                        <option value="<?= $v ?>"><?= $l ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Status -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Status</label>
                <select name="status" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                    <?php foreach(['pending'=>'Pending','assigned'=>'Assigned','in_progress'=>'In Progress'] as $v => $l): ?>
                        <option value="<?= $v ?>"><?= $l ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Priority -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Priority</label>
                <select name="priority" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                    <option value="low">Low</option>
                    <option value="normal" selected>Normal</option>
                    <option value="high">High</option>
                    <option value="urgent">Urgent</option>
                </select>
            </div>

            <!-- Scheduled Date -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Scheduled Date <span class="text-red-500">*</span></label>
                <input type="date" name="scheduled_date" required value="<?= e($_POST['scheduled_date'] ?? '') ?>" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
            </div>

            <!-- Scheduled Time -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Scheduled Time</label>
                <input type="time" name="scheduled_time" value="<?= e($_POST['scheduled_time'] ?? '') ?>" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
            </div>

            <!-- Assign To -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Assign To</label>
                <select name="assigned_to" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                    <option value="">— Unassigned —</option>
                    <?php foreach($employees as $emp): ?>
                        <option value="<?= $emp['id'] ?>"><?= e($emp['first_name'] . ' ' . $emp['last_name']) ?> <?= !empty($emp['position']) ? '('.e($emp['position']).')' : '' ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Branch -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Branch</label>
                <select name="branch_id" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                    <option value="">— Select Branch —</option>
                    <?php foreach(($branches ?? []) as $b): ?>
                        <option value="<?= $b['id'] ?>"><?= e($b['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Notes -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-slate-700 mb-1">Notes</label>
                <textarea name="notes" rows="3" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500"><?= e($_POST['notes'] ?? '') ?></textarea>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 pt-2 border-t border-slate-100">
            <a href="/jobs" class="inline-flex items-center gap-2 bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 px-4 py-2 rounded-lg text-sm font-medium transition-colors">Cancel</a>
            <button type="submit" class="inline-flex items-center gap-2 bg-sky-500 hover:bg-sky-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Create Job
            </button>
        </div>
    </form>
</div>

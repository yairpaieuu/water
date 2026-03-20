<?php
/**
 * Leads – Show
 * Variables: $lead (array), $branch (array), $assignedUser (array)
 */
declare(strict_types=1);

$fullName  = trim(($lead['first_name'] ?? '') . ' ' . ($lead['last_name'] ?? ''));
$pageTitle = $fullName ?: 'Lead Details';

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

$currentStatus = $lead['status'] ?? 'new';
$canConvert    = in_array($currentStatus, ['qualified', 'proposal']);
$isClosed      = in_array($currentStatus, ['won', 'lost']);

$statusFlow = ['new', 'contacted', 'qualified', 'proposal', 'won', 'lost'];
?>

<!-- Page header -->
<div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 mb-6">
    <div class="flex items-start gap-3">
        <a href="/leads"
           class="mt-1 flex items-center justify-center w-9 h-9 rounded-lg border border-slate-200 bg-white text-slate-500 hover:text-slate-700 hover:bg-slate-50 transition-colors shadow-sm flex-shrink-0"
           title="Back to leads">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <div class="flex flex-wrap items-center gap-2">
                <h1 class="text-2xl font-bold text-slate-800"><?= e($fullName) ?></h1>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize <?= $statusBadge($currentStatus) ?>">
                    <?= e($currentStatus) ?>
                </span>
            </div>
            <p class="text-sm text-slate-500 mt-0.5 font-mono"><?= e($lead['lead_code'] ?? '') ?></p>
        </div>
    </div>

    <div class="flex items-center gap-2 flex-wrap sm:flex-nowrap">
        <?php if ($canConvert): ?>
        <a href="/leads/<?= e($lead['id']) ?>/convert"
           class="inline-flex items-center gap-2 bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            Convert to Customer
        </a>
        <?php endif; ?>
        <a href="/leads/<?= e($lead['id']) ?>/edit"
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

<!-- Pipeline progress indicator -->
<?php if (!$isClosed): ?>
<div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 mb-6">
    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-4">Pipeline Progress</p>
    <div class="flex items-center gap-1 sm:gap-2">
        <?php
        $pipelineSteps = ['new', 'contacted', 'qualified', 'proposal'];
        $currentIdx    = array_search($currentStatus, $pipelineSteps);
        ?>
        <?php foreach ($pipelineSteps as $i => $step): ?>
        <?php
        $isDone    = $currentIdx !== false && $i <= $currentIdx;
        $isCurrent = $i === $currentIdx;
        ?>
        <?php if ($i > 0): ?>
        <div class="flex-1 h-1 rounded-full <?= ($currentIdx !== false && $i <= $currentIdx) ? 'bg-sky-400' : 'bg-slate-200' ?>"></div>
        <?php endif; ?>
        <div class="flex flex-col items-center gap-1 flex-shrink-0">
            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold border-2
                        <?= $isCurrent ? 'bg-sky-500 border-sky-500 text-white shadow-md' : ($isDone ? 'bg-sky-100 border-sky-300 text-sky-600' : 'bg-white border-slate-300 text-slate-400') ?>">
                <?= $isDone && !$isCurrent ? '✓' : ($i + 1) ?>
            </div>
            <span class="hidden sm:block text-xs <?= $isCurrent ? 'text-sky-600 font-semibold' : 'text-slate-400' ?> capitalize whitespace-nowrap">
                <?= e($step) ?>
            </span>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

    <!-- Main details -->
    <div class="xl:col-span-2 space-y-6">
        <!-- Lead details card -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <h2 class="text-base font-semibold text-slate-700 mb-5 pb-4 border-b border-slate-100">Lead Details</h2>
            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-5 text-sm">
                <div>
                    <dt class="text-slate-500 font-medium mb-0.5">Lead Code</dt>
                    <dd class="text-slate-800 font-mono"><?= e($lead['lead_code'] ?? '—') ?></dd>
                </div>
                <div>
                    <dt class="text-slate-500 font-medium mb-0.5">Full Name</dt>
                    <dd class="text-slate-800 font-medium"><?= e($fullName) ?: '—' ?></dd>
                </div>
                <div>
                    <dt class="text-slate-500 font-medium mb-0.5">Phone</dt>
                    <dd>
                        <?php if (!empty($lead['phone'])): ?>
                        <a href="tel:<?= e($lead['phone']) ?>" class="text-sky-600 hover:underline"><?= e($lead['phone']) ?></a>
                        <?php else: ?><span class="text-slate-400">—</span><?php endif; ?>
                    </dd>
                </div>
                <div>
                    <dt class="text-slate-500 font-medium mb-0.5">Email</dt>
                    <dd>
                        <?php if (!empty($lead['email'])): ?>
                        <a href="mailto:<?= e($lead['email']) ?>" class="text-sky-600 hover:underline"><?= e($lead['email']) ?></a>
                        <?php else: ?><span class="text-slate-400">—</span><?php endif; ?>
                    </dd>
                </div>
                <div>
                    <dt class="text-slate-500 font-medium mb-0.5">Source</dt>
                    <dd class="text-slate-800"><?= e($sourceLabels[$lead['source'] ?? ''] ?? ucfirst(str_replace('_', ' ', $lead['source'] ?? '—'))) ?></dd>
                </div>
                <div>
                    <dt class="text-slate-500 font-medium mb-0.5">Branch</dt>
                    <dd class="text-slate-800"><?= e($branch['name'] ?? $lead['branch_name'] ?? '—') ?></dd>
                </div>
                <div>
                    <dt class="text-slate-500 font-medium mb-0.5">Assigned To</dt>
                    <dd class="text-slate-800"><?= e($assignedUser['name'] ?? $lead['assigned_to_name'] ?? 'Unassigned') ?></dd>
                </div>
                <div>
                    <dt class="text-slate-500 font-medium mb-0.5">Created</dt>
                    <dd class="text-slate-800">
                        <?= !empty($lead['created_at']) ? e(date('d M Y, H:i', strtotime($lead['created_at']))) : '—' ?>
                    </dd>
                </div>
                <?php if (!empty($lead['address']) || !empty($lead['city'])): ?>
                <div class="sm:col-span-2">
                    <dt class="text-slate-500 font-medium mb-0.5">Address</dt>
                    <dd class="text-slate-800">
                        <?= e(implode(', ', array_filter([$lead['address'] ?? '', $lead['city'] ?? '']))) ?: '—' ?>
                    </dd>
                </div>
                <?php endif; ?>
                <?php if (!empty($lead['notes'])): ?>
                <div class="sm:col-span-2">
                    <dt class="text-slate-500 font-medium mb-0.5">Notes</dt>
                    <dd class="text-slate-800 whitespace-pre-wrap"><?= e($lead['notes']) ?></dd>
                </div>
                <?php endif; ?>
            </dl>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="space-y-6">
        <!-- Quick status update -->
        <?php if (!$isClosed): ?>
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <h2 class="text-base font-semibold text-slate-700 mb-4">Update Status</h2>
            <form method="POST" action="/leads/<?= e($lead['id']) ?>/update" class="space-y-3">
                <?= \App\Core\CSRF::field() ?>
                <div>
                    <label for="quick_status" class="block text-xs font-medium text-slate-500 mb-1.5">New Status</label>
                    <select id="quick_status"
                            name="status"
                            class="border border-slate-300 rounded-lg px-3 py-2 w-full text-sm focus:outline-none focus:ring-2 focus:ring-sky-500">
                        <?php foreach ($statusFlow as $s):
                            if (in_array($s, ['won', 'lost'])) continue;
                        ?>
                        <option value="<?= e($s) ?>" <?= $currentStatus === $s ? 'selected' : '' ?>>
                            <?= e(ucfirst($s)) ?>
                        </option>
                        <?php endforeach; ?>
                        <option value="lost" <?= $currentStatus === 'lost' ? 'selected' : '' ?>>Lost</option>
                    </select>
                </div>
                <button type="submit"
                        class="w-full inline-flex items-center justify-center gap-2 bg-sky-500 hover:bg-sky-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Update Status
                </button>
            </form>
        </div>
        <?php endif; ?>

        <!-- Summary card -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <h2 class="text-base font-semibold text-slate-700 mb-4">Summary</h2>
            <div class="space-y-3 text-sm">
                <div class="flex items-center justify-between">
                    <span class="text-slate-500">Status</span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize <?= $statusBadge($currentStatus) ?>">
                        <?= e($currentStatus) ?>
                    </span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-slate-500">Source</span>
                    <span class="text-slate-700"><?= e($sourceLabels[$lead['source'] ?? ''] ?? '—') ?></span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-slate-500">Assigned</span>
                    <span class="text-slate-700"><?= e($assignedUser['name'] ?? 'Unassigned') ?></span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-slate-500">Branch</span>
                    <span class="text-slate-700"><?= e($branch['name'] ?? '—') ?></span>
                </div>
                <?php if (!empty($lead['updated_at'])): ?>
                <div class="flex items-center justify-between">
                    <span class="text-slate-500">Last Updated</span>
                    <span class="text-slate-700"><?= e(date('d M Y', strtotime($lead['updated_at']))) ?></span>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Contact card -->
        <?php if (!empty($lead['phone']) || !empty($lead['email'])): ?>
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <h2 class="text-base font-semibold text-slate-700 mb-4">Contact</h2>
            <div class="space-y-3">
                <?php if (!empty($lead['phone'])): ?>
                <a href="tel:<?= e($lead['phone']) ?>"
                   class="flex items-center gap-3 text-sm text-slate-700 hover:text-sky-600 transition-colors group">
                    <div class="w-8 h-8 rounded-lg bg-sky-50 group-hover:bg-sky-100 flex items-center justify-center flex-shrink-0 transition-colors">
                        <svg class="w-4 h-4 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                    </div>
                    <?= e($lead['phone']) ?>
                </a>
                <?php endif; ?>
                <?php if (!empty($lead['email'])): ?>
                <a href="mailto:<?= e($lead['email']) ?>"
                   class="flex items-center gap-3 text-sm text-slate-700 hover:text-sky-600 transition-colors group">
                    <div class="w-8 h-8 rounded-lg bg-sky-50 group-hover:bg-sky-100 flex items-center justify-center flex-shrink-0 transition-colors">
                        <svg class="w-4 h-4 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <span class="truncate"><?= e($lead['email']) ?></span>
                </a>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Hidden delete form -->
<form id="deleteForm" method="POST" action="/leads/<?= e($lead['id']) ?>/delete" class="hidden">
    <?= \App\Core\CSRF::field() ?>
</form>

<script>
function confirmDelete() {
    Swal.fire({
        title: 'Delete Lead?',
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

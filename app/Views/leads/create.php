<?php
/**
 * Leads – Create
 * Variables: $branches (array), $users (array), $errors (array), $old (array)
 */
declare(strict_types=1);

$pageTitle = 'Add New Lead';
$errors    = $errors ?? [];
$old       = $old    ?? [];

$v   = fn(string $k): string => e($old[$k] ?? '');
$err = fn(string $k): string => $errors[$k] ?? '';
?>

<!-- Page header -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div class="flex items-center gap-3">
        <a href="/leads"
           class="flex items-center justify-center w-9 h-9 rounded-lg border border-slate-200 bg-white text-slate-500 hover:text-slate-700 hover:bg-slate-50 transition-colors shadow-sm"
           title="Back to leads">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Add New Lead</h1>
            <p class="text-sm text-slate-500 mt-0.5">Capture a new prospect in the pipeline.</p>
        </div>
    </div>
</div>

<?php if (!empty($errors)): ?>
<div class="flex items-start gap-3 p-4 mb-6 bg-red-50 border border-red-200 rounded-xl text-red-800 text-sm">
    <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
    <div>
        <p class="font-medium mb-1">Please fix the following errors:</p>
        <ul class="list-disc list-inside space-y-0.5">
            <?php foreach ($errors as $errMsg): ?>
            <li><?= e($errMsg) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>
<?php endif; ?>

<form method="POST" action="/leads/store" novalidate class="space-y-6">
    <?= \App\Core\CSRF::field() ?>

    <!-- Personal Information -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <h2 class="text-base font-semibold text-slate-700 mb-5 flex items-center gap-2">
            <svg class="w-4 h-4 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            Personal Information
        </h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div>
                <label for="first_name" class="block text-sm font-medium text-slate-700 mb-1">
                    First Name <span class="text-red-500" aria-hidden="true">*</span>
                </label>
                <input type="text"
                       id="first_name"
                       name="first_name"
                       value="<?= $v('first_name') ?>"
                       required
                       maxlength="100"
                       autocomplete="given-name"
                       class="border <?= $err('first_name') ? 'border-red-400 bg-red-50' : 'border-slate-300' ?> rounded-lg px-3 py-2 w-full text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent transition-colors">
                <?php if ($err('first_name')): ?>
                <p class="mt-1 text-xs text-red-600" role="alert"><?= e($err('first_name')) ?></p>
                <?php endif; ?>
            </div>

            <div>
                <label for="last_name" class="block text-sm font-medium text-slate-700 mb-1">Last Name</label>
                <input type="text"
                       id="last_name"
                       name="last_name"
                       value="<?= $v('last_name') ?>"
                       maxlength="100"
                       autocomplete="family-name"
                       class="border <?= $err('last_name') ? 'border-red-400 bg-red-50' : 'border-slate-300' ?> rounded-lg px-3 py-2 w-full text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent transition-colors">
                <?php if ($err('last_name')): ?>
                <p class="mt-1 text-xs text-red-600" role="alert"><?= e($err('last_name')) ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Contact Information -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <h2 class="text-base font-semibold text-slate-700 mb-5 flex items-center gap-2">
            <svg class="w-4 h-4 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
            </svg>
            Contact Information
        </h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div>
                <label for="phone" class="block text-sm font-medium text-slate-700 mb-1">
                    Phone <span class="text-red-500" aria-hidden="true">*</span>
                </label>
                <input type="tel"
                       id="phone"
                       name="phone"
                       value="<?= $v('phone') ?>"
                       required
                       maxlength="30"
                       autocomplete="tel"
                       class="border <?= $err('phone') ? 'border-red-400 bg-red-50' : 'border-slate-300' ?> rounded-lg px-3 py-2 w-full text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent transition-colors">
                <?php if ($err('phone')): ?>
                <p class="mt-1 text-xs text-red-600" role="alert"><?= e($err('phone')) ?></p>
                <?php endif; ?>
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-slate-700 mb-1">Email Address</label>
                <input type="email"
                       id="email"
                       name="email"
                       value="<?= $v('email') ?>"
                       maxlength="180"
                       autocomplete="email"
                       class="border <?= $err('email') ? 'border-red-400 bg-red-50' : 'border-slate-300' ?> rounded-lg px-3 py-2 w-full text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent transition-colors">
                <?php if ($err('email')): ?>
                <p class="mt-1 text-xs text-red-600" role="alert"><?= e($err('email')) ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Address -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <h2 class="text-base font-semibold text-slate-700 mb-5 flex items-center gap-2">
            <svg class="w-4 h-4 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            Address
        </h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div class="sm:col-span-2">
                <label for="address" class="block text-sm font-medium text-slate-700 mb-1">Street Address</label>
                <input type="text"
                       id="address"
                       name="address"
                       value="<?= $v('address') ?>"
                       autocomplete="street-address"
                       class="border <?= $err('address') ? 'border-red-400 bg-red-50' : 'border-slate-300' ?> rounded-lg px-3 py-2 w-full text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent transition-colors">
                <?php if ($err('address')): ?>
                <p class="mt-1 text-xs text-red-600" role="alert"><?= e($err('address')) ?></p>
                <?php endif; ?>
            </div>

            <div>
                <label for="city" class="block text-sm font-medium text-slate-700 mb-1">City</label>
                <input type="text"
                       id="city"
                       name="city"
                       value="<?= $v('city') ?>"
                       maxlength="100"
                       autocomplete="address-level2"
                       class="border <?= $err('city') ? 'border-red-400 bg-red-50' : 'border-slate-300' ?> rounded-lg px-3 py-2 w-full text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent transition-colors">
                <?php if ($err('city')): ?>
                <p class="mt-1 text-xs text-red-600" role="alert"><?= e($err('city')) ?></p>
                <?php endif; ?>
            </div>

            <div>
                <label for="branch_id" class="block text-sm font-medium text-slate-700 mb-1">Branch</label>
                <select id="branch_id"
                        name="branch_id"
                        class="border <?= $err('branch_id') ? 'border-red-400 bg-red-50' : 'border-slate-300' ?> rounded-lg px-3 py-2 w-full text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent transition-colors">
                    <option value="">— Select Branch —</option>
                    <?php foreach ($branches as $b): ?>
                    <option value="<?= e($b['id']) ?>" <?= ($old['branch_id'] ?? '') == $b['id'] ? 'selected' : '' ?>>
                        <?= e($b['name']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
                <?php if ($err('branch_id')): ?>
                <p class="mt-1 text-xs text-red-600" role="alert"><?= e($err('branch_id')) ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Lead Classification -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <h2 class="text-base font-semibold text-slate-700 mb-5 flex items-center gap-2">
            <svg class="w-4 h-4 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
            Lead Classification
        </h2>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
            <!-- Source -->
            <div>
                <label for="source" class="block text-sm font-medium text-slate-700 mb-1">Source</label>
                <select id="source"
                        name="source"
                        class="border <?= $err('source') ? 'border-red-400 bg-red-50' : 'border-slate-300' ?> rounded-lg px-3 py-2 w-full text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent transition-colors">
                    <option value="">— Select Source —</option>
                    <?php
                    $sources = [
                        'walk_in'   => 'Walk-in',
                        'referral'  => 'Referral',
                        'online'    => 'Online',
                        'marketing' => 'Marketing',
                        'other'     => 'Other',
                    ];
                    foreach ($sources as $val => $label): ?>
                    <option value="<?= e($val) ?>" <?= ($old['source'] ?? '') === $val ? 'selected' : '' ?>>
                        <?= e($label) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
                <?php if ($err('source')): ?>
                <p class="mt-1 text-xs text-red-600" role="alert"><?= e($err('source')) ?></p>
                <?php endif; ?>
            </div>

            <!-- Status -->
            <div>
                <label for="status" class="block text-sm font-medium text-slate-700 mb-1">Status</label>
                <select id="status"
                        name="status"
                        class="border <?= $err('status') ? 'border-red-400 bg-red-50' : 'border-slate-300' ?> rounded-lg px-3 py-2 w-full text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent transition-colors">
                    <option value="new"       <?= ($old['status'] ?? 'new') === 'new'       ? 'selected' : '' ?>>New</option>
                    <option value="contacted" <?= ($old['status'] ?? '') === 'contacted' ? 'selected' : '' ?>>Contacted</option>
                    <option value="qualified" <?= ($old['status'] ?? '') === 'qualified' ? 'selected' : '' ?>>Qualified</option>
                    <option value="proposal"  <?= ($old['status'] ?? '') === 'proposal'  ? 'selected' : '' ?>>Proposal</option>
                </select>
                <?php if ($err('status')): ?>
                <p class="mt-1 text-xs text-red-600" role="alert"><?= e($err('status')) ?></p>
                <?php endif; ?>
            </div>

            <!-- Assigned To -->
            <div>
                <label for="assigned_to" class="block text-sm font-medium text-slate-700 mb-1">Assign To</label>
                <select id="assigned_to"
                        name="assigned_to"
                        class="border <?= $err('assigned_to') ? 'border-red-400 bg-red-50' : 'border-slate-300' ?> rounded-lg px-3 py-2 w-full text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent transition-colors">
                    <option value="">— Unassigned —</option>
                    <?php foreach ($users as $u): ?>
                    <option value="<?= e($u['id']) ?>" <?= ($old['assigned_to'] ?? '') == $u['id'] ? 'selected' : '' ?>>
                        <?= e($u['name']) ?><?= !empty($u['role']) ? ' (' . e(ucfirst($u['role'])) . ')' : '' ?>
                    </option>
                    <?php endforeach; ?>
                </select>
                <?php if ($err('assigned_to')): ?>
                <p class="mt-1 text-xs text-red-600" role="alert"><?= e($err('assigned_to')) ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Notes -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <h2 class="text-base font-semibold text-slate-700 mb-5 flex items-center gap-2">
            <svg class="w-4 h-4 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            Notes
        </h2>
        <label for="notes" class="sr-only">Notes</label>
        <textarea id="notes"
                  name="notes"
                  rows="4"
                  maxlength="2000"
                  placeholder="Describe the lead's requirements or any relevant information…"
                  class="border border-slate-300 rounded-lg px-3 py-2 w-full text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent transition-colors resize-y"><?= e($old['notes'] ?? '') ?></textarea>
    </div>

    <!-- Form actions -->
    <div class="flex flex-col-reverse sm:flex-row items-center justify-end gap-3 pt-2">
        <a href="/leads"
           class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-lg border border-slate-300 bg-white text-slate-700 hover:bg-slate-50 text-sm font-medium transition-colors">
            Cancel
        </a>
        <button type="submit"
                class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-sky-500 hover:bg-sky-600 text-white px-5 py-2.5 rounded-lg text-sm font-medium transition-colors shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            Save Lead
        </button>
    </div>
</form>

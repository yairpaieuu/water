<?php $pageTitle = 'Settings'; ob_start(); ?>
<?php include VIEW_PATH . '/partials/flash.php'; ?>
<div class="max-w-4xl mx-auto" x-data="{ tab: 'general' }">
  <h2 class="text-xl font-bold text-slate-800 mb-6">Settings</h2>

  <div class="flex gap-1 mb-6 bg-slate-100 rounded-xl p-1 w-fit">
    <button @click="tab = 'general'" :class="tab === 'general' ? 'bg-white shadow text-sky-600' : 'text-slate-600 hover:text-slate-800'" class="px-5 py-2 rounded-lg text-sm font-medium transition-all">General</button>
    <button @click="tab = 'logo'" :class="tab === 'logo' ? 'bg-white shadow text-sky-600' : 'text-slate-600 hover:text-slate-800'" class="px-5 py-2 rounded-lg text-sm font-medium transition-all">Logo</button>
    <button @click="tab = 'branches'" :class="tab === 'branches' ? 'bg-white shadow text-sky-600' : 'text-slate-600 hover:text-slate-800'" class="px-5 py-2 rounded-lg text-sm font-medium transition-all">Branches</button>
  </div>

  <!-- General Tab -->
  <div x-show="tab === 'general'" x-cloak>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
      <h3 class="font-semibold text-slate-800 mb-5">General Settings</h3>
      <form method="POST" action="/settings/update" class="space-y-5">
        <?= \App\Core\CSRF::field() ?>
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">App Name</label>
          <input type="text" name="app_name" value="<?= e($settings['app_name'] ?? 'AquaCRM') ?>" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500">
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Timezone</label>
          <select name="timezone" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500">
            <?php foreach (DateTimeZone::listIdentifiers() as $tz): ?>
              <option value="<?= e($tz) ?>" <?= (($settings['timezone'] ?? 'UTC') === $tz) ? 'selected' : '' ?>><?= e($tz) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Currency</label>
          <input type="text" name="currency" value="<?= e($settings['currency'] ?? 'LKR') ?>" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500" placeholder="LKR">
        </div>
        <div class="flex justify-end pt-2">
          <button type="submit" class="px-5 py-2 bg-sky-500 text-white text-sm font-semibold rounded-lg hover:bg-sky-600 transition-colors">Save Settings</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Logo Tab -->
  <div x-show="tab === 'logo'" x-cloak>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
      <h3 class="font-semibold text-slate-800 mb-5">Logo Settings</h3>
      <p class="text-sm text-slate-500 mb-6">Upload your company logo. It will appear in the login page and the admin panel sidebar. Accepted: JPEG, PNG, GIF, WebP (max 2 MB).</p>

      <!-- Current logo preview -->
      <?php $currentLogo = $settings['app_logo'] ?? ''; ?>
      <?php if ($currentLogo): ?>
      <div class="mb-6 p-4 bg-slate-50 rounded-xl border border-slate-200 flex items-center gap-4">
        <img src="<?= e('/assets/uploads/' . $currentLogo) ?>"
             alt="Current logo"
             class="h-16 w-auto object-contain bg-white rounded-lg p-1 border border-slate-200">
        <div>
          <p class="text-sm font-medium text-slate-700">Current Logo</p>
          <p class="text-xs text-slate-400 mt-0.5"><?= e($currentLogo) ?></p>
          <form method="POST" action="/settings/remove-logo" class="mt-2" onsubmit="return confirm('Remove the current logo?')">
            <?= \App\Core\CSRF::field() ?>
            <button type="submit" class="text-xs text-red-600 hover:text-red-800 font-medium">Remove logo</button>
          </form>
        </div>
      </div>
      <?php else: ?>
      <div class="mb-6 p-4 bg-slate-50 rounded-xl border border-slate-200 text-center text-sm text-slate-400">
        No logo uploaded yet. The default icon will be used.
      </div>
      <?php endif; ?>

      <!-- Upload form -->
      <form method="POST" action="/settings/upload-logo" enctype="multipart/form-data" class="space-y-4">
        <?= \App\Core\CSRF::field() ?>
        <div x-data="{ filename: '' }">
          <label class="block text-sm font-medium text-slate-700 mb-2">
            <?= $currentLogo ? 'Replace Logo' : 'Upload Logo' ?>
          </label>
          <div class="flex items-center gap-3">
            <label class="cursor-pointer flex items-center gap-2 px-4 py-2 bg-slate-100 hover:bg-slate-200 border border-slate-300 rounded-lg text-sm font-medium text-slate-700 transition-colors">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
              </svg>
              Choose File
              <input type="file"
                     name="app_logo"
                     accept="image/jpeg,image/png,image/gif,image/webp"
                     required
                     class="sr-only"
                     @change="filename = $event.target.files[0]?.name ?? ''">
            </label>
            <span x-text="filename || 'No file chosen'" class="text-sm text-slate-500"></span>
          </div>
        </div>
        <div class="flex justify-end pt-2">
          <button type="submit" class="px-5 py-2 bg-sky-500 text-white text-sm font-semibold rounded-lg hover:bg-sky-600 transition-colors">
            Upload Logo
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Branches Tab -->
  <div x-show="tab === 'branches'" x-cloak>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden mb-6">
      <div class="px-6 py-4 border-b border-slate-100"><h3 class="font-semibold text-slate-800">Branches</h3></div>
      <?php if (empty($settings['branches'] ?? [])): ?>
        <div class="px-6 py-8 text-center text-slate-400 text-sm">No branches found.</div>
      <?php else: ?>
      <table class="w-full text-sm">
        <thead class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wide">
          <tr>
            <th class="px-6 py-3 text-left">Name</th>
            <th class="px-6 py-3 text-left">Address</th>
            <th class="px-6 py-3 text-left">Phone</th>
            <th class="px-6 py-3 text-left">Status</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          <?php foreach ($settings['branches'] ?? [] as $br): ?>
          <tr class="hover:bg-slate-50">
            <td class="px-6 py-3 font-medium text-slate-800"><?= e($br['name'] ?? '') ?></td>
            <td class="px-6 py-3 text-slate-600"><?= e($br['address'] ?? '—') ?></td>
            <td class="px-6 py-3 text-slate-600"><?= e($br['phone'] ?? '—') ?></td>
            <td class="px-6 py-3">
              <?php $bs = ($br['status'] ?? '') === 'active'; ?>
              <span class="px-2 py-0.5 rounded-full text-xs font-semibold <?= $bs ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-600' ?>"><?= $bs ? 'Active' : 'Inactive' ?></span>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      <?php endif; ?>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
      <h3 class="font-semibold text-slate-800 mb-5">Add New Branch</h3>
      <form method="POST" action="/branches/store" class="space-y-4">
        <?= \App\Core\CSRF::field() ?>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Branch Name <span class="text-red-500">*</span></label>
            <input type="text" name="name" required class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500">
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Phone</label>
            <input type="text" name="phone" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500">
          </div>
          <div class="md:col-span-2">
            <label class="block text-sm font-medium text-slate-700 mb-1">Address</label>
            <input type="text" name="address" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500">
          </div>
        </div>
        <div class="flex justify-end pt-2">
          <button type="submit" class="px-5 py-2 bg-sky-500 text-white text-sm font-semibold rounded-lg hover:bg-sky-600 transition-colors">Add Branch</button>
        </div>
      </form>
    </div>
  </div>
</div>
<?php $content = ob_get_clean(); include VIEW_PATH . '/layouts/app.php'; ?>

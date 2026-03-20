<?php $pageTitle = 'Create User'; ob_start(); ?>
<?php include VIEW_PATH . '/partials/flash.php'; ?>
<div class="max-w-2xl mx-auto">
  <div class="flex items-center justify-between mb-6">
    <h2 class="text-xl font-bold text-slate-800">Create User</h2>
    <a href="/users" class="text-sm text-sky-500 hover:underline">&larr; Back</a>
  </div>
  <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
    <?php if (!empty($errors)): ?>
      <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700">
        <ul class="list-disc list-inside space-y-1">
          <?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>
    <form method="POST" action="/users/store" class="space-y-5">
      <?= \App\Core\CSRF::field() ?>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <div class="md:col-span-2">
          <label class="block text-sm font-medium text-slate-700 mb-1">Full Name <span class="text-red-500">*</span></label>
          <input type="text" name="name" value="<?= e($_POST['name'] ?? '') ?>" required class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500">
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Username <span class="text-red-500">*</span></label>
          <input type="text" name="username" value="<?= e($_POST['username'] ?? '') ?>" required autocomplete="off" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500">
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Email <span class="text-red-500">*</span></label>
          <input type="email" name="email" value="<?= e($_POST['email'] ?? '') ?>" required class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500">
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Role <span class="text-red-500">*</span></label>
          <select name="role" required class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500">
            <option value="">-- Select Role --</option>
            <?php foreach (['admin'=>'Admin','manager'=>'Manager','technician'=>'Technician','sales'=>'Sales'] as $val => $label): ?>
              <option value="<?= $val ?>" <?= (($_POST['role'] ?? '') === $val) ? 'selected' : '' ?>><?= $label ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Password <span class="text-red-500">*</span></label>
          <input type="password" name="password" required class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500">
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Confirm Password <span class="text-red-500">*</span></label>
          <input type="password" name="password_confirmation" required class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500">
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Branch</label>
          <select name="branch_id" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500">
            <option value="">-- No Branch --</option>
            <?php foreach ($branches as $b): ?>
              <option value="<?= e($b['id']) ?>" <?= (($_POST['branch_id'] ?? '') == $b['id']) ? 'selected' : '' ?>><?= e($b['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Status</label>
          <select name="status" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500">
            <option value="active" <?= (($_POST['status'] ?? 'active') === 'active') ? 'selected' : '' ?>>Active</option>
            <option value="inactive" <?= (($_POST['status'] ?? '') === 'inactive') ? 'selected' : '' ?>>Inactive</option>
          </select>
        </div>
      </div>
      <div class="flex justify-end gap-3 pt-2">
        <a href="/users" class="px-5 py-2 rounded-lg border border-slate-300 text-sm text-slate-700 hover:bg-slate-50">Cancel</a>
        <button type="submit" class="px-5 py-2 rounded-lg bg-sky-500 text-white text-sm font-semibold hover:bg-sky-600 transition-colors">Create User</button>
      </div>
    </form>
  </div>
</div>
<?php $content = ob_get_clean(); include VIEW_PATH . '/layouts/app.php'; ?>

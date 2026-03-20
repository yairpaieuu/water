<?php $pageTitle = 'Add Employee'; ob_start(); ?>
<?php include VIEW_PATH . '/partials/flash.php'; ?>
<div class="max-w-3xl mx-auto">
  <div class="flex items-center justify-between mb-6">
    <h2 class="text-xl font-bold text-slate-800">Add New Employee</h2>
    <a href="/employees" class="text-sm text-sky-500 hover:underline">&larr; Back</a>
  </div>
  <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
    <?php if (!empty($errors)): ?>
      <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700">
        <ul class="list-disc list-inside space-y-1">
          <?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>
    <form method="POST" action="/employees/store" class="space-y-5">
      <?= \App\Core\CSRF::field() ?>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Employee Code</label>
          <input type="text" name="employee_code" value="<?= e($_POST['employee_code'] ?? '') ?>" readonly class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm bg-slate-50 text-slate-500 cursor-not-allowed">
          <p class="text-xs text-slate-400 mt-1">Auto-generated</p>
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Status</label>
          <select name="status" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500">
            <option value="active" <?= (($_POST['status'] ?? 'active') === 'active') ? 'selected' : '' ?>>Active</option>
            <option value="inactive" <?= (($_POST['status'] ?? '') === 'inactive') ? 'selected' : '' ?>>Inactive</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">First Name <span class="text-red-500">*</span></label>
          <input type="text" name="first_name" value="<?= e($_POST['first_name'] ?? '') ?>" required class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500">
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Last Name <span class="text-red-500">*</span></label>
          <input type="text" name="last_name" value="<?= e($_POST['last_name'] ?? '') ?>" required class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500">
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Email <span class="text-red-500">*</span></label>
          <input type="email" name="email" value="<?= e($_POST['email'] ?? '') ?>" required class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500">
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Phone</label>
          <input type="text" name="phone" value="<?= e($_POST['phone'] ?? '') ?>" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500">
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Branch</label>
          <select name="branch_id" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500">
            <option value="">-- Select Branch --</option>
            <?php foreach ($branches as $b): ?>
              <option value="<?= e($b['id']) ?>" <?= (($_POST['branch_id'] ?? '') == $b['id']) ? 'selected' : '' ?>><?= e($b['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Department</label>
          <select name="department" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500">
            <?php foreach (['management'=>'Management','sales'=>'Sales','technical'=>'Technical','admin'=>'Admin'] as $val => $label): ?>
              <option value="<?= $val ?>" <?= (($_POST['department'] ?? '') === $val) ? 'selected' : '' ?>><?= $label ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Position</label>
          <input type="text" name="position" value="<?= e($_POST['position'] ?? '') ?>" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500" placeholder="e.g. Field Technician">
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Date Joined</label>
          <input type="date" name="date_joined" value="<?= e($_POST['date_joined'] ?? '') ?>" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500">
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Salary</label>
          <input type="number" step="0.01" name="salary" value="<?= e($_POST['salary'] ?? '') ?>" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500" placeholder="0.00">
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Linked User Account <span class="text-slate-400 font-normal">(optional)</span></label>
          <select name="user_id" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500">
            <option value="">-- None --</option>
            <?php foreach ($users as $u): ?>
              <option value="<?= e($u['id']) ?>" <?= (($_POST['user_id'] ?? '') == $u['id']) ? 'selected' : '' ?>><?= e($u['name']) ?> (<?= e($u['email']) ?>)</option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Address</label>
          <input type="text" name="address" value="<?= e($_POST['address'] ?? '') ?>" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500">
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">City</label>
          <input type="text" name="city" value="<?= e($_POST['city'] ?? '') ?>" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500">
        </div>
      </div>
      <div class="flex justify-end gap-3 pt-2">
        <a href="/employees" class="px-5 py-2 rounded-lg border border-slate-300 text-sm text-slate-700 hover:bg-slate-50">Cancel</a>
        <button type="submit" class="px-5 py-2 rounded-lg bg-sky-500 text-white text-sm font-semibold hover:bg-sky-600 transition-colors">Save Employee</button>
      </div>
    </form>
  </div>
</div>
<?php $content = ob_get_clean(); include VIEW_PATH . '/layouts/app.php'; ?>

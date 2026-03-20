<?php $pageTitle = 'Create Contract'; ob_start(); ?>
<?php include VIEW_PATH . '/partials/flash.php'; ?>
<div class="max-w-3xl mx-auto">
  <div class="flex items-center justify-between mb-6">
    <h2 class="text-xl font-bold text-slate-800">New Service Contract</h2>
    <a href="/contracts" class="text-sm text-sky-500 hover:underline">&larr; Back to Contracts</a>
  </div>
  <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
    <?php if (!empty($errors)): ?>
      <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700">
        <ul class="list-disc list-inside space-y-1">
          <?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>
    <form method="POST" action="/contracts/store" class="space-y-5">
      <?= \App\Core\CSRF::field() ?>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Customer <span class="text-red-500">*</span></label>
          <select name="customer_id" required class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
            <option value="">-- Select Customer --</option>
            <?php foreach ($customers as $c): ?>
              <option value="<?= e($c['id']) ?>" <?= (($_POST['customer_id'] ?? '') == $c['id']) ? 'selected' : '' ?>><?= e($c['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Product <span class="text-red-500">*</span></label>
          <select name="product_id" required class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
            <option value="">-- Select Product --</option>
            <?php foreach ($products as $p): ?>
              <option value="<?= e($p['id']) ?>" <?= (($_POST['product_id'] ?? '') == $p['id']) ? 'selected' : '' ?>><?= e($p['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Service Type <span class="text-red-500">*</span></label>
          <select name="service_type_id" required class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
            <option value="">-- Select Type --</option>
            <?php foreach ($serviceTypes as $st): ?>
              <option value="<?= e($st['id']) ?>" <?= (($_POST['service_type_id'] ?? '') == $st['id']) ? 'selected' : '' ?>><?= e($st['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Branch <span class="text-red-500">*</span></label>
          <select name="branch_id" required class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
            <option value="">-- Select Branch --</option>
            <?php foreach ($branches as $b): ?>
              <option value="<?= e($b['id']) ?>" <?= (($_POST['branch_id'] ?? '') == $b['id']) ? 'selected' : '' ?>><?= e($b['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Installation Date <span class="text-red-500">*</span></label>
          <input type="date" name="installation_date" value="<?= e($_POST['installation_date'] ?? '') ?>" required class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Serial Number</label>
          <input type="text" name="serial_number" value="<?= e($_POST['serial_number'] ?? '') ?>" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500" placeholder="e.g. SN-00123">
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Assigned Technician</label>
          <select name="assigned_technician_id" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
            <option value="">-- Unassigned --</option>
            <?php foreach ($employees as $emp): ?>
              <option value="<?= e($emp['id']) ?>" <?= (($_POST['assigned_technician_id'] ?? '') == $emp['id']) ? 'selected' : '' ?>><?= e($emp['first_name'] . ' ' . $emp['last_name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Status</label>
          <select name="status" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
            <?php foreach (['active'=>'Active','inactive'=>'Inactive','expired'=>'Expired','cancelled'=>'Cancelled'] as $val => $label): ?>
              <option value="<?= $val ?>" <?= (($_POST['status'] ?? 'active') === $val) ? 'selected' : '' ?>><?= $label ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Location Notes</label>
        <textarea name="location_notes" rows="3" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500" placeholder="Address or installation location details..."><?= e($_POST['location_notes'] ?? '') ?></textarea>
      </div>
      <div class="flex justify-end gap-3 pt-2">
        <a href="/contracts" class="px-5 py-2 rounded-lg border border-slate-300 text-sm text-slate-700 hover:bg-slate-50">Cancel</a>
        <button type="submit" class="px-5 py-2 rounded-lg bg-sky-500 text-white text-sm font-semibold hover:bg-sky-600 transition-colors">Create Contract</button>
      </div>
    </form>
  </div>
</div>
<?php $content = ob_get_clean(); include VIEW_PATH . '/layouts/app.php'; ?>

<?php $pageTitle = 'Users'; ob_start(); ?>
<?php include VIEW_PATH . '/partials/flash.php'; ?>
<div class="space-y-6">
  <div class="flex items-center justify-between">
    <h2 class="text-xl font-bold text-slate-800">System Users</h2>
    <a href="/users/create" class="inline-flex items-center gap-2 px-4 py-2 bg-sky-500 text-white text-sm font-semibold rounded-lg hover:bg-sky-600 transition-colors">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
      Add User
    </a>
  </div>

  <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wide">
          <tr>
            <th class="px-6 py-3 text-left">Name</th>
            <th class="px-6 py-3 text-left">Email</th>
            <th class="px-6 py-3 text-left">Role</th>
            <th class="px-6 py-3 text-left">Branch</th>
            <th class="px-6 py-3 text-left">Status</th>
            <th class="px-6 py-3 text-left">Last Login</th>
            <th class="px-6 py-3 text-left">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          <?php if (empty($users)): ?>
            <tr><td colspan="7" class="px-6 py-10 text-center text-slate-400">No users found.</td></tr>
          <?php else: ?>
          <?php foreach ($users as $u):
            $roleColors = ['admin'=>'bg-red-100 text-red-700','manager'=>'bg-purple-100 text-purple-700','technician'=>'bg-amber-100 text-amber-700','sales'=>'bg-sky-100 text-sky-700'];
            $roleColor = $roleColors[$u['role'] ?? ''] ?? 'bg-slate-100 text-slate-600';
            $isActive = ($u['status'] ?? '') === 'active';
            $branchName = '';
            foreach ($branches as $b) { if ($b['id'] == ($u['branch_id'] ?? '')) { $branchName = $b['name']; break; } }
          ?>
          <tr class="hover:bg-slate-50 transition-colors" x-data="{ editing: false }">
            <td class="px-6 py-3">
              <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-sky-500 flex items-center justify-center text-white text-xs font-bold">
                  <?= strtoupper(mb_substr($u['name'] ?? 'U', 0, 1)) ?>
                </div>
                <span class="font-medium text-slate-800"><?= e($u['name'] ?? '') ?></span>
              </div>
            </td>
            <td class="px-6 py-3 text-slate-600"><?= e($u['email'] ?? '') ?></td>
            <td class="px-6 py-3">
              <span x-show="!editing" class="px-2 py-0.5 rounded-full text-xs font-semibold <?= $roleColor ?>"><?= ucfirst(e($u['role'] ?? '')) ?></span>
              <div x-show="editing" style="display:none">
                <form method="POST" action="/users/<?= e($u['id']) ?>/update" class="flex gap-1 items-center">
                  <?= \App\Core\CSRF::field() ?>
                  <input type="hidden" name="action" value="role">
                  <select name="role" class="border border-slate-300 rounded px-2 py-1 text-xs focus:ring-1 focus:ring-sky-500">
                    <?php foreach (['admin','manager','technician','sales'] as $r): ?>
                      <option value="<?= $r ?>" <?= ($u['role'] ?? '') === $r ? 'selected' : '' ?>><?= ucfirst($r) ?></option>
                    <?php endforeach; ?>
                  </select>
                  <select name="status" class="border border-slate-300 rounded px-2 py-1 text-xs focus:ring-1 focus:ring-sky-500">
                    <option value="active" <?= ($u['status'] ?? '') === 'active' ? 'selected' : '' ?>>Active</option>
                    <option value="inactive" <?= ($u['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                  </select>
                  <button type="submit" class="px-2 py-1 bg-sky-500 text-white rounded text-xs hover:bg-sky-600">Save</button>
                </form>
              </div>
            </td>
            <td class="px-6 py-3 text-slate-600"><?= e($branchName ?: '—') ?></td>
            <td class="px-6 py-3">
              <span class="px-2 py-0.5 rounded-full text-xs font-semibold <?= $isActive ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' ?>"><?= $isActive ? 'Active' : 'Inactive' ?></span>
            </td>
            <td class="px-6 py-3 text-slate-500 text-xs"><?= e($u['last_login'] ?? 'Never') ?></td>
            <td class="px-6 py-3">
              <div class="flex items-center gap-2">
                <button @click="editing = !editing" class="text-xs text-sky-500 hover:underline">Edit</button>
                <form method="POST" action="/users/<?= e($u['id']) ?>/delete" onsubmit="return false;" x-data>
                  <?= \App\Core\CSRF::field() ?>
                  <button type="button" class="text-xs text-red-500 hover:underline"
                    @click="Swal.fire({ title: 'Delete User?', text: 'This cannot be undone.', icon: 'warning', showCancelButton: true, confirmButtonColor: '#ef4444', confirmButtonText: 'Delete' }).then(r => { if(r.isConfirmed) $el.closest('form').submit(); })">
                    Delete
                  </button>
                </form>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?php $content = ob_get_clean(); include VIEW_PATH . '/layouts/app.php'; ?>

<?php
/** @var array $kpis */
/** @var array $recentActivities */
/** @var array $user */
$kpis              ??= [];
$recentActivities  ??= [];
?>

<!-- KPI Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 md:gap-6 mb-6">

    <!-- Total Customers -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5 flex items-center gap-4 hover:shadow-md transition-shadow">
        <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center">
            <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-sm text-slate-500 font-medium truncate">Total Customers</p>
            <p class="text-2xl font-bold text-slate-800">
                <?= number_format((int)($kpis['total_customers'] ?? 0)) ?>
            </p>
        </div>
        <div class="flex-shrink-0 text-xs text-green-600 font-semibold bg-green-50 px-2 py-1 rounded-full">+2.5%</div>
    </div>

    <!-- Active Services -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5 flex items-center gap-4 hover:shadow-md transition-shadow">
        <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-cyan-50 flex items-center justify-center">
            <svg class="w-6 h-6 text-cyan-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-sm text-slate-500 font-medium truncate">Active Services</p>
            <p class="text-2xl font-bold text-slate-800">
                <?= number_format((int)($kpis['active_services'] ?? 0)) ?>
            </p>
        </div>
        <div class="flex-shrink-0 text-xs text-green-600 font-semibold bg-green-50 px-2 py-1 rounded-full">+5.1%</div>
    </div>

    <!-- Pending Jobs -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5 flex items-center gap-4 hover:shadow-md transition-shadow">
        <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-amber-50 flex items-center justify-center">
            <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-sm text-slate-500 font-medium truncate">Pending Jobs</p>
            <p class="text-2xl font-bold text-slate-800">
                <?= number_format((int)($kpis['pending_jobs'] ?? 0)) ?>
            </p>
        </div>
        <div class="flex-shrink-0 text-xs text-amber-600 font-semibold bg-amber-50 px-2 py-1 rounded-full">Action</div>
    </div>

    <!-- Monthly Revenue -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5 flex items-center gap-4 hover:shadow-md transition-shadow">
        <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center">
            <svg class="w-6 h-6 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-sm text-slate-500 font-medium truncate">Monthly Revenue</p>
            <p class="text-2xl font-bold text-slate-800">
                LKR <?= htmlspecialchars($kpis['monthly_revenue'] ?? '0.00', ENT_QUOTES, 'UTF-8') ?>
            </p>
        </div>
        <div class="flex-shrink-0 text-xs text-green-600 font-semibold bg-green-50 px-2 py-1 rounded-full">+8.3%</div>
    </div>
</div>

<!-- Charts Row -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 md:gap-6 mb-6">

    <!-- Revenue Chart -->
    <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-slate-100 p-5">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-base font-semibold text-slate-800">Revenue Overview</h3>
                <p class="text-xs text-slate-500 mt-0.5">Monthly revenue for the current year</p>
            </div>
            <select class="text-xs border border-slate-200 rounded-lg px-2 py-1.5 text-slate-600 focus:outline-none focus:ring-2 focus:ring-primary-500">
                <option>This Year</option>
                <option>Last Year</option>
            </select>
        </div>
        <div class="h-56">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>

    <!-- Service Status Doughnut -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5">
        <div class="mb-4">
            <h3 class="text-base font-semibold text-slate-800">Service Status</h3>
            <p class="text-xs text-slate-500 mt-0.5">Breakdown by current status</p>
        </div>
        <div class="flex justify-center h-44">
            <canvas id="serviceChart"></canvas>
        </div>
        <div class="mt-4 space-y-2">
            <div class="flex items-center justify-between text-xs">
                <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500 inline-block"></span>Active</span>
                <span class="font-semibold text-slate-700"><?= (int)($kpis['active_services'] ?? 0) ?></span>
            </div>
            <div class="flex items-center justify-between text-xs">
                <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-amber-400 inline-block"></span>Pending</span>
                <span class="font-semibold text-slate-700"><?= (int)($kpis['pending_jobs'] ?? 0) ?></span>
            </div>
            <div class="flex items-center justify-between text-xs">
                <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-slate-300 inline-block"></span>Inactive</span>
                <span class="font-semibold text-slate-700">0</span>
            </div>
        </div>
    </div>
</div>

<!-- Bottom Row: Recent Activities + Quick Actions -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 md:gap-6">

    <!-- Recent Activities -->
    <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-slate-100 p-5">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-semibold text-slate-800">Recent Activities</h3>
            <a href="/activity-log" class="text-xs text-primary-500 hover:text-primary-600 font-medium">View all</a>
        </div>

        <?php if (empty($recentActivities)): ?>
        <div class="flex flex-col items-center justify-center py-10 text-slate-400">
            <svg class="w-12 h-12 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <p class="text-sm">No recent activity found</p>
        </div>
        <?php else: ?>
        <div class="space-y-3 max-h-72 overflow-y-auto pr-1">
            <?php foreach ($recentActivities as $activity): ?>
            <div class="flex items-start gap-3">
                <div class="flex-shrink-0 w-8 h-8 rounded-full bg-primary-100 flex items-center justify-center mt-0.5">
                    <svg class="w-4 h-4 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm text-slate-700">
                        <?= htmlspecialchars($activity['description'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                    </p>
                    <p class="text-xs text-slate-400 mt-0.5">
                        <?= htmlspecialchars($activity['user_name'] ?? 'System', ENT_QUOTES, 'UTF-8') ?>
                        &mdash;
                        <?= htmlspecialchars($activity['created_at'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                    </p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5">
        <h3 class="text-base font-semibold text-slate-800 mb-4">Quick Actions</h3>
        <div class="space-y-2">
            <a href="/customers/create"
               class="flex items-center gap-3 w-full px-4 py-3 bg-primary-50 hover:bg-primary-100 text-primary-700 rounded-xl transition-colors text-sm font-medium">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                </svg>
                New Customer
            </a>
            <a href="/jobs/create"
               class="flex items-center gap-3 w-full px-4 py-3 bg-amber-50 hover:bg-amber-100 text-amber-700 rounded-xl transition-colors text-sm font-medium">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Create Job
            </a>
            <a href="/invoices/create"
               class="flex items-center gap-3 w-full px-4 py-3 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 rounded-xl transition-colors text-sm font-medium">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                New Invoice
            </a>
            <a href="/leads/create"
               class="flex items-center gap-3 w-full px-4 py-3 bg-violet-50 hover:bg-violet-100 text-violet-700 rounded-xl transition-colors text-sm font-medium">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Add Lead
            </a>
            <a href="/reports/sales"
               class="flex items-center gap-3 w-full px-4 py-3 bg-slate-50 hover:bg-slate-100 text-slate-700 rounded-xl transition-colors text-sm font-medium">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                View Reports
            </a>
        </div>
    </div>
</div>

<!-- Chart initialization -->
<script>
(function () {
    const activeServices = <?= (int)($kpis['active_services'] ?? 0) ?>;
    const pendingJobs    = <?= (int)($kpis['pending_jobs'] ?? 0) ?>;

    // Revenue line chart
    const rCtx = document.getElementById('revenueChart');
    if (rCtx) {
        new Chart(rCtx, {
            type: 'line',
            data: {
                labels: ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
                datasets: [{
                    label: 'Revenue (LKR)',
                    data: [0,0,0,0,0,0,0,0,0,0,0,0],
                    fill: true,
                    backgroundColor: 'rgba(14,165,233,0.08)',
                    borderColor: '#0ea5e9',
                    borderWidth: 2,
                    pointBackgroundColor: '#0ea5e9',
                    pointRadius: 3,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(0,0,0,0.04)' },
                        ticks: { color: '#94a3b8', font: { size: 11 } }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: '#94a3b8', font: { size: 11 } }
                    }
                }
            }
        });
    }

    // Service doughnut chart
    const sCtx = document.getElementById('serviceChart');
    if (sCtx) {
        new Chart(sCtx, {
            type: 'doughnut',
            data: {
                labels: ['Active', 'Pending', 'Inactive'],
                datasets: [{
                    data: [activeServices, pendingJobs, 0],
                    backgroundColor: ['#10b981', '#f59e0b', '#cbd5e1'],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '72%',
                plugins: {
                    legend: { display: false },
                    tooltip: { callbacks: { label: ctx => ` ${ctx.label}: ${ctx.parsed}` } }
                }
            }
        });
    }
})();
</script>

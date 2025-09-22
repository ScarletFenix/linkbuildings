<?php
// dashboard.php
require_once __DIR__ . '/../../includes/db.php';

// ---- Get Totals ----
$totalSites = $pdo->query("SELECT COUNT(*) FROM sites")->fetchColumn();
$activeSites = $pdo->query("SELECT COUNT(*) FROM sites WHERE status = 'active'")->fetchColumn();
$inactiveSites = $pdo->query("SELECT COUNT(*) FROM sites WHERE status = 'inactive'")->fetchColumn();
$totalUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
?>

<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-800">Dashboard Overview</h2>
    <p class="text-gray-500">Quick snapshot of your platform.</p>
</div>

<!-- KPI Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">

    <!-- Total Sites -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                <i class="fas fa-globe text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-gray-500">Total Sites</p>
                <h3 class="text-2xl font-bold"><?= $totalSites ?></h3>
            </div>
        </div>
    </div>

    <!-- Active Sites -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-green-100 text-green-600">
                <i class="fas fa-check-circle text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-gray-500">Active Sites</p>
                <h3 class="text-2xl font-bold"><?= $activeSites ?></h3>
            </div>
        </div>
    </div>

    <!-- Inactive Sites -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-red-100 text-red-600">
                <i class="fas fa-times-circle text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-gray-500">Inactive Sites</p>
                <h3 class="text-2xl font-bold"><?= $inactiveSites ?></h3>
            </div>
        </div>
    </div>

    <!-- Total Users -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                <i class="fas fa-users text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-gray-500">Total Users</p>
                <h3 class="text-2xl font-bold"><?= $totalUsers ?></h3>
            </div>
        </div>
    </div>

</div>

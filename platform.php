<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';

// Restrict to admin only
requireRole('admin');

// Get the current page from the query string
$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';

// Fetch statistics from the database
$totalSites = 0;
$totalOrders = 0;
$totalRevenue = 0;
$pendingOrders = 0;
$totalBuyers = 0;

try {
    // Get total sites
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM sites WHERE status = 'active'");
    $totalSites = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Get total orders
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM orders");
    $totalOrders = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Get total revenue
    $stmt = $pdo->query("SELECT SUM(amount) as total FROM orders WHERE status = 'completed'");
    $revenueData = $stmt->fetch(PDO::FETCH_ASSOC);
    $totalRevenue = $revenueData['total'] ? $revenueData['total'] : 0;
    
    // Get pending orders
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM orders WHERE status = 'pending'");
    $pendingOrders = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Get total buyers
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM buyers WHERE status = 'active'");
    $totalBuyers = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Get recent sites
    $stmt = $pdo->query("SELECT id, site_name, site_url, created_at FROM sites ORDER BY created_at DESC LIMIT 5");
    $recentSites = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get recent orders
    $stmt = $pdo->query("SELECT o.id, o.amount, o.status, o.created_at, s.site_name 
                         FROM orders o 
                         JOIN sites s ON o.site_id = s.id 
                         ORDER BY o.created_at DESC LIMIT 5");
    $recentOrders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get all sites for management
    $stmt = $pdo->query("SELECT * FROM sites ORDER BY created_at DESC");
    $allSites = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get all orders for management
    $stmt = $pdo->query("SELECT o.*, s.site_name, b.name as buyer_name 
                         FROM orders o 
                         JOIN sites s ON o.site_id = s.id 
                         JOIN buyers b ON o.buyer_id = b.id 
                         ORDER BY o.created_at DESC");
    $allOrders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get all buyers
    $stmt = $pdo->query("SELECT * FROM buyers ORDER BY created_at DESC");
    $allBuyers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SEO Link Building - Admin Dashboard</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            primary: '#4361ee',
            secondary: '#3f37c9',
            success: '#4cc9f0',
            danger: '#f72585',
            warning: '#f77f00',
            info: '#4895ef',
            dark: '#2b2d42',
            light: '#f8f9fa'
          }
        }
      }
    }
  </script>
  <style>
    .sidebar {
      width: 250px;
      transition: all 0.3s ease;
    }
    .main-content {
      margin-left: 250px;
      transition: all 0.3s ease;
    }
    @media (max-width: 768px) {
      .sidebar {
        margin-left: -250px;
        position: absolute;
        z-index: 100;
        height: 100%;
      }
      .main-content {
        margin-left: 0;
      }
      .sidebar.active {
        margin-left: 0;
      }
    }
    .dropdown:hover .dropdown-menu {
      display: block;
    }
    .email-image {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background: linear-gradient(135deg, #4361ee 0%, #3a0ca3 100%);
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-weight: bold;
      cursor: pointer;
      transition: all 0.3s ease;
    }
    .email-image:hover {
      transform: scale(1.05);
      box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }
    .stat-card {
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .stat-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    .site-status-active {
      background-color: #10B981;
      color: white;
    }
    .site-status-pending {
      background-color: #F59E0B;
      color: white;
    }
    .order-status-completed {
      background-color: #10B981;
      color: white;
    }
    .order-status-pending {
      background-color: #F59E0B;
      color: white;
    }
    .buyer-status-active {
      background-color: #10B981;
      color: white;
    }
    .buyer-status-inactive {
      background-color: #EF4444;
      color: white;
    }
    .page-content {
      display: none;
    }
    .page-content.active {
      display: block;
    }
  </style>
</head>
<body class="bg-gray-100 min-h-screen flex">
  <!-- Sidebar -->
  <div class="sidebar bg-white shadow-lg fixed h-full">
    <div class="p-4 border-b">
      <h1 class="text-xl font-bold text-primary flex items-center">
        <i class="fas fa-crown mr-2"></i> Admin Panel
      </h1>
    </div>
    <nav class="mt-4">
      <a href="?page=dashboard" class="flex items-center p-3 text-dark hover:bg-blue-50 hover:text-primary border-l-4 <?= $page == 'dashboard' ? 'border-primary bg-blue-50' : 'border-transparent' ?>">
        <i class="fas fa-tachometer-alt mr-3"></i>
        Dashboard
      </a>
      <a href="?page=sites" class="flex items-center p-3 text-dark hover:bg-blue-50 hover:text-primary border-l-4 <?= $page == 'sites' ? 'border-primary bg-blue-50' : 'border-transparent' ?>">
        <i class="fas fa-globe mr-3"></i>
        Manage Sites
      </a>
      <a href="?page=orders" class="flex items-center p-3 text-dark hover:bg-blue-50 hover:text-primary border-l-4 <?= $page == 'orders' ? 'border-primary bg-blue-50' : 'border-transparent' ?>">
        <i class="fas fa-shopping-cart mr-3"></i>
        Orders
      </a>
      <a href="?page=buyers" class="flex items-center p-3 text-dark hover:bg-blue-50 hover:text-primary border-l-4 <?= $page == 'buyers' ? 'border-primary bg-blue-50' : 'border-transparent' ?>">
        <i class="fas fa-users mr-3"></i>
        Buyers
      </a>
      <a href="?page=settings" class="flex items-center p-3 text-dark hover:bg-blue-50 hover:text-primary border-l-4 <?= $page == 'settings' ? 'border-primary bg-blue-50' : 'border-transparent' ?>">
        <i class="fas fa-cog mr-3"></i>
        Settings
      </a>
    </nav>
    <div class="absolute bottom-0 w-full p-4 border-t">
      <div class="flex items-center">
        <div class="email-image mr-3">
          <?= strtoupper(substr($userEmail, 0, 1)) ?>
        </div>
        <div>
          <p class="text-sm font-medium"><?= htmlspecialchars($userName) ?></p>
          <p class="text-xs text-gray-500">Administrator</p>
        </div>
      </div>
    </div>
  </div>

  <!-- Main Content -->
  <div class="main-content flex-1">
    <!-- Top Navigation -->
    <header class="bg-white shadow-sm py-4 px-6 flex justify-between items-center">
      <button id="sidebarToggle" class="md:hidden text-gray-500">
        <i class="fas fa-bars text-xl"></i>
      </button>
      
      <div class="flex items-center space-x-6">
        <div class="relative dropdown">
          <div class="email-image cursor-pointer" title="<?= htmlspecialchars($userEmail) ?>">
            <?= strtoupper(substr($userEmail, 0, 1)) ?>
          </div>
          
          <div class="dropdown-menu absolute right-0 mt-2 w-64 bg-white rounded-lg shadow-xl py-2 hidden z-50">
            <div class="px-4 py-3 border-b">
              <div class="flex items-center">
                <div class="email-image mr-3">
                  <?= strtoupper(substr($userEmail, 0, 1)) ?>
                </div>
                <div>
                  <p class="font-medium"><?= htmlspecialchars($userName) ?></p>
                  <p class="text-sm text-gray-500 truncate"><?= htmlspecialchars($userEmail) ?></p>
                </div>
              </div>
            </div>
            
            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50">
              <i class="fas fa-user mr-2"></i> Your Profile
            </a>
            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50">
              <i class="fas fa-cog mr-2"></i> Settings
            </a>
            
            <div class="border-t mt-2"></div>
            
            <a href="./api/logout.php" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50">
              <i class="fas fa-sign-out-alt mr-2"></i> Sign out
            </a>
          </div>
        </div>
      </div>
    </header>

    <!-- Dashboard Content -->
    <main class="p-6">
      <!-- Dashboard Page -->
      <div id="dashboard-page" class="page-content <?= $page == 'dashboard' ? 'active' : '' ?>">
        <div class="mb-6">
          <h2 class="text-2xl font-bold text-dark">SEO Link Building Dashboard</h2>
          <p class="text-gray-500">Welcome back, <?= htmlspecialchars($userName) ?>! Manage your sites and orders.</p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
          <div class="stat-card bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
              <div class="p-3 rounded-full bg-blue-100 text-primary">
                <i class="fas fa-globe text-xl"></i>
              </div>
              <div class="ml-4">
                <p class="text-gray-500">Active Sites</p>
                <h3 class="text-2xl font-bold"><?= $totalSites ?></h3>
              </div>
            </div>
          </div>
          
          <div class="stat-card bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
              <div class="p-3 rounded-full bg-green-100 text-success">
                <i class="fas fa-shopping-cart text-xl"></i>
              </div>
              <div class="ml-4">
                <p class="text-gray-500">Total Orders</p>
                <h3 class="text-2xl font-bold"><?= $totalOrders ?></h3>
              </div>
            </div>
          </div>
          
          <div class="stat-card bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
              <div class="p-3 rounded-full bg-yellow-100 text-warning">
                <i class="fas fa-clock text-xl"></i>
              </div>
              <div class="ml-4">
                <p class="text-gray-500">Pending Orders</p>
                <h3 class="text-2xl font-bold"><?= $pendingOrders ?></h3>
              </div>
            </div>
          </div>
          
          <div class="stat-card bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
              <div class="p-3 rounded-full bg-purple-100 text-secondary">
                <i class="fas fa-dollar-sign text-xl"></i>
              </div>
              <div class="ml-4">
                <p class="text-gray-500">Total Revenue</p>
                <h3 class="text-2xl font-bold">$<?= number_format($totalRevenue, 2) ?></h3>
              </div>
            </div>
          </div>
        </div>

        <!-- Two-column layout -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
          <!-- Recent Sites -->
          <div class="bg-white shadow-lg rounded-xl p-6">
            <div class="flex justify-between items-center mb-4">
              <h3 class="text-xl font-bold text-dark flex items-center">
                <i class="fas fa-globe mr-2 text-primary"></i> Recent Sites
              </h3>
              <a href="?page=sites" class="text-sm text-primary hover:underline">View All</a>
            </div>
            <div class="overflow-y-auto max-h-80">
              <?php
              if (isset($recentSites) && count($recentSites) > 0) {
                foreach ($recentSites as $site) {
                  echo '<div class="flex items-center py-3 border-b">';
                  echo '<div class="p-2 bg-blue-100 text-blue-600 rounded-lg mr-3">';
                  echo '<i class="fas fa-globe"></i>';
                  echo '</div>';
                  echo '<div class="flex-1">';
                  echo '<p class="font-medium">' . htmlspecialchars($site['site_name']) . '</p>';
                  echo '<p class="text-sm text-gray-500 truncate">' . htmlspecialchars($site['site_url']) . '</p>';
                  echo '</div>';
                  echo '<div class="text-right">';
                  echo '<span class="px-2 py-1 text-xs rounded-full site-status-active">Active</span>';
                  echo '<p class="text-xs text-gray-400 mt-1">' . date('M j, Y', strtotime($site['created_at'])) . '</p>';
                  echo '</div>';
                  echo '</div>';
                }
              } else {
                echo '<p class="text-gray-500 py-4 text-center">No sites found</p>';
              }
              ?>
            </div>
          </div>

          <!-- Recent Orders -->
          <div class="bg-white shadow-lg rounded-xl p-6">
            <div class="flex justify-between items-center mb-4">
              <h3 class="text-xl font-bold text-dark flex items-center">
                <i class="fas fa-shopping-cart mr-2 text-primary"></i> Recent Orders
              </h3>
              <a href="?page=orders" class="text-sm text-primary hover:underline">View All</a>
            </div>
            <div class="overflow-y-auto max-h-80">
              <?php
              if (isset($recentOrders) && count($recentOrders) > 0) {
                foreach ($recentOrders as $order) {
                  $statusClass = $order['status'] == 'completed' ? 'order-status-completed' : 'order-status-pending';
                  
                  echo '<div class="flex items-center py-3 border-b">';
                  echo '<div class="p-2 bg-green-100 text-green-600 rounded-lg mr-3">';
                  echo '<i class="fas fa-shopping-cart"></i>';
                  echo '</div>';
                  echo '<div class="flex-1">';
                  echo '<p class="font-medium">Order #' . $order['id'] . '</p>';
                  echo '<p class="text-sm text-gray-500">' . htmlspecialchars($order['site_name']) . '</p>';
                  echo '</div>';
                  echo '<div class="text-right">';
                  echo '<p class="font-medium">$' . number_format($order['amount'], 2) . '</p>';
                  echo '<span class="px-2 py-1 text-xs rounded-full ' . $statusClass . '">' . ucfirst($order['status']) . '</span>';
                  echo '<p class="text-xs text-gray-400 mt-1">' . date('M j, Y', strtotime($order['created_at'])) . '</p>';
                  echo '</div>';
                  echo '</div>';
                }
              } else {
                echo '<p class="text-gray-500 py-4 text-center">No orders found</p>';
              }
              ?>
            </div>
          </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white shadow-lg rounded-xl p-6 mb-8">
          <h3 class="text-xl font-bold text-dark mb-4 flex items-center">
            <i class="fas fa-bolt mr-2 text-primary"></i> Quick Actions
          </h3>
          
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="?page=sites&action=add" class="flex flex-col items-center justify-center p-4 border rounded-lg hover:bg-blue-50 transition-colors">
              <div class="p-3 bg-blue-100 text-primary rounded-full mb-2">
                <i class="fas fa-plus-circle text-xl"></i>
              </div>
              <p class="font-medium">Add New Site</p>
              <p class="text-sm text-gray-500 text-center">Add a new website to your catalog</p>
            </a>
            
            <a href="?page=orders" class="flex flex-col items-center justify-center p-4 border rounded-lg hover:bg-green-50 transition-colors">
              <div class="p-3 bg-green-100 text-success rounded-full mb-2">
                <i class="fas fa-eye text-xl"></i>
              </div>
              <p class="font-medium">View Orders</p>
              <p class="text-sm text-gray-500 text-center">Manage and process orders</p>
            </a>
            
            <a href="#" class="flex flex-col items-center justify-center p-4 border rounded-lg hover:bg-purple-50 transition-colors">
              <div class="p-3 bg-purple-100 text-secondary rounded-full mb-2">
                <i class="fas fa-chart-bar text-xl"></i>
              </div>
              <p class="font-medium">Generate Reports</p>
              <p class="text-sm text-gray-500 text-center">View sales and performance reports</p>
            </a>
          </div>
        </div>
      </div>

      <!-- Sites Page -->
      <div id="sites-page" class="page-content <?= $page == 'sites' ? 'active' : '' ?>">
        <div class="mb-6 flex justify-between items-center">
          <div>
            <h2 class="text-2xl font-bold text-dark">Manage Sites</h2>
            <p class="text-gray-500">Add, edit, and manage your websites for link building</p>
          </div>
          <a href="?page=sites&action=add" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary">
            <i class="fas fa-plus-circle mr-2"></i> Add New Site
          </a>
        </div>

        <!-- Sites Table -->
        <div class="bg-white shadow-lg rounded-xl p-6 mb-8">
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead>
                <tr>
                  <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Site Name</th>
                  <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">URL</th>
                  <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">DA</th>
                  <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                  <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                  <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <?php
                if (isset($allSites) && count($allSites) > 0) {
                  foreach ($allSites as $site) {
                    $statusClass = $site['status'] == 'active' ? 'site-status-active' : 'site-status-pending';
                    echo '<tr>';
                    echo '<td class="px-4 py-4 whitespace-nowrap">';
                    echo '<div class="flex items-center">';
                    echo '<div class="p-2 bg-blue-100 text-blue-600 rounded-lg mr-3">';
                    echo '<i class="fas fa-globe"></i>';
                    echo '</div>';
                    echo '<div class="text-sm font-medium text-gray-900">' . htmlspecialchars($site['site_name']) . '</div>';
                    echo '</div>';
                    echo '</td>';
                    echo '<td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">' . htmlspecialchars($site['site_url']) . '</td>';
                    echo '<td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">' . htmlspecialchars($site['domain_authority']) . '</td>';
                    echo '<td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">$' . number_format($site['price'], 2) . '</td>';
                    echo '<td class="px-4 py-4 whitespace-nowrap">';
                    echo '<span class="px-2 py-1 text-xs rounded-full ' . $statusClass . '">' . ucfirst($site['status']) . '</span>';
                    echo '</td>';
                    echo '<td class="px-4 py-4 whitespace-nowrap text-sm font-medium">';
                    echo '<a href="#" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>';
                    echo '<a href="#" class="text-red-600 hover:text-red-900">Delete</a>';
                    echo '</td>';
                    echo '</tr>';
                  }
                } else {
                  echo '<tr><td colspan="6" class="px-4 py-4 text-center text-gray-500">No sites found</td></tr>';
                }
                ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Orders Page -->
      <div id="orders-page" class="page-content <?= $page == 'orders' ? 'active' : '' ?>">
        <div class="mb-6">
          <h2 class="text-2xl font-bold text-dark">Order Management</h2>
          <p class="text-gray-500">View and manage all orders from buyers</p>
        </div>

        <!-- Orders Table -->
        <div class="bg-white shadow-lg rounded-xl p-6 mb-8">
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead>
                <tr>
                  <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order ID</th>
                  <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Site</th>
                  <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Buyer</th>
                  <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                  <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                  <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                  <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <?php
                if (isset($allOrders) && count($allOrders) > 0) {
                  foreach ($allOrders as $order) {
                    $statusClass = $order['status'] == 'completed' ? 'order-status-completed' : 'order-status-pending';
                    echo '<tr>';
                    echo '<td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#' . $order['id'] . '</td>';
                    echo '<td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">' . htmlspecialchars($order['site_name']) . '</td>';
                    echo '<td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">' . htmlspecialchars($order['buyer_name']) . '</td>';
                    echo '<td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">$' . number_format($order['amount'], 2) . '</td>';
                    echo '<td class="px-4 py-4 whitespace-nowrap">';
                    echo '<span class="px-2 py-1 text-xs rounded-full ' . $statusClass . '">' . ucfirst($order['status']) . '</span>';
                    echo '</td>';
                    echo '<td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">' . date('M j, Y', strtotime($order['created_at'])) . '</td>';
                    echo '<td class="px-4 py-4 whitespace-nowrap text-sm font-medium">';
                    echo '<a href="#" class="text-indigo-600 hover:text-indigo-900 mr-3">View</a>';
                    echo '<a href="#" class="text-green-600 hover:text-green-900">Process</a>';
                    echo '</td>';
                    echo '</tr>';
                  }
                } else {
                  echo '<tr><td colspan="7" class="px-4 py-4 text-center text-gray-500">No orders found</td></tr>';
                }
                ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Buyers Page -->
      <div id="buyers-page" class="page-content <?= $page == 'buyers' ? 'active' : '' ?>">
        <div class="mb-6">
          <h2 class="text-2xl font-bold text-dark">Buyer Management</h2>
          <p class="text-gray-500">Manage all buyers and their accounts</p>
        </div>

        <!-- Buyers Table -->
        <div class="bg-white shadow-lg rounded-xl p-6 mb-8">
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead>
                <tr>
                  <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                  <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                  <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Orders</th>
                  <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Spent</th>
                  <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                  <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Joined</th>
                  <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <?php
                if (isset($allBuyers) && count($allBuyers) > 0) {
                  foreach ($allBuyers as $buyer) {
                    $statusClass = $buyer['status'] == 'active' ? 'buyer-status-active' : 'buyer-status-inactive';
                    echo '<tr>';
                    echo '<td class="px-4 py-4 whitespace-nowrap">';
                    echo '<div class="flex items-center">';
                    echo '<div class="p-2 bg-green-100 text-green-600 rounded-lg mr-3">';
                    echo '<i class="fas fa-user"></i>';
                    echo '</div>';
                    echo '<div class="text-sm font-medium text-gray-900">' . htmlspecialchars($buyer['name']) . '</div>';
                    echo '</div>';
                    echo '</td>';
                    echo '<td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">' . htmlspecialchars($buyer['email']) . '</td>';
                    echo '<td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">' . rand(1, 20) . '</td>';
                    echo '<td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">$' . number_format(rand(100, 5000), 2) . '</td>';
                    echo '<td class="px-4 py-4 whitespace-nowrap">';
                    echo '<span class="px-2 py-1 text-xs rounded-full ' . $statusClass . '">' . ucfirst($buyer['status']) . '</span>';
                    echo '</td>';
                    echo '<td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">' . date('M j, Y', strtotime($buyer['created_at'])) . '</td>';
                    echo '<td class="px-4 py-4 whitespace-nowrap text-sm font-medium">';
                    echo '<a href="#" class="text-indigo-600 hover:text-indigo-900 mr-3">View</a>';
                    echo '<a href="#" class="text-red-600 hover:text-red-900">Disable</a>';
                    echo '</td>';
                    echo '</tr>';
                  }
                } else {
                  echo '<tr><td colspan="7" class="px-4 py-4 text-center text-gray-500">No buyers found</td></tr>';
                }
                ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Settings Page -->
      <div id="settings-page" class="page-content <?= $page == 'settings' ? 'active' : '' ?>">
        <div class="mb-6">
          <h2 class="text-2xl font-bold text-dark">System Settings</h2>
          <p class="text-gray-500">Configure your SEO link building platform</p>
        </div>

        <!-- Settings Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
          <!-- General Settings -->
          <div class="bg-white shadow-lg rounded-xl p-6">
            <h3 class="text-lg font-bold text-dark mb-4 flex items-center">
              <i class="fas fa-cog mr-2 text-primary"></i> General Settings
            </h3>
            <div class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Site Name</label>
                <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md" value="SEO Link Building Platform">
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Admin Email</label>
                <input type="email" class="w-full px-3 py-2 border border-gray-300 rounded-md" value="admin@seolink.example">
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Items Per Page</label>
                <select class="w-full px-3 py-2 border border-gray-300 rounded-md">
                  <option>10</option>
                  <option selected>25</option>
                  <option>50</option>
                  <option>100</option>
                </select>
              </div>
              <button class="px-4 py-2 bg-primary text-white rounded-md hover:bg-secondary">
                Save General Settings
              </button>
            </div>
          </div>

          <!-- Payment Settings -->
          <div class="bg-white shadow-lg rounded-xl p-6">
            <h3 class="text-lg font-bold text-dark mb-4 flex items-center">
              <i class="fas fa-credit-card mr-2 text-primary"></i> Payment Settings
            </h3>
            <div class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Currency</label>
                <select class="w-full px-3 py-2 border border-gray-300 rounded-md">
                  <option selected>USD ($)</option>
                  <option>EUR (€)</option>
                  <option>GBP (£)</option>
                  <option>INR (₹)</option>
                </select>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Payment Method</label>
                <div class="space-y-2">
                  <div class="flex items-center">
                    <input type="checkbox" id="paypal" class="mr-2" checked>
                    <label for="paypal">PayPal</label>
                  </div>
                  <div class="flex items-center">
                    <input type="checkbox" id="stripe" class="mr-2" checked>
                    <label for="stripe">Stripe</label>
                  </div>
                  <div class="flex items-center">
                    <input type="checkbox" id="bank-transfer" class="mr-2">
                    <label for="bank-transfer">Bank Transfer</label>
                  </div>
                </div>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Commission Rate (%)</label>
                <input type="number" class="w-full px-3 py-2 border border-gray-300 rounded-md" value="15">
              </div>
              <button class="px-4 py-2 bg-primary text-white rounded-md hover:bg-secondary">
                Save Payment Settings
              </button>
            </div>
          </div>
        </div>

        <!-- Notification Settings -->
        <div class="bg-white shadow-lg rounded-xl p-6 mb-8">
          <h3 class="text-lg font-bold text-dark mb-4 flex items-center">
            <i class="fas fa-bell mr-2 text-primary"></i> Notification Settings
          </h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <h4 class="font-medium text-gray-700 mb-3">Email Notifications</h4>
              <div class="space-y-2">
                <div class="flex items-center">
                  <input type="checkbox" id="new-order" class="mr-2" checked>
                  <label for="new-order">New Orders</label>
                </div>
                <div class="flex items-center">
                  <input type="checkbox" id="new-registration" class="mr-2" checked>
                  <label for="new-registration">New Registrations</label>
                </div>
                <div class="flex items-center">
                  <input type="checkbox" id="payment-received" class="mr-2" checked>
                  <label for="payment-received">Payments Received</label>
                </div>
              </div>
            </div>
            <div>
              <h4 class="font-medium text-gray-700 mb-3">System Alerts</h4>
              <div class="space-y-2">
                <div class="flex items-center">
                  <input type="checkbox" id="maintenance" class="mr-2" checked>
                  <label for="maintenance">System Maintenance</label>
                </div>
                <div class="flex items-center">
                  <input type="checkbox" id="updates" class="mr-2" checked>
                  <label for="updates">Software Updates</label>
                </div>
                <div class="flex items-center">
                  <input type="checkbox" id="security" class="mr-2" checked>
                  <label for="security">Security Alerts</label>
                </div>
              </div>
            </div>
          </div>
          <div class="mt-6">
            <button class="px-4 py-2 bg-primary text-white rounded-md hover:bg-secondary">
              Save Notification Settings
            </button>
          </div>
        </div>
      </div>
    </main>
  </div>

  <script>
    // Toggle sidebar on mobile
    document.getElementById('sidebarToggle').addEventListener('click', function() {
      document.querySelector('.sidebar').classList.toggle('active');
    });

    // Toggle dropdown menu
    document.querySelectorAll('.dropdown').forEach(item => {
      item.addEventListener('click', function(e) {
        if (window.innerWidth < 768) {
          const menu = this.querySelector('.dropdown-menu');
          menu.classList.toggle('hidden');
        }
      });
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
      const sidebar = document.querySelector('.sidebar');
      const sidebarToggle = document.getElementById('sidebarToggle');
      const dropdowns = document.querySelectorAll('.dropdown');
      
      if (window.innerWidth < 768 && 
          !sidebar.contains(event.target) && 
          !sidebarToggle.contains(event.target) &&
          sidebar.classList.contains('active')) {
        sidebar.classList.remove('active');
      }
      
      dropdowns.forEach(dropdown => {
        if (!dropdown.contains(event.target)) {
          const menu = dropdown.querySelector('.dropdown-menu');
          if (!menu.classList.contains('hidden') && window.innerWidth >= 768) {
            menu.classList.add('hidden');
          }
        }
      });
    });
  </script>
</body>
</html>
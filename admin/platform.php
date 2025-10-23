<?php
require_once __DIR__ . '/../includes/auth.php';

// Only allow admin users
requireRole('admin');

// Default page is dashboard if not specified
$page = $_GET['page'] ?? 'dashboard';
$file = __DIR__ . "/pages/{$page}.php";

if (!file_exists($file)) {
    $page = "404";
    $file = __DIR__ . "/pages/404.php";
}

// AJAX partial load (no layout)
if (isset($_GET['ajax'])) {
    include $file;
    exit;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Platform</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <style>
    .sidebar { width: 250px; transition: all 0.3s ease; z-index: 50; }
    .main-content { margin-left: 250px; transition: all 0.3s ease; }
    @media (max-width: 768px) {
      .sidebar { transform: translateX(-100%); position: fixed; height: 100%; top: 0; left: 0; }
      .sidebar.active { transform: translateX(0); box-shadow: 4px 0 10px rgba(0,0,0,0.1); }
      .main-content { margin-left: 0; }
      .overlay { display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0;
                 background-color: rgba(0,0,0,0.5); z-index: 40; }
      .overlay.active { display: block; }
    }
    .nav-link.active {
      background-color: #EFF6FF; color: #3B82F6; border-left: 4px solid #3B82F6;
    }
    .email-image {
      width: 40px; height: 40px; border-radius: 50%;
      background: linear-gradient(135deg, #4361ee 0%, #3a0ca3 100%);
      display: flex; align-items: center; justify-content: center;
      color: white; font-weight: bold;
    }
  </style>
</head>
<body class="bg-gray-100 min-h-screen flex">

  <!-- Mobile overlay -->
  <div class="overlay" id="overlay"></div>

  <!-- Sidebar -->
  <div class="sidebar bg-white shadow-lg fixed h-full">
    <div class="p-4 border-b flex justify-between items-center">
      <h1 class="text-xl font-bold text-blue-600 flex items-center">
        <i class="fas fa-crown mr-2"></i> <span>Admin Platform</span>
      </h1>
      <button id="closeSidebar" class="md:hidden text-gray-500">
        <i class="fas fa-times text-xl"></i>
      </button>
    </div>
    <nav class="mt-4">
      <!-- Dashboard has clean URL -->
      <a href="platform.php" data-page="dashboard" class="nav-link flex items-center p-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600">
        <i class="fas fa-tachometer-alt mr-3"></i> <span>Dashboard</span>
      </a>
      <a href="platform.php?page=sites" data-page="sites" class="nav-link flex items-center p-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600">
        <i class="fas fa-link mr-3"></i> <span>Sites</span>
      </a>
      <a href="platform.php?page=orders" data-page="orders" class="nav-link flex items-center p-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600">
        <i class="fas fa-shopping-cart mr-3"></i> <span>Orders</span>
      </a>
      <a href="platform.php?page=buyers" data-page="buyers" class="nav-link flex items-center p-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600">
        <i class="fas fa-users mr-3"></i> <span>Buyers</span>
      </a>
      <a href="platform.php?page=settings" data-page="settings" class="nav-link flex items-center p-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600">
        <i class="fas fa-cog mr-3"></i> <span>Settings</span>
      </a>
    </nav>

    <!-- User details -->
    <div class="absolute bottom-0 w-full p-4 border-t bg-white">
      <div class="flex items-center">
        <div class="email-image mr-3">
          <?= strtoupper(substr($userEmail, 0, 1)) ?>
        </div>
        <div class="flex flex-col">
          <p class="text-sm font-medium"><?= htmlspecialchars($userName) ?></p>
          <p class="text-sm font-medium"><?= htmlspecialchars($userEmail) ?></p>
          <p class="text-sm font-medium">Administrator</p>
        </div>
      </div>
    </div>
  </div>

  <!-- Main Content -->
  <div class="main-content flex-1 w-full">
    <!-- Topbar -->
    <header class="bg-white shadow-sm py-4 px-6 flex justify-between items-center">
      <button id="sidebarToggle" class="md:hidden text-gray-500">
        <i class="fas fa-bars text-xl"></i>
      </button>
      <h2 class="text-lg font-semibold text-gray-800 md:hidden">Admin Platform</h2>
      <a href="../api/logout.php" class="text-red-500 hover:underline flex items-center">
        <i class="fas fa-sign-out-alt mr-1"></i> <span class="hidden md:inline">Logout</span>
      </a>
    </header>

    <!-- Dynamic Content -->
    <main id="content" class="p-4 md:p-6">
      <?php include $file; ?>
    </main>
  </div>

  <script>
  // Handle nav clicks with AJAX + pushState
  $(document).on("click", ".nav-link", function(e) {
    e.preventDefault();
    let url = $(this).attr("href");
    let page = $(this).data("page");

    // Active class
    $(".nav-link").removeClass("active");
    $(this).addClass("active");

    // Push state (dashboard stays clean)
    if (page === "dashboard") {
      history.pushState({ page: "dashboard" }, "", "platform.php");
    } else {
      history.pushState({ page: page }, "", url);
    }

    // Loading UI
    $("#content").html(`
      <div class="bg-white rounded-lg shadow p-4 md:p-6">
        <p class="text-gray-500">Loading ${page}...</p>
      </div>
    `);

    // Load partial
    $.get(url + (url.includes("?") ? "&" : "?") + "ajax=1", function(data) {
      $("#content").html(data);
    }).fail(function() {
      $("#content").html(`<div class="bg-white rounded-lg shadow p-4 md:p-6">
        <p class="text-red-500">Error loading ${page}.</p></div>`);
    });
  });

  // Handle back/forward
  window.onpopstate = function() {
    let url = location.href;
    $.get(url + (url.includes("?") ? "&" : "?") + "ajax=1", function(data) {
      $("#content").html(data);
      let page = new URL(url).searchParams.get("page") || "dashboard";
      $(".nav-link").removeClass("active");
      $(`[data-page="${page}"]`).addClass("active");
    });
  };

  // Sidebar toggle mobile
  $(document).on("click", "#sidebarToggle", function() {
    $(".sidebar").addClass("active");
    $("#overlay").addClass("active");
  });
  $(document).on("click", "#closeSidebar, #overlay", function() {
    $(".sidebar").removeClass("active");
    $("#overlay").removeClass("active");
  });

  // Highlight nav on load
  $(document).ready(function() {
    let currentPage = new URL(location.href).searchParams.get("page") || "dashboard";
    $(".nav-link").removeClass("active");
    $(`[data-page="${currentPage}"]`).addClass("active");
  });
  </script>

<!-- Table Scripts -->
<script src="assets/js/tables.js"></script>



</body>
</html>
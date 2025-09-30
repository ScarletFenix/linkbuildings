<?php
require_once __DIR__ . '/includes/auth.php';
requireRole('buyer');

// UI Avatars (clean initials avatar)
$avatarUrl = "https://ui-avatars.com/api/?name=" . urlencode($userName) . "&background=3B82F6&color=fff&size=200&bold=true&rounded=true";
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Dashboard - LinkBuildings</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
  <script src="https://unpkg.com/lucide@latest"></script>

  <!-- Professional Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  
  <style>
    :root {
      --primary: #2563eb;
      --secondary: #10b981;
      --dark: #1f2937;
      --light: #f9fafb;
    }
    
    body {
      font-family: 'Inter', sans-serif;
    }
    
    .gradient-text {
      background: linear-gradient(135deg, #2563eb 0%, #10b981 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }
    
    .stat-card {
      transition: all 0.3s ease;
      border-left: 4px solid;
    }
    
    .stat-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
    }
    
    .feature-icon {
      width: 48px;
      height: 48px;
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 1rem;
    }
    
    .nav-shadow {
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    }
    
    .dashboard-section {
      background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    }
    
    .partner-card {
      background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
      color: white;
      border-radius: 16px;
      overflow: hidden;
    }
    
    .table-container {
      background: white;
      border-radius: 12px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
      overflow: hidden;
    }
    
    .quick-stats {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 1.5rem;
    }
    
    @media (max-width: 768px) {
      .quick-stats {
        grid-template-columns: 1fr;
      }
    }
  </style>
</head>
<body class="bg-gray-50 min-h-screen">

  <!-- Navbar -->
<nav class="bg-white nav-shadow px-6 py-4 flex items-center justify-between">
  <!-- Left: Logo -->
  <div class="flex items-center space-x-3">
    <div class="w-10 h-10 bg-gradient-to-r from-blue-600 to-green-500 rounded-lg flex items-center justify-center">
      <i data-lucide="link" class="w-6 h-6 text-white"></i>
    </div>
    <span class="text-xl font-bold gradient-text" style="font-family: 'Montserrat', sans-serif;">
      LinkBuildings
    </span>
  </div>

  <!-- Right: Cart + User Menu -->
  <div class="flex items-center space-x-6">
    <a href="#" id="open-cart"
   class="flex items-center space-x-1 text-gray-700 font-medium hover:text-blue-600 transition">
  <span>🛒</span>
  <span>Cart (<span id="cart-count"><?= isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0 ?></span>)</span>
</a>


    <!-- User Menu -->
    <div class="relative" x-data="{ open: false }">
      <button @click="open = !open" 
              class="flex items-center space-x-3 bg-blue-50 hover:bg-blue-100 px-4 py-2 rounded-lg transition-all duration-200">
        <div class="w-8 h-8 rounded-full overflow-hidden border-2 border-blue-200">
          <img src="<?= $avatarUrl ?>" alt="User Avatar" class="w-full h-full object-cover">
        </div>
        <span class="text-blue-800 font-medium hidden md:block">
          <?= htmlspecialchars(explode(' ', $userName)[0]) ?>
        </span>
        <i data-lucide="chevron-down" 
           class="w-4 h-4 text-blue-600 transition-transform duration-200"
           :class="{'rotate-180': open}"></i>
      </button>

      <!-- Dropdown -->
      <div x-show="open"
           x-transition:enter="transition ease-out duration-200"
           x-transition:enter-start="opacity-0 transform scale-95 -translate-y-2"
           x-transition:enter-end="opacity-100 transform scale-100 translate-y-0"
           x-transition:leave="transition ease-in duration-150"
           x-transition:leave-start="opacity-100 transform scale-100 translate-y-0"
           x-transition:leave-end="opacity-0 transform scale-95 -translate-y-2"
           @click.outside="open = false" 
           class="absolute right-0 mt-2 w-64 bg-white shadow-xl rounded-xl border border-gray-100 z-50 p-4 origin-top-right">

        <!-- User Info -->
        <div class="flex items-center space-x-4 pb-4 border-b border-gray-100 mb-4">
          <img src="<?= $avatarUrl ?>" alt="User Avatar" 
               class="w-12 h-12 rounded-full border-2 border-blue-200">
          <div>
            <p class="font-semibold text-gray-800 text-base"><?= htmlspecialchars ($userName) ?></p>
            <p class="text-sm text-gray-500"><?= htmlspecialchars($userEmail) ?></p>
          </div>
        </div>

        <!-- Menu Items -->
        <nav class="flex flex-col space-y-2">
          <a href="./index.php" 
             class="flex items-center space-x-3 px-3 py-2 rounded-lg hover:bg-blue-50 text-gray-700 text-base font-medium transition">
            <i data-lucide="home" class="w-5 h-5 text-blue-600"></i>
            <span>Back to Home</span>
          </a>
          <a href="./api/logout.php" 
             class="flex items-center space-x-3 px-3 py-2 rounded-lg hover:bg-red-50 text-red-600 text-base font-medium transition">
            <i data-lucide="log-out" class="w-5 h-5"></i>
            <span>Logout</span>
          </a>
        </nav>
      </div>
    </div>
  </div>
</nav>


  <!-- Dashboard Header -->
  <section class="dashboard-section py-8 px-4 sm:px-6">
    <div class="max-w-7xl mx-auto">
      <!-- Welcome Section -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Welcome back, <?= htmlspecialchars(explode(' ', $userName)[0]) ?>!</h1>
        <p class="text-gray-600">Here's your link building dashboard with all the tools you need for SEO success.</p>
      </div>

      <!-- Quick Stats -->
      <!-- <div class="quick-stats mb-8">
        <div class="stat-card bg-white p-6 rounded-xl border-l-4 border-blue-500">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-500 text-sm font-medium">Active Campaigns</p>
              <p class="text-2xl font-bold text-gray-900 mt-1">3</p>
            </div>
            <div class="feature-icon bg-blue-100 text-blue-600">
              <i data-lucide="trending-up" class="w-6 h-6"></i>
            </div>
          </div>
        </div>

        <div class="stat-card bg-white p-6 rounded-xl border-l-4 border-green-500">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-500 text-sm font-medium">Links Purchased</p>
              <p class="text-2xl font-bold text-gray-900 mt-1">12</p>
            </div>
            <div class="feature-icon bg-green-100 text-green-600">
              <i data-lucide="link" class="w-6 h-6"></i>
            </div>
          </div>
        </div>

        <div class="stat-card bg-white p-6 rounded-xl border-l-4 border-purple-500">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-500 text-sm font-medium">Total Spent</p>
              <p class="text-2xl font-bold text-gray-900 mt-1">$2,450</p>
            </div>
            <div class="feature-icon bg-purple-100 text-purple-600">
              <i data-lucide="dollar-sign" class="w-6 h-6"></i>
            </div>
          </div>
        </div>

        <div class="stat-card bg-white p-6 rounded-xl border-l-4 border-orange-500">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-500 text-sm font-medium">Avg. Domain Rating</p>
              <p class="text-2xl font-bold text-gray-900 mt-1">74</p>
            </div>
            <div class="feature-icon bg-orange-100 text-orange-600">
              <i data-lucide="star" class="w-6 h-6"></i>
            </div>
          </div>
        </div>
      </div> -->

      <!-- Main Content Grid -->
      <div class="grid lg:grid-cols-3 gap-8">
        <!-- Left Column: Platform Info -->
        <div class="lg:col-span-2">
          <div class="bg-white rounded-xl shadow-sm p-6 mb-8">
            <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
              <i data-lucide="zap" class="w-5 h-5 text-blue-600 mr-2"></i>
              Premium Link Building Platform
            </h2>
            <p class="text-gray-700 mb-6 leading-relaxed">
              Our platform offers a curated selection of high-quality domains with verified metrics, 
              ensuring you get maximum authority, relevance, and long-term SEO impact.
            </p>

            <div class="grid md:grid-cols-2 gap-6">
              <div class="flex items-start space-x-3">
                <div class="feature-icon bg-blue-50 text-blue-600">
                  <i data-lucide="eye" class="w-5 h-5"></i>
                </div>
                <div>
                  <h3 class="font-semibold text-gray-900 mb-1">Transparent Pricing</h3>
                  <p class="text-gray-600 text-sm">Competitive rates with clear value per link</p>
                </div>
              </div>

              <div class="flex items-start space-x-3">
                <div class="feature-icon bg-green-50 text-green-600">
                  <i data-lucide="shield-check" class="w-5 h-5"></i>
                </div>
                <div>
                  <h3 class="font-semibold text-gray-900 mb-1">Verified Metrics</h3>
                  <p class="text-gray-600 text-sm">Real traffic, strong domain ratings, and organic growth</p>
                </div>
              </div>

              <div class="flex items-start space-x-3">
                <div class="feature-icon bg-purple-50 text-purple-600">
                  <i data-lucide="filter" class="w-5 h-5"></i>
                </div>
                <div>
                  <h3 class="font-semibold text-gray-900 mb-1">Advanced Filtering</h3>
                  <p class="text-gray-600 text-sm">Find the perfect site with advanced search tools</p>
                </div>
              </div>

              <div class="flex items-start space-x-3">
                <div class="feature-icon bg-orange-50 text-orange-600">
                  <i data-lucide="lock" class="w-5 h-5"></i>
                </div>
                <div>
                  <h3 class="font-semibold text-gray-900 mb-1">Secure Transactions</h3>
                  <p class="text-gray-600 text-sm">Trusted marketplace with smooth and safe purchases</p>
                </div>
              </div>
            </div>

            <div class="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
              <p class="text-blue-800 text-sm">
                <span class="font-semibold">Pro Tip:</span> Use the advanced filters to find domains that match your niche and budget requirements for maximum ROI.
              </p>
            </div>
          </div>
        </div>

        <!-- Right Column: Partner Program -->
        <div class="partner-card p-6 rounded-xl">
          <div class="flex items-center mb-4">  
            <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center mr-3">
              <i data-lucide="award" class="w-5 h-5 text-white"></i>
            </div>
            <h2 class="text-xl font-bold text-white">Volume Discount Program</h2>
          </div>
          
          <p class="text-blue-100 mb-6">
            Get rewarded for scaling your link-building efforts! Our partner program 
            helps you save more as you grow your SEO campaigns.
          </p>

          <div class="space-y-3 mb-6">
            <div class="flex justify-between items-center p-3 bg-white/10 rounded-lg">
              <span class="text-white font-medium">5+ links</span>
              <span class="bg-white text-blue-600 px-2 py-1 rounded text-sm font-bold">5% OFF</span>
            </div>
            <div class="flex justify-between items-center p-3 bg-white/10 rounded-lg">
              <span class="text-white font-medium">10+ links</span>
              <span class="bg-white text-blue-600 px-2 py-1 rounded text-sm font-bold">10% OFF</span>
            </div>
            <div class="flex justify-between items-center p-3 bg-white/10 rounded-lg">
              <span class="text-white font-medium">15+ links</span>
              <span class="bg-white text-blue-600 px-2 py-1 rounded text-sm font-bold">15% OFF</span>
            </div>
            <div class="flex justify-between items-center p-3 bg-white/10 rounded-lg">
              <span class="text-white font-medium">20+ links</span>
              <span class="bg-white text-blue-600 px-2 py-1 rounded text-sm font-bold">20% OFF</span>
            </div>
            <div class="flex justify-between items-center p-3 bg-white/10 rounded-lg">
              <span class="text-white font-medium">25+ links</span>
              <span class="bg-white text-blue-600 px-2 py-1 rounded text-sm font-bold">25% OFF</span>
            </div>
          </div>

          <p class="text-white text-xs italic text-center">
          *Discounts apply automatically at checkout based on your purchase volume.*
          </p>
        </div>
      </div>
    </div>
  </section>

  <!-- Table Section -->
  <section class="py-8 px-4 sm:px-6">
    <div class="max-w-7xl mx-auto">
      <div class="table-container">
        <div class="p-6 border-b border-gray-100">
          <div class="flex flex-col md:flex-row md:items-center justify-between sticky top-0 z-50">
            <h2 class="text-xl font-bold text-gray-900 mb-4 md:mb-0">Available Domains</h2>
          </div>
        </div>
        <?php include __DIR__ . '/buyer/buyers_table.php'; ?>
      </div>
    </div>
  </section>

  <!-- Footer Section -->
  <footer class="bg-gray-900 text-white mt-12">    
    <div class="border-t border-gray-800 mt-12 pt-8 pb-8 text-center text-gray-400">
      <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <p>&copy; 2025 LinkBuildings. All rights reserved.</p>
      </div>
    </div>
  </footer>


  <!-- Cart Sidebar -->
<div id="cart-sidebar" 
     class="fixed top-0 right-0 w-[600px] h-full bg-white shadow-lg transform translate-x-full transition-transform duration-300 z-50">
  <div class="flex justify-between items-center p-4 border-b">
    <h2 class="text-lg font-bold">Your Cart</h2>
    <button id="close-cart" class="text-gray-500 hover:text-gray-800">✖</button>
  </div>
  <div id="cart-content" class="p-4 overflow-y-auto h-[calc(100%-60px)]">
    <!-- AJAX cart content will be loaded here -->
  </div>
</div>


  <script>
     document.addEventListener("click", async function(e) {
  // Open cart
  if (e.target.id === "open-cart" || e.target.closest("#open-cart")) {
    e.preventDefault();
    await renderCart();
    document.getElementById("cart-sidebar").classList.remove("translate-x-full");
  } 

  // Close cart
  if (e.target.id === "close-cart") {
    document.getElementById("cart-sidebar").classList.add("translate-x-full");
  }
});

async function renderCart() {
  const res = await fetch("/linkbuildings/cart/cart.php");
  const html = await res.text();
  document.getElementById("cart-content").innerHTML = html;
}

    lucide.createIcons();
  </script>
</body>
</html>
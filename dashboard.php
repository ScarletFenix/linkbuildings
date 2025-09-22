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
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700&family=Inter:wght@400;500&display=swap" rel="stylesheet">
  

</head>
<body class="bg-gray-50 min-h-screen" style="font-family: 'Inter', sans-serif;">

  <!-- Navbar -->
<nav class="bg-white border-b border-blue-200 shadow-md px-6 py-4 flex items-center justify-between" style="font-family: 'Inter', sans-serif;">
  <!-- Left: Home -->
  <div>
    <a href="./index.php" class="flex items-center space-x-2 text-blue-700 font-semibold hover:text-blue-900 transition">
      <i data-lucide="home" class="w-6 h-6"></i>
      <span class="hidden sm:inline text-base">Home</span>
    </a>
  </div>

  <!-- Right: User Menu -->
  <div class="relative" x-data="{ open: false }">
    <!-- Avatar Button -->
    <button @click="open = !open" 
            class="w-10 h-10 rounded-full overflow-hidden border-2 border-blue-600 hover:ring-2 hover:ring-blue-400 transition focus:outline-none focus:ring-2 focus:ring-blue-400">
      <img src="<?= $avatarUrl ?>" alt="User  Avatar" class="w-full h-full object-cover">
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
         class="absolute right-0 mt-2 w-64 bg-white shadow-lg rounded-xl border border-blue-200 z-50 p-4 origin-top-right">

      <!-- User Info -->
      <div class="flex items-center space-x-4 pb-4 border-b border-blue-100 mb-4">
        <img src="<?= $avatarUrl ?>" alt="User  Avatar" 
             class="w-12 h-12 rounded-full border border-blue-300">
        <div>
          <p class="font-semibold text-gray-800 text-base"><?= htmlspecialchars($userName) ?></p>
          <p class="text-sm text-gray-500"><?= htmlspecialchars($userEmail) ?></p>
        </div>
      </div>

      <!-- Menu Items -->
      <nav class="flex flex-col space-y-2">
        <a href="./api/logout.php" 
           class="flex items-center space-x-3 px-4 py-2 rounded-lg hover:bg-red-50 text-red-600 text-base font-medium transition">
          <i data-lucide="log-out" class="w-6 h-6"></i>
          <span>Logout</span>
        </a>
      </nav>
    </div>
  </div>
</nav>


  <!-- Hero Section -->
<section class="py-12 px-4 sm:px-6 bg-gradient-to-r from-blue-100 via-white to-blue-100">
  <div class="max-w-6xl mx-auto grid md:grid-cols-2 gap-8 items-center">
    
    <!-- Left Content -->
    <div class="text-center md:text-left">
      <h1 class="text-3xl sm:text-4xl md:text-4xl font-extrabold mb-4 bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-blue-900" style="font-family: 'Montserrat', sans-serif;">
        LINK BUILDINGS
      </h1>
      <p class="text-base sm:text-lg text-gray-700 mb-6 max-w-lg mx-auto md:mx-0 leading-relaxed">
        Our platform offers a curated selection of high-quality domains with verified metrics, 
        ensuring you get maximum authority, relevance, and long-term SEO impact.
      </p>

      <ul class="space-y-2 text-gray-800 text-sm sm:text-base max-w-md mx-auto md:mx-0">
        <li class="flex items-start space-x-2">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600 flex-shrink-0 mt-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
          </svg>
          <span>Transparent Pricing – Competitive rates with clear value per link</span>
        </li>
        <li class="flex items-start space-x-2">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600 flex-shrink-0 mt-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
          </svg>
          <span>Verified Metrics – Real traffic, strong domain ratings, and organic growth</span>
        </li>
        <li class="flex items-start space-x-2">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600 flex-shrink-0 mt-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
          </svg>
          <span>Easy Filtering – Find the perfect site with advanced search tools</span>
        </li>
        <li class="flex items-start space-x-2">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600 flex-shrink-0 mt-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
          </svg>
          <span>Secure Transactions – A trusted marketplace with smooth and safe purchases</span>
        </li>
      </ul>

      <p class="mt-6 text-gray-700 text-sm sm:text-base max-w-lg mx-auto md:mx-0 leading-relaxed">
        Discover high-quality domains tailored for SEO success. 
        LinkBuildings ensures transparency, verified authority, and seamless transactions—helping you 
        build stronger links with confidence.
      </p>
    </div>

    <!-- Right Info Card -->
    <div class="bg-blue-50 p-5 sm:p-6 rounded-xl shadow-md border border-blue-100 border-l-4 border-blue-800">
      <h2 class="text-base sm:text-lg font-bold text-gray-800 mb-3">
        Partner Program
      </h2>
      <p class="text-sm sm:text-base text-gray-600 mb-4">
        Get rewarded for scaling your link-building efforts! Our partner program 
        helps you save more as you grow your SEO campaigns.
      </p>

      <ul class="divide-y divide-gray-200 text-gray-700 mb-4 text-sm sm:text-base">
        <li class="py-2"><span class="font-semibold">5+ links:</span> 5% discount</li>
        <li class="py-2"><span class="font-semibold">10+ links:</span> 10% discount</li>
        <li class="py-2"><span class="font-semibold">15+ links:</span> 15% discount</li>
        <li class="py-2"><span class="font-semibold">20+ links:</span> 20% discount</li>
        <li class="py-2"><span class="font-semibold">25+ links:</span> 25% discount</li>
      </ul>

      <p class="text-xs sm:text-sm text-gray-500 italic">
        Discounts apply automatically at checkout.
      </p>
    </div>
  </div>
</section>



  <!-- Table Section -->
  <?php include __DIR__ . '/buyer/buyers_table.php'; ?>
  
  
    

  <!-- Footer Section -->
    <footer class="bg-gray-900 text-white mt-12">    
            <div class="border-t border-gray-800 mt-12 pt-8 pb-8 text-center text-gray-400">
                <p>&copy; 2025 LinkBuildings. All rights reserved.</p>
            </div>
        </div>
    </footer>
    


  <script>
    
    lucide.createIcons();
  </script>
</body>
</html>

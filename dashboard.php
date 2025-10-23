<?php
require_once __DIR__ . '/includes/auth.php';
requireRole('buyer');

$avatarUrl = "https://ui-avatars.com/api/?name=" . urlencode($userName) . "&background=3B82F6&color=fff&size=200&bold=true&rounded=true";
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Buyer Dashboard - LinkBuildings</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
  <script src="https://unpkg.com/lucide@latest"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Montserrat:wght@700&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Inter', sans-serif;
      background-color: #f8fafc;
      color: #1f2937;
      overflow-x: hidden;
    }
    .sidebar {
      width: 260px;
      background: linear-gradient(180deg, #1e3a8a, #1e40af);
      color: white;
      transition: transform 0.3s ease;
    }
    .sidebar a {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 10px 16px;
      border-radius: 8px;
      transition: all 0.2s ease;
    }
    .sidebar a:hover {
      background-color: rgba(255, 255, 255, 0.15);
    }
    .active-link {
      background-color: rgba(255, 255, 255, 0.2);
    }
    @media (max-width: 1024px) {
      .sidebar {
        position: fixed;
        top: 0;
        left: -260px;
        height: 100vh;
        z-index: 50;
      }
      .sidebar.open {
        transform: translateX(260px);
      }
    }
    @keyframes pulse-dot {
      0% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.6), 0 0 0 0 rgba(239, 68, 68, 0.3); }
      40% { box-shadow: 0 0 0 4px rgba(239, 68, 68, 0), 0 0 0 8px rgba(239, 68, 68, 0.1); }
      70% { box-shadow: 0 0 0 6px rgba(239, 68, 68, 0), 0 0 0 10px rgba(239, 68, 68, 0); }
      100% { box-shadow: 0 0 0 8px rgba(239, 68, 68, 0), 0 0 0 12px rgba(239, 68, 68, 0); }
    }
    .animate-pulse-dot {
      animation: pulse-dot 2s ease-in-out infinite;
    }
    .logout-link {
      text-decoration: none !important;
    }
    .logout-link:hover {
      opacity: 0.9;
      text-decoration: none !important;
    }
  </style>
</head>
<body x-data="{ sidebarOpen: false }" class="flex min-h-screen">

  <!-- Sidebar -->
  <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-64'"
         class="sidebar fixed left-0 top-0 h-full flex flex-col justify-between p-6 z-40 transform lg:translate-x-0">

    <div>
      <div class="flex items-center justify-between mb-8">
        <div class="flex items-center space-x-3">
          <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center">
            <i data-lucide="link" class="w-6 h-6 text-blue-700"></i>
          </div>
          <h1 class="font-bold text-lg text-white" style="font-family:'Montserrat'">LinkBuildings</h1>
        </div>
        <button @click="sidebarOpen = false" class="text-white lg:hidden">
          <i data-lucide="x" class="w-6 h-6"></i>
        </button>
      </div>

      <nav class="flex flex-col space-y-2 text-sm cursor-pointer">
        <a data-page="dashboard" class="active-link"><i data-lucide="layout-dashboard" class="w-4 h-4"></i> Dashboard</a>
        <a data-page="browse-sites"><i data-lucide="globe" class="w-4 h-4"></i> Browse Sites</a>
        <a data-page="orders"><i data-lucide="package" class="w-4 h-4"></i> My Orders</a>
      </nav>
    </div>

    <div class="border-t border-blue-300/40 pt-4 mt-6">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-full border-2 border-blue-200 overflow-hidden">
          <img src="<?= $avatarUrl ?>" alt="User Avatar" class="w-full h-full object-cover">
        </div>
        <div class="flex flex-col">
          <p class="font-semibold text-white text-sm"><?= htmlspecialchars($userName) ?></p>
          <p class="text-blue-200 text-xs truncate max-w-[160px]"><?= htmlspecialchars($userEmail) ?></p>
        </div>
      </div>
      <div class="mt-4 cursor-pointer">
        <a id="logout-btn" class="flex items-center gap-2 mt-3 text-red-200 hover:text-red-100 text-sm logout-link">
          <i data-lucide="log-out" class="w-4 h-4"></i> Logout
        </a>
      </div>
    </div>
  </aside>

  <!-- Overlay -->
  <div x-show="sidebarOpen" @click="sidebarOpen = false"
       class="fixed inset-0 bg-black/40 z-30 lg:hidden"
       x-transition.opacity></div>

  <!-- Main -->
  <div class="flex-1 flex flex-col min-h-screen transition-all duration-300 lg:ml-[260px] bg-gray-50">

    <header class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between shadow-sm sticky top-0 z-30">
      <div class="flex items-center gap-3">
        <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden text-gray-700">
          <i data-lucide="menu" class="w-6 h-6"></i>
        </button>
        <h2 class="text-xl font-semibold text-gray-800">Buyer Dashboard</h2>
      </div>

      <div class="relative" x-data="{ open: false, hasUnread: true }">
        <button @click="open = !open"
          class="relative bg-blue-50 p-2 rounded-full hover:bg-blue-100 transition">
          <i data-lucide="bell" class="w-5 h-5 text-blue-600"></i>
          <template x-if="hasUnread">
            <span class="absolute top-0 right-0 block w-2.5 h-2.5">
              <span class="absolute inset-0 bg-red-500 rounded-full animate-pulse-dot"></span>
            </span>
          </template>
        </button>

        <div x-show="open" @click.outside="open = false"
             class="absolute right-0 mt-3 w-80 bg-white rounded-xl shadow-lg border border-gray-100 z-50">
          <div class="p-4 border-b border-gray-100 flex justify-between items-center">
            <h4 class="font-semibold text-gray-800">Notifications</h4>
            <button class="text-sm text-blue-600 hover:underline" @click="hasUnread = false">Mark all read</button>
          </div>
          <ul class="divide-y divide-gray-100 max-h-80 overflow-y-auto">
            <li class="p-4 hover:bg-gray-50">
              <div class="flex items-start gap-3">
                <i data-lucide="check-circle" class="w-5 h-5 text-green-500 mt-0.5"></i>
                <div>
                  <p class="text-sm font-medium text-gray-800">Order #145 completed successfully.</p>
                  <p class="text-xs text-gray-500 mt-1">2 hours ago</p>
                </div>
              </div>
            </li>
          </ul>
        </div>
      </div>
    </header>

    <main id="main-content" class="p-6 space-y-8 relative z-10">
      <div class="flex justify-center items-center min-h-[200px]">
        <p class="text-gray-500">Loading dashboard...</p>
      </div>
    </main>

    <footer class="text-center text-gray-500 text-sm py-4 border-t border-gray-200 mt-auto">
      © <?= date('Y') ?> LinkBuildings. All Rights Reserved.
    </footer>
  </div>

<script>
lucide.createIcons();

document.addEventListener('DOMContentLoaded', () => {
  const mainContent = document.getElementById('main-content');
  const navLinks = document.querySelectorAll('.sidebar nav a');
  const defaultPage = 'dashboard';

  async function loadPage(page, push = true) {
    mainContent.innerHTML = `
      <div class="flex justify-center items-center min-h-[200px]">
        <svg class="animate-spin h-6 w-6 text-blue-500 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
        </svg>
        <span class="text-gray-500">Loading ${page}...</span>
      </div>
    `;

    try {
      const res = await fetch(`./buyer/pages/${page}.php?ajax=1`, { cache: 'no-store' });
      if (!res.ok) throw new Error(`HTTP ${res.status}`);
      const html = await res.text();

      // Parse HTML and extract scripts
      const temp = document.createElement('div');
      temp.innerHTML = html;
      const scripts = Array.from(temp.querySelectorAll('script'));
      scripts.forEach(s => s.remove());

      mainContent.innerHTML = temp.innerHTML;
      lucide.createIcons();

      // Execute scripts properly
      scripts.forEach(oldScript => {
        const newScript = document.createElement('script');
        if (oldScript.src) {
          const base = window.location.pathname.split('/').slice(0, -1).join('/');
          newScript.src = new URL(oldScript.getAttribute('src'), `${window.location.origin}${base}/`).href;
        } else {
          newScript.textContent = oldScript.textContent;
        }
        document.body.appendChild(newScript);
        newScript.remove();
      });

      // Sidebar active link
      navLinks.forEach(l => l.classList.remove('active-link'));
      const activeLink = document.querySelector(`[data-page="${page}"]`);
      if (activeLink) activeLink.classList.add('active-link');

      // URL update
      if (push) {
        if (page === defaultPage) {
          history.pushState({ page }, '', 'dashboard.php');
        } else {
          history.pushState({ page }, '', `dashboard.php?page=${page}`);
        }
      }
    } catch (err) {
      mainContent.innerHTML = `
        <div class="p-6 text-center text-red-500">
          <p class="font-semibold">Error loading page</p>
          <p class="text-sm">${err.message}</p>
        </div>`;
    }
  }

  navLinks.forEach(link => {
    link.addEventListener('click', e => {
      e.preventDefault();
      const page = link.dataset.page;
      loadPage(page);
    });
  });

  window.addEventListener('popstate', event => {
    const page = event.state?.page || defaultPage;
    loadPage(page, false);
  });

  const urlParams = new URLSearchParams(window.location.search);
  const startPage = urlParams.get('page') || defaultPage;
  loadPage(startPage, false);

  // Logout handler
  const logoutBtn = document.getElementById('logout-btn');
  logoutBtn.addEventListener('click', async e => {
    e.preventDefault();
    try {
      const res = await fetch('./api/logout.php');
      if (res.ok) window.location.href = './login.php';
    } catch (err) {
      console.error('Logout failed:', err);
    }
  });
});
</script>

</body>
</html>

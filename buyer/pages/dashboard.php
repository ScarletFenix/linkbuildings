<?php
require_once __DIR__ . '/../../includes/auth.php';
requireRole('buyer');

$userName = $_SESSION['user_name'] ?? 'Buyer';
?>

<div class="space-y-6">
  <!-- 👋 Greeting Section -->
  <section class="bg-gradient-to-r from-blue-600 to-blue-800 text-white rounded-2xl p-6 shadow-md flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
      <h2 class="text-2xl font-semibold mb-1">Welcome back, <?= htmlspecialchars($userName) ?> 👋</h2>
      <p class="text-blue-100 text-sm">Here’s what’s happening with your account today.</p>
    </div>
  </section>

  <!-- 📊 Stats Grid -->
  <section class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6" id="statsGrid">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
      <div class="p-3 bg-blue-100 text-blue-600 rounded-lg"><i data-lucide="package" class="w-6 h-6"></i></div>
      <div>
        <p class="text-sm text-gray-500">Total Orders</p>
        <p id="totalOrders" class="text-2xl font-semibold text-gray-800">0</p>
      </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
      <div class="p-3 bg-green-100 text-green-600 rounded-lg"><i data-lucide="check-circle" class="w-6 h-6"></i></div>
      <div>
        <p class="text-sm text-gray-500">Completed</p>
        <p id="completedOrders" class="text-2xl font-semibold text-gray-800">0</p>
      </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
      <div class="p-3 bg-yellow-100 text-yellow-600 rounded-lg"><i data-lucide="clock" class="w-6 h-6"></i></div>
      <div>
        <p class="text-sm text-gray-500">In Progress</p>
        <p id="inProgressOrders" class="text-2xl font-semibold text-gray-800">0</p>
      </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
      <div class="p-3 bg-red-100 text-red-600 rounded-lg"><i data-lucide="x-circle" class="w-6 h-6"></i></div>
      <div>
        <p class="text-sm text-gray-500">Cancelled</p>
        <p id="cancelledOrders" class="text-2xl font-semibold text-gray-800">0</p>
      </div>
    </div>
  </section>

  <!-- 🧱 Info & Consulting Section -->
  <section class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- 💡 About Link Building -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col justify-center items-center text-center space-y-4">
      <div class="max-w-md">
        <h3 class="text-xl font-semibold text-gray-800 mb-2">About Link Building</h3>
        <p class="text-gray-600 leading-relaxed">
          Link building is one of the most crucial aspects of SEO — it strengthens your website’s authority and improves visibility across search engines.
          Our platform connects you with verified publishers, ensuring every link adds real value to your digital presence.
        </p>
        <ul class="space-y-2 text-gray-600 list-disc list-inside text-left mt-4">
          <li>High-quality backlinks from trusted domains</li>
          <li>Transparent order tracking and reporting</li>
          <li>Tailored strategies for your niche</li>
        </ul>
        <div class="mt-6">
          <a href="#" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition">
            Learn More
            <i data-lucide="arrow-right" class="ml-2 w-4 h-4"></i>
          </a>
        </div>
      </div>
    </div>

    <!-- 💬 Consulting with Henry -->
<div class="bg-gradient-to-br from-pink-50 to-white border border-gray-100 rounded-xl shadow-sm p-6 flex flex-col justify-center items-center text-center space-y-4">
  <img src="https://i.pravatar.cc/100?img=12" alt="Henry" class="w-24 h-24 rounded-full object-cover border-4 border-white shadow-md">
  <div class="max-w-md">
    <h3 class="text-lg font-semibold text-gray-800">Consulting with <span class="text-pink-600">Henry</span></h3>
    <p class="text-gray-600 text-sm mb-4">
      SEO strategist with 8+ years of experience in outreach and authority link campaigns.
    </p>
    <p class="text-gray-600 leading-relaxed">
      Book a Google Meet session with Henry to discuss your brand’s growth strategy, backlink opportunities, and SEO campaign improvements.
    </p>
    <div class="mt-6">
      <a href="https://meet.google.com/" target="_blank" class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition">
        <i data-lucide="phone" class="w-4 h-4 mr-2"></i> Call with Google Meet
      </a>
    </div>
  </div>
</div>

  </section>

  <!-- 🎥 How Our Platform Works -->
  <section class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
  <h3 class="text-lg font-semibold text-gray-800 mb-6 text-center">
    How Our Platform Works
  </h3>

  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
    <!-- 🎥 Responsive 16:9 Video Placeholders -->
    <div class="bg-gray-100 rounded-lg aspect-video flex flex-col items-center justify-center text-gray-400 hover:bg-gray-200 transition">
      <span>Video Placeholder</span>
    </div>

    <div class="bg-gray-100 rounded-lg aspect-video flex flex-col items-center justify-center text-gray-400 hover:bg-gray-200 transition">
      <span>Video Placeholder</span>
    </div>

    <div class="bg-gray-100 rounded-lg aspect-video flex flex-col items-center justify-center text-gray-400 hover:bg-gray-200 transition">
      <span>Video Placeholder</span>
    </div>
  </div>
</section>


</div>

<!-- ✅ Keep dashboard JS intact -->
<script src="<?= rtrim($_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'], '/') ?>/linkbuildings/buyer/assets/js/dashboard.js"></script>

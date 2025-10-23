<?php 
require_once __DIR__ . '/../../includes/auth.php'; 
requireRole('buyer'); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Buyer Dashboard</title>
  <link rel="stylesheet" href="/linkbuildings/assets/styles.css">
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    .capsule {
      display: inline-block;
      background-color: #e0f2fe;
      color: #0369a1;
      padding: 4px 10px;
      margin: 3px;
      border-radius: 9999px;
      font-size: 0.75rem;
      font-weight: 600;
    }
    .expandable {
      max-height: 0;
      overflow: hidden;
      transition: max-height 0.4s ease, opacity 0.4s ease, padding 0.3s ease;
      opacity: 0;
    }
    .expandable.show {
      max-height: 1200px;
      opacity: 1;
      padding: 1rem 0;
    } 
    thead th {
      height: 60px;
      vertical-align: middle;
    }
  </style>
</head>
<body class="p-6 bg-gray-50">

<div class="max-w-7xl mx-auto">

  <!-- ✅ Filter Section -->
  <div class="mb-6 bg-white p-4 rounded-lg shadow border border-gray-200">
    <h2 class="text-lg font-semibold mb-3 text-gray-800">Filters</h2>
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 text-sm">
      <input id="filter-niche" type="text" placeholder="Niche" class="border p-2 rounded w-full">
      <input id="filter-country" type="text" placeholder="Country" class="border p-2 rounded w-full">
      <input id="filter-min-dr" type="number" placeholder="Min DR" class="border p-2 rounded w-full">
      <input id="filter-max-dr" type="number" placeholder="Max DR" class="border p-2 rounded w-full">
      <input id="filter-min-traffic" type="number" placeholder="Min Traffic" class="border p-2 rounded w-full">
      <input id="filter-max-traffic" type="number" placeholder="Max Traffic" class="border p-2 rounded w-full">
      <input id="filter-search" type="text" placeholder="Search..." class="border p-2 rounded w-full col-span-2 md:col-span-3 lg:col-span-6">
    </div>
    <div class="mt-4 flex justify-end gap-2">
      <button id="apply-filters" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm">Apply Filters</button>
      <button id="reset-filters" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded text-sm">Reset</button>
    </div>
  </div>

  <!-- Table -->
  <div class="overflow-x-auto rounded-lg border border-gray-200 max-h-[800px] overflow-y-auto">
  <table class="w-full text-xs sm:text-sm text-gray-800 text-center">
    <thead class="bg-gradient-to-r from-blue-600 to-green-500 text-white uppercase tracking-wider sticky top-0 z-50 shadow">
      <tr>
        <th class="px-3 py-3">Niche</th>
        <th class="px-3 py-3">Discount</th>
        <th class="px-3 py-3">Site</th>
        <th class="px-3 py-3">Country</th>
        <th class="px-3 py-3">DR</th>
        <th class="px-3 py-3">Traffic</th>
        <th class="px-3 py-3">Backlinks</th>
        <th class="px-3 py-3">Price</th>
        <th class="px-3 py-3">Action</th>
      </tr>
    </thead>
    <tbody id="tableBody" class="bg-white divide-y divide-gray-200"></tbody>
  </table>
</div>

  <!-- Pagination -->
  <div id="pagination" class="flex justify-center mt-4 space-x-1 text-sm"></div>
</div>

<!-- <script>  </script> -->

<!-- <script src="../assets/js/sites-table.js"></script> -->
 <script src="<?= rtrim($_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'], '/') ?>/linkbuildings/buyer/assets/js/sites-table.js"></script>

</body>
</html>

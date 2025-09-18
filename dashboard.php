<?php
require_once __DIR__ . '/includes/auth.php';
requireRole('buyer');
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Dashboard - LinkBuildings</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 p-8">
  <div class="max-w-3xl mx-auto bg-white p-6 rounded shadow">
    <h1 class="text-2xl font-bold text-blue-600 mb-4">
      Welcome, <?= htmlspecialchars($userName) ?>!
    </h1>
    <p class="mb-4">Your ID: <?= htmlspecialchars($userId) ?></p>
    <p class="mb-4">Your Email: <?= htmlspecialchars($userEmail) ?></p>
    <p class="mb-4">This is your secure dashboard.</p>

    <div class="flex space-x-4">
      <a href="./api/logout.php" 
         class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition">
         Logout
      </a>
      <a href="./index.php" 
         class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition">
         Home
      </a>
    </div>
  </div>
</body>
</html>

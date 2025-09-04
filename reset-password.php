<?php
require_once __DIR__ . '/includes/db.php';
$config = require __DIR__ . '/includes/.env.php';

// Validate token
$token = $_GET['token'] ?? '';
if (!$token) {
    header("Location: /linkbuildings/login.php?error=invalid_token");
    exit;
}

$stmt = $pdo->prepare("SELECT email, expires_at FROM password_resets WHERE token = ?");
$stmt->execute([$token]);
$reset = $stmt->fetch();

if (!$reset || strtotime($reset['expires_at']) < time()) {
    header("Location: /linkbuildings/login.php?error=expired_token");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Reset Password - linkbuildings</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-blue-50 to-blue-100 flex items-center justify-center min-h-screen">
  <div class="w-full max-w-md bg-white rounded-2xl shadow-xl p-8">
    <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Reset Password</h2>

    <!-- Flash Message -->
    <?php if (isset($_GET['status']) && $_GET['status'] === 'error'): ?>
      <div class="mb-4 bg-red-500 text-white text-sm font-semibold text-center py-3 px-4 rounded-lg">
        ❌ Passwords do not match. Try again.
      </div>
    <?php endif; ?>

    <form action="/linkbuildings/api/update-password.php" method="POST" class="space-y-5">
      <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

      <!-- New Password -->
      <div>
        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
        <input type="password" id="password" name="password" required minlength="6"
               class="w-full px-4 py-2 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
      </div>

      <!-- Confirm Password -->
      <div>
        <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
        <input type="password" id="confirm_password" name="confirm_password" required minlength="6"
               class="w-full px-4 py-2 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
      </div>

      <!-- Submit -->
      <div>
        <button type="submit"
                class="w-full py-2 px-4 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition duration-200 shadow-md">
          Update Password
        </button>
      </div>
    </form>
  </div>
</body>
</html>

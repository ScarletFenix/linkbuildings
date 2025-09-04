<?php
$config = require __DIR__ . '/includes/.env.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Forgot Password - linkbuildings</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-blue-50 to-blue-100 flex items-center justify-center min-h-screen">
  <div class="w-full max-w-md bg-white rounded-2xl shadow-xl p-8">
    <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Forgot Password</h2>

    <!-- Flash Message -->
    <?php if (isset($_GET['status'])): ?>
      <?php if ($_GET['status'] === 'sent'): ?>
        <div class="mb-4 bg-green-500 text-white text-sm font-semibold text-center py-3 px-4 rounded-lg">
          ✅ Password reset link sent! Check your email.
        </div>
      <?php elseif ($_GET['status'] === 'error'): ?>
        <div class="mb-4 bg-red-500 text-white text-sm font-semibold text-center py-3 px-4 rounded-lg">
          ❌ Something went wrong. Please try again.
        </div>
      <?php elseif ($_GET['status'] === 'missing'): ?>
        <div class="mb-4 bg-yellow-500 text-white text-sm font-semibold text-center py-3 px-4 rounded-lg">
          ⚠️ Please enter your email address.
        </div>
      <?php endif; ?>
    <?php endif; ?>

    <form id="forgotForm" action="/linkbuildings/api/send-reset.php" method="POST" class="space-y-5" novalidate>
      <!-- Email -->
      <div>
        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Enter your registered Email</label>
        <input type="email" id="email" name="email" required
               class="w-full px-4 py-2 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
      </div>

      <!-- reCAPTCHA -->
      <div class="mb-3">
        <div class="g-recaptcha" data-sitekey="<?= $config['GOOGLE_RECAPTCHA_SITE_KEY'] ?>"></div>
      </div>

      <!-- Submit -->
      <div>
        <button type="submit"
                class="w-full py-2 px-4 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition duration-200 shadow-md">
          Send Reset Link
        </button>
      </div>

      <!-- Back to Login -->
      <p class="text-sm text-center text-gray-600 mt-4">
        Remembered your password?
        <a href="./login.php" class="text-blue-600 hover:underline font-medium">Back to Login</a>
      </p>
    </form>
  </div>

  <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</body>
</html>

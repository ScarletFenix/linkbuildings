<?php
$config = require __DIR__ . '/includes/.env.php'; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Login - linkbuildings</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-blue-50 to-blue-100 flex items-center justify-center min-h-screen">
  <div class="w-full max-w-4xl flex bg-white rounded-2xl shadow-xl overflow-hidden">

    <!-- Left: Image -->
    <div class="hidden md:block w-2/5">
      <img src="/linkbuildings/assets/img/login_img.jpg" alt="Login Illustration" class="w-full h-full object-cover">
    </div>

    <!-- Right: Form -->
    <div class="w-full md:w-3/5 p-8">
      <h2 class="text-3xl font-bold text-center text-gray-800 mb-6">Login to linkbuildings</h2>

      <div id="flashMessage" class="hidden mb-4 text-white text-sm font-semibold text-center py-3 px-4 rounded-lg transition-all duration-300"></div>

      <form id="loginForm" action="./api/login.php" method="POST" class="space-y-5" novalidate>

        <!-- Email -->
        <div>
          <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
          <input type="email" id="email" name="email" required
                 class="w-full px-4 py-2 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
          <p class="text-xs text-red-500 mt-1 hidden" id="emailError">Enter a valid email address.</p>
        </div>

        <!-- Password -->
        <div>
          <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
          <div class="relative">
            <input type="password" id="password" name="password" required minlength="6"
                   class="w-full px-4 py-2 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 pr-12" />
            <button type="button" id="togglePassword" class="absolute inset-y-0 right-3 flex items-center text-gray-500">
              👁️
            </button>
          </div>
          <p class="text-xs text-red-500 mt-1 hidden" id="passwordError">Password must be at least 6 characters.</p>
        </div>

        <!-- Forgot Password -->
        <div class="text-right">
          <a href="/linkbuildings/forgot-password.php" class="text-sm text-blue-600 hover:underline">Forgot Password?</a>
        </div>

        <!-- reCAPTCHA -->
        <div class="mb-3">
          <div class="g-recaptcha" data-sitekey="<?= $config['GOOGLE_RECAPTCHA_SITE_KEY'] ?>"></div>
        </div>

        <!-- Submit -->
        <div>
          <button type="submit"
                  class="w-full py-2 px-4 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition duration-200 shadow-md">
            Login
          </button>
        </div>

        <!-- Google Login -->
        <div class="mt-3">
          <a href="/linkbuildings/api/google_login.php"
             class="w-full flex items-center justify-center gap-2 py-2 px-4 bg-gray-500 text-white font-semibold rounded-lg hover:bg-gray-600 transition duration-200 shadow-md">
            <img src="https://www.svgrepo.com/show/475656/google-color.svg" alt="Google" class="w-5 h-5">
            Login with Google
          </a>
        </div>

        <p class="text-sm text-center text-gray-600 mt-4">
          Don’t have an account?
          <a href="./register.php" class="text-blue-600 hover:underline font-medium">Register here</a>.
        </p>

        <!-- Back to Home -->
        <p class="text-sm text-center text-gray-600 mt-4">
          <a href="/linkbuildings" class="text-blue-600 hover:underline font-medium"> <-- Back to Home</a>
        </p>
      </form>
    </div>
  </div>

  <script src="https://www.google.com/recaptcha/api.js" async defer></script>

  <script>
    const form = document.getElementById('loginForm');
    const flash = document.getElementById('flashMessage');
    const emailField = document.getElementById('email');
    const passwordField = document.getElementById('password');
    const togglePassword = document.getElementById('togglePassword');
    const emailError = document.getElementById('emailError');
    const passwordError = document.getElementById('passwordError');

    // Show/hide password
    togglePassword.addEventListener('click', () => {
      const type = passwordField.type === 'password' ? 'text' : 'password';
      passwordField.type = type;
      togglePassword.textContent = type === 'password' ? '👁️' : '🙈';
    });

    // Client-side validation
    form.addEventListener('submit', (e) => {
      e.preventDefault();
      let valid = true;

      const email = emailField.value.trim();
      const password = passwordField.value;

      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

      if (!emailRegex.test(email)) {
        emailError.classList.remove('hidden');
        valid = false;
      } else {
        emailError.classList.add('hidden');
      }

      if (password.length < 6) {
        passwordError.classList.remove('hidden');
        valid = false;
      } else {
        passwordError.classList.add('hidden');
      }

      if (!valid) {
        flash.textContent = 'Please correct the highlighted errors.';
        flash.classList.remove('hidden', 'bg-green-500');
        flash.classList.add('bg-red-500');
        return;
      }

      // Submit
      flash.classList.remove('hidden', 'bg-red-500');
      flash.classList.add('bg-blue-500');
      flash.textContent = 'Logging in...';
      setTimeout(() => form.submit(), 800);
    });

    // Handle error messages from PHP
    const params = new URLSearchParams(window.location.search);
    if (params.has('error')) {
      flash.classList.remove('hidden');

      const type = params.get('error');
      if (type === 'invalid') {
        flash.classList.add('bg-red-500');
        flash.textContent = 'Invalid email or password.';
      } else if (type === 'rate_limit') {
        flash.classList.add('bg-yellow-500');
        flash.textContent = 'Too many failed attempts. Please wait 10 minutes.';
      } else if (type === 'captcha') {
        flash.classList.add('bg-red-500');
        flash.textContent = 'Please complete the CAPTCHA.';
      }
    }
  </script>
</body>
</html>

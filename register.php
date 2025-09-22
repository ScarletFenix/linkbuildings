<?php
$config = require __DIR__ . '/includes/.env.php'; // Ensure .env.php returns GOOGLE_RECAPTCHA_SITE_KEY
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Register</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body class="bg-gradient-to-br from-blue-50 to-blue-100 flex items-center justify-center min-h-screen">
  <div class="w-full max-w-4xl flex bg-white rounded-2xl shadow-xl overflow-hidden">
    
    <!-- Image Section -->
    <div class="hidden md:block w-2/5">
      <img src="/linkbuildings/assets/img/register_img.jpg" alt="Registration Illustration" class="w-full h-full object-cover">
    </div>
    
    <!-- Form Section -->
    <div class="w-full md:w-3/5 p-8">
      <h2 class="text-3xl font-bold text-center text-gray-800 mb-6">Create an Account</h2>

      <div id="flashMessage" class="hidden mb-4 text-white text-sm font-semibold text-center py-3 px-4 rounded-lg transition-all duration-300"></div>

      <form id="registerForm" action="/linkbuildings/api/register.php" method="POST" class="space-y-5" novalidate>
        <!-- Username -->
        <div>
          <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
          <input type="text" id="name" name="name" maxlength="30" required
                 class="w-full px-4 py-2 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
          <p class="text-xs text-red-500 mt-1 hidden" id="nameError">Username must be 3–30 characters, letters/numbers/underscores only.</p>
        </div>

        <!-- Email -->
        <div>
          <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
          <input type="email" id="email" name="email" required
                 class="w-full px-4 py-2 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
          <p class="text-xs text-red-500 mt-1 hidden" id="emailError">Enter a valid email address.</p>
          <p class="text-xs text-red-500 mt-1 hidden" id="emailTaken">This email is already registered.</p>
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

        <!-- Terms & Services -->
        <div class="flex items-center">
          <input type="checkbox" id="terms" name="terms" class="mr-2" required>
          <label for="terms" class="text-sm text-gray-700">I agree to the <a href="/linkbuildings/terms.php" class="text-blue-600 hover:underline">Terms and Services</a>.</label>
        </div>

        <!-- reCAPTCHA -->
        <div class="mb-3">
          <div class="g-recaptcha" data-sitekey="<?= $config['GOOGLE_RECAPTCHA_SITE_KEY'] ?>"></div>
        </div>

        <!-- Submit -->
        <div>
          <button type="submit"
                  class="w-full py-2 px-4 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition duration-200 shadow-md">
            Register
          </button>
        </div>

        <!-- Google Login -->
        <div class="mt-3">
          <a href="/linkbuildings/api/google_login.php"
             class="w-full flex items-center justify-center gap-2 py-2 px-4 bg-gray-500 text-white font-semibold rounded-lg hover:bg-gray-600 transition duration-200 shadow-md">
            <img src="https://www.svgrepo.com/show/475656/google-color.svg" alt="Google" class="w-5 h-5">
            Continue with Google
          </a>
        </div>

        <p class="text-sm text-center text-gray-600 mt-4">
          Already have an account?
          <a href="/linkbuildings/login" class="text-blue-600 hover:underline font-medium">Login here</a>.
        </p>
        <!-- Back to Home -->
        <p class="text-sm text-center text-gray-600 mt-4">
          <a href="/linkbuildings" class="text-blue-600 hover:underline font-medium"><-- Back to Home</a>
        </p>
      </form>
    </div>
  </div>

  <script>
    const form = document.getElementById('registerForm');
    const flash = document.getElementById('flashMessage');

    const nameField = document.getElementById('name');
    const emailField = document.getElementById('email');
    const passwordField = document.getElementById('password');
    const togglePassword = document.getElementById('togglePassword');
    const termsCheckbox = document.getElementById('terms');

    const nameError = document.getElementById('nameError');
    const emailError = document.getElementById('emailError');
    const emailTaken = document.getElementById('emailTaken');
    const passwordError = document.getElementById('passwordError');

    // Show/hide password
    togglePassword.addEventListener('click', () => {
      const type = passwordField.type === 'password' ? 'text' : 'password';
      passwordField.type = type;
      togglePassword.textContent = type === 'password' ? '👁️' : '🙈';
    });

    // Sanitize input
    const sanitize = (str) => str.replace(/[<>\/"'`;(){}]/g, '');

    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      let valid = true;

      const username = sanitize(nameField.value.trim());
      const email = sanitize(emailField.value.trim());
      const password = passwordField.value;

      // Validate username
      const usernameRegex = /^[a-zA-Z0-9_]{3,30}$/;
      if (!usernameRegex.test(username)) {
        nameError.classList.remove('hidden');
        valid = false;
      } else {
        nameError.classList.add('hidden');
      }

      // Validate email format
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailRegex.test(email)) {
        emailError.classList.remove('hidden');
        valid = false;
      } else {
        emailError.classList.add('hidden');
      }

      // Validate password
      if (password.length < 6) {
        passwordError.classList.remove('hidden');
        valid = false;
      } else {
        passwordError.classList.add('hidden');
      }

      // Validate terms checkbox
      if (!termsCheckbox.checked) {
        flash.classList.remove('hidden', 'bg-green-500');
        flash.classList.add('bg-red-500');
        flash.textContent = 'You must agree to the Terms and Services.';
        valid = false;
      }

      if (!valid) return;

      // Check if email exists
      try {
        const res = await fetch(`/linkbuildings/api/check_email.php?email=${encodeURIComponent(email)}`);
        const data = await res.json();

        if (data.exists) {
          emailTaken.classList.remove('hidden');
          flash.classList.remove('hidden', 'bg-green-500');
          flash.classList.add('bg-red-500');
          flash.textContent = 'Email already in use.';
          return;
        } else {
          emailTaken.classList.add('hidden');
        }

        flash.classList.remove('hidden', 'bg-red-500');
        flash.classList.add('bg-green-500');
        flash.textContent = 'Submitting...';
        setTimeout(() => form.submit(), 1000);

      } catch (err) {
        console.error(err);
        flash.classList.remove('hidden', 'bg-green-500');
        flash.classList.add('bg-red-500');
        flash.textContent = 'Server error. Try again.';
      }
    });
  </script>
</body>
</html>

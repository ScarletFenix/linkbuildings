<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Default dashboard for buyers
$dashboardLink = './dashboard.php';

// Admins go to platform
if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') {
    $dashboardLink = './platform.php';
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LinkBuildings - Professional Link Building Services</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        
        :root {
            --primary: #2563eb;
            --secondary: #10b981;
            --dark: #1f2937;
            --light: #f9fafb;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            scroll-behavior: smooth;
        }
        
        .hero-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .hover-lift {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .hover-lift:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        
        .stats-counter {
            transition: all 0.3s ease;
        }
        
        .feature-card {
            transition: all 0.3s ease;
            border: 1px solid #e5e7eb;
        }
        
        .feature-card:hover {
            border-color: #2563eb;
            transform: translateY(-2px);
        }
        
        .testimonial-card {
            background: linear-gradient(145deg, #ffffff 0%, #f8fafc 100%);
            border: 1px solid #e2e8f0;
        }
        
        .cta-gradient {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
        }
        
        .pricing-card {
            transition: all 0.3s ease;
        }
        
        .pricing-card.popular {
            transform: scale(1.05);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            z-index: 10;
        }
        
        .pricing-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }
        
        .pricing-card.popular:hover {
            transform: scale(1.05) translateY(-10px);
        }
        
        .faq-item {
            border-bottom: 1px solid #e5e7eb;
        }
        
        .faq-question {
            cursor: pointer;
            padding: 1.5rem 0;
        }
        
        .faq-answer {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
        }
        
        .faq-answer.open {
            max-height: 500px;
        }
        
        .mobile-menu {
            transform: translateX(-100%);
            transition: transform 0.3s ease;
        }
        
        .mobile-menu.open {
            transform: translateX(0);
        }
        
        /* Loading animation for form submission */
        .btn-loading {
            position: relative;
            color: transparent;
        }
        
        .btn-loading::after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            top: 50%;
            left: 50%;
            margin: -10px 0 0 -10px;
            border: 2px solid #ffffff;
            border-radius: 50%;
            border-top-color: transparent;
            animation: spin 0.8s ease infinite;
        }
        
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        
        /* Success/error message animations */
        .alert-message {
            opacity: 0;
            transform: translateY(-10px);
            transition: opacity 0.3s ease, transform 0.3s ease;
        }
        
        .alert-message.show {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Header/Navigation -->
    <header class="bg-white shadow-sm fixed w-full z-50">
    <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 shadow-sm">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center">
                <a href="./index.php" class="text-2xl font-bold text-blue-600">
                    Link<span class="text-green-500">Buildings</span>
                </a>
            </div>

            <!-- Desktop Menu -->
            <div class="hidden md:flex space-x-8">
                <a href="#services" class="text-gray-700 hover:text-blue-600 font-medium">Services</a>
                <a href="#pricing" class="text-gray-700 hover:text-blue-600 font-medium">Pricing</a>
                <a href="#testimonials" class="text-gray-700 hover:text-blue-600 font-medium">Testimonials</a>
                <a href="#faq" class="text-gray-700 hover:text-blue-600 font-medium">FAQ</a>
            </div>

            <div class="flex items-center space-x-4">
                <div class="hidden md:flex items-center space-x-4">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <!-- Show role-based Dashboard -->
                        <a href="<?php echo $dashboardLink; ?>"
                           class="px-5 py-2 rounded-lg font-medium text-white bg-green-600 hover:bg-green-700 transition-colors">
                           Dashboard
                        </a>
                    <?php else: ?>
                        <!-- Show Login/Register if not logged in -->
                        <a href="./login.php"
                           class="px-5 py-2 rounded-lg font-medium text-blue-600 border border-blue-600 hover:bg-blue-50 transition-colors">
                           Login
                        </a>
                        <a href="./register.php"
                           class="px-5 py-2 rounded-lg font-medium text-white bg-blue-600 hover:bg-blue-700 transition-colors">
                           Register
                        </a>
                    <?php endif; ?>
                </div>

                <!-- Mobile menu toggle -->
                <button id="mobile-menu-button" class="md:hidden text-gray-700">
                    <i class="fas fa-bars text-xl"></i>
                </button>
            </div>
        </div>
    </nav>

    <!-- Mobile Menu -->
    <div id="mobile-menu" class="mobile-menu fixed inset-0 bg-white z-50 p-8 md:hidden">
        <div class="flex justify-between items-center mb-12">
            <div class="text-2xl font-bold text-blue-600">Link<span class="text-green-500">Buildings</span></div>
            <button id="mobile-menu-close" class="text-gray-700">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <div class="flex flex-col space-y-8">
            <a href="#services" class="text-xl text-gray-700 hover:text-blue-600 font-medium">Services</a>
            <a href="#pricing" class="text-xl text-gray-700 hover:text-blue-600 font-medium">Pricing</a>
            <a href="#testimonials" class="text-xl text-gray-700 hover:text-blue-600 font-medium">Testimonials</a>
            <a href="#faq" class="text-xl text-gray-700 hover:text-blue-600 font-medium">FAQ</a>

            <?php if (isset($_SESSION['user_id'])): ?>
                <!-- Mobile role-based Dashboard -->
                <a href="<?php echo $dashboardLink; ?>"
                   class="px-5 py-2 rounded-lg font-medium text-white bg-green-600 hover:bg-green-700 transition-colors">
                   Dashboard
                </a>
            <?php else: ?>
                <a href="./login.php"
                   class="px-5 py-2 rounded-lg font-medium text-blue-600 border border-blue-600 hover:bg-blue-50 transition-colors">
                   Login
                </a>
                <a href="./register.php"
                   class="px-5 py-2 rounded-lg font-medium text-white bg-blue-600 hover:bg-blue-700 transition-colors">
                   Register
                </a>
            <?php endif; ?>
        </div>

        <div class="absolute bottom-8 left-8 right-8">
            <div class="flex justify-center space-x-6">
                <a href="#" class="text-gray-500 hover:text-blue-600"><i class="fab fa-twitter text-xl"></i></a>
                <a href="#" class="text-gray-500 hover:text-blue-600"><i class="fab fa-linkedin text-xl"></i></a>
                <a href="#" class="text-gray-500 hover:text-blue-600"><i class="fab fa-facebook text-xl"></i></a>
            </div>
        </div>
    </div>
</header>

    <!-- Hero Section -->
    <section class="pt-24 md:pt-32 pb-16 md:pb-20 hero-gradient text-white relative">
  <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-12">
    <div class="grid grid-cols-1 md:grid-cols-12 gap-8 md:gap-12 items-start">

      <!-- Left Content (stacked on mobile) -->
      <div class="md:col-span-4 flex flex-col justify-center text-center md:text-left">
        <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-extrabold mb-4 md:mb-6 leading-tight drop-shadow-md">
          Build Quality Backlinks That <span class="text-green-300">Drive Results</span>
        </h1>
        <p class="text-base sm:text-lg md:text-xl text-blue-100 leading-relaxed mb-6 md:mb-8">
          We specialize in premium link building services that boost search rankings, 
          increase organic traffic, and establish your website’s authority. 
          Below is a sample showcase of sites we work with.
        </p>
        <div class="flex flex-wrap justify-center md:justify-start gap-3 sm:gap-4">
          <a href="#contact" class="bg-white text-blue-600 px-5 sm:px-6 py-2.5 sm:py-3 rounded-lg font-semibold hover:bg-gray-100 transition">
            Start Building Links
          </a>
        </div>
      </div>

      <!-- Right Showcase Table -->
      <div class="md:col-span-8 mt-10 md:mt-0">
        <div class="bg-white/95 backdrop-blur-md shadow-2xl rounded-2xl border border-gray-200 p-4 sm:p-6 w-full">
          <!-- Table -->
          <div class="overflow-x-auto rounded-lg">
            <table id="sitesTable" class="w-full text-xs sm:text-sm text-gray-800 text-center">
              <thead class="bg-gradient-to-r from-blue-600 to-green-500 text-white text-[11px] sm:text-xs uppercase tracking-wider">
                <tr>
                  <th class="px-2 sm:px-3 py-2 sm:py-3">Niche</th>
                  <th class="px-2 sm:px-3 py-2 sm:py-3">Site</th>
                  <th class="px-2 sm:px-3 py-2 sm:py-3">Homepage</th>
                  <th class="px-2 sm:px-3 py-2 sm:py-3">Article</th>
                  <th class="px-2 sm:px-3 py-2 sm:py-3">DR</th>
                  <th class="px-2 sm:px-3 py-2 sm:py-3">Traffic</th>
                  <th class="px-2 sm:px-3 py-2 sm:py-3">Country</th>
                </tr>
              </thead>
              <tbody id="tableBody" class="divide-y divide-gray-200"></tbody>
            </table>
          </div>

          <!-- Footer Row with Pagination + Disclaimer -->
          <div class="flex flex-col sm:flex-row justify-between items-center gap-3 mt-4 text-[11px] sm:text-xs text-gray-600">
            <!-- Compact Pagination -->
            <div id="pagination" class="flex gap-1"></div>
            <!-- Disclaimer -->
            <p class="italic text-center sm:text-right">
              * Traffic data from 
              <a href="https://ahrefs.com" target="_blank" class="underline hover:text-blue-600">Ahrefs</a> 
              & <a href="https://semrush.com" target="_blank" class="underline hover:text-green-600">SEMrush</a>.
            </p>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>




    <!-- Stats Section -->
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                <div class="stats-counter p-6">
                    <div class="text-3xl md:text-4xl font-bold text-blue-600 mb-2">1250+</div>
                    <div class="text-gray-600">Links Built</div>
                </div>
                <div class="stats-counter p-6">
                    <div class="text-3xl md:text-4xl font-bold text-green-600 mb-2">    98%</div>
                    <div class="text-gray-600">Success Rate</div>
                </div>
                <div class="stats-counter p-6">
                    <div class="text-3xl md:text-4xl font-bold text-blue-600 mb-2">200+</div>
                    <div class="text-gray-600">Happy Clients</div>
                </div>
                <div class="stats-counter p-6">
                    <div class="text-3xl md:text-4xl font-bold text-green-600 mb-2">24h</div>
                    <div class="text-gray-600">Average Delivery</div>
                </div>
            </div>
        </div>
    </section>


    <!-- Services Section -->
    <section id="services" class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Our Link Building Services</h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                We specialize exclusively in building high-quality backlinks that drive real SEO results
            </p>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            <div class="feature-card bg-white p-8 rounded-xl shadow-sm">
                <div class="w-16 h-16 bg-blue-100 rounded-lg flex items-center justify-center mb-6">
                    <i class="fas fa-newspaper text-3xl text-blue-600"></i>
                </div>
                <h3 class="text-xl font-semibold mb-4">Guest Posting</h3>
                <p class="text-gray-600 mb-4">
                    High-quality guest posts on authoritative websites in your niche with contextual backlinks.
                </p>
                <ul class="space-y-2 text-gray-600">
                    <li class="flex items-center"><i class="fas fa-check text-green-500 mr-2"></i> Premium websites</li>
                    <li class="flex items-center"><i class="fas fa-check text-green-500 mr-2"></i> Contextual placement</li>
                    <li class="flex items-center"><i class="fas fa-check text-green-500 mr-2"></i> SEO-optimized content</li>
                </ul>
            </div>

            <div class="feature-card bg-white p-8 rounded-xl shadow-sm">
                <div class="w-16 h-16 bg-green-100 rounded-lg flex items-center justify-center mb-6">
                    <i class="fas fa-link text-3xl text-green-600"></i>
                </div>
                <h3 class="text-xl font-semibold mb-4">Link Insertion</h3>
                <p class="text-gray-600 mb-4">
                    Strategic placement in resource pages and directories relevant to your industry.
                </p>
                <ul class="space-y-2 text-gray-600">
                    <li class="flex items-center"><i class="fas fa-check text-green-500 mr-2"></i> Industry-specific</li>
                    <li class="flex items-center"><i class="fas fa-check text-green-500 mr-2"></i> High authority sites</li>
                    <li class="flex items-center"><i class="fas fa-check text-green-500 mr-2"></i> Permanent placement</li>
                </ul>
            </div>

            <div class="feature-card bg-white p-8 rounded-xl shadow-sm">
                <div class="w-16 h-16 bg-purple-100 rounded-lg flex items-center justify-center mb-6">
                    <i class="fas fa-share-alt text-3xl text-purple-600"></i>
                </div>
                <h3 class="text-xl font-semibold mb-4">Broken Link Building</h3>
                <p class="text-gray-600 mb-4">
                    Replacing broken links with your content on high-authority websites.
                </p>
                <ul class="space-y-2 text-gray-600">
                    <li class="flex items-center"><i class="fas fa-check text-green-500 mr-2"></i> Natural placement</li>
                    <li class="flex items-center"><i class="fas fa-check text-green-500 mr-2"></i> High success rate</li>
                    <li class="flex items-center"><i class="fas fa-check text-green-500 mr-2"></i> Win-win solution</li>
                </ul>
            </div>
        </div>
    </div>
</section>

    <!-- How It Works -->
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">How Our Process Works</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Simple, transparent process from start to finish
                </p>
            </div>

            <div class="grid md:grid-cols-4 gap-8">
                <div class="text-center">
                    <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <span class="text-2xl font-bold text-blue-600">1</span>
                    </div>
                    <h3 class="text-xl font-semibold mb-4">Strategy Session</h3>
                    <p class="text-gray-600">We analyze your niche and goals to create a custom link building strategy.</p>
                </div>
                
                <div class="text-center">
                    <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <span class="text-2xl font-bold text-green-600">2</span>
                    </div>
                    <h3 class="text-xl font-semibold mb-4">Outreach & Placement</h3>
                    <p class="text-gray-600">We reach out to relevant websites and secure premium placements.</p>
                </div>
                
                <div class="text-center">
                    <div class="w-20 h-20 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <span class="text-2xl font-bold text-purple-600">3</span>
                    </div>
                    <h3 class="text-xl font-semibold mb-4">Quality Assurance</h3>
                    <p class="text-gray-600">Every link is reviewed for quality, relevance, and proper implementation.</p>
                </div>
                
                <div class="text-center">
                    <div class="w-20 h-20 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <span class="text-2xl font-bold text-orange-600">4</span>
                    </div>
                    <h3 class="text-xl font-semibold mb-4">Results Delivery</h3>
                    <p class="text-gray-600">You receive detailed reports and watch your rankings improve.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section id="pricing" class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Transparent Pricing</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Choose the plan that works best for your business needs
                </p>
            </div>

            <div class="grid md:grid-cols-3 gap-8 items-stretch">
                <div class="pricing-card bg-white p-8 rounded-xl shadow-sm">
                    <div class="mb-8">
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">Starter</h3>
                        <p class="text-gray-600">Perfect for small businesses just starting with SEO</p>
                    </div>
                    <div class="mb-8">
                        <div class="text-4xl font-bold text-gray-900 mb-2">$499<span class="text-lg text-gray-500">/month</span></div>
                        <p class="text-gray-600">Billed monthly</p>
                    </div>
                    <ul class="space-y-4 mb-8">
                        <li class="flex items-center"><i class="fas fa-check text-green-500 mr-3"></i> 5 Quality Backlinks</li>
                        <li class="flex items-center"><i class="fas fa-check text-green-500 mr-3"></i> Guest Post Outreach</li>
                        <li class="flex items-center"><i class="fas fa-check text-green-500 mr-3"></i> Content Creation</li>
                        <li class="flex items-center"><i class="fas fa-times text-gray-400 mr-3"></i> <span class="text-gray-400">Broken Link Building</span></li>
                        <li class="flex items-center"><i class="fas fa-times text-gray-400 mr-3"></i> <span class="text-gray-400">Priority Support</span></li>
                    </ul>
                    <a href="#contact" class="block w-full bg-gray-100 text-gray-800 text-center font-semibold py-3 rounded-lg hover:bg-gray-200 transition-colors">
                        Get Started
                    </a>
                </div>

                <div class="pricing-card popular bg-white p-8 rounded-xl shadow-sm border-2 border-blue-600 relative">
                    <div class="absolute top-0 right-0 bg-blue-600 text-white px-4 py-1 text-sm font-semibold rounded-bl-lg">
                        Most Popular
                    </div>
                    <div class="mb-8">
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">Professional</h3>
                        <p class="text-gray-600">Ideal for growing businesses seeking consistent results</p>
                    </div>
                    <div class="mb-8">
                        <div class="text-4xl font-bold text-gray-900 mb-2">$999<span class="text-lg text-gray-500">/month</span></div>
                        <p class="text-gray-600">Billed monthly</p>
                    </div>
                    <ul class="space-y-4 mb-8">
                        <li class="flex items-center"><i class="fas fa-check text-green-500 mr-3"></i> 15 Quality Backlinks</li>
                        <li class="flex items-center"><i class="fas fa-check text-green-500 mr-3"></i> Guest Post Outreach</li>
                        <li class="flex items-center"><i class="fas fa-check text-green-500 mr-3"></i> Content Creation</li>
                        <li class="flex items-center"><i class="fas fa-check text-green-500 mr-3"></i> Broken Link Building</li>
                        <li class="flex items-center"><i class="fas fa-check text-green-500 mr-3"></i> Priority Support</li>
                    </ul>
                    <a href="#contact" class="block w-full bg-blue-600 text-white text-center font-semibold py-3 rounded-lg hover:bg-blue-700 transition-colors">
                        Get Started
                    </a>
                </div>

                <div class="pricing-card bg-white p-8 rounded-xl shadow-sm">
                    <div class="mb-8">
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">Enterprise</h3>
                        <p class="text-gray-600">For established businesses needing maximum impact</p>
                    </div>
                    <div class="mb-8">
                        <div class="text-4xl font-bold text-gray-900 mb-2">$1,999<span class="text-lg text-gray-500">/month</span></div>
                        <p class="text-gray-600">Billed monthly</p>
                    </div>
                    <ul class="space-y-4 mb-8">
                        <li class="flex items-center"><i class="fas fa-check text-green-500 mr-3"></i> 30+ Quality Backlinks</li>
                        <li class="flex items-center"><i class="fas fa-check text-green-500 mr-3"></i> Guest Post Outreach</li>
                        <li class="flex items-center"><i class="fas fa-check text-green-500 mr-3"></i> Premium Content Creation</li>
                        <li class="flex items-center"><i class="fas fa-check text-green-500 mr-3"></i> Broken Link Building</li>
                        <li class="flex items-center"><i class="fas fa-check text-green-500 mr-3"></i> 24/7 Priority Support</li>
                    </ul>
                    <a href="#contact" class="block w-full bg-gray-100 text-gray-800 text-center font-semibold py-3 rounded-lg hover:bg-gray-200 transition-colors">
                        Get Started
                    </a>
                </div>
            </div>

            <div class="text-center mt-12">
                <p class="text-gray-600">Need a custom plan? <a href="#contact" class="text-blue-600 hover:text-blue-800 font-semibold">Contact us</a></p>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section id="testimonials" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">What Our Clients Say</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Join hundreds of satisfied clients who have transformed their SEO performance
                </p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <div class="testimonial-card p-8 rounded-xl">
                    <div class="flex items-center mb-6">
                        <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1287&q=80" alt="Portrait of Sarah Johnson, Marketing Director at TechStart Inc, smiling professional woman with dark hair" class="w-12 h-12 rounded-full mr-4 object-cover">
                        <div>
                            <div class="font-semibold">Sarah Johnson</div>
                            <div class="text-blue-600">Marketing Director, TechStart Inc</div>
                        </div>
                    </div>
                    <p class="text-gray-600 italic">
                        "LinkBuilder Pro helped us increase our organic traffic by 300% in just 3 months. Their strategic approach to link building is unmatched."
                    </p>
                    <div class="flex mt-4">
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-yellow-400"></i>
                    </div>
                </div>

                <div class="testimonial-card p-8 rounded-xl">
                    <div class="flex items-center mb-6">
                        <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1170&q=80" alt="Portrait of Michael Chen, CEO of EcomGrowth, confident business executive with glasses" class="w-12 h-12 rounded-full mr-4 object-cover">
                        <div>
                            <div class="font-semibold">Michael Chen</div>
                            <div class="text-blue-600">CEO, EcomGrowth</div>
                        </div>
                    </div>
                    <p class="text-gray-600 italic">
                        "The quality of links we received was exceptional. Each placement was relevant and helped us dominate our niche's search results."
                    </p>
                    <div class="flex mt-4">
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-yellow-400"></i>
                    </div>
                </div>

                <div class="testimonial-card p-8 rounded-xl">
                    <div class="flex items-center mb-6">
                        <img src="https://images.unsplash.com/photo-1580489944761-15a19d654956?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1361&q=80" alt="Portrait of Emily Rodriguez, SEO Manager at DigitalAgency, professional woman with curly hair" class="w-12 h-12 rounded-full mr-4 object-cover">
                        <div>
                            <div class="font-semibold">Emily Rodriguez</div>
                            <div class="text-blue-600">SEO Manager, DigitalAgency</div>
                        </div>
                    </div>
                    <p class="text-gray-600 italic">
                        "Professional, reliable, and delivers exactly what they promise. Our clients are thrilled with the results we've achieved together."
                    </p>
                    <div class="flex mt-4">
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-yellow-400"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section id="faq" class="py-20 bg-gray-50">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Frequently Asked Questions</h2>
                <p class="text-xl text-gray-600">
                    Everything you need to know about our link building services
                </p>
            </div>

            <div class="space-y-4">
                <div class="faq-item">
                    <div class="faq-question flex justify-between items-center">
                        <h3 class="text-lg font-semibold">How long does it take to see results?</h3>
                        <i class="fas fa-chevron-down text-blue-600"></i>
                    </div>
                    <div class="faq-answer">
                        <p class="text-gray-600 pb-4">Most clients start seeing improvements in search rankings within 4-8 weeks. However, the timeline can vary based on your industry competitiveness, website age, and the quality of your existing SEO foundation. We provide detailed monthly reports to track progress.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question flex justify-between items-center">
                        <h3 class="text-lg font-semibold">What types of websites do you get links from?</h3>
                        <i class="fas fa-chevron-down text-blue-600"></i>
                    </div>
                    <div class="faq-answer">
                        <p class="text-gray-600 pb-4">We secure placements on authoritative, relevant websites in your industry. This includes industry publications, reputable blogs, news sites, and resource directories. All websites have strong domain authority, good traffic metrics, and clean backlink profiles.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question flex justify-between items-center">
                        <h3 class="text-lg font-semibold">Do you offer any guarantees?</h3>
                        <i class="fas fa-chevron-down text-blue-600"></i>
                    </div>
                    <div class="faq-answer">
                        <p class="text-gray-600 pb-4">We guarantee the delivery of the number of links specified in your package. If we're unable to secure a placement, we'll either replace it with an equivalent opportunity or provide a prorated refund. While we can't guarantee specific ranking improvements (as Google's algorithm has many factors), our methods have consistently produced positive results for our clients.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question flex justify-between items-center">
                        <h3 class="text-lg font-semibold">How do you ensure link quality?</h3>
                        <i class="fas fa-chevron-down text-blue-600"></i>
                    </div>
                    <div class="faq-answer">
                        <p class="text-gray-600 pb-4">Every potential placement goes through a rigorous vetting process. We check domain authority, traffic metrics, content quality, relevance to your niche, and backlink profile health. We also ensure links are dofollow and placed in content contextually relevant to your business.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question flex justify-between items-center">
                        <h3 class="text-lg font-semibold">Can I choose where my links are placed?</h3>
                        <i class="fas fa-chevron-down text-blue-600"></i>
                    </div>
                    <div class="faq-answer">
                        <p class="text-gray-600 pb-4">Yes, we encourage client collaboration. After our strategy session, we'll present a list of potential placement opportunities for your approval. You can veto any sites you're uncomfortable with and suggest others you'd prefer to target.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question flex justify-between items-center">
                        <h3 class="text-lg font-semibold">How is your pricing structured?</h3>
                        <i class="fas fa-chevron-down text-blue-600"></i>
                    </div>
                    <div class="faq-answer">
                        <p class="text-gray-600 pb-4">Our pricing is based on the number and quality of links, the difficulty of placement in your industry, and any additional services like content creation. We offer monthly packages as well as custom enterprise solutions for larger campaigns.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 cta-gradient text-white">
        <div class="max-w-4xl mx-auto text-center px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl md:text-4xl font-bold mb-6">Ready to Boost Your Search Rankings?</h2>
            <p class="text-xl mb-8 opacity-90">
                Start building quality backlinks today and watch your website climb to the top of search results
            </p>
            <a href="#contact" class="inline-block bg-white text-blue-600 px-8 py-4 rounded-lg font-semibold hover:bg-gray-100 transition-colors hover-lift">
                Get Free Consultation
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-4 gap-8">
                <div>
                    <div class="text-2xl font-bold text-white mb-4">Link<span class="text-green-400">Buildings</span></div>
                    <p class="text-gray-400 mb-4">
                        Professional link building services that deliver real results for your business.
                    </p>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-linkedin"></i></a>
                        <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-facebook"></i></a>
                    </div>
                </div>
                
                <div>
                    <h3 class="text-lg font-semibold mb-4">Services</h3>
                    <ul class="space-y-2">
                        <li><a href="#services" class="text-gray-400 hover:text-white">Guest Posting</a></li>
                        <li><a href="#services" class="text-gray-400 hover:text-white">Resource Links</a></li>
                        <li><a href="#services" class="text-gray-400 hover:text-white">Broken Link Building</a></li>
                        <li><a href="#services" class="text-gray-400 hover:text-white">Link Audit</a></li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="text-lg font-semibold mb-4">Company</h3>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-white">About Us</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">Case Studies</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">Blog</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">Careers</a></li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="text-lg font-semibold mb-4">Contact Us</h3>
                    <p class="text-gray-400 mb-4">Get in touch to discuss your link building needs</p>
                    <a href="mailto:hello@linkbuildings.com" class="text-blue-400 hover:text-blue-300">hello@linkbuildings.com</a>
                </div>
            </div>
            
            <div class="border-t border-gray-800 mt-12 pt-8 text-center text-gray-400">
                <p>&copy; 2025 LinkBuildings. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
         // Sample data (more than 5 rows for pagination)
  const tableData = [
    { niche: "Tech", site: "techinsight.com", homepage: "$150", article: "$90", dr: 72, traffic: "45k", country: "us" },
    { niche: "Health", site: "wellnessdaily.org", homepage: "$120", article: "$70", dr: 65, traffic: "32k", country: "gb" },
    { niche: "Business", site: "bizgrowth.net", homepage: "$180", article: "$110", dr: 78, traffic: "60k", country: "ca" },
    { niche: "Finance", site: "moneyminds.io", homepage: "$200", article: "$130", dr: 82, traffic: "75k", country: "au" },
    { niche: "Travel", site: "globetrekker.com", homepage: "$170", article: "$95", dr: 70, traffic: "40k", country: "in" },
    { niche: "Education", site: "edulearners.com", homepage: "$140", article: "$80", dr: 68, traffic: "28k", country: "us" },
    { niche: "Food", site: "dailyrecipes.net", homepage: "$130", article: "$85", dr: 66, traffic: "25k", country: "gb" },
    { niche: "Sports", site: "proathletehub.com", homepage: "$160", article: "$100", dr: 74, traffic: "38k", country: "au" },
    { niche: "Lifestyle", site: "modernliving.com", homepage: "$150", article: "$90", dr: 70, traffic: "50k", country: "us" },
    { niche: "Entertainment", site: "moviemagic.org", homepage: "$180", article: "$110", dr: 75, traffic: "55k", country: "ca" },
    { niche: "Tech", site: "techinsight.com", homepage: "$150", article: "$90", dr: 72, traffic: "45k", country: "us" },
    { niche: "Health", site: "wellnessdaily.org", homepage: "$120", article: "$70", dr: 65, traffic: "32k", country: "gb" },
    { niche: "Business", site: "bizgrowth.net", homepage: "$180", article: "$110", dr: 78, traffic: "60k", country: "ca" },
    { niche: "Finance", site: "moneyminds.io", homepage: "$200", article: "$130", dr: 82, traffic: "75k", country: "au" },
    { niche: "Travel", site: "globetrekker.com", homepage: "$170", article: "$95", dr: 70, traffic: "40k", country: "in" },
    { niche: "Education", site: "edulearners.com", homepage: "$140", article: "$80", dr: 68, traffic: "28k", country: "us" },
    { niche: "Food", site: "dailyrecipes.net", homepage: "$130", article: "$85", dr: 66, traffic: "25k", country: "gb" },
    { niche: "Sports", site: "proathletehub.com", homepage: "$160", article: "$100", dr: 74, traffic: "38k", country: "au" },
    { niche: "Fashion", site: "styletrends.io", homepage: "$190", article: "$120", dr: 77, traffic: "55k", country: "us" },
    { niche: "Gaming", site: "gamerzone.net", homepage: "$175", article: "$115", dr: 73, traffic: "50k", country: "de" },
    { niche: "Crypto", site: "cryptovision.org", homepage: "$210", article: "$140", dr: 80, traffic: "65k", country: "sg" },
    { niche: "Real Estate", site: "propertyfocus.com", homepage: "$160", article: "$105", dr: 71, traffic: "42k", country: "ae" },

  ];

  const rowsPerPage = 10;
  let currentPage = 1;

  function renderTable(page) {
    const tableBody = document.getElementById("tableBody");
    tableBody.innerHTML = "";

    const start = (page - 1) * rowsPerPage;
    const end = start + rowsPerPage;
    const pageData = tableData.slice(start, end);

    pageData.forEach(row => {
      const tr = document.createElement("tr");
      tr.className = "hover:bg-gray-50";
      tr.innerHTML = `
        <td class="px-2 py-2 font-medium">${row.niche}</td>
        <td class="px-2 py-2"><a href="#" class="text-blue-600 hover:underline">${row.site}</a></td>
        <td class="px-2 py-2">${row.homepage}</td>
        <td class="px-2 py-2">${row.article}</td>
        <td class="px-2 py-2 font-bold">${row.dr}</td>
        <td class="px-2 py-2">${row.traffic}</td>
        <td class="px-2 py-2 flex items-center justify-center space-x-1">
          <img src="https://flagcdn.com/w20/${row.country}.png" alt="${row.country} flag">
        </td>
      `;
      tableBody.appendChild(tr);
    });

    renderPagination();
  }

  function renderPagination() {
    const pagination = document.getElementById("pagination");
    pagination.innerHTML = "";

    const totalPages = Math.ceil(tableData.length / rowsPerPage);

    for (let i = 1; i <= totalPages; i++) {
      const btn = document.createElement("button");
      btn.innerText = i;
      btn.className = `px-3 py-1 border rounded ${i === currentPage ? "bg-blue-600 text-white" : "bg-gray-100 text-gray-700 hover:bg-gray-200"}`;
      btn.onclick = () => {
        currentPage = i;
        renderTable(currentPage);
      };
      pagination.appendChild(btn);
    }
  }

  // Initial load
  renderTable(currentPage);


        // Simple counter animation for stats
        document.addEventListener('DOMContentLoaded', function() {
            const counters = document.querySelectorAll('.stats-counter');
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const target = entry.target;
                        const number = target.querySelector('div:first-child');
                        const value = parseInt(number.textContent.replace('+', '').replace('%', ''));
                        
                        let start = 0;
                        const duration = 2000;
                        const increment = value / (duration / 16);
                        
                        const timer = setInterval(() => {
                            start += increment;
                            if (start >= value) {
                                number.textContent = value + (number.textContent.includes('%') ? '%' : '+');
                                clearInterval(timer);
                            } else {
                                number.textContent = Math.floor(start) + (number.textContent.includes('%') ? '%' : '+');
                            }
                        }, 16);
                        
                        observer.unobserve(target);
                    }
                });
            }, { threshold: 0.5 });
            
            counters.forEach(counter => {
                observer.observe(counter);
            });

            // Mobile menu functionality
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            const mobileMenuClose = document.getElementById('mobile-menu-close');
            const mobileMenu = document.getElementById('mobile-menu');
            
            mobileMenuButton.addEventListener('click', () => {
                mobileMenu.classList.add('open');
                document.body.style.overflow = 'hidden';
            });
            
            mobileMenuClose.addEventListener('click', () => {
                mobileMenu.classList.remove('open');
                document.body.style.overflow = 'auto';
            });
            
            // Close mobile menu when clicking on links
            const mobileMenuLinks = mobileMenu.querySelectorAll('a');
            mobileMenuLinks.forEach(link => {
                link.addEventListener('click', () => {
                    mobileMenu.classList.remove('open');
                    document.body.style.overflow = 'auto';
                });
            });

            // FAQ accordion functionality
            const faqQuestions = document.querySelectorAll('.faq-question');
            faqQuestions.forEach(question => {
                question.addEventListener('click', () => {
                    const answer = question.nextElementSibling;
                    const icon = question.querySelector('i');
                    
                    // Toggle this answer
                    answer.classList.toggle('open');
                    icon.classList.toggle('fa-chevron-down');
                    icon.classList.toggle('fa-chevron-up');
                    
                    // Close other answers
                    faqQuestions.forEach(otherQuestion => {
                        if (otherQuestion !== question) {
                            const otherAnswer = otherQuestion.nextElementSibling;
                            const otherIcon = otherQuestion.querySelector('i');
                            
                            otherAnswer.classList.remove('open');
                            otherIcon.classList.add('fa-chevron-down');
                            otherIcon.classList.remove('fa-chevron-up');
                        }
                    });
                });
            });

            // Form submission handling
            const contactForm = document.getElementById('contact-form');
            const formMessage = document.getElementById('form-message');
            
            contactForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Get form data
                const formData = new FormData(contactForm);
                const submitButton = contactForm.querySelector('button[type="submit"]');
                
                // Show loading state
                submitButton.classList.add('btn-loading');
                
                // Simulate form submission (in a real scenario, you would send to a server)
                setTimeout(() => {
                    // Show success message
                    formMessage.textContent = 'Thanks for your message! We\'ll get back to you within 24 hours.';
                    formMessage.classList.remove('hidden', 'bg-red-100', 'text-red-700');
                    formMessage.classList.add('show', 'bg-green-100', 'text-green-700');
                    
                    // Reset form
                    contactForm.reset();
                    
                    // Remove loading state
                    submitButton.classList.remove('btn-loading');
                    
                    // Hide message after 5 seconds
                    setTimeout(() => {
                        formMessage.classList.remove('show');
                        setTimeout(() => {
                            formMessage.classList.add('hidden');
                        }, 300);
                    }, 5000);
                }, 1500);
            });
        });
    </script>
</body>
</html>
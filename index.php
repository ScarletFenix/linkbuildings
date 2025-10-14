<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Default dashboard for buyers
$dashboardLink = './dashboard.php';

// Admins go to platform
if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') {
    $dashboardLink = './admin/platform.php';
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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
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
        
        .nav-scrolled {
            background: rgba(15, 23, 42, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }
        
        .hero-gradient {
            background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 50%, #0f172a 100%);
        }
        
        .hover-lift {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .hover-lift:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        
        /* Animation classes */
        .fade-in-up {
            opacity: 0;
            transform: translateY(30px);
            transition: opacity 0.8s ease, transform 0.8s ease;
        }
        
        .fade-in-left {
            opacity: 0;
            transform: translateX(-30px);
            transition: opacity 0.8s ease, transform 0.8s ease;
        }
        
        .fade-in-right {
            opacity: 0;
            transform: translateX(30px);
            transition: opacity 0.8s ease, transform 0.8s ease;
        }
        
        .animate-in {
            opacity: 1;
            transform: translate(0, 0);
        }
        
        .stagger-delay-1 { transition-delay: 0.1s; }
        .stagger-delay-2 { transition-delay: 0.2s; }
        .stagger-delay-3 { transition-delay: 0.3s; }
        .stagger-delay-4 { transition-delay: 0.4s; }
        .stagger-delay-5 { transition-delay: 0.5s; }
        
        /* Mobile menu styles */
        .mobile-menu {
            transform: translateX(-100%);
            transition: transform 0.3s ease;
        }
        
        .mobile-menu.open {
            transform: translateX(0);
        }
        
        /* Stats counter animation */
        .stats-counter {
            transition: all 0.3s ease;
        }
        
        /* Feature cards */
        .feature-card {
            transition: all 0.3s ease;
            border: 1px solid #e5e7eb;
        }
        
        .feature-card:hover {
            border-color: #2563eb;
            transform: translateY(-2px);
        }
        
        /* Testimonial cards */
        .testimonial-card {
            background: linear-gradient(145deg, #ffffff 0%, #f8fafc 100%);
            border: 1px solid #e2e8f0;
        }
        
        /* CTA gradient */
        .cta-gradient {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
        }
        
        /* Pricing cards */
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
        
        /* FAQ styles */
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
        
        /* Table styles */
        .table-container {
            max-height: 500px;
            overflow-y: auto;
        }
        
        .table-container::-webkit-scrollbar {
            width: 6px;
        }
        
        .table-container::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 3px;
        }
        
        .table-container::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.3);
            border-radius: 3px;
        }
        
        .table-container::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.5);
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Header/Navigation -->
    <header id="navbar" class="fixed w-full z-50 transition-all duration-300">
        <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center">
                    <a href="./index.php" class="text-2xl font-bold text-white flex items-center">
                        <svg class="w-8 h-8 mr-2" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M13 2L3 14H12L11 22L21 10H12L13 2Z" fill="url(#gradient)"/>
                            <defs>
                                <linearGradient id="gradient" x1="0%" y1="0%" x2="100%" y2="100%">
                                    <stop offset="0%" stop-color="#3B82F6"/>
                                    <stop offset="100%" stop-color="#10B981"/>
                                </linearGradient>
                            </defs>
                        </svg>
                        Link<span class="text-green-400">Buildings</span>
                    </a>
                </div>

                <!-- Desktop Menu -->
                <div class="hidden md:flex space-x-8">
                    <a href="#services" class="text-gray-300 hover:text-white font-medium transition-colors duration-300">Services</a>
                    <a href="#pricing" class="text-gray-300 hover:text-white font-medium transition-colors duration-300">Pricing</a>
                    <a href="#testimonials" class="text-gray-300 hover:text-white font-medium transition-colors duration-300">Testimonials</a>
                    <a href="#faq" class="text-gray-300 hover:text-white font-medium transition-colors duration-300">FAQ</a>
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
                    <button id="mobile-menu-button" class="md:hidden text-white">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </div>
        </nav>

        <!-- Mobile Menu -->
        <div id="mobile-menu" class="mobile-menu fixed inset-0 bg-slate-900 z-50 p-8 md:hidden">
            <div class="flex justify-between items-center mb-12">
                <div class="text-2xl font-bold text-white flex items-center">
                    <svg class="w-8 h-8 mr-2" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M13 2L3 14H12L11 22L21 10H12L13 2Z" fill="url(#gradient)"/>
                        <defs>
                            <linearGradient id="gradient" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" stop-color="#3B82F6"/>
                                <stop offset="100%" stop-color="#10B981"/>
                            </linearGradient>
                        </defs>
                    </svg>
                    Link<span class="text-green-400">Buildings</span>
                </div>
                <button id="mobile-menu-close" class="text-white">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <div class="flex flex-col space-y-8">
                <a href="#services" class="text-xl text-gray-300 hover:text-white font-medium">Services</a>
                <a href="#pricing" class="text-xl text-gray-300 hover:text-white font-medium">Pricing</a>
                <a href="#testimonials" class="text-xl text-gray-300 hover:text-white font-medium">Testimonials</a>
                <a href="#faq" class="text-xl text-gray-300 hover:text-white font-medium">FAQ</a>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <!-- Mobile role-based Dashboard -->
                    <a href="<?php echo $dashboardLink; ?>"
                       class="px-5 py-3 rounded-lg font-medium text-white bg-gradient-to-r from-green-500 to-green-600 text-center">
                       Dashboard
                    </a>
                <?php else: ?>
                    <a href="./login.php"
                       class="px-5 py-3 rounded-lg font-medium text-white border border-white/30 text-center hover:bg-white/10 transition-colors">
                       Login
                    </a>
                    <a href="./register.php"
                       class="px-5 py-3 rounded-lg font-medium text-white bg-gradient-to-r from-blue-500 to-blue-600 text-center">
                       Register
                    </a>
                <?php endif; ?>
            </div>

            <div class="absolute bottom-8 left-8 right-8">
                <div class="flex justify-center space-x-6">
                    <a href="#" class="text-gray-400 hover:text-white transition-colors"><i class="fab fa-twitter text-xl"></i></a>
                    <a href="#" class="text-gray-400 hover:text-white transition-colors"><i class="fab fa-linkedin text-xl"></i></a>
                    <a href="#" class="text-gray-400 hover:text-white transition-colors"><i class="fab fa-facebook text-xl"></i></a>
                </div>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="pt-24 md:pt-32 pb-16 md:pb-24 hero-gradient text-white relative overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 opacity-10">
  <div 
    class="absolute inset-0"
    style="
      background-color: #DFDBE5;
      background-image: url('data:image/svg+xml,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27 width=%2716%27 height=%2732%27 viewBox=%270 0 16 32%27%3E%3Cg fill=%27%239C92AC%27 fill-opacity=%270.48%27%3E%3Cpath fill-rule=%27evenodd%27 d=%27M0 24h4v2H0v-2zm0 4h6v2H0v-2zm0-8h2v2H0v-2zM0 0h4v2H0V0zm0 4h2v2H0V4zm16 20h-6v2h6v-2zm0 4H8v2h8v-2zm0-8h-4v2h4v-2zm0-20h-6v2h6V0zm0 4h-4v2h4V4zm-2 12h2v2h-2v-2zm0-8h2v2h-2V8zM2 8h10v2H2V8zm0 8h10v2H2v-2zm-2-4h14v2H0v-2zm4-8h6v2H4V4zm0 16h6v2H4v-2zM6 0h2v2H6V0zm0 24h2v2H6v-2z%27/%3E%3C/g%3E%3C/svg%3E');
    background-repeat: repeat;
  ">
  </div>
</div>

        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                
                <!-- Left Content -->
                <div class="text-center lg:text-left">
                    <div class="mb-4 fade-in-up stagger-delay-1">
                        <span class="inline-block bg-blue-500/20 text-blue-300 text-sm font-semibold px-4 py-2 rounded-full mb-4">
                            Premium Link Building Services
                        </span>
                    </div>
                    
                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold leading-tight mb-6 fade-in-up stagger-delay-2">
                        <span class="bg-gradient-to-r from-blue-400 to-cyan-400 bg-clip-text text-transparent">
                            Authority Backlinks
                        </span>
                        <br>That Drive <span class="text-cyan-300">Real SEO Results</span>
                    </h1>
                    
                    <p class="text-xl text-blue-100 leading-relaxed mb-8 max-w-2xl fade-in-up stagger-delay-3">
                        We acquire premium backlinks from high-authority, niche-relevant domains to enhance search visibility, scale organic traffic, and strengthen domain authority.
Our methodology leverages data from leading SEO platforms, ensuring measurable improvements in rankings and ROI.
                    </p>
                    
                    <!-- Trust Indicators -->
                    <div class="mt-12 flex flex-wrap items-center justify-center lg:justify-start gap-8 text-blue-200 fade-in-up stagger-delay-5">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-cyan-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span>Premium Domain Authority</span>
                        </div>
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-cyan-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span>White-Hat Strategies</span>
                        </div>
                    </div>
                </div>
                
                <!-- Right Content - Improved Table -->
                <div id="sites-showcase" class="w-[900px] bg-white/10 backdrop-blur-md rounded-2xl border border-white/20 p-6 shadow-2xl fade-in-right">
                    <div class="mb-6">
                        <h3 class="text-2xl font-bold text-white mb-2">Our Premium Network</h3>
                        <p class="text-blue-200">Sample of authoritative sites we work with</p>
                    </div>
                    
                    <!-- Improved Table with Scroll -->
                    <div class="table-container max-h-96 overflow-y-auto rounded-xl border border-white/10 w-full">
  <table class="w-full text-sm table-fixed">
    <thead class="bg-white/5 sticky top-0">
      <tr class="text-left text-blue-200 uppercase text-xs font-semibold tracking-wider">
        <th class="py-4 px-6 w-[25%]">Niche</th>
        <th class="py-4 px-6 w-[35%]">Domain</th>
        <th class="py-4 px-6 w-[15%] text-right">DR</th>
        <th class="py-4 px-6 w-[15%] text-right">Traffic</th>
        <th class="py-4 px-6 w-[10%] text-right">Price</th>
        <th class="py-4 px-6 w-[10%] text-right">Country</th>
      </tr>
    </thead>
    <tbody class="divide-y divide-white/5" id="tableBody">
      <!-- Rows -->
    </tbody>
  </table>
</div>

                    
                    <div class="mt-4 flex justify-between items-center text-xs text-blue-300">
                        <span>Showing <span id="visibleRows">10</span> of <span id="totalRows">50</span> premium domains</span>
                        <a href="#" class="text-cyan-300 hover:text-cyan-200 font-medium flex items-center">
                            View Full Portfolio
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
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
    <section class="relative py-20 cta-gradient text-white overflow-hidden">
  <!-- Background pattern -->
  <div class="absolute inset-0 opacity-10">
    <div class="absolute inset-0" 
         style="background-color: #1e3a8a; 
                background-image: url('data:image/svg+xml,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27 width=%2716%27 height=%2732%27 viewBox=%270 0 16 32%27%3E%3Cg fill=%27%239C92AC%27 fill-opacity=%270.48%27%3E%3Cpath fill-rule=%27evenodd%27 d=%27M0 24h4v2H0v-2zm0 4h6v2H0v-2zm0-8h2v2H0v-2zM0 0h4v2H0V0zm0 4h2v2H0V4zm16 20h-6v2h6v-2zm0 4H8v2h8v-2zm0-8h-4v2h4v-2zm0-20h-6v2h6V0zm0 4h-4v2h4V4zm-2 12h2v2h-2v-2zm0-8h2v2h-2V8zM2 8h10v2H2V8zm0 8h10v2H2v-2zm-2-4h14v2H0v-2zm4-8h6v2H4V4zm0 16h6v2H4v-2zM6 0h2v2H6V0zm0 24h2v2H6v-2z%27/%3E%3C/g%3E%3C/svg%3E'); 
                background-repeat: repeat;">
    </div>
  </div>

  <!-- Content -->
  <div class="relative max-w-4xl mx-auto text-center px-4 sm:px-6 lg:px-8">
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
        // Sample data for the table (increased to 50 rows)
        const tableData = [
            { niche: "Technology", site: "TechInsider.io", dr: 84, traffic: "120K/mo", price: "$850", status: "active", country: "US" },
            { niche: "Finance", site: "WealthBuilders.com", dr: 79, traffic: "95K/mo", price: "$790", status: "active", country: "US" },
            { niche: "Health", site: "WellnessToday.org", dr: 76, traffic: "82K/mo", price: "$720", status: "active", country: "US" },
            { niche: "Business", site: "ExecutiveGrowth.net", dr: 81, traffic: "110K/mo", price: "$920", status: "active", country: "US" },
            { niche: "Marketing", site: "DigitalStrategy.co", dr: 77, traffic: "88K/mo", price: "$750", status: "active", country: "US" },
            { niche: "Lifestyle", site: "ModernLiving.com", dr: 72, traffic: "65K/mo", price: "$680", status: "active", country: "US" },
            { niche: "Travel", site: "GlobeTrekker.com", dr: 75, traffic: "78K/mo", price: "$710", status: "active", country: "US" },
            { niche: "Food", site: "GourmetKitchen.net", dr: 69, traffic: "52K/mo", price: "$590", status: "active", country: "US" },
            { niche: "Education", site: "EduMasters.org", dr: 83, traffic: "115K/mo", price: "$880", status: "active", country: "US" },
            { niche: "Sports", site: "AthleteHub.com", dr: 71, traffic: "61K/mo", price: "$640", status: "active", country: "US" },
            { niche: "Entertainment", site: "PopCulture.io", dr: 68, traffic: "48K/mo", price: "$560", status: "active", country: "US" },
            { niche: "Fashion", site: "StyleTrends.co", dr: 74, traffic: "72K/mo", price: "$690", status: "active" },
            { niche: "Real Estate", site: "PropertyFocus.com", dr: 73, traffic: "68K/mo", price: "$670", status: "active" },
            { niche: "Automotive", site: "AutoEnthusiast.net", dr: 70, traffic: "58K/mo", price: "$620", status: "active" },
            { niche: "Gaming", site: "GameZone.io", dr: 76, traffic: "85K/mo", price: "$730", status: "active" },
            { niche: "Parenting", site: "FamilyFirst.org", dr: 67, traffic: "45K/mo", price: "$540", status: "active" },
            { niche: "Pets", site: "PetCareExperts.com", dr: 65, traffic: "38K/mo", price: "$510", status: "active" },
            { niche: "Home Improvement", site: "DIYMastery.net", dr: 69, traffic: "55K/mo", price: "$600", status: "active" },
            { niche: "Photography", site: "LensArtistry.co", dr: 72, traffic: "63K/mo", price: "$660", status: "active" },
            { niche: "Fitness", site: "FitLifeJournal.com", dr: 75, traffic: "80K/mo", price: "$700", status: "active" },
            { niche: "Cryptocurrency", site: "CryptoInsider.io", dr: 78, traffic: "92K/mo", price: "$780", status: "active" },
            { niche: "Sustainability", site: "EcoLiving.org", dr: 74, traffic: "70K/mo", price: "$680", status: "active" },
            { niche: "Career", site: "ProfessionalGrowth.net", dr: 80, traffic: "98K/mo", price: "$810", status: "active" },
            { niche: "Beauty", site: "BeautyExperts.co", dr: 71, traffic: "60K/mo", price: "$630", status: "active" },
            { niche: "Science", site: "ScienceDaily.io", dr: 85, traffic: "125K/mo", price: "$950", status: "active" },
            { niche: "Politics", site: "PolicyReview.org", dr: 82, traffic: "105K/mo", price: "$870", status: "active" },
            { niche: "Art", site: "CreativeExpressions.com", dr: 68, traffic: "50K/mo", price: "$570", status: "active" },
            { niche: "Music", site: "MusicLovers.net", dr: 70, traffic: "56K/mo", price: "$610", status: "active" },
            { niche: "Books", site: "LiteraryWorld.org", dr: 73, traffic: "66K/mo", price: "$650", status: "active" },
            { niche: "History", site: "HistoricalInsights.com", dr: 77, traffic: "86K/mo", price: "$740", status: "active" },
            { niche: "Psychology", site: "MindMatters.io", dr: 79, traffic: "94K/mo", price: "$800", status: "active" },
            { niche: "Philosophy", site: "DeepThoughts.org", dr: 75, traffic: "76K/mo", price: "$690", status: "active" },
            { niche: "Technology", site: "FutureTech.io", dr: 83, traffic: "112K/mo", price: "$890", status: "active" },
            { niche: "Finance", site: "InvestmentGurus.com", dr: 81, traffic: "102K/mo", price: "$840", status: "active" },
            { niche: "Health", site: "MedicalBreakthroughs.net", dr: 84, traffic: "118K/mo", price: "$920", status: "active" },
            { niche: "Business", site: "StartupSuccess.co", dr: 78, traffic: "90K/mo", price: "$770", status: "active" },
            { niche: "Marketing", site: "GrowthHackers.io", dr: 80, traffic: "96K/mo", price: "$790", status: "active" },
            { niche: "Lifestyle", site: "UrbanLiving.com", dr: 72, traffic: "64K/mo", price: "$670", status: "active" },
            { niche: "Travel", site: "AdventureSeekers.net", dr: 74, traffic: "74K/mo", price: "$710", status: "active" },
            { niche: "Food", site: "CulinaryDelights.org", dr: 70, traffic: "57K/mo", price: "$620", status: "active" },
            { niche: "Education", site: "LearningHub.io", dr: 82, traffic: "108K/mo", price: "$860", status: "active" },
            { niche: "Sports", site: "ProAthleteLife.com", dr: 76, traffic: "84K/mo", price: "$730", status: "active" },
            { niche: "Entertainment", site: "CelebrityNews.net", dr: 69, traffic: "53K/mo", price: "$580", status: "active" },
            { niche: "Fashion", site: "HauteCouture.co", dr: 77, traffic: "88K/mo", price: "$750", status: "active" },
            { niche: "Real Estate", site: "LuxuryProperties.com", dr: 79, traffic: "93K/mo", price: "$780", status: "active" },
            { niche: "Automotive", site: "LuxuryCars.io", dr: 75, traffic: "79K/mo", price: "$720", status: "active" },
            { niche: "Gaming", site: "EsportsElite.net", dr: 80, traffic: "97K/mo", price: "$800", status: "active" },
            { niche: "Parenting", site: "ModernParent.org", dr: 71, traffic: "62K/mo", price: "$650", status: "active" },
            { niche: "Pets", site: "AnimalLovers.com", dr: 68, traffic: "49K/mo", price: "$560", status: "active" },
            { niche: "Home Improvement", site: "RenovationExperts.net", dr: 73, traffic: "67K/mo", price: "$680", status: "active" }
        ];

        // Populate the table
        function populateTable() {
            const tableBody = document.getElementById('tableBody');
            tableBody.innerHTML = '';
            
            tableData.forEach(row => {
                const tr = document.createElement('tr');
                tr.className = 'hover:bg-white/5 transition-colors';
                tr.innerHTML = `
                    <td class="py-4 px-4 font-medium text-white">${row.niche}</td>
                    <td class="py-4 px-4">
                        <div class="flex items-center">
                            <div class="w-2 h-2 bg-green-400 rounded-full mr-2"></div>
                            <span class="text-cyan-300">${row.site}</span>
                        </div>
                    </td>
                    <td class="py-4 px-4 text-right font-bold text-white">${row.dr}</td>
                    <td class="py-4 px-4 text-right text-blue-200">${row.traffic}</td>
                    <td class="py-4 px-4 text-right font-medium text-cyan-300">${row.price}</td>
                    <td class="py-4 px-4 text-right font-medium text-cyan-300">${row.country}</td>
                `;
                tableBody.appendChild(tr);
            });
            
            // Update row counts
            document.getElementById('totalRows').textContent = tableData.length;
            document.getElementById('visibleRows').textContent = Math.min(10, tableData.length);
        }

        // Animation on scroll
        function animateOnScroll() {
            const elements = document.querySelectorAll('.fade-in-up, .fade-in-left, .fade-in-right');
            
            elements.forEach(element => {
                const elementTop = element.getBoundingClientRect().top;
                const elementVisible = 150;
                
                if (elementTop < window.innerHeight - elementVisible) {
                    element.classList.add('animate-in');
                }
            });
        }

        // Navbar scroll effect
        function handleNavbarScroll() {
            const navbar = document.getElementById('navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('nav-scrolled');
            } else {
                navbar.classList.remove('nav-scrolled');
            }
        }

        // Mobile menu functionality
        function initMobileMenu() {
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
        }

        // Initialize everything when DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            populateTable();
            initMobileMenu();
            
            // Initial animation check
            animateOnScroll();
            
            // Event listeners
            window.addEventListener('scroll', () => {
                animateOnScroll();
                handleNavbarScroll();
            });
            
            // Smooth scrolling for anchor links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    
                    const targetId = this.getAttribute('href');
                    if (targetId === '#') return;
                    
                    const targetElement = document.querySelector(targetId);
                    if (targetElement) {
                        window.scrollTo({
                            top: targetElement.offsetTop - 80,
                            behavior: 'smooth'
                        });
                    }
                });
            });
        });

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
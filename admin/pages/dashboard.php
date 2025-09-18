<?php
// dashboard.php - Complete solution with dummy data
?>

<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-800">Dashboard Overview</h2>
    <p class="text-gray-500">Welcome back, <span class="font-semibold"><?= htmlspecialchars($userName) ?></span>! Here's what's happening with your business today.</p>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                <i class="fas fa-globe text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-gray-500">Active Sites</p>
                <h3 class="text-2xl font-bold">24</h3>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-green-100 text-green-600">
                <i class="fas fa-shopping-cart text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-gray-500">Total Orders</p>
                <h3 class="text-2xl font-bold">158</h3>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                <i class="fas fa-clock text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-gray-500">Pending Orders</p>
                <h3 class="text-2xl font-bold">18</h3>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                <i class="fas fa-dollar-sign text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-gray-500">Total Revenue</p>
                <h3 class="text-2xl font-bold">$12,540</h3>
            </div>
        </div>
    </div>
</div>

<!-- Charts and Recent Activity -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Recent Sites -->
    <div class="bg-white shadow-lg rounded-xl p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-gray-800 flex items-center">
                <i class="fas fa-globe mr-2 text-blue-600"></i> Recent Sites
            </h3>
            <a href="#" class="text-sm text-blue-600 hover:underline">View All</a>
        </div>
        <div class="overflow-y-auto max-h-80">
            <div class="flex items-center py-3 border-b">
                <div class="p-2 bg-green-100 text-green-600 rounded-lg mr-3">
                    <i class="fas fa-globe"></i>
                </div>
                <div class="flex-1">
                    <p class="font-medium">TechReview Central</p>
                    <p class="text-sm text-gray-500 truncate">techreviewcentral.com</p>
                </div>
                <div class="text-right">
                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Active</span>
                    <p class="text-xs text-gray-400 mt-1">Jun 11, 2023</p>
                </div>
            </div>
            <div class="flex items-center py-3 border-b">
                <div class="p-2 bg-green-100 text-green-600 rounded-lg mr-3">
                    <i class="fas fa-globe"></i>
                </div>
                <div class="flex-1">
                    <p class="font-medium">Foodie Adventures</p>
                    <p class="text-sm text-gray-500 truncate">foodieadventures.blog</p>
                </div>
                <div class="text-right">
                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Active</span>
                    <p class="text-xs text-gray-400 mt-1">Jun 9, 2023</p>
                </div>
            </div>
            <div class="flex items-center py-3 border-b">
                <div class="p-2 bg-green-100 text-green-600 rounded-lg mr-3">
                    <i class="fas fa-globe"></i>
                </div>
                <div class="flex-1">
                    <p class="font-medium">Travel Diaries</p>
                    <p class="text-sm text-gray-500 truncate">traveldiaries.com</p>
                </div>
                <div class="text-right">
                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Active</span>
                    <p class="text-xs text-gray-400 mt-1">Jun 7, 2023</p>
                </div>
            </div>
            <div class="flex items-center py-3 border-b">
                <div class="p-2 bg-green-100 text-green-600 rounded-lg mr-3">
                    <i class="fas fa-globe"></i>
                </div>
                <div class="flex-1">
                    <p class="font-medium">Fitness Fundamentals</p>
                    <p class="text-sm text-gray-500 truncate">fitnessfundamentals.net</p>
                </div>
                <div class="text-right">
                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Active</span>
                    <p class="text-xs text-gray-400 mt-1">Jun 4, 2023</p>
                </div>
            </div>
            <div class="flex items-center py-3 border-b">
                <div class="p-2 bg-green-100 text-green-600 rounded-lg mr-3">
                    <i class="fas fa-globe"></i>
                </div>
                <div class="flex-1">
                    <p class="font-medium">Finance Wisdom</p>
                    <p class="text-sm text-gray-500 truncate">financewisdom.org</p>
                </div>
                <div class="text-right">
                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Active</span>
                    <p class="text-xs text-gray-400 mt-1">Jun 1, 2023</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Orders -->
    <div class="bg-white shadow-lg rounded-xl p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-gray-800 flex items-center">
                <i class="fas fa-shopping-cart mr-2 text-blue-600"></i> Recent Orders
            </h3>
            <a href="#" class="text-sm text-blue-600 hover:underline">View All</a>
        </div>
        <div class="overflow-y-auto max-h-80">
            <div class="flex items-center py-3 border-b">
                <div class="p-2 bg-blue-100 text-blue-600 rounded-lg mr-3">
                    <i class="fas fa-receipt"></i>
                </div>
                <div class="flex-1">
                    <p class="font-medium">Order #5001</p>
                    <p class="text-sm text-gray-500">John Smith • TechReview Central</p>
                </div>
                <div class="text-right">
                    <p class="font-medium">$249.99</p>
                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Completed</span>
                    <p class="text-xs text-gray-400 mt-1">Jun 12, 2023</p>
                </div>
            </div>
            <div class="flex items-center py-3 border-b">
                <div class="p-2 bg-blue-100 text-blue-600 rounded-lg mr-3">
                    <i class="fas fa-receipt"></i>
                </div>
                <div class="flex-1">
                    <p class="font-medium">Order #5002</p>
                    <p class="text-sm text-gray-500">Emma Johnson • Foodie Adventures</p>
                </div>
                <div class="text-right">
                    <p class="font-medium">$149.50</p>
                    <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                    <p class="text-xs text-gray-400 mt-1">Jun 11, 2023</p>
                </div>
            </div>
            <div class="flex items-center py-3 border-b">
                <div class="p-2 bg-blue-100 text-blue-600 rounded-lg mr-3">
                    <i class="fas fa-receipt"></i>
                </div>
                <div class="flex-1">
                    <p class="font-medium">Order #5003</p>
                    <p class="text-sm text-gray-500">Michael Brown • Travel Diaries</p>
                </div>
                <div class="text-right">
                    <p class="font-medium">$199.00</p>
                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Completed</span>
                    <p class="text-xs text-gray-400 mt-1">Jun 10, 2023</p>
                </div>
            </div>
            <div class="flex items-center py-3 border-b">
                <div class="p-2 bg-blue-100 text-blue-600 rounded-lg mr-3">
                    <i class="fas fa-receipt"></i>
                </div>
                <div class="flex-1">
                    <p class="font-medium">Order #5004</p>
                    <p class="text-sm text-gray-500">Sarah Williams • Fitness Fundamentals</p>
                </div>
                <div class="text-right">
                    <p class="font-medium">$99.99</p>
                    <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">Processing</span>
                    <p class="text-xs text-gray-400 mt-1">Jun 9, 2023</p>
                </div>
            </div>
            <div class="flex items-center py-3 border-b">
                <div class="p-2 bg-blue-100 text-blue-600 rounded-lg mr-3">
                    <i class="fas fa-receipt"></i>
                </div>
                <div class="flex-1">
                    <p class="font-medium">Order #5005</p>
                    <p class="text-sm text-gray-500">Robert Davis • Finance Wisdom</p>
                </div>
                <div class="text-right">
                    <p class="font-medium">$299.50</p>
                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Completed</span>
                    <p class="text-xs text-gray-400 mt-1">Jun 8, 2023</p>
                </div>
            </div>
        </div>
    </div>
</div>



<!-- Chart.js Script -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Revenue Chart
        const revenueCtx = document.getElementById('revenueChart').getContext('2d');
        const revenueChart = new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: ['Jun 7', 'Jun 8', 'Jun 9', 'Jun 10', 'Jun 11', 'Jun 12', 'Jun 13'],
                datasets: [{
                    label: 'Revenue ($)',
                    data: [1240, 1580, 980, 2100, 1750, 1920, 2250],
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 2,
                    tension: 0.3,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            drawBorder: false
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    });
</script>
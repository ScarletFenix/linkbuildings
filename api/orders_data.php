<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole('buyer');
require_once __DIR__ . '/../includes/db.php'; // Database connection

header('Content-Type: application/json');



// ✅ Ensure the user is logged in
$userId = $_SESSION['user_id'] ?? null;
if (!$userId) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

try {
    // --- 📊 Fetch order stats for the logged-in buyer ---
    $statsQuery = $pdo->prepare("
        SELECT
            COUNT(*) AS total,
            SUM(payment_status = 'paid') AS completed,
            SUM(payment_status = 'pending') AS in_progress,
            SUM(payment_status = 'failed') AS cancelled
        FROM orders
        WHERE user_id = :user_id
    ");
    $statsQuery->execute(['user_id' => $userId]);
    $stats = $statsQuery->fetch(PDO::FETCH_ASSOC) ?: [
        'total' => 0,
        'completed' => 0,
        'in_progress' => 0,
        'cancelled' => 0
    ];

    // --- 📋 Fetch recent 5 orders with their site names ---
    $ordersQuery = $pdo->prepare("
        SELECT 
            o.id,
            COALESCE(s.site_url, 'Unknown Site') AS site_url,
            o.created_at,
            o.payment_status,
            o.final_total
        FROM orders o
        LEFT JOIN sites s ON o.site_id = s.id
        WHERE o.user_id = :user_id
        ORDER BY o.created_at DESC
        LIMIT 5
    ");
    $ordersQuery->execute(['user_id' => $userId]);
    $recentOrders = $ordersQuery->fetchAll(PDO::FETCH_ASSOC);

    // --- 📈 Fetch order growth data (past 6 months) ---
    $growthQuery = $pdo->prepare("
        SELECT 
            DATE_FORMAT(created_at, '%b') AS month,
            COUNT(*) AS count
        FROM orders
        WHERE user_id = :user_id
        AND created_at >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
        GROUP BY month
        ORDER BY MIN(created_at)
    ");
    $growthQuery->execute(['user_id' => $userId]);
    $growthData = $growthQuery->fetchAll(PDO::FETCH_ASSOC);

    // Ensure growth data includes empty months if there are gaps
    $months = [];
    for ($i = 5; $i >= 0; $i--) {
        $monthKey = date('M', strtotime("-$i month"));
        $months[$monthKey] = 0;
    }

    foreach ($growthData as $row) {
        $months[$row['month']] = (int)$row['count'];
    }

    $growth = [
        'labels' => array_keys($months),
        'values' => array_values($months)
    ];  

    // --- ✅ Final combined response ---
    echo json_encode([
        'success' => true,
        'stats' => [
            'total' => (int)($stats['total'] ?? 0),
            'completed' => (int)($stats['completed'] ?? 0),
            'in_progress' => (int)($stats['in_progress'] ?? 0),
            'cancelled' => (int)($stats['cancelled'] ?? 0),
        ],
        'recentOrders' => $recentOrders,
        'growth' => $growth
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}
?>

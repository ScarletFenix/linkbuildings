<?php
// api/sites.php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../includes/auth.php';
requireRole('buyer');

require_once __DIR__ . '/../includes/db.php'; // $pdo available

// Pagination settings
$page  = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
$limit = 50;
$offset = ($page - 1) * $limit;

try {
    // Count total active sites
    $countStmt = $pdo->query("SELECT COUNT(*) FROM sites WHERE status = 'active'");
    $totalRows = (int) $countStmt->fetchColumn();
    $totalPages = ceil($totalRows / $limit);

    // Fetch active sites
    $stmt = $pdo->prepare("
        SELECT id, site_name, description, site_img, niche, site_url, price, dr, traffic, country, backlinks, status, created_at
        FROM sites
        WHERE status = 'active'
        ORDER BY created_at DESC
        LIMIT :limit OFFSET :offset
    ");
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();

    $sites = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Process image paths
    foreach ($sites as &$site) {
        $filePath = __DIR__ . '/../uploads/sites/' . $site['site_img'];
        $publicPath = '/linkbuildings/uploads/sites/' . $site['site_img'];

        if (!empty($site['site_img']) && file_exists($filePath)) {
            $site['site_img_url'] = $publicPath;
        } else {
            // fallback if missing
            $site['site_img_url'] = '/linkbuildings/assets/images/placeholder.png';
        }
    }

    // Respond JSON
    header('Content-Type: application/json');
    echo json_encode([
        "page" => $page,
        "per_page" => $limit,
        "total" => $totalRows,
        "total_pages" => $totalPages,
        "data" => $sites
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "Query failed: " . $e->getMessage()]);
}

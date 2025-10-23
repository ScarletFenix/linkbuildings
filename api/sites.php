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
    // ✅ Dynamic filter conditions
    $where = ["status = 'active'"];
    $params = [];

    // Filter by niche
    if (!empty($_GET['niche'])) {
        $where[] = "niche = :niche";
        $params[':niche'] = $_GET['niche'];
    }

    // Filter by country
    if (!empty($_GET['country'])) {
        $where[] = "country = :country";
        $params[':country'] = $_GET['country'];
    }

    // Filter by DR range
    if (isset($_GET['min_dr']) && is_numeric($_GET['min_dr'])) {
        $where[] = "dr >= :min_dr";
        $params[':min_dr'] = $_GET['min_dr'];
    }
    if (isset($_GET['max_dr']) && is_numeric($_GET['max_dr'])) {
        $where[] = "dr <= :max_dr";
        $params[':max_dr'] = $_GET['max_dr'];
    }

    // Filter by Traffic range
    if (isset($_GET['min_traffic']) && is_numeric($_GET['min_traffic'])) {
        $where[] = "traffic >= :min_traffic";
        $params[':min_traffic'] = $_GET['min_traffic'];
    }
    if (isset($_GET['max_traffic']) && is_numeric($_GET['max_traffic'])) {
        $where[] = "traffic <= :max_traffic";
        $params[':max_traffic'] = $_GET['max_traffic'];
    }

    // Search by site name or description
    if (!empty($_GET['search'])) {
        $where[] = "(site_name LIKE :search OR description LIKE :search)";
        $params[':search'] = '%' . $_GET['search'] . '%';
    }

    // Build WHERE clause
    $whereClause = implode(' AND ', $where);

    // ✅ Count total active sites with filters
    $countQuery = "SELECT COUNT(*) FROM sites WHERE $whereClause";
    $countStmt = $pdo->prepare($countQuery);
    foreach ($params as $key => $val) {
        $countStmt->bindValue($key, $val);
    }
    $countStmt->execute();
    $totalRows = (int) $countStmt->fetchColumn();
    $totalPages = ceil($totalRows / $limit);

    // ✅ Fetch active sites + discount info
    $query = "
        SELECT 
            id,
            site_name,
            description,
            site_img,
            niche,
            site_url,
            price,
            dr,
            traffic,
            country,
            backlinks,
            status,
            created_at,
            has_discount,
            discount_start,
            discount_end,
            discount_percent
        FROM sites
        WHERE $whereClause
        ORDER BY created_at DESC
        LIMIT :limit OFFSET :offset
    ";

    $stmt = $pdo->prepare($query);

    // Bind filters
    foreach ($params as $key => $val) {
        $stmt->bindValue($key, $val);
    }

    // Bind pagination
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();

    $sites = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Process image paths & discount logic
    foreach ($sites as &$site) {
        $filePath = __DIR__ . '/../uploads/sites/' . $site['site_img'];
        $publicPath = '/linkbuildings/uploads/sites/' . $site['site_img'];

        if (!empty($site['site_img']) && file_exists($filePath)) {
            $site['site_img_url'] = $publicPath;
        } else {
            $site['site_img_url'] = '/linkbuildings/assets/images/placeholder.png';
        }

        // ✅ Convert discount status for convenience
        $site['is_discount_active'] = false;
        if ($site['has_discount'] == 1 && !empty($site['discount_start']) && !empty($site['discount_end'])) {
            $now = new DateTime();
            $start = new DateTime($site['discount_start']);
            $end = new DateTime($site['discount_end']);
            $site['is_discount_active'] = ($now >= $start && $now <= $end);
        }

        // ✅ Sanitize discount_percent (ensure it's numeric)
        $site['discount_percent'] = isset($site['discount_percent']) 
            ? (float)$site['discount_percent'] 
            : null;
    }

    // Respond JSON
    header('Content-Type: application/json');
    echo json_encode([
        "page" => $page,
        "per_page" => $limit,
        "total" => $totalRows,
        "total_pages" => $totalPages,
        "data" => $sites
    ], JSON_UNESCAPED_SLASHES);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "Query failed: " . $e->getMessage()]);
}

<?php
require_once __DIR__ . '/../../includes/auth.php';
requireRole('admin');
require_once __DIR__ . '/../../includes/db.php';

// --- Pagination setup
$perPageOptions = [25, 50, 100, 250, 500];
$perPage = isset($_GET['per_page']) && in_array((int)$_GET['per_page'], $perPageOptions)
    ? (int)$_GET['per_page']
    : 25;

$page = isset($_GET['p']) ? max(1, (int)$_GET['p']) : 1;
$offset = ($page - 1) * $perPage;

// --- Count total rows
$totalStmt = $pdo->query("SELECT COUNT(*) FROM sites");
$totalRows = (int)$totalStmt->fetchColumn();
$totalPages = ceil($totalRows / $perPage);

// --- Fetch paginated sites
$stmt = $pdo->prepare("SELECT * FROM sites ORDER BY id DESC LIMIT :limit OFFSET :offset");
$stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$sites = $stmt->fetchAll(PDO::FETCH_ASSOC);

$no = $offset + 1;
?>

<div class="overflow-x-auto bg-white rounded-lg shadow p-6 mt-4" id="sitesTableWrapper">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-bold">All Sites</h2>
        <input type="text" id="searchInput" placeholder="Search..." class="border rounded p-2 w-1/3">
    </div>

    <table class="min-w-full border border-gray-200" id="sitesTable">
        <thead>
            <tr class="bg-gray-100 text-center">
                <th class="p-2 border">No</th>
                <th class="p-2 border">Site Name</th>
                <th class="p-2 border">Image</th>
                <th class="p-2 border">Description</th>
                <th class="p-2 border">Niche</th>
                <th class="p-2 border">Site URL</th>
                <th class="p-2 border">Price</th>
                <th class="p-2 border">DR</th>
                <th class="p-2 border">Traffic</th>
                <th class="p-2 border">Country</th>
                <th class="p-2 border">Backlinks</th>
                <th class="p-2 border">Status</th>
                <th class="p-2 border">Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php if (!empty($sites)): ?>
            <?php foreach ($sites as $site): ?>
                <tr>
                    <td class="p-2 border text-center"><?= $no++ ?></td>
                    <td class="p-2 border"><?= htmlspecialchars($site['site_name']) ?></td>
                    <!-- Site Image -->
                    <td class="p-2 border text-center">
                        <?php if (!empty($site['site_img']) && file_exists(__DIR__ . '/../../uploads/sites/' . $site['site_img'])): ?>
                            <img src="/linkbuildings/uploads/sites/<?= htmlspecialchars($site['site_img']) ?>" alt="Site Image" class="h-16 w-16 object-cover mx-auto rounded">
                        <?php else: ?>
                            <span class="text-gray-500 text-xs">No Image</span>
                        <?php endif; ?>
                    </td>
                    <td class="p-2 border" title="<?= htmlspecialchars($site['description']) ?>">
                        <?= htmlspecialchars(strlen($site['description']) > 50 ? substr($site['description'], 0, 50) . '...' : $site['description']) ?>
                    </td>
                    <td class="p-2 border">
                        <?php
                        $niches = array_map('trim', explode(',', $site['niche']));
                        $chunks = [];
                        $pattern = [3, 2]; 
                        $rowIndex = 0;
                        $i = 0;
                        while ($i < count($niches)) {
                            $count = $pattern[$rowIndex % 2]; 
                            $chunks[] = array_slice($niches, $i, $count);
                            $i += $count;
                            $rowIndex++;
                        }
                        ?>
                        <div class="flex flex-col gap-1">
                            <?php foreach ($chunks as $row): ?>
                                <div class="flex flex-wrap gap-1">
                                    <?php foreach ($row as $n): ?>
                                        <span class="bg-blue-50 text-blue-700 px-2 py-0.5 rounded-full text-xs">
                                            <?= htmlspecialchars($n) ?>
                                        </span>
                                    <?php endforeach; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </td>
                    <td class="p-2 border">
                        <a href="<?= htmlspecialchars($site['site_url']) ?>" target="_blank" class="text-blue-600 hover:underline">
                            <?= htmlspecialchars($site['site_url']) ?>
                        </a>
                    </td>
                    <!-- Price | dr | traffic | country | backlinks -->
                    <td class="p-2 border">€<?= htmlspecialchars($site['price']) ?></td>
                    <td class="p-2 border"><?= htmlspecialchars($site['dr']) ?></td>
                    <td class="p-2 border"><?= htmlspecialchars($site['traffic']) ?></td>
                    <td class="p-2 border"><?= htmlspecialchars($site['country']) ?></td>
                    <td class="p-2 border"><?= htmlspecialchars($site['backlinks']) ?></td>
                    <td class="p-2 border text-center">
                        <span class="<?= $site['status']=='active' ? 'text-green-700 bg-green-100' : 'text-red-700 bg-red-100' ?> px-2 py-1 rounded-full text-xs">
                            <?= ucfirst($site['status']) ?>
                        </span>
                    </td>
                    <!-- Actions Buttons -->
                    <td class="p-2 border text-center text-xs">
    <div class="flex justify-center gap-1 mb-1">
        <button class="editBtn bg-yellow-500 text-white px-2 py-1 rounded hover:bg-yellow-600" data-id="<?= $site['id'] ?>">Edit</button>
        <button class="deleteBtn bg-red-600 text-white px-2 py-1 rounded hover:bg-red-700" data-id="<?= $site['id'] ?>">Delete</button>
    </div>
    <div>
        <button class="toggleStatusBtn bg-blue-600 text-white px-2 py-1 rounded hover:bg-blue-700" 
            data-id="<?= $site['id'] ?>" 
            data-status="<?= $site['status'] ?>">
            <?= $site['status'] == 'active' ? 'Deactivate' : 'Activate' ?>
        </button>
    </div>
</td>

                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="13" class="p-2 text-center text-gray-500">No sites found.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>

    <!-- Pagination controls -->
    <div class="flex justify-between items-center mt-4">
        <div class="flex items-center gap-2">
            <label for="perPage">Rows per page:</label>
            <select id="perPage" class="border rounded p-1">
                <?php foreach ($perPageOptions as $opt): ?>
                    <option value="<?= $opt ?>" <?= $perPage == $opt ? 'selected' : '' ?>><?= $opt ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="space-x-1" id="paginationWrapper">
            <?php for($i=1; $i<=$totalPages; $i++): ?>
                <button type="button" class="page-btn px-2 py-1 border rounded <?= $i==$page?'bg-gray-300':'' ?>" data-page="<?= $i ?>"><?= $i ?></button>
            <?php endfor; ?>
        </div>
    </div>
</div>

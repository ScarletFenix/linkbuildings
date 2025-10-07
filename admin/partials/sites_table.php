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
                <th class="p-2 border">Discount</th>
                <th class="p-2 border">Status</th>
                <th class="p-2 border">Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php if (!empty($sites)): ?>
            <?php foreach ($sites as $site): ?>
                <?php
                $now = date('Y-m-d H:i:s');
                $discountStatus = '—';
                $countdown = '';

                if ($site['has_discount'] && $site['discount_start'] && $site['discount_end']) {
                    if ($now < $site['discount_start']) {
                        $discountStatus = 'Upcoming';
                        $countdown = '';
                    } elseif ($now >= $site['discount_start'] && $now <= $site['discount_end']) {
                        $discountStatus = 'Active';
                        $countdown = $site['discount_end'];
                    } else {
                        $discountStatus = 'Expired';
                    }
                }
                ?>
                <tr>
                    <td class="p-2 border text-center"><?= $no++ ?></td>
                    <td class="p-2 border"><?= htmlspecialchars($site['site_name']) ?></td>
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
                    <td class="p-2 border">€<?= htmlspecialchars($site['price']) ?></td>
                    <td class="p-2 border"><?= htmlspecialchars($site['dr']) ?></td>
                    <td class="p-2 border"><?= htmlspecialchars($site['traffic']) ?></td>
                    <td class="p-2 border"><?= htmlspecialchars($site['country']) ?></td>
                    <td class="p-2 border"><?= htmlspecialchars($site['backlinks']) ?></td>

                    <!-- Discount column -->
                    <td class="p-2 border text-center">
                        <div class="discount-status" data-end="<?= htmlspecialchars($countdown) ?>" data-status="<?= $discountStatus ?>">
                            <?php if ($discountStatus === 'Active'): ?>
                                <span class="status-label text-green-700 bg-green-100 px-2 py-1 rounded-full text-xs"><?= $discountStatus ?></span>
                                <div class="countdown font-bold text-red-600 text-xs mt-1"></div>
                            <?php elseif ($discountStatus === 'Upcoming'): ?>
                                <span class="status-label text-yellow-700 bg-yellow-100 px-2 py-1 rounded-full text-xs"><?= $discountStatus ?></span>
                            <?php elseif ($discountStatus === 'Expired'): ?>
                                <span class="status-label text-red-700 bg-red-100 px-2 py-1 rounded-full text-xs"><?= $discountStatus ?></span>
                            <?php else: ?>
                                <span class="text-gray-500 text-xs">—</span>
                            <?php endif; ?>
                        </div>
                    </td>

                    <td class="p-2 border text-center">
                        <span class="<?= $site['status']=='active' ? 'text-green-700 bg-green-100' : 'text-red-700 bg-red-100' ?> px-2 py-1 rounded-full text-xs">
                            <?= ucfirst($site['status']) ?>
                        </span>
                    </td>

                    <td class="p-2 border text-center text-xs">
                        <div class="flex justify-center gap-1 mb-1">
                            <button 
                                class="editBtn bg-yellow-500 text-white px-2 py-1 rounded hover:bg-yellow-600"
                                data-id="<?= $site['id'] ?>"
                                data-site_name="<?= htmlspecialchars($site['site_name'], ENT_QUOTES) ?>"
                                data-niche="<?= htmlspecialchars($site['niche'], ENT_QUOTES) ?>"
                                data-site_url="<?= htmlspecialchars($site['site_url'], ENT_QUOTES) ?>"
                                data-price="<?= htmlspecialchars($site['price'], ENT_QUOTES) ?>"
                                data-dr="<?= htmlspecialchars($site['dr'], ENT_QUOTES) ?>"
                                data-traffic="<?= htmlspecialchars($site['traffic'], ENT_QUOTES) ?>"
                                data-country="<?= htmlspecialchars($site['country'], ENT_QUOTES) ?>"
                                data-backlinks="<?= htmlspecialchars($site['backlinks'], ENT_QUOTES) ?>"
                                data-description="<?= htmlspecialchars($site['description'], ENT_QUOTES) ?>"
                                data-has_discount="<?= $site['has_discount'] ?>"
                                data-discount_start="<?= $site['discount_start'] ?>"
                                data-discount_end="<?= $site['discount_end'] ?>"
                            >Edit</button>
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
            <tr><td colspan="14" class="p-2 text-center text-gray-500">No sites found.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>

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

<script>
// Countdown timer — auto-switch to "Expired"
document.querySelectorAll('.discount-status').forEach(wrapper => {
    const end = wrapper.dataset.end ? new Date(wrapper.dataset.end).getTime() : null;
    const countdownEl = wrapper.querySelector('.countdown');
    const label = wrapper.querySelector('.status-label');

    if (!end || !countdownEl || !label) return;

    const tick = () => {
        const now = new Date().getTime();
        const diff = end - now;

        if (diff <= 0) {
            label.textContent = 'Expired';
            label.className = 'status-label text-red-700 bg-red-100 px-2 py-1 rounded-full text-xs';
            countdownEl.textContent = 'Expired';
            countdownEl.className = 'countdown font-bold text-red-600 text-xs mt-1';
            clearInterval(interval);
            return;
        }

        const hours = Math.floor((diff % (1000*60*60*24)) / (1000*60*60));
        const minutes = Math.floor((diff % (1000*60*60)) / (1000*60));
        const seconds = Math.floor((diff % (1000*60)) / 1000);
        countdownEl.textContent = `${hours}h ${minutes}m ${seconds}s`;
    };

    tick();
    const interval = setInterval(tick, 1000);
});

// existing edit button logic unchanged
document.querySelectorAll('.editBtn').forEach(btn => {
    btn.addEventListener('click', () => {
        const form = document.getElementById('editForm');
        form.id.value = btn.dataset.id;
        form.site_name.value = btn.dataset.site_name;
        form.niche.value = btn.dataset.niche;
        form.site_url.value = btn.dataset.site_url;
        form.price.value = btn.dataset.price;
        form.dr.value = btn.dataset.dr;
        form.traffic.value = btn.dataset.traffic;
        form.country.value = btn.dataset.country;
        form.backlinks.value = btn.dataset.backlinks;
        form.description.value = btn.dataset.description;

        const discountFields = document.getElementById('edit_discount_fields');
        form.has_discount.checked = btn.dataset.has_discount === "1";
        discountFields.classList.toggle('hidden', btn.dataset.has_discount !== "1");

        if (btn.dataset.discount_start) {
            form.discount_start.value = btn.dataset.discount_start.replace(' ', 'T').slice(0, 16);
        } else {
            form.discount_start.value = '';
        }
        if (btn.dataset.discount_end) {
            form.discount_end.value = btn.dataset.discount_end.replace(' ', 'T').slice(0, 16);
        } else {
            form.discount_end.value = '';
        }

        const previewImg = document.getElementById('editPreviewImg');
        previewImg.src = '';
        previewImg.classList.add('hidden');

        document.getElementById('editModal').classList.remove('hidden');
    });
});

document.getElementById('edit_has_discount').addEventListener('change', function() {
    document.getElementById('edit_discount_fields').classList.toggle('hidden', !this.checked);
});

document.getElementById('closeEditModal').addEventListener('click', () => {
    document.getElementById('editModal').classList.add('hidden');
});
</script>

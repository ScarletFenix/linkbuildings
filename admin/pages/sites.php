<?php
// sites.php
require_once __DIR__ . '/../../includes/db.php';

// Country & Niche lists
$countries = ["Germany","Spain","Italy","Austria","Sweden","Norway","Denmark","Finland","Portugal","Brazil",
              "Belgium","Netherlands","Romania","Poland","UK","France","Greece","Hungary","Slovakia","Slovenia"];

$nichesList = [
    'automotive','beauty','business & e-business','computer games','construction',
'cooking','culture & art','diet & weight loss','entertainment','fashion & clothing',
'family, kids & pregnancy','finance, banking & insurance','health & medical',
'home & garden & interior','technology','music','real estate','travel, tours & hotels',
'sports & fitness','agriculture & forestry','wedding','education & science',
'dating & relationships','food & drink','e-commerce & shopping','news & media',
'pets','films & TV','jobs & careers','nature & hobbies'

];
?>

<!-- Form Wrapper Start -->
<div id="siteFormWrapper" class="bg-white rounded-lg shadow p-6 max-w-full mx-auto">
    <h2 class="text-xl font-bold mb-4">Add New Site</h2>

    <div id="formAlert"></div> <!-- AJAX alerts will show here -->

    <form id="siteForm" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-3 gap-4">

        <!-- Site Name -->
        <div>
            <label class="block text-sm font-medium">Site Name <span class="text-red-600">*</span></label>
            <input type="text" name="site_name" class="w-full border rounded p-2" required>
        </div>

        <!-- Niche -->
        <div>
            <label class="block text-sm font-medium">Niche <span class="text-red-600">*</span></label>
            <div id="nicheSelect" class="w-full border rounded p-2 relative">
                <div id="selectedNiches" class="flex flex-wrap gap-2 mb-2"></div>
                <input type="text" id="nicheSearch" placeholder="Search niches..." class="w-full border rounded p-2">
                <div id="nicheOptions" class="absolute z-10 bg-white border rounded w-full mt-1 max-h-40 overflow-y-auto hidden"></div>
            </div>
            <input type="hidden" name="niche" id="nicheHidden">
        </div>

        <!-- Site URL -->
        <div>
            <label class="block text-sm font-medium">Site URL <span class="text-red-600">*</span></label>
            <input type="url" name="site_url" class="w-full border rounded p-2" required>
        </div>

        <!-- Price -->
        <div>
            <label class="block text-sm font-medium">Price (€) <span class="text-red-600">*</span></label>
            <input type="number" step="0.01" name="price" min="0" class="w-full border rounded p-2" required>
        </div>

        <!-- DR -->
        <div>
            <label class="block text-sm font-medium">DR <span class="text-red-600">*</span></label>
            <input type="number" name="dr" min="0" class="w-full border rounded p-2" required>
        </div>

        <!-- Traffic -->
        <div>
            <label class="block text-sm font-medium">Traffic <span class="text-red-600">*</span></label>
            <input type="number" name="traffic" min="0" class="w-full border rounded p-2" required>
        </div>

        <!-- Country -->
        <div>
            <label class="block text-sm font-medium">Country <span class="text-red-600">*</span></label>
            <select name="country" class="w-full border rounded p-2" required>
                <option value="">-- Select Country --</option>
                <?php foreach($countries as $c): ?>
                    <option value="<?= htmlspecialchars($c) ?>"><?= htmlspecialchars($c) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Backlinks -->
        <div>
            <label class="block text-sm font-medium">Backlinks <span class="text-red-600">*</span></label>
            <input type="number" name="backlinks" min="0" class="w-full border rounded p-2" required>
        </div>

        <!-- Site Image -->
        <div>
            <label class="block text-sm font-medium">Site Image <span class="text-red-600">*</span></label>
            <input type="file" name="site_img" class="w-full border rounded p-2" required>
        </div>

        <!-- Description -->
        <div class="md:col-span-3">
            <label class="block text-sm font-medium">Description <span class="text-red-600">*</span></label>
            <textarea name="description" rows="4" class="w-full border rounded p-2" required></textarea>
        </div>

        <!-- Submit -->
        <div class="md:col-span-3">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Add Site</button>
        </div>
    </form>
</div>
<!-- Form Wrapper End -->
<!-- Table Wrapper Start -->
    <div id="sitesTableWrapper">
        <?php include __DIR__ . "/../partials/sites_table.php"; ?>
    </div>
<!-- Table Wrapper End -->
<!-- Table Wrapper End -->


<!-- Edit Modal -->
<div id="editModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
  <div class="bg-white rounded-lg shadow p-6 w-full max-w-2xl">
    <h2 class="text-xl font-bold mb-4">Edit Site</h2>
    <form id="editForm" enctype="multipart/form-data" class="grid grid-cols-2 gap-4">
      <input type="hidden" name="id">

      <div>
        <label class="block text-sm font-medium">Site Name</label>
        <input type="text" name="site_name" class="w-full border rounded p-2" required>
      </div>
      <div>
        <label class="block text-sm font-medium">Niche</label>
        <input type="text" name="niche" class="w-full border rounded p-2" required>
      </div>
      <div>
        <label class="block text-sm font-medium">Site URL</label>
        <input type="url" name="site_url" class="w-full border rounded p-2" required>
      </div>
      <div>
        <label class="block text-sm font-medium">Price (€)</label>
        <input type="number" step="0.01" name="price" class="w-full border rounded p-2" required>
      </div>
      <div>
        <label class="block text-sm font-medium">DR</label>
        <input type="number" name="dr" class="w-full border rounded p-2" required>
      </div>
      <div>
        <label class="block text-sm font-medium">Traffic</label>
        <input type="number" name="traffic" class="w-full border rounded p-2" required>
      </div>
      <div>
        <label class="block text-sm font-medium">Country</label>
        <input type="text" name="country" class="w-full border rounded p-2" required>
      </div>
      <div>
        <label class="block text-sm font-medium">Backlinks</label>
        <input type="number" name="backlinks" class="w-full border rounded p-2" required>
      </div>
      <div class="col-span-2">
        <label class="block text-sm font-medium">Description</label>
        <textarea name="description" rows="3" class="w-full border rounded p-2" required></textarea>
      </div>
      <div class="col-span-2">
        <label class="block text-sm font-medium">Site Image</label>
        <!-- 📌 File input -->
        <input type="file" name="site_img" class="w-full border rounded p-2">
        <!-- 📌 Preview -->
        <img id="editPreviewImg" src="" alt="Current Site Image" class="mt-2 max-h-32 hidden border rounded">
      </div>

      <div class="col-span-2 flex justify-end gap-2 mt-4">
        <button type="button" id="closeEditModal" class="bg-gray-500 text-white px-4 py-2 rounded">Cancel</button>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Update</button>
      </div>
    </form>
  </div>
</div>

<!-- End Edit Modal -->




<script>
// ========== Niche Selector ==========
const niches = <?= json_encode($nichesList) ?>;
const nicheInput = document.getElementById('nicheSearch');
const nicheOptions = document.getElementById('nicheOptions');
const selectedNichesDiv = document.getElementById('selectedNiches');
const hiddenInput = document.getElementById('nicheHidden');

let selectedNiches = [];

function renderOptions() {
    const val = nicheInput.value.toLowerCase();
    nicheOptions.innerHTML = '';
    niches.filter(n => n.toLowerCase().includes(val) && !selectedNiches.includes(n))
           .forEach(n => {
               const div = document.createElement('div');
               div.textContent = n;
               div.className = 'p-2 hover:bg-gray-200 cursor-pointer';
               div.onclick = () => {
                   if(selectedNiches.length < 5) selectedNiches.push(n);
                   renderSelected();
                   nicheInput.value = '';
                   nicheOptions.classList.add('hidden');
               };
               nicheOptions.appendChild(div);
           });
    nicheOptions.classList.toggle('hidden', nicheOptions.innerHTML === '');
}

function renderSelected() {
    selectedNichesDiv.innerHTML = '';
    selectedNiches.forEach(n => {
        const span = document.createElement('span');
        span.textContent = n + ' ×';
        span.className = 'bg-blue-100 text-blue-700 px-2 py-1 rounded-full cursor-pointer';
        span.onclick = () => { selectedNiches = selectedNiches.filter(x => x !== n); renderSelected(); };
        selectedNichesDiv.appendChild(span);
    });
    hiddenInput.value = selectedNiches.join(',');
}

nicheInput.addEventListener('input', renderOptions);
nicheInput.addEventListener('focus', renderOptions);
document.addEventListener('click', e => {
    if (!e.target.closest('#nicheSelect')) nicheOptions.classList.add('hidden');
});

// ========== AJAX Form ==========
document.getElementById("siteForm").addEventListener("submit", function(e) {
    e.preventDefault();
    const formData = new FormData(this);

    fetch("/linkbuildings/admin/ajax/sites_add.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        const alertBox = document.getElementById("formAlert");
        alertBox.innerHTML = `<div class="mb-4 p-3 rounded ${data.success ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'}">${data.message}</div>`;

        if (data.success) {
            // Reset form
            this.reset();
            selectedNiches = [];
            renderSelected();
            setTimeout(() => alertBox.innerHTML = '', 3000);

            // 🔄 Reload the table partial
            fetch("/linkbuildings/admin/partials/sites_table.php")
                .then(r => r.text())
                .then(html => {
                    document.getElementById("sitesTableWrapper").innerHTML = html;
                })
                .catch(err => console.error("Table Reload Error:", err));
        }
    })
    .catch(err => console.error("AJAX Error:", err));
});

</script>

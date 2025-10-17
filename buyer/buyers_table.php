<?php 
require_once __DIR__ . '/../includes/auth.php'; 
requireRole('buyer'); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Buyer Dashboard</title>
  <link rel="stylesheet" href="/linkbuildings/assets/styles.css">
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    .capsule {
      display: inline-block;
      background-color: #e0f2fe;
      color: #0369a1;
      padding: 4px 10px;
      margin: 3px;
      border-radius: 9999px;
      font-size: 0.75rem;
      font-weight: 600;
    }
    .expandable {
      max-height: 0;
      overflow: hidden;
      transition: max-height 0.4s ease, opacity 0.4s ease, padding 0.3s ease;
      opacity: 0;
    }
    .expandable.show {
      max-height: 1200px;
      opacity: 1;
      padding: 1rem 0;
    } 
    thead th {
      height: 60px;
      vertical-align: middle;
    }
  </style>
</head>
<body class="p-6 bg-gray-50">

<div class="max-w-7xl mx-auto">
  <!-- Table -->
  <div class="overflow-x-auto rounded-lg border border-gray-200 max-h-[800px] overflow-y-auto">
  <table class="w-full text-xs sm:text-sm text-gray-800 text-center">
    <thead class="bg-gradient-to-r from-blue-600 to-green-500 text-white uppercase tracking-wider sticky top-0 z-50 shadow">
      <tr>
        <th class="px-3 py-3">Niche</th>
        <th class="px-3 py-3">Discount</th>
        <th class="px-3 py-3">Site</th>
        <th class="px-3 py-3">Country</th>
        <th class="px-3 py-3">DR</th>
        <th class="px-3 py-3">Traffic</th>
        <th class="px-3 py-3">Backlinks</th>
        <th class="px-3 py-3">Price</th>
        <th class="px-3 py-3">Action</th>
      </tr>
    </thead>
    <tbody id="tableBody" class="bg-white divide-y divide-gray-200"></tbody>
  </table>
</div>

  <!-- Pagination -->
  <div id="pagination" class="flex justify-center mt-4 space-x-1 text-sm"></div>
</div>

<script>  
document.addEventListener("click", async function(e) {
  if (e.target.classList.contains("buy-now")) {
    e.preventDefault();

    const btn = e.target;
    const siteId = btn.getAttribute("data-site-id");

    try {
      const res = await fetch("/linkbuildings/cart/add_to_cart.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "site_id=" + siteId
      });

      const data = await res.json();

      if (data.success) {
        document.getElementById("cart-count").innerText = data.cart_count;
        btn.innerText = "✅ Added";
        btn.classList.remove("bg-green-600", "hover:bg-green-700");
        btn.classList.add("bg-gray-500", "cursor-not-allowed");

        setTimeout(() => {
          btn.innerText = "Buy Now";
          btn.classList.add("bg-green-600", "hover:bg-green-700");
          btn.classList.remove("bg-gray-500", "cursor-not-allowed");
        }, 2000);
      }
    } catch (err) {
      console.error("Error:", err);
    }
  }
});

// Country mapping
const countryCodes = {
  "Germany": "de","Spain": "es","Italy": "it","Austria": "at","Sweden": "se",
  "Norway": "no","Denmark": "dk","Finland": "fi","Portugal": "pt","Brazil": "br",
  "Belgium": "be","Netherlands": "nl","Romania": "ro","Poland": "pl","UK": "gb",
  "France": "fr","Greece": "gr","Hungary": "hu","Slovakia": "sk","Slovenia": "si"
};

async function loadSites(page = 1) {
  try {
    const res = await fetch(`/linkbuildings/api/sites.php?page=${page}`);
    if (!res.ok) throw new Error(`HTTP error ${res.status}`);
    const data = await res.json();

    const tbody = document.getElementById("tableBody");
    tbody.innerHTML = "";

    data.data.forEach(site => {
      let niches = site.niche ? site.niche.split(",").map(n => n.trim()) : [];
      let limitedNiches = niches.slice(0, 3); 
      let formattedNiches = formatNiches(limitedNiches);

      const row = document.createElement("tr");
      row.className = "hover:bg-gray-50 cursor-pointer";

      let discountHTML = `<span class='text-gray-500'>—</span>`;
      if (site.has_discount == 1 && site.discount_start && site.discount_end) {
        const now = new Date();
        const startTime = new Date(site.discount_start);
        const endTime = new Date(site.discount_end);
        const timerId = `timer-${site.id}`;

        if (now < startTime) {
          // Upcoming
          discountHTML = `
            <div class="flex flex-col items-center">
              <span class="text-yellow-600 font-semibold">Upcoming</span>
              <span class="text-xs text-gray-500">Starts: ${startTime.toLocaleString()}</span>
            </div>
          `;
        } else if (now >= startTime && now < endTime) {
          // ✅ Active → show discount percent instead of “Active”
          const percent = site.discount_percent && site.discount_percent != 0
            ? parseFloat(site.discount_percent).toString().replace(/\.0+$/, "")
            : null;

          if (percent) {
            discountHTML = `
              <div class="flex flex-col items-center">
                <span class="bg-green-100 text-green-700 font-semibold px-2 py-0.5 rounded-md text-xs">${percent}% OFF</span>
                <span id="${timerId}" class="text-xs font-bold text-red-600 mt-1"></span>
              </div>
            `;
          } else {
            discountHTML = `
              <div class="flex flex-col items-center">
                <span class="bg-green-100 text-green-700 font-semibold px-2 py-0.5 rounded-md text-xs">Active</span>
                <span id="${timerId}" class="text-xs font-bold text-red-600 mt-1"></span>
              </div>
            `;
          }
          setTimeout(() => startCountdown(site.discount_end, timerId), 100);
        } else {
          // Expired
          discountHTML = `
            <div class="flex flex-col items-center">
              <span class="text-gray-500 font-semibold">Expired</span>
            </div>
          `;
        }
      }

      row.innerHTML = `
        <td class="px-2 py-3"><div class="niche-container">${formattedNiches}</div>
          ${niches.length > 6 ? `<a href="#" class="toggle-niches text-blue-600 text-xs ml-2">View More</a>` : ""}
        </td>
        <td class="px-2 py-3">${discountHTML}</td>
        <td class="px-2 py-3 font-semibold">${site.site_name}</td>
        <td class="px-2 py-3">
          <div class="flex flex-col items-center">
            <img src="https://flagcdn.com/24x18/${countryCodes[site.country] || site.country.toLowerCase()}.png" 
                 alt="${site.country} flag" class="mb-1">
            <span class="text-xs">${site.country}</span>
          </div>
        </td>
        <td class="px-2 py-3 font-bold">${site.dr}</td>
        <td class="px-2 py-3">${Number(site.traffic).toLocaleString()}</td>
        <td class="px-2 py-3">${site.backlinks}</td>
        <td class="px-2 py-3 font-bold">€${site.price}</td>
        <td class="px-2 py-3">
          <button class="buy-now bg-green-600 text-white px-3 py-1 text-xs rounded hover:bg-green-700"
                  data-site-id="${site.id}">
            Buy Now
          </button>
        </td>
      `;

      const detailsRow = document.createElement("tr");
      detailsRow.classList.add("details-row");
      detailsRow.style.display = "none"; 
      detailsRow.innerHTML = `
        <td colspan="9" class="p-8 bg-gray-50">
          <div class="expandable rounded-lg">
            <div class="flex items-center justify-center gap-4 mb-4 bg-gray-50 border border-gray-200 rounded-md p-3">
              <h3 class="text-lg font-semibold">Overview:</h3>
              <div>
                <a href="${site.site_url}" target="_blank" class="text-blue-600 underline">${site.site_url}</a>
              </div>
            </div>
            <div class="grid grid-cols-3 gap-4 text-center mb-6">
              <div class="p-4 bg-white shadow rounded-lg">
                <div class="text-sm font-bold text-gray-600">DR
                </div>
                <div class="text-2xl font-semibold text-blue-600">${site.dr}</div>
              </div>
              <div class="p-4 bg-white shadow rounded-lg">
                <div class="text-sm font-bold text-gray-600">Traffic</div>
                <div class="text-2xl font-semibold text-green-600">${Number(site.traffic).toLocaleString()}</div>
              </div>
              <div class="p-4 bg-white shadow rounded-lg">
                <div class="text-sm font-bold text-gray-600">Backlinks</div>
                <div class="text-2xl font-semibold text-purple-600">${site.backlinks}</div>
              </div>    
            </div>
            <div class="grid grid-cols-2 gap-6 mb-6">
              <div class="w-full h-72 bg-white shadow rounded-lg flex items-center justify-center">
                <img src="${site.site_img_url}" alt="${site.site_name}" class="object-cover w-full h-full rounded-lg">
              </div>
              <div class="bg-white p-6 shadow rounded-lg text-left flex flex-col">
                <div class="font-semibold mb-3 text-lg text-center">Description</div>
                <div class="text-sm text-gray-700 leading-relaxed overflow-y-auto max-h-48 pr-2">
                  ${site.description || "No description available."}
                </div>
              </div>
            </div>
            <div class="bg-white p-4 shadow rounded-lg text-sm text-gray-700">
              <p>The data presented in this report have been aggregated from two industry‐leading platforms: <strong>Ahrefs</strong> and <strong>SearchAPI</strong>.</p>
              <p class="mt-2">Please note that these results are updated monthly and may be subject to minor discrepancies.</p>
            </div>
          </div>
        </td>
      `;

      // Expand/collapse
      row.addEventListener("click", () => {
        const expandable = detailsRow.querySelector(".expandable");
        const isVisible = detailsRow.style.display === "table-row";

        document.querySelectorAll(".details-row").forEach(r => {
          r.style.display = "none";
          r.querySelector(".expandable").classList.remove("show");
        });

        if (!isVisible) {
          detailsRow.style.display = "table-row";
          requestAnimationFrame(() => expandable.classList.add("show"));
        }
      });

      // Niche toggle
      const toggleNiches = row.querySelector(".toggle-niches");
      if (toggleNiches) {
        toggleNiches.addEventListener("click", (e) => {
          e.stopPropagation();
          e.preventDefault();
          const container = row.querySelector(".niche-container");
          if (toggleNiches.textContent === "View More") {
            container.innerHTML = formatNiches(niches);
            toggleNiches.textContent = "View Less";
          } else {
            container.innerHTML = formatNiches(limitedNiches);
            toggleNiches.textContent = "View More";
          }
        });
      }

      tbody.appendChild(row);
      tbody.appendChild(detailsRow);
    });

    // Pagination
    const pagination = document.getElementById("pagination");
    pagination.innerHTML = "";
    for (let i = 1; i <= data.total_pages; i++) {
      const btn = document.createElement("button");
      btn.innerText = i;
      btn.className = `px-3 py-1 border rounded ${
        i === data.page ? "bg-blue-600 text-white" : "bg-gray-100 hover:bg-gray-200"
      }`;
      btn.onclick = () => loadSites(i);
      pagination.appendChild(btn);
    }
  } catch (err) {
    console.error("Failed to load sites:", err);
  }
}

// Countdown Timer
function startCountdown(endTime, elementId) {
  const target = new Date(endTime).getTime();
  const el = document.getElementById(elementId);
  if (!el) return;

  const badge = el.previousElementSibling;
  
  const timer = setInterval(() => {
    const now = new Date().getTime();
    const diff = target - now;

    if (diff <= 0) {
      clearInterval(timer);
      el.textContent = "Expired";
      el.style.color = "#6b7280";
      if (badge) {
        badge.textContent = "Expired";
        badge.style.color = "#6b7280";
      }
      return;
    }

    const d = Math.floor(diff / (1000 * 60 * 60 * 24));
    const h = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    const m = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
    const s = Math.floor((diff % (1000 * 60)) / 1000);

    el.textContent = `${d}d ${h}h ${m}m ${s}s`;
  }, 1000);
}

function formatNiches(niches) {
  let html = "";
  for (let i = 0; i < niches.length; i++) {
    if (i % 3 === 0) html += "<div class='flex flex-wrap justify-center'>";
    html += `<span class="capsule">${niches[i]}</span>`;
    if ((i + 1) % 3 === 0 || i === niches.length - 1) html += "</div>";
  }
  return html;
}

loadSites();
</script>
</body>
</html>

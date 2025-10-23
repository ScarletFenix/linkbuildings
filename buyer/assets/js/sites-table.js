;(function () {
  // ✅ Prevent redeclaration of global mappings if script is reloaded by AJAX
  if (!window.sitesTableLoaded) {
    window.sitesTableLoaded = true;
  }

  // ✅ Country mapping (only runs once)
  if (typeof window.countryCodes === "undefined") {
    window.countryCodes = {
      "Germany": "de","Spain": "es","Italy": "it","Austria": "at","Sweden": "se",
      "Norway": "no","Denmark": "dk","Finland": "fi","Portugal": "pt","Brazil": "br",
      "Belgium": "be","Netherlands": "nl","Romania": "ro","Poland": "pl","UK": "gb",
      "France": "fr","Greece": "gr","Hungary": "hu","Slovakia": "sk","Slovenia": "si"
    };
  }


// ✅ Collect current filters
function getFilters() {
  return {
    niche: document.getElementById("filter-niche").value.trim(),
    country: document.getElementById("filter-country").value.trim(),
    min_dr: document.getElementById("filter-min-dr").value.trim(),
    max_dr: document.getElementById("filter-max-dr").value.trim(),
    min_traffic: document.getElementById("filter-min-traffic").value.trim(),
    max_traffic: document.getElementById("filter-max-traffic").value.trim(),
    search: document.getElementById("filter-search").value.trim()
  };
}

async function loadSites(page = 1) {
  try {
    const filters = getFilters();
    const query = new URLSearchParams({ page, ...filters }).toString();
    const res = await fetch(`/linkbuildings/api/sites.php?${query}`);
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
          discountHTML = `
            <div class="flex flex-col items-center">
              <span class="text-yellow-600 font-semibold">Upcoming</span>
              <span class="text-xs text-gray-500">Starts: ${startTime.toLocaleString()}</span>
            </div>
          `;
        } else if (now >= startTime && now < endTime) {
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
          <a href="/linkbuildings/checkout.php?site_id=${site.id}" 
             class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 text-xs rounded transition">
            Buy Now
          </a>
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
                <div class="text-sm font-bold text-gray-600">DR</div>
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

      // Expand/collapse logic
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

      // Toggle niches
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

// ✅ Apply & Reset filter buttons
  // Expose an init function so this script can be re-run after AJAX reloads
  function initSitesTable() {
    // Avoid attaching listeners multiple times by using a data attribute on the container
    const container = document.getElementById('buyer-sites-table') || document.body;
    if (container.dataset.sitesInited === '1') {
      // still reload data when re-initialized
      loadSites();
      return;
    }

    // Mark as initialized
    container.dataset.sitesInited = '1';

    // Use event delegation for apply/reset so it still works if DOM is replaced
    if (!window._sitesTableClickDelegated) {
      const delegatedClick = (e) => {
        const apply = e.target.closest && e.target.closest('#apply-filters');
        const reset = e.target.closest && e.target.closest('#reset-filters');

        if (apply) {
          e.preventDefault();
          loadSites(1);
          return;
        }

        if (reset) {
          e.preventDefault();
          document.querySelectorAll("#filter-niche, #filter-country, #filter-min-dr, #filter-max-dr, #filter-min-traffic, #filter-max-traffic, #filter-search")
            .forEach(el => el.value = "");
          loadSites(1);
          return;
        }
      };

      document.addEventListener('click', delegatedClick);
      window._sitesTableClickDelegated = true;
    }

    // Initial load
    loadSites();
  }

  // make available globally so AJAX loaders can call it
  window.initSitesTable = initSitesTable;

  // auto-run on first load
  initSitesTable();
})();
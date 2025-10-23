function loadDashboardData() {
  console.log("🚀 Dashboard.js: Loading dashboard data...");

  const totalOrdersEl = document.getElementById('totalOrders');
  const completedOrdersEl = document.getElementById('completedOrders');
  const inProgressOrdersEl = document.getElementById('inProgressOrders');
  const cancelledOrdersEl = document.getElementById('cancelledOrders');
  const recentOrdersBodyEl = document.getElementById('recentOrdersBody');

  [totalOrdersEl, completedOrdersEl, inProgressOrdersEl, cancelledOrdersEl].forEach(el => {
    if (el) el.textContent = '0';
  });

  if (recentOrdersBodyEl) {
    recentOrdersBodyEl.innerHTML = `
      <tr>
        <td colspan="5" class="text-center py-4 text-gray-400">
          Loading recent orders...
        </td>
      </tr>
    `;
  }

  fetch('/linkbuildings/api/orders_data.php', { method: 'GET', credentials: 'same-origin' })
    .then(res => {
      if (!res.ok) throw new Error(`HTTP error! Status: ${res.status}`);
      return res.json();
    })
    .then(data => {
      if (!data.success) throw new Error(data.error || "Failed to load data");

      totalOrdersEl.textContent = data.stats?.total || 0;
      completedOrdersEl.textContent = data.stats?.completed || 0;
      inProgressOrdersEl.textContent = data.stats?.in_progress || 0;
      cancelledOrdersEl.textContent = data.stats?.cancelled || 0;

      if (recentOrdersBodyEl && Array.isArray(data.recentOrders)) {
        if (data.recentOrders.length > 0) {
          recentOrdersBodyEl.innerHTML = '';
          data.recentOrders.forEach(order => {
            const statusMap = {
              paid: { label: "Completed", color: "bg-green-100 text-green-700" },
              pending: { label: "In Progress", color: "bg-yellow-100 text-yellow-700" },
              failed: { label: "Cancelled", color: "bg-red-100 text-red-700" }
            };
            const status = statusMap[order.payment_status] || { label: "Unknown", color: "bg-gray-100 text-gray-700" };

            const row = document.createElement('tr');
            row.className = 'border-b hover:bg-gray-50 transition';
            row.innerHTML = `
              <td class="px-4 py-3 font-medium text-gray-800">#${order.id}</td>
              <td class="px-4 py-3">${order.site_url}</td>
              <td class="px-4 py-3">${new Date(order.created_at).toLocaleDateString()}</td>
              <td class="px-4 py-3">
                <span class="px-2 py-1 text-xs font-medium rounded-full ${status.color}">
                  ${status.label}
                </span>
              </td>
              <td class="px-4 py-3 text-right font-semibold">$${parseFloat(order.final_total || 0).toFixed(2)}</td>
            `;
            recentOrdersBodyEl.appendChild(row);
          });
        } else {
          recentOrdersBodyEl.innerHTML = `
            <tr>
              <td colspan="5" class="text-center py-4 text-gray-400">
                No recent orders found
              </td>
            </tr>
          `;
        }
      }

      if (typeof lucide !== 'undefined') lucide.createIcons();
    })
    .catch(err => {
      console.error("💥 Error loading dashboard data:", err);
      if (recentOrdersBodyEl) {
        recentOrdersBodyEl.innerHTML = `
          <tr>
            <td colspan="5" class="text-center py-4 text-red-500">
              Error: ${err.message}
            </td>
          </tr>
        `;
      }
    });
}

// ✅ Export globally so AJAX loader can trigger it
window.loadDashboardData = loadDashboardData;

// ✅ Auto-run if this file loads standalone
if (document.readyState !== 'loading') {
  loadDashboardData();
} else {
  document.addEventListener('DOMContentLoaded', loadDashboardData);
}

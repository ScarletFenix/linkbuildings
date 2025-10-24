function loadDashboardData() {
  const ids = ['totalOrders', 'completedOrders', 'inProgressOrders', 'cancelledOrders'];
  const [totalEl, completedEl, progressEl, cancelledEl] = ids.map(id => document.getElementById(id));
  const recentOrdersEl = document.getElementById('recentOrdersBody');

  // Reset stats
  [totalEl, completedEl, progressEl, cancelledEl].forEach(el => el && (el.textContent = '0'));

  // Show initial loading message
  if (recentOrdersEl) {
    recentOrdersEl.innerHTML = `
      <tr>
        <td colspan="7" class="text-center py-4 text-gray-400">
          Loading recent orders...
        </td>
      </tr>
    `;
  }

  // Helper: Format date like "22 Oct 2025"
  const formatDate = dateString => {
    const d = new Date(dateString);
    return isNaN(d) ? 'N/A' : d.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
  };

  // Fetch dashboard data
  fetch('/linkbuildings/api/orders_data.php', { credentials: 'same-origin' })
    .then(res => {
      if (!res.ok) throw new Error(`HTTP ${res.status}`);
      return res.json();
    })
    .then(data => {
      if (!data.success) throw new Error(data.error || 'Failed to load data');

      // Update stats
      totalEl.textContent = data.stats?.total || 0;
      completedEl.textContent = data.stats?.completed || 0;
      progressEl.textContent = data.stats?.in_progress || 0;
      cancelledEl.textContent = data.stats?.cancelled || 0;

      // Render recent orders
      if (recentOrdersEl) {
        const orders = data.recentOrders || [];

        if (orders.length === 0) {
          recentOrdersEl.innerHTML = `
            <tr><td colspan="7" class="text-center py-4 text-gray-400">No recent orders found</td></tr>
          `;
          return;
        }

        const paymentMap = {
          paid: { label: 'Paid', color: 'bg-green-100 text-green-700' },
          pending: { label: 'Pending', color: 'bg-yellow-100 text-yellow-700' },
          failed: { label: 'Failed', color: 'bg-red-100 text-red-700' }
        };

        const orderMap = {
          processing: { label: 'Processing', color: 'bg-blue-100 text-blue-700' },
          completed: { label: 'Completed', color: 'bg-green-100 text-green-700' },
          cancelled: { label: 'Cancelled', color: 'bg-red-100 text-red-700' }
        };

        recentOrdersEl.innerHTML = orders
          .slice(0, 10)
          .map(order => {
            const pay = paymentMap[order.payment_status] || { label: order.payment_status || 'Unknown', color: 'bg-gray-100 text-gray-700' };
            const stat = orderMap[order.order_status] || { label: order.order_status || 'Unknown', color: 'bg-gray-100 text-gray-700' };

            return `
              <tr class="border-b hover:bg-gray-50 transition">
                <td class="px-4 py-3 font-medium text-gray-800">${order.order_identifier || '—'}</td>
                <td class="px-4 py-3">${order.site_url || 'Unknown Site'}</td>
                <td class="px-4 py-3">${formatDate(order.created_at)}</td>
                <td class="px-4 py-3">${order.payment_method || 'N/A'}</td>
                <td class="px-4 py-3">
                  <span class="px-2 py-1 text-xs font-medium rounded-full ${pay.color}">
                    ${pay.label}
                  </span>
                </td>
                <td class="px-4 py-3">
                  <span class="px-2 py-1 text-xs font-medium rounded-full ${stat.color}">
                    ${stat.label}
                  </span>
                </td>
                <td class="px-4 py-3 text-right font-semibold">$${parseFloat(order.final_total || 0).toFixed(2)}</td>
              </tr>
            `;
          })
          .join('');
      }

      if (window.lucide) lucide.createIcons();
    })
    .catch(err => {
      console.error('Dashboard data error:', err);
      if (recentOrdersEl) {
        recentOrdersEl.innerHTML = `
          <tr>
            <td colspan="7" class="text-center py-4 text-red-500">
              Error loading data
            </td>
          </tr>
        `;
      }
    });
}

// Expose and auto-run
window.loadDashboardData = loadDashboardData;
document.readyState !== 'loading'
  ? loadDashboardData()
  : document.addEventListener('DOMContentLoaded', loadDashboardData);

// buyer/assets/js/orders.js

console.log("📦 orders.js loaded");

function formatDateTime(dateString) {
  const date = new Date(dateString);
  const options = {
    day: "2-digit",
    month: "short",
    year: "numeric",
  };
  return date.toLocaleString("en-GB", options); // e.g. "22 Oct 2025"
}

function loadOrdersData() {
  console.log("🚀 Loading orders data...");

  const ordersTableBody = document.getElementById("ordersTableBody");

  if (ordersTableBody) {
    ordersTableBody.innerHTML = `
      <tr>
        <td colspan="6" class="text-center py-4 text-gray-400">
          Loading orders...
        </td>
      </tr>
    `;
  }

  fetch("/linkbuildings/api/orders_data.php", { method: "GET", credentials: "same-origin" })
    .then((res) => {
      if (!res.ok) throw new Error(`HTTP error! Status: ${res.status}`);
      return res.json();
    })
    .then((data) => {
      if (!data.success) throw new Error(data.error || "Failed to load data");

      if (Array.isArray(data.recentOrders) && data.recentOrders.length > 0) {
        ordersTableBody.innerHTML = "";

        data.recentOrders.forEach((order) => {
          const statusMap = {
            paid: { label: "Completed", color: "bg-green-100 text-green-700" },
            pending: { label: "In Progress", color: "bg-yellow-100 text-yellow-700" },
            failed: { label: "Cancelled", color: "bg-red-100 text-red-700" },
          };
          const status = statusMap[order.payment_status] || { label: "Unknown", color: "bg-gray-100 text-gray-700" };

          const row = document.createElement("tr");
          row.className = "border-b hover:bg-gray-50 transition";
          row.innerHTML = `
            <td class="px-4 py-3 font-medium text-gray-800">#${order.id}</td>
            <td class="px-4 py-3">${order.site_url}</td>
            <td class="px-4 py-3">${formatDateTime(order.created_at)}</td>
            <td class="px-4 py-3">
              <span class="px-2 py-1 text-xs font-medium rounded-full ${status.color}">
                ${status.label}
              </span>
            </td>
            <td class="px-4 py-3 text-right font-semibold">$${parseFloat(order.final_total || 0).toFixed(2)}</td>
          `;

          ordersTableBody.appendChild(row);
        });
      } else {
        ordersTableBody.innerHTML = `
          <tr>
            <td colspan="6" class="text-center py-4 text-gray-400">
              No orders found.
            </td>
          </tr>
        `;
      }

      if (typeof lucide !== "undefined") lucide.createIcons();
    })
    .catch((err) => {
      console.error("💥 Error loading orders data:", err);
      if (ordersTableBody) {
        ordersTableBody.innerHTML = `
          <tr>
            <td colspan="6" class="text-center py-4 text-red-500">
              Error: ${err.message}
            </td>
          </tr>
        `;
      }
    });
}

// ✅ Export globally so the AJAX page loader can trigger it
window.loadOrdersData = loadOrdersData;

// ✅ Auto-run when loaded directly
if (document.readyState !== "loading") {
  loadOrdersData();
} else {
  document.addEventListener("DOMContentLoaded", loadOrdersData);
}

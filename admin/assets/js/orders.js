console.log("🧩 Admin Orders script loaded");

document.addEventListener("DOMContentLoaded", () => {
  const tableBody = document.getElementById("adminOrdersTableBody");
  const filterButtons = document.querySelectorAll(".filter-btn");
  let allOrders = [];

  // 📦 Load Data from API
  function loadAdminOrders() {
    tableBody.innerHTML = `
      <tr><td colspan="8" class="text-center py-4 text-gray-400">Loading orders...</td></tr>
    `;
    fetch("/linkbuildings/api/admin_orders_data.php", {
      method: "GET",
      credentials: "same-origin",
    })
      .then((res) => res.json())
      .then((data) => {
        if (!data.success) throw new Error(data.error || "Failed to load orders");
        allOrders = data.recentOrders;
        renderOrders(allOrders);
      })
      .catch((err) => {
        console.error("Error loading admin orders:", err);
        tableBody.innerHTML = `
          <tr><td colspan="8" class="text-center py-4 text-red-500">
            Error loading data: ${err.message}
          </td></tr>
        `;
      });
  }

  // 🎨 Render Table
  function renderOrders(orders) {
    if (!orders.length) {
      tableBody.innerHTML = `
        <tr><td colspan="8" class="text-center py-4 text-gray-400">No orders found.</td></tr>
      `;
      return;
    }

    tableBody.innerHTML = "";
    orders.forEach((order) => {
      const paymentColors = {
        paid: "bg-green-100 text-green-700",
        pending: "bg-yellow-100 text-yellow-700",
        failed: "bg-red-100 text-red-700",
      };
      const orderColors = {
        Pending: "bg-yellow-100 text-yellow-700",
        InProgress: "bg-blue-100 text-blue-700",
        Completed: "bg-green-100 text-green-700",
        Rejected: "bg-red-100 text-red-700",
      };

      const row = `
        <tr class="border-b hover:bg-gray-50 transition">
          <td class="px-4 py-3 font-medium text-gray-800">${order.order_identifier || "N/A"}</td>
          <td class="px-4 py-3">${order.buyer_name || "Unknown"}</td>
          <td class="px-4 py-3">${order.site_url || "Unknown Site"}</td>
          <td class="px-4 py-3">${new Date(order.created_at).toLocaleDateString("en-GB", {
            day: "2-digit",
            month: "short",
            year: "numeric",
          })}</td>
          <td class="px-4 py-3">${order.payment_method || "N/A"}</td>
          <td class="px-4 py-3">
            <span class="px-2 py-1 text-xs font-medium rounded-full ${
              paymentColors[order.payment_status] || "bg-gray-100 text-gray-700"
            }">
              ${order.payment_status || "Unknown"}
            </span>
          </td>
          <td class="px-4 py-3">
            <span class="px-2 py-1 text-xs font-medium rounded-full ${
              orderColors[order.order_status] || "bg-gray-100 text-gray-700"
            }">
              ${order.order_status || "Unknown"}
            </span>
          </td>
          <td class="px-4 py-3 text-right font-semibold">$${parseFloat(order.final_total || 0).toFixed(2)}</td>
        </tr>
      `;
      tableBody.insertAdjacentHTML("beforeend", row);
    });
  }

  // 🧩 Filtering Logic
  filterButtons.forEach((btn) => {
    btn.addEventListener("click", () => {
      filterButtons.forEach((b) => b.classList.remove("bg-blue-600", "text-white"));
      filterButtons.forEach((b) => b.classList.add("bg-gray-100", "text-gray-700"));
      btn.classList.add("bg-blue-600", "text-white");
      btn.classList.remove("bg-gray-100", "text-gray-700");

      const status = btn.dataset.status;
      if (status === "all") return renderOrders(allOrders);

      const filtered = allOrders.filter(
        (order) => order.order_status?.toLowerCase() === status.toLowerCase()
      );
      renderOrders(filtered);
    });
  });

  // 🚀 Auto-load
  loadAdminOrders();
});

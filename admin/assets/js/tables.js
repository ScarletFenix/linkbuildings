// tables.js

// AJAX loader for pagination & per-page
function loadSites(page = 1, perPage = 25) {
    $.get("/linkbuildings/admin/partials/sites_table.php", { p: page, per_page: perPage }, function (data) {
        $("#sitesTableWrapper").replaceWith(data);
        attachEvents(); // rebind events after reload
    });
}

function attachEvents() {
    // --- Toggle niches
    $(document).off("click", ".toggle-niches").on("click", ".toggle-niches", function () {
        const $this = $(this);
        const niches = JSON.parse($this.attr("data-niches"));
        const container = $this.prev(".niche-container");
        if ($this.attr("data-state") === "collapsed") {
            container.empty();
            niches.forEach(n => container.append(
                `<span class='bg-blue-100 text-blue-700 px-2 py-1 rounded-full text-xs whitespace-nowrap mr-1 mb-1 inline-block'>${n}</span>`
            ));
            $this.text("Collapse").attr("data-state", "expanded");
        } else {
            container.empty();
            niches.slice(0, 4).forEach(n => container.append(
                `<span class='bg-blue-100 text-blue-700 px-2 py-1 rounded-full text-xs whitespace-nowrap mr-1 mb-1 inline-block'>${n}</span>`
            ));
            $this.text("View more").attr("data-state", "collapsed");
        }
    });

    // --- Search filter
    $("#searchInput").off("input").on("input", function () {
        const val = $(this).val().toLowerCase();
        $("#sitesTable tbody tr").filter(function () {
            $(this).toggle($(this).text().toLowerCase().indexOf(val) > -1);
        });
    });

    // --- Per page selector
    $(document).off("change", "#perPage").on("change", "#perPage", function () {
        loadSites(1, $(this).val());
    });

    // --- Pagination buttons
    $(document).off("click", ".page-btn").on("click", ".page-btn", function () {
        const page = $(this).data("page");
        const perPage = $("#perPage").val();
        loadSites(page, perPage);
    });

    // --- DELETE BUTTON
    $(document).off("click", ".deleteBtn").on("click", ".deleteBtn", function () {
        const id = $(this).data("id");
        if (!confirm("Are you sure you want to delete this site?")) return;

        $.post("/linkbuildings/admin/ajax/sites_delete.php", { id }, function (res) {
            if (res.success) {
                loadSites();
            } else {
                alert(res.message || "Delete failed.");
            }
        }, "json");
    });

    // --- TOGGLE STATUS BUTTON
    $(document).off("click", ".toggleStatusBtn").on("click", ".toggleStatusBtn", function () {
        const id = $(this).data("id");
        const status = $(this).data("status");

        $.post("/linkbuildings/admin/ajax/sites_toggle.php", { id, status }, function (res) {
            if (res.success) {
                loadSites();
            } else {
                alert(res.message || "Toggle failed.");
            }
        }, "json");
    });

    // --- EDIT BUTTON
    $(document).off("click", ".editBtn").on("click", ".editBtn", function () {
        const id = $(this).data("id");

        $.get("/linkbuildings/admin/ajax/sites_get.php", { id }, function (res) {
            if (res.success) {
                const d = res.data;

                // Fill fields
                $("#editForm [name=id]").val(d.id);
                $("#editForm [name=site_name]").val(d.site_name);
                $("#editForm [name=description]").val(d.description);
                $("#editForm [name=site_url]").val(d.site_url);
                $("#editForm [name=price]").val(d.price);
                $("#editForm [name=dr]").val(d.dr);
                $("#editForm [name=traffic]").val(d.traffic);
                $("#editForm [name=country]").val(d.country);
                $("#editForm [name=backlinks]").val(d.backlinks);

                // --- Image preview
                if (d.site_img) {
                    $("#editImagePreview").html(
                        `<img src="/linkbuildings/uploads/sites/${d.site_img}" class="h-20 rounded border mx-auto">`
                    );
                } else {
                    $("#editImagePreview").html(`<span class="text-gray-500 text-sm">No image uploaded</span>`);
                }

                // --- Init Niche selector
                initNicheSelector("#editNicheSelector", "#editForm [name=niche]", d.niche ? d.niche.split(",") : []);

                $("#editModal").removeClass("hidden");
            } else {
                alert(res.message || "Failed to load site.");
            }
        }, "json");
    });

    // --- EDIT FORM SUBMIT
    $(document).off("submit", "#editForm").on("submit", "#editForm", function (e) {
        e.preventDefault();
        const formData = new FormData(this);

        $.ajax({
            url: "/linkbuildings/admin/ajax/sites_edit.php",
            method: "POST",
            data: formData,
            contentType: false,
            processData: false,
            dataType: "json",
            success: function (res) {
                if (res.success) {
                    $("#editModal").addClass("hidden");
                    loadSites();
                } else {
                    alert(res.message || "Update failed.");
                }
            }
        });
    });

    // --- CLOSE EDIT MODAL
    $(document).off("click", "#closeEditModal").on("click", "#closeEditModal", function () {
        $("#editModal").addClass("hidden");
    });
}

// --- Niche Selector (reusable for Add + Edit)
function initNicheSelector(containerId, hiddenInputSelector, preselected = []) {
    const $container = $(containerId);
    const $hidden = $(hiddenInputSelector);

    let selected = Array.isArray(preselected) ? preselected : (preselected ? preselected.split(",") : []);

    function render() {
        $container.empty();
        selected.forEach((niche, idx) => {
            $container.append(`
                <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded-full text-xs flex items-center gap-1">
                    ${niche}
                    <button type="button" class="removeNiche" data-idx="${idx}">&times;</button>
                </span>
            `);
        });

        if (selected.length < 5) {
            $container.append(`<input type="text" class="nicheInput flex-grow border-0 focus:ring-0 text-xs" placeholder="Type niche...">`);
        }

        $hidden.val(selected.join(","));
    }

    $container.off("click", ".removeNiche").on("click", ".removeNiche", function () {
        const idx = $(this).data("idx");
        selected.splice(idx, 1);
        render();
    });

    $container.off("keypress", ".nicheInput").on("keypress", ".nicheInput", function (e) {
        if (e.which === 13) {
            e.preventDefault();
            const val = $(this).val().trim();
            if (val && !selected.includes(val)) {
                selected.push(val);
                render();
            }
        }
    });

    render();
}

// Initial load
$(document).ready(function () {
    attachEvents();
});

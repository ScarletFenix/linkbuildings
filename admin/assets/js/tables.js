// tables.js

// AJAX loader for pagination & per-page
function loadSites(page = 1, perPage = 25) {
    $.get("/linkbuildings/admin/partials/sites_table.php", { p: page, per_page: perPage }, function(data){
        $("#sitesTableWrapper").replaceWith(data);
        attachEvents(); // rebind events after reload
    });
}

function attachEvents(){
    // Toggle niches
    $(document).off("click", ".toggle-niches").on("click", ".toggle-niches", function(){
        const $this = $(this);
        const niches = JSON.parse($this.attr("data-niches"));
        const container = $this.prev(".niche-container");
        if($this.attr("data-state") === "collapsed"){
            container.empty();
            niches.forEach(n => container.append(`<span class='bg-blue-100 text-blue-700 px-2 py-1 rounded-full text-xs whitespace-nowrap'>${n}</span>`));
            $this.text("Collapse").attr("data-state", "expanded");
        } else {
            container.empty();
            niches.slice(0,4).forEach(n => container.append(`<span class='bg-blue-100 text-blue-700 px-2 py-1 rounded-full text-xs whitespace-nowrap'>${n}</span>`));
            $this.text("View more").attr("data-state", "collapsed");
        }
    });

    // Search filter
    $("#searchInput").off("input").on("input", function(){
        const val = $(this).val().toLowerCase();
        $("#sitesTable tbody tr").filter(function(){
            $(this).toggle($(this).text().toLowerCase().indexOf(val) > -1);
        });
    });

    // Per page selector
    $(document).off("change", "#perPage").on("change", "#perPage", function(){
        loadSites(1, $(this).val());
    });

    // Pagination buttons
    $(document).off("click", ".page-btn").on("click", ".page-btn", function(){
        const page = $(this).data("page");
        const perPage = $("#perPage").val();
        loadSites(page, perPage);
    }); 
}




// Initial load
$(document).ready(function(){
    attachEvents();
});

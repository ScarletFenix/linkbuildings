$(document).ready(function() {
    const maxSelections = 5;
    const allNiches = [
        'automotive','beauty','business & e-business','computer games','construction',
'cooking','culture & art','diet & weight loss','entertainment','fashion & clothing',
'family, kids & pregnancy','finance, banking & insurance','health & medical',
'home & garden & interior','technology','music','real estate','travel, tours & hotels',
'sports & fitness','agriculture & forestry','wedding','education & science',
'dating & relationships','food & drink','e-commerce & shopping','news & media',
'pets','films & TV','jobs & careers','nature & hobbies'

    ];  

    let selectedNiches = [];

    const $nicheInput = $("#nicheSearch");
    const $nicheOptions = $("#nicheOptions");
    const $selectedContainer = $("#selectedNiches");
    const $hiddenInput = $("#nicheHidden");

    // Show dropdown on focus/click
    $nicheInput.on("focus click", function() {
        renderOptions(allNiches);
        $nicheOptions.show();
    });

    // Filter options as user types
    $nicheInput.on("input", function() {
        const query = $(this).val().toLowerCase();
        const filtered = allNiches.filter(n => n.toLowerCase().includes(query));
        renderOptions(filtered);
    });

    // Click outside to hide options
    $(document).on("click", function(e) {
        if (!$(e.target).closest("#nicheSelect").length) {
            $nicheOptions.hide();
        }
    });

    // Render dropdown options
    function renderOptions(list) {
        $nicheOptions.empty();
        list.forEach(niche => {
            if (!selectedNiches.includes(niche)) {
                $nicheOptions.append(`
                    <div class="px-2 py-1 hover:bg-gray-200 cursor-pointer niche-option">${niche}</div>
                `);
            }
        });
        if(list.length === 0) {
            $nicheOptions.append(`<div class="px-2 py-1 text-gray-400">No matches found</div>`);
        }
    }

    // Select a niche
    $nicheOptions.on("click", ".niche-option", function() {
        const niche = $(this).text();
        if (selectedNiches.length < maxSelections) {
            selectedNiches.push(niche);
            updateSelected();
            $nicheInput.val("");
            renderOptions(allNiches);
        } else {
            alert(`You can select a maximum of ${maxSelections} niches`);
        }
    });

    // Remove a selected niche
    $selectedContainer.on("click", ".remove-niche", function() {
        const niche = $(this).data("niche");
        selectedNiches = selectedNiches.filter(n => n !== niche);
        updateSelected();
        renderOptions(allNiches);
    });

    // Update display of selected niches and hidden input
    function updateSelected() {
        $selectedContainer.empty();
        selectedNiches.forEach(n => {
            $selectedContainer.append(`
                <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded-full text-xs flex items-center gap-1">
                    ${n} <span class="remove-niche cursor-pointer" data-niche="${n}">&times;</span>
                </span>
            `);
        });
        $hiddenInput.val(selectedNiches.join(","));
    }

    

    // Optional: prepopulate if hidden input has value (edit mode)
    if($hiddenInput.val()) {
        selectedNiches = $hiddenInput.val().split(",");
        updateSelected();
    }

    
    
});

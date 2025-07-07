document.addEventListener("DOMContentLoaded", function () {
    const container = document.getElementById("items-container");
    const addButton = document.getElementById("add-item-btn");
    let itemIndex = container.querySelectorAll(".item-group").length;

    addButton.addEventListener("click", function () {
        const newItem = document.createElement("div");
        newItem.classList.add("item-group", "flex", "gap-4", "items-center");
        newItem.innerHTML = `
            <input type="text" name="items[${itemIndex}][term]" placeholder="Term" required class="flex-1 px-3 py-2 border border-neutral-300 rounded-md">
            <input type="text" name="items[${itemIndex}][details]" placeholder="Details" class="flex-1 px-3 py-2 border border-neutral-300 rounded-md">
            <button type="button" class="remove-item-btn text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></button>
        `;
        container.appendChild(newItem);
        itemIndex++;
    });

    container.addEventListener("click", function (e) {
        if (e.target.closest(".remove-item-btn")) {
            e.target.closest(".item-group").remove();
        }
    });
});

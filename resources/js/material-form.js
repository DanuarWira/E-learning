document.addEventListener("DOMContentLoaded", function () {
    const container = document.getElementById("items-container");
    const addButton = document.getElementById("add-item-btn");
    let itemIndex = container.querySelectorAll(".item-group").length;

    addButton.addEventListener("click", function () {
        const newItem = document.createElement("div");
        newItem.classList.add(
            "item-group",
            "grid",
            "grid-cols-1",
            "md:grid-cols-3",
            "gap-4",
            "items-center",
            "border-t",
            "pt-4"
        );
        newItem.innerHTML = `
            <input type="text" name="items[${itemIndex}][title]" placeholder="Judul (opsional)" class="md:col-span-1 border-neutral-300 rounded-md">
            <textarea name="items[${itemIndex}][description]" placeholder="Deskripsi" required class="md:col-span-2 border-neutral-300 rounded-md"></textarea>
            <input type="text" name="items[${itemIndex}][url]" placeholder="URL (opsional)" class="md:col-span-3 border-neutral-300 rounded-md">
            <button type="button" class="remove-item-btn text-red-500 hover:text-red-700 justify-self-end md:col-span-3"><i class="fas fa-trash"></i> Hapus Item</button>
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

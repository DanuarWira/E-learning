document.addEventListener("DOMContentLoaded", function () {
    const itemsContainer = document.getElementById("items-container");
    const addButton = document.getElementById("add-item-btn");
    const typeSelector = document.getElementById("type-selector");
    let itemIndex = 0;

    function createNewItem() {
        const index = itemIndex++;
        const itemGroup = document.createElement("div");
        itemGroup.classList.add(
            "item-group",
            "grid",
            "grid-cols-1",
            "gap-4",
            "border-t",
            "pt-4"
        );

        const type = typeSelector.value;
        let inputFieldHTML = "";

        if (type === "Audio" || type === "Gambar") {
            inputFieldHTML = `<input type="file" name="items[${index}][file]" class="w-full text-sm text-neutral-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-violet-50 file:text-violet-700 hover:file:bg-violet-100">`;
        } else if (type === "Video") {
            inputFieldHTML = `<input type="url" name="items[${index}][url]" placeholder="Masukkan URL YouTube di sini" class="w-full border-neutral-300 rounded-md">`;
        }

        itemGroup.innerHTML = `
            <div>
                <label class="block text-sm font-medium">Deskripsi Item #${
                    index + 1
                }</label>
                <textarea name="items[${index}][description]" placeholder="Deskripsi materi..." required class="mt-1 w-full border-neutral-300 rounded-md"></textarea>
            </div>
            <div class="input-field-container">
                ${inputFieldHTML}
            </div>
            <div class="text-right">
                <button type="button" class="remove-item-btn text-red-500 hover:text-red-700 text-sm">Hapus Item Ini</button>
            </div>
        `;

        itemsContainer.appendChild(itemGroup);
    }

    // Tambahkan item pertama saat halaman dimuat
    createNewItem();

    // Event listener untuk tombol 'Tambah Item'
    addButton.addEventListener("click", createNewItem);

    // Event listener untuk menghapus item
    itemsContainer.addEventListener("click", function (e) {
        if (e.target.classList.contains("remove-item-btn")) {
            e.target.closest(".item-group").remove();
        }
    });

    // Event listener untuk mengubah input berdasarkan tipe
    typeSelector.addEventListener("change", function () {
        // Hapus semua item yang ada untuk memulai ulang dengan tipe yang benar
        itemsContainer.innerHTML =
            '<h3 class="text-lg font-medium border-b pb-2">Item Materi</h3>';
        itemIndex = 0;
        createNewItem(); // Buat item pertama dengan tipe yang baru
    });
});

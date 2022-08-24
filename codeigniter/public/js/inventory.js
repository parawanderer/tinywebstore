var actionsBlocked = false;

// delete product using button
const deleteProductModal = document.getElementById('deleteProductModal');
const deleteProductInput = document.getElementById('deleteProductInput');
const deleteProductModalLabel = document.getElementById("deleteProductModalLabel");
const productDeleteForm = document.getElementById("productDeleteForm");
const productDeleteButton = document.getElementById("productDeleteButton");

const deleteModal = new bootstrap.Modal(deleteProductModal, {});

const productDeleteButtons = document.getElementsByClassName("product-delete-button");
for (var i = 0; i < productDeleteButtons.length; ++i) {
    const button = productDeleteButtons[i];

    button.addEventListener("click", function(event) {
        deleteProductModalLabel.innerText = "Delete Product \"" + button.dataset.productName + "\"";
        deleteProductInput.value = button.dataset.id;

        deleteModal.show();
    });
}

productDeleteButton.addEventListener("click", function(event) {
    if (actionsBlocked) {
        event.preventDefault();
        return;
    }
    actionsBlocked = true;
});
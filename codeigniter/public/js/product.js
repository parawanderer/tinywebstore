
const mediaThumbnails = document.getElementsByClassName("product-media-selector");
const productMediaMainImage = document.getElementById("productMediaMainImage");

var currentSelection = document.getElementsByClassName("current-selection")[0];

for (var i = 0; i < mediaThumbnails.length; ++i) {
    const selector = mediaThumbnails[i];

    selector.addEventListener("click", function(event) {
        event.preventDefault();

        // selection outline visuals
        const imageContainer = selector.getElementsByClassName("thumbnail-product-media-select")[0];
        
        if (currentSelection) {
            currentSelection.classList.remove("current-selection");
            const oldImageContainer = currentSelection.getElementsByClassName("thumbnail-product-media-select")[0];
            oldImageContainer.classList.remove("border-3", "border-indigo");
        }
        selector.classList.add("current-selection");
        imageContainer.classList.add("border-3", "border-indigo");
        currentSelection = selector;


        // update preview image
        if (productMediaMainImage)
            productMediaMainImage.src = selector.getElementsByClassName("product-media-preview-img")[0].src;
    });
}
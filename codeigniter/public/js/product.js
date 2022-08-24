// select current product image/media
const mediaThumbnails = document.getElementsByClassName("product-media-selector");
const initialSelection = document.getElementsByClassName("current-selection")[0];

const primaryMediaContainer = document.getElementById("primaryMediaContainer");
const productMediaMainImage = document.getElementById("productMediaMainImage");

var currentSelection = initialSelection;

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
        if (primaryMediaContainer) {
            primaryMediaContainer.dataset.isVideo = selector.dataset.isVideo;
            primaryMediaContainer.dataset.id = selector.dataset.id;
            primaryMediaContainer.dataset.poster = selector.dataset.poster;
        }

        if (productMediaMainImage)
            productMediaMainImage.src = selector.getElementsByClassName("product-media-preview-img")[0].src;
    });
}

// view media

const mediaViewModal = document.getElementById('viewMediaModal');
const mediaViewImage = document.getElementById("mediaViewImage");
const mediaViewVideo = document.getElementById("mediaViewVideo");
const viewModal = new bootstrap.Modal(mediaViewModal, {});


if (primaryMediaContainer) {
    primaryMediaContainer.addEventListener("click", function(event) {
        if (primaryMediaContainer.dataset.isVideo) {
            mediaViewImage.style.display = 'none';
            mediaViewVideo.style.display = '';

            mediaViewVideo.src = primaryMediaContainer.dataset.id;
            mediaViewVideo.poster = primaryMediaContainer.dataset.poster;
        } else {
            mediaViewImage.style.display = '';
            mediaViewVideo.style.display = 'none';

            mediaViewImage.src = primaryMediaContainer.dataset.id;
        }

        viewModal.show();
    });
}

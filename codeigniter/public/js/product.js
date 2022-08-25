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


// review
const reviewForm = document.getElementById("productReviewForm");
reviewForm?.addEventListener('submit', function (event) {

    if (!reviewForm.checkValidity()) {
        event.preventDefault();
        event.stopPropagation();
    }

    reviewForm.classList.add('was-validated');

});


//tooltips for stars
// https://getbootstrap.com/docs/5.0/components/tooltips/#example-enable-tooltips-everywhere
var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
  return new bootstrap.Tooltip(tooltipTriggerEl)
})


// review stars
const stars = document.getElementsByClassName("rating-star");
function renderStarRating(rating = 1) {
    let i = 0;

    for (; i < rating; ++i) {
        stars[i].classList.remove("bi-star");
        stars[i].classList.add("bi-star-fill");
    }

    for (; i < 5; ++i) {
        stars[i].classList.remove("bi-star-fill");
        stars[i].classList.add("bi-star");
    }
}

const starRatingInput = document.getElementById("starRatingInput");

var currentStar = 1;
for (var i = 0; i < stars.length; ++i) {
    const star = stars[i];

    star.addEventListener("mouseenter", function(event) {
        // preview rating but reset on mouse out
        const val = parseInt(star.dataset.starVal);
        renderStarRating(val);
    });

    star.addEventListener("mouseleave", function(event) {
        // reset on mouse out
        renderStarRating(currentStar);
    });

    star.addEventListener("click", function(event) {
        // premanent new rating (for now)
        currentStar = parseInt(star.dataset.starVal);
        renderStarRating(currentStar);
        starRatingInput.value = currentStar;
    });
}
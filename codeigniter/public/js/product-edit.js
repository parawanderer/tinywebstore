var addRemoveBlocked = false;

const productTitleInput = document.getElementById("productTitle");
const productPriceInput = document.getElementById("productPrice");
const productAvailabilityInput = document.getElementById("productAvailability");
const productDescriptionInput = document.getElementById("productDescription");

function clearIndexPhpFromPath(path) {
    return path.replace("/index.php", "");
}

// restore from past session
function restorePastSession() {
    const key = clearIndexPhpFromPath(window.location.pathname);
    const pastData = sessionStorage.getItem(key);

    if (pastData) {
        const deserialized = JSON.parse(pastData);

        productTitleInput.value = deserialized["productTitle"];
        productPriceInput.value = deserialized["productPrice"];
        productAvailabilityInput.value = deserialized["productAvailability"];
        productDescriptionInput.value = deserialized["productDescription"];
    }

    sessionStorage.removeItem(key);
}
restorePastSession();


// drop session info when finalizing
const finalizeButton = document.getElementById("finaliseButton");
finalizeButton.addEventListener("click", function(event) {
    if (addRemoveBlocked) {
        event.preventDefault();
        return;
    }
    
    const key = clearIndexPhpFromPath(window.location.pathname);
    sessionStorage.removeItem(key);
    addRemoveBlocked = true;
});


//validation
const form = document.getElementById('productEditCreateForm');
form.addEventListener('submit', function (event) {
    if (!form.checkValidity()) {
        event.preventDefault();
        event.stopPropagation();
    }

    form.classList.add('was-validated');
});

// primary image selection
const productPrimaryImageInput = document.getElementById("currentPrimaryImage");
const mediaSelection = document.getElementsByClassName("product-media-selector");
const mediaPreviewImage = document.getElementById("productMediaMainImage");
var currentSelection = document.getElementsByClassName("current-selection")[0];

for (var i = 0; i < mediaSelection.length; ++i) {
    const selector = mediaSelection[i];

    selector.addEventListener("click", function (event) {
        
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
        if (mediaPreviewImage)
            mediaPreviewImage.src = selector.dataset["mediaFullsizeImg"];

        //update input
        productPrimaryImageInput.value = selector.dataset.id;
    });
}

// media adding
const addMediaModal = document.getElementById('addMediaModal');
const addMediaButton = document.getElementById("addMediaButton");
const uploadMediaConfirmButton = document.getElementById("uploadMediaConfirmButton");

const addModal = new bootstrap.Modal(addMediaModal, {});

addMediaButton.addEventListener("click", function(event) {
    addModal.show();
});

function rememberCurrentFormFields() {
    // need to save form info for when user returns...
    const key = clearIndexPhpFromPath(window.location.pathname);

    const data = {
        "productTitle" : productTitleInput.value,
        "productPrice": productPriceInput.value,
        "productAvailability" : productAvailabilityInput.value,
        "productDescription" : productDescriptionInput.value
    };

    sessionStorage.setItem(key, JSON.stringify(data));
}

function saveSessionAndBlockRetry(event) {
    if (addRemoveBlocked) {
        event.preventDefault();
        return;
    }
    
    rememberCurrentFormFields();
    addRemoveBlocked = true;
}

// MEDIA ADDING: remember changes when returning to page in browser "page lifetime session" storage
uploadMediaConfirmButton.addEventListener("click", saveSessionAndBlockRetry);


// media removing
const deleteMediaModal = document.getElementById('deleteMediaModal');
const removeMediaButtons = document.getElementsByClassName("media-remove-button");
const deleteMediaConfirmButton = document.getElementById("deleteMediaConfirmButton");
const deleteModal = new bootstrap.Modal(deleteMediaModal, {});

for (var i = 0; i < removeMediaButtons.length; ++i) {
    const button = removeMediaButtons[i];

    button.addEventListener("click", function(event) {
        event.preventDefault();
        event.stopPropagation();

        deleteMediaInput.value = event.target.dataset['mediaId'];
        deletePreviewImage.src = event.target.dataset['mediaFullsizeImg'];

        deleteModal.show();
    });
}
deleteMediaConfirmButton.addEventListener("click", saveSessionAndBlockRetry);


// description
const descriptionInput = document.getElementById("productDescription");
const htmlEditor = pell.init({
    // <HTMLElement>, required
    element: document.getElementById('pellEditor'),
    onChange: function(html) { 
        descriptionInput.value = html;
     },
    defaultParagraphSeparator: 'p',
    styleWithCSS: false,
    actions: [
      'bold',
      'underline',
      'italic',
      'strikethrough',
      'heading1',
      'heading2',
      'paragraph',
      'olist',
      'ulist',
      'code',
      'line',
      'link'
    ]
});

htmlEditor.content.innerHTML = descriptionInput.value;

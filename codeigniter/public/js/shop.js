const deleteMediaModal = document.getElementById('deleteMediaModal');
const deleteMediaInput = document.getElementById('deleteMediaInput');
const deletePreviewImage = document.getElementById("deletePreviewImage");
const items = document.getElementsByClassName("media-remove-button");

const deleteModal = new bootstrap.Modal(deleteMediaModal, {});

// delete
for (var i = 0; i < items.length; ++i) {
    const button = items[i];

    button.addEventListener('click', function(event) {
        event.preventDefault();
        event.stopPropagation();

        deleteMediaInput.value = this.dataset['mediaId'];
        deletePreviewImage.src = this.dataset['mediaFullsizeImg'];

        deleteModal.show();
    });
}

// view
const mediaViewModal = document.getElementById('viewMediaModal');
const mediaViewImage = document.getElementById("mediaViewImage");
const mediaViewVideo = document.getElementById("mediaViewVideo");
const videoLinkAlt = document.getElementById("videoLinkAlt");
const viewModal = new bootstrap.Modal(mediaViewModal, {});

const mediaItemContainers = document.getElementsByClassName("media-item-clickable");
for (var i = 0; i < mediaItemContainers.length; ++i) {
    const container = mediaItemContainers[i];

    container.addEventListener("click", function(event) {
        if (container.dataset.isVideo) {
            mediaViewImage.style.display = 'none';
            mediaViewVideo.style.display = '';

            mediaViewVideo.src = container.dataset.id;
            mediaViewVideo.poster = container.dataset.poster;

            videoLinkAlt.href = container.dataset.id;
        } else {
            mediaViewImage.style.display = '';
            mediaViewVideo.style.display = 'none';

            mediaViewImage.src = container.dataset.mediaFullsizeImg;
        }

        viewModal.show();
    });
}



// add
const addMediaModal = document.getElementById('addMediaModal');
const addMediaButton = document.getElementById("addMediaButton");

const addModal = new bootstrap.Modal(addMediaModal, {});

addMediaButton?.addEventListener("click", function(event) {
    addModal.show();
});

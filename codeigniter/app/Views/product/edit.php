<link rel="stylesheet" href="/css/shop.css">
<!-- lightweight editor to add "advanced" feature of creating more complex descriptions since that was part of the project and i do not want to write an editor from scratch -->
<link rel="stylesheet" type="text/css" href="https://unpkg.com/pell/dist/pell.min.css">

<div class="container p-outer-block">
    <form action="<?= $is_edit ? "/product/edit/{$product['id']}" : '/product/create' ?>" method="post" id="productEditCreateForm" novalidate>
    <?= csrf_field() ?>

        <div class="row mb-3 mb-md-0">
            <div class="container d-flex justify-content-between">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/">Home</a></li>
                        <li class="breadcrumb-item"><a href="/shop/inventory"><?= esc($user['shop_name']) ?></a></li>
                        <li class="breadcrumb-item">
                            <?php if ($is_edit) : ?>

                                <a href="/product/<?= esc($product['id']) ?>"><?= esc($product['title']) ?></a>
                            <?php else: ?>
                                New Product
                            <?php endif ?>
                        </li>
                        <li class="breadcrumb-item active" aria-current="<?= $is_edit ? 'Edit Product' : 'Create Product' ?>">
                            <?php if ($is_edit) : ?>
                                Edit
                            <?php else: ?>
                                Create
                            <?php endif ?>
                        </li>
                    </ol>
                </nav>
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button class="btn btn-primary btn-lg bg-indigo" type="submit" id="finaliseButton">
                        <?php if ($is_edit) : ?>
                            Save Changes
                        <?php else: ?>
                            Create Product
                        <?php endif ?>
                    </button>
                </div>
            </div>
        </div>
        <div class="row">
            <?php if ($error) : ?>
                <div class="alert alert-danger" role="alert">
                    <?= service('validation')->listErrors() ?>
                </div>
            <?php endif ?>

            <div class="col-12 col-md-6 px-md-5">
                <div class="row">
                    <?php if ($is_edit && $primary_media) : ?>
                        <img 
                            src="/uploads/shop/media/<?= esc($primary_media['thumbnail_id']) ?>" 
                            class="rounded float-start product-img-current" 
                            alt="Product Photo" 
                            id="productMediaMainImage"
                        />
                    <?php else: ?>
                        <div class="rounded float-start product-img-current bg-grey-light d-flex justify-content-center">
                            <i class="bi bi-image text-white fs-1 align-self-center"></i>
                        </div>
                    <?php endif ?>
                </div>

                <div class="row mt-3"">
                    <label class="form-label">Primary Image</label>
                </div>

                <div class="row justify-content-start align-items-end">
                    <?php if ($is_edit) : ?>
                    <div class="col-12 mb-4 mb-md-0">
                        <?php if (count($media) > 0): ?>
                            <div class="row row-cols-4 g-4 product-media-select pt-4">
                                <?php foreach ($media as $mediaItem) : ?>

                                    <div class="col">
                                        <a 
                                            href="#" 
                                            class="product-media-selector <?=  $primary_media && $primary_media['id'] === $mediaItem['id'] ? 'current-selection' : '' ?> "
                                            data-id="<?= esc($mediaItem['id']) ?>"
                                            data-media-fullsize-img="/uploads/shop/media/<?= esc($mediaItem['thumbnail_id']) ?>" 
                                        >
                                            <div class="card thumbnail-product-media-select h-100 <?=  $primary_media && $primary_media['id'] === $mediaItem['id']  ? 'border-3 border-indigo' : '' ?>">
                                                <img 
                                                    src="/uploads/shop/media/<?= esc($mediaItem['thumbnail_id_s']) ?>"
                                                    class="card-img-top product-media-preview-img" 
                                                    alt="Media Select Primary Thumbnail"
                                                />
                                                <div class="card-img-overlay d-flex flex-row-reverse px-1 py-1">
                                                    <button 
                                                        data-media-id="<?= esc($mediaItem['thumbnail_id_s']) ?>" 
                                                        data-media-fullsize-img="/uploads/shop/media/<?= esc($mediaItem['thumbnail_id']) ?>" 
                                                        class="btn-close media-remove-button" 
                                                        aria-label="Delete Media"
                                                        type="button"
                                                        >
                                                    </button>
                                                </div>
                                            </div>
                                        </a>
                                    </div>

                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="row">
                                <div class="col">
                                    <p class="text-muted">
                                        No Media Uploaded Yet
                                    </p>
                                </div>
                            </div>
                        <?php endif ?>
                    </div>
                    <?php else: ?>
                        <div class="col">
                            <p class="text-muted">
                                Save product before adding media
                            </p>
                        </div>
                    <?php endif ?>
                    <div class="col">
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button class="btn btn-primary bg-indigo" type="button" id="addMediaButton" <?= !$is_edit ? 'disabled' : '' ?>>
                                <i class="bi bi-images"></i> Add Media
                            </button>
                        </div>
                    </div>
                </div>

                <input type="hidden" id="currentPrimaryImage" name="productPrimaryImage" value="<?= esc($primary_media ? $primary_media['id'] : '') ?>">
            </div>

            <div class="col-12 col-md-5 mt-4 mt-md-0">

                <div class="row">
                    <div class="mb-3">
                        <label for="productTitle" class="form-label">Product Title</label>
                        <input type="text" class="form-control" id="productTitle" name="productTitle" required placeholder="Your Product Title..." value="<?= esc($product['title']) ?>">
                        <div class="invalid-feedback">
                            Please provide a product name
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <label for="productPrice" class="col-form-label">€</label>
                        </div>
                        <div class="col-auto">
                            <input type="number" min="0" step="any" id="productPrice" class="form-control" required name="productPrice" value="<?= esc($product['price']) ?>">
                            <div class="invalid-feedback">
                                Prices must be a minimum of € 0.00 (free)
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="mb-3">
                        <label for="productAvailability" class="col-form-label">Availability</label>
                        <input type="number" min="0" step="1" id="productAvailability" class="form-control" required name="productAvailability" placeholder="How many units are currently in stock" value="<?= esc($product['availability']) ?>">
                        <div class="invalid-feedback">
                            Must be a value starting at 0
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row pt-4">
            <div class="product-divider title-underline"></div>
        </div>
        <div class="row py-4">
            <div class="col">
                <div class="row">
                    <div class="col">
                        <h3>
                            Description
                        </h3>
                        <div class="mb-3">
                            <div id="pellEditor" class="pell"></div>
                            <input type="hidden" name="productDescription" value="<?= esc($product['description']) ?>" id="productDescription">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<div class="modal fade z-3000" id="addMediaModal" tabindex="-1" aria-labelledby="addMediaModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addMediaModalLabel">Add Media</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post" action="/product/media/<?= esc($is_edit ? $product['id'] : '') ?>" enctype="multipart/form-data" id="addMediaForProductForm">
                <?= csrf_field() ?>
                <div class="modal-body mx-2">
                    <p>Media to add to your product</p>
                    <div class="row">
                        <input class="form-control" type="file" id="mediaFile" name="mediaFile">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary bg-indigo" id="uploadMediaConfirmButton">Upload</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade z-3000" id="deleteMediaModal" tabindex="-1" aria-labelledby="deleteMediaModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteMediaModalLabel">Delete Media</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure that you'd like to delete this media?</p>
                <img src="" alt="Image Being Deleted" class="image-popup-img" id="deletePreviewImage">
            </div>
            <div class="modal-footer">
                <form method="post" action="/product/media/<?= esc($is_edit ? $product['id'] : '') ?>/delete">
                    <?= csrf_field() ?>
                    <input type="hidden" value="" id="deleteMediaInput" name="deleteMediaId">
                    <button type="submit" class="btn btn-secondary" id="deleteMediaConfirmButton">Delete Media</button>
                </form>
                <button type="button" class="btn btn-primary bg-indigo" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/pell"></script>
<script src="/js/product-edit.js"></script>
<link href="/css/shop.css" rel="stylesheet">

<div class="container p-outer-block-s shop-container">
    <section class="card">
        <div class="card text-white shop-banner">
            <?php if ($shop['shop_banner_img']) : ?>
                <img src="/uploads/shop/banner/<?= esc($shop['shop_banner_img']) ?>" class="card-img bg-indigo banner-img" alt="Shop banner">

                <div class="card-img-overlay d-flex align-items-end">
                    <?php if ($shop['shop_logo_img']) : ?>
                        <img src="/uploads/shop/logo/<?= esc($shop['shop_logo_img_m']) ?>" class="rounded shop-logo" alt="Shop logo">
                    <?php else : ?>
                        <div class="text-avatar shop-logo rounded">
                            <?= esc($shop['name']) ?>
                        </div>
                    <?php endif ?>
                </div>
            <?php else : ?>
                <div class="card-img-overlay d-flex align-items-end bg-indigo" style="<?= $shop['theme_color'] ? "background-color: {$shop['theme_color']};" : '' ?> <?= $shop['font_color'] ? "color: {$shop['font_color']}; " : '' ?>">
                    <?php if ($shop['shop_logo_img']) : ?>
                        <img src="/uploads/shop/logo/<?= esc($shop['shop_logo_img_m']) ?>" class="rounded shop-logo" alt="Shop logo">
                    <?php else : ?>
                        <div class="text-avatar shop-logo rounded">
                            <?= esc($shop['name']) ?>
                        </div>
                    <?php endif ?>
                    <h5 class="card-title px-3"><?= esc($shop['name']) ?></h5>
                </div>
            <?php endif ?>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col">
                    <h2 class="card-title shop-title"><?= esc($shop['name']) ?></h2>
                    <p class="text-muted"><?= esc($shop['address']) ?></p>
                    
                    <address>
                        <?php if ($shop['phone_number']) : ?>
                            <p class="text-muted"><i class="bi bi-telephone-fill color-indigo" <?= $icon_color ? "style=\"color: {$icon_color}; \"" : "" ?>></i>
                                <?= esc($shop['phone_number']) ?>
                            </p>
                        <?php endif ?>

                        <?php if ($shop['support_email']) : ?>
                            <p class="text-muted"><i class="bi bi bi-envelope color-indigo" <?= $icon_color ? "style=\"color: {$icon_color}; \"" : "" ?>></i>
                                <?= esc($shop['support_email']) ?>
                            </p>
                        <?php endif ?>
                    </address>
                </div>

                <?php if ($logged_in) : ?>
                    <div class="col">
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <?php if ($owns_shop) : ?>
                                <a href="/shop/edit" class="btn btn-primary bg-indigo" style="<?= $shop['font_color'] ? "background-color: {$shop['font_color']}; border-color: {$shop['font_color']}; " : '' ?> <?= $shop['theme_color'] ? "color: {$shop['theme_color']}; " : '' ?>">Edit Shop</a>
                                <a href="/shop/inventory" class="btn btn-primary bg-indigo" style="<?= $shop['font_color'] ? "background-color: {$shop['font_color']}; border-color: {$shop['font_color']}; " : '' ?> <?= $shop['theme_color'] ? "color: {$shop['theme_color']}; " : '' ?>">View Inventory</a>
                            <?php elseif(!$user['has_shop']) : ?>
                                <a href="/message?to=<?= esc($shop['id']) ?>" class="btn btn-primary bg-indigo" style="<?= $shop['font_color'] ? "background-color: {$shop['font_color']}; border-color: {$shop['font_color']}; " : '' ?> <?= $shop['theme_color'] ? "color: {$shop['theme_color']}; " : '' ?>">Contact Shop</a>
                            <?php endif ?>
                        </div>
                    </div>
                <?php endif ?>
            </div>
        </div>
    </section>

    <section class="card my-2">
        <div class="card-body user-editable-box" style="background-color: <?= esc($shop['theme_color']) ?? 'transparent' ?>; color: <?= esc($shop['font_color']) ?? 'inherit' ?>;">
            <p class="card-text">
                <?= $description_safe ?>
            </p>
        </div>
    </section>

    <section class="card my-2">
        <div class="card-body" id="media" style="background-color: <?= esc($shop['theme_color']) ?? 'transparent' ?>; color: <?= esc($shop['font_color']) ?? 'inherit' ?>;">
            <h5 class="card-title">Media</h5>

            <div class="row row-cols-1 row-cols-md-4 g-4">
                <?php foreach ($media as $mediaItem) : ?>
                    <article class="col">
                        <a 
                            class="media-item-clickable" 
                            data-is-video="<?= esc($mediaItem['is_video']) ?>" 
                            data-id="/uploads/shop/media/<?= esc($mediaItem['id']) ?>"
                            data-media-fullsize-img="/uploads/shop/media/<?= esc($mediaItem['thumbnail_id']) ?>" 
                            data-poster="<?= esc('/uploads/shop/media/' . $mediaItem['thumbnail_id']) ?>"
                        >
                            <div class="card h-100 media-item-container">
                                <img 
                                    src="/uploads/shop/media/<?= esc($mediaItem['thumbnail_id_l']) ?>" 
                                    class="card-img-top media-item-img" 
                                    alt="Shop uploaded media item"
                                >

                                <?php if ($owns_shop) : ?>
                                    <div class="card-img-overlay d-flex flex-row-reverse">
                                        <button 
                                            data-media-id="<?= esc($mediaItem['id']) ?>" 
                                            data-media-fullsize-img="/uploads/shop/media/<?= esc($mediaItem['thumbnail_id']) ?>" 
                                            class="btn-close media-remove-button" 
                                            aria-label="Delete Media">
                                        </button>
                                    </div>
                                <?php endif ?>
                            </div>
                        </a>
                    </article>
                <?php endforeach; ?>
                <?php if (count($media) === 0) : ?>
                    <p>
                        No media yet
                    </p>
                <?php endif ?>
            </div>

            <?php if ($owns_shop) : ?>
                <div class="d-grid gap-2 d-md-flex justify-content-md-end pt-3">
                    <button class="btn btn-primary bg-indigo" id="addMediaButton" style="<?= $shop['font_color'] ? "background-color: {$shop['font_color']}; border-color: {$shop['font_color']}; " : '' ?> <?= $shop['theme_color'] ? "color: {$shop['theme_color']}; " : '' ?>">
                        <i class="bi bi-images"></i>
                        Add Media
                    </button>
                </div>

            <?php endif ?>
        </div>
    </section>

    <section class="card my-2">
        <div class="card-body" style="background-color: <?= esc($shop['theme_color']) ?? 'transparent' ?>; color: <?= esc($shop['font_color']) ?? 'inherit' ?>;">
            <h3 class="card-title">Products</h3>
            <p class="card-text">These are products featured by <?= esc($shop['name']) ?></p>

            <div class="row row-cols-1 row-cols-md-4 g-4">

            <?php foreach ($products as $product): ?>
                <article class="col">
                    <div class="card h-100 text-dark">
                        <?php if ($product['media_thumbnail_id']): ?>
                            <img src="/uploads/shop/media/<?= esc($product['media_thumbnail_id_l']) ?>" class="card-img-top product-thumbnail-img" alt="Product thumbnail">
                        <?php else: ?>
                            <div class="rounded float-start product-thumbnail-img bg-grey-light d-flex justify-content-center">
                                <i class="bi bi-image text-white fs-1 align-self-center"></i>
                            </div>
                        <?php endif ?>
                        <div class="card-body">
                            <h5 class="card-title fs-6">
                                <a href="/product/<?= esc($product['id']) ?>" class="stretched-link text-decoration-none text-reset">
                                <?= esc($product['title']) ?>
                                </a>
                            </h5>
                            <h6 class="card-text color-indigo">€ <?= esc($product['price']) ?></h6>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="card my-2">
        <div class="card-body">
            <h5 class="card-title">Similar Products</h5>
            <p class="card-text">These are products similar to products sold by <?= esc($shop['name']) ?></p>
        </div>
    </section>
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
                <form method="post" action="/shop/media/delete">
                    <?= csrf_field() ?>
                    <input type="hidden" value="" id="deleteMediaInput" name="deleteMediaId">
                    <button type="submit" class="btn btn-secondary">Delete Media</button>
                </form>
                <button type="button" class="btn btn-primary bg-indigo" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade z-3000" id="addMediaModal" tabindex="-1" aria-labelledby="addMediaModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addMediaModalLabel">Add Media</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post" action="/shop/media/add" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="modal-body mx-2">
                    <p>Media to add to your store</p>
                    <div class="row">
                        <input class="form-control" type="file" id="mediaFile" name="mediaFile">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary bg-indigo">Upload</button>
                </div>
            </form>
        </div>
    </div>
</div>


<div class="modal fade view-media-modal z-3000" id="viewMediaModal" tabindex="-1" aria-labelledby="viewMediaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewMediaModalLabel">Store Media</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body image-view-image-container d-flex justify-content-center align-items-center">
                <img src="" class="media-view-image" alt="Media Item" id="mediaViewImage">

                <video controls src="" poster="" class="w-100 h-100" style="display: none;" id="mediaViewVideo">
                Sorry, your browser doesn't support embedded videos. <a href="" id="videoLinkAlt">Here is a link to the video instead</a>.
                </video>
            </div>
        </div>
    </div>
</div>

<script src="/js/shop.js"></script>
<link rel="stylesheet" href="/css/shop.css">

<div class="container p-outer-block">
    <div class="row">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Home</a></li>
                <li class="breadcrumb-item">
                    <a href="/shop/<?= esc($shop['id']) ?>">
                        <?= esc($shop['name']) ?>
                    </a>
                </li>
                <li class="breadcrumb-item active" aria-current="Search Result">
                    <?= esc($product['title']) ?>
                </li>
            </ol>
        </nav>
    </div>
    <div class="row">
        <div class="col-6 px-5">
            <div class="row">

                <?php if ($primary_media) : ?>
                    <img 
                        src="/uploads/shop/media/<?= esc($primary_media['is_video'] ? $primary_media['thumbnail_id'] : $primary_media['id']) ?>" 
                        class="rounded float-start product-img-current" 
                        alt="Product Photo" 
                        id="productMediaMainImage"
                    >

                <?php else: ?>
                    <div class="rounded float-start product-img-current bg-grey-light d-flex justify-content-center">
                        <i class="bi bi-image text-white fs-1 align-self-center"></i>
                    </div>
                <?php endif ?>
            </div>
            
            <div class="row row-cols-1 row-cols-md-4 g-4 product-media-select pt-4">
                <?php foreach ($media as $mediaItem) : ?>

                    <div class="col">
                        <a 
                            href="#" 
                            class="product-media-selector <?= $primary_media['id'] === $mediaItem['id'] ? 'current-selection' : '' ?> " 
                            data-is-video="<?= esc($mediaItem['is_video']) ?>" 
                            data-id="/uploads/shop/media/<?= esc($mediaItem['id']) ?>" 
                            data-poster="<?= esc('/uploads/shop/media/' . $mediaItem['thumbnail_id']) ?>"
                        >
                            <div class="card thumbnail-product-media-select h-100 <?=$primary_media['id'] === $mediaItem['id']  ? 'border-3 border-indigo' : '' ?>">
                                <img 
                                    src="/uploads/shop/media/<?= esc($mediaItem['is_video'] ? $mediaItem['thumbnail_id'] : $mediaItem['id']) ?>"
                                    class="card-img-top product-media-preview-img" 
                                    alt="Media Open Preview Thumbnail"
                                />
                            </div>
                        </a>
                    </div>

                <?php endforeach; ?>
            </div>
        </div>

        <div class="col-6">
            <div class="row">
                <h2>
                    <?= esc($product['title']) ?>
                </h2>
            </div>
            <div class="row">
                <p>
                    Sold by
                    <a href="/shop/<?= esc($shop['id']) ?>">
                        <?= esc($shop['name']) ?>
                    </a>
                </p>
            </div>
            <div class="row">
                <h3 class="color-indigo">
                    € <?= esc($product['price']) ?>
                </h3>
            </div>
            
            <form action="/cart/add" method="post">
                <?= csrf_field() ?>
                
                <input type="hidden" value="<?= esc($product['id'], 'attr') ?>" name="productId" />

                <div class="d-grid gap-2 d-md-block py-2">
                    <div class="row pb-4">
                        <div class="col col-lg-4">
                            <label for="productQuantity" class="pb-2">Quantity</label>
                            <?php if ($product['availability'] > 0) : ?>
                                <select class="form-select" aria-label="Select Product Quantity" id="productQuantity" name="productQuantity">
                                    <option value="1" selected>1</option>
                                    <?php if ($product['availability'] > 1) : ?>
                                        <option value="2">2</option>
                                    <?php endif ?>
                                    <?php if ($product['availability'] > 2) : ?>
                                        <option value="3">3</option>
                                    <?php endif ?>
                                    <?php if ($product['availability'] > 3) : ?>
                                        <option value="4">4</option>
                                    <?php endif ?>
                                    <?php if ($product['availability'] > 4) : ?>
                                        <option value="5">5</option>
                                    <?php endif ?>
                                </select>
                            <?php else : ?>
                                <select class="form-select" aria-label="Select Count" id="productQuantity" name="productQuantity" aria-label="Product out of stock" disabled>
                                    <option selected>Product out of Stock</option>
                                </select>
                            <?php endif ?>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-lg btn-primary bg-indigo" <?= $product['availability'] == 0 ? 'disabled' : '' ?>>
                        <i class="bi bi-basket px-1" aria-hidden="true"></i>
                        Add To Cart
                    </button>
                    <?php if ($logged_in) : ?>
                        <button type="button" class="btn btn-lg btn-secondary" aria-label="Add Product to your watch list">
                            <i class="bi bi-eye px-1" aria-hidden="true"></i>
                            Watch
                        </button>
                    <?php endif ?>
                </div>
            </form>
            <div class="d-grid gap-2 d-md-block">
                <?php if ($is_shop_owner) : ?>
                    <a class="btn btn-secondary" href="/product/edit/<?= esc($product['id']) ?>">Edit Product</a>
                <?php endif ?>
            </div>
        </div>
    </div>
    <div class="row pt-4">
        <div class="product-divider title-underline"></div>
    </div>
    <div class="row py-4">
        <div class="col">
            <?php if ($description_safe) : ?>
            <div class="row">
                <div class="col">
                    <h3>
                        Description
                    </h3>
                    <div class="desc-container">
                        <?= $description_safe ?>
                    </div>
                </div>
            </div>
            <?php endif ?>
            <div class="row">
                <div class="col">
                    <h3>Reviews (<?= count($reviews) ?>)

                        <span class="color-indigo px-2">
                            <?php
                            $stars = 0;

                            while ($average_score > 0 && ($average_score - 1) >= 0) {
                                echo '<i class="bi bi-star-fill" aria-hidden="true"></i>';
                                $average_score -= 1;
                                $stars += 1;
                            }

                            if (ceil($average_score) == 1) {
                                echo '<i class="bi bi-star-half" aria-hidden="true"></i>';
                            } else {
                                echo '<i class="bi bi-star" aria-hidden="true"></i>';
                            }
                            $stars += 1;

                            for (; $stars < 5; ++$stars) {
                                echo '<i class="bi bi-star" aria-hidden="true"></i>';
                            }
                            ?>

                        </span>

                    </h3>
                    <div class="container">
                        <?php foreach ($reviews as $review) : ?>
                            <div class="row py-2">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="float-end text-end">
                                            <div class="review-score color-indigo">

                                                <?php
                                                $i;
                                                for ($i = 0; $i < $review['rating']; ++$i) { ?>
                                                    <i class="bi bi-star-fill" aria-hidden="true"></i>
                                                <?php
                                                }
                                                for (; $i < 5; ++$i) {
                                                ?>
                                                    <i class="bi bi-star" aria-hidden="true"></i>
                                                <?php } ?>

                                            </div>
                                            <h6 class="text-muted">
                                                <?= date("F jS, Y", strtotime($review['timestamp'])) ?>
                                            </h6>
                                        </div>
                                        <h5 class="card-title"><?= esc($review['title']) ?></h5>
                                        <h6 class="card-subtitle mb-2 text-muted">
                                            <?= esc($review['first_name']) ?> <?= esc($review['last_name']) ?>
                                        </h6>
                                        <p class="card-text py-2">
                                            <?= esc($review['content']) ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <h4 class="pb-4">Similar Products</h4>
            <div class="row row-cols-1 row-cols-md-3 g-4">
                <?php foreach ($similar_products as $similarProduct) : ?>
                    <div class="col">
                        <div class="card h-100">
                            <?php if ($similarProduct['media_thumbnail_id']): ?>
                                <img src="/uploads/shop/media/<?= esc($similarProduct['media_thumbnail_id']) ?>" class="card-img-top similar-products-img" alt="Similar Product Thumbnail">
                            <?php else: ?>
                                <div class="rounded float-start similar-products-img bg-grey-light d-flex justify-content-center">
                                    <i class="bi bi-image text-white fs-1 align-self-center"></i>
                                </div>
                            <?php endif ?>
                            <div class="card-body">
                                <h6 class="card-title">
                                    <a href="/product/<?= esc($similarProduct['id']) ?>" class="stretched-link text-decoration-none text-reset">
                                        <?= esc($similarProduct['title']) ?>
                                    </a>
                                </h6>
                                <h6 class="card-text color-indigo">
                                    € <?= esc($similarProduct['price']) ?>
                                </h6>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<script src="/js/product.js"></script>
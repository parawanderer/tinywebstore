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
    <section class="row">
        <div class="col-12 col-md-6 px-md-5">
            <div class="row">

                <?php if ($primary_media) : ?>

                    <a href="#" id="primaryMediaContainer" class="media-item-clickable px-0" data-is-video="<?= esc($primary_media['is_video']) ?>" data-id="/uploads/shop/media/<?= esc($primary_media['id']) ?>" data-poster="<?= esc('/uploads/shop/media/' . $primary_media['thumbnail_id']) ?>">
                        <img src="/uploads/shop/media/<?= esc($primary_media['thumbnail_id']) ?>" class="rounded float-start product-img-current" alt="Product Photo" id="productMediaMainImage">
                    </a>

                <?php else : ?>
                    <div class="rounded float-start product-img-current bg-grey-light d-flex justify-content-center">
                        <i class="bi bi-image text-white fs-1 align-self-center"></i>
                    </div>
                <?php endif ?>
            </div>

            <div class="row row-cols-4 g-4 product-media-select pt-4">
                <?php foreach ($media as $mediaItem) : ?>
                    <article class="col">
                        <a href="#" class="product-media-selector <?= $primary_media['id'] === $mediaItem['id'] ? 'current-selection' : '' ?> " data-is-video="<?= esc($mediaItem['is_video']) ?>" data-id="/uploads/shop/media/<?= esc($mediaItem['id']) ?>" data-poster="<?= esc('/uploads/shop/media/' . $mediaItem['thumbnail_id']) ?>" data-media-fullsize-img="<?= esc('/uploads/shop/media/' . $mediaItem['thumbnail_id']) ?>">
                            <div class="card thumbnail-product-media-select h-100 <?= $primary_media['id'] === $mediaItem['id']  ? 'border-3 border-indigo' : '' ?>">
                                <img src="/uploads/shop/media/<?= esc($mediaItem['thumbnail_id_s']) ?>" class="card-img-top product-media-preview-img" alt="Media Open Preview Thumbnail" />
                            </div>
                        </a>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="col-12 col-md-6 pt-5 pt-md-0">
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

            <div class="d-grid gap-2 d-md-block py-2">
                <form action="/cart/add" method="post">
                    <?= csrf_field() ?>
                    <input type="hidden" value="<?= esc($product['id'], 'attr') ?>" name="productId" />

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
                        <?php if ($product['availability'] == 0 && !$is_watched) : ?>
                            <a href="/watch/add/<?= esc($product['id'], 'attr') ?>" class="btn btn-lg btn-secondary" aria-label="Add Product to your watch list">
                                <i class="bi bi-eye px-1" aria-hidden="true"></i>
                                Watch
                            </a>
                        <?php elseif ($is_watched) : ?>
                            <a href="/watch/remove/<?= esc($product['id'], 'attr') ?>" class="btn btn-lg btn-secondary" aria-label="Add Product to your watch list">
                                <i class="bi bi-eye-slash px-1" aria-hidden="true"></i>
                                Unwatch
                            </a>
                        <?php endif ?>
                    <?php endif ?>
                </form>
            </div>

            <div class="d-block">
                <?php if ($is_shop_owner) : ?>
                    <a class="btn btn-secondary" href="/product/edit/<?= esc($product['id']) ?>">Edit Product</a>
                <?php endif ?>
            </div>
        </div>
    </section>
    <div class="row pt-4">
        <div class="product-divider title-underline"></div>
    </div>
    <div class="row py-4">
        <div class="col-12 col-md-6">
            <?php if ($description_safe) : ?>
                <section class="row">
                    <div class="col">
                        <h3>
                            Description
                        </h3>
                        <div class="desc-container">
                            <?= $description_safe ?>
                        </div>
                    </div>
                </section>
            <?php endif ?>
            <section class="row">
                <div class="col">
                    <h3 id="reviews" class="py-2">Reviews (<?= count($reviews) ?>)

                        <span class="color-indigo px-2 stars-top">
                            <?php
                            $stars = 0;

                            while ($average_score > 0 && ($average_score - 1) >= 0) {
                                echo '<i class="bi bi-star-fill" aria-hidden="true"></i>';
                                $average_score -= 1;
                                $stars += 1;
                            }
                            
                            if ($stars < 5) {
                                if (ceil($average_score) == 1) {
                                    echo '<i class="bi bi-star-half" aria-hidden="true"></i>';
                                } else {
                                    echo '<i class="bi bi-star" aria-hidden="true"></i>';
                                }
                                $stars += 1;

                                for (; $stars < 5; ++$stars) {
                                    echo '<i class="bi bi-star" aria-hidden="true"></i>';
                                }
                            }
                            ?>

                        </span>

                    </h3>
                    <?php if ($can_review) : ?>
                        <div class="container p-0">
                            <div class="col-12 col-md-10 my-4">
                                <h5>
                                    Leave Your Review!
                                </h5>

                                <form action="/product/<?= esc($product['id']) ?>/review" method="post" class="row g-3" id="productReviewForm" novalidate>
                                    <?= csrf_field() ?>

                                    <div class="mb-1">
                                        <label for="reviewTitleInput" class="form-label">Title</label>
                                        <input type="text" class="form-control" id="reviewTitleInput" name="reviewTitle" required>
                                        <div class="invalid-feedback">
                                            Please provide a title for your review
                                        </div>
                                    </div>

                                    <div class="mb-1">
                                        <label aria-label="rating-selector" class="form-label">Rating</label>
                                        <input type="hidden" value="1" id="starRatingInput" name="starRating"  aria-valuemin="1" aria-valuemax="5" aria-label="Star value input">
                                        <div id="starRatingContainer" class="color-indigo">
                                            <i class="bi bi-star-fill rating-star" aria-hidden="true" id="star1" data-star-val="1" data-bs-toggle="tooltip" data-bs-placement="top" title="Very Bad"></i>
                                            <i class="bi bi-star rating-star" aria-hidden="true" id="star2" data-star-val="2" data-bs-toggle="tooltip" data-bs-placement="top" title="Poor"></i>
                                            <i class="bi bi-star rating-star" aria-hidden="true"id="star3" data-star-val="3" data-bs-toggle="tooltip" data-bs-placement="top" title="OK"></i>
                                            <i class="bi bi-star rating-star" aria-hidden="true" id="star4" data-star-val="4" data-bs-toggle="tooltip" data-bs-placement="top" title="Good"></i>
                                            <i class="bi bi-star rating-star" aria-hidden="true" id="star5" data-star-val="5" data-bs-toggle="tooltip" data-bs-placement="top" title="Excellent"></i>
                                        </div>
                                    </div>

                                    <div class="mb-1">
                                        <label for="reviewContent" class="form-label">Review Content</label>
                                        <textarea class="form-control" id="description" name="reviewContent" rows="5" required></textarea>
                                        <div class="invalid-feedback">
                                            Please provide your review
                                        </div>
                                    </div>

                                    <div class="mb-3 d-flex justify-content-end">
                                        <button type="submit" class="btn btn-primary bg-indigo align-self-end ">Leave Review</button>
                                    </div>

                                </form>
                            </div>
                        </div>
                    <?php endif ?>

                    <div class="container">
                        <?php foreach ($reviews as $review) : ?>
                            <article class="row py-2">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="float-end text-end">
                                            <div class="color-indigo">

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
                                                <time datetime="<?= esc(date("Y-m-d H:i", strtotime($review['timestamp'])), 'attr') ?>">
                                                    <?= date("F jS, Y", strtotime($review['timestamp'])) ?>
                                                </time>
                                            </h6>
                                        </div>
                                        <h5 class="card-title"><?= esc($review['title']) ?></h5>
                                        <h6 class="card-subtitle mb-2 text-muted">
                                            <?= esc($review['first_name']) ?> <?= esc($review['last_name']) ?>
                                            <?php if ($review['author_id'] == $user['id']): ?>
                                                <span class="badge rounded-pill bg-indigo mx-1">You</span>
                                            <?php endif?>
                                        </h6>
                                        <p class="card-text py-2">
                                            <?= nl2br(esc($review['content'])) ?>
                                        </p>
                                    </div>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>
        </div>
        <section class="col-12 col-md-6">
            <h4 class="pb-4">Similar Products</h4>
            <div class="row row-cols-1 row-cols-md-3 g-4">
                <?php foreach ($similar_products as $similarProduct) : ?>
                    <article class="col">
                        <div class="card h-100">
                            <?php if ($similarProduct['media_thumbnail_id_l']) : ?>
                                <img src="/uploads/shop/media/<?= esc($similarProduct['media_thumbnail_id_l']) ?>" class="card-img-top similar-products-img" alt="Similar Product Thumbnail">
                            <?php else : ?>
                                <div class="rounded float-start similar-products-img bg-grey-light d-flex justify-content-center">
                                    <i class="bi bi-image text-white fs-1 align-self-center"></i>
                                </div>
                            <?php endif ?>
                            <div class="card-body">
                                <h6 class="card-title fs-4 fs-md-6">
                                    <a href="/product/<?= esc($similarProduct['id']) ?>" class="stretched-link text-decoration-none text-reset">
                                        <?= esc($similarProduct['title']) ?>
                                    </a>
                                </h6>
                                <h6 class="card-text color-indigo fs-4 fs-md-6">
                                    € <?= esc($similarProduct['price']) ?>
                                </h6>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>
    </div>
</div>

<div class="modal fade view-media-modal z-3000" id="viewMediaModal" tabindex="-1" aria-labelledby="viewMediaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewMediaModalLabel">Product Media</h5>
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


<script src="/js/product.js"></script>
<link href="/css/shop.css" rel="stylesheet">

<div class="container p-outer-block">
    <div class="row">
        <div class="col">
            <h1 class="text-center pb-4">Newest Products</h1>

            <div class="row row-cols-1 row-cols-md-4 g-4">
                <?php foreach ($products as $product) : ?>
                    <article class="col">
                        <div class="card h-100 text-dark">
                            <?php if ($product['media_thumbnail_id']) : ?>
                                <img src="/uploads/shop/media/<?= esc($product['media_thumbnail_id_l']) ?>" class="card-img-top product-thumbnail-img" alt="Product Thumbnail">
                            <?php else : ?>
                                <div class="rounded float-start product-thumbnail-img bg-grey-light d-flex justify-content-center">
                                    <i class="bi bi-image text-white fs-1 align-self-center"></i>
                                </div>
                            <?php endif ?>
                            <div class="card-body">
                                <h4 class="card-title fs-5">
                                    <a href="/product/<?= esc($product['id']) ?>" class="stretched-link text-decoration-none text-reset">
                                        <?= esc($product['title']) ?>
                                    </a>
                                </h4>
                                <p class="fw-bold card-text color-indigo">€ <?= esc($product['price']) ?></p>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php if (count($products_seen) > 0): ?>
    <div class="row">
        <div class="col">
            <h3 class="text-center py-4">Your Last Viewed Products</h3>

            <div class="row">
                <?php foreach ($products_seen as $product) : ?>
                    <article class="col-6 col-md-2 mb-4">
                        <div class="card h-100 text-dark">
                            <?php if ($product['media_thumbnail_id']) : ?>
                                <img src="/uploads/shop/media/<?= esc($product['media_thumbnail_id_m']) ?>" class="card-img-top product-thumbnail-img-s" alt="Product Thumbnail">
                            <?php else : ?>
                                <div class="rounded float-start product-thumbnail-img-s bg-grey-light d-flex justify-content-center">
                                    <i class="bi bi-image text-white fs-1 align-self-center"></i>
                                </div>
                            <?php endif ?>
                            <div class="card-body">
                                <h5 class="card-title fs-6">
                                    <a href="/product/<?= esc($product['id']) ?>" class="stretched-link text-decoration-none text-reset">
                                        <?= esc($product['title']) ?>
                                    </a>
                                </h5>
                                <p class="fw-bold card-text color-indigo">€ <?= esc($product['price']) ?></p>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif ?>
</div>
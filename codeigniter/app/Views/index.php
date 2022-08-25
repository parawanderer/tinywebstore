<link href="/css/shop.css" rel="stylesheet">

<div class="container p-outer-block">
    <div class="row">
        <div class="col">
            <h1 class="text-center pb-4">Newest Products</h1>


            <div class="row row-cols-1 row-cols-md-4 g-4">
                <?php foreach ($products as $product) : ?>
                    <div class="col">
                        <div class="card h-100 text-dark">
                            <?php if ($product['media_thumbnail_id']) : ?>
                                <img src="/uploads/shop/media/<?= esc($product['media_thumbnail_id']) ?>" class="card-img-top product-thumbnail-img" alt="Product thumbnail">
                            <?php else : ?>
                                <div class="rounded float-start product-thumbnail-img bg-grey-light d-flex justify-content-center">
                                    <i class="bi bi-image text-white fs-1 align-self-center"></i>
                                </div>
                            <?php endif ?>
                            <div class="card-body">
                                <h5 class="card-title">
                                    <a href="/product/<?= esc($product['id']) ?>" class="stretched-link text-decoration-none text-reset">
                                        <?= esc($product['title']) ?>
                                    </a>
                                </h5>
                                <h6 class="card-text color-indigo">€ <?= esc($product['price']) ?></h6>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
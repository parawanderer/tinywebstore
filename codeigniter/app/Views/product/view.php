<link rel="stylesheet" href="/css/shop.css">

<div class="container p-outer-block">
    <div class="row">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Home</a></li>
                <li class="breadcrumb-item active" aria-current="Search Result">Search</li>
            </ol>
        </nav>
    </div>
    <div class="row">
        <div class="col">
            <div class="row">
                <img src="/img/pillows.jpg" class="rounded float-start product-img-current" alt="Product Photo">
            </div>
            <div class="row row-cols-1 row-cols-md-4 g-4 py-3">

                <div class="col">
                    <a class="product-item-select">
                        <div class="card h-100">
                            <img src="/img/pillows.jpg" class="img-thumbnail product-img-thumb" alt="Product Photo Choice">
                        </div>
                    </a>
                </div>

                <div class="col">
                    <a class="product-item-select">
                        <div class="card h-100">
                            <img src="/img/pillows.jpg" class="img-thumbnail product-img-thumb" alt="Product Photo Choice">
                        </div>
                    </a>
                </div>

            </div>
        </div>

        <div class="col">
            <div class="row">
                <h2>
                    <?= esc($product['title']) ?>
                </h2>
            </div>
            <div class="row">
                <a href="/shop/<?= esc($shop['id']) ?>">
                    <?= esc($shop['name']) ?>
                </a>
            </div>
            <div class="row py-4">
            </div>
            <div class="row">
                <h3 class="color-indigo">
                    € <?= esc($product['price']) ?>
                </h3>
            </div>

            <div class="d-grid gap-2 d-md-block py-2">
                <div class="row pb-4">
                    <div class="col col-lg-4">
                        <label for="productQuantity">Quantity</label>
                        <?php if ($product['availability'] > 0) : ?>
                            <select class="form-select" aria-label="Select Product Quantity" id="productQuantity" name="productQuantity">
                                <option value="1">1</option>
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
                
                <button type="button" class="btn btn-lg btn-primary bg-indigo py-2" <?= $product['availability'] === 0 ? 'disabled' : '' ?>>
                    <i class="bi bi-basket px-1" aria-hidden="true"></i> 
                    Add To Cart
                </button>
            </div>
            <div class="d-grid gap-2 d-md-block">
                <?php if ($is_shop_owner) : ?>
                    <a class="btn btn-secondary" href="/product/edit/<?= esc($product['id']) ?>">Edit Product</a>
                <?php endif ?>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="product-divider title-underline"></div>
    </div>
    <div class="row py-4">
        <div class="col">
            <h3>
                Description
            </h3>
            <div class="container">
                <?= $description_safe ?>
            </div>
        </div>
        <div class="col">
            <h4>Similar Products</h4>
            ...
        </div>
    </div>
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
            <?php foreach ($reviews as $review): ?>
                <div class="row py-2">
                    <div class="card">
                        <div class="card-body">
                            <div class="float-end text-end">
                                <div class="review-score color-indigo">
                                
                                    <?php
                                        $i;
                                        for ($i = 0; $i < $review['rating']; ++$i) {?>
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
        <div class="col">

        </div>
    </div>

</div>
<div class="container p-outer-block px-md-5">
    <form action="/search" method="get" id="extendedSearchForm">
        <input type="hidden" value="<?= esc($query['term'], 'attr') ?>" name="q">
        <div class="row">
            <div class="col-12 col-md-3 px-4 px-md-0">
                <aside>
                <div class="row">
                    <h5>Filter Options</h5>
                </div>

                <div class="row">
                    <h6 class="title-underline">Price</h6>
                    <div class="row g-1 align-items-center justify-content-left">
                        <div class="col-auto">
                            <span class="col-form-label">Min</span>
                        </div>
                        <div class="col-auto">
                            <input name="cmin" type="number" aria-label="Price from" min="0" max="10000" class="form-control" value="<?= esc($query['costMin'], 'attr') ?>" id="minPriceInput">
                        </div>
                        <div class="col-auto">
                            <span class="col-form-label">Max</span>
                        </div>
                        <div class="col-auto">
                            <input name="cmax" type="number" aria-label="Price until" min="0" max="10000" class="form-control" value="<?= esc($query['costMax'], 'attr') ?>" id="maxPriceInput">
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <h6 class="title-underline">Review Score</h6>
                    <div class="row g-1 align-items-center justify-content-left">
                        <div class="col-auto">
                            <span class="col-form-label">Min</span>
                        </div>
                        <div class="col-auto">
                            <input name="rs" type="number" aria-label="Min Review Score" min="1" max="5" class="form-control" value="<?= esc($query['reviewScoreMin'], 'attr') ?>">
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <h6 class="title-underline">Review Count</h6>
                    <div class="row g-1 align-items-center justify-content-left">
                        <div class="col-auto">
                            <span class="col-form-label">Min</span>
                        </div>
                        <div class="col-auto">
                            <input name="rc" type="number" aria-label="Min reviews count" min="0" class="form-control" value="<?= esc($query['reviewCount'], 'attr') ?>">
                        </div>
                    </div>
                </div>

                <div class="mb-3 form-check mt-3">
                    <input type="checkbox" class="form-check-input" id="excludeOutOfStock" name="stock" value="1" <?= $query['mustBeInStock'] ? 'checked' : '' ?>>
                    <label class="form-check-label" for="excludeOutOfStock">Exclude Out of Stock</label>
                </div>

                <div class="row py-md-3"></div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="submit" class="align-self-end btn btn-primary btn-lg bg-indigo">
                        Filter
                    </button>
                </div>
                </aside>
            </div>
            <div class="col-12 col-md-9 px-0 px-md-3 pt-4 pt-md-0">
                <main>
                <div class="container d-flex justify-content-between title-underline px-3">
                    <div>
                        <h3>Searched for "<?= esc($query['term']) ?>"</h3>
                        <p class="text-muted">Total <?= esc($total) ?> matches</p>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <p class="text-muted align-self-end p-0 m-0 mb-1">
                            Sorting:
                        </p>
                        <select class="form-select form-select-sm align-self-end" aria-label="Sorting selector" name="sort" id="sortOrderSelect">
                            <option <?= !$query['sortOption'] ? 'selected' : '' ?> value="0">Alphabetical</option>
                            <option <?= $query['sortOption'] == 1 ? 'selected' : '' ?> value="1">Price Ascending</option>
                            <option <?= $query['sortOption'] == 2 ? 'selected' : '' ?> value="2">Price Descending</option>
                            <option <?= $query['sortOption'] == 3 ? 'selected' : '' ?> value="3">Review Rating</option>
                        </select>
                    </div>
                </div>

                <div class="row w-100">
                    <?php foreach ($results as &$product) : ?>
                        <div class="p-3 my-3 title-underline">
                            <article class="d-flex position-relative">

                                <?php if ($product['media_thumbnail_id_m']) : ?>
                                    <a href="/product/<?= esc($product['id'], 'attr') ?>">
                                        <img src="/uploads/shop/media/<?= esc($product['media_thumbnail_id_m'], 'attr') ?>" class="flex-shrink-0 me-md-3 img-thumb-m rounded" alt="Product Image">
                                    </a>
                                <?php else : ?>
                                    <a href="/product/<?= esc($product['id'], 'attr') ?>">
                                        <div class="rounded float-start bg-grey-light d-flex justify-content-center img-thumb-m">
                                            <i class="bi bi-image text-white fs-1 align-self-center"></i>
                                        </div>
                                    </a>
                                <?php endif ?>

                                <div class="container-fluid px-3 px-md-4">
                                    <div class="row">
                                        <div class="col-12 col-md-8">
                                            <a href="/product/<?= esc($product['id'], 'attr') ?>" class="text-decoration-none text-reset">
                                                <h5 class="mt-0">
                                                    <?= esc($product['title']) ?>

                                                    <?php if ($product['availability'] == 0) : ?>
                                                        <span class="badge rounded-pill bg-secondary mx-2">
                                                            Out of Stock
                                                        </span>
                                                    <?php endif ?>
                                                </h5>
                                            </a>
                                            <p class="prod-search-rating mb-0 mb-md-2">
                                                <span class="color-indigo px-2 stars-top">
                                                    <?php
                                                    $stars = 0;
                                                    $average_score = $product['avg_rating'] ?? 0;

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
                                                <?php if($product['rating_count'] > 0): ?>
                                                    (<?= esc($product['rating_count']) ?>)
                                                <?php endif ?>
                                            </p>
                                            <p class="text-muted pt-2 hide-mobile">
                                                <?= ellipsize(strip_tags($product['description']), 100) ?>
                                            </p>
                                        </div>
                                        <div class="col-12 col-md-4">
                                            <div class="row">
                                                <h3 class="color-indigo fw-bold">€ <?= esc($product['price']) ?></h3>
                                            </div>
                                            <div class="row">
                                                <p class="text-small">Sold by <a href="/shop/<?= esc($product['shop_id'], 'attr') ?>"><?= esc($product['shop_name']) ?></a></p>
                                            </div>
                                            <?php if ($product['availability'] > 0) : ?>
                                                <div class="row hide-mobile">
                                                    <div class="d-grid gap-2 d-md-flex justify-content-md-start">
                                                        <button type="button" class="btn btn-primary bg-indigo">
                                                            <i class="bi bi-basket" aria-hidden="true"></i>
                                                            To Cart
                                                        </button>
                                                    </div>
                                                </div>
                                            <?php endif ?>
                                        </div>
                                    </div>

                                </div>
                            </article>
                        </div>
                    <?php endforeach ?>
                </div>
                </main>
            </div>
        </div>
    </form>
</div>

<script src="/js/search-results.js"></script>
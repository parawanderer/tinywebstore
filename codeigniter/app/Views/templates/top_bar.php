<script>
    window.AppIsLoggedIn = <?= json_encode(boolval($logged_in) ?? false) ?>;
</script>

<div class="search-overlay" id="searchOverlay" tabindex="0"></div>
<header>
    <nav aria-label="Main Site Nav" class="navbar navbar-expand-lg navbar-dark search-bar justify-content-center justify-content-md-start">
        <div class="row w-100">
        <div class="order-1 order-md-1 col-6 col-md-3 d-flex justify-content-start align-items-center">
            <a class="navbar-brand" href="/">
                <i class="bi bi-shop store-icon"></i>
                <span class="fs-4">Web Store</span>
            </a>
        </div>
        <div class="col-12 col-md-6 order-3 order-md-2 pb-2 pb-md-0">
            <form class="d-flex" action="/search" method="get">
                <div class="input-group search-container" id="searchContainer">
                    <input autocomplete="off" type="text" name="q" class="form-control" placeholder="Product Name..." aria-label="Product Name" aria-describedby="productSearchButton" id="searchBarInput">
                    <button class="btn search-button btn-light" type="submit" id="productSearchButton" aria-label="Search Button">
                        <i class="bi bi-search color-indigo"></i>
                    </button>
                    <ul class="dropdown-menu search-result-dropdown w-100" aria-label="Quick Search Results" id="searchResultsDropDown">
                        <li><a class="dropdown-item" href="#">Action</a></li>
                        <li><a class="dropdown-item" href="#">Another action</a></li>
                        <li><a class="dropdown-item" href="#">Something else here</a></li>
                    </ul>
                </div>
            </form>
        </div>
        <div class="order-2 order-md-3 col-6 col-md-3">
        <div class="justify-content-end align-items-center collapse navbar-collapse d-flex" id="navbarSupportedContent">
            <ul class="navbar-nav ml-auto mb-md-2 mb-lg-0 flex-row">
                <li class="nav-item dropdown nav-main-dropdown-container">
                    <a class="nav-link active px-2" href="#" id="cartDropDown" role="button" aria-label="Basket" <?php if (count($cart) > 0) : ?>data-bs-toggle="dropdown" aria-expanded="false" <?php endif ?>>
                        <div class="account-icon d-inline-flex">
                            <i class="bi bi-basket"></i>
                            <?php if (count($cart) > 0) : ?>
                                <span class="px-2">(<?= count($cart) ?>)</span>
                            <?php endif ?>
                        </div>
                    </a>
                    <?php if (count($cart) > 0) : ?>
                        <ul class="dropdown-menu dropdown-menu-end cart-dropdown nav-dropdown" aria-labelledby="cartDropdown">
                            <?php foreach($cart as &$product): ?>
                            <li>
                                <article class="container px-2 px-md-3">
                                    <div class="row py-2 border-bottom">
                                        <div class="col col-3">
                                            <?php if ($product['media_thumbnail_id']): ?>
                                                <a href="/product/<?= esc($product['id']) ?>">
                                                    <img src="/uploads/shop/media/<?= esc($product['media_thumbnail_id']) ?>" class="rounded img-thumbnail img-thumb-xs" alt="Product Thumbnail">
                                                </a>
                                            <?php else: ?>
                                                <div class="rounded float-start bg-grey-light d-flex justify-content-center img-thumb-xs">
                                                    <i class="bi bi-image text-white fs-4 align-self-center"></i>
                                                </div>
                                            <?php endif ?>
                                        </div>
                                        <div class="col-1 px-0">
                                            (<?= esc($product['quantity']) ?>)
                                        </div>
                                        <div class="col">
                                            <p class="m-0">
                                                <a href="/product/<?= esc($product['id']) ?>">
                                                    <?= esc($product['title']) ?>
                                                </a>
                                            </p>
                                        </div>
                                        <div class="col-2">
                                            <a href="/cart/remove/<?= esc($product['id']) ?>" class="btn btn-close" aria-label="Remove Product"></a>
                                        </div>
                                    </div>
                                </article>
                            </li>
                            <?php endforeach; ?>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="/cart"><i class="bi bi-basket" aria-hidden="true"></i> Checkout</a></li>
                        </ul>
                    <?php endif ?>
                </li>
                <?php if ($logged_in) : ?>
                    <li class="nav-item dropdown nav-main-dropdown-container">
                        <a class="nav-link active px-2" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Account">
                            <div class="account-icon d-inline-flex">
                                <i class="bi bi-person"></i>
                                <span class="username"><?= esc($user['first_name']) ?></span>
                            </div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end nav-dropdown" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="/account"><i class="bi bi-person" aria-hidden="true"></i> Account</a></li>
                            <?php if ($user['has_shop']) : ?>
                                <li><a class="dropdown-item" href="<?= esc($user['shop_url']) ?>"><i class="bi bi-shop" aria-hidden="true"></i> Shop (<?= esc($user['shop_name']) ?>)</a></li>
                                <li><a class="dropdown-item" href="/shop/stats"><i class="bi bi-bar-chart-line" aria-hidden="true"></i> Shop Stats</a></li>
                                <li><a class="dropdown-item" href="/shop/inventory"><i class="bi bi-boxes" aria-hidden="true"></i> Shop Inventory</a></li>
                                <li><a class="dropdown-item" href="/shop/orders"><i class="bi bi-boxes" aria-hidden="true"></i> Shop Orders</a></li>
                            <?php endif ?>
                            <li><a class="dropdown-item" href="/account/orders"><i class="bi bi-receipt" aria-hidden="true"></i> Your Orders</a></li>
                            <li><a class="dropdown-item" href="/account/watchlist"><i class="bi bi-eye" aria-hidden="true"></i> Watchlist</a></li>
                            <li><a class="dropdown-item" href="/account/messages"><i class="bi bi-envelope" aria-hidden="true"></i> Messages</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="/account/logout"><i class="bi bi-box-arrow-right" aria-hidden="true"></i> Logout</a></li>
                        </ul>
                    </li>
                <?php else : ?>
                    <li class="nav-item">
                        <a class="nav-link active" href="/account/login">
                            <div class="account-icon d-inline-flex">
                                <i class="bi bi-person" aria-hidden="true"></i>
                                <span class="username">Login</span>
                            </div>
                        </a>
                    </li>
                <?php endif ?>
            </ul>
        </div>
        </div>
        </div>
    </nav>
</header>
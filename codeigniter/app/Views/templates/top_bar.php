<nav class="navbar navbar-expand-lg navbar-dark search-bar">
    <div class="row w-100">
    <div class="col-3 d-flex justify-content-start align-items-center">
        <a class="navbar-brand" href="/">
            <i class="bi bi-shop store-icon"></i>
            <span class="fs-4">Web Store</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
    </div>
    <div class="col">
        <form class="d-flex" action="/search" method="get">
            <div class="input-group">
                <input type="text" class="form-control" placeholder="Product Name..." aria-label="Product Name" aria-describedby="productSearchButton">
                <button class="btn search-button btn-light" type="button" id="productSearchButton">
                    <i class="bi bi-search color-indigo" aria-label="Search Button"></i>
                </button>
            </div>
        </form>
    </div>
    <div class="col-3">
    <div class="justify-content-end align-items-center collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav ml-auto mb-2 mb-lg-0">
            <li class="nav-item dropdown">
                <a class="nav-link active" href="#" id="navbarDropdown" role="button" aria-label="Basket" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="account-icon d-inline-flex">
                        <i class="bi bi-basket" aria-label="Basket"></i>
                        <?php if (count($cart) > 0) : ?>
                            <span class="px-2">(<?= count($cart) ?>)</span>
                        <?php endif ?>
                    </div>
                </a>
                <?php if (count($cart) > 0) : ?>
                    <ul class="dropdown-menu dropdown-menu-end cart-dropdown" aria-labelledby="navbarDropdown">
                        <?php foreach($cart as &$product): ?>
                        <li>
                            <div class="container">
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
                            </div>
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
                <li class="nav-item dropdown">
                    <a class="nav-link active" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="account-icon d-inline-flex">
                            <i class="bi bi-person" aria-label="Account"></i>
                            <span class="username"><?= esc($user['first_name']) ?></span>
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
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
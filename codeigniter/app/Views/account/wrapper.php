<link rel="stylesheet" href="/css/account.css">

<div class="container p-outer-block">
  <div class="row">
    <div class="col col-lg-3 account-nav">
        <ul class="nav nav-pills flex-column mb-auto">
        <li class="<?= $page === '' ? 'nav-item' : '' ?> fs-6">
            <a href="/account" class="nav-link <?= $page === '' ? 'active' : 'link-dark' ?>" aria-current="page">
            <i class="bi bi-person fs-5" aria-hidden="true"></i>
            Account
            </a>
        </li>
        <?php if ($user['has_shop']): ?>
            <li class="<?= $page === 'shop' ? 'nav-item' : '' ?> fs-6">
                <a href="<?= esc($user['shop_url']) ?>" class="nav-link link-dark">
                <i class="bi bi-shop fs-5" aria-hidden="true"></i>
                Shop (<?= esc($user['shop_name']) ?>)
                </a>
            </li>
            <li class="<?= $page === 'shop' ? 'nav-item' : '' ?> fs-6">
                <a href="/shop/inventory" class="nav-link <?= $page === 'inventory' ? 'active' : 'link-dark' ?>">
                <i class="bi bi-boxes fs-5" aria-hidden="true"></i>
                Shop Inventory
                </a>
            </li>
        <?php endif ?> 
        <li class="<?= $page === 'orders' ? 'nav-item' : '' ?> fs-6">
            <a href="/account/orders" class="nav-link <?= $page === 'orders' ? 'active' : 'link-dark' ?>">
            <i class="bi bi-receipt fs-5" aria-hidden="true"></i>
            Your Orders
            </a>
        </li>
        <li class="<?= $page === 'watchlist' ? 'nav-item' : '' ?> fs-6">
            <a href="/account/watchlist" class="nav-link <?= $page === 'watchlist' ? 'active' : 'link-dark' ?>">
            <i class="bi bi-eye fs-5" aria-hidden="true"></i>
            Watchlist
            </a>
        </li>
        <li class="<?= $page === 'messages' ? 'nav-item' : '' ?> fs-6">
            <a href="/account/messages" class="nav-link <?= $page === 'messages' ? 'active' : 'link-dark' ?>">
            <i class="bi bi-envelope fs-5" aria-hidden="true"></i>
            Messages
            </a>
        </li>
        <li class="border-top my-3"></li>
        <li class="fs-6">
            <a href="/account/logout" class="nav-link link-dark">
            <i class="bi bi-box-arrow-right fs-5" aria-hidden="true"></i>
            Logout
            </a>
        </li>
        </ul>
    </div>
    <div class="col">
        <?= $this->renderSection('content') ?>
    </div>
  </div>
</div>
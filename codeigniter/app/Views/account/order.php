<?= $this->extend('account/wrapper') ?>


<?= $this->section('content') ?>
<div class="container">
    <div class="row">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/account/orders">Orders</a></li>
                <li class="breadcrumb-item active" aria-current="Current Order">
                    Order #<?= esc($order['id']) ?>
                </li>
            </ol>
        </nav>
    </div>
    <div class="row">
        <div class="container d-flex justify-content-between mb-3">
            <h1 class="p-0 m-0">
                Order #<?= esc($order['id']) ?>
            </h1>

            <?php if ($order['status'] == 0) : ?>
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <form action="/account/order/<?= esc($order['id']) ?>/cancel" method="post">
                        <?= csrf_field() ?>
                        <button type="submit" class="align-self-end btn btn-primary btn-lg bg-indigo">
                            Cancel Order
                        </button>
                    </form>
                </div>
            <?php endif ?>
        </div>

        <?php foreach ($order['entries'] as &$productDetails) : ?>
            <div class="card p-3 my-3">
                <div class="d-flex position-relative">

                    <?php if ($productDetails['media_thumbnail_id']) : ?>
                        <a href="/product/<?= esc($productDetails['product_id']) ?>">
                            <img src="/uploads/shop/media/<?= esc($productDetails['media_thumbnail_id']) ?>" class="flex-shrink-0 me-3 img-thumb-m rounded" alt="Product Image">
                        </a>
                    <?php elseif (!$productDetails['is_deleted']) : ?>
                        <a href="/product/<?= esc($productDetails['product_id']) ?>">
                            <div class="rounded float-start bg-grey-light d-flex justify-content-center img-thumb-m">
                                <i class="bi bi-image text-white fs-1 align-self-center"></i>
                            </div>
                        </a>
                    <?php else : ?>
                        <div class="rounded float-start bg-grey-light d-flex justify-content-center img-thumb-m">
                            <i class="bi bi-image text-white fs-1 align-self-center"></i>
                        </div>
                    <?php endif ?>

                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-9">
                                <h5 class="mt-0">
                                    <?= esc($productDetails['is_deleted'] ? $productDetails['product_title_backup'] : $productDetails['product_title']) ?>
                                    <?php if ($productDetails['quantity'] > 1) : ?>
                                        <span class="badge rounded-pill bg-secondary">&times; <?= esc($productDetails['quantity']) ?></span>
                                    <?php endif ?>

                                    <?php if ($productDetails['completed']) : ?>
                                        <span class="badge rounded-pill bg-indigo mx-2  mt-2 mt-md-0">
                                            <i class="bi bi-check-lg" aria-hidden="true"></i> Completed
                                        </span>
                                    <?php elseif ($order['status'] == 2): ?>
                                        <span class="badge rounded-pill bg-dark mt-2 mt-md-0">
                                            <i class="bi bi-x-octagon-fill" aria-hidden="true"></i> Cancelled
                                        </span>
                                    <?php else : ?>
                                        <span class="badge rounded-pill bg-warning text-dark mx-2 mt-2 mt-md-0">
                                            <i class="bi bi-hourglass-split" aria-hidden="true"></i> Pending
                                        </span>
                                    <?php endif ?>
                                </h5>
                                <h5 class="color-indigo fw-bold">€ <?= esc($productDetails['price_per_unit'] * $productDetails['quantity']) ?></h5>
                                <p>Sold by <a href="/shop/<?= esc($productDetails['shop_id']) ?>"><?= esc($productDetails['shop_name']) ?></a></p>
                            </div>
                            <div class="col-3 d-flex justify-content-end">
                                <?php if ($productDetails['completed']) : ?>
                                    <a href="/product/<?= esc($productDetails['product_id'], 'attr') ?>#reviews" class="btn btn-primary bg-indigo align-self-end mx-2">Leave Review</a>
                                <?php endif ?>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        <?php endforeach ?>

        <div class="container">
            <div class="row">
                <h3 class="pb-2">
                    Order Details
                </h3>
            </div>
            <div class="row">
                <div class="col">
                    <h6 class="m-0">Order Number</h6>
                    <p class="m-0 p-0 pb-2">#<?= esc($order['id']) ?></p>
                    <h6 class="m-0">Ordered on</h6>
                    <p class="m-0 p-0 pb-2"><?= date("F jS, Y \a\\t G\:i", strtotime($order['created'])) ?></p>
                </div>
                <div class="col">
                    <h6 class="m-0">Order Type</h6>
                    <p class="m-0 p-0 pb-2">
                        <?php if ($order['type'] == 0) : ?>
                            Delivery
                        <?php else : ?>
                            Pick Up
                        <?php endif ?>
                    </p>
                    <?php if ($order['type'] == 0) : ?>
                        <h6 class="m-0">Delivered To</h6>
                        <p class="m-0 p-0 pb-2">
                            <?= esc($order['address']) ?>
                        </p>
                    <?php else : ?>
                        <h6 class="m-0">Picked Up Scheduled</h6>
                        <p class="m-0 p-0 pb-2">
                            <?= date("F jS, Y \a\\t G\:i", strtotime($order['pickup_datetime'])) ?>
                        </p>
                    <?php endif ?>
                </div>
                <div class="col">
                    <h6 class="m-0">Cost Total</h6>
                    <p class="m-0 p-0 pb-2">
                        € <?= esc($order['price_total']) ?>
                    </p>
                    <h6 class="m-0">Status</h6>
                    <p class="m-0 p-0 pb-2">
                        <?php if ($order['status'] == 0) : ?>
                            <span class="badge rounded-pill bg-warning text-dark">
                                <i class="bi bi-hourglass-split" aria-hidden="true"></i> Pending
                            </span>
                        <?php elseif ($order['status'] == 1) : ?>
                            <span class="badge rounded-pill bg-indigo">
                                <i class="bi bi-check-lg" aria-hidden="true"></i> Completed
                            </span>
                        <?php else : ?>
                            <span class="badge rounded-pill bg-dark">
                                <i class="bi bi-x-octagon-fill" aria-hidden="true"></i> Cancelled
                            </span>
                        <?php endif ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
<?= $this->extend('account/wrapper') ?>


<?= $this->section('content') ?>
<div class="container">
    <div class="row">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/shop/orders">Shop Orders</a></li>
                <li class="breadcrumb-item active" aria-current="Current Order">
                    Order #<?= esc($order['order_id']) ?>
                </li>
            </ol>
        </nav>
    </div>
    <div class="row">
        <div class="container d-flex justify-content-between mb-3">
            <h1 class="p-0 m-0">
                Order #<?= esc($order['order_id']) ?>
            </h1>

            <?php if ($order['completed'] == 0 && $order['status'] != 2) : ?>
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <form action="/shop/order/complete" method="post">
                        <?= csrf_field() ?>
                        <input type="hidden" name="orderId" value="<?= esc($order['order_id']) ?>">
                        <button type="submit" class="align-self-end btn btn-primary btn-lg bg-indigo">
                            Complete Order
                        </button>
                    </form>
                </div>
            <?php endif ?>
        </div>

        <?php foreach ($order['entries'] as &$productDetails) : ?>
            <article class="card p-3 my-3">
                <div class="d-flex position-relative">

                    <?php if ($productDetails['media_thumbnail_id']) : ?>
                        <a href="/product/<?= esc($productDetails['product_id']) ?>">
                            <img src="/uploads/shop/media/<?= esc($productDetails['media_thumbnail_id_m']) ?>" class="flex-shrink-0 me-3 img-thumb-m rounded" alt="Product Image">
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
                            <div class="col">
                                <h5 class="mt-0">
                                    <?= esc($productDetails['product_title']) ?>
                                    <?php if ($productDetails['quantity'] > 1) : ?>
                                        <span class="badge rounded-pill bg-secondary">&times; <?= esc($productDetails['quantity']) ?></span>
                                    <?php endif ?>

                                    <?php if ($productDetails['completed']) : ?>
                                        <span class="badge rounded-pill bg-indigo mx-2">
                                            <i class="bi bi-check-lg" aria-hidden="true"></i> Completed
                                        </span>
                                    <?php elseif ($order['status'] == 2): ?>
                                        <span class="badge rounded-pill bg-dark">
                                            <i class="bi bi-x-octagon-fill" aria-hidden="true"></i> Cancelled
                                        </span>
                                    <?php else : ?>
                                        <span class="badge rounded-pill bg-warning text-dark mx-2">
                                            <i class="bi bi-hourglass-split" aria-hidden="true"></i> Pending
                                        </span>
                                    <?php endif ?>
                                </h5>
                                <h5 class="fw-bold">
                                    <span class="color-indigo">
                                        € <?= esc($productDetails['profit_total']) ?>
                                    </span>
                                    <span class="text-muted px-2 fs-6">(<?= esc($productDetails['quantity']) ?> &times; € <?= esc($productDetails['price_per_unit']) ?>)</span>
                                </h5>
                            </div>
                        </div>
                    </div>
                </div>
            </article>
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
                    <p class="m-0 p-0 pb-2">#<?= esc($order['order_id']) ?></p>
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
                    <h6 class="m-0">Profit Total</h6>
                    <p class="m-0 p-0 pb-2">
                        € <?= esc($order['profit']) ?>
                    </p>
                    <h6 class="m-0">Status</h6>
                    <p class="m-0 p-0 pb-2">
                        <?php if ($order['status'] != 2 && $order['completed'] == 0) : ?>
                            <span class="badge rounded-pill bg-warning text-dark">
                                <i class="bi bi-hourglass-split" aria-hidden="true"></i> Pending
                            </span>
                        <?php elseif ($order['completed'] == 1) : ?>
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
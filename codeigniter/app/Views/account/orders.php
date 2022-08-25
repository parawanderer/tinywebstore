<?= $this->extend('account/wrapper') ?>


<?= $this->section('content') ?>
<div class="container px-4">
    <h1>Orders</h1>
    <p class="text-muted fs-5">Your latest orders</p>
    <div class="row">
        <div class="col">
            <?php foreach ($orders as $order) : ?>
                <div class="row py-3">
                    <div class="row">
                        <div class="card p-3">
                            <div class="row">
                                <div class="col-8">
                                    <div class="card-body">
                                        <h5 class="card-title">
                                            Order #<?= esc($order['id']) ?>
                                            <?php if ($order['status'] == 0): ?>
                                                <span class="badge rounded-pill bg-warning text-dark mx-2">
                                                    <i class="bi bi-hourglass-split" aria-hidden="true"></i> Pending
                                                </span>
                                            <?php elseif ($order['status'] == 1): ?>
                                                <span class="badge rounded-pill bg-indigo mx-2">
                                                    <i class="bi bi-check-lg" aria-hidden="true"></i> Completed
                                                </span>
                                            <?php else: ?>
                                                <span class="badge rounded-pill bg-dark mx-2">
                                                    <i class="bi bi-x-octagon-fill" aria-hidden="true"></i> Cancelled
                                                </span>
                                            <?php endif ?>
                                        </h5>
                                        <h4 class="card-text color-indigo">
                                            € <?= esc($order['price_total']) ?>
                                        </h4>
                                        <p class="card-text"><small class="text-muted"><?= date("F jS, Y", strtotime($order['created'])) ?></small></p>
                                    </div>
                                </div>
                                <div class="col-4 d-flex justify-content-end">
                                    <a href="/account/order/<?= esc($order['id'], 'attr') ?>" class="btn btn-primary bg-indigo align-self-end mx-2">Details</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach ?>
            <?php if (count($orders) === 0) : ?>
            <div class="container">
                <p class="text-muted">
                    You have no orders
                </p>
            </div>

        <?php endif ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
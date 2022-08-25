<?= $this->extend('account/wrapper') ?>


<?= $this->section('content') ?>
<link rel="stylesheet" href="/css/shop.css">

<div class="container px-4">
    <div class="row mb-3">
        <div class="container d-flex justify-content-between">
            <div>
                <h1>Orders (<?= count($orders) ?>)</h1>
                <p class="text-muted fs-5">Latest orders for <?= esc($shop['name']) ?></p>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">Order Id</th>
                        <th scope="col">Order Value</th>
                        <th scope="col">Date</th>
                        <th scope="col">Type</th>
                        <th scope="col">Status</th>
                        <th scope="col"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($orders as &$order): ?>
                    <tr class="align-middle">
                        <th scope="row">
                            #<?= esc($order['id']) ?>
                        </th>
                        <th>
                            € <?= esc($order['shop_total_price']) ?>
                        </th>
                        <td>
                            <h6 class="text-muted">
                                <?= date("F jS, Y", strtotime($order['created'])) ?>
                            </h6>
                        </td>
                        <td>
                            <?php if ($order['type'] == 0) : ?>
                                Delivery
                            <?php else : ?>
                                Pick Up
                            <?php endif ?>
                        </td>
                        <td>
                            <?php if ($order['completed'] == 0 && $order['status'] != 2) : ?>
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
                        </td>
                        <td>
                            <a class="btn btn-primary bg-indigo" href="/shop/order/<?= esc($order['id']) ?>" role="button">
                                Details
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <?php if (count($orders) === 0): ?>
                <div class="container">
                    <p class="text-muted">
                        Your shop has no products
                    </p>
                </div>

            <?php endif ?>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
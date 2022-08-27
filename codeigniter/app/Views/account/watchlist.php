<?= $this->extend('account/wrapper') ?>


<?= $this->section('content') ?>
<div class="container px-0 px-md-4">
    <h1>My Watchlist (<?= count($watchlist) ?>)</h1>
    <p class="text-muted fs-5 pb-4">Your watched products. You will receive an alert when one of these becomes available.</p>
    <div class="row">
        <div class="col">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col" class="hide-mobile"></th>
                        <th scope="col">Item</th>
                        <th scope="col">Added On</th>
                        <th scope="col">Price</th>
                        <th scope="col">Status</th>
                        <th scope="col"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($watchlist as $product) : ?>
                        <tr class="align-middle">
                            <th class="hide-mobile">
                                <?php if ($product['media_thumbnail_id_s']) : ?>
                                    <a href="/product/<?= esc($product['product_id']) ?>">
                                        <img src="/uploads/shop/media/<?= esc($product['media_thumbnail_id_s']) ?>" class="img-thumbnail img-thumb-s" alt="Image thumbnail">
                                    </a>
                                <?php else : ?>
                                    <div class="rounded float-start bg-grey-light d-flex justify-content-center img-thumb-s">
                                        <i class="bi bi-image text-white fs-1 align-self-center"></i>
                                    </div>
                                <?php endif ?>
                            </th>
                            <th>
                                <?php if ($product['title']) : ?>
                                    <a href="/product/<?= esc($product['product_id']) ?>" class="text-decoration-none text-reset">
                                        <?= esc($product['title']) ?>
                                    </a>
                                <?php else : ?>
                                    <?= esc($product['fallback_title']) ?>
                                <?php endif ?>
                            </th>

                            <td>
                                <h6 class="text-muted">
                                    <?= date("F jS, Y", strtotime($product['created'])) ?>
                                </h6>
                            </td>
                            <td>
                                <?php if ($product['price']) : ?>
                                    € <?= esc($product['price']) ?>
                                <?php endif ?>
                            </td>

                            <td>
                                <?php if ($product['availability'] === null) : ?>
                                    <span class="badge rounded-pill bg-dark">Removed Product</span>
                                <?php elseif ($product['availability'] == 0): ?>
                                    <span class="badge rounded-pill bg-secondary">Out of Stock</span>
                                <?php else : ?>
                                    <span class="badge rounded-pill text-light bg-indigo">Available</span>
                                <?php endif ?>
                            </td>

                            <td>
                                <a class="btn btn-primary bg-indigo" href="/watch/remove/<?= esc($product['id'], 'attr') ?>" role="button">
                                    <i class="bi bi-eye-slash" aria-hidden="true"></i>
                                    Unwatch
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

            <?php if (count($watchlist) === 0): ?>
            <div class="container">
                <p class="text-muted">
                    You have no watched products
                </p>
            </div>

            <?php endif ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
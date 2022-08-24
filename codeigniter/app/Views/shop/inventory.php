<?= $this->extend('account/wrapper') ?>


<?= $this->section('content') ?>
<link rel="stylesheet" href="/css/shop.css">

<div class="container px-4">
    <div class="row mb-3">
        <div class="container d-flex justify-content-between">
            <div>
                <h1>Inventory</h1>
                <p class="text-muted fs-5">Inventory for <?= esc($shop['name']) ?></p>
            </div>    

            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <a href="/product/create" class="align-self-end btn btn-primary btn-lg bg-indigo" >
                    Add New
                </a>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col"></th>
                        <th scope="col">Item</th>
                        <th scope="col">Price</th>
                        <th scope="col">Availability</th>
                        <th scope="col">Status</th>
                        <th scope="col"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                    <tr>
                        <th scope="row">
                            <a href="/product/<?= esc($product['id']) ?>">
                                <img src="/img/pillows.jpg" class="img-thumbnail inventory-thumbnail" alt="Image thumbnail">
                            </a>
                        </th>
                        <th>
                            <a href="/product/<?= esc($product['id']) ?>" class="text-decoration-none text-reset">
                                <?= esc($product['title']) ?>
                            </a>
                        </th>
                        <td>€ <?= esc($product['price']) ?></td>
                        <td><?= esc($product['availability']) ?></td>
                        <td>
                            <?php if ($product['availability'] === 0): ?>
                                <span class="badge rounded-pill bg-secondary">Out of Stock</span>
                            <?php else: ?>
                                <span class="badge rounded-pill bg-indigo">Available</span>
                            <?php endif ?>
                        </td>
                        <td>
                            <a class="btn btn-primary bg-indigo" href="/product/edit/<?= esc($product['id']) ?>" role="button">
                                <i class="bi bi-pencil-fill" aria-hidden="true"></i>
                                Edit
                            </a>
                            <a class="btn btn-primary bg-indigo" href="/product/delete/<?= esc($product['id']) ?>" role="button">
                                <i class="bi bi-trash3-fill" aria-hidden="true"></i>
                                Delete
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

        </div>
    </div>
</div>
<?= $this->endSection() ?>
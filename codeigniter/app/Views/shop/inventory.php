<?= $this->extend('account/wrapper') ?>


<?= $this->section('content') ?>
<link rel="stylesheet" href="/css/shop.css">

<div class="container px-4">
    <div class="row mb-3">
        <div class="container d-flex justify-content-between">
            <div>
                <h1>Inventory (<?= count($products) ?>)</h1>
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
                    <tr class="align-middle">
                        <th scope="row">
                            <?php if ($product['media_thumbnail_id']): ?>
                                <a href="/product/<?= esc($product['id']) ?>">
                                    <img src="/uploads/shop/media/<?= esc($product['media_thumbnail_id']) ?>" class="img-thumbnail inventory-thumbnail" alt="Image thumbnail">
                                </a>
                            <?php else: ?>
                                <a href="/product/<?= esc($product['id']) ?>">
                                    <div class="rounded float-start bg-grey-light d-flex justify-content-center inventory-thumbnail">
                                        <i class="bi bi-image text-white fs-1 align-self-center"></i>
                                    </div>
                                </a>
                            <?php endif ?>
                        </th>
                        <th>
                            <a href="/product/<?= esc($product['id']) ?>" class="text-decoration-none text-reset">
                                <?= esc($product['title']) ?>
                            </a>
                        </th>
                        <td>€ <?= esc($product['price']) ?></td>
                        <td><?= esc($product['availability']) ?></td>
                        <td>
                            <?php if ($product['availability'] == 0): ?>
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
                            <a class="btn btn-primary bg-indigo product-delete-button" data-id="<?= esc($product['id'], 'attr') ?>" data-product-name="<?= esc($product['title'], 'attr') ?>" href="#" role="button">
                                <i class="bi bi-trash3-fill" aria-hidden="true"></i>
                                Delete
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <?php if (count($products) === 0): ?>
                <div class="container">
                    <p class="text-muted">
                        Your shop has no products
                    </p>
                </div>

            <?php endif ?>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteProductModal" tabindex="-1" aria-labelledby="deleteProductModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteProductModalLabel">Delete Product ""</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure that you'd like to delete this product? This action cannot be reversed.</p>
            </div>
            <div class="modal-footer">
                <form method="post" action="/product/delete" id="productDeleteForm">
                    <?= csrf_field() ?>
                    <input type="hidden" value="" id="deleteProductInput" name="deleteProductId">
                    <button type="submit" class="btn btn-secondary" id="productDeleteButton">Delete Product</button>
                </form>
                <button type="button" class="btn btn-primary bg-indigo" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<script src="/js/inventory.js"></script>

<?= $this->endSection() ?>
<div class="container p-outer-block">
    <h2 class="text-underline">Your Cart</h2>
    
    <div class="row">
        <div class="col">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col"></th>
                        <th scope="col">Item</th>
                        <th scope="col">Price</th>
                        <th scope="col">Quantity</th>
                        <th scope="col">Status</th>
                        <th scope="col"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cart as $product): ?>
                    <tr>
                        <th scope="row">
                            <?php if ($product['media_thumbnail_id']): ?>
                                <a href="/product/<?= esc($product['id']) ?>">
                                    <img src="/uploads/shop/media/<?= esc($product['media_thumbnail_id']) ?>" class="img-thumbnail img-thumb-s" alt="Image thumbnail">
                                </a>
                            <?php else: ?>
                                <div class="rounded float-start bg-grey-light d-flex justify-content-center img-thumb-s">
                                    <i class="bi bi-image text-white fs-1 align-self-center"></i>
                                </div>
                            <?php endif ?>
                        </th>
                        <th>
                            <a href="/product/<?= esc($product['id']) ?>" class="text-decoration-none text-reset">
                                <?= esc($product['title']) ?>
                            </a>
                        </th>
                        <td>€ <?= esc($product['price']) ?></td>
                        <td><?= esc($product['quantity']) ?></td>
                        <td>
                            <?php if ($product['availability'] >= $product['quantity']): ?>
                                <span class="badge rounded-pill bg-indigo">Available</span>
                            <?php elseif ($product['availability'] > 0): ?>
                                <span class="badge rounded-pill bg-primary">
                                    <?= esc($product['availability']) ?> / <?= esc($product['quantity']) ?> Available
                                </span>
                            <?php else: ?>
                                <span class="badge rounded-pill bg-secondary">Out of Stock</span>
                            <?php endif ?>
                        </td>
                        <td>
                            <a class="btn btn-primary bg-indigo" href="/cart/remove/<?= esc($product['id']) ?>" role="button">
                                <i class="bi bi-trash3-fill" aria-hidden="true"></i>
                                Remove
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php if ($logged_in): ?>
        Login stuff
    <?php else: ?>
        <p class="text-muted">
            You are not logged in. Please log in if you wish to check out
        </p>
        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
            <a href="/account/login" class="align-self-end btn btn-primary btn-lg bg-indigo" >
                Login
            </a>
        </div>
    <?php endif ?>
</div>
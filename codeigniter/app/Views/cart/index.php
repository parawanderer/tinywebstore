<link rel="stylesheet" href="/css/checkout.css">

<div class="container p-outer-block">
    <h1 class="pb-3">Your Cart</h1>

    <div class="row">
        <div class="col">
            <div class="table-responsive">
                <table class="table" summary="A list of the products currently in your cart, as well as the current availability of your choices. Checking out will only allow you to purchase up to the available products">
                    <thead>
                        <tr>
                            <th scope="col"></th>
                            <th scope="col">Item</th>
                            <th scope="col">Price</th>
                            <th scope="col">Quantity</th>
                            <th scope="col">Status</th>
                            <th scope="col">Seller</th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cart as $product) : ?>
                            <tr  class="align-middle">
                                <th>
                                    <?php if ($product['media_thumbnail_id']) : ?>
                                        <a href="/product/<?= esc($product['id']) ?>" class="d-none d-md-inline">
                                            <img src="/uploads/shop/media/<?= esc($product['media_thumbnail_id']) ?>" class="img-thumbnail img-thumb-s" alt="Image thumbnail">
                                        </a>
                                    <?php else : ?>
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
                                    <?php if ($product['availability'] >= $product['quantity']) : ?>
                                        <span class="badge rounded-pill bg-indigo">Available</span>
                                    <?php elseif ($product['availability'] > 0) : ?>
                                        <span class="badge rounded-pill bg-primary">
                                            <?= esc($product['availability']) ?> / <?= esc($product['quantity']) ?> Available
                                        </span>
                                    <?php else : ?>
                                        <span class="badge rounded-pill bg-secondary">Out of Stock</span>
                                    <?php endif ?>
                                </td>
                                <td>
                                    <a href="/shop/<?= esc($product['shop_id']) ?>" target="_blank">
                                        <?= esc($product['shop_name']) ?>
                                    </a>
                                </td>
                                <td>
                                    <a class="btn btn-primary bg-indigo" href="/cart/remove/<?= esc($product['id']) ?>" role="button">
                                        <i class="bi bi-trash3-fill" aria-label="Remove"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <tr class="align-middle">
                            <th colspan="6" class="fw-bold fs-5">Total</th>
                            <td class="text-end color-indigo fw-bold fs-5">
                                € <?= esc($priceTotal) ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php if ($logged_in) : ?>
        <div class="container">
            <h2 class="pt-4">Checkout</h2>
            <h6 class="text-muted pb-3">Please choose your preferred checkout option</h6>

            <div class="accordion" id="checkoutAccordion">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="deliveryOptionHeader1">
                        <button class="accordion-button accordion-button-indigo fs-4" id="buttonDeliveryOptDeliver" type="button" data-opens="deliveryOptionsPanelDeliver" aria-expanded="true" aria-controls="deliveryOptionsPanelDeliver">
                            <i class="bi bi-truck" aria-hidden="true"></i>
                            <span class="px-2">Delivery</span>
                        </button>
                    </h2>
                    <div id="deliveryOptionsPanelDeliver" class="accordion-collapse collapse show" aria-labelledby="deliveryOptionHeader1">
                        <div class="accordion-body">

                            <form action="/cart/checkout" method="post" class="row g-3 needs-validation" id="checkoutFormDelivery" novalidate>
                                <?= csrf_field() ?>

                                <input type="hidden" name="deliveryType" value="0">

                                <div class="row g-3 align-items-center">
                                    <div class="col-2">
                                        <label for="address" class="col-form-label">Address</label>
                                    </div>
                                    <div class="col">
                                        <input required type="text" class="form-control" id="address" name="address" placeholder="Appelstraat 123 bus 2" value="<?= esc($userDetails['address'], 'attr') ?>">
                                        <div class="invalid-feedback">
                                            Please provide an address
                                        </div>
                                    </div>
                                </div>

                                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                    <button type="submit" class="btn btn-lg btn-primary bg-indigo">Purchase</button>
                                </div>

                            </form>

                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="deliveryOptionHeader2">
                        <button class="accordion-button accordion-button-indigo fs-4 collapsed" id="buttonDeliveryOptPickup" type="button" data-opens="deliveryOptionsPanelPickup" aria-expanded="false" aria-controls="deliveryOptionsPanelPickup">
                            <i class="bi bi-person" aria-hidden="true"></i>
                            <span class="px-2">Pick Up</span>
                        </button>
                    </h2>
                    <div id="deliveryOptionsPanelPickup" class="accordion-collapse collapse" aria-labelledby="deliveryOptionHeader2">
                        <div class="accordion-body">

                            <form action="/cart/checkout" method="post" class="row g-3 needs-validation" id="checkoutFormPickup" novalidate>
                                <?= csrf_field() ?>

                                <input type="hidden" name="deliveryType" value="1">
                                
                                <p class="text-muted">
                                    Here is a list of shops and their addresses from which you will be able to pick up your products.
                                    Refer to the product list above to see what product belongs to what shop.<br/><br/>
                                    The shops will prepare the products to be available starting at the given time.
                                </p>

                                <table class="table" summary="A list of the stores you have ordered products from and their locations and contact details.">
                                    <thead>
                                        <tr>
                                            <th scope="col">Shop Name</th>
                                            <th scope="col">Address</th>
                                            <th scope="col">Phone Number</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($shops as $shop) : ?>
                                            <tr>
                                                <td><?= esc($shop['name']) ?></td>
                                                <td><?= esc($shop['address']) ?></td>
                                                <td><?= esc($shop['phone_number']) ?></td>
                                            </tr>
                                        <?php endforeach ?>
                                    </tbody>
                                </table>

                                <div class="row g-3 align-items-center">
                                    <div class="col-3">
                                        <label for="address" class="col-form-label">Pickup Date/Time</label>
                                    </div>
                                    <div class="col">
                                        <input required type="datetime-local" class="form-control" name="pickupTime" value="<?= date('Y-m-d\TH:i', time() + 86400) ?>" min="<?= date('Y-m-d\TH:i', time() + 86400) ?>" max="<?= date('Y-m-d\TH:i', time() + 1209600) ?>" placeholder="Choose a date and a time..." value="<?= esc($userDetails['address'], 'attr') ?>">
                                        <div class="invalid-feedback">
                                            Please choose a pick up date
                                        </div>
                                    </div>
                                </div>

                                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                    <button type="submit" class="btn btn-lg btn-primary bg-indigo">Purchase</button>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <?php else : ?>
        <p class="text-muted">
            You are not logged in. Please log in if you wish to check out
        </p>
        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
            <a href="/account/login" class="align-self-end btn btn-primary btn-lg bg-indigo">
                Login
            </a>
        </div>
    <?php endif ?>
</div>

<script src="/js/cart.js"></script>
<link rel="stylesheet" href="/css/checkout.css">
<div class="container success-container text-center">
    <h1 class="pb-3">Checkout Success!</h1>

    <h6 class="text-muted pb-3">
        Your order with a value of <span class="color-indigo fw-bold">€ <?= esc($order_value) ?></span> has been checked out successfully!
    </h6>
    <div class="d-grid gap-2 d-md-flex justify-content-md-center">
        <a href="/" class="btn btn-lg btn-primary bg-indigo mx-3">Home</a>
        <a href="/account/orders" class="btn btn-lg btn-primary bg-indigo mx-3">Order History</a>
    </div>
</div>
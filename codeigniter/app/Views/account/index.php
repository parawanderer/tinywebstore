<?= $this->extend('account/wrapper') ?>


<?= $this->section('content') ?>
    <div class="container px-4">

        <h1>Account Info</h1>
        
        <p class="text-muted fs-5">Welcome, <?= esc($user['first_name']) ?></p>
        <br/>


        <div class="row">
            <div class="col col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            Your Info 
                        </h4>
                        <p class="card-text">
                            <span class="d-block"><?= esc($user['first_name']) ?> <?= esc($user['last_name']) ?></span>
                            <span class="d-block"><?= esc($user_address) ?></span>
                            <span class="d-block">
                                <?= esc($user['username']) ?>
                                <?php if ($user['has_shop']): ?>
                                    <span class="badge bg-primary">Store Manager</span>
                                <?php else: ?>
                                    <span class="badge bg-indigo-bright">User</span> 
                                <?php endif ?>
                            </span>
                        </p>
                        <a href="#" class="btn btn-primary bg-indigo">Edit Details</a>
                    </div>
                </div>
            </div>
            <div class="col col-lg-4">
            <?php if ($user['has_shop']): ?>
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            Your Shop
                        </h4>
                        <p class="card-text">
                            <?= esc($user['shop_name']) ?><br/>
                        </p>
                        <a href="<?= esc($user['shop_url']) ?>" class="btn btn-primary bg-indigo">Manage Shop</a>
                    </div>
                </div>
            <?php endif ?> 
            </div>
            <div class="col col-lg-4"></div>
        </div>
    </div>
<?= $this->endSection() ?>
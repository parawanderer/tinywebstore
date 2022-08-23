<?= $this->extend('account/wrapper') ?>


<?= $this->section('content') ?>
    <div class="container px-4">
        <h1>Account Info</h1>
        <p class="text-muted fs-5">Welcome, <?= esc($user['first_name']) ?></p>
        <br/>
        <h3>Your Info 
            <?php if ($user['has_shop']): ?>
                <span class="badge bg-primary">Store Manager</span>
            <?php else: ?>
                <span class="badge bg-indigo-bright">User</span> 
            <?php endif ?> 
        </h3>
        <p class="fs-6"><?= esc($user['first_name']) ?> <?= esc($user['last_name']) ?></p>
        <p class="fs-6"><?= esc($user_address) ?></p>
        <p class="fs-6"><?= esc($user['username']) ?></p>
    </div>
<?= $this->endSection() ?>
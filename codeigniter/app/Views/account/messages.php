<?= $this->extend('account/wrapper') ?>


<?= $this->section('content') ?>
<div class="container px-2 px-md-4"">
    <h1>Messages</h1>
    <p class="text-muted fs-5 pb-1">Your message history</p>
    <div class="container p-0 m-0">
        <div class="container">
            <div class="row px-3 py-2">
                <div class="col-9 d-flex justify-content-start align-items-center">
                    <h6>
                        <?php if($is_shop): ?>
                            Started By
                        <?php else: ?>
                            Started With
                        <?php endif ?>
                    </h6>
                </div>
                <div class="col-3 d-flex justify-content-end align-items-center">
                    <h6>Last Message</h6>
                </div>
            </div>
        </div>

        <?php foreach ($messages as $message) : ?>
            <div class="card d-flex position-relative mb-2">
                <div class="row px-3 py-2">
                    <div class="col-1 d-flex justify-content-end align-items-center hide-mobile">
                        <?php if($is_shop): ?>
                            <div class="rounded float-start bg-indigo d-flex justify-content-center align-items-center img-thumb-xs">
                                <span class="text-white fw-bold text-uppercase text-center">
                                    <?= esc(substr($message['user_first_name'], 0, 1) . substr($message['user_last_name'], 0, 1)) ?>
                                </span>
                            </div>
                        <?php else: ?>
                            <?php if($message['shop_logo_img']): ?>
                                <img src="/uploads/shop/logo/<?= esc($message['shop_logo_img']) ?>" class="img-thumbnail img-thumb-xs" alt="Shop thumbnail">
                            <?php else: ?>
                                <div class="rounded float-start bg-indigo d-flex justify-content-center align-items-center img-thumb-xs">
                                    <span class="text-white fw-bold text-uppercase text-center"><?= esc($message['shop_name']) ?></span>
                                </div>
                            <?php endif ?>
                        <?php endif ?>
                    </div>
                    <div class="col-8 d-flex justify-content-start align-items-center">
                        <div>
                            <a href="/account/message/<?= esc($message['id']) ?>" class="text-decoration-none text-reset stretched-link">
                                <h5 class="mt-0">
                                    <?php if($is_shop): ?>
                                        <?= esc($message['user_first_name']) ?> <?= esc($message['user_last_name']) ?>
                                    <?php else: ?>
                                        <?= esc($message['shop_name']) ?>
                                    <?php endif ?>
                                </h5>
                            </a>
                        </div>
                    </div>
                    <div class="col-3 d-flex justify-content-end align-items-center">
                        <?php if($message['updated']): ?>
                        <h6 class="text-muted">
                            <?= date("F jS, Y \a\\t G\:i", strtotime($message['updated'])) ?>
                        </h6>
                        <?php endif ?>
                    </div>
                </div>
            </div>

        <?php endforeach ?>
        <?php if (count($messages) === 0) : ?>
            <div class="container">
                <p class="text-muted">
                    You have no messages
                </p>
            </div>

        <?php endif ?>
    </div>
</div>
<?= $this->endSection() ?>
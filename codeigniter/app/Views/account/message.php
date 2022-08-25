<?= $this->extend('account/wrapper') ?>


<?= $this->section('content') ?>
<div class="container">
    <div class="row">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/account/messages">Messages</a></li>
                <li class="breadcrumb-item active" aria-current="Conversation with
                    <?php if ($is_shop) : ?>
                        <?= esc($sender['first_name']) ?> <?= esc($sender['last_name']) ?>
                    <?php else : ?>
                        <?= esc($shop['name']) ?>
                    <?php endif ?>
                ">
                    <?php if ($is_shop) : ?>
                        <?= esc($sender['first_name']) ?> <?= esc($sender['last_name']) ?>
                    <?php else : ?>
                        <?= esc($shop['name']) ?>
                    <?php endif ?>
                </li>
            </ol>
        </nav>
    </div>
    <div class="row">
        <div class="container d-flex title-underline">
            <div class=" d-flex justify-content-start align-items-center">
                <?php if ($is_shop) : ?>
                    <div class="rounded float-start bg-indigo d-flex justify-content-center align-items-center img-thumb-s">
                        <span class="text-white fw-bold text-uppercase"><?= esc(substr($sender['first_name'], 0, 1) . substr($sender['last_name'], 0, 1)) ?></span>
                    </div>
                <?php else : ?>
                    <?php if ($shop['shop_logo_img']) : ?>
                        <a href="/shop/<?= esc($shop['id']) ?>">
                            <img src="/uploads/shop/logo/<?= esc($shop['shop_logo_img']) ?>" class="img-thumbnail img-thumb-s" alt="Shop thumbnail">
                        </a>
                    <?php else : ?>
                        <a href="/shop/<?= esc($shop['id']) ?>">
                            <div class="rounded float-start bg-indigo d-flex justify-content-center align-items-center img-thumb-s">
                                <span class="text-white fw-bold text-uppercase text-center"><?= esc($shop['name']) ?></span>
                            </div>
                        </a>
                    <?php endif ?>
                <?php endif ?>
            </div>
            <div class="px-3 d-flex justify-content-start align-items-center">
                <h4 class="mt-0">
                    <?php if ($is_shop) : ?>
                        <?= esc($sender['first_name']) ?> <?= esc($sender['last_name']) ?>
                    <?php else : ?>
                        <a href="/shop/<?= esc($shop['id']) ?>" class="text-decoration-none text-reset">
                            <?= esc($shop['name']) ?>
                        </a>
                    <?php endif ?>
                </h4>
            </div>
        </div>
    </div>
    <div class="row py-4 overflow-auto max-height-85 title-underline" id="messageList">
        <?php foreach ($messages as &$message) : ?>

            <div class="row mt-2">
                <div class="col-1">
                    <div class="row">
                        <div class="col d-flex justify-content-center align-items-end">
                            <?php if ($message['from_user']) : ?>
                                <div class="rounded float-start bg-indigo d-flex justify-content-center align-items-center img-thumb-xs">
                                    <span class="text-white fw-bold text-uppercase"><?= esc(substr($sender['first_name'], 0, 1) . substr($sender['last_name'], 0, 1)) ?></span>
                                </div>
                            <?php else : ?>
                                <?php if ($shop['shop_logo_img']) : ?>
                                    <a href="/shop/<?= esc($shop['id']) ?>">
                                        <img src="/uploads/shop/logo/<?= esc($shop['shop_logo_img']) ?>" class="img-thumbnail img-thumb-xs" alt="Shop thumbnail">
                                    </a>
                                <?php else : ?>
                                    <a href="/shop/<?= esc($shop['id']) ?>">
                                        <div class="rounded float-start bg-indigo d-flex justify-content-center align-items-center img-thumb-xs">
                                            <span class="text-white fw-bold text-uppercase text-center"><?= esc($shop['shop_name']) ?></span>
                                        </div>
                                    </a>
                                <?php endif ?>
                            <?php endif ?>
                        </div>
                    </div>
                    <div class="row pt-2">
                        <div class="col d-flex justify-content-center align-items-end">
                            <p class="text-muted">
                                <?php
                                $time = strtotime($message['timestamp']);
                                $now = time();
                                $diff = $now - $time;

                                if ($diff < 86400) {
                                    echo date("G\:i", $time);
                                } else if ($diff < 31536000) {
                                    echo date("M jS", $time);
                                } else {
                                    echo date("M Y", $time);
                                }
                                ?>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-11">
                    <div class="card text-dark bg-light mb-3 border-0">
                        <div class="card-body">
                            <h6 class="card-title">
                                <?php if ($message['from_user']) : ?>
                                    <?= esc($sender['first_name']) ?> <?= esc($sender['last_name']) ?>
                                <?php else : ?>
                                    <?= esc($shop['name']) ?>
                                <?php endif ?>
                            </h6>
                            <p class="card-text">
                                <?= nl2br(esc($message['message'])) ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

        <?php endforeach ?>

        <?php if (count($messages) === 0): ?>
            <div class="row p-3">
                <p class="text-muted">
                    No messages in this conversation yet...
                </p>
            </div>
        <?php endif ?>
    </div>
    <div class="row p-4">
        <form method="post" action="/account/message/<?= esc($conversation['id']) ?>" id="messageForm">
            <?= csrf_field() ?>
            <div class="input-group input-group mb-3" >
                <textarea name="contentInput" data-rows-min="1" aria-multiline="true" class="form-control" id="contentInput" placeholder="Enter your message..." aria-label="Your Message" aria-describedby="sendMessageButton" rows="1"></textarea>
                <button class="btn btn-primary bg-indigo" type="submit" id="sendMessageButton" disabled aria-disabled="Disabled until message is input">Send Message</button>
            </div>
        </form>
    </div>
</div>

<script src="/js/message.js"></script>
<?= $this->endSection() ?>
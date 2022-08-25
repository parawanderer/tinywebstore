<ul id="resultContainer">
    <?php foreach ($results as &$product) : ?>
        <li>
            <div class="d-flex position-relative dropdown-item">
                <div class="px-2">
                    <?php if ($product['media_thumbnail_id']) : ?>
                        <a href="/product/<?= esc($product['id']) ?>">
                            <img src="/uploads/shop/media/<?= esc($product['media_thumbnail_id'], 'attr') ?>" class="img-thumbnail img-thumb-xs" alt="Image thumbnail">
                        </a>
                    <?php else : ?>
                        <div class="rounded float-start bg-grey-light d-flex justify-content-center img-thumb-xs">
                            <i class="bi bi-image text-white fs-1 align-self-center"></i>
                        </div>
                    <?php endif ?>
                </div>
                <div>
                    <a href="/product/<?= esc($product['id'], 'attr') ?>" class="stretched-link text-decoration-none text-reset">
                        <h6 class="m-0 p-0"><?= esc($product['title']) ?></h6>
                    </a>
                    <p class="color-indigo">€ <?= esc($product['price']) ?></p>
                </div>
            </div>
        </li>
    <?php endforeach ?>
</ul>
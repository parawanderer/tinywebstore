<link href="/css/shop.css" rel="stylesheet">
<!-- lightweight editor to add "advanced" feature of creating more complex descriptions since that was part of the project and i do not want to write an editor from scratch -->
<link rel="stylesheet" type="text/css" href="https://unpkg.com/pell/dist/pell.min.css"> 

<div class="container p-outer-block">
  <div class="row justify-content-md-center">
    <div class="col col-lg-6 login-form-block">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/shop/<?= esc($shop['id']) ?>">Shop</a></li>
                <li class="breadcrumb-item active" aria-current="Edit Shop">Edit</li>
            </ol>
        </nav>

        <h3>Edit Shop "<?= esc($shop['name']) ?></b>"</h3>

        <?php if ($error): ?>
            <div class="alert alert-danger" role="alert">
                <?= service('validation')->listErrors() ?>
            </div>
        <?php endif ?>

        <form action="/shop/edit" method="post" class="row g-3" id="shopEditForm" enctype="multipart/form-data" novalidate>    

            <?= csrf_field() ?>

            <?php if ($shop['shop_banner_img']): ?>
                <div class="card text-white shop-banner-edit">
                    <img src="/uploads/shop/banner/<?= esc($shop['shop_banner_img']) ?>" class="card-img bg-indigo banner-img" alt="Shop header image">
                </div>
            <?php endif ?>
            
            <div class="row align-items-end py-3">
                <div class="col-6">
                    <label for="shopBanner" class="form-label">Shop Banner</label>
                    <input class="form-control" type="file" id="shopBanner" name="shopBanner">
                </div>
                <?php if ($shop['shop_banner_img']): ?>
                    <div class="col-6">
                        <input type="checkbox" class="form-check-input" id="removeCurrentBanner" name="removeCurrentBanner" value="1">
                        <label class="form-check-label" for="removeCurrentBanner">Remove Current</label>
                    </div>
                <?php endif ?>
            </div>


            <div class="row">
                <?php if ($shop['shop_logo_img']): ?>
                    <div class="col-3">
                        <img src="/uploads/shop/logo/<?= esc($shop['shop_logo_img']) ?>" class="img-thumbnail shop-logo" alt="Shop logo image">
                    </div>
                <?php endif ?>


                <div class="col-6">
                    <label for="shopLogo" class="form-label">Shop Logo</label>
                    <input class="form-control" type="file" id="shopLogo" name="shopLogo">

                    <?php if ($shop['shop_logo_img']): ?>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="removeCurrentLogo" name="removeCurrentLogo" value="1">
                            <label class="form-check-label" for="removeCurrentLogo">Remove Current</label>
                        </div>
                    <?php endif ?>
                </div>
            </div>


            <div class="col-12">
                <label for="shopName" class="form-label">Shop Name</label>
                <input type="text" class="form-control" id="shopName" name="shopName" required value="<?= esc($shop['name']) ?>">
                <div class="invalid-feedback">
                    Please provide a name for your shop
                </div>
            </div>

            
            <h5 class="title-underline py-3">Theme</h5>
            
            <div class="row">
                <div class="col-4">
                    <label for="backgroundColor" class="form-label">Background</label>
                    <input type="color" class="form-control form-control-color" id="backgroundColor" name="backgroundColor" value="<?= esc($shop['theme_color'] ?? '#ffffff') ?>" title="Choose your background color">
                    <div class="invalid-feedback">
                        Contrast ratio too low!
                    </div>
                </div>
                <div class="col-4">
                    <label for="textColor" class="form-label">Text Color</label>
                    <input type="color" class="form-control form-control-color" id="textColor" name="textColor" value="<?= esc($shop['font_color'] ?? '#000000') ?>" title="Choose your text color">
                    <div class="invalid-feedback">
                        Contrast ratio too low!
                    </div>
                </div>
            </div>

            <h5 class="title-underline py-3">About</h5>

            

            <div class="mb-3">
                <label class="form-label">Description</label>
                <div id="pellEditor" class="pell"></div>
                <input type="hidden" name="description" value="<?= esc($shop['description']) ?>" id="description">
            </div>
            
            <div class="col-12">
                <label for="phoneNumber" class="form-label">Phone Number</label>
                <input type="text" class="form-control" id="phoneNumber" name="phoneNumber" value="<?= esc($shop['phone_number']) ?>">
            </div>

            <div class="col-12">
                <label for="supportEmail" class="form-label">Support Email</label>
                <input type="email" class="form-control" id="supportEmail" name="supportEmail" required value="<?= esc($shop['support_email']) ?>">
            </div>
            

            <div class="mb-3">
                <label for="address" class="form-label">Address</label>
                <textarea class="form-control" id="description" name="address" rows="5"><?= esc($shop['address']) ?></textarea>
            </div>


            <div class="col-12">
                <button type="submit" class="btn btn-primary bg-indigo" id="registerUserButton">Update Shop</button>
            </div>
        </form>
    </div>
  </div>
</div>


<script src="https://unpkg.com/pell"></script>
<script src="/js/shop-edit.js"></script>
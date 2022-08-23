<div class="container p-outer-block">
  <div class="row justify-content-md-center">
    <div class="col col-lg-6 login-form-block">
        <h2 class="title-underline">Register New User</h2>

        <?php if ($already_exists): ?>
            <div class="alert alert-primary" role="alert">
                <h4 class="alert-heading">User already exists!</h4>
                <p>The email <b><?= esc($email) ?></b> is already a registered user account. Try to <a href="/account/login" class="alert-link">log in here</a>.
                </p>
            </div>

        <?php elseif ($success): ?>
            <div class="alert alert-success" role="alert">
                <h4 class="alert-heading">User registered!</h4>
                <p>The email <b><?= esc($email) ?></b> has been successfully registered as a new user account!
                </p>
            </div>

        <?php else: ?>
            
            <?php if ($error): ?>
                <div class="alert alert-danger" role="alert">
                    <?= service('validation')->listErrors() ?>
                </div>
            <?php endif ?>

            <form action="/account/register" method="post" class="row g-3" id="registrationForm" novalidate>
                <?= csrf_field() ?>

                <div class="col-12">
                    <label for="email" class="form-label">Email</label>
                    <?php if (!empty($email)): ?>
                        <input type="email" aria-label="readonly input" readonly class="form-control" id="email" name="email" value="<?= esc($email) ?>">
                    <?php else: ?>
                        <input type="email" class="form-control" id="email" name="email" required>
                    <?php endif ?>
                    <div class="invalid-feedback">
                        Please provide a valid email
                    </div>
                </div>

                <div class="col-md-6">
                    <label for="firstName" class="form-label">First Name</label>
                    <input type="text" class="form-control" id="firstName" name="firstName" required>
                    <div class="invalid-feedback">
                        Please provide your first name
                    </div>
                </div>
                <div class="col-md-6">
                    <label for="lastName" class="form-label">Last Name</label>
                    <input type="text" class="form-control" id="lastName" name="lastName" required>
                    <div class="invalid-feedback">
                        Please provide your last name
                    </div>
                </div>
                <div class="col-12">
                    <label for="address" class="form-label">Address</label>
                    <input type="text" class="form-control" id="address" name="address" required placeholder="Appelstraat 123 bus 2">
                    <div class="invalid-feedback">
                        Please provide an address
                    </div>
                </div>
                <div class="col-md-8">
                    <label for="city" class="form-label">City</label>
                    <input type="text" class="form-control" id="city" name="city" required>
                    <div class="invalid-feedback">
                        Please provide a city
                    </div>
                </div>
                <div class="col-md-4">
                    <label for="zipCode" class="form-label">Zip</label>
                    <input type="text" class="form-control" id="zipCode" name="zipCode" required>
                    <div class="invalid-feedback">
                        Please provide a zip code
                    </div>
                </div>
                <div class="col-12">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                    <div class="invalid-feedback">
                        Your password must be at least 10 characters long
                    </div>
                </div>
                <div class="col-12">
                    <label for="repeatPassword" class="form-label">Repeat Password</label>
                    <input type="password" class="form-control" id="repeatPassword" name="repeatPassword" required>
                    <div class="invalid-feedback">
                        Your passwords must match
                    </div>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary bg-indigo" id="registerUserButton">Register User</button>
                </div>
            </form>

        <?php endif ?>
    </div>
  </div>
</div>

<script src="/js/register.js"></script>
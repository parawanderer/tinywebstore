<link href="/css/login.css" rel="stylesheet">

<div class="container p-outer-block">
  <div class="row justify-content-md-center">
    <section class="col-12 col-md-4 login-form-block">
        <h3>Login</h3>

        
        <?php if ($error): ?>
            <div class="alert alert-danger" role="alert">
                <?php if ($generic_login_error): ?>
                    Failed to log you in
                <?php else: ?>
                    <?= service('validation')->listErrors() ?>
                <?php endif ?>
            </div>
        <?php endif ?>

        <form action="/account/login" method="post">
            <?= csrf_field() ?>

            <div class="mb-3">
                <label for="loginEmail" class="form-label">Email address</label>
                <input type="email" class="form-control" id="loginEmail" name="loginEmail">
            </div>
            <div class="mb-3">
                <label for="loginPassword" class="form-label">Password</label>
                <input type="password" class="form-control" id="loginPassword" name="loginPassword">
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="loginRememberMe" name="loginRememberMe">
                <label class="form-check-label" for="loginRememberMe">Remember me</label>
            </div>
            <div class="col-12 d-flex justify-content-end">
                <button type="submit" class="btn btn-primary bg-indigo">Login</button>
            </div>
        </form>
    </section>
    <section class="col-12 col-md-4 line-left signup-form-block">
        <h3>Signup</h3>

        <h6>New user?</h6>
        <p class="text-muted">Sign up for a new account here. </p>
        <p class="text-muted">
        Store owners must contact our <a href="mailto:ficticious-support@email.test">support</a> to set up an account
        </p>

        <?= session()->getFlashdata('error') ?>
        <?= service('validation')->listErrors() ?>

        <form action="/account/register" method="post">
            <?= csrf_field() ?>

            <div class="mb-3">
                <label for="createUserEmail" class="form-label">Email address</label>
                <input type="email" class="form-control" id="createUserEmail" name="email">
            </div>
            <div class="col-12 d-flex justify-content-end">
                <button type="submit" class="btn btn-primary bg-indigo">Sign Up</button>
            </div>
        </form>
    </section>
  </div>
</div>

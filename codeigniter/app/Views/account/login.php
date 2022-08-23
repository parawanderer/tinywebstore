<link href="/css/login.css" rel="stylesheet">

<div class="container p-outer-block">
  <div class="row justify-content-md-center">
    <div class="col col-lg-4 login-form-block">
        <h3>Login</h3>

        <?= session()->getFlashdata('error') ?>
        <?= service('validation')->listErrors() ?>

        <form action="/account/login" method="post">
            <?= csrf_field() ?>

            <form>
                <div class="mb-3">
                    <label for="loginEmail" class="form-label">Email address</label>
                    <input type="email" class="form-control" id="loginEmail" name="loginEmail">
                </div>
                <div class="mb-3">
                    <label for="loginPassword" class="form-label">Password</label>
                    <input type="password" class="form-control" id="loginPassword" name="loginPassword">
                </div>
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="loginRememberMe">
                    <label class="form-check-label" for="loginRememberMe" name="loginRememberMe">Remember me</label>
                </div>
                <button type="submit" class="btn btn-primary bg-indigo">Login</button>
            </form>
        </form>
    </div>
    <div class="col col-lg-4 line-left signup-form-block">
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

            <form>
                <div class="mb-3">
                    <label for="createUserEmail" class="form-label">Email address</label>
                    <input type="email" class="form-control" id="createUserEmail" name="email">
                </div>
                <button type="submit" class="btn btn-primary bg-indigo">Sign Up</button>
            </form>
        </form>
    </div>
  </div>
</div>

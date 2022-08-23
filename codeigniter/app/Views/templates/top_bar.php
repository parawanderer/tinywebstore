<nav class="navbar navbar-expand-lg navbar-dark search-bar">
  <div class="container-fluid">
    <a class="navbar-brand" href="/">
        <i class="bi bi-shop store-icon"></i> 
        <span class="fs-4">Web Store</span>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
  </div>
  <div class="container-fluid">
    <form class="d-flex">
        <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
        <button class="btn btn-outline-success" type="submit">Search</button>
      </form>
  </div>
  <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link active" href="#" aria-label="Basket"><i class="bi bi-basket"></i></a>
        </li>
        <?php if ($logged_in): ?>
            <li class="nav-item dropdown">
                <a class="nav-link active" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="account-icon d-inline-flex">  
                        <i class="bi bi-person" aria-label="Account"></i>
                        <span class="username"><?= esc($user['first_name']) ?></span>
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                    <li><a class="dropdown-item" href="/account"><i class="bi bi-person" aria-hidden="true"></i> Account</a></li>
                    <li><a class="dropdown-item" href="/account/orders"><i class="bi bi-receipt" aria-hidden="true"></i> Orders</a></li>
                    <li><a class="dropdown-item" href="/account/watchlist"><i class="bi bi-eye" aria-hidden="true"></i> Watchlist</a></li>
                    <li><a class="dropdown-item" href="/account/messages"><i class="bi bi-envelope" aria-hidden="true"></i> Messages</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="/account/logout"><i class="bi bi-box-arrow-right" aria-hidden="true"></i> Logout</a></li>
                </ul>
            </li>
        <?php else: ?>
            <li class="nav-item">
                <a class="nav-link active" href="/account/login">
                    <div class="account-icon d-inline-flex">
                        <i class="bi bi-person" aria-hidden="true"></i>
                        <span class="username">Login</span>
                    </div>
                </a>
            </li>
        <?php endif ?>
      </ul>
    </div>
</nav>
<?= $this->extend('account/wrapper') ?>


<?= $this->section('content') ?>
    <div class="container px-4">
        <h1>Watchlist</h1>
        <p class="text-muted fs-5">Your watched items</p>
        <br/>
        <div class="row">
            <div class="col">
            <table class="table">
            <thead>
                <tr>
                <th scope="col">#</th>
                <th scope="col">Item</th>
                <th scope="col">Added Date</th>
                <th scope="col">Availability</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th scope="row">1</th>
                    <td>iPhone</td>
                    <td>DATE</td>
                    <td><span class="badge rounded-pill bg-success">Available</span></td>
                    </tr>
                <tr>
                    <th scope="row">2</th>
                    <td>iPad</td>
                    <td>DATE</td>
                    <td><span class="badge rounded-pill bg-secondary">Out of Stock</span></td>
                </tr>
                <tr>
                    <th scope="row">3</th>
                    <td>Samsung Galaxy Phone</td>
                    <td>DATE</td>
                    <td><span class="badge rounded-pill bg-secondary">Out of Stock</span></td>
                </tr>
            </tbody>
        </table>

            </div>
        </div>
    </div>
<?= $this->endSection() ?>
<?= $this->extend('account/wrapper') ?>


<?= $this->section('content') ?>
    <div class="container px-4">
        <h1>Orders</h1>
        <p class="text-muted fs-5">Your latest orders</p>
        <br/>
        <div class="row">
            <div class="col">
            <table class="table">
            <thead>
                <tr>
                <th scope="col">#</th>
                <th scope="col">First</th>
                <th scope="col">Last</th>
                <th scope="col">Handle</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                <th scope="row">1</th>
                <td>Mark</td>
                <td>Otto</td>
                <td>@mdo</td>
                </tr>
                <tr>
                <th scope="row">2</th>
                <td>Jacob</td>
                <td>Thornton</td>
                <td>@fat</td>
                </tr>
                <tr>
                <th scope="row">3</th>
                <td colspan="2">Larry the Bird</td>
                <td>@twitter</td>
                </tr>
            </tbody>
        </table>

            </div>
        </div>
    </div>
<?= $this->endSection() ?>
<?= $this->extend('account/wrapper') ?>


<?= $this->section('content') ?>
<link rel="stylesheet" href="/css/shop.css">

<div class="container px-0 px-md-4">
    <div class="row mb-3">
        <h1>Statistics</h1>
        <p class="text-muted fs-5">Statistics for <?= esc($shop['name']) ?></p>
    </div>
    <div class="row">
        <div class="col-6 col-md-3 mb-3 mb-md-0">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title color-indigo"><?= esc($customers30d) ?></h5>
                    <p class="card-text">Customers / 30 Days</p>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-3 mb-md-0">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title color-indigo"><?= esc($orders30d) ?></h5>
                    <p class="card-text">Orders / 30 Days</p>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-3 mb-md-0">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title color-indigo">€ <?= esc($profit30d) ?></h5>
                    <p class="card-text">Profit / 30 Days</p>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-3 mb-md-0">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title color-indigo">€ <?= esc($avgProfit30d) ?></h5>
                    <p class="card-text">Avg Sale / 30 Days</p>
                </div>
            </div>
        </div>
    </div>
    <div class="row pt-3">
        <div class="col-12 col-md-8">
            <div class="row">
                <h2>Sales by Date <span class="badge rounded-pill bg-indigo mx-2 fs-6">7 Days</span></h2>
                <div>
                    <canvas id="salesByDateChart"></canvas>
                </div>
            </div>
            <div class="row">
                <h3 class="py-3 pt-5">
                    Top Selling Products <span class="badge rounded-pill bg-indigo mx-2 fs-6">Last 30 Days</span>
                </h3>
                <div class="table-responsive">
                <table class="table" summary="A list of the top 10 best selling products in the last 30 days">
                    <thead>
                        <tr>
                            <th scope="col"></th>
                            <th scope="col">Product Name</th>
                            <th scope="col">Units Sold</th>
                            <th scope="col">Profit</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($top10selling as $product): ?>
                        <tr class="align-middle">
                            <th>
                                <?php if ($product['media_thumbnail_id']): ?>
                                    <a href="/product/<?= esc($product['product_id']) ?>">
                                        <img src="/uploads/shop/media/<?= esc($product['media_thumbnail_id_xs']) ?>" class="img-thumbnail img-thumb-xs" alt="Image thumbnail">
                                    </a>
                                <?php else: ?>
                                    <a href="/product/<?= esc($product['product_id']) ?>">
                                        <div class="rounded float-start bg-grey-light d-flex justify-content-center img-thumb-xs">
                                            <i class="bi bi-image text-white fs-1 align-self-center"></i>
                                        </div>
                                    </a>
                                <?php endif ?>
                            </th>
                            <th>
                                <a href="/product/<?= esc($product['product_id']) ?>" class="text-decoration-none text-reset">
                                    <?= esc($product['title']) ?>
                                </a>
                            </th>
                            <td><?= esc($product['units_sold']) ?></td>
                            <td>€ <?= esc($product['sales_value']) ?></td>
                        </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="row">
                <div class="card py-3">
                    <h6>8 Week Sale History</h6>
                    <div>
                        <canvas id="sales8wChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- I assume the exercise is in getting the stats, not building my own graph library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js" integrity="sha512-ElRFoEQdI5Ht6kZvyzXhYG9NqjtkmlkfYk0wr6wHxU9JEHakS7UJZNeml5ALk+8IKlU6jDgMabC3vkumRokgJA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script>
    const ctx = document.getElementById('salesByDateChart').getContext('2d');
    const ctx8w = document.getElementById('sales8wChart').getContext('2d');

    const DATA = <?= json_encode($last7dStats) ?>.reverse();
    const DATA8WEEKS = <?= json_encode($last8wStats) ?>.reverse();

    // https://www.chartjs.org/docs/latest/getting-started/

    function generateDatasets(data) {
        const labels = [];
        const dataPurchases = [];
        const dataProfit = [];
        for (let i = 0; i < data.length; ++i) {
            labels.push(data[i][0]);
            dataPurchases.push(parseInt(data[i][1] || 0));
            dataProfit.push(parseInt(data[i][2] || 0));
        }

        return [labels, dataPurchases, dataProfit];
    }

    // 7 days
    const [labels, dataPurchases, dataProfit] = generateDatasets(DATA);

    const data = {
        labels: labels,
        datasets: [{
                label: 'Purchases',
                data: dataPurchases,
                borderColor: 'rgb(102 16 242)',
                fill: false,
                cubicInterpolationMode: 'monotone',
                tension: 0.4,
                yAxisID: 'y',
            },
            {
                label: 'Profits',
                data: dataProfit,
                borderColor: 'rgb(242 170 16)',
                fill: false,
                cubicInterpolationMode: 'monotone',
                tension: 0.4,
                yAxisID: 'y1',
            }
        ],
    };

    const mainChart = new Chart(ctx, {
        type: 'line',
        data: data,
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: 'Purchase & Profit statistics: Last 30 days',
                },
            },
            interaction: {
                intersect: false,
            },
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',

                    // grid line settings
                    grid: {
                        drawOnChartArea: false, // only want the grid lines for one axis to show up
                    },
                },
            },
        },
    });


    // 8 weeks
    const [labels2, dataPurchases2, dataProfit2] = generateDatasets(DATA8WEEKS);

    const data2 = {
        labels: labels2,
        datasets: [
            {
                label: 'Profits',
                data: dataProfit2,
                borderColor: 'rgb(242 170 16)',
                fill: false,
                cubicInterpolationMode: 'monotone',
                tension: 0.4,
                yAxisID: 'y',
            }
        ],
    };

    const chart8Weeks = new Chart(ctx8w, {
        type: 'line',
        data: data2,
        options: {
            responsive: true,
            interaction: {
                intersect: false,
            },
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                }
            },
        },
    });

</script>

<!-- <script src="/js/stats.js"></script> -->
<?= $this->endSection() ?>
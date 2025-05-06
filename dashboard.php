<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: auth/login.php");
    exit();
}
include 'config/db.php';
include 'includes/header.php';

$user_id = $_SESSION["user_id"];
$is_admin = $_SESSION["is_admin"];

// Count queries
$where_clause = $is_admin ? '' : "WHERE user_id = $user_id";
$count_brands = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM master_brands $where_clause"))['total'];
$count_categories = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM categories $where_clause"))['total'];
$count_items = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM items $where_clause"))['total'];
$count_users = $is_admin ? mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users"))['total'] : mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE is_admin = '0'"))['total'];


$recent_query = "
    (SELECT 'Brand' AS type, code, name, status, created_at FROM master_brands " . (!$is_admin ? "WHERE user_id = $user_id" : "") . " ORDER BY id DESC LIMIT 5)
    UNION
    (SELECT 'Category' AS type, code, name, status, created_at FROM categories " . (!$is_admin ? "WHERE user_id = $user_id" : "") . " ORDER BY id DESC LIMIT 5)
    UNION
    (SELECT 'Item' AS type, code, name, status, created_at FROM items " . (!$is_admin ? "WHERE user_id = $user_id" : "") . " ORDER BY id DESC LIMIT 5)
    ORDER BY created_at DESC
    LIMIT 5
";
$recent_logs = mysqli_query($conn, $recent_query);




?>

<div class="container-fluid">
    <h2 class="mb-4">Welcome, <?= $_SESSION["user_name"]; ?> <?php if ($_SESSION["is_admin"]) echo "<small class='text-danger'>(Admin)</small>"; ?></h2>

    <!-- Section 1: Stats -->
    <div class="row mb-3">
        <div class="col-md-3">
            <div class="card text-white bg-dark">
                <div class="card-body text-center">
                    <h4><?= $count_users ?></h4>
                    <p class="card-text">Total Users</p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-white bg-primary">
                <div class="card-body text-center">
                    <h4><?= $count_brands ?></h4>
                    <p class="card-text">Total Brands</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success">
                <div class="card-body text-center">
                    <h4><?= $count_categories ?></h4>
                    <p class="card-text">Total Categories</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-info">
                <div class="card-body text-center">
                    <h4><?= $count_items ?></h4>
                    <p class="card-text">Total Items</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Section 2: Recent Activities -->
    <div class="mb-4">
        <h4>Recent Activity</h4>
        <table class="table table-bordered table-sm table-hover">
            <thead class="table-light">
                <tr>
                    <th>Type</th>
                    <th>Code</th>
                    <th>Name</th>
                    <th>Status</th>
                    <th>Created At</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($recent_logs)) { ?>
                    <tr>
                        <td><span class="badge bg-secondary"><?= htmlspecialchars($row['type']) ?></span></td>
                        <td><?= htmlspecialchars($row['code']) ?></td>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td><span class="badge bg-<?= $row['status'] === 'Active' ? 'success' : 'danger' ?>"><?= htmlspecialchars($row['status']) ?></span></td>
                        <td><?= $row['created_at'] ?? '—' ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>


    <!-- Section 3: Chart -->
    <div>
        <h4>Distribution</h4>
        <canvas id="itemChart" height="100"></canvas>
    </div>
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('itemChart').getContext('2d');
    const itemChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Brands', 'Categories', 'Items'],
            datasets: [{
                label: 'Record Count',
                data: [<?= $count_brands ?>, <?= $count_categories ?>, <?= $count_items ?>],
                backgroundColor: ['#0d6efd', '#198754', '#0dcaf0']
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>

<?php include 'includes/footer.php'; ?>
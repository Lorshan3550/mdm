<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: ../auth/login.php");
    exit();
}

include '../config/db.php';
include '../includes/header.php';


$user_id = $_SESSION["user_id"];
$is_admin = $_SESSION["is_admin"];

$search_code = $_GET['code'] ?? '';
$search_name = $_GET['name'] ?? '';
$search_status = $_GET['status'] ?? '';

$filter = "WHERE 1=1";
if (!$is_admin) {
    $filter .= " AND items.user_id = $user_id";
}
if (!empty($search_code)) {
    $filter .= " AND items.code LIKE '%" . mysqli_real_escape_string($conn, $search_code) . "%'";
}
if (!empty($search_name)) {
    $filter .= " AND items.name LIKE '%" . mysqli_real_escape_string($conn, $search_name) . "%'";
}
if (!empty($search_status)) {
    $filter .= " AND items.status = '" . mysqli_real_escape_string($conn, $search_status) . "'";
}

$limit = 5;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Count total items for pagination
$count_sql = "SELECT COUNT(*) as total FROM items 
              JOIN master_brands ON items.brand_id = master_brands.id 
              JOIN categories ON items.category_id = categories.id 
              $filter";
$total_items = mysqli_fetch_assoc(mysqli_query($conn, $count_sql))['total'];
$total_pages = ceil($total_items / $limit);


// $sql = "SELECT items.*, master_brands.name AS brand_name, categories.name AS category_name 
//         FROM items 
//         JOIN master_brands ON items.brand_id = master_brands.id 
//         JOIN categories ON items.category_id = categories.id 
//         $filter
//         ORDER BY items.id DESC 
//         LIMIT 5";

$sql = "SELECT items.*, master_brands.name AS brand_name, categories.name AS category_name 
        FROM items 
        JOIN master_brands ON items.brand_id = master_brands.id 
        JOIN categories ON items.category_id = categories.id 
        $filter
        ORDER BY items.id DESC 
        LIMIT $limit OFFSET $offset";


$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Item List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2 class="mb-4">Search Items</h2>
    
    <form method="GET" class="row g-3 align-items-end mb-4">
        <div class="col-md-3">
            <label for="code" class="form-label">Item Code</label>
            <input type="text" name="code" id="code" class="form-control" value="<?= htmlspecialchars($search_code) ?>">
        </div>
        <div class="col-md-3">
            <label for="name" class="form-label">Item Name</label>
            <input type="text" name="name" id="name" class="form-control" value="<?= htmlspecialchars($search_name) ?>">
        </div>
        <div class="col-md-3">
            <label for="status" class="form-label">Status</label>
            <select name="status" id="status" class="form-select">
                <option value="">All Status</option>
                <option value="Active" <?= $search_status === 'Active' ? 'selected' : '' ?>>Active</option>
                <option value="Inactive" <?= $search_status === 'Inactive' ? 'selected' : '' ?>>Inactive</option>
            </select>
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-primary">Search</button>
            <a href="index.php" class="btn btn-outline-secondary">Reset</a>
        </div>
    </form>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h4">Your Items</h2>
        <div>
            <a href="export_csv.php" target="_blank" class="btn btn-outline-success me-2">Export CSV</a>
            <a href="create.php" class="btn btn-success">+ Add Item</a>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle">
            <thead class="table-dark">
                <tr>
                    <th>Code</th>
                    <th>Name</th>
                    <th>Brand</th>
                    <th>Category</th>
                    <th>Attachment</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                <tr>
                    <td><?= htmlspecialchars($row['code']) ?></td>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= htmlspecialchars($row['brand_name']) ?></td>
                    <td><?= htmlspecialchars($row['category_name']) ?></td>
                    <td>
                        <?php if (!empty($row['attachment'])): ?>
                            <a href="<?= $row['attachment'] ?>" target="_blank" class="btn btn-sm btn-outline-primary">View</a>
                        <?php else: ?>
                            <span class="text-muted">No File</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <span class="badge <?= $row['status'] === 'Active' ? 'bg-success' : 'bg-secondary' ?>">
                            <?= $row['status'] ?>
                        </span>
                    </td>
                    <td>
                        <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-primary">Edit</a>
                        <a href="delete.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger"
                           onclick="return confirm('Delete this item?')">Delete</a>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>

    <nav>
    <ul class="pagination justify-content-center">
        <?php if ($page > 1): ?>
            <li class="page-item">
                <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $page - 1])) ?>">Previous</a>
            </li>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>"><?= $i ?></a>
            </li>
        <?php endfor; ?>

        <?php if ($page < $total_pages): ?>
            <li class="page-item">
                <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $page + 1])) ?>">Next</a>
            </li>
        <?php endif; ?>
    </ul>
</nav>


    <a href="../dashboard.php" class="btn btn-outline-secondary mt-3">← Back to Dashboard</a>
</div>
</body>
</html>

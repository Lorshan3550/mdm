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

$limit = 5;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Base query
$where_clause = $is_admin ? '' : "WHERE user_id = $user_id";

// Get total number of categories
$count_sql = "SELECT COUNT(*) AS total FROM categories $where_clause";
$total_categories = mysqli_fetch_assoc(mysqli_query($conn, $count_sql))['total'];
$total_pages = ceil($total_categories / $limit);

// Fetch paginated results
$sql = "SELECT * FROM categories $where_clause ORDER BY id DESC LIMIT $limit OFFSET $offset";
$result = mysqli_query($conn, $sql);


// if ($is_admin) {
//     $sql = "SELECT * FROM categories $where_clause ORDER BY id DESC LIMIT $limit OFFSET $offset";
// } else {
//     $sql = "SELECT * FROM categories $where_clause ORDER BY id DESC LIMIT $limit OFFSET $offset";
// }

// $result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Your Categories</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Your Categories</h2>
            <a href="create.php" class="btn btn-success">+ Add Category</a>
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-bordered align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Code</th>
                        <th>Name</th>
                        <th>Status</th>
                        <th style="width: 150px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                        <tr>
                            <td><?= htmlspecialchars($row['code']) ?></td>
                            <td><?= htmlspecialchars($row['name']) ?></td>
                            <td><span class="badge bg-<?= $row['status'] === 'Active' ? 'success' : 'secondary' ?>">
                                    <?= htmlspecialchars($row['status']) ?></span>
                            </td>
                            <td>
                                <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-primary">Edit</a>
                                <a href="delete.php?id=<?= $row['id'] ?>"
                                    class="btn btn-sm btn-danger"
                                    onclick="return confirm('Delete this category?')">Delete</a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <nav>
            <ul class="pagination justify-content-center mt-3">
                <?php if ($page > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?= $page - 1 ?>">Previous</a>
                    </li>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>

                <?php if ($page < $total_pages): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?= $page + 1 ?>">Next</a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>


        <a href="../dashboard.php" class="btn btn-outline-secondary mt-3">← Back to Dashboard</a>
    </div>
</body>

</html>
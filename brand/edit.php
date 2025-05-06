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

if (!isset($_GET['id'])) {
    echo "No brand selected.";
    exit();
}

$brand_id = (int) $_GET['id'];

$query = $is_admin
    ? "SELECT * FROM master_brands WHERE id = $brand_id"
    : "SELECT * FROM master_brands WHERE id = $brand_id AND user_id = $user_id";

// Fetch existing brand data
$result = mysqli_query($conn, $query);
$brand = mysqli_fetch_assoc($result);

if (!$brand) {
    echo "Brand not found or access denied.";
    exit();
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $code = mysqli_real_escape_string($conn, $_POST["code"]);
    $name = mysqli_real_escape_string($conn, $_POST["name"]);
    $status = isset($_POST["status"]) ? mysqli_real_escape_string($conn, $_POST["status"]) : $brand['status'];

    if (empty($code) || empty($name)) {
        $error = "Code and Name are required!";
    } else {
        $update = $is_admin
            ? "UPDATE master_brands SET code = '$code', name = '$name', status = '$status' WHERE id = $brand_id"
            : "UPDATE master_brands SET code = '$code', name = '$name', status = '$status' WHERE id = $brand_id AND user_id = $user_id";

        if (mysqli_query($conn, $update)) {
            header("Location: index.php");
            exit();
        } else {
            $error = "Failed to update brand: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Brand</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="card shadow-sm">
            <div class="card-body">
                <h2 class="mb-4">Edit Brand</h2>

                <?php if (!empty($error)) : ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="code" class="form-label">Brand Code</label>
                        <input type="text" name="code" id="code" class="form-control" 
                               value="<?= htmlspecialchars($brand['code']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="name" class="form-label">Brand Name</label>
                        <input type="text" name="name" id="name" class="form-control" 
                               value="<?= htmlspecialchars($brand['name']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select name="status" id="status" class="form-select">
                            <option value="Active" <?= $brand['status'] === 'Active' ? 'selected' : '' ?>>Active</option>
                            <option value="Inactive" <?= $brand['status'] === 'Inactive' ? 'selected' : '' ?>>Inactive</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Update Brand</button>
                    <a href="index.php" class="btn btn-secondary ms-2">← Back to Brand List</a>
                </form>
            </div>
        </div>
    </div>
</body>
</html>

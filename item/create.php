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

$query_brand = $is_admin
    ? "SELECT * FROM master_brands WHERE status = 'Active'"
    : "SELECT * FROM master_brands WHERE user_id = $user_id AND status = 'Active'";

$query_cat = $is_admin
    ? "SELECT * FROM categories WHERE status = 'Active'"
    : "SELECT * FROM categories WHERE user_id = $user_id AND status = 'Active'";

// Fetch dropdown options
$brands = mysqli_query($conn, $query_brand);
$categories = mysqli_query($conn, $query_cat);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $brand_id = (int) $_POST["brand_id"];
    $category_id = (int) $_POST["category_id"];
    $code = mysqli_real_escape_string($conn, $_POST["code"]);
    $name = mysqli_real_escape_string($conn, $_POST["name"]);
    $status = $_POST["status"] ?? 'Active';

    // File upload
    $attachment = '';
    if ($_FILES["attachment"]["name"]) {
        $target_dir = "../uploads/";
        if (!is_dir($target_dir)) mkdir($target_dir);
        $filename = basename($_FILES["attachment"]["name"]);
        $target_file = $target_dir . time() . "_" . $filename;

        if (move_uploaded_file($_FILES["attachment"]["tmp_name"], $target_file)) {
            $attachment = $target_file;
        }
    }

    $sql = "INSERT INTO items (user_id, brand_id, category_id, code, name, attachment, status)
            VALUES ('$user_id', '$brand_id', '$category_id', '$code', '$name', '$attachment', '$status')";

    if (mysqli_query($conn, $sql)) {
        header("Location: index.php");
        exit();
    } else {
        $error = "Failed to create item: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Item</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2 class="mb-4">Create Item</h2>

    <?php if (!empty($error)) : ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="row g-3">
        <div class="col-md-6">
            <label for="brand_id" class="form-label">Brand</label>
            <select name="brand_id" id="brand_id" class="form-select" required>
                <option value="">-- Select Brand --</option>
                <?php while ($b = mysqli_fetch_assoc($brands)) {
                    echo "<option value='{$b['id']}'>{$b['name']}</option>";
                } ?>
            </select>
        </div>

        <div class="col-md-6">
            <label for="category_id" class="form-label">Category</label>
            <select name="category_id" id="category_id" class="form-select" required>
                <option value="">-- Select Category --</option>
                <?php while ($c = mysqli_fetch_assoc($categories)) {
                    echo "<option value='{$c['id']}'>{$c['name']}</option>";
                } ?>
            </select>
        </div>

        <div class="col-md-6">
            <label for="code" class="form-label">Item Code</label>
            <input type="text" name="code" id="code" class="form-control" placeholder="Enter item code" required>
        </div>

        <div class="col-md-6">
            <label for="name" class="form-label">Item Name</label>
            <input type="text" name="name" id="name" class="form-control" placeholder="Enter item name" required>
        </div>

        <div class="col-md-6">
            <label for="attachment" class="form-label">Attachment</label>
            <input type="file" name="attachment" id="attachment" class="form-control">
        </div>

        <div class="col-md-6">
            <label for="status" class="form-label">Status</label>
            <select name="status" id="status" class="form-select">
                <option value="Active">Active</option>
                <option value="Inactive">Inactive</option>
            </select>
        </div>

        <div class="col-12 d-flex justify-content-between mt-4">
            <a href="index.php" class="btn btn-outline-secondary">← Back to Item List</a>
            <button type="submit" class="btn btn-success">Create Item</button>
        </div>
    </form>
</div>
</body>
</html>

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
    echo "Item ID is missing.";
    exit();
}

$item_id = (int) $_GET['id'];

$query = $is_admin
    ? "SELECT * FROM items WHERE id = $item_id"
    : "SELECT * FROM items WHERE id = $item_id AND user_id = $user_id";

$result = mysqli_query($conn, $query);
$item = mysqli_fetch_assoc($result);

if (!$item) {
    echo "Item not found or access denied.";
    exit();
}

$query_brand = $is_admin
    ? "SELECT * FROM master_brands WHERE status = 'Active'"
    : "SELECT * FROM master_brands WHERE user_id = $user_id AND status = 'Active'";

$query_cat = $is_admin
    ? "SELECT * FROM categories WHERE status = 'Active'"
    : "SELECT * FROM categories WHERE user_id = $user_id AND status = 'Active'";

$brands = mysqli_query($conn, $query_brand);
$categories = mysqli_query($conn, $query_cat);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $code = mysqli_real_escape_string($conn, $_POST["code"]);
    $name = mysqli_real_escape_string($conn, $_POST["name"]);
    $status = $_POST["status"] ?? 'Active';
    $brand_id = (int) $_POST["brand_id"];
    $category_id = (int) $_POST["category_id"];
    $attachment = $item['attachment'];

    if ($_FILES["attachment"]["name"]) {
        $target_dir = "../uploads/";
        if (!is_dir($target_dir)) mkdir($target_dir);
        $filename = time() . "_" . basename($_FILES["attachment"]["name"]);
        $target_file = $target_dir . $filename;

        if (move_uploaded_file($_FILES["attachment"]["tmp_name"], $target_file)) {
            $attachment = $target_file;
        }
    }

    $update = $is_admin
        ? "UPDATE items SET code='$code', name='$name', brand_id='$brand_id', category_id='$category_id', attachment='$attachment', status='$status' WHERE id = $item_id"
        : "UPDATE items SET code='$code', name='$name', brand_id='$brand_id', category_id='$category_id', attachment='$attachment', status='$status' WHERE id = $item_id AND user_id = $user_id";

    if (mysqli_query($conn, $update)) {
        header("Location: index.php");
        exit();
    } else {
        $error = "Update failed: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Item</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2 class="mb-4">Edit Item</h2>

    <?php if (!empty($error)) : ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="row g-3">
        <div class="col-md-6">
            <label for="brand_id" class="form-label">Brand</label>
            <select name="brand_id" id="brand_id" class="form-select" required>
                <?php while ($b = mysqli_fetch_assoc($brands)) {
                    $selected = $b['id'] == $item['brand_id'] ? 'selected' : '';
                    echo "<option value='{$b['id']}' $selected>{$b['name']}</option>";
                } ?>
            </select>
        </div>

        <div class="col-md-6">
            <label for="category_id" class="form-label">Category</label>
            <select name="category_id" id="category_id" class="form-select" required>
                <?php while ($c = mysqli_fetch_assoc($categories)) {
                    $selected = $c['id'] == $item['category_id'] ? 'selected' : '';
                    echo "<option value='{$c['id']}' $selected>{$c['name']}</option>";
                } ?>
            </select>
        </div>

        <div class="col-md-6">
            <label for="code" class="form-label">Item Code</label>
            <input type="text" name="code" id="code" class="form-control" value="<?= htmlspecialchars($item['code']) ?>" required>
        </div>

        <div class="col-md-6">
            <label for="name" class="form-label">Item Name</label>
            <input type="text" name="name" id="name" class="form-control" value="<?= htmlspecialchars($item['name']) ?>" required>
        </div>

        <div class="col-md-6">
            <label for="attachment" class="form-label">Attachment (leave blank to keep current)</label>
            <?php if (!empty($item['attachment'])): ?>
                <p><a href="<?= $item['attachment'] ?>" target="_blank" class="btn btn-sm btn-outline-primary">View Current File</a></p>
            <?php endif; ?>
            <input type="file" name="attachment" id="attachment" class="form-control">
        </div>

        <div class="col-md-6">
            <label for="status" class="form-label">Status</label>
            <select name="status" id="status" class="form-select">
                <option value="Active" <?= $item['status'] === 'Active' ? 'selected' : '' ?>>Active</option>
                <option value="Inactive" <?= $item['status'] === 'Inactive' ? 'selected' : '' ?>>Inactive</option>
            </select>
        </div>

        <div class="col-12 d-flex justify-content-between mt-4">
            <a href="index.php" class="btn btn-outline-secondary">← Back to Item List</a>
            <button type="submit" class="btn btn-primary">Update Item</button>
        </div>
    </form>
</div>
</body>
</html>

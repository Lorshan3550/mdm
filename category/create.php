<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION["user_id"];
$is_admin = $_SESSION["is_admin"];

include '../config/db.php';
include '../includes/header.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $code = mysqli_real_escape_string($conn, $_POST["code"]);
    $name = mysqli_real_escape_string($conn, $_POST["name"]);
    $status = isset($_POST["status"]) ? mysqli_real_escape_string($conn, $_POST["status"]) : 'Active';

    if (empty($code) || empty($name)) {
        $error = "Code and Name are required!";
    } else {
        $sql = "INSERT INTO categories (user_id, code, name, status) 
                VALUES ('$user_id', '$code', '$name', '$status')";
        if (mysqli_query($conn, $sql)) {
            header("Location: index.php");
            exit();
        } else {
            $error = "Failed to create category: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Category</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2 class="mb-4">Create Category</h2>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST" action="create.php" class="bg-white p-4 rounded shadow-sm">
        <div class="mb-3">
            <label for="code" class="form-label">Category Code</label>
            <input type="text" name="code" id="code" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="name" class="form-label">Category Name</label>
            <input type="text" name="name" id="name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select name="status" id="status" class="form-select">
                <option value="Active">Active</option>
                <option value="Inactive">Inactive</option>
            </select>
        </div>
        <button type="submit" class="btn btn-success">Create</button>
        <a href="index.php" class="btn btn-secondary ms-2">← Back to Category List</a>
    </form>
</div>
</body>
</html>

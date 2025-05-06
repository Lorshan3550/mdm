<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: ../auth/login.php");
    exit();
}

include '../config/db.php';

$user_id = $_SESSION["user_id"];
$is_admin = $_SESSION["is_admin"];


if (!isset($_GET['id'])) {
    echo "Item ID missing.";
    exit();
}

$item_id = (int) $_GET['id'];

// Check item exists and belongs to user
$query = $is_admin
    ? "SELECT * FROM items WHERE id = $item_id"
    : "SELECT * FROM items WHERE id = $item_id AND user_id = $user_id";
$result = mysqli_query($conn, $query);
$item = mysqli_fetch_assoc($result);

if (!$item) {
    echo "Item not found or access denied.";
    exit();
}

// Delete attachment file if it exists
if (!empty($item['attachment']) && file_exists($item['attachment'])) {
    unlink($item['attachment']);
}

$delete = $is_admin
    ? "DELETE FROM items WHERE id = $item_id"
    : "DELETE FROM items WHERE id = $item_id AND user_id = $user_id";

if (mysqli_query($conn, $delete)) {
    header("Location: index.php");
    exit();
} else {
    echo "Failed to delete item: " . mysqli_error($conn);
}
?>

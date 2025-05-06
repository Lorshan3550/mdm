<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: ../auth/login.php");
    exit();
}

include '../config/db.php';

$user_id = $_SESSION["user_id"];
$is_admin = $_SESSION["is_admin"];


if (isset($_GET['id'])) {
    $brand_id = (int) $_GET['id'];

    $query = $is_admin
    ? "SELECT * FROM master_brands WHERE id = $brand_id"
    : "SELECT * FROM master_brands WHERE id = $brand_id AND user_id = $user_id";


    // Ensure brand belongs to the user
    $check = mysqli_query($conn, $query );
    if (mysqli_num_rows($check) === 1) {
        // Delete brand
        $delete = mysqli_query($conn, "DELETE FROM master_brands WHERE id = $brand_id");
        echo mysqli_error($conn);
        if ($delete) {
            header("Location: index.php");
            exit();
        } else {
            echo "Failed to delete brand.";
        }
    } else {
        echo "Brand not found or unauthorized.";
    }
} else {
    echo "No brand selected.";
}

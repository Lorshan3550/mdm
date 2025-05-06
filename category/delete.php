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
    $category_id = (int) $_GET['id'];

    $query = $is_admin
    ? "SELECT * FROM categories WHERE id = $category_id"
    : "SELECT * FROM categories WHERE id = $category_id AND user_id = $user_id";


    // Confirm the category belongs to this user
    $check = mysqli_query($conn, $query);

    if (mysqli_num_rows($check) === 1) {
        $delete = mysqli_query($conn, "DELETE FROM categories WHERE id = $category_id");
        if ($delete) {
            header("Location: index.php");
            exit();
        } else {
            echo "Failed to delete category.";
        }
    } else {
        echo "Category not found or unauthorized.";
    }
} else {
    echo "No category selected.";
}

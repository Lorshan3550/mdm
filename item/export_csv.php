<?php
session_start();
include '../config/db.php';

$user_id = $_SESSION["user_id"];
$is_admin = $_SESSION["is_admin"];

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="items.csv"');

$output = fopen('php://output', 'w');
fputcsv($output, ['Code', 'Name', 'Brand', 'Category', 'Status']);

$filter = $is_admin ? '' : "WHERE items.user_id = $user_id";

$sql = "SELECT items.*, master_brands.name AS brand_name, categories.name AS category_name 
        FROM items 
        JOIN master_brands ON items.brand_id = master_brands.id 
        JOIN categories ON items.category_id = categories.id 
        $filter 
        ORDER BY items.id DESC";

$result = mysqli_query($conn, $sql);

while ($row = mysqli_fetch_assoc($result)) {
    fputcsv($output, [
        $row['code'],
        $row['name'],
        $row['brand_name'],
        $row['category_name'],
        $row['status']
    ]);
}

fclose($output);
exit();

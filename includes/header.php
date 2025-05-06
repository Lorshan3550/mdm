



<!-- includes/header.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>MDM System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            display: flex;
        }
        .sidebar {
            width: 220px;
            background-color: #343a40;
            min-height: 100vh;
        }
        .sidebar .nav-link {
            color: #ddd;
        }
        .sidebar .nav-link:hover {
            background-color: #495057;
            color: white;
        }
        .main-content {
            flex-grow: 1;
            padding: 20px;
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar d-flex flex-column p-3">
    <h4 class="text-white">MDM</h4>
    <hr class="text-white">
    <ul class="nav nav-pills flex-column mb-auto">
        <li><a href="/mdm/dashboard.php" class="nav-link">Dashboard</a></li>
        <li><a href="/mdm/brand/index.php" class="nav-link">Brands</a></li>
        <li><a href="/mdm/category/index.php" class="nav-link">Categories</a></li>
        <li><a href="/mdm/item/index.php" class="nav-link">Items</a></li>
    </ul>
    <hr class="text-white">
    <a href="/mdm/auth/logout.php" class="btn btn-sm btn-danger mt-auto">Logout</a>
</div>

<!-- Main content area -->
<div class="main-content" style="min-height: 100vh;">

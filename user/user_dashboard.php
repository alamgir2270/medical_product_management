<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header("Location: ../login.php");
    exit();
}

include 'user_layout.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #f5a623, #f57c00);
        }
    </style>
</head>
<body class="gradient-bg">
    <div class="container py-5">
        <h1 class="text-white text-center mb-4">Welcome to Your Dashboard</h1>
        <div class="row justify-content-center g-4">
            <!-- View Inventory Section -->
            <div class="col-md-4">
                <div class="card text-center bg-light shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">View Inventory</h5>
                        <p class="card-text">Explore available items in inventory.</p>
                        <a href="view_inventory.php" class="btn btn-primary">View Inventory</a>
                    </div>
                </div>
            </div>

            <!-- Place Order Section -->
            <div class="col-md-4">
                <div class="card text-center bg-light shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Place Order</h5>
                        <p class="card-text">Order items from our inventory.</p>
                        <a href="place_order.php" class="btn btn-primary">Place Order</a>
                    </div>
                </div>
            </div>

            <!-- Track Orders Section -->
            <div class="col-md-4">
                <div class="card text-center bg-light shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Track Orders</h5>
                        <p class="card-text">Check the status of your orders.</p>
                        <a href="track_order.php" class="btn btn-primary">Track Orders</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'user_footer.php'; ?>
</body>
</html>

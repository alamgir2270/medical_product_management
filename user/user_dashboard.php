<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header("Location: ../login.php");
    exit();
}

$username = $_SESSION['username']; // Ensure this is stored correctly during login
include 'user_layout.php'; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Welcome, <?php echo htmlspecialchars($username); ?>!</h2>
        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <h5 class="card-title">View Inventory</h5>
                        <p class="card-text">Check available medical products.</p>
                        <a href="view_inventory.php" class="btn btn-primary">View</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <h5 class="card-title">Place Order</h5>
                        <p class="card-text">Order your required products.</p>
                        <a href="place_order.php" class="btn btn-success">Order</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <h5 class="card-title">Track Orders</h5>
                        <p class="card-text">Track your order status.</p>
                        <a href="track_order.php" class="btn btn-info">Track</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php include 'user_footer.php'; ?>
</body>
</html>

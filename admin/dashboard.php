<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}
include '../includes/db.php';

// Get totals for dashboard metrics
$total_users = $conn->query("SELECT COUNT(*) AS count FROM User WHERE role = 'user'")->fetch_assoc()['count'];
$total_items = $conn->query("SELECT COUNT(*) AS count FROM Item")->fetch_assoc()['count'];
$total_orders = $conn->query("SELECT COUNT(*) AS count FROM Orders")->fetch_assoc()['count'];
$low_stock = $conn->query("SELECT COUNT(*) AS count FROM Inventory WHERE quantity < 10")->fetch_assoc()['count'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<?php include 'admin_layout.php'; ?>
<div class="container mt-4">
    <h2>Admin Dashboard</h2>
    <div class="row mt-4">
        <!-- Total Users -->
        <div class="col-md-3">
            <div class="card text-center shadow">
                <div class="card-body">
                    <h5 class="card-title">Total Users</h5>
                    <p class="card-text"><?php echo $total_users; ?></p>
                </div>
            </div>
        </div>

        <!-- Total Items -->
        <div class="col-md-3">
            <div class="card text-center shadow">
                <div class="card-body">
                    <h5 class="card-title">Total Items</h5>
                    <p class="card-text"><?php echo $total_items; ?></p>
                </div>
            </div>
        </div>

        <!-- Total Orders -->
        <div class="col-md-3">
            <div class="card text-center shadow">
                <div class="card-body">
                    <h5 class="card-title">Total Orders</h5>
                    <p class="card-text"><?php echo $total_orders; ?></p>
                </div>
            </div>
        </div>

        <!-- Low Stock Items -->
        <div class="col-md-3">
            <div class="card text-center shadow">
                <div class="card-body">
                    <h5 class="card-title">Low Stock</h5>
                    <p class="card-text"><?php echo $low_stock; ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Manage Users Section -->
    <h3 class="mt-5">Manage Users</h3>
    <form method="POST" class="mb-3">
        <div class="mb-3">
            <label for="user_id" class="form-label">User ID</label>
            <input type="number" class="form-control" name="user_id" required>
        </div>
        <div class="mb-3">
            <label for="role" class="form-label">New Role</label>
            <select class="form-control" name="role" required>
                <option value="user">User</option>
                <option value="admin">Admin</option>
            </select>
        </div>
        <button type="submit" name="update_role" class="btn btn-primary">Update Role</button>
    </form>

    <!-- Manage Stock Section -->
    <h3 class="mt-5">Manage Stock for Low Stock Items</h3>
    <form method="POST" class="mb-3">
        <div class="mb-3">
            <label for="item_id" class="form-label">Item ID</label>
            <input type="number" class="form-control" name="item_id" required>
        </div>
        <div class="mb-3">
            <label for="quantity" class="form-label">New Stock Quantity</label>
            <input type="number" class="form-control" name="quantity" required>
        </div>
        <button type="submit" name="update_stock" class="btn btn-primary">Update Stock</button>
    </form>
</div>
<?php include 'admin_footer.php'; ?>
</body>
</html>

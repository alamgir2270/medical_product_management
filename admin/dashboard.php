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

// Fetch all users for dropdown
$users = $conn->query("SELECT user_id, username FROM User");

// Fetch low stock items for dropdown
$low_stock_items = $conn->query("SELECT i.item_id, i.name, inv.quantity FROM Item i 
                                  JOIN Inventory inv ON i.item_id = inv.item_id 
                                  WHERE inv.quantity < 10");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #1f4037, #99f2c8, #1c92d2, #f2fcfe);
            background-size: 400% 400%;
            animation: gradientBG 12s ease infinite;
            font-family: Arial, sans-serif;
        }

        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .card {
            background: rgba(255, 255, 255, 0.8);
            border: none;
            border-radius: 10px;
        }

        .card-title {
            font-weight: bold;
            color: #333;
        }

        .btn-primary {
            background-color: #4CAF50;
            border-color: #4CAF50;
        }

        .btn-primary:hover {
            background-color: #45a049;
            border-color: #45a049;
        }
    </style>
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
        <!-- Dropdown for selecting user -->
        <div class="mb-3">
            <label for="user_id" class="form-label">Username</label>
            <select class="form-control" name="user_id" required>
                <option value="">Select User</option>
                <?php while ($user = $users->fetch_assoc()): ?>
                    <option value="<?php echo $user['user_id']; ?>"><?php echo $user['username']; ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <!-- Role selection -->
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
        <!-- Dropdown for selecting low stock item -->
        <div class="mb-3">
            <label for="item_id" class="form-label">Item</label>
            <select class="form-control" name="item_id" required>
                <option value="">Select Item</option>
                <?php while ($item = $low_stock_items->fetch_assoc()): ?>
                    <option value="<?php echo $item['item_id']; ?>">
                        <?php echo $item['name']; ?> (Current Stock: <?php echo $item['quantity']; ?>)
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <!-- Input for new stock quantity -->
        <div class="mb-3">
            <label for="quantity" class="form-label">New Stock Quantity</label>
            <input type="number" class="form-control" name="quantity" min="1" required>
        </div>
        <button type="submit" name="update_stock" class="btn btn-primary">Update Stock</button>
    </form>
</div>
<?php include 'admin_footer.php'; ?>
</body>
</html>

<?php
session_start();
include '../includes/db.php'; // Database connection

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Handle creating a new order
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_order'])) {
    $user_id = $_POST['user_id'];
    $item_id = $_POST['item_id'];
    $quantity = $_POST['quantity'];
    $status = $_POST['status'];

    // Insert the order into Orders table
    $conn->query("INSERT INTO Orders (user_id, order_date, status) VALUES ($user_id, NOW(), '$status')");
    $order_id = $conn->insert_id;

    // Insert the order details into Order_Item table
    $conn->query("INSERT INTO Order_Item (order_id, item_id, quantity) VALUES ($order_id, $item_id, $quantity)");
}

// Handle deleting an order
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];

    // First, delete the associated order items from the Order_Item table
    $conn->query("DELETE FROM Order_Item WHERE order_id = $delete_id");

    // Then, delete the order from the Orders table
    $conn->query("DELETE FROM Orders WHERE order_id = $delete_id");

    header("Location: orders.php");
    exit();
}

// Handle order status update (on form submission)
if (isset($_POST['update_order_status'])) {
    $order_id = $_POST['order_id'];
    $new_status = $_POST['status'];

    // Update the status of the order in the Orders table
    $conn->query("UPDATE Orders SET status = '$new_status' WHERE order_id = $order_id");

    // Redirect after update to avoid form resubmission on refresh
    header("Location: orders.php");
    exit();
}

// Fetch all orders with user and item details
$sql = "SELECT o.order_id, o.order_date, u.username, i.name AS item_name, oi.quantity, o.status 
        FROM Orders o
        JOIN User u ON o.user_id = u.user_id
        JOIN Order_Item oi ON o.order_id = oi.order_id
        JOIN Item i ON oi.item_id = i.item_id
        ORDER BY o.order_date DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Orders</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background: linear-gradient(to right, #ff7e5f, #feb47b); /* Gradient background */
            color: #333;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .container {
            background: rgba(255, 255, 255, 0.9); /* White background with slight transparency */
            border-radius: 10px;
            padding: 20px;
            margin-top: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        h2, h3 {
            color: #333;
        }
        .btn-primary {
            background: #ff7e5f;
            border: none;
        }
        .btn-primary:hover {
            background: #feb47b;
        }
        table {
            background-color: #fff;
        }
    </style>
</head>
<body>
<?php include 'admin_layout.php'; ?>

<div class="container mt-4">
    <h2 class="text-center">Orders Management</h2>

    <!-- Create Order Section -->
    <h3>Create Order</h3>
    <form method="POST" class="mb-5">
        <div class="row">
            <div class="col-md-3">
                <label for="user_id" class="form-label">Username</label>
                <select class="form-control" name="user_id" required>
                    <option value="">Select Username</option>
                    <?php
                    $users = $conn->query("SELECT user_id, username FROM User");
                    while ($user = $users->fetch_assoc()) {
                        echo "<option value='{$user['user_id']}'>{$user['username']}</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="col-md-3">
                <label for="item_id" class="form-label">Item</label>
                <select class="form-control" name="item_id" required>
                    <option value="">Select Item</option>
                    <?php
                    $items = $conn->query("SELECT item_id, name, price FROM Item");
                    while ($item = $items->fetch_assoc()) {
                        echo "<option value='{$item['item_id']}'>{$item['name']} - ৳{$item['price']}</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="col-md-3">
                <label for="quantity" class="form-label">Quantity</label>
                <input type="number" class="form-control" name="quantity" min="1" required>
            </div>
            <div class="col-md-3">
                <label for="status" class="form-label">Order Status</label>
                <select class="form-control" name="status" required>
                    <option value="pending">Pending</option>
                    <option value="shipped">Shipped</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>
        </div>
        <button type="submit" name="create_order" class="btn btn-primary mt-3">Create Order</button>
    </form>

    <!-- All Orders Section -->
    <h3>All Orders</h3>
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>Order ID</th>
                <th>Order Date</th>
                <th>User</th>
                <th>Item</th>
                <th>Quantity</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($order = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $order['order_id']; ?></td>
                    <td><?php echo $order['order_date']; ?></td>
                    <td><?php echo $order['username']; ?></td>
                    <td><?php echo $order['item_name']; ?></td>
                    <td><?php echo $order['quantity']; ?></td>
                    <td><?php echo $order['status']; ?></td>
                    <td>
                        <a href="orders.php?edit_id=<?php echo $order['order_id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                        <a href="orders.php?delete_id=<?php echo $order['order_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <!-- Update Order Status Section -->
    <h3 class="mt-5">Update Order Status</h3>
    <form method="POST" class="mb-3">
        <div class="mb-3">
            <label for="order_id" class="form-label">Order ID</label>
            <input type="number" class="form-control" name="order_id" required>
        </div>
        <div class="mb-3">
            <label for="status" class="form-label">New Status</label>
            <select class="form-control" name="status" required>
                <option value="pending">Pending</option>
                <option value="shipped">Shipped</option>
                <option value="completed">Completed</option>
                <option value="cancelled">Cancelled</option>
            </select>
        </div>
        <button type="submit" name="update_order_status" class="btn btn-primary">Update Order Status</button>
    </form>
</div>
<?php include 'admin_footer.php'; ?>
</body>
</html>

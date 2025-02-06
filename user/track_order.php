<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header("Location: ../login.php");
    exit();
}

include 'user_layout.php';
require '../includes/db.php'; // Database connection

$user_id = $_SESSION['user_id'];

// Handle order cancellation
if (isset($_POST['cancel_order_id'])) {
    $cancel_order_id = $_POST['cancel_order_id'];
    
    // Update the order status to 'cancelled'
    $update_sql = "UPDATE Orders SET status = 'cancelled' WHERE order_id = ? AND user_id = ? AND status = 'pending'";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("ii", $cancel_order_id, $user_id);
    if ($stmt->execute()) {
        $success_message = "Your order has been cancelled successfully.";
    } else {
        $error_message = "Failed to cancel the order. Please try again.";
    }
}

// Fetch orders with status
$sql = "SELECT o.order_id, o.order_date, o.status, i.name AS item_name, oi.quantity
        FROM Orders o
        JOIN Order_Item oi ON o.order_id = oi.order_id
        JOIN Item i ON oi.item_id = i.item_id
        WHERE o.user_id = ?
        ORDER BY o.order_date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!-- Add Beautiful Gradient Background -->
<style>
    body {
        background: linear-gradient(120deg, #a8edea, #fed6e3);
        min-height: 100vh;
    }

    .table-container {
        background: #ffffff;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    h2 {
        font-weight: bold;
        color: #4c4cfc;
    }

    .btn-danger {
        background-color: #ff6b6b;
        border-color: #ff6b6b;
    }

    .btn-danger:hover {
        background-color: #ff4a4a;
        border-color: #ff4a4a;
    }

    .badge-warning {
        background-color: #ffd166;
        color: #212529;
    }

    .badge-danger {
        background-color: #ef476f;
    }

    .badge-success {
        background-color: #06d6a0;
    }
</style>

<!-- Order Tracker -->
<div class="container mt-5">
    <div class="table-container">
        <h2 class="text-center mb-4">Track Your Orders</h2>

        <?php if (isset($success_message)): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php elseif (isset($error_message)): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <!-- Orders Table -->
        <table class="table table-striped table-hover mt-4">
            <thead class="table-primary">
                <tr>
                    <th>Order ID</th>
                    <th>Item Name</th>
                    <th>Quantity</th>
                    <th>Order Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['order_id']; ?></td>
                        <td><?php echo $row['item_name']; ?></td>
                        <td><?php echo $row['quantity']; ?></td>
                        <td><?php echo $row['order_date']; ?></td>
                        <td>
                            <?php if ($row['status'] == 'pending'): ?>
                                <span class="badge badge-warning"><?php echo ucfirst($row['status']); ?></span>
                            <?php elseif ($row['status'] == 'cancelled'): ?>
                                <span class="badge badge-danger"><?php echo ucfirst($row['status']); ?></span>
                            <?php else: ?>
                                <span class="badge badge-success"><?php echo ucfirst($row['status']); ?></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($row['status'] == 'pending'): ?>
                                <form method="POST" action="">
                                    <input type="hidden" name="cancel_order_id" value="<?php echo $row['order_id']; ?>">
                                    <button type="submit" class="btn btn-danger btn-sm">Cancel Order</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Footer -->
<?php include 'user_footer.php'; ?>

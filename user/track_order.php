<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header("Location: ../login.php");
    exit();
}

include 'user_layout.php';
require '../includes/db.php'; // Database connection

$user_id = $_SESSION['user_id'];

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

<div class="container mt-5">
    <h2 class="text-center">Track Your Orders</h2>

    <table class="table table-striped mt-4">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Item Name</th>
                <th>Quantity</th>
                <th>Order Date</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['order_id']; ?></td>
                    <td><?php echo $row['item_name']; ?></td>
                    <td><?php echo $row['quantity']; ?></td>
                    <td><?php echo $row['order_date']; ?></td>
                    <td><?php echo $row['status']; ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php include 'user_footer.php'; ?>

<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header("Location: ../login.php");
    exit();
}

include 'user_layout.php';
require '../includes/db.php'; // Database connection

// Check if stock update request is made
if (isset($_POST['update_stock'])) {
    if ($_SESSION['role'] == 'admin') { // Only allow admins to update stock
        $item_id = $_POST['item_id'];
        $new_quantity = $_POST['quantity'];

        // Validate and update the stock in Inventory
        if ($new_quantity >= 0) {
            $update_sql = "UPDATE Inventory SET quantity = ? WHERE item_id = ?";
            $stmt_update = $conn->prepare($update_sql);
            $stmt_update->bind_param('ii', $new_quantity, $item_id);

            if ($stmt_update->execute()) {
                $message = "Stock updated successfully.";
            } else {
                $message = "Error updating stock: " . $stmt_update->error;
            }
        } else {
            $message = "Quantity cannot be negative.";
        }
    } else {
        $message = "You do not have permission to update stock.";
    }
}

// Fetch inventory items
$sql = "SELECT i.item_id, i.name, i.description, i.price, inv.quantity
        FROM Item i
        JOIN Inventory inv ON i.item_id = inv.item_id";
$result = $conn->query($sql);
?>

<div class="container mt-5">
    <h2 class="text-center">Available Inventory</h2>
    <?php if (isset($message)): ?>
        <div class="alert alert-info"><?php echo $message; ?></div>
    <?php endif; ?>
    <table class="table table-bordered mt-4">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Item Name</th>
                <th>Description</th>
                <th>Price (USD)</th>
                <th>Stock</th>
                <?php if ($_SESSION['role'] == 'admin'): ?>
                    <th>Update Stock</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['item_id']; ?></td>
                    <td><?php echo $row['name']; ?></td>
                    <td><?php echo $row['description']; ?></td>
                    <td><?php echo $row['price']; ?></td>
                    <td><?php echo $row['quantity']; ?></td>
                    <?php if ($_SESSION['role'] == 'admin'): ?>
                        <!-- Admin can update stock -->
                        <td>
                            <form action="view_inventory.php" method="POST" class="d-inline">
                                <input type="hidden" name="item_id" value="<?php echo $row['item_id']; ?>">
                                <input type="number" name="quantity" min="0" value="<?php echo $row['quantity']; ?>" required>
                                <button type="submit" name="update_stock" class="btn btn-warning btn-sm">Update</button>
                            </form>
                        </td>
                    <?php endif; ?>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php include 'user_footer.php'; ?>

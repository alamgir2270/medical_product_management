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

<!-- Beautiful Background -->
<style>
    body {
        background: linear-gradient(to bottom, #e3f2fd, #90caf9);
        min-height: 100vh;
    }

    .table-container {
        background: #ffffff;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    h2 {
        font-weight: bold;
        color: #1976d2;
    }

    .btn-warning {
        color: white !important;
    }
</style>

<!-- Inventory Table -->
<div class="container mt-5">
    <div class="table-container">
        <h2 class="text-center mb-4">Available Inventory</h2>
        
        <?php if (isset($message)): ?>
            <div class="alert alert-info"><?php echo $message; ?></div>
        <?php endif; ?>

        <table class="table table-striped table-hover mt-4">
            <thead class="table-primary">
                <tr>
                    <th>ID</th>
                    <th>Item Name</th>
                    <th>Description</th>
                    <th>Price (BDT)</th>
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
                        <td class="text-success">৳<?php echo number_format($row['price'], 2); ?></td>
                        <td class="text-primary"><?php echo $row['quantity']; ?></td>
                        <?php if ($_SESSION['role'] == 'admin'): ?>
                            <!-- Admin can update stock -->
                            <td>
                                <form action="view_inventory.php" method="POST" class="d-inline">
                                    <input type="hidden" name="item_id" value="<?php echo $row['item_id']; ?>">
                                    <input type="number" name="quantity" min="0" value="<?php echo $row['quantity']; ?>" class="form-control w-75 d-inline" required>
                                    <button type="submit" name="update_stock" class="btn btn-warning btn-sm">Update</button>
                                </form>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Footer -->
<?php include 'user_footer.php'; ?>

<!-- Bootstrap JS and dependencies -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js"></script>

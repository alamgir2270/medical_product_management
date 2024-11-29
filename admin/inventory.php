<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}
include '../includes/db.php';

// Add Item (Create)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_item'])) {
    $name = $_POST['name'];
    $description = $_POST['description']; // New field
    $quantity = $_POST['quantity'];
    $price = $_POST['price'];
    $supplier_id = $_POST['supplier_id'];

    // Insert new item into the database
    $conn->query("INSERT INTO Item (name, description, price, supplier_id) VALUES ('$name', '$description', '$price', '$supplier_id')");
    $item_id = $conn->insert_id; // Get the newly created item_id
    $conn->query("INSERT INTO Inventory (item_id, quantity) VALUES ('$item_id', '$quantity')");

    header("Location: inventory.php");
    exit();
}

// Add New Supplier (Create Supplier)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_supplier'])) {
    $supplier_name = $_POST['supplier_name'];
    $conn->query("INSERT INTO Supplier (name) VALUES ('$supplier_name')");
    header("Location: inventory.php");
    exit();
}

// Update Item (Edit)
if (isset($_GET['edit_id'])) {
    $item_id = $_GET['edit_id'];
    $result = $conn->query("SELECT Item.name, Item.description, Inventory.quantity, Item.price, Item.supplier_id
                            FROM Inventory
                            INNER JOIN Item ON Inventory.item_id = Item.item_id
                            WHERE Item.item_id = '$item_id'");
    $item = $result->fetch_assoc();
}

// Save updated item
if (isset($_POST['update_item'])) {
    $item_id = $_POST['item_id'];
    $name = $_POST['name'];
    $description = $_POST['description']; 
    $quantity = $_POST['quantity'];
    $price = $_POST['price'];
    $supplier_id = $_POST['supplier_id'];

    // Update the item and inventory
    $conn->query("UPDATE Item SET name = '$name', description = '$description', price = '$price', supplier_id = '$supplier_id' WHERE item_id = '$item_id'");
    $conn->query("UPDATE Inventory SET quantity = '$quantity' WHERE item_id = '$item_id'");

    $_SESSION['success'] = "Item deleted successfully.";
    header("Location: inventory.php");
    exit();
}

// Delete Item
if (isset($_GET['delete_id'])) {
    $item_id = $_GET['delete_id'];
    // Delete related rows in order_item first
    $conn->query("DELETE FROM order_item WHERE item_id = '$item_id'");

    $conn->query("DELETE FROM Inventory WHERE item_id = '$item_id'");
    $conn->query("DELETE FROM Item WHERE item_id = '$item_id'");
    header("Location: inventory.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Inventory</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script>
        // Function to toggle the visibility of the "Add New Supplier" form
        function toggleSupplierForm() {
            var form = document.getElementById('supplierForm');
            form.style.display = form.style.display === 'none' ? 'block' : 'none';
        }
    </script>
</head>
<body>
<?php include 'admin_layout.php'; ?>

<div class="container mt-4">
    <h2>Inventory Management</h2>
    
    <!-- Add New Item Form -->
    <form method="POST" class="mb-4">
        <h4>Add New Item</h4>
        <div class="form-group">
            <input type="text" name="name" class="form-control" placeholder="Item Name" required>
        </div>
        <div class="form-group">
            <textarea name="description" class="form-control" placeholder="Description" rows="3" required></textarea>
        </div>
        <div class="form-group">
            <input type="number" name="quantity" class="form-control" placeholder="Quantity" required>
        </div>
        <div class="form-group">
            <input type="number" name="price" class="form-control" placeholder="Price" required>
        </div>
        <div class="form-group">
            <select name="supplier_id" class="form-control" required>
                <option value="">Select Supplier</option>
                <?php
                $suppliers = $conn->query("SELECT * FROM Supplier");
                while ($supplier = $suppliers->fetch_assoc()) {
                    echo "<option value='{$supplier['supplier_id']}'>{$supplier['name']}</option>";
                }
                ?>
            </select>
            <button type="button" class="btn btn-link mt-2" onclick="toggleSupplierForm()">Add New Supplier</button>
        </div>
        <button type="submit" name="add_item" class="btn btn-primary mt-2">Add Item</button>
    </form>

    <!-- Add New Supplier Form (Initially hidden) -->
    <div id="supplierForm" style="display:none;">
        <h4>Add New Supplier</h4>
        <form method="POST">
            <div class="form-group">
                <input type="text" name="supplier_name" class="form-control" placeholder="Supplier Name" required>
            </div>
            <button type="submit" name="add_supplier" class="btn btn-success mt-2">Add Supplier</button>
        </form>
    </div>

    <!-- Inventory Table -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Item ID</th>
                <th>Name</th>
                <th>Description</th> <!-- New column -->
                <th>Quantity</th>
                <th>Price</th>
                <th>Supplier</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $result = $conn->query("SELECT Item.item_id, Item.name, Item.description, Inventory.quantity, Item.price, Supplier.name AS supplier_name
                                    FROM Inventory
                                    INNER JOIN Item ON Inventory.item_id = Item.item_id
                                    INNER JOIN Supplier ON Item.supplier_id = Supplier.supplier_id");

            while ($row = $result->fetch_assoc()) {
                $status = ($row['quantity'] < 10) ? "<span class='badge bg-danger'>Low Stock</span>" : "<span class='badge bg-success'>In Stock</span>";
                echo "<tr>
                        <td>{$row['item_id']}</td>
                        <td>{$row['name']}</td>
                        <td>{$row['description']}</td>
                        <td>{$row['quantity']}</td>
                        <td>{$row['price']}</td>
                        <td>{$row['supplier_name']}</td>
                        <td>{$status}</td>
                        <td>
                            <a href='inventory.php?edit_id={$row['item_id']}' class='btn btn-warning btn-sm'>Edit</a>
                            <a href='inventory.php?delete_id={$row['item_id']}' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure?\")'>Delete</a>
                        </td>
                      </tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<!-- Edit Item Modal (If Edit is clicked) -->
<?php if (isset($item)): ?>
    <div class="container mt-4">
        <h4>Edit Item</h4>
        <form method="POST">
            <input type="hidden" name="item_id" value="<?= $item_id ?>">
            <div class="form-group">
                <input type="text" name="name" class="form-control" value="<?= $item['name'] ?>" required>
            </div>
            <div class="form-group">
                <textarea name="description" class="form-control" rows="3" required><?= $item['description'] ?></textarea>
            </div>
            <div class="form-group">
                <input type="number" name="quantity" class="form-control" value="<?= $item['quantity'] ?>" required>
            </div>
            <div class="form-group">
                <input type="number" name="price" class="form-control" value="<?= $item['price'] ?>" required>
            </div>
            <div class="form-group">
                <select name="supplier_id" class="form-control" required>
                    <?php
                    $suppliers = $conn->query("SELECT * FROM Supplier");
                    while ($supplier = $suppliers->fetch_assoc()) {
                        $selected = $supplier['supplier_id'] == $item['supplier_id'] ? 'selected' : '';
                        echo "<option value='{$supplier['supplier_id']}' {$selected}>{$supplier['name']}</option>";
                    }
                    ?>
                </select>
            </div>
            <button type="submit" name="update_item" class="btn btn-primary mt-2">Update Item</button>
        </form>
    </div>
<?php endif; ?>

<?php include 'admin_footer.php'; ?>

</body>
</html>

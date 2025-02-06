<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}
include '../includes/db.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Add new Item
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_item'])) {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $quantity = $_POST['quantity'];
    $price = $_POST['price'];
    $supplier_id = $_POST['supplier_id'];

    $stmt = $conn->prepare("INSERT INTO Item (name, description, price, supplier_id) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssdi", $name, $description, $price, $supplier_id);
    if ($stmt->execute()) {
        $item_id = $conn->insert_id;
        $stmtInv = $conn->prepare("INSERT INTO Inventory (item_id, quantity) VALUES (?, ?)");
        $stmtInv->bind_param("ii", $item_id, $quantity);
        $stmtInv->execute();
    }
    $stmt->close();
    header("Location: inventory.php");
    exit();
}

// Add New Supplier
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_supplier'])) {
    $supplier_name = $_POST['supplier_name'];
    $contact = $_POST['contact'];
    $address = $_POST['address'];

    $stmt = $conn->prepare("INSERT INTO Supplier (name, contact, address) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $supplier_name, $contact, $address);
    $stmt->execute();
    $stmt->close();
    header("Location: inventory.php");
    exit();
}

// Get item details for editing
if (isset($_GET['edit_id'])) {
    $item_id = $_GET['edit_id'];
    $stmt = $conn->prepare("SELECT Item.item_id, Item.name, Item.description, Inventory.quantity, Item.price, Item.supplier_id,
                            CASE 
                                WHEN Inventory.quantity < 10 THEN 'Low Stock'
                                ELSE 'In Stock'
                            END AS status
                            FROM Inventory
                            INNER JOIN Item ON Inventory.item_id = Item.item_id
                            WHERE Item.item_id = ?");
    $stmt->bind_param("i", $item_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $item = $result->fetch_assoc();
    $stmt->close();
}

// Save updated item
if (isset($_POST['update_item'])) {
    $item_id = $_POST['item_id'];
    $name = $_POST['name'];
    $description = $_POST['description'];
    $quantity = $_POST['quantity'];
    $price = $_POST['price'];
    $supplier_id = $_POST['supplier_id'];

    $stmt = $conn->prepare("UPDATE Item SET name = ?, description = ?, price = ?, supplier_id = ? WHERE item_id = ?");
    $stmt->bind_param("ssdii", $name, $description, $price, $supplier_id, $item_id);
    $stmt->execute();

    $stmtInv = $conn->prepare("UPDATE Inventory SET quantity = ? WHERE item_id = ?");
    $stmtInv->bind_param("ii", $quantity, $item_id);
    $stmtInv->execute();

    $stmt->close();
    $stmtInv->close();

    header("Location: inventory.php");
    exit();
}

// Delete Item
if (isset($_GET['delete_id'])) {
    $item_id = $_GET['delete_id'];

    $stmtOrderItem = $conn->prepare("DELETE FROM order_item WHERE item_id = ?");
    $stmtOrderItem->bind_param("i", $item_id);
    $stmtOrderItem->execute();

    $stmtInv = $conn->prepare("DELETE FROM Inventory WHERE item_id = ?");
    $stmtInv->bind_param("i", $item_id);
    $stmtInv->execute();

    $stmtItem = $conn->prepare("DELETE FROM Item WHERE item_id = ?");
    $stmtItem->bind_param("i", $item_id);
    $stmtItem->execute();

    $stmtOrderItem->close();
    $stmtInv->close();
    $stmtItem->close();

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
    <style>
        body {
            background: linear-gradient(135deg, #6e8efb, #a777e3, #f68b1f);
            color: white;
        }
        .container {
            background: rgba(0, 0, 0, 0.8);
            border-radius: 10px;
            padding: 20px;
            color: white;
        }
        table {
            background: rgba(255, 255, 255, 0.9);
            color: black;
        }
        h2, h4 {
            text-align: center;
        }
        .form-control, .btn {
            border-radius: 5px;
        }
        .btn {
            box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.5);
        }
        .badge {
            padding: 5px 10px;
        }
    </style>
    <script>
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

    <?php if (isset($item)): ?>
        <!-- Edit Item Form -->
        <form method="POST" class="mb-4">
            <h4>Edit Item</h4>
            <input type="hidden" name="item_id" value="<?php echo $item['item_id']; ?>">
            <div class="form-group">
                <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($item['name']); ?>" required>
            </div>
            <div class="form-group">
                <textarea name="description" class="form-control" rows="3" required><?php echo htmlspecialchars($item['description']); ?></textarea>
            </div>
            <div class="form-group">
                <input type="number" name="quantity" class="form-control" value="<?php echo htmlspecialchars($item['quantity']); ?>" required>
            </div>
            <div class="form-group">
                <input type="number" name="price" class="form-control" value="<?php echo htmlspecialchars($item['price']); ?>" required>
            </div>
            <div class="form-group">
                <select name="supplier_id" class="form-control" required>
                    <option value="">Select Supplier</option>
                    <?php
                    $suppliers = $conn->query("SELECT * FROM Supplier");
                    while ($supplier = $suppliers->fetch_assoc()) {
                        $selected = $item['supplier_id'] == $supplier['supplier_id'] ? 'selected' : '';
                        echo "<option value='{$supplier['supplier_id']}' $selected>{$supplier['name']}</option>";
                    }
                    ?>
                </select>
            </div>
            <button type="submit" name="update_item" class="btn btn-success mt-2">Update Item</button>
        </form>
    <?php endif; ?>

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

    <!-- Add New Supplier Form -->
    <div id="supplierForm" style="display:none;">
        <h4>Add New Supplier</h4>
        <form method="POST">
            <div class="form-group">
                <input type="text" name="supplier_name" class="form-control" placeholder="Supplier Name" required>
            </div>
            <div class="form-group">
                <input type="text" name="contact" class="form-control" placeholder="Supplier Contact" required>
            </div>
            <div class="form-group">
                <input type="text" name="address" class="form-control" placeholder="Supplier Address" required>
            </div>
            <button type="submit" name="add_supplier" class="btn btn-success mt-2">Add Supplier</button>
        </form>
    </div>

    <!-- Inventory Table -->
    <table class="table table-bordered mt-4">
        <thead>
            <tr>
                <th>Item ID</th>
                <th>Name</th>
                <th>Description</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Supplier</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $result = $conn->query("SELECT Item.item_id, Item.name, Item.description, Inventory.quantity, Item.price, Supplier.name AS supplier_name,
                                    CASE 
                                        WHEN Inventory.quantity < 10 THEN 'Low Stock'
                                        ELSE 'In Stock'
                                    END AS status
                                    FROM Inventory
                                    INNER JOIN Item ON Inventory.item_id = Item.item_id
                                    INNER JOIN Supplier ON Item.supplier_id = Supplier.supplier_id");
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['item_id']}</td>
                        <td>" . htmlspecialchars($row['name']) . "</td>
                        <td>" . htmlspecialchars($row['description']) . "</td>
                        <td>" . htmlspecialchars($row['quantity']) . "</td>
                        <td>" . htmlspecialchars($row['price']) . "</td>
                        <td>" . htmlspecialchars($row['supplier_name']) . "</td>
                        <td>" . ($row['status'] == 'Low Stock' ? "<span class='badge bg-danger'>Low Stock</span>" : "<span class='badge bg-success'>In Stock</span>") . "</td>
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

<?php include 'admin_footer.php'; ?>
</body>
</html>

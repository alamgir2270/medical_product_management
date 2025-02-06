<?php
session_start();
include '../includes/db.php'; // Database connection

// Ensure user is logged in and has 'user' role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: ../login.php");
    exit();
}

// Handle the form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $item_id = $_POST['item_id'];
    $quantity = $_POST['quantity'];

    // Fetch available quantity and price from Inventory and Item tables
    $stmt = $conn->prepare("SELECT inv.quantity, i.price, i.name FROM Inventory inv JOIN Item i ON inv.item_id = i.item_id WHERE inv.item_id = ?");
    $stmt->bind_param("i", $item_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $inventory = $result->fetch_assoc();

    if ($inventory && $inventory['quantity'] >= $quantity) {
        // Insert into Orders table
        $stmt = $conn->prepare("INSERT INTO Orders (user_id, order_date) VALUES (?, NOW())");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $order_id = $stmt->insert_id; // Get the inserted order's ID

        // Insert into Order_Item table
        $stmt = $conn->prepare("INSERT INTO Order_Item (order_id, item_id, quantity) VALUES (?, ?, ?)");
        $stmt->bind_param("iii", $order_id, $item_id, $quantity);
        $stmt->execute();

        // Update inventory
        $stmt = $conn->prepare("UPDATE Inventory SET quantity = quantity - ? WHERE item_id = ?");
        $stmt->bind_param("ii", $quantity, $item_id);
        $stmt->execute();

        // Calculate the total price of the order
        $total_price = $inventory['price'] * $quantity;

        // Success message
        $success_message = "Order placed successfully! Total: ৳" . $total_price;
    } else {
        // Error message if stock is insufficient
        $error_message = "Insufficient stock for the selected item.";
    }
}

// Fetch all items for display
$stmt = $conn->prepare("SELECT i.item_id, i.name, i.price, inv.quantity 
                        FROM Item i
                        JOIN Inventory inv ON i.item_id = inv.item_id");
$stmt->execute();
$items = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Place Order</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        /* Gradient Background */
        body {
            background: linear-gradient(135deg, #74ebd5, #9face6);
            color: #333;
            font-family: 'Arial', sans-serif;
            min-height: 100vh;
        }

        /* Center content and style form */
        .container {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 10px;
            box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.2);
            padding: 20px;
            margin-top: 50px;
        }

        h2 {
            color: #5a67d8;
            font-weight: bold;
        }

        .btn-primary {
            background-color: #6a5acd;
            border: none;
        }

        .btn-primary:hover {
            background-color: #5b4cc7;
        }

        .alert {
            margin-top: 15px;
        }
    </style>
    
</head>
<body>
<?php include 'user_layout.php'; ?>

<div class="container">
    <h2 class="text-center">Place an Order</h2>

    <?php if (isset($success_message)): ?>
        <div class="alert alert-success"><?php echo $success_message; ?></div>
    <?php elseif (isset($error_message)): ?>
        <div class="alert alert-danger"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label for="item_id" class="form-label">Select Item</label>
            <select name="item_id" id="item_id" class="form-select" required>
                <option value="">-- Select an Item --</option>
                <?php while ($item = $items->fetch_assoc()): ?>
                    <option value="<?php echo $item['item_id']; ?>">
                        <?php echo $item['name'] . " - ৳" . $item['price'] . " (Stock: " . $item['quantity'] . ")"; ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="quantity" class="form-label">Quantity</label>
            <input type="number" name="quantity" id="quantity" class="form-control" min="1" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Place Order</button>
    </form>
</div>

<?php include 'user_footer.php'; ?>
</body>
</html>

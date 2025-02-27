<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}
include '../includes/db.php';

// Get total sales
$total_sales = $conn->query("SELECT SUM(Item.price * Order_Item.quantity) AS total FROM Order_Item INNER JOIN Item ON Order_Item.item_id = Item.item_id")->fetch_assoc()['total'];

// Handle date filter
$start_date = $_POST['start_date'] ?? '1970-01-01';
$end_date = $_POST['end_date'] ?? date('Y-m-d');

// Get orders between the date range
$sql = "
    SELECT Orders.order_id, User.username, Orders.order_date,
           SUM(Item.price * Order_Item.quantity) AS total_amount
    FROM Orders
    INNER JOIN User ON Orders.user_id = User.user_id
    INNER JOIN Order_Item ON Orders.order_id = Order_Item.order_id
    INNER JOIN Item ON Order_Item.item_id = Item.item_id
    WHERE Orders.order_date BETWEEN '$start_date' AND '$end_date'
    GROUP BY Orders.order_id
    ORDER BY Orders.order_date DESC
";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Reports</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        /* Gradient Background */
        body {
            background: linear-gradient(to right, #ff7e5f, #feb47b);
            font-family: 'Arial', sans-serif;
            color: white;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .container {
            background-color: rgba(255, 255, 255, 0.8);
            padding: 30px;
            border-radius: 10px;
            margin-top: 50px;
        }

        .table th, .table td {
            color: #333;
        }

        .alert-info {
            background-color: #4caf50;
            color: white;
            font-weight: bold;
        }

        .btn-primary {
            background-color: #5c6bc0;
            border: none;
        }

        .btn-primary:hover {
            background-color: #3f51b5;
        }

        .row .col {
            margin-bottom: 20px;
        }

        h2, h4 {
            text-shadow: 2px 2px 5px rgba(0,0,0,0.1);
        }

        label {
            font-weight: bold;
        }
    </style>
</head>
<body>
<?php include 'admin_layout.php'; ?>
<div class="container mt-4">
    <h2>Sales Reports</h2>

    <!-- Filter Form -->
    <form method="POST" class="mb-3">
        <div class="row">
            <div class="col">
                <label for="start_date">Start Date:</label>
                <input type="date" class="form-control" id="start_date" name="start_date" value="<?php echo $start_date; ?>">
            </div>
            <div class="col">
                <label for="end_date">End Date:</label>
                <input type="date" class="form-control" id="end_date" name="end_date" value="<?php echo $end_date; ?>">
            </div>
            <div class="col">
                <button type="submit" class="btn btn-primary mt-4">Filter</button>
            </div>
        </div>
    </form>

    <div class="alert alert-info">Total Sales: <?php echo number_format($total_sales, 2); ?> BDT</div>

    <!-- Table of Orders -->
    <h4>Orders</h4>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>User</th>
                <th>Order Date</th>
                <th>Total Amount</th>
            </tr>
        </thead>
        <tbody>
            <?php
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['order_id']}</td>
                        <td>{$row['username']}</td>
                        <td>{$row['order_date']}</td>
                        <td>" . number_format($row['total_amount'], 2) . " BDT</td>
                      </tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<?php include 'admin_footer.php'; ?>
</body>
</html>

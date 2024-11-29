<?php
session_start();

// Destroy session and redirect to login with a message
session_destroy();
header("refresh:5;url=../login.php"); // Redirect to login page after 10 seconds
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logout</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="d-flex justify-content-center align-items-center vh-100 bg-light">
    <div class="text-center">
        <h1 class="text-success">Logout Successful!</h1>
        <p class="text-muted">You will be redirected to the login page in 5 seconds.</p>
        <p>If not, <a href="../index.php" class="text-primary">click here</a>.</p>
    </div>
</body>
</html>

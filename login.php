<?php
session_start();
include 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username_or_email = $_POST['username_or_email'];
    $password = $_POST['password'];

    // Fetch user based on username or email
    $sql = "SELECT * FROM User WHERE (username = ? OR email = ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ss', $username_or_email, $username_or_email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            // Redirect based on role
            if ($user['role'] === 'admin') {
                header('Location: admin/dashboard.php');
            } else {
                header('Location: user/user_dashboard.php');
            }
            exit();
        } else {
            $error = "Invalid username or password!";
        }
    } else {
        $error = "Invalid username or password!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background-image: url('assets/images/login.jpg'); /* Correct relative path */
            background-size: cover;
            background-repeat: no-repeat;
            background-attachment: fixed;
        }
        .navbar {
            background-color: #343a40; /* Dark background for the navbar */
        }
        .navbar-brand, .nav-link {
            color: #ffffff !important; /* White text color */
        }
        .nav-link:hover {
            color: #ffc107 !important; /* Gold on hover */
        }
        .container {
            background-color: rgba(255, 255, 255, 0.8); /* Semi-transparent background for the form */
            border-radius: 10px;
            padding: 20px;
        }
        .main{
            width: 40%;
            border-radius: 10px;
            padding: 20px;
            background-color: rgba(255, 255, 255, 0.8); 
            margin:auto;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Medical Products Management</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="signup.php">Sign Up</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">Log In</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="main mt-5">
        <div class=" justify-content-center">
            <div class="">
                <h2 class="text-center ">Login</h2>
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                <form method="POST">
                    <div class="mb-3">
                        <label for="username_or_email" class="form-label">Username or Email</label>
                        <input type="text" class="form-control" id="username_or_email" name="username_or_email" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Login</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>

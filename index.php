<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medical Products Supply Management</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <!-- Link to external stylesheet -->
    <link rel="stylesheet" href="assets/css/styles.css">
    <style>
        /* Apply background image to the entire page */
        body {
            background-image: url('assets/images/background.jpg'); /* Add your image path here */
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            font-family: Arial, sans-serif;
            background-color: #f0f2f5; /* Fallback color */
        }

        /* Colorful background gradient for .main */
        .main {
            width: 50%;
            margin: auto;
            padding-top: 20px;
            padding-bottom: 20px;
            background: linear-gradient(to right, rgba(34, 193, 195, 0.8), rgba(253, 187, 45, 0.8)); /* Gradient background */
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2); /* Adding a subtle shadow */
        }

        /* Styling for the heading with colorful text */
        h1 {
            font-size: 2.5rem;
            background: -webkit-linear-gradient(left, #f06c64, #ff007f); /* Gradient from pink to red */
            -webkit-background-clip: text;
            color: transparent;
            font-weight: bold;
        }

        /* Text color for the paragraph */
        p {
            font-size: 1.5rem;
            color: #f4f4f4;
            font-weight: bold;
        }

        /* Button styles */
        .btn {
            font-weight: bold;
            padding: 10px 20px;
            text-transform: uppercase;
        }

        .btn-primary {
            background-color: #0069d9;
            border-color: #0062cc;
        }

        .btn-success {
            background-color: #28a745;
            border-color: #218838;
        }

        /* Applying color to the second h1 line */
        .second-heading {
            background: -webkit-linear-gradient(left, #f06c64, #ff007f); /* Gradient from pink to red */
            -webkit-background-clip: text;
            color: transparent;
            font-size: 2.2rem;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="main text-center">
        <h1>Welcome to </h1>
        <h1 class="second-heading">Medical Products Supply Management System</h1>
        <p class="mt-3">Please login or sign up to continue.</p>
        <div class="d-flex justify-content-center gap-3 mt-3">
            <a href="login.php" class="btn btn-primary">Login</a>
            <a href="signup.php" class="btn btn-success">Sign Up</a>
        </div>
    </div>
</body>
</html>

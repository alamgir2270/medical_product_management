<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>User Dashboard</title>
    <style>
        /* Ensure footer sticks to the bottom of the viewport */
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            margin: 0;
        }
        main {
            flex: 1; /* Pushes the footer to the bottom */
        }
        footer {
            background-color: #343a40; /* Dark background */
            color: white;
        }
    </style>
</head>
<body>
    <!-- Main content of the dashboard -->
    <main>
        <div class="container py-5">
            <h1 class="text-center">Welcome to the User Dashboard</h1>
            <p class="text-center">Your dashboard content goes here.</p>
        </div>
    </main>
    
    <!-- Footer -->
    <footer class="text-center py-3">
        <p>&copy; 2024 Medical Management System. All Rights Reserved.</p>
    </footer>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

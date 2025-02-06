<?php
// Assume you have a database connection set up

// 1. Get the token from the URL
if (isset($_GET['token'])) {
    $token = $_GET['token'];
    
    // 2. Validate the token (you need a database connection for this)
    $stmt = $conn->prepare("SELECT * FROM password_resets WHERE token = ? AND expiration > NOW()");
    $stmt->bind_param('s', $token);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        // Token is valid
        $row = $result->fetch_assoc();
        $user_id = $row['user_id'];
        
        // If token is valid, allow the user to reset the password
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['new_password'])) {
            // Hash the new password
            $new_password = password_hash($_POST['new_password'], PASSWORD_BCRYPT);
            
            // Update the user's password in the database
            $update_stmt = $conn->prepare("UPDATE users SET password = ? WHERE user_id = ?");
            $update_stmt->bind_param('si', $new_password, $user_id);
            $update_stmt->execute();
            
            // Optional: Delete the token after it is used
            $delete_stmt = $conn->prepare("DELETE FROM password_resets WHERE token = ?");
            $delete_stmt->bind_param('s', $token);
            $delete_stmt->execute();
            
            echo "Password has been reset successfully.";
        }
    } else {
        echo "Invalid or expired token.";
    }
}
?>

<!-- HTML form for resetting the password -->
<form method="POST" action="">
    <label for="new_password">New Password</label>
    <input type="password" name="new_password" id="new_password" required>
    <input type="submit" value="Reset Password">
</form>

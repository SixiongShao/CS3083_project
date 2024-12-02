<?php
session_start(); // Start the session
include "connect.php";

// Get the values from the form
$un = $_POST['uname'];
$pass = $_POST['pwd'];

// Prepare a SQL statement to prevent SQL injection
$sql = "SELECT * FROM users WHERE username = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $un);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($result && mysqli_num_rows($result) === 1) {
    $row = mysqli_fetch_assoc($result);
    if (password_verify($pass, $row['Password'])) {
        // Store user ID in session
        $_SESSION['user_id'] = $row['User_ID']; // Assuming 'id' is the column name for user ID in your database
        $_SESSION['user_role'] = $row['ProfileSettings'];
        header("Location: main.php");
        exit();
    } else {
        header("Location: login.html?error=incorrect username or password");
        exit();
    }
} else {
    header("Location: login.html?error=incorrect username or password");
    exit();
}
?>
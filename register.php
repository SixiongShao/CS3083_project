<?php
// Database connection details
include "connect.php";

// Function to get the next User ID
function getNextUserId($conn) {
    // Query to get the maximum User_ID from the Users table
    $query = "SELECT COALESCE(MAX(User_ID), 0) + 1 AS NextUserId FROM Users";
    $result = $conn->query($query);

    if ($result) {
        $row = $result->fetch_assoc();
        return $row['NextUserId'];
    } else {
        die("Error fetching next User ID: " . $conn->error);
    }
}

// Handle registration form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // Get the next User ID
    $userId = getNextUserId($conn);

    // Prepare an SQL statement
    $stmt = $conn->prepare("INSERT INTO Users (User_ID, FullName, Email, Username, Password, ProfileSettings) VALUES (?, ?, ?, ?, ?, 'C')");

    if ($stmt) {
        // Bind the parameters
        $stmt->bind_param("issss", $userId, $fullname, $email, $username, $hashedPassword);

        // Execute the statement
        if ($stmt->execute()) {
            echo "Registration successful! Your User ID is: " . $userId;
            header("Location: login.php");
            exit(); // Ensure no further code is executed
        } else {
            // Check for duplicate entry error
            if ($stmt->errno === 1062) {
                echo "Error: Duplicate entry detected. Details: " . $stmt->error;
            } else {
                echo "Error: " . $stmt->error;
            }
        }

        // Close the statement
        $stmt->close();
    } else {
        echo "Error: " . $conn->error;
    }
}

// Close the connection
$conn->close();
?>

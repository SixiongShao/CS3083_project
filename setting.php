<?php
session_start();
require 'connect.php';

// Assume user ID is stored in session after login
$userId = $_SESSION['user_id'];

// Fetch current user data
$query = "SELECT FullName, Email, Username FROM Users WHERE User_ID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Check if email or username is already taken by another user
    $checkQuery = "SELECT User_ID FROM Users WHERE (Email = ? OR Username = ?) AND User_ID != ?";
    $checkStmt = $conn->prepare($checkQuery);
    $checkStmt->bind_param("ssi", $email, $username, $userId);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows > 0) {
        $_SESSION['error_message'] = "Error: Email or Username already taken.";
    } else {
        // Hash the password if it is not empty
        $hashedPassword = !empty($password) ? password_hash($password, PASSWORD_BCRYPT) : null;

        // Prepare update query
        if ($hashedPassword) {
            $updateQuery = "UPDATE Users SET FullName = ?, Email = ?, Username = ?, Password = ? WHERE User_ID = ?";
            $updateStmt = $conn->prepare($updateQuery);
            $updateStmt->bind_param("ssssi", $fullname, $email, $username, $hashedPassword, $userId);
        } else {
            // If password is not being updated
            $updateQuery = "UPDATE Users SET FullName = ?, Email = ?, Username = ? WHERE User_ID = ?";
            $updateStmt = $conn->prepare($updateQuery);
            $updateStmt->bind_param("sssi", $fullname, $email, $username, $userId);
        }

        // Execute update statement
        if ($updateStmt->execute()) {
            $_SESSION['success_message'] = "Profile updated successfully!";
        } else {
            $_SESSION['error_message'] = "Error updating profile: " . htmlspecialchars($updateStmt->error);
        }

        // Close statement
        $updateStmt->close();

        // Redirect to the same page to display the message
        header("Location: setting.php");
        exit;
    }

    // Close check statement
    $checkStmt->close();
}

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Setting</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #f4f4f4;
            padding: 10px 20px;
            border-bottom: 1px solid #ddd;
        }
        .user-id {
            font-size: 16px;
            font-weight: bold;
        }
        .content {
            padding: 20px;
            display: flex;
            gap: 10px;
        }
        .button, .logout-button {
            text-align: center;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            font-size: 18px;
            border-radius: 5px;
            flex: 1;
            max-width: 150px;
        }
        .button:hover, .logout-button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <h1>Profile Settings</h1><br>
    <div class="content">
        <!-- Display success or error messages -->
        <?php if (isset($_SESSION['success_message'])): ?>
            <p class="success"><?= htmlspecialchars($_SESSION['success_message']) ?></p>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error_message'])): ?>
            <p class="error"><?= htmlspecialchars($_SESSION['error_message']) ?></p>
            <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>

        <form method="POST">
            <br><br>
            <label for="fullname">Full Name:</label>
            <input type="text" id="fullname" name="fullname" value="<?= htmlspecialchars($user['FullName']) ?>" required>
            <br><br>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['Email']) ?>" required>
            <br><br>
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" value="<?= htmlspecialchars($user['Username']) ?>" required>
            <br><br>
            <label for="password">New Password (leave blank to keep current):</label>
            <input type="password" id="password" name="password">
            <br><br>
            <button type="submit" class="button">Update Profile</button>
            <br><br>
            <a href="main.php" class="logout-button">Back</a>
        </form>
    </div>
</body>
</html>

<?php
session_start();

// Ensure the user is logged in and fetch their role
if (!isset($_SESSION['user_role'])) {
    // Redirect to login if role is not set
    header("Location: login.php");
    exit;
}

$user_role = $_SESSION['user_role']; // 'M' for manager, 'C' for coach
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
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
        .button2 {
            text-align: center;
            padding: 40px 80px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            font-size: 30px;
            border-radius: 15px;
            flex: 1;
            max-width: 800px;
        }
        .button:hover, .logout-button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <header>
        <h1>Home</h1>
        <div class="user-id">User ID: <?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'Not logged in'; ?></div>
        <?php if ($user_role === 'M'): ?>
            <h2>Manager</h2>
        <?php endif; ?>
        <?php if ($user_role === 'C'): ?>
            <h2>Coach</h2>
        <?php endif; ?>
        <a href="setting.php" class="logout-button">Profile Setting</a>
        <a href="logout.php" class="logout-button">Logout</a>
    </header>
    <div class="content">
        <form action="league.php" method="post">     
            <input type="submit" value="League" class="button2" />
        </form>
        <form action="match.php" method="post">     
            <input type="submit" value="Match" class="button2" />
        </form>
        <!-- Only show Trade button for managers -->
        <?php if ($user_role === 'M'): ?>
        <form action="trade.php" method="post">     
            <input type="submit" value="Trade" class="button2" />
        </form>
        <?php endif; ?>
        <form action="player.php" method="post">     
            <input type="submit" value="Player" class="button2" />
        </form>
    </div>
</body>
</html>

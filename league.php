<?php
session_start();
include "connect.php";

// SQL query to select all data from leagues table
$sql = "SELECT * FROM leagues";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>League</title>
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
        }
        .button, .logout-button {
            text-align: center;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            font-size: 18px;
            border-radius: 5px;
        }
        .button:hover, .logout-button:hover {
            background-color: #0056b3;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        th {
            background-color: #f4f4f4;
        }
    </style>
</head>
<body>
    <header>
        <h1>League</h1>
        <div class="user-id">User ID: <?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'Not logged in'; ?></div>
        <a href="main.php" class="logout-button">Back to main</a>
        <a href="logout.php" class="logout-button">Logout</a>
    </header>

    <div class="content">
    <?php
if ($result->num_rows > 0) {
    echo "<table><tr><th>League ID</th><th>League Name</th><th>League Type</th><th>Commissioner</th><th>Max Teams</th><th>Draft Date</th><th>Action</th></tr>";
    // Output data of each row
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>" . htmlspecialchars($row["League_ID"]) . "</td>
                <td>" . htmlspecialchars($row["LeagueName"]) . "</td>
                <td>" . htmlspecialchars($row["LeagueType"]) . "</td>
                <td>" . htmlspecialchars($row["Commissioner"]) . "</td>
                <td>" . htmlspecialchars($row["MaxTeams"]) . "</td>
                <td>" . htmlspecialchars($row["DraftDate"]) . "</td>
                <td>
                    <form action='team.php' method='post' style='display:inline;'>
                        <input type='hidden' name='league_id' value='" . htmlspecialchars($row["League_ID"]) . "' />
                        <input type='submit' value='View Teams' class='button' />
                    </form>
                    <form action='drafts.php' method='post' style='display:inline;'>
                        <input type='hidden' name='league_id' value='" . htmlspecialchars($row["League_ID"]) . "' />
                        <input type='submit' value='View Drafts' class='button' />
                    </form>
                </td>
              </tr>";
    }
    echo "</table>";
} else {
    echo "No leagues found.";
}

// Close connection
$conn->close();
?>
        
    </div>
</body>
</html>
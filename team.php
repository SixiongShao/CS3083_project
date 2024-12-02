<?php
session_start();
include "connect.php";

// Check if league_id is passed via POST request
if (isset($_POST['league_id'])) {
    $_SESSION['league_id'] = $_POST['league_id'];
}

// Ensure league_id is set in session
if (!isset($_SESSION['league_id'])) {
    die("League ID not set in session.");
}

// Retrieve League ID from session
$league_id = $_SESSION['league_id'];

// Update team status if requested
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $team_id = $_POST['team_id'];
    $new_status = $_POST['new_status'];

    $updateSql = "UPDATE Teams SET Status = ? WHERE Team_ID = ?";
    $updateStmt = $conn->prepare($updateSql);
    $updateStmt->bind_param("si", $new_status, $team_id);
    $updateStmt->execute();
    $updateStmt->close();
}

// SQL query to select all teams for the given League_ID
$sql = "SELECT Team_ID, TeamName, Owner, League_ID, TotalPoints, Ranking, Status FROM Teams WHERE League_ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $league_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Team</title>
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
        .button, .logout-button, .action-button {
            text-align: center;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            font-size: 18px;
            border-radius: 5px;
        }
        .button:hover, .logout-button:hover, .action-button:hover {
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
        .action-form {
            display: inline;
        }
    </style>
</head>
<body>
    <header>
    <h1>Teams in League ID: <?php echo htmlspecialchars($league_id); ?></h1>
        <div class="user-id">User ID: <?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'Not logged in'; ?></div>
        <a href="logout.php" class="logout-button">Logout</a>
    </header>

    <div class="content">
    <?php
        if ($result->num_rows > 0) {
            echo "<table>
                <tr>
                    <th>Team ID</th>
                    <th>Team Name</th>
                    <th>Owner</th>
                    <th>League ID</th>
                    <th>Total Points</th>
                    <th>Ranking</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>";
            // Output data of each row
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                    <td>" . htmlspecialchars($row["Team_ID"]) . "</td>
                    <td>" . htmlspecialchars($row["TeamName"]) . "</td>
                    <td>" . htmlspecialchars($row["Owner"]) . "</td>
                    <td>" . htmlspecialchars($row["League_ID"]) . "</td>
                    <td>" . htmlspecialchars($row["TotalPoints"]) . "</td>
                    <td>" . htmlspecialchars($row["Ranking"]) . "</td>
                    <td>" . htmlspecialchars($row["Status"]) . "</td>
                    <td>

                        <form method='GET' action='contracts.php' class='action-form'>
                            <input type='hidden' name='Team_ID' value='" . htmlspecialchars($row["Team_ID"]) . "'>
                            <button type='submit' class='action-button'>Contracts</button>
                        </form>
                        <form method='GET' action='waivers.php' class='action-form'>
                            <input type='hidden' name='Team_ID' value='" . htmlspecialchars($row["Team_ID"]) . "'>
                            <button type='submit' class='action-button'>Waivers</button>
                        </form>
                    </td>
                </tr>";
            }
            echo "</table>";
        } else {
            echo "No teams found for this league.";
        }

        // Close connection
        $stmt->close();
        $conn->close();
        ?>
        

        <br>
        <form action="league.php" method="post">     
            <input type="submit" value="Back to League" class="button" />
        </form>
    </div>
</body>
</html>

<?php
session_start();
require 'connect.php';

// Get Team_ID from query string
$Team_ID = isset($_GET['Team_ID']) ? htmlspecialchars($_GET['Team_ID']) : 'Unknown';

// Fetch waivers for the team
$query = "SELECT Waiver_ID, Player_ID, WaiverOrder, WaiverStatus, WaiverPickupDate FROM Waivers WHERE Team_ID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $Team_ID);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Waivers</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #f4f4f4;
            padding: 10px 20px;
            border-bottom: 1px solid #ddd;
        }
        .team-id {
            font-weight: bold;
        }
        .back-button {
            text-decoration: none;
            color: white;
            background-color: #007bff;
            padding: 10px 15px;
            border-radius: 5px;
        }
        .back-button:hover {
            background-color: #0056b3;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
    </style>
</head>
<body>
    <header>
        <div class="team-id">Team ID: <?= htmlspecialchars($Team_ID) ?></div>
        <a href="team.php" class="back-button">Back to Teams</a>
    </header>
    <table>
        <thead>
            <tr>
                <th>Waiver ID</th>
                <th>Player ID</th>
                <th>Waiver Order</th>
                <th>Waiver Status</th>
                <th>Waiver Pickup Date</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['Waiver_ID']) ?></td>
                        <td><?= htmlspecialchars($row['Player_ID']) ?></td>
                        <td><?= htmlspecialchars($row['WaiverOrder']) ?></td>
                        <td><?= htmlspecialchars($row['WaiverStatus']) ?></td>
                        <td><?= htmlspecialchars($row['WaiverPickupDate']) ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5">No waivers found for this team.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <?php
    // Close the statement and connection
    $stmt->close();
    $conn->close();
    ?>
</body>
</html>

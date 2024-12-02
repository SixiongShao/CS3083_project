<?php
require 'connect.php';

// Fetch team rankings based on wins, including team names
$query = "SELECT Teams.Team_ID, Teams.TeamName, COUNT(matches.Winner) AS Win_Count
          FROM matches
          JOIN Teams ON matches.Winner = Teams.Team_ID
          WHERE matches.Winner IS NOT NULL
          GROUP BY Teams.Team_ID, Teams.TeamName
          ORDER BY Win_Count DESC";

$result = $conn->query($query);

if ($result && $result->num_rows > 0) {
    $rankings = [];
    while ($row = $result->fetch_assoc()) {
        $rankings[] = $row;
    }
} else {
    $error_message = "No rankings available.";
}

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Team Rankings</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        header {
            background-color: #f4f4f4;
            padding: 10px 20px;
            border-bottom: 1px solid #ddd;
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
        .new-match {
            text-decoration: none;
            color: white;
            background-color: #007bff;
            padding: 10px 15px;
            border-radius: 5px;
            font-size: 16px;
        }
    </style>
</head>
<body>
    <header>
        <h1>Team Rankings by Wins</h1>
        <a href="match.php" class="new-match">Back to Match</a>
    </header>
    <div class="content">
        <?php if (!empty($rankings)): ?>
            <table>
                <thead>
                    <tr>
                        <th>Rank</th>
                        <th>Team ID</th>
                        <th>Team Name</th>
                        <th>Win Count</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rankings as $index => $team): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= htmlspecialchars($team['Team_ID']) ?></td>
                        <td><?= htmlspecialchars($team['TeamName']) ?></td>
                        <td><?= htmlspecialchars($team['Win_Count']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No rankings available.</p>
        <?php endif; ?>
    </div>
</body>
</html>

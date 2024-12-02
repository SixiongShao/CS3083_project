<?php
session_start();
require 'connect.php';

// Initialize variables for search functionality
$searchConditions = [];
$params = [];
$searchResults = [];

// Handle search form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search'])) {
    if (!empty($_POST['Player_ID'])) {
        $searchConditions[] = "Player_ID LIKE ?";
        $params[] = "%" . $_POST['Player_ID'] . "%";
    }
    if (!empty($_POST['FullName'])) {
        $searchConditions[] = "FullName LIKE ?";
        $params[] = "%" . $_POST['FullName'] . "%";
    }
    if (!empty($_POST['Sport'])) {
        $searchConditions[] = "Sport LIKE ?";
        $params[] = "%" . $_POST['Sport'] . "%";
    }
    if (!empty($_POST['Position'])) {
        $searchConditions[] = "Position LIKE ?";
        $params[] = "%" . $_POST['Position'] . "%";
    }
    if (!empty($_POST['RealTeam'])) {
        $searchConditions[] = "RealTeam LIKE ?";
        $params[] = "%" . $_POST['RealTeam'] . "%";
    }

    // Build the query with the search conditions
    $query = "SELECT * FROM Players";
    if (!empty($searchConditions)) {
        $query .= " WHERE " . implode(" AND ", $searchConditions);
    }

    $stmt = $conn->prepare($query);
    if ($params) {
        $stmt->bind_param(str_repeat('s', count($params)), ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $searchResults = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
} else {
    // Default query to fetch all players
    $query = "SELECT * FROM Players";
    $result = $conn->query($query);
    $searchResults = $result->fetch_all(MYSQLI_ASSOC);
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Player Management</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
        }
        .search-table {
            margin-bottom: 20px;
        }
        .search-table input {
            width: 100%;
            padding: 5px;
            margin-bottom: 5px;
        }
        .search-table button {
            padding: 10px 15px;
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
        }
        .search-table button:hover {
            background-color: #0056b3;
        }
        .return-button {
            margin-top: 20px;
            padding: 10px 15px;
            background-color: #28a745;
            color: white;
            text-decoration: none;
            border: none;
        }
        .return-button:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <h1>Player Management</h1>
    <a href="main.php" class="return-button">Back to Main</a>
    <!-- Search Functionality -->
    <form method="POST" class="search-table">
        <table>
            <thead>
                <tr>
                    <th>Player ID</th>
                    <th>Player Name</th>
                    <th>Sport</th>
                    <th>Position</th>
                    <th>Real Team</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><input type="text" name="Player_ID" placeholder="Search by Player ID"></td>
                    <td><input type="text" name="FullName" placeholder="Search by Player Name"></td>
                    <td><input type="text" name="Sport" placeholder="Search by Sport"></td>
                    <td><input type="text" name="Position" placeholder="Search by Position"></td>
                    <td><input type="text" name="RealTeam" placeholder="Search by Real Team"></td>
                    <td><button type="submit" name="search">Search</button></td>
                </tr>
            </tbody>
        </table>
    </form>

    <!-- Player Table -->
    <table>
        <thead>
            <tr>
                <th>Player ID</th>
                <th>Player Name</th>
                <th>Sport</th>
                <th>Position</th>
                <th>Real Team</th>
                <th>Fantasy Points</th>
                <th>Availability</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($searchResults as $player): ?>
                <tr>
                    <td><?= htmlspecialchars($player['Player_ID']) ?></td>
                    <td><?= htmlspecialchars($player['FullName']) ?></td>
                    <td><?= htmlspecialchars($player['Sport']) ?></td>
                    <td><?= htmlspecialchars($player['Position']) ?></td>
                    <td><?= htmlspecialchars($player['RealTeam']) ?></td>
                    <td><?= htmlspecialchars($player['FantasyPoints']) ?></td>
                    <td><?= htmlspecialchars($player['AvailabilityStatus']) ?></td>
                    <td>
                        <a href="playerstatistics.php?Player_ID=<?= htmlspecialchars($player['Player_ID']) ?>">Player Statistics</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <br>

    <!-- Return Button -->
    <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
        <a href="player.php" class="return-button">Return to Full Table</a>
    <?php endif; ?>
</body>
</html>

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

// Fetch drafts for the league
$query = "SELECT Draft_ID, DraftDate, DraftOrder, DraftStatus FROM Drafts WHERE League_ID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $league_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Drafts</title>
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
        .league-id {
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
        <div class="league-id">League ID: <?= htmlspecialchars($league_id) ?></div>
        <a href="league.php" class="back-button">Back to Leagues</a>
    </header>
    <table>
        <thead>
            <tr>
                <th>Draft ID</th>
                <th>Draft Date</th>
                <th>Draft Order</th>
                <th>Draft Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['Draft_ID']) ?></td>
                        <td><?= htmlspecialchars($row['DraftDate']) ?></td>
                        <td><?= htmlspecialchars($row['DraftOrder']) ?></td>
                        <td><?= htmlspecialchars($row['DraftStatus']) ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4">No drafts found for this league.</td>
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

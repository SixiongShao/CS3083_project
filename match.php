<?php
session_start();
require 'connect.php';

// Fetch user role from session
$user_role = isset($_SESSION['user_role']) ? $_SESSION['user_role'] : '';

// Check if the form is submitted for an update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['Match_ID']) && $user_role === 'M') {
    $match_id = $_POST['Match_ID'];
    $team1_id = $_POST['Team1_ID'];
    $team2_id = $_POST['Team2_ID'];
    $match_date = $_POST['MatchDate'];
    $final_score = $_POST['FinalScore'];
    $winner = $_POST['Winner'];

    // Update the database
    $updateQuery = "UPDATE matches 
                    SET Team1_ID = '$team1_id', 
                        Team2_ID = '$team2_id', 
                        MatchDate = '$match_date', 
                        FinalScore = '$final_score', 
                        Winner = '$winner' 
                    WHERE Match_ID = '$match_id'";
    if ($conn->query($updateQuery) === TRUE) {
        $success_message = "Match updated successfully!";
    } else {
        $error_message = "Error updating match: " . $conn->error;
    }
}

// Fetch all matches from the database
$query = "SELECT * FROM matches";
$result = $conn->query($query);
$matches = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $matches[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Match Management</title>
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
        .new-match {
            text-decoration: none;
            color: white;
            background-color: #007bff;
            padding: 10px 15px;
            border-radius: 5px;
            font-size: 16px;
        }
        .new-match:hover {
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
        .edit-btn {
            padding: 5px 10px;
            font-size: 14px;
            color: white;
            background-color: #007bff;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }
        .edit-btn:hover {
            background-color: #0056b3;
        }
        input[type="text"], input[type="date"] {
            width: 100%;
            padding: 5px;
            box-sizing: border-box;
        }
    </style>
</head>
<body>
    <header>
        <h1>Matches</h1>
        <div class="user-id">User ID: <?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'Not logged in'; ?></div>
        <?php if ($user_role === 'M'): ?>
            <a href="new_match.php" class="new-match">New Match</a>
        <?php endif; ?>
        <a href="ranking.php" class="new-match">View Ranking</a>
        <a href="main.php" class="new-match">Back to Main</a>
        <a href="logout.php" class="new-match">Logout</a>
    </header>

    <div>
        <?php if (!empty($success_message)): ?>
            <p style="color: green;"><?= $success_message ?></p>
        <?php endif; ?>
        <?php if (!empty($error_message)): ?>
            <p style="color: red;"><?= $error_message ?></p>
        <?php endif; ?>
    </div>

    <table>
        <thead>
            <tr>
                <th>Match ID</th>
                <th>Team 1 ID</th>
                <th>Team 2 ID</th>
                <th>Match Date</th>
                <th>Final Score</th>
                <th>Winner</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($matches as $match): ?>
            <tr>
                <form method="POST">
                    <td>
                        <?= htmlspecialchars($match['Match_ID']) ?>
                        <input type="hidden" name="Match_ID" value="<?= htmlspecialchars($match['Match_ID']) ?>">
                    </td>
                    <td>
                        <input type="text" name="Team1_ID" value="<?= htmlspecialchars($match['Team1_ID']) ?>" <?= $user_role === 'C' ? 'readonly' : '' ?>>
                    </td>
                    <td>
                        <input type="text" name="Team2_ID" value="<?= htmlspecialchars($match['Team2_ID']) ?>" <?= $user_role === 'C' ? 'readonly' : '' ?>>
                    </td>
                    <td>
                        <input type="date" name="MatchDate" value="<?= htmlspecialchars($match['MatchDate']) ?>" <?= $user_role === 'C' ? 'readonly' : '' ?>>
                    </td>
                    <td>
                        <input type="text" name="FinalScore" value="<?= htmlspecialchars($match['FinalScore']) ?>" <?= $user_role === 'C' ? 'readonly' : '' ?>>
                    </td>
                    <td>
                        <input type="text" name="Winner" value="<?= htmlspecialchars($match['Winner']) ?>" <?= $user_role === 'C' ? 'readonly' : '' ?>>
                    </td>
                    <td>
                        <?php if ($user_role === 'M'): ?>
                            <button type="submit" class="edit-btn">Save</button>
                        <?php endif; ?>
                    </td>
                </form>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>

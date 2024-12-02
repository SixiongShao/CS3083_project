<?php
session_start();
require 'connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $team1_id = $_POST['Team1_ID'];
    $team2_id = $_POST['Team2_ID'];
    $match_date = $_POST['MatchDate'];
    $final_score = $_POST['FinalScore'];
    $winner = $_POST['Winner'];

    // Get the maximum Match_ID
    $maxIdQuery = "SELECT MAX(Match_ID) AS MaxMatchID FROM matches";
    $result = $conn->query($maxIdQuery);
    $row = $result->fetch_assoc();
    $new_match_id = $row['MaxMatchID'] + 1;

    // Insert the new match into the database with the new Match_ID
    $insertQuery = "INSERT INTO matches (Match_ID, Team1_ID, Team2_ID, MatchDate, FinalScore, Winner)
                    VALUES ('$new_match_id', '$team1_id', '$team2_id', '$match_date', '$final_score', '$winner')";

    if ($conn->query($insertQuery) === TRUE) {
        $success_message = "New match added successfully!";
    } else {
        $error_message = "Error adding match: " . $conn->error;
    }
}

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Match</title>
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
        form {
            max-width: 500px;
            margin: 20px auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        form label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        form input[type="text"], form input[type="date"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
        }
        form button {
            padding: 10px 15px;
            color: white;
            background-color: #007bff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        form button:hover {
            background-color: #0056b3;
        }
        .message {
            text-align: center;
            margin: 10px;
        }
        .success {
            color: green;
        }
        .error {
            color: red;
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
    </style>
</head>
<body>
    <header>
        <h1>Add New Match</h1>
        <a href="main.php" class="logout-button">Back to Main</a>
        <a href="match.php" class="logout-button">Back to Match</a>
    </header>
    <div class="message">
        <?php if (!empty($success_message)): ?>
            <p class="success"><?= htmlspecialchars($success_message) ?></p >
        <?php endif; ?>
        <?php if (!empty($error_message)): ?>
            <p class="error"><?= htmlspecialchars($error_message) ?></p >
        <?php endif; ?>
    </div>
    <form method="POST">
        <label for="Team1_ID">Team 1 ID</label>
        <input type="text" id="Team1_ID" name="Team1_ID" required>

        <label for="Team2_ID">Team 2 ID</label>
        <input type="text" id="Team2_ID" name="Team2_ID" required>

        <label for="MatchDate">Match Date</label>
        <input type="date" id="MatchDate" name="MatchDate" required>

        <label for="FinalScore">Final Score</label>
        <input type="text" id="FinalScore" name="FinalScore">

        <label for="Winner">Winner</label>
        <input type="text" id="Winner" name="Winner">

        <button type="submit">Add Match</button>
    </form>
</body>
</html>
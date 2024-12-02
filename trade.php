<?php
session_start();
require 'connect.php';

// Initialize messages
$success_message = "";
$error_message = "";

// Handle adding a new trade
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_trade'])) {
    $Team1_ID = $_POST['Team1_ID'];
    $Team2_ID = $_POST['Team2_ID'];
    $TradedPlayer1_ID = $_POST['TradedPlayer1_ID'];
    $TradedPlayer2_ID = $_POST['TradedPlayer2_ID'];
    $TradeDate = $_POST['TradeDate'];

    // Fetch the largest Trade_ID
    $sql_max_id = "SELECT IFNULL(MAX(Trade_ID), 0) AS max_id FROM Trades";
    $result = $conn->query($sql_max_id);
    if ($result) {
        $row = $result->fetch_assoc();
        $new_trade_id = $row['max_id'] + 1;
    } else {
        die("Error fetching max Trade_ID: " . $conn->error);
    }

    // Insert the new trade with the calculated Trade_ID
    $sql = "INSERT INTO Trades (Trade_ID, Team1_ID, Team2_ID, TradedPlayer1_ID, TradedPlayer2_ID, TradeDate) 
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("iiiiss", $new_trade_id, $Team1_ID, $Team2_ID, $TradedPlayer1_ID, $TradedPlayer2_ID, $TradeDate);
    if ($stmt->execute()) {
        $success_message = "Trade added successfully!";
        header("Location: trade.php");
        exit;
    } else {
        die("Execution failed: " . $stmt->error);
    }
}

// Fetch all trades from the database
$trades = [];
if ($conn) {
    $sql = "SELECT Trade_ID, Team1_ID, Team2_ID, TradedPlayer1_ID, TradedPlayer2_ID, TradeDate FROM Trades";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $trades[] = $row;
        }
    }
}

// Fetch all teams for dropdown lists
$teams = [];
$result = $conn->query("SELECT Team_ID, TeamName FROM Teams");
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $teams[] = $row;
    }
}

// Fetch players for Team 1
$team1_players = [];
if (isset($_POST['Team1_ID'])) {
    $team1_id = $_POST['Team1_ID'];

    $stmt = $conn->prepare("CALL get_players_by_team(?)");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("i", $team1_id);
    if (!$stmt->execute()) {
        die("Execution failed: " . $stmt->error);
    }

    $result = $stmt->get_result();
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $team1_players[] = $row;
        }
        $result->free();
    }

    while ($conn->more_results() && $conn->next_result()) {
        $result = $conn->store_result();
        if ($result) {
            $result->free();
        }
    }

    $stmt->close();
}

// Fetch players for Team 2
$team2_players = [];
if (isset($_POST['Team2_ID'])) {
    $team2_id = $_POST['Team2_ID'];

    $stmt = $conn->prepare("CALL get_players_by_team(?)");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("i", $team2_id);
    if (!$stmt->execute()) {
        die("Execution failed: " . $stmt->error);
    }

    $result = $stmt->get_result();
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $team2_players[] = $row;
        }
        $result->free();
    }

    while ($conn->more_results() && $conn->next_result()) {
        $result = $conn->store_result();
        if ($result) {
            $result->free();
        }
    }

    $stmt->close();
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trades Management</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
        }
        button {
            padding: 5px 10px;
            margin: 2px;
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
    <h1>Trades Management</h1>
    
    <div class="user-id">User ID: <?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'Not logged in'; ?></div>
    <br>
    <a href="main.php" class="logout-button">Back to main</a>

    <!-- Display success or error messages -->
    <?php if (!empty($success_message)): ?>
        <p class="success"><?= htmlspecialchars($success_message) ?></p>
    <?php endif; ?>
    <?php if (!empty($error_message)): ?>
        <p class="error"><?= htmlspecialchars($error_message) ?></p>
    <?php endif; ?>
<br>
    <form method="POST">
        <br>
        <table>
            <thead>
                <tr>
                    <th>Trade ID</th>
                    <th>Team 1 ID</th>
                    <th>Team 2 ID</th>
                    <th>Traded Player 1 ID</th>
                    <th>Traded Player 2 ID</th>
                    <th>Trade Date</th>
                </tr>
            </thead>
            <tbody>
                <!-- Display existing trades -->
                <?php foreach ($trades as $trade): ?>
                <tr>
                    <td><?= htmlspecialchars($trade['Trade_ID']) ?></td>
                    <td><?= htmlspecialchars($trade['Team1_ID']) ?></td>
                    <td><?= htmlspecialchars($trade['Team2_ID']) ?></td>
                    <td><?= htmlspecialchars($trade['TradedPlayer1_ID']) ?></td>
                    <td><?= htmlspecialchars($trade['TradedPlayer2_ID']) ?></td>
                    <td><?= htmlspecialchars($trade['TradeDate']) ?></td>
                </tr>
                <?php endforeach; ?>

                <!-- Form for adding a new trade -->
                <tr>
                    <td>New</td>
                    <td>
                        <select name="Team1_ID" onchange="this.form.submit()">
                            <option value="">Select Team 1</option>
                            <?php foreach ($teams as $team): ?>
                                <option value="<?= $team['Team_ID'] ?>" <?= isset($_POST['Team1_ID']) && $_POST['Team1_ID'] == $team['Team_ID'] ? 'selected' : '' ?>>
                                    <?= $team['Team_ID'] ?> - <?= $team['TeamName'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <td>
                        <select name="Team2_ID" <?= empty($_POST['Team1_ID']) ? 'disabled' : '' ?> onchange="this.form.submit()">
                            <option value="">Select Team 2</option>
                            <?php foreach ($teams as $team): ?>
                                <?php if (!empty($_POST['Team1_ID']) && $_POST['Team1_ID'] != $team['Team_ID']): ?>
                                    <option value="<?= $team['Team_ID'] ?>" <?= isset($_POST['Team2_ID']) && $_POST['Team2_ID'] == $team['Team_ID'] ? 'selected' : '' ?>>
                                        <?= $team['Team_ID'] ?> - <?= $team['TeamName'] ?>
                                    </option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <td>
                        <select name="TradedPlayer1_ID" <?= empty($_POST['Team1_ID']) ? 'disabled' : '' ?>>
                            <option value="">Select Player</option>
                            <?php foreach ($team1_players as $player): ?>
                                <option value="<?= $player['Player_ID'] ?>"><?= $player['Player_ID'] ?> - <?= $player['FullName'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <td>
                        <select name="TradedPlayer2_ID" <?= empty($_POST['Team2_ID']) ? 'disabled' : '' ?>>
                            <option value="">Select Player</option>
                            <?php foreach ($team2_players as $player): ?>
                                <option value="<?= $player['Player_ID'] ?>"><?= $player['Player_ID'] ?> - <?= $player['FullName'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <td><input type="date" name="TradeDate" required></td>
                    <td><button type="submit" name="new_trade" class="logout-button">Add</button></td>
                </tr>
            </tbody>
        </table>
    </form>
</body>
</html>

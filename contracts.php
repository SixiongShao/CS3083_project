<?php
session_start();
require 'connect.php';

// Fetch user role from session
$user_role = isset($_SESSION['user_role']) ? $_SESSION['user_role'] : '';

// Get Team_ID from query string
$Team_ID = isset($_GET['Team_ID']) ? htmlspecialchars($_GET['Team_ID']) : 'Unknown';

// Handle adding a new contract
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_contract']) && $user_role === 'M') {
    $Player_ID = $_POST['Player_ID'];
    $Contract_Date = $_POST['Contract_Date'];

    $insertQuery = "INSERT INTO Contracts (Team_ID, Player_ID, Contract_date) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($insertQuery);
    $stmt->bind_param("sss", $Team_ID, $Player_ID, $Contract_Date);
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Contract added successfully!";
    } else {
        $_SESSION['error_message'] = "Error adding contract: " . $conn->error;
    }
    header("Location: contracts.php?Team_ID=$Team_ID");
    exit;
}

// Fetch contracts for the team
$query = "SELECT c.Player_ID, c.Contract_Date, p.AvailabilityStatus 
          FROM Contracts c 
          JOIN Players p ON c.Player_ID = p.Player_ID 
          WHERE c.Team_ID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $Team_ID);
$stmt->execute();
$result = $stmt->get_result();
$contracts = $result->fetch_all(MYSQLI_ASSOC);

// Fetch all player names and map by Player_ID
$playerNames = [];
$playerNameQuery = "SELECT Player_ID, FullName, AvailabilityStatus FROM Players";
$playerNameResult = $conn->query($playerNameQuery);
if ($playerNameResult->num_rows > 0) {
    while ($row = $playerNameResult->fetch_assoc()) {
        $playerNames[$row['Player_ID']] = [
            'FullName' => $row['FullName'],
            'AvailabilityStatus' => $row['AvailabilityStatus']
        ];
    }
}

// Fetch players not already in the Contracts table
$availablePlayersQuery = "SELECT Player_ID, FullName, AvailabilityStatus 
                          FROM Players 
                          WHERE Player_ID NOT IN (SELECT Player_ID FROM Contracts)";
$availablePlayersResult = $conn->query($availablePlayersQuery);
$availablePlayers = [];
if ($availablePlayersResult->num_rows > 0) {
    while ($row = $availablePlayersResult->fetch_assoc()) {
        $availablePlayers[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contracts</title>
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
        .back-button, .new-button {
            text-decoration: none;
            color: white;
            background-color: #007bff;
            padding: 10px 15px;
            border-radius: 5px;
        }
        .back-button:hover, .new-button:hover {
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

    <?php if (!empty($_SESSION['success_message'])): ?>
        <p style="color: green;"><?= htmlspecialchars($_SESSION['success_message']) ?></p>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>
    <?php if (!empty($_SESSION['error_message'])): ?>
        <p style="color: red;"><?= htmlspecialchars($_SESSION['error_message']) ?></p>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th>Team ID</th>
                <th>Player ID</th>
                <th>Player Name</th>
                <th>Contract Date</th>
                <th>Availability Status</th>
            </tr>
        </thead>
        <tbody>
            <!-- Existing contracts -->
            <?php foreach ($contracts as $contract): ?>
                <tr>
                    <td><?= htmlspecialchars($Team_ID) ?></td>
                    <td><?= htmlspecialchars($contract['Player_ID']) ?></td>
                    <td><?= htmlspecialchars($playerNames[$contract['Player_ID']]['FullName'] ?? 'Unknown') ?></td>
                    <td><?= htmlspecialchars($contract['Contract_Date']) ?></td>
                    <td><?= htmlspecialchars($contract['AvailabilityStatus'] ?? 'Unknown') ?></td>
                </tr>
            <?php endforeach; ?>

            <!-- New contract form for managers only -->
            <?php if ($user_role === 'M'): ?>
            <tr>
                <form method="POST">
                    <td>New</td>
                    <td>
                        <select name="Player_ID" required>
                            <option value="">Select Player</option>
                            <?php foreach ($availablePlayers as $player): ?>
                                <option value="<?= $player['Player_ID'] ?>">
                                    <?= $player['Player_ID'] ?> - <?= htmlspecialchars($player['FullName']) ?> (<?= htmlspecialchars($player['AvailabilityStatus']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <td>-</td>
                    <td>
                        <input type="date" name="Contract_Date" required>
                        <button type="submit" name="new_contract" class="back-button">Add</button>
                    </td>
                    <td>-</td>
                </form>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>

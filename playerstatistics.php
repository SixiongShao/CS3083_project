<?php
session_start();
require 'connect.php';

// Get Player_ID from query string
$Player_ID = isset($_GET['Player_ID']) ? htmlspecialchars($_GET['Player_ID']) : '';

// Handle updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['Statistic_ID'])) {
    $Statistic_ID = $_POST['Statistic_ID'];
    $GameDate = $_POST['GameDate'];
    $PerformanceStats = $_POST['PerformanceStats'];
    $InjuryStatus = $_POST['InjuryStatus'];

    // Update query using MySQLi procedural style
    $updateQuery = "UPDATE PlayerStatistics 
                    SET GameDate = ?, 
                        PerformanceStats = ?, 
                        InjuryStatus = ? 
                    WHERE Statistic_ID = ? AND Player_ID = ?";

    if ($stmt = mysqli_prepare($conn, $updateQuery)) {
        mysqli_stmt_bind_param($stmt, "ssssi", $GameDate, $PerformanceStats, $InjuryStatus, $Statistic_ID, $Player_ID);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    } else {
        $_SESSION['error_message'] = "Error preparing the query: " . mysqli_error($conn);
    }
}

// Fetch player statistics using MySQLi
$query = "SELECT Statistic_ID, GameDate, PerformanceStats, InjuryStatus FROM PlayerStatistics WHERE Player_ID = ?";
if ($stmt = mysqli_prepare($conn, $query)) {
    mysqli_stmt_bind_param($stmt, "i", $Player_ID);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $statistics = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_stmt_close($stmt);
} else {
    $_SESSION['error_message'] = "Error preparing the fetch query: " . mysqli_error($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Player Statistics</title>
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
        .player-id {
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
        .edit-btn {
            padding: 5px 10px;
            color: white;
            background-color: #007bff;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }
        .edit-btn:hover {
            background-color: #0056b3;
        }
        .save-btn {
            background-color: #28a745;
        }
        .save-btn:hover {
            background-color: #218838;
        }
        input[type="text"], input[type="date"], select {
            width: 100%;
            padding: 5px;
        }
    </style>
</head>
<body>
    <header>
        <div class="player-id">Player ID: <?= htmlspecialchars($Player_ID) ?></div>
        <a href="player.php" class="back-button">Back to Players</a>
    </header>
    <form method="POST" id="editForm">
        <table>
            <thead>
                <tr>
                    <th>Statistics ID</th>
                    <th>Game Date</th>
                    <th>Performance Stats</th>
                    <th>Injury Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($statistics as $stat): ?>
                <tr>
                    <td><?= htmlspecialchars($stat['Statistic_ID']) ?></td>
                    <td>
                        <input type="date" name="GameDate" value="<?= htmlspecialchars($stat['GameDate']) ?>" disabled>
                    </td>
                    <td>
                        <input type="text" name="PerformanceStats" value="<?= htmlspecialchars($stat['PerformanceStats']) ?>" disabled>
                    </td>
                    <td>
                        <select name="InjuryStatus" disabled>
                            <option value="Y" <?= $stat['InjuryStatus'] === 'Y' ? 'selected' : '' ?>>Yes</option>
                            <option value="N" <?= $stat['InjuryStatus'] === 'N' ? 'selected' : '' ?>>No</option>
                        </select>
                    </td>
                    <td>
                        <button 
                            type="button" 
                            class="edit-btn" 
                            onclick="toggleEdit(this, '<?= htmlspecialchars($stat['Statistic_ID']) ?>')">
                            Edit
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </form>

    <script>
        function toggleEdit(button, statId) {
            const row = button.closest('tr');
            const inputs = row.querySelectorAll('input, select');
            const isEditing = button.textContent === "Save";

            if (isEditing) {
                // Save the updated values
                const form = document.getElementById('editForm');
                const hiddenInput1 = document.createElement('input');
                hiddenInput1.type = 'hidden';
                hiddenInput1.name = 'Statistic_ID';
                hiddenInput1.value = statId;
                form.appendChild(hiddenInput1);

                form.submit();
            } else {
                // Enable editing
                inputs.forEach(input => input.disabled = false);
                button.textContent = "Save";
                button.classList.add('save-btn');
            }
        }
    </script>
</body>
</html>

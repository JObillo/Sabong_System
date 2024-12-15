<?php
// admin.php

// Include the database connection file
include('../connection.php');

// Fetch the leaderboard data: fastest kill (shortest duration)
$leaderboardSql = "SELECT match_id, winner, winner_name, duration FROM matches WHERE duration IS NOT NULL ORDER BY duration ASC LIMIT 10";
$leaderboardResult = $conn->query($leaderboardSql);

// Check if the query was successful and if there are results
if (!$leaderboardResult) {
    die("Query failed: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cockfight Management System</title>
    <link rel="stylesheet" href="../css/admin.css">
    <style>
        .match-timer {
            font-size: 1.2em;
            color: #FF5733;
        }
        .stop-btn {
            background-color: red;
            color: white;
            padding: 5px 10px;
            border: none;
            cursor: pointer;
            font-size: 1em;
        }

        /* Leaderboard Styles */
        .leaderboard-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .leaderboard-table th, .leaderboard-table td {
            padding: 8px;
            border: 1px solid #ddd;
            text-align: center;
        }
        .leaderboard-table th {
            background-color: #f2f2f2;
        }
        .leaderboard-container {
            margin: 20px;
        }
    </style>
</head>
<body>
    <!-- Leaderboard Section -->
    <div class="leaderboard-container">
        <h2>Fastest Kills Leaderboard</h2>
        <table class="leaderboard-table">
            <tr>
                <th>No</th>
                <th>Match ID</th>
                <th>Duration</th>
                <th>Winner</th>
                <th>Winner Name</th>
            </tr>
            <?php $rank = 1; while($leaderboard = $leaderboardResult->fetch_assoc()): ?>
            <tr>
                <td><?php echo $rank++; ?></td>
                <td><?php echo $leaderboard['match_id']; ?></td>
                <td><?php echo $leaderboard['duration']; ?></td>
                <td><?php echo $leaderboard['winner']; ?></td>
                <td><?php echo $leaderboard['winner_name']; ?></td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>

</body>
</html>

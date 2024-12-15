<?php
// admin.php

// Include the database connection file
include('../connection.php');

// Fetch ongoing matches from the database (only those where end_time is NULL)
$sql = "SELECT * FROM matches WHERE end_time IS NULL";
$result = $conn->query($sql);

// Fetch the leaderboard data: fastest kill (shortest duration)
$leaderboardSql = "SELECT match_id, winner, winner_name, duration FROM matches WHERE duration IS NOT NULL ORDER BY duration ASC LIMIT 10";
$leaderboardResult = $conn->query($leaderboardSql);

// Check if the query was successful and if there are results
if (!$result) {
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

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }
        .modal-content {
            color: black;
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            width: 400px;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
        }
        .modal button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
            margin-top: 10px;
            font-size: 1.2em;
        }
        .modal .cancel-btn {
            background-color: #f44336;
        }
        .modal select {
            padding: 10px;
            font-size: 1em;
            margin: 10px 0;
            width: 100%;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        /* Leaderboard Modal */
        .leaderboard-table {
            width: 100%;
            border-collapse: collapse;
        }
        .leaderboard-table th, .leaderboard-table td {
            padding: 8px;
            border: 1px solid #ddd;
            text-align: center;
        }
        .leaderboard-table th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>Cockfight Management System</h1>
        </header>

        <!-- Match Entry Section -->
        <section class="match-entry">
            <h2>Add New Match</h2>
            <form action="process.php" method="POST">
                <div class="entry">
                    <div class="meron">
                        <label for="cock1">Meron (Red):</label>
                        <input type="text" id="cock1" name="cock1" placeholder="Enter Meron Cock Name" required>
                    </div>
                    <div class="wala">
                        <label for="cock2">Wala (Blue):</label>
                        <input type="text" id="cock2" name="cock2" placeholder="Enter Wala Cock Name" required>
                    </div>
                </div>
                <button type="submit">Start Match</button>
            </form>
        </section>

        <!-- Ongoing Matches Section -->
        <section class="status-display">
            <h2>Ongoing Matches</h2>
            <?php if ($result->num_rows == 0): ?>
                <p>No ongoing matches.</p>
            <?php else: ?>
                <?php while($match = $result->fetch_assoc()): ?>
                    <div class="match">
                        <p>Match between: <?php echo $match['meron']; ?> (Meron) vs <?php echo $match['wala']; ?> (Wala)</p>
                        <button class="stop-btn" onclick="showModal(<?php echo $match['match_id']; ?>, '<?php echo $match['meron']; ?>', '<?php echo $match['wala']; ?>')">Stop Match</button>
                    </div>
                <?php endwhile; ?>
            <?php endif; ?>
        </section>

        <!-- Leaderboard Button -->
        <button onclick="showLeaderboard()">View Leaderboard</button>

        <!-- Modal for Announcing Winner -->
        <div id="stopMatchModal" class="modal">
            <div class="modal-content">
                <h2>Announce Winner</h2>
                <p id="winnerMessage"></p>
                <form method="POST" action="process.php">
                    <input type="hidden" name="match_id" id="match_id">
                    <label for="winner">Select Winner:</label>
                    <select id="winner" name="winner" required>
                        <option value="">Select Winner</option>
                        <option value="Meron">Meron</option>
                        <option value="Wala">Wala</option>
                    </select>
                    <button type="submit" name="stop_match">Confirm Winner</button>
                    <button type="button" class="cancel-btn" onclick="closeModal()">Cancel</button>
                </form>
            </div>
        </div>

        <!-- Leaderboard Modal -->
        <div id="leaderboardModal" class="modal">
            <div class="modal-content">
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
                <button class="cancel-btn" onclick="closeLeaderboard()">Close</button>
            </div>
        </div>

    </div>

    <script>
        // Show Modal for selecting the winner
        function showModal(matchId, meron, wala) {
            const winnerMessage = `Match between ${meron} and ${wala}. Select the winner:`;
            document.getElementById('winnerMessage').innerHTML = winnerMessage;
            document.getElementById('match_id').value = matchId;
            document.getElementById('stopMatchModal').style.display = 'flex';
        }

        // Close the Modal
        function closeModal() {
            document.getElementById('stopMatchModal').style.display = 'none';
        }

        // Show Leaderboard Modal
        function showLeaderboard() {
            document.getElementById('leaderboardModal').style.display = 'flex';
        }

        // Close Leaderboard Modal
        function closeLeaderboard() {
            document.getElementById('leaderboardModal').style.display = 'none';
        }
    </script>
</body>
</html>

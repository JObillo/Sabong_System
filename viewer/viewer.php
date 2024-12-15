<?php
// viewer.php

// Include the database connection file
include('../connection.php');

// Fetch ongoing matches (where end_time is NULL)
$sql_ongoing = "SELECT * FROM matches WHERE end_time IS NULL";
$result_ongoing = $conn->query($sql_ongoing);

// Fetch completed matches (where end_time is NOT NULL)
$sql_completed = "SELECT * FROM matches WHERE end_time IS NOT NULL";
$result_completed = $conn->query($sql_completed);

// Check if the queries were successful
if (!$result_ongoing || !$result_completed) {
    die("Query failed: " . $conn->error);
}

// Prepare match data for JavaScript
$ongoing_matches = [];
while ($match = $result_ongoing->fetch_assoc()) {
    $ongoing_matches[] = $match;
}

$completed_matches = [];
while ($match = $result_completed->fetch_assoc()) {
    $completed_matches[] = $match;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Viewer - Cockfight Matchups</title>
    <link rel="stylesheet" href="../css/viewer.css">
    <style>
        /* Add styles for the leaderboard button */
        .leaderboard-btn {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 1.1em;
            margin: 10px;
        }

        .leaderboard-btn:hover {
            background-color: #0056b3;
        }

        .match-timer {
            font-size: 1.2em;
            color: #FF5733;
        }

        .match {
            margin-bottom: 20px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 8px;
            background-color: #f9f9f9;
        }

        .match p {
            font-size: 1.1em;
        }

        .outcome {
            font-size: 1.2em;
            color: #28a745; /* Green for winner */
        }

        .outcome.pending {
            color: #ffc107; /* Yellow for ongoing matches */
        }

        /* Timer Styles */
        .timer-wrapper {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .timer {
            font-weight: bold;
        }

        /* Style for different sections */
        .section {
            margin-bottom: 40px;
        }

        .section h2 {
            color: #333;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>Cockfight Matchup Viewer</h1>
            <!-- Leaderboard Button -->
            <a href="leaderboard.php" class="leaderboard-btn">Leaderboard</a>
        </header>

        <!-- Ongoing Matches Section -->
        <section class="section">
            <h2>Ongoing Matches</h2>

            <?php foreach ($ongoing_matches as $match): ?>
                <div class="match">
                    <p><strong>Match ID:</strong> <?php echo $match['match_id']; ?></p>
                    <p><strong>Meron:</strong> <?php echo $match['meron']; ?> vs <strong>Wala:</strong> <?php echo $match['wala']; ?></p>
                    <div class="timer-wrapper">
                        <div class="timer">
                            <strong>Time Elapsed:</strong> 
                            <span id="timer-<?php echo $match['match_id']; ?>">
                                <?php 
                                    // Display elapsed time or just show ongoing status
                                    echo "Ongoing";
                                ?>
                            </span>
                        </div>
                        <div class="outcome pending">
                            <?php 
                                echo "Match Ongoing"; 
                            ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </section>

        <!-- Completed Matches Section -->
        <section class="section">
            <h2>Completed Matches</h2>

            <?php foreach ($completed_matches as $match): ?>
                <div class="match">
                    <p><strong>Match ID:</strong> <?php echo $match['match_id']; ?></p>
                    <p><strong>Meron:</strong> <?php echo $match['meron']; ?> vs <strong>Wala:</strong> <?php echo $match['wala']; ?></p>
                    <div class="timer-wrapper">
                        <div class="timer">
                            <strong>Duration:</strong> 
                            <span>
                                <?php 
                                    // Fetch duration directly from the database
                                    echo $match['duration']; // assuming 'duration' column stores the time in the format "X min Y sec"
                                ?>
                            </span>
                        </div>
                        <div class="outcome">
                            <?php 
                                echo "Winner: " . $match['winner_name']; 
                            ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </section>
    </div>

    <script>
        // Function to update timers for ongoing matches
        function updateTimers() {
            const matches = <?php echo json_encode($ongoing_matches); ?>;  // Fetch ongoing matches data

            matches.forEach(function(match) {
                if (!match.end_time) { // Only process ongoing matches
                    const startTime = new Date(match.start_time).getTime(); // Match start time
                    const currentTime = new Date().getTime(); // Current time
                    let elapsedTime = Math.floor((currentTime - startTime) / 1000); // Elapsed time in seconds

                    // Format elapsed time as HH:MM:SS
                    const hours = Math.floor(elapsedTime / 3600);
                    const minutes = Math.floor((elapsedTime % 3600) / 60);
                    const seconds = elapsedTime % 60;

                    const formattedTime = 
                        (hours < 10 ? "0" : "") + hours + ":" +
                        (minutes < 10 ? "0" : "") + minutes + ":" +
                        (seconds < 10 ? "0" : "") + seconds;

                    // Update the timer element
                    const timerElement = document.getElementById(`timer-${match.match_id}`);
                    if (timerElement) {
                        timerElement.textContent = formattedTime;
                    }
                }
            });
        }

        // Update timers every second
        setInterval(updateTimers, 1000);

        // Run immediately on page load
        window.onload = updateTimers;
    </script>

</body>
</html>

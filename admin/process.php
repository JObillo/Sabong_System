<?php
// process.php

include('../connection.php');

// Start a new match
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cock1'], $_POST['cock2'])) {
    // Sanitize user input
    $cock1 = mysqli_real_escape_string($conn, $_POST['cock1']);
    $cock2 = mysqli_real_escape_string($conn, $_POST['cock2']);

    // Insert a new match into the database
    $sql = "INSERT INTO matches (meron, wala, start_time) VALUES ('$cock1', '$cock2', NOW())";

    if ($conn->query($sql) === TRUE) {
        header('Location: admin.php');
        exit;
    } else {
        echo "Error starting match: " . $conn->error;
    }
}

// Stop a match and announce winner
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['stop_match'], $_POST['winner'], $_POST['match_id'])) {
    // Get match ID and winner
    $match_id = (int) $_POST['match_id'];
    $winner = mysqli_real_escape_string($conn, $_POST['winner']);

    // Fetch match details (meron, wala, and start time)
    $sql = "SELECT meron, wala, start_time FROM matches WHERE match_id = $match_id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $match = $result->fetch_assoc();
        $winner_name = $winner === 'Meron' ? $match['meron'] : $match['wala'];

        // Update the match with the winner and duration
        $sql = "UPDATE matches 
                SET end_time = NOW(), 
                    winner = '$winner', 
                    winner_name = '$winner_name', 
                    duration = TIMEDIFF(NOW(), start_time) 
                WHERE match_id = $match_id";

        if ($conn->query($sql) === TRUE) {
            header('Location: admin.php');
            exit;
        } else {
            echo "Error stopping the match: " . $conn->error;
        }
    } else {
        echo "Match not found.";
    }
}
?>

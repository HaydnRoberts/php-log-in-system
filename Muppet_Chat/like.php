<?php
include_once "user.php";
include_once "db.php"; // Ensure the connection is included

session_start();
$logged_in = false;

if (isset($_SESSION["user"])) {
    $logged_in = true;
    $user = unserialize($_SESSION["user"]);
}

// Check if the user is logged in
if (!$logged_in) {
    header('Location: login.php');
    exit();
}

// Check if the required parameters are set
if (isset($_POST['post_id'], $_POST['user_id'], $_POST['action'])) {
    $post_id = $_POST['post_id'];
    $user_id = $_POST['user_id'];
    $action = $_POST['action'];

    $query = "SELECT * FROM likes WHERE post_id = $post_id AND user_id = $user_id";
    $mylikes = mysqli_query($connection, $query);
    $query = "SELECT * FROM likes WHERE post_id = $post_id AND likes = '1'";
    $totallikes = mysqli_query($connection, $query);
    $query = "SELECT * FROM likes WHERE post_id = $post_id AND dislikes = '1'";
    $totaldislikes = mysqli_query($connection, $query);

    $alllikes = mysqli_num_rows($totallikes);
    $alldislikes = mysqli_num_rows($totaldislikes);

    if (mysqli_num_rows($mylikes) == 1) {
        $row = mysqli_fetch_assoc($mylikes);
        $likes = $row['likes'];
        $dislikes = $row['dislikes'];
        $row_exists = 1;
        // Handle like or dislike based on the action
        if ($action == 'like') {
            if ($likes == 1) {
                $likes = 0;
            } else {
                $likes = 1;
                if ($dislikes == 1) {
                    $dislikes = 0;
                }
            }
        } elseif ($action == 'dislike') {
            if ($dislikes == 1) {
                $dislikes = 0;
            } else {
                $dislikes = 1;
                if ($likes == 1) {
                    $likes = 0;
                }
            }
        }
    } else {
        $row_exists = 0;
        if ($action == 'like') {
            $likes = 1;
            $dislikes = 0;
        } else {
            $likes = 0;
            $dislikes = 1;
        }
        
    }
    // Update or insert the like in the database
    if ($row_exists == 1) {
        $query = "UPDATE `likes` SET likes = $likes, dislikes = $dislikes WHERE user_id = $user_id AND post_id = $post_id";
    } elseif ($row_exists == 0) {
        $query = "INSERT INTO `likes` (post_id, user_id, likes, dislikes) VALUES ($post_id, $user_id, $likes, $dislikes)";
    }

    mysqli_query($connection, $query);

    // Fetch the updated like count for the post
    $updatedLikesQuery = "SELECT * FROM likes WHERE post_id = $post_id AND likes = '1'";
    $updatedLikesResult = mysqli_query($connection, $updatedLikesQuery);
    $updatedLikesCount = mysqli_num_rows($updatedLikesResult);

    // Send the updated like count as a response
    echo json_encode(['success' => true, 'likesCount' => $updatedLikesCount]);
    exit();
}

// If the request is missing required parameters, return an error response
echo json_encode(['success' => false, 'message' => 'Invalid request']);
exit();
?>

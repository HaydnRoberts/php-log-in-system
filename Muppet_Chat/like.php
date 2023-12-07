<?php
include_once "user.php";

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

if (isset($_POST['like_btn']) || isset($_POST['dislike_btn'])) {
    $post_id = $_POST['post_id'];
    $email = $user->email;
    $query = "SELECT id FROM users WHERE email = '$email'";
    $user_result = mysqli_query($connection, $query);
    $user_data = mysqli_fetch_assoc($user_result);
    $user_id = $user_data['id'];
    $likes = $_POST['likes'];
    $dislikes = $_POST['dislikes'];
    $row_exists = $_POST['row_exists'];

    if (isset($_POST['like_btn'])) {
        if ($likes == 1) {
            $likes = 0;
        } else {
            $likes = 1;
            if ($dislikes == 1) {
                $dislikes = 0;
            }
        }
    } else {
        if ($dislikes == 1) {
            $dislikes = 0;
        } else {
            $dislikes = 1;
            if ($likes == 1) {
                $likes = 0;
            }
        }
    }
    if ($row_exists == 1){
        $query = "UPDATE `likes` SET likes = $likes, dislikes = $dislikes WHERE user_id = $user_id AND post_id = $post_id";
    } elseif ($row_exists == 0) {
        $query = "INSERT INTO `likes` (post_id, user_id, likes, dislikes) VALUES ($post_id, $user_id, $likes, $dislikes)";
    }
    mysqli_query($connection, $query);
}



header("Location: index.php");
?>
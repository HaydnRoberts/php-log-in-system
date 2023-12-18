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

if (isset($_POST['update_ping']) && $_POST['update_ping'] == 'true') {
    $replyquery = "SELECT * FROM reply WHERE ping_owner = 1";
    $ping_replys = mysqli_query($connection, $replyquery);

    $selectquery = "SELECT * FROM posts WHERE post_owner_id LIKE '{$user->email}'";
    $owners_post = mysqli_query($connection, $selectquery);

    while ($post_ids = mysqli_fetch_assoc($ping_replys)) {
        mysqli_data_seek($owners_post, 0);
        while ($data = mysqli_fetch_assoc($owners_post)) {
            if ($data['id'] == $post_ids["post_id"]) {
                // Update ping_owner to 0 for the matching posts
                $updateQuery = "UPDATE `reply` SET `ping_owner` = '0' WHERE `post_id` = {$data['id']}";
                mysqli_query($connection, $updateQuery);
            }
        }
    }
}
?>

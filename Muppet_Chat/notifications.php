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

include_once "notification_action.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <p>You have new notifications on posts</p>
    <?php 
    /*$count = count($ping_posts);
    $counter = 0;
    for($count;){
        echo $ping_posts[$counter]; 
        $counter += 1;
    }
    */
?>
</body>
</html>
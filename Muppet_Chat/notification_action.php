<?php
/*
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

$query = "SELECT * FROM reply WHERE ping_owner = 1";
$ping_replys = mysqli_query($connection, $query);

$selectquery = "SELECT * FROM posts WHERE post_owner_id LIKE '{$user->email}'";
$owners_post = mysqli_query($connection, $selectquery);

$post_ids = [];
$owners_post_ids = [];

while ($reply_data = mysqli_fetch_assoc($ping_replys)) {
    $post_ids[] = $reply_data['post_id'];
}

while ($post_data = mysqli_fetch_assoc($owners_post)) {
    $owners_post_ids[] = $post_data['id'];
}

$ping_posts = [];
foreach ($owners_post_ids as $owners_post_id) {
    foreach ($post_ids as $post_id) {
        if ($post_id == $owners_post_id){
            $ping_posts[] = $post_id; 
        }
    }
}
var_dump($ping_posts);
return $ping_posts;
*/
include_once "user.php";
// Check if the user is logged in
if (!$logged_in) {
    $ping_posts = [];
    return $ping_posts;
} else {
    $query = "SELECT * FROM reply WHERE ping_owner = 1";
    $ping_replys = mysqli_query($connection, $query);

    $selectquery = "SELECT * FROM posts WHERE post_owner_id LIKE '{$user->email}'";
    $owners_post = mysqli_query($connection, $selectquery);

    $post_ids = array_column(mysqli_fetch_all($ping_replys, MYSQLI_ASSOC), 'post_id');
    $owners_post_ids = array_column(mysqli_fetch_all($owners_post, MYSQLI_ASSOC), 'id');

    $ping_posts = array_intersect($owners_post_ids, $post_ids);

    return $ping_posts;
}
?>
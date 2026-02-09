<?php

include_once "user.php";
// Check if the user is logged in
if (!$logged_in) {
    $ping_posts = [];
} else {
    $query = "SELECT * FROM reply WHERE ping_owner = 1";
    $ping_replys = mysqli_query($connection, $query);

    $selectquery = "SELECT * FROM posts WHERE post_owner_id LIKE '{$user->email}'";
    $owners_post = mysqli_query($connection, $selectquery);

    $post_ids = array_column(mysqli_fetch_all($ping_replys, MYSQLI_ASSOC), 'post_id');
    $owners_post_ids = array_column(mysqli_fetch_all($owners_post, MYSQLI_ASSOC), 'id');

    $ping_posts = array_intersect($owners_post_ids, $post_ids);
}
?>
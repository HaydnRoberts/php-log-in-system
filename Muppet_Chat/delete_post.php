<?php

include_once "user.php";
$id = $_GET['id'];
$query = "DELETE FROM posts WHERE id = $id";
$stmt = $connection->prepare($query);
// Execute the statement
$stmt->execute();

header("Location: my_posts.php");
exit();
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

// Get the post content and image
$post_content = $_POST['post_content'];
$post_image = $_FILES["post_image"]["name"];
$tempname = $_FILES["post_image"]["tmp_name"];

// Generate a unique name for the uploaded image
$extension = pathinfo($post_image, PATHINFO_EXTENSION);
$new_image_name = uniqid() . '.' . $extension;
$folder = "./image/" . $new_image_name;

// Now let's move the uploaded image into the folder: image
if (move_uploaded_file($tempname, $folder)) {
    echo "<h3>  Image uploaded successfully!</h3>";
} else {
    echo "<h3>  Failed to upload image!</h3>";
}

// Prepare the SQL statement
if ($post_image === null) {
    $query = "INSERT INTO `posts` (`post_owner_id`, `post_content`) VALUES (?, ?)";
    $stmt = $connection->prepare($query);
    $stmt->bind_param("ss", $user->email, $post_content);
} else {
    $query = "INSERT INTO `posts` (`post_owner_id`, `post_content`, `post_image`) VALUES (?, ?, ?)";
    $stmt = $connection->prepare($query);
    $stmt->bind_param("sss", $user->email, $post_content, $new_image_name);
}
// Execute the statement
$stmt->execute();

// Redirect to the home page
header('Location: index.php');
exit();

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
/*if (isset($_FILES["post_image"])) {
    $image = $_FILES['post_image']['tmp_name'];
    $image_type = $_FILES['post_image']['type'];
    if ($image_type == 'image/jpeg') {
        $post_image = fopen($image, 'rb');
    } else {
        $image = imagecreatefromstring(file_get_contents($image));
        ob_start();
        imagejpeg($image);
        $post_image = ob_get_clean();
    }
} else {
    $post_image = null;
}
*/
$post_image = $_FILES["post_image"]["name"];
$tempname = $_FILES["post_image"]["tmp_name"];
$folder = "./image/" . $post_image;

// Now let's move the uploaded image into the folder: image
if (move_uploaded_file($tempname, $folder)) {
    echo "<h3>  Image uploaded successfully!</h3>";
} else {
    echo "<h3>  Failed to upload image!</h3>";
}

// Prepare the SQL statement
if ($post_image === null) {
    $query = "INSERT INTO `posts` (`post_owner_id`, `post_content`) VALUES ('{$user->email}', '{$post_content}')";
} else {
    $query = "INSERT INTO `posts` (`post_owner_id`, `post_content`, `post_image`) VALUES ('{$user->email}', '{$post_content}', '{$post_image}')";
}
$stmt = $connection->prepare($query);
// Execute the statement
$stmt->execute();

// Redirect to the home page
header('Location: index.php');
exit();
?>

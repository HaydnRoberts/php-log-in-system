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

// Check if the required parameters are set
if (isset($_POST['post_id'], $_POST['user_id'], $_POST['new_content'])) {
    $post_id = $_POST['post_id'];
    $user_id = $_POST['user_id'];
    $new_content = $_POST['new_content'];

    print("post id: " . $post_id . " user id: " . $user_id . " new content: " . $new_content);
    // Update or insert the like in the database
    
    $query = "INSERT INTO `reply` (post_id, user_id, content) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($stmt, 'iis', $post_id, $user_id, $new_content);
    mysqli_stmt_execute($stmt);


    echo json_encode(['success' => true]);
    exit();
}

// If the request is missing required parameters, return an error response
echo json_encode(['success' => false, 'message' => 'Invalid request']);
exit();
?>

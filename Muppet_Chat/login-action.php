<?php

include_once "user.php";

if ( ! isset($_POST["email"])) {
    header("location /");
    exit();
}

$user = new User($connection, $_POST["email"], $_POST["password"]);

$user->authenticate();

if ($user->is_logged_in()) {
    session_start();
    $_SESSION["user"] = serialize($user);
    header("Location: index.php");
} else {
    echo "Could not log in with these credentials";
    echo '<a href="login.php" class="btn">Try again</a>';
}
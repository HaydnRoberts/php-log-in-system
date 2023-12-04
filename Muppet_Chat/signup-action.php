<?php

include_once "user.php";

if ( ! isset($_POST["email"])) {
    header("location /");
    exit();
}

$user = new User($connection, $_POST["email"], $_POST["password"]);
$user->insert();
header("Location: index.php");
exit();


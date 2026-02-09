<?php
include_once "user.php";

if (
    empty($_POST["email"]) ||
    empty($_POST["password"])
) {
    header("Location: signup.php?error=missing_fields");
    exit();
}

$email = trim($_POST["email"]);
$password = $_POST["password"];

$user = new User($connection, $email, $password);

if (!$user->insert()) {
    header("Location: signup.php?error=email_exists");
    exit();
}

header("Location: index.php");
exit();

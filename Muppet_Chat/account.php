<?php

include_once "user.php";

session_start();
$logged_in = false;
if (isset($_SESSION["user"])) {
	$logged_in = true;
	$user = unserialize($_SESSION["user"]);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Muppet Chat</title>
    <link href='style.css' rel='stylesheet'>
</head>
<body>

	<?php 
	include_once "notification_action.php";
	if ($ping_posts){
		$count = count($ping_posts);
	} else{
		$count = 0;
	}
	include_once "db.php";
    nav($count);
    ?>

	<h1>Account</h1>
	<?php if ($logged_in): ?>
		<p> <?= htmlspecialchars($user->email); ?> </p>

		<div class="container">
		<p>Terms of Service</p>
        <iframe src='ToS.txt' width='800' height='400' frameBorder='0' style="padding-bottom: 20px;"></iframe>
            
			<a href="logout.php" class="btn">Log out</a>
		</div>	
	<?php else: ?>
		<div class="container">
			<p>Join the fun and chat with your favorite Muppets!</p>
			<a href="signup.php" class="btn">Sign Up</a>
			<a href="login.php" class="btn">Log In</a>
		</div>
		
	<?php endif; ?>
	
</body>
</html>

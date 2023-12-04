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

	<?php include_once "db.php"; 
	nav();
	?>

	<h1>Welcome to Muppet Chat!</h1>
	<?php if ($logged_in): ?>
		<p> Welcome <?= $user->email; ?> </p>
		<div class="container">
		<?php
			$selectquery = " select * from posts ";
			$result = mysqli_query($connection, $selectquery);

			while ($data = mysqli_fetch_assoc($result)) {
		?>
			<div class="card">
				<img src="./image/<?php echo $data['post_image']; ?>" class="card_image">
				<div class="card_container">
					<h4><b><?php echo $data['post_owner_id'];?></b></h4>
					<?php echo $data['post_content'];?>
				</div>
			</div>
			<br>

		<?php
			}
		?>
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

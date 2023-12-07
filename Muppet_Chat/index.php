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
		<p> Welcome <?= htmlspecialchars($user->email); ?> </p>
<div class="container">
    <?php
        $selectquery = " select * from posts ORDER BY date DESC";
        $result = mysqli_query($connection, $selectquery);

        while ($data = mysqli_fetch_assoc($result)) {
            $post_id = $data['id'];
            $email = $user->email;
            $query = "SELECT id FROM users WHERE email = '$email'";
            $user_result = mysqli_query($connection, $query);
            $user_data = mysqli_fetch_assoc($user_result);
            $user_id = $user_data['id'];
            $likes = 0;
            $dislikes = 0;
			

            $query = "SELECT * FROM likes WHERE post_id = $post_id AND user_id = $user_id";
            $mylikes = mysqli_query($connection, $query);
			$query = "SELECT * FROM likes WHERE post_id = $post_id AND likes = '1' ";
            $totallikes = mysqli_query($connection, $query);
			$query = "SELECT * FROM likes WHERE post_id = $post_id AND dislikes = '1' ";
            $totaldislikes = mysqli_query($connection, $query);
			
			$alllikes = mysqli_num_rows($totallikes);
			$alldislikes = mysqli_num_rows($totaldislikes);
            if (mysqli_num_rows($mylikes) == 1) {
                $row = mysqli_fetch_assoc($mylikes);
                $likes = $row['likes'];
                $dislikes = $row['dislikes'];
				$row_exists = 1;
            } else {
				$row_exists = 0;
			}
    ?>
        <div class="card">
            <img src="./image/<?php echo $data['post_image']; ?>" class="card_image">
            <div class="card_container">
                <h4><b><?php echo htmlspecialchars($data['post_owner_id']);?></b></h4>
                <p style="overflow: auto; height: 300px; text-align"><?php echo htmlspecialchars($data['post_content']);?></p>    
            </div>
			<form method="post" action="like.php">
                    <input type="hidden" name="post_id" value="<?php echo $post_id; ?>">
                    <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
					<input type="hidden" name="likes" value="<?php echo $likes; ?>">
                    <input type="hidden" name="dislikes" value="<?php echo $dislikes; ?>">
					<input type="hidden" name="row_exists" value="<?php echo $row_exists; ?>">
                    <button type="submit" name="like_btn"><img src="..\icons\like.png"><?php echo $alllikes; ?></button>
                    <button type="submit" name="dislike_btn"><img src="..\icons\dislike.png"><?php echo $alldislikes; ?></button>
                </form>
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

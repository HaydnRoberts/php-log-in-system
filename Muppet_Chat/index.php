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
    <?php if ($logged_in) : ?>
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
                        <h4><b><?php echo htmlspecialchars($data['post_owner_id']); ?></b></h4>
                        <p style="overflow: auto; height: 300px; text-align"><?php echo htmlspecialchars($data['post_content']); ?></p>
                    </div>
					<div class="like-dislike-container">
						<button class="like-btn" data-postid="<?php echo $post_id; ?>">
						<?php if($row_exists==1 && $row['likes']==1): ?>
							<img src="..\icons\like_clicked.png"><?php echo $alllikes; ?></span>
						<?php else: ?>
							<img src="..\icons\like.png"><?php echo $alllikes; ?></span>
						<?php endif; ?>
						</button>
						<button class="dislike-btn" data-postid="<?php echo $post_id; ?>">
						<?php if($row_exists==1 && $row['dislikes']==1): ?>
							<img src="..\icons\dislike_clicked.png"><?php echo $alldislikes; ?></span>
						<?php else: ?>
							<img src="..\icons\dislike.png"><?php echo $alldislikes; ?></span>
						<?php endif; ?>
						</button>
					</div>
                </div>
                <br>
            <?php
            }
            ?>
            <script>
			document.addEventListener("DOMContentLoaded", function () {
				document.querySelectorAll('.like-btn, .dislike-btn').forEach(function (button) {
					button.addEventListener('click', function () {
						handleLikeDislike(this);
					});
				});

				function handleLikeDislike(button) {
					var post_id = button.getAttribute('data-postid');
					var user_id = <?php echo $user_id; ?>;

					var isLike = button.classList.contains('like-btn');
					var isDislike = button.classList.contains('dislike-btn');

					var formData = new FormData();
					formData.append('post_id', post_id);
					formData.append('user_id', user_id);

					if (isLike) {
						formData.append('action', 'like');
					} else if (isDislike) {
						formData.append('action', 'dislike');
					}

					var xhr = new XMLHttpRequest();
					xhr.open('POST', 'like.php', true);
					xhr.onload = function () {
						// Handle the response if needed
					};
					xhr.send(formData);
					location.reload();
				}
			});
		</script>

        </div>
    <?php else : ?>
        <div class="container">
            <p>Join the fun and chat with your favorite Muppets!</p>
            <a href="signup.php" class="btn">Sign Up</a>
            <a href="login.php" class="btn">Log In</a>
        </div>

    <?php endif; ?>

</body>

</html>

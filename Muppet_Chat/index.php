<?php
include_once "user.php";
// this is the session start on every page, this determines who the user is and if they are logged in
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
	<script>
		// this code was to click on a card to scroll to it, it is a cool feature but it will be turned into an opt in feature instead as is not very useful for most users
		/* document.addEventListener("DOMContentLoaded", function () {
		document.querySelectorAll('.card').forEach(function (card) {
			card.addEventListener('click', function () {
				this.scrollIntoView({ behavior: 'smooth', block: 'start' });
			});
		});
	});
	*/
	</script>
</head>

<body>

    <?php 
	include_once "notification_action.php";
	if (!$ping_posts == null){
		$count = count($ping_posts);
	} else{
		$count = 0;
	}
	include_once "db.php";
    nav($count);
    ?>

    <h1>Welcome to Muppet Chat!</h1>
    <?php if ($logged_in) : ?>
		<div class="container">
		<?php
		$selectquery = "SELECT * FROM posts ORDER BY date DESC";
		$result = mysqli_query($connection, $selectquery);

		$email = $user->email;
		$user_query = "SELECT id FROM users WHERE email = '$email'";
		$user_result = mysqli_query($connection, $user_query);
		$user_data = mysqli_fetch_assoc($user_result);
		$user_id = $user_data['id'];

		while ($data = mysqli_fetch_assoc($result)):
		$post_id = $data['id'];

		$mylikes = mysqli_query($connection, "SELECT * FROM likes WHERE post_id=$post_id AND user_id=$user_id");
		$alllikes = mysqli_num_rows(mysqli_query($connection, "SELECT * FROM likes WHERE post_id=$post_id AND likes=1"));
		$alldislikes = mysqli_num_rows(mysqli_query($connection, "SELECT * FROM likes WHERE post_id=$post_id AND dislikes=1"));

		$row_exists = mysqli_num_rows($mylikes) === 1;
		$row = $row_exists ? mysqli_fetch_assoc($mylikes) : null;

		$replys = mysqli_query($connection, "SELECT * FROM reply WHERE post_id=$post_id");
		$reply_count = mysqli_num_rows($replys);
		?>
		<article class="post">
		<header class="post-header">
			<div class="avatar"></div>
			<span class="username"><?= htmlspecialchars($data['post_owner_id']) ?></span>
		</header>

		<p class="post-content"><?= htmlspecialchars($data['post_content']) ?></p>

		<?php if (!empty($data['post_image'])): ?>
			<img class="post-image" src="./image/<?= $data['post_image'] ?>">
		<?php endif; ?>

		<footer class="post-actions">
			<button class="action <?= ($row_exists && $row['likes']) ? 'liked' : '' ?>"
					onclick="handleLike(<?= $post_id ?>)">
			❤️ <?= $alllikes ?>
			</button>

			<button class="action" onclick="toggleReply(<?= $post_id ?>)">
			💬 <?= $reply_count ?>
			</button>

			<button class="action" onclick="handleDislike(<?= $post_id ?>)">
			👎 <?= $alldislikes ?>
			</button>
		</footer>

		<div class="reply-card" id="reply-<?= $post_id ?>">
			<?php if ($reply_count): ?>
			<?php while ($reply = mysqli_fetch_assoc($replys)): ?>
				<p><?= htmlspecialchars($reply['content']) ?></p>
			<?php endwhile; ?>
			<?php else: ?>
			<p>No replies yet</p>
			<?php endif; ?>

			<form class="replyForm" onsubmit="submitReply(event, <?= $post_id ?>)">
			<textarea></textarea>
			<button type="submit">Reply</button>
			</form>
		</div>
		</article>
		<?php endwhile; ?>
		</div>


		<script>
function toggleReply(id) {
  document.getElementById("reply-" + id).classList.toggle("open");
}

function handleLike(postId) {
  sendAction(postId, 'like');
}

function handleDislike(postId) {
  sendAction(postId, 'dislike');
}

function sendAction(postId, action) {
  const formData = new FormData();
  formData.append('post_id', postId);
  formData.append('user_id', <?= $user_id ?>);
  formData.append('action', action);

  fetch('like.php', { method: 'POST', body: formData })
    .then(() => location.reload());
}

function submitReply(e, postId) {
  e.preventDefault();
  const textarea = e.target.querySelector('textarea');

  const formData = new FormData();
  formData.append('post_id', postId);
  formData.append('user_id', <?= $user_id ?>);
  formData.append('new_content', textarea.value);

  fetch('reply.php', { method: 'POST', body: formData })
    .then(() => location.reload());
}
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

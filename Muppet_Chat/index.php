<?php
include_once "user.php";
session_start();

$logged_in = false;
if (isset($_SESSION["user"])) {
    $logged_in = true;
    $user = unserialize($_SESSION["user"]);
}

include_once "db.php";
include_once "notification_action.php";

$count = is_array($ping_posts ?? null) ? count($ping_posts) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Muppet Chat</title>
<link rel="stylesheet" href="style.css">
</head>

<body>

<?php nav($count); ?>

<!-- Ambient packet background -->
<div class="packet-layer">
	<div class="packet">IPv4 Src 192.168.12.1 → Dst 192.168.12.2 TTL 255</div>
	<div class="packet">Ethernet II aa:bb:cc:00:01:10 → aa:bb:cc:00:02:10</div>
	<div class="packet">ICMP Echo Request • Frame 114 bytes • eth1</div>
	<div class="packet">Total Length: 100 • Flags: 0x0 • Fragment Offset: 0</div>
	<div class="packet">Protocol: ICMP (1) • Header Checksum: 0x2245</div>
</div>

<main class="feed">

<h1 class="page-title">Welcome to Muppet Chat</h1>

<?php if ($logged_in): ?>

<?php
$email = $user->email;
$user_query = mysqli_query($connection, "SELECT id FROM users WHERE email='$email'");
$user_id = mysqli_fetch_assoc($user_query)['id'];

$posts = mysqli_query($connection, "SELECT * FROM posts ORDER BY date DESC");
while ($post = mysqli_fetch_assoc($posts)):

$post_id = $post['id'];

/* user reaction */
$mylike = mysqli_query(
    $connection,
    "SELECT * FROM likes WHERE post_id=$post_id AND user_id=$user_id"
);
$user_reaction = mysqli_num_rows($mylike) === 1
    ? mysqli_fetch_assoc($mylike)
    : null;

/* totals */
$likes = mysqli_num_rows(
    mysqli_query($connection, "SELECT * FROM likes WHERE post_id=$post_id AND likes=1")
);
$dislikes = mysqli_num_rows(
    mysqli_query($connection, "SELECT * FROM likes WHERE post_id=$post_id AND dislikes=1")
);

/* replies */
$replies = mysqli_query(
    $connection,
    "SELECT reply.*, users.email 
     FROM reply 
     JOIN users ON reply.user_id = users.id 
     WHERE post_id=$post_id"
);
$reply_count = mysqli_num_rows($replies);
?>

<article class="post crt">

<header class="post-header">
	<div class="avatar"></div>
	<span class="username"><?= htmlspecialchars($post['post_owner_id']) ?></span>
</header>

<p class="post-text"><?= htmlspecialchars($post['post_content']) ?></p>

<?php if (!empty($post['post_image'])): ?>
	<img class="post-image" src="./image/<?= htmlspecialchars($post['post_image']) ?>">
<?php endif; ?>

<footer class="post-actions">

<button class="icon-btn <?= ($user_reaction && $user_reaction['likes']) ? 'active' : '' ?>"
        onclick="react(<?= $post_id ?>,'like')">
    <img 
        src="../icons/<?= ($user_reaction && $user_reaction['likes']) ? 'like_clicked.png' : 'like.png' ?>" 
        alt="Like"
    >
    <span><?= $likes ?></span>
</button>

<button class="icon-btn" onclick="toggleReply(<?= $post_id ?>)">
    <img src="../icons/icons8-message-48.png" alt="Reply">
    <span><?= $reply_count ?></span>
</button>

<button class="icon-btn <?= ($user_reaction && $user_reaction['dislikes']) ? 'active' : '' ?>"
        onclick="react(<?= $post_id ?>,'dislike')">
    <img 
        src="../icons/<?= ($user_reaction && $user_reaction['dislikes']) ? 'dislike_clicked.png' : 'dislike.png' ?>" 
        alt="Dislike"
    >
    <span><?= $dislikes ?></span>
</button>

</footer>

<section class="reply-panel" id="reply-<?= $post_id ?>">

<?php if ($reply_count): ?>
	<?php while ($r = mysqli_fetch_assoc($replies)): ?>
		<div class="reply">
			<span class="reply-user"><?= htmlspecialchars($r['email']) ?></span>
			<p><?= htmlspecialchars($r['content']) ?></p>
		</div>
	<?php endwhile; ?>
<?php else: ?>
	<p class="no-replies">No replies yet</p>
<?php endif; ?>

<form class="reply-form" onsubmit="sendReply(event,<?= $post_id ?>)">
	<textarea placeholder="Add a reply…"></textarea>
	<button type="submit">Reply</button>
</form>

</section>
</article>

<?php endwhile; ?>

<?php else: ?>

<div class="guest">
	<p>Join the fun and chat with your favourite muppets</p>
	<a href="signup.php" class="btn">Sign Up</a>
	<a href="login.php" class="btn">Log In</a>
</div>

<?php endif; ?>

</main>

<script>
function toggleReply(id){
	document.getElementById('reply-'+id).classList.toggle('open');
}

function react(post, action){
	const fd = new FormData();
	fd.append('post_id', post);
	fd.append('user_id', <?= $user_id ?? 0 ?>);
	fd.append('action', action);
	fetch('like.php', { method:'POST', body:fd })
		.then(() => location.reload());
}

function sendReply(e, id){
	e.preventDefault();
	const textarea = e.target.querySelector('textarea');
	if (!textarea.value.trim()) return;

	const fd = new FormData();
	fd.append('post_id', id);
	fd.append('user_id', <?= $user_id ?? 0 ?>);
	fd.append('new_content', textarea.value);

	fetch('reply.php', { method:'POST', body:fd })
		.then(() => location.reload());
}
</script>

</body>
</html>

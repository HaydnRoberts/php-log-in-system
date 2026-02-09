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
    <title>Muppet Chat</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
</head>

<body>

<?php
include_once "notification_action.php";
include_once "db.php";
$count = is_array($ping_posts) ? count($ping_posts) : 0;
nav($count);
?>

<main class="feed">

<h1 class="page-title">Welcome to Muppet Chat</h1>

<?php if ($logged_in): ?>

<?php
$email = $user->email;
$user_query = mysqli_query($connection, "SELECT id FROM users WHERE email='$email'");
$user_id = mysqli_fetch_assoc($user_query)['id'];

$posts = mysqli_query($connection, "SELECT * FROM posts ORDER BY date DESC");
?>

<?php while ($post = mysqli_fetch_assoc($posts)): ?>
<?php
$post_id = $post['id'];

$mylike_q = mysqli_query($connection, "SELECT * FROM likes WHERE post_id=$post_id AND user_id=$user_id");
$my_like = mysqli_num_rows($mylike_q) === 1 ? mysqli_fetch_assoc($mylike_q) : null;

$likes = mysqli_num_rows(mysqli_query($connection, "SELECT id FROM likes WHERE post_id=$post_id AND likes=1"));
$dislikes = mysqli_num_rows(mysqli_query($connection, "SELECT id FROM likes WHERE post_id=$post_id AND dislikes=1"));

$replies = mysqli_query($connection, "SELECT r.*, u.email FROM reply r JOIN users u ON r.user_id=u.id WHERE r.post_id=$post_id");
$reply_count = mysqli_num_rows($replies);
?>

<article class="post-card">

<header class="post-header">
    <div class="avatar"></div>
    <span class="username"><?= htmlspecialchars($post['post_owner_id']) ?></span>
</header>

<p class="post-text"><?= htmlspecialchars($post['post_content']) ?></p>

<?php if (!empty($post['post_image'])): ?>
<img class="post-image" src="./image/<?= htmlspecialchars($post['post_image']) ?>">
<?php endif; ?>

<footer class="post-actions">
    <button class="icon-btn <?= ($my_like && $my_like['likes']) ? 'active' : '' ?>"
        onclick="react(<?= $post_id ?>, 'like')">
        <img src="icons/like.png">
        <span><?= $likes ?></span>
    </button>

    <button class="icon-btn" onclick="toggleReplies(<?= $post_id ?>)">
        <img src="icons/icons8-message-48.png">
        <span><?= $reply_count ?></span>
    </button>

    <button class="icon-btn <?= ($my_like && $my_like['dislikes']) ? 'active' : '' ?>"
        onclick="react(<?= $post_id ?>, 'dislike')">
        <img src="icons/dislike.png">
        <span><?= $dislikes ?></span>
    </button>
</footer>

<section class="replies" id="replies-<?= $post_id ?>">
<?php if ($reply_count): ?>
<?php while ($reply = mysqli_fetch_assoc($replies)): ?>
<div class="reply">
    <span class="reply-user"><?= htmlspecialchars($reply['email']) ?></span>
    <p><?= htmlspecialchars($reply['content']) ?></p>
</div>
<?php endwhile; ?>
<?php else: ?>
<p class="no-replies">No replies yet</p>
<?php endif; ?>

<form class="reply-form" onsubmit="submitReply(event, <?= $post_id ?>)">
    <textarea placeholder="Add a reply…"></textarea>
    <button type="submit">Reply</button>
</form>
</section>

</article>

<?php endwhile; ?>

<?php else: ?>

<div class="logged-out">
    <p>Join the fun and chat with your favorite Muppets!</p>
    <a href="signup.php" class="btn">Sign Up</a>
    <a href="login.php" class="btn">Log In</a>
</div>

<?php endif; ?>

</main>

<script>
function toggleReplies(id) {
    document.getElementById('replies-' + id).classList.toggle('open');
}

function react(postId, action) {
    const fd = new FormData();
    fd.append('post_id', postId);
    fd.append('user_id', <?= $user_id ?? 0 ?>);
    fd.append('action', action);

    fetch('like.php', { method: 'POST', body: fd })
        .then(() => location.reload());
}

function submitReply(e, postId) {
    e.preventDefault();
    const text = e.target.querySelector('textarea').value;

    const fd = new FormData();
    fd.append('post_id', postId);
    fd.append('user_id', <?= $user_id ?? 0 ?>);
    fd.append('new_content', text);

    fetch('reply.php', { method: 'POST', body: fd })
        .then(() => location.reload());
}
</script>

</body>
</html>

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

<!-- Ambient background -->
<div class="packet-layer"></div>

<?php nav($count); ?>

<main class="feed">

<h1 class="page-title">Welcome to Muppet Chat</h1>

<?php if ($logged_in): ?>

<?php
$email = $user->email;
$user_query = "SELECT id FROM users WHERE email='$email'";
$user_result = mysqli_query($connection, $user_query);
$user_id = mysqli_fetch_assoc($user_result)['id'];

$posts = mysqli_query($connection, "SELECT * FROM posts ORDER BY date DESC");
while ($post = mysqli_fetch_assoc($posts)):

$post_id = $post['id'];

$mylike = mysqli_query($connection, "SELECT * FROM likes WHERE post_id=$post_id AND user_id=$user_id");
$liked = mysqli_num_rows($mylike) === 1 ? mysqli_fetch_assoc($mylike) : null;

$likes = mysqli_num_rows(mysqli_query($connection, "SELECT * FROM likes WHERE post_id=$post_id AND likes=1"));
$dislikes = mysqli_num_rows(mysqli_query($connection, "SELECT * FROM likes WHERE post_id=$post_id AND dislikes=1"));

$replies = mysqli_query($connection, "SELECT reply.*, users.email FROM reply JOIN users ON reply.user_id = users.id WHERE post_id=$post_id");
$reply_count = mysqli_num_rows($replies);
?>

<article class="post crt">
  <header class="post-header">
    <div class="avatar"></div>
    <span class="username"><?= htmlspecialchars($post['post_owner_id']) ?></span>
  </header>

  <p class="post-text"><?= htmlspecialchars($post['post_content']) ?></p>

  <?php if (!empty($post['post_image'])): ?>
    <img class="post-image" src="./image/<?= $post['post_image'] ?>">
  <?php endif; ?>

  <footer class="post-actions">
    <button class="icon-btn <?= ($liked && $liked['likes']) ? 'active' : '' ?>" onclick="react(<?= $post_id ?>,'like')">
      <img src="../icons/icons8-like-50.png"> <?= $likes ?>
    </button>

    <button class="icon-btn" onclick="toggleReply(<?= $post_id ?>)">
      <img src="../icons/icons8-reply-50.png"> <?= $reply_count ?>
    </button>

    <button class="icon-btn <?= ($liked && $liked['dislikes']) ? 'active' : '' ?>" onclick="react(<?= $post_id ?>,'dislike')">
      <img src="../icons/icons8-dislike-50.png"> <?= $dislikes ?>
    </button>
  </footer>

  <section class="reply-panel" id="reply-<?= $post_id ?>">
    <?php if ($reply_count): while ($r = mysqli_fetch_assoc($replies)): ?>
      <div class="reply">
        <span class="reply-user"><?= htmlspecialchars($r['email']) ?></span>
        <p><?= htmlspecialchars($r['content']) ?></p>
      </div>
    <?php endwhile; else: ?>
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
  fd.append('post_id',post);
  fd.append('user_id',<?= $user_id ?? 0 ?>);
  fd.append('action',action);
  fetch('like.php',{method:'POST',body:fd}).then(()=>location.reload());
}

function sendReply(e,id){
  e.preventDefault();
  const t=e.target.querySelector('textarea');
  const fd=new FormData();
  fd.append('post_id',id);
  fd.append('user_id',<?= $user_id ?? 0 ?>);
  fd.append('new_content',t.value);
  fetch('reply.php',{method:'POST',body:fd}).then(()=>location.reload());
}
</script>

<div class="packet-layer">
	<div class="packet">
		IPv4 Src 192.168.12.1 → Dst 192.168.12.2 TTL 255
	</div>
	<div class="packet">
		Ethernet II aa:bb:cc:00:01:10 → aa:bb:cc:00:02:10
	</div>
	<div class="packet">
		ICMP Echo Request • Frame 114 bytes • eth1
	</div>
</div>

</body>
</html>

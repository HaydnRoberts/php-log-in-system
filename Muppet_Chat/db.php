<?php
function nav() {
    echo '
    <nav class="navbar">
		<a href="index.php"><img src="../icons/icons8-home-50.png"></a>
		<a href="create_post.php"><img src="../icons/icons8-message-48.png"></a>
		<img src="../icons/icons8-news-48.png">';
		
		$notification = false;
		if($notification){
		echo '<img src="../icons/icons8-notification-48 (1).png">';
		} else{
		echo '<img src="../icons/icons8-notification-48.png">';
        }
		echo '<a href="account.php"><img src="../icons/icons8-user-avatar-48.png"></a>
	</nav>
    ';
} ?>
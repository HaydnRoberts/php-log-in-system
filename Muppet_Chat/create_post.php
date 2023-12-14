<?php
session_start();
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
    <h1>Create a post</h1>
    <div class="container">
        <form action="post_action.php" method="post" enctype="multipart/form-data">
            <p>Write your post</p>
            <textarea name="post_content"></textarea>
            <p>Add an image</p>
            <input type="file" name="post_image" accept="image/png, image/jpeg, image/gif" />
            <hr>
            <button type="submit" class="btn">POST!</button>
        </form>
    </div>
</body>
</html>

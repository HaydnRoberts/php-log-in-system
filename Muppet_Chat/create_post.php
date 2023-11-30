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
    <h1>Create a post</h1>
    <div class="container">
        <form action="post-action.php" method="post">
            <p>Write your post</p>
            <textarea></textarea>
            <p>Add an image</p>
            <input type="file" accept="image/png, image/jpeg" />
            <button type="submit">
        </form>
    </div>
</body>
</html>
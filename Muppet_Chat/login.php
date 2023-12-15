<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Muppet Chat Sign Up</title>
    <link href='style.css' rel='stylesheet'>
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
    
    <div style="padding: 50px; margin-top: 3em;">
        <h1>Log in to your account</h1>

        <form action="login-action.php" method="post">
            <div class="mb-3">
                <label for="exampleInputEmail1" class="form-label">Email address</label>
                <input name="email" type="email" class="form-control" id="exampleInputEmail1">
            </div>
            <div class="mb-3">
                <label for="exampleInputPassword1" class="form-label">Password</label>
                <input name="password" type="password" class="form-control" id="exampleInputPassword1">
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
</body>
</html>
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
	if ($ping_posts){
		$count = count($ping_posts);
	} else{
		$count = 0;
	}
	include_once "db.php";
    nav($count);
    ?>

    <?php if (isset($_GET["error"])): ?>
        <p style="color: red;">
            <?php
            if ($_GET["error"] === "email_exists") {
                echo "That email is already registered.";
            } elseif ($_GET["error"] === "missing_fields") {
                echo "Please fill in all fields.";
            }
            ?>
        </p>
    <?php endif; ?>

    <div style="padding: 50px; margin-top: 3em;">
        <h1>Sign up for an account</h1>
        <p>Terms of Service</p>
        <iframe src='ToS.txt' width='800' height='400' frameBorder='0' style="padding-bottom: 20px;"></iframe>
        <form action="signup-action.php" method="post">
            <div class="mb-3">
                <label for="exampleInputEmail1" class="form-label">Email address</label>
                <input name="email" type="email" class="form-control" id="exampleInputEmail1">
            </div>
            <div class="mb-3">
                <label for="exampleInputPassword1" class="form-label">Password</label>
                <input name="password" type="password" class="form-control" id="exampleInputPassword1">
            </div>
            
            <button type="submit" class="btn btn-primary" onclick="return confirm('You must consent to the terms of service to continue');">Submit</button>
        </form>
    </div>
</body>
</html>
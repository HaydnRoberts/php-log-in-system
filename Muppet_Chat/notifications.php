<?php
include_once "user.php";

session_start();
$logged_in = false;

if (isset($_SESSION["user"])) {
    $logged_in = true;
    $user = unserialize($_SESSION["user"]);
}

// Check if the user is logged in
if (!$logged_in) {
    header('Location: login.php');
    exit();
}

include_once "notification_action.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
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
        
    <?php if ($count >= 1): ?>
        <div class="container">
            <p>You have new notifications on these posts:</p>
            <?php 
        
        
            $replyquery = "SELECT * FROM reply WHERE ping_owner = 1";
            $ping_replys = mysqli_query($connection, $replyquery);

            $selectquery = "SELECT * FROM posts WHERE post_owner_id LIKE '{$user->email}'";
            $owners_post = mysqli_query($connection, $selectquery);

        

            while ($post_ids = mysqli_fetch_assoc($ping_replys)) {
                mysqli_data_seek($owners_post, 0);
                while ($data = mysqli_fetch_assoc($owners_post)) {
                    if ($data['id'] == $post_ids["post_id"]) {
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

                        $query = "SELECT * FROM reply WHERE post_id = $post_id";
                        $replys = mysqli_query($connection, $query);
                        
                        // Initialize an array to store all rows of content
                        $reply_contents = [];
                        $reply_users = [];
                        $reply_ids = [];

                        // Loop through all rows of reply and store content in the array
                        while ($reply_data = mysqli_fetch_assoc($replys)) {
                            $reply_contents[] = $reply_data['content'];
                            $reply_users[] = $reply_data['user_id'];
                            $reply_ids[] = $reply_data['reply_id'];
                        }

                        $reply_ammount = 0;
                        if (!empty($reply_ids)) {
                            foreach ($reply_ids as $reply_id) {
                                $reply_ammount += 1;
                            }
                        }

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
                    <div class="post-area">
                    <form id="updatePingForm">
                        <button type="submit" onclick="updatePing()">I have read all notifications</button>
                    </form>

                    <script>
                    function updatePing() {
                        // Your JavaScript code here to handle the button click and send an AJAX request
                        var xhr = new XMLHttpRequest();
                        xhr.open("POST", "update_ping.php", true);
                        xhr.onload = function () {
                            // Handle the response if needed
                            location.reload();
                        };
                        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

                        xhr.onreadystatechange = function() {
                            if (xhr.readyState == 4 && xhr.status == 200) {
                                // Handle the response from the server, if needed
                                console.log(xhr.responseText);
                            }
                        };

                        // Send the data to the server
                        xhr.send("update_ping=true");
                    }

                    </script>
                        <div class="card">

                            <img src="./image/<?php echo $data['post_image']; ?>" class="card_image">

                            <div class="card_container">
                                <h4><b><?php echo htmlspecialchars($data['post_owner_id']); ?></b></h4>
                                <p style="overflow: auto; height: 300px; text-align:left;"><?php echo htmlspecialchars($data['post_content']); ?></p>
                            </div>

                            <div class="like-dislike-container">
                                <button class="like-btn" data-postid="<?php echo $post_id; ?>">
                                <?php if($row_exists==1 && $row['likes']==1): ?>
                                    <img src="..\icons\like_clicked.png"><?php echo $alllikes; ?></span>
                                <?php else: ?>
                                    <img src="..\icons\like.png"><?php echo $alllikes; ?></span>
                                <?php endif; ?>
                                </button>
                                
                                <button class="reply-box" onclick="togglereply(this, '<?php echo $post_id; ?>')">See Replies <?php echo $reply_ammount; ?></button>
                                
                                <button class="dislike-btn" data-postid="<?php echo $post_id; ?>">
                                <?php if($row_exists==1 && $row['dislikes']==1): ?>
                                    <img src="..\icons\dislike_clicked.png"><?php echo $alldislikes; ?></span>
                                <?php else: ?>
                                    <img src="..\icons\dislike.png"><?php echo $alldislikes; ?></span>
                                <?php endif; ?>
                                </button>	
                                
                            </div>

                        </div>
                        <script>
                            // Function to toggle the hidden class to show/hide the reply-card
                            function togglereply(button, post_id) {
                                // Get the parent card container
                                var cardContainer = button.closest('.post-area');

                                // Find the reply-card within the specific card container
                                var replyCard = cardContainer.querySelector('.reply-card');

                                // Toggle the visibility of the reply-card
                                if (replyCard.style.display === 'none' || replyCard.style.display === '') {
                                    replyCard.style.display = 'block';
                                    // Save the state to local storage
                                    localStorage.setItem('replyCardState_' + post_id, 'visible');
                                } else {
                                    replyCard.style.display = 'none';
                                    // Save the state to local storage
                                    localStorage.setItem('replyCardState_' + post_id, 'hidden');
                                }
                            }


                            // Function to set the initial state of the reply-card on page load
                            window.onload = function () {
                                // Loop through all reply cards on the page
                                document.querySelectorAll('.reply-card').forEach(function(replyCard) {
                                    // Get the post ID from the reply-card element
                                    var postId = replyCard.id;

                                    // Get the state from local storage using the post ID
                                    var replyCardState = localStorage.getItem('replyCardState_' + postId);

                                    // Apply the initial state to the reply-card
                                    if (replyCardState === 'visible') {
                                        replyCard.style.display = 'block';
                                    } else {
                                        replyCard.style.display = 'none';
                                    }
                                });
                            };

                        </script>

                        <div class="reply-card" style="display: none;" id="<?php echo $post_id; ?>">
                            <?php 
                            if (!empty($reply_contents) && !empty($reply_users) && !empty($reply_ids)) {
                                $comment_number = 0;
                                foreach ($reply_ids as $reply_id) {
                                    // Retrieve corresponding content and user for the current $reply_id
                                    $reply_user = $reply_users[$comment_number];
                                    $reply_content = $reply_contents[$comment_number];
                                    // If content is not empty and user is found
                                    if (!empty($reply_content) && !empty($reply_user)) {
                                        // Converts user id into user email
                                        $query = "SELECT * FROM users WHERE id = $reply_user";
                                        $reply_user_result = mysqli_query($connection, $query);

                                        if ($reply_user_result) {
                                            $reply_user_data = mysqli_fetch_assoc($reply_user_result);
                                            $reply_user_email = $reply_user_data["email"];
                                            echo '<p style="padding: 5px;">' .  htmlspecialchars($reply_user_email) . ': ' .  htmlspecialchars($reply_content) . '</p>';
                                        }
                                    }
                                    $comment_number += 1;
                                }
                            } else {
                                echo "<p style='padding: 5px;'>There are no replies yet</p>";
                            }
                            ?>
                            <form class="replyForm" >
                                <textarea style="height: 100px; width: 360px;" class="new-content"></textarea>
                                <button type="submit" class="reply-btn" data-postid="<?php echo $post_id; ?>" onclick="togglereply(this, '<?php echo $post_id; ?>')">Submit Reply</button>
                            </form>
                        </div>
                    <br>
                </div>
            <?php
                    }
                };
            }
            ?>

        <script>

        document.addEventListener("DOMContentLoaded", function () {
            document.querySelectorAll('.like-btn, .dislike-btn').forEach(function (button) {
                button.addEventListener('click', function () {
                    event.preventDefault();
                    handleLikeDislike(this);
                });
            });
            
            document.querySelectorAll('.replyForm').forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    event.preventDefault();
                    handleReply(this);
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
                    location.reload();
                };
                xhr.send(formData);
                
            }

            function handleReply(form) {
            var post_id = form.querySelector('.reply-btn').getAttribute('data-postid');
            var user_id = <?php echo $user_id; ?>;
            var new_content = form.querySelector('.new-content').value;

            var formData = new FormData();
            formData.append('post_id', post_id);
            formData.append('user_id', user_id);
            formData.append('new_content', new_content);

            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'reply.php', true);
            xhr.onload = function () {
                // Handle the response if needed
                location.reload();
            };
            xhr.send(formData);

            return false;
        }
        });
        </script>
        </div>

        </div>
    <?php elseif ($count == 0): ?>
        <div class="container">
            <p>You have no new notifications.</p>
    <?php else: ?>
        <p>You have an error with your notifications.</p>
    <?php endif; ?>
    </body>
</html>
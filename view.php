<?php

include('header.php');
include('post.php');
include('comment.php');

$posts = new Post($db);
$comment = new Comment($db);

?>

<div class="container">
    <div class="row justify-content-center">
        <?php
        // Fetch the post based on slug
        foreach ($posts->getSinglePost($_GET['slug']) as $post) { ?>
            <div class="col-md-8">
                <div class="card mb-3">
                    <div class="row no-gutters">
                        <!-- Image on the left side -->
                        <div class="col-md-4">
                            <img src="images/<?php echo htmlspecialchars($post['image']); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>" style="width: 100%; height: auto;">
                        </div>
                        <!-- Content on the right side -->
                        <div class="col-md-8">
                            <div class="card-body">
                                <h4 class="card-title"><?php echo htmlspecialchars($post['title']); ?></h4>
                                <p class="card-text"><?php echo htmlspecialchars_decode($post['description']); ?></p>
                                <p class="card-text">
                                    <small class="text-muted">Published on <?php echo date('Y-m-d', strtotime($post['created_at'])); ?></small>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Comments Section -->
                <div class="col-md-12">
                    <h4>Comments (<?php echo $comment->countComments($_GET['slug']); ?>)</h4>

                    <?php
                    // Handle comment submission
                    if (isset($_POST['btnComment'])) {
                        $date = date('Y-m-d');
                        $status = 0; // Comments are initially set to pending (status = 0)
                        if (!empty($_POST['name']) && !empty($_POST['email']) && !empty($_POST['description'])) {
                            $result = $comment->comment(
                                strip_tags($_POST['name']),
                                strip_tags($_POST['email']),
                                strip_tags($_POST['subject']),
                                strip_tags($_POST['description']),
                                $_GET['slug'],
                                $date,
                                $status
                            );
                            if ($result == true) {
                                echo "<div class='alert alert-success'>Comment added successfully! It will appear after approval.</div>";
                            } else {
                                echo "<div class='alert alert-danger'>Failed to add comment.</div>";
                            }
                        } else {
                            echo "<div class='alert alert-warning'>Name, email, and description fields are compulsory.</div>";
                        }
                    }
                    ?>

                    <!-- Display comments -->
                    <?php foreach ($comment->getCommentsBySlug($_GET['slug']) as $comment) { ?>
                     
                            <div class="media">
                                <div class="media-left media-top">
                                    <img src="images/avatar.png" alt="" style="width: 50px; border-radius:50px;">
                                </div>
                                <div class="media-body">
                                    <strong><?php echo $comment['name']; ?></strong>
                                    <p><?php echo $comment['description']; ?></p>
                                </div>
                            </div>
                       
                    <?php } ?>
                    <br>
                    <h4>Add new Comment</h4>

                    <!-- Comment form -->
                    <form action="" method="POST">
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="subject">Subject</label>
                            <input type="text" name="subject" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea name="description" class="form-control" required></textarea>
                        </div>
                        <div class="form-group">
                            <button type="submit" name="btnComment" class="btn btn-outline-success">Comment</button>
                        </div>
                    </form>
                </div>
            </div>
        <?php } ?>
    </div>
</div>

<!-- Custom styles -->
<style type="text/css">
    .card {
        margin-top: 20px;
    }

    .card img {
        border: none;
        /* Remove any border */
        border-radius: 0;
        /* Remove rounded corners */
    }

    .row.no-gutters {
        display: flex;
        align-items: center;
    }

    .avatar-img {
        transition: transform 0.3s, box-shadow 0.3s;
    }

    .avatar-img:hover {
        transform: scale(1.1);
        /* Slightly increase the size */
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        /* Add shadow */
    }
</style>
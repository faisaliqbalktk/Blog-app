<?php 
include('header.php'); 
include('db.php'); // Ensure the database connection is included

include('post.php');
$posts = new Post($db); // Ensure $db is passed correctly to Post class

// Ensure that a 'slug' is passed in the URL
if (isset($_POST['btnUpdate'])) {
    // Call updatePost on the correct object
    $result = $posts->updatePost($_POST['title'], $_POST['description'], $_GET['slug']);
    if ($result == true) {
        echo "<div class='text-center alert alert-success'>Post updated successfully!</div>";
    } else {
        echo "<div class='text-center alert alert-danger'>Failed to update post.</div>";
    }
}
?>

<div class="container">
    <div class="row justify-content-center">
        <?php 
        // Fetch the single post using the slug
        $postResult = $posts->getSinglePost($_GET['slug']);
        if ($postResult && mysqli_num_rows($postResult) > 0) {
            $post = mysqli_fetch_assoc($postResult);
        ?>
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Edit post</div>
                <div class="card-body">
                    <!-- Start form -->
                    <form action="" method="POST" enctype="multipart/form-data"> <!-- Fixed the form action to point to the same file -->
                        <div class="form-group">
                            <label for="title">Title</label>
                            <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($post['title']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea cols="10" name="description" id="editor" class="form-control"><?php echo htmlspecialchars($post['description']); ?></textarea> <!-- Fixed typo 'descrition' -->
                        </div>

                        <div class="form-group">
                            <label for="image">Image</label>
                            <input type="file" name="image" class="form-control">
                            <img style="width: 150px;" src="images/<?php echo $post['image'] ?>" alt="">
                        </div>

                        <div class="form-group">
                            <button type="submit" name="btnUpdate" class="btn btn-primary">Update</button>
                        </div>
                    </form>
                    <!-- End form -->
                </div>
            </div>
        </div>
        <?php 
        } else {
            echo "<div class='alert alert-danger'>Post not found.</div>";
        }
        ?>
    </div>
</div>

<script>
    // Initialize ClassicEditor for description field
    ClassicEditor
        .create(document.querySelector('#editor'))
        .then(editor => {
            document.querySelector('form').addEventListener('submit', (event) => {
                // Synchronize CKEditor content into textarea
                document.querySelector('textarea[name="description"]').value = editor.getData();
                
                // If the editor content is empty, prevent form submission
                if (editor.getData().trim() === '') {
                    event.preventDefault();  // Prevent form submission
                    alert('Description is required.');
                }
            });
        })
        .catch(error => {
            console.error(error);
        });
</script>

<style type="text/css">
    .card {
        margin-top: 10px;
    }

    .btn-primary {
        margin-top: 10px;
    }
</style>

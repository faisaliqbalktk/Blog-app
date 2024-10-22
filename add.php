<?php include('session.php'); ?>
<?php include("header.php"); ?>
<?php include("post.php"); ?>
<?php include('functions/functions.php'); ?>
<?php include('Tag.php'); ?>

<?php
$tags = new Tag($db);
$post = new Post($db);

if (isset($_POST['btnSubmit'])) {
    $data = date('Y-m-d');
    if (!empty($_POST['title']) && !empty($_POST['description'])) {
        $title = strip_tags($_POST['title']);
        $description = strip_tags(trim(html_entity_decode($_POST['description'])));
        $slug = createSlug($title);

        // Upload image and check if it was successful
        $image = uploadImage();
        if ($image !== false) {
            // Check if the slug already exists
            $checkSlug = mysqli_query($db, "SELECT * FROM posts WHERE slug='$slug'");
            $result = mysqli_num_rows($checkSlug);

            if ($result > 0) {
                // If slug exists, create a unique slug
                $newSlug = $slug . uniqid();
                $record = $post->addPost($title, $description, $image, $data, $newSlug);
            } else {
                // If slug does not exist, use the original slug
                $record = $post->addPost($title, $description, $image, $data, $slug);
            }

            // Check if the post was added successfully
            if ($record == true) {
                echo "<div class='text-center alert alert-success'>Post added successfully!</div>";
            } else {
                echo "<div class='text-center alert alert-danger'>Failed to add the post.</div>";
            }
        } else {
            echo "<div class='text-center alert alert-danger'>Failed to upload image.</div>";
        }
    } else {
        echo "<div class='text-center alert alert-danger'>Every field is required.</div>";
    }
}
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Add post</div>
                <div class="card-body">
                    <!-- Start form -->
                    <form action="add.php" method="POST" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="title">Title</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea cols="10" name="description" id="editor" class="form-control"></textarea> <!-- Removed required attribute -->
                        </div>

                        <div class="form-group">
                            <label for="image">Image</label>
                            <input type="file" name="image" class="form-control" required>
                        </div>

                        <div class="form-group form-check-inline">
                            <label for="tags"><b>Choose tags</b>&nbsp;&nbsp;</label><br>
                            <?php foreach ($tags->getAllTags() as $tag) { ?>
                                <input type="checkbox" name="tags[]"
                                 class="form-check-input" 
                                 value="<?php echo htmlspecialchars($tag['id']); ?>">
                                <?php echo htmlspecialchars($tag['tag']); ?>
                                <br>
                            <?php } ?>
                        </div>

                        <div class="form-group">
                            <button type="submit" name="btnSubmit" class="btn btn-primary">Submit</button>
                        </div>
                    </form>
                    <!-- End form -->
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    ClassicEditor
        .create(document.querySelector('#editor'))
        .then(editor => {
            document.querySelector('form').addEventListener('submit', (event) => {
                // Synchronize the CKEditor content into the hidden textarea
                document.querySelector('textarea[name="description"]').value = editor.getData();
                
                // If the editor is empty, prevent form submission
                if (editor.getData().trim() === '') {
                    event.preventDefault();  // Prevent form submission
                    alert('Description is required.');  // Alert the user
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

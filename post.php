<?php
include('db.php');

class Post
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function addPost($title, $description, $image, $date, $slug)
    {
        // Escape special characters to avoid SQL injection (basic method)
        $title = mysqli_real_escape_string($this->db, $title);
        $description = mysqli_real_escape_string($this->db, $description);

        // Insert post into the posts table
        $sql = "INSERT INTO posts(title, description, image, created_at, slug) VALUES('$title', '$description', '$image', '$date', '$slug')";
        $result = mysqli_query($this->db, $sql);

        // Check if the post was added successfully
        if ($result) {
            if (isset($_POST['tags']) && is_array($_POST['tags'])) {
                $tags = $_POST['tags'];
                $lastInsertedId = mysqli_insert_id($this->db);

                // Insert tags associated with the post
                foreach ($tags as $tag) {
                    // Ensure $tag is numeric to prevent invalid data
                    if (is_numeric($tag)) {
                        $tag = mysqli_real_escape_string($this->db, $tag); // Escape $tag
                        $sql = "INSERT INTO post_tags(post_id, tag_id) VALUES('$lastInsertedId', '$tag')";
                        $tagResult = mysqli_query($this->db, $sql);

                        // Check if tag insert failed
                        if (!$tagResult) {
                            return false; // Return false if any tag insertion fails
                        }
                    }
                }
            }
        }

        return $result;
    }

    public function getPost()
    {
        #for search
        if (isset($_GET['keyword'])) {
            $keyword = $_GET['keyword'];
            return $this->search($keyword);
        }

        #for tag
        if (isset($_GET['tag'])) {
            $tag = mysqli_real_escape_string($this->db, $_GET['tag']); // Escape $tag for safety
            $sql = "SELECT *
                    FROM posts
                    INNER JOIN post_tags ON posts.id = post_tags.post_id
                    INNER JOIN tags ON tags.id = post_tags.tag_id
                    WHERE tags.tag='$tag'";
            $result = mysqli_query($this->db, $sql);
            return $result;
        }

        $limit = 2;
        if(isset($_GET["page"])){
            $page = $_GET["page"];
        }else{
            $page = 1;
        }
        $start = ($page -1)*$limit;


        // Fetch all posts if no tag is specified
        $sql = "SELECT * FROM posts LIMIT $start, $limit";
        $result = mysqli_query($this->db, $sql);
        return $result;
    }

    public function search($keyword)
    {
        $sql = "SELECT * FROM posts 
                WHERE title LIKE '%{$keyword}%'
                OR description LIKE '%{$keyword}%'";
        $result = mysqli_query($this->db, $sql);
        return $result;
    }


    public function getSinglePost($slug)
    {
        $slug = mysqli_real_escape_string($this->db, $slug); // Escape $slug for safety
        $sql = "SELECT * FROM posts WHERE slug='$slug'";
        $result = mysqli_query($this->db, $sql);
        return $result;
    }

    public function deletePostBySlug($slug)
    {
        $sql = "DELETE FROM posts WHERE slug='$slug'";
        $result = mysqli_query($this->db, $sql);
        return $result;
    }

    public function getPopularPosts()
    {
        $sql = "SELECT * FROM posts LEFT JOIN comments on 
                posts.slug=comments.slug GROUP BY comments.slug 
                ORDER by count(comments.slug) DESC LIMIT 5";
        $result = mysqli_query($this->db, $sql);
        return $result;
    }

    public function updatePost($title, $description, $slug)
    {
        $newImage = $_FILES['image']['name'];
        if (!empty($newImage)) {
            $image = uploadImage();
            $sql = "UPDATE posts SET title='$title', description='$description', image='$image' WHERE slug= '$slug'";
            $result = mysqli_query($this->db, $sql);
            return $result;
        } else {
            $sql = "UPDATE posts SET title='$title', description='$description' WHERE slug= '$slug'";
            $result = mysqli_query($this->db, $sql);
            return $result;
        }
    }
}

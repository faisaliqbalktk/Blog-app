<?php include('header.php'); ?>
<?php include('post.php'); ?>
<?php 
     $post = new Post($db);
?>
<?php
$post->deletePostBySlug($_GET['slug']);
header('Location:result.php');
?>